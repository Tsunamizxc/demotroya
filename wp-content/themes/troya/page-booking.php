<?php
/**
 * Template Name: Бронирование Bnovo
 * Template Post Type: page
 *
 * Full-page Bnovo booking module. Passes query params into the iframe.
 *
 * @package Troya
 */

get_header();

$uid = troya_option( 'bnovo_uid', 'eb9cee17-77f8-4f25-9817-b3fc2080617b' );

$query = array();
$allowed = array( 'dfrom', 'dto', 'adults', 'children', 'lang', 'currency', 'padding', 'radius', 'promo' );

foreach ( $allowed as $key ) {
	if ( isset( $_GET[ $key ] ) && '' !== $_GET[ $key ] ) {
		$query[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
	}
}

if ( empty( $query['lang'] ) ) {
	$query['lang'] = 'ru';
}

$iframe_src = add_query_arg(
	$query,
	'https://reservationsteps.ru/rooms/index/' . rawurlencode( $uid )
);

$title = troya_option( 'booking_page_title', 'Онлайн-бронирование' );
$lead  = troya_option( 'booking_page_lead', 'Выберите даты и номер — бронирование без комиссии напрямую в отеле «Троя».' );
?>

<section class="booking-page">
	<div class="container booking-page__head">
		<p class="crumbs">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a><span>/</span><span>Бронирование</span>
		</p>
		<h1 class="heading"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $lead ) : ?>
			<p class="booking-page__lead text"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
	</div>

	<div class="booking-page__frame">
		<iframe
			id="bnovo_booking_iframe"
			class="booking-page__iframe"
			src="<?php echo esc_url( $iframe_src ); ?>"
			title="<?php echo esc_attr( $title ); ?>"
			width="100%"
			height="2200"
			frameborder="0"
			scrolling="auto"
			allowfullscreen
			loading="eager"
			referrerpolicy="no-referrer-when-downgrade"
		></iframe>
	</div>
</section>

<?php
get_footer();
