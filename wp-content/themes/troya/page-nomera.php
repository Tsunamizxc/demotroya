<?php
/**
 * Template Name: Каталог номеров
 *
 * @package Troya
 */

get_header();

$rooms_q = new WP_Query(
	array(
		'post_type'      => 'troya_room',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	)
);
?>
<section class="hero hero--page">
	<div class="hero__bg">
		<img class="hero__img" src="<?php echo esc_url( troya_img_url( troya_option( 'hero_poster' ), troya_asset( 'photos/hero.jpg' ) ) ); ?>" alt="Номера отеля Троя" />
		<div class="hero__overlay"></div>
		<div class="hero__grain"></div>
	</div>
	<p class="hero__city"><?php echo esc_html( troya_option( 'site_address_short', 'Казань — ул. Восстания, 119' ) ); ?></p>
	<div class="container hero__content">
		<p class="crumbs anim-up" style="--d: 0.2s">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a><span>/</span><span>Номера</span>
		</p>
		<p class="hero__eyebrow anim-up" style="--d: 0.35s"><?php echo esc_html( troya_option( 'rooms_eyebrow', 'Номерной фонд' ) ); ?></p>
		<h1 class="hero__title anim-up" style="--d: 0.5s"><?php echo esc_html( troya_option( 'rooms_title', 'Выберите свой номер' ) ); ?></h1>
	</div>
</section>

<div class="room-catalog">
	<?php
	$i = 0;
	while ( $rooms_q->have_posts() ) :
		$rooms_q->the_post();
		$i++;
		$img  = troya_img_url(
			troya_field( 'room_image' ),
			get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: ''
		);
		if ( ! $img ) {
			$gallery = troya_field( 'room_gallery', array() );
			if ( is_array( $gallery ) && ! empty( $gallery[0]['url'] ) ) {
				$img = (string) $gallery[0]['url'];
			}
		}
		if ( ! $img ) {
			$path = (string) get_post_meta( get_the_ID(), '_troya_room_img_path', true );
			$img  = $path ? troya_asset( $path ) : troya_asset( 'photos/room-1.jpg' );
		}
		$flip = 0 === $i % 2 ? ' room-band--flip' : '';
		$mist = 0 === $i % 2 ? ' room-band--mist' : '';
		?>
		<article class="room-band<?php echo esc_attr( $mist . $flip ); ?>">
			<div class="room-band__media">
				<img src="<?php echo esc_url( $img ); ?>" alt="<?php the_title_attribute(); ?>" />
				<span class="room-band__index"><?php echo esc_html( sprintf( '%02d', $i ) ); ?></span>
			</div>
			<div class="room-band__body reveal">
				<span class="rooms__tag"><?php echo esc_html( (string) troya_field( 'room_tag' ) ); ?></span>
				<h2 class="rooms__name"><?php the_title(); ?></h2>
				<p class="room-band__sub"><?php echo esc_html( (string) troya_field( 'room_subtitle' ) ); ?></p>
				<p class="room-band__price"><?php echo esc_html( (string) troya_field( 'room_price' ) ); ?><small>за ночь</small></p>
				<p class="text"><?php echo esc_html( (string) troya_field( 'room_desc' ) ); ?></p>
				<div class="room-band__actions">
					<a href="<?php the_permalink(); ?>" class="btn btn--gold">Смотреть номер</a>
					<a href="<?php echo esc_url( troya_room_booking_url( get_the_ID() ) ); ?>" class="btn btn--outline">Забронировать</a>
					<button type="button" class="btn btn--ghost" data-open-modal data-room="<?php echo esc_attr( get_the_title() . ' — ' . troya_field( 'room_subtitle' ) ); ?>">Забронировать по телефону</button>
				</div>
			</div>
		</article>
	<?php endwhile; ?>
	<?php wp_reset_postdata(); ?>
</div>

<section class="section section--mist">
	<div class="container room-catalog-cta">
		<p class="eyebrow">Бронирование</p>
		<h2 class="heading gold-line-center">Не нашли подходящий?</h2>
		<p class="text"><?php echo esc_html( troya_option( 'rooms_note' ) ); ?></p>
		<a href="tel:<?php echo esc_attr( preg_replace( '/\D+/', '', troya_option( 'site_phone_1', '88435644646' ) ) ); ?>" class="btn btn--gold"><?php echo esc_html( troya_option( 'site_phone_1', '8 (843) 564-46-46' ) ); ?></a>
	</div>
</section>

<?php
get_footer();
