<?php
/**
 * Template Name: Бронирование Bnovo
 * Template Post Type: page
 *
 * Full-page Bnovo booking module. Passes query params into the iframe.
 *
 * @package Troya
 */

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'booking-widget-page';
		return $classes;
	}
);

get_header();

$uid = troya_bnovo_uid();

$query   = array();
$allowed = array( 'dfrom', 'dto', 'adults', 'children', 'lang', 'currency', 'padding', 'radius', 'promo', 'onlyrooms', 'is_auto_search', 'scroll_to_rooms' );

foreach ( $allowed as $key ) {
	if ( isset( $_GET[ $key ] ) && '' !== $_GET[ $key ] ) {
		$query[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
	}
}

if ( empty( $query['lang'] ) ) {
	$query['lang'] = 'ru';
}

// Bnovo не отдаёт список номеров, пока в фильтре нет дат.
if ( empty( $query['dfrom'] ) && empty( $query['dto'] ) ) {
	$query['dfrom'] = wp_date( 'd-m-Y' );
	$query['dto']   = wp_date( 'd-m-Y', strtotime( '+1 day' ) );
}

// Эквивалент клика «Найти»: кнопка внутри iframe, с родителя её не нажать.
if ( empty( $query['is_auto_search'] ) ) {
	$query['is_auto_search'] = '1';
}
if ( empty( $query['scroll_to_rooms'] ) ) {
	$query['scroll_to_rooms'] = '1';
}

$iframe_src = add_query_arg(
	$query,
	'https://reservationsteps.ru/rooms/index/' . rawurlencode( $uid )
);

$rooms_page_url = troya_booking_url();

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
		<h1 class="hero__title anim-up" style="--d: 0.5s"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $lead ) : ?>
			<p class="hero__text anim-up" style="--d: 0.7s"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="section section--compact">
	<div class="container">
		<?php troya_render_payment_policy(); ?>
	</div>
</section>

<section class="booking-page">
	<div class="booking-page__back" id="booking-back">
		<a class="booking-page__back-btn" id="booking-back-btn" href="<?php echo esc_url( $rooms_page_url ); ?>"><span class="booking-page__back-desktop">К списку номеров</span><span class="booking-page__back-mobile">Вернуться к номерам</span></a>
	</div>
	<div class="booking-page__frame">
		<iframe
			id="bnovo_booking_iframe"
			class="booking-page__iframe"
			src="<?php echo esc_url( $iframe_src ); ?>"
			title="<?php echo esc_attr( $title ); ?>"
			width="100%"
			height="4800"
			frameborder="0"
			scrolling="no"
			allowfullscreen
			loading="eager"
			referrerpolicy="no-referrer-when-downgrade"
		></iframe>
	</div>
</section>

<?php
get_footer();
