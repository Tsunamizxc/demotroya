<?php
/**
 * Single room with photo gallery and Bnovo availability.
 *
 * @package Troya
 */

get_header();

while ( have_posts() ) :
	the_post();

	$gallery = troya_field( 'room_gallery', array() );
	if ( ! is_array( $gallery ) ) {
		$gallery = array();
	}

	$main = troya_field( 'room_image' );
	$img  = troya_img_url(
		$main,
		get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: ''
	);

	if ( ! $img && ! empty( $gallery[0]['url'] ) ) {
		$img = (string) $gallery[0]['url'];
	}
	if ( ! $img ) {
		$path = (string) get_post_meta( get_the_ID(), '_troya_room_img_path', true );
		$img  = $path ? troya_asset( $path ) : troya_asset( 'photos/room-1.jpg' );
	}

	$feats      = troya_field( 'room_features', array() );
	$rooms_url  = troya_rooms_url();
	$book_url   = troya_room_booking_url( get_the_ID() );
	$room_title = get_the_title();
	?>
	<section class="hero hero--page">
		<div class="hero__bg">
			<img class="hero__img" src="<?php echo esc_url( $img ); ?>" alt="<?php the_title_attribute(); ?>" />
			<div class="hero__overlay"></div>
			<div class="hero__grain"></div>
		</div>
		<div class="container hero__content">
			<p class="crumbs anim-up" style="--d: 0.2s">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a><span>/</span>
				<a href="<?php echo esc_url( $rooms_url ); ?>">Номера</a><span>/</span>
				<span><?php the_title(); ?></span>
			</p>
			<p class="hero__eyebrow anim-up" style="--d: 0.35s"><?php echo esc_html( (string) troya_field( 'room_tag' ) ); ?></p>
			<h1 class="hero__title anim-up" style="--d: 0.5s"><?php the_title(); ?></h1>
			<p class="hero__text anim-up" style="--d: 0.7s"><?php echo esc_html( (string) troya_field( 'room_price' ) ); ?> · <?php echo esc_html( (string) troya_field( 'room_subtitle' ) ); ?></p>
		</div>
	</section>

	<section class="section">
		<div class="container room-detail">
			<div class="reveal-left">
				<?php if ( $gallery ) : ?>
					<div class="room-gallery">
						<?php foreach ( $gallery as $i => $photo ) : ?>
							<?php
							$url = is_array( $photo ) ? ( $photo['url'] ?? '' ) : '';
							$alt = is_array( $photo ) ? ( $photo['alt'] ?? get_the_title() ) : get_the_title();
							if ( ! $url ) {
								continue;
							}
							?>
							<figure class="room-gallery__item<?php echo 0 === $i ? ' is-wide' : ''; ?>">
								<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="<?php echo 0 === $i ? 'eager' : 'lazy'; ?>" decoding="async" />
							</figure>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<div class="room-gallery room-gallery--single">
						<figure class="room-gallery__item is-wide">
							<img src="<?php echo esc_url( $img ); ?>" alt="<?php the_title_attribute(); ?>" />
						</figure>
					</div>
				<?php endif; ?>

				<p class="room-detail__lead"><?php echo esc_html( (string) troya_field( 'room_desc' ) ); ?></p>
				<div class="text"><?php the_content(); ?></div>
				<?php if ( is_array( $feats ) && $feats ) : ?>
					<ul class="room-band__facts" style="list-style:none;display:flex;flex-wrap:wrap;gap:12px;margin-top:24px;padding:0;">
						<?php foreach ( $feats as $f ) : ?>
							<li style="background:var(--bg-mist,#efe8df);padding:10px 14px;border-radius:12px;"><?php echo esc_html( $f['text'] ?? '' ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php troya_render_room_availability( get_the_ID() ); ?>
			</div>
			<aside class="room-bookcard reveal-right">
				<p class="eyebrow">Этот номер</p>
				<p class="room-bookcard__price"><?php echo esc_html( (string) troya_field( 'room_price' ) ); ?><small>за ночь</small></p>
				<p class="room-bookcard__note"><?php echo esc_html( troya_option( 'rooms_note' ) ); ?></p>
				<a class="btn btn--gold" href="<?php echo esc_url( $book_url ); ?>">Забронировать</a>
				<button type="button" class="btn btn--outline" data-open-modal data-room="<?php echo esc_attr( $room_title ); ?>">Забронировать по телефону</button>
				<a class="room-bookcard__phone" href="tel:<?php echo esc_attr( preg_replace( '/\D+/', '', troya_option( 'site_phone_1', '88435644646' ) ) ); ?>"><?php echo esc_html( troya_option( 'site_phone_1' ) ); ?></a>
			</aside>
		</div>
	</section>
	<?php
endwhile;

get_footer();
