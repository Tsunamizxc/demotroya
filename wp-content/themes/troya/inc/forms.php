<?php
/**
 * Booking form + SMTP mailer.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

require_once TROYA_DIR . '/assets/phpmailer/PHPMailer.php';
require_once TROYA_DIR . '/assets/phpmailer/SMTP.php';
require_once TROYA_DIR . '/assets/phpmailer/Exception.php';

add_action( 'wp_ajax_troya_submit_booking', 'troya_handle_booking_submission' );
add_action( 'wp_ajax_nopriv_troya_submit_booking', 'troya_handle_booking_submission' );

function troya_handle_booking_submission(): void {
	check_ajax_referer( 'troya_forms', 'nonce' );

	// Honeypot
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => 'Заявка отправлена.' ) );
	}

	$name     = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$phone    = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$email    = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$checkin  = sanitize_text_field( wp_unslash( $_POST['checkin'] ?? '' ) );
	$checkout = sanitize_text_field( wp_unslash( $_POST['checkout'] ?? '' ) );
	$guests   = sanitize_text_field( wp_unslash( $_POST['guests'] ?? '' ) );
	$room     = sanitize_text_field( wp_unslash( $_POST['room'] ?? '' ) );
	$comment  = sanitize_textarea_field( wp_unslash( $_POST['comment'] ?? '' ) );

	if ( '' === $name || '' === $phone || '' === $checkin || '' === $checkout ) {
		wp_send_json_error( array( 'message' => 'Заполните обязательные поля.' ), 400 );
	}

	$fields = array(
		'name'     => $name,
		'phone'    => $phone,
		'email'    => $email,
		'checkin'  => $checkin,
		'checkout' => $checkout,
		'guests'   => $guests,
		'room'     => $room,
		'comment'  => $comment,
	);

	$saved = troya_save_booking( $fields );

	if ( ! $saved ) {
		wp_send_json_error( array( 'message' => 'Не удалось сохранить заявку. Попробуйте позже.' ), 500 );
	}

	// Optional SMTP — does not block success if mail fails.
	$subject = sprintf( 'Заявка на бронирование — %s', $name );
	$body    = troya_build_booking_email( $fields );
	troya_send_mail( $subject, $body );

	wp_send_json_success(
		array(
			'message' => 'Заявка отправлена. Мы свяжемся с вами для подтверждения.',
			'id'      => $saved,
		)
	);
}

/**
 * @param array<string, string> $fields Fields.
 */
function troya_build_booking_email( array $fields ): string {
	$labels = array(
		'name'     => 'Имя',
		'phone'    => 'Телефон',
		'email'    => 'Email',
		'checkin'  => 'Заезд',
		'checkout' => 'Выезд',
		'guests'   => 'Гостей',
		'room'     => 'Номер',
		'comment'  => 'Комментарий',
	);

	$rows = '';
	$alt  = true;

	foreach ( $labels as $key => $label ) {
		if ( empty( $fields[ $key ] ) ) {
			continue;
		}
		$alt = ! $alt;
		$bg  = $alt ? '' : ' style="background-color:#f8f8f8;"';
		$rows .= sprintf(
			'<tr%s><td style="padding:10px;border:1px solid #e9e9e9;"><b>%s</b></td><td style="padding:10px;border:1px solid #e9e9e9;">%s</td></tr>',
			$bg,
			esc_html( $label ),
			esc_html( $fields[ $key ] )
		);
	}

	return '<p>Новая заявка с сайта отеля «Троя»</p><table style="width:100%;border-collapse:collapse;font-family:Arial,sans-serif;font-size:14px;">' . $rows . '</table>';
}

function troya_send_mail( string $subject, string $body ): bool {
	$host       = (string) troya_option( 'smtp_host', '' );
	$username   = (string) troya_option( 'smtp_username', '' );
	$password   = (string) troya_option( 'smtp_password', '' );
	$from_email = (string) troya_option( 'smtp_from_email', $username );
	$from_name  = (string) troya_option( 'smtp_from_name', 'Отель Троя' );
	$to_raw     = (string) troya_option( 'smtp_to_email', troya_option( 'site_email', 'hoteltroya@mail.ru' ) );
	$recipients = troya_parse_email_list( $to_raw );

	if ( ! $recipients ) {
		return false;
	}

	// If SMTP is not configured — soft skip (booking already saved in admin).
	if ( '' === $host || '' === $username ) {
		return false;
	}

	$mail = new PHPMailer\PHPMailer\PHPMailer( true );

	try {
		$port       = (int) troya_option( 'smtp_port', 465 );
		$encryption = (string) troya_option( 'smtp_encryption', 'ssl' );

		$mail->isSMTP();
		$mail->CharSet  = 'UTF-8';
		$mail->Host     = $host;
		$mail->Port     = $port;
		$mail->SMTPAuth = true;
		$mail->Username = $username;
		$mail->Password = $password;

		if ( 'ssl' === $encryption ) {
			$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
		} elseif ( 'tls' === $encryption ) {
			$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
		} else {
			$mail->SMTPSecure  = false;
			$mail->SMTPAutoTLS = false;
		}

		$mail->setFrom( $from_email ?: $username, $from_name );

		foreach ( $recipients as $email ) {
			$mail->addAddress( $email );
		}

		$mail->isHTML( true );
		$mail->Subject = $subject;
		$mail->Body    = $body;

		return $mail->send();
	} catch ( Exception $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Troya mail error: ' . $e->getMessage() );
		}

		return false;
	}
}
