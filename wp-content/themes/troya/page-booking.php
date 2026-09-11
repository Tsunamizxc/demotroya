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

$uid = troya_bnovo_uid();

$query   = array();
$allowed = array( 'dfrom', 'dto', 'adults', 'children', 'lang', 'currency', 'padding', 'radius', 'promo', 'onlyrooms' );

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
$hero  = troya_img_url( troya_option( 'hero_poster' ), troya_asset( 'photos/hero-building.jpg' ) );
?>

<section class="hero hero--page hero--booking">
	<div class="hero__bg">
		<img class="hero__img" src="<?php echo esc_url( $hero ); ?>" alt="" />
		<div class="hero__overlay"></div>
		<div class="hero__grain"></div>
	</div>
	<div class="container hero__content">
		<p class="crumbs anim-up" style="--d: 0.2s">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a><span>/</span><span>Бронирование</span>
		</p>
		<p class="hero__eyebrow anim-up" style="--d: 0.35s">Bnovo</p>
		<h1 class="hero__title anim-up" style="--d: 0.5s"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $lead ) : ?>
			<p class="hero__text anim-up" style="--d: 0.7s"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="booking-page">
	<div class="booking-page__frame">
		<iframe
			id="bnovo_booking_iframe"
			class="booking-page__iframe"
			src="<?php echo esc_url( $iframe_src ); ?>"
			title="<?php echo esc_attr( $title ); ?>"
			width="100%"
			height="800"
			frameborder="0"
			scrolling="yes"
			allowfullscreen
			loading="eager"
			referrerpolicy="no-referrer-when-downgrade"
		></iframe>
	</div>
</section>

<?php
get_footer();
