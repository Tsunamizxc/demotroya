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
	troya_send_mail( $subject, $body, $email );

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

function troya_send_mail( string $subject, string $body, string $reply_to = '' ): bool {
	$host       = (string) troya_option( 'smtp_host', '' );
	$username   = (string) troya_option( 'smtp_username', '' );
	$password   = (string) troya_option( 'smtp_password', '' );
	$from_email = (string) troya_option( 'smtp_from_email', $username );
	$from_name  = (string) troya_option( 'smtp_from_name', 'Отель Троя' );
	$to_raw     = (string) troya_option( 'smtp_to_email', 'tech@cursiva.ru' );
	$recipients = troya_parse_email_list( $to_raw );

	if ( ! $recipients ) {
		return false;
	}

	// If SMTP is not configured — soft skip (booking already saved in admin).
	if ( '' === $host || '' === $username || '' === $password ) {
		return false;
	}

	$mail = new PHPMailer\PHPMailer\PHPMailer( true );

	try {
		$port       = (int) troya_option( 'smtp_port', 465 );
		$encryption = (string) troya_option( 'smtp_encryption', 'ssl' );

		$mail->isSMTP();
		$mail->CharSet    = 'UTF-8';
		$mail->Host       = $host;
		$mail->Port       = $port ?: 465;
		$mail->SMTPAuth   = true;
		$mail->Username   = $username;
		$mail->Password   = $password;
		$mail->Timeout    = 20;
		$mail->SMTPDebug  = 0;

		if ( 'ssl' === $encryption ) {
			$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
		} elseif ( 'tls' === $encryption ) {
			$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
		} else {
			$mail->SMTPSecure  = false;
			$mail->SMTPAutoTLS = false;
		}

		$mail->setFrom( $from_email ?: $username, $from_name );

		if ( $reply_to && is_email( $reply_to ) ) {
			$mail->addReplyTo( $reply_to );
		}

		foreach ( $recipients as $email ) {
			$mail->addAddress( $email );
		}

		$mail->isHTML( true );
		$mail->Subject = $subject;
		$mail->Body    = $body;
		$mail->AltBody = wp_strip_all_tags( $body );

		return $mail->send();
	} catch ( Throwable $e ) {
		error_log( 'Troya mail error: ' . $e->getMessage() );
		return false;
	}
}

add_action( 'admin_post_troya_smtp_test', 'troya_handle_smtp_test' );
add_action( 'admin_notices', 'troya_smtp_admin_notice' );

function troya_handle_smtp_test(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Недостаточно прав.', 'troya' ) );
	}

	check_admin_referer( 'troya_smtp_test' );

	$ok = troya_send_mail(
		'Тест SMTP — Отель Троя',
		'<p>Это тестовое письмо с сайта отеля «Троя».</p><p>Если вы его видите, SMTP настроен корректно.</p>'
	);

	wp_safe_redirect(
		add_query_arg(
			'troya_smtp',
			$ok ? 'ok' : 'fail',
			admin_url( 'admin.php?page=troya-settings' )
		)
	);
	exit;
}

function troya_smtp_admin_notice(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$status = isset( $_GET['troya_smtp'] ) ? sanitize_key( (string) $_GET['troya_smtp'] ) : '';
	if ( ! $status ) {
		return;
	}

	if ( 'ok' === $status ) {
		echo '<div class="notice notice-success is-dismissible"><p>Тестовое письмо отправлено на адреса из «Email получателей заявок».</p></div>';
		return;
	}

	echo '<div class="notice notice-error is-dismissible"><p>Не удалось отправить тестовое письмо. Проверьте SMTP-настройки (хост Beget, логин, пароль, порт 465/SSL) и логи сервера.</p></div>';
}
