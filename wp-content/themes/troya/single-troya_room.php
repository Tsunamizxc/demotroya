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
				<?php
				$gallery_items = array();
				if ( $gallery ) {
					foreach ( $gallery as $photo ) {
						$url = is_array( $photo ) ? (string) ( $photo['url'] ?? '' ) : '';
						if ( ! $url ) {
							continue;
						}
						$gallery_items[] = array(
							'url' => $url,
							'alt' => is_array( $photo ) ? (string) ( $photo['alt'] ?? $room_title ) : $room_title,
						);
					}
				}
				if ( ! $gallery_items && $img ) {
					$gallery_items[] = array(
						'url' => $img,
						'alt' => $room_title,
					);
				}
				$gallery_count = count( $gallery_items );
				?>
				<?php if ( $gallery_items ) : ?>
					<div class="room-gallery<?php echo 1 === $gallery_count ? ' room-gallery--single' : ''; ?>" data-room-gallery>
						<div class="room-gallery__viewport">
							<div class="room-gallery__track" id="room-gallery-track">
								<?php foreach ( $gallery_items as $i => $photo ) : ?>
									<figure class="room-gallery__slide<?php echo 0 === $i ? ' is-active' : ''; ?>" data-index="<?php echo (int) $i; ?>">
										<button
											type="button"
											class="room-gallery__open"
											data-gallery-open
											data-index="<?php echo (int) $i; ?>"
											aria-label="Открыть фото <?php echo esc_attr( (string) ( $i + 1 ) ); ?> на весь экран"
										>
											<img
												src="<?php echo esc_url( $photo['url'] ); ?>"
												alt="<?php echo esc_attr( $photo['alt'] ); ?>"
												loading="<?php echo 0 === $i ? 'eager' : 'lazy'; ?>"
												decoding="async"
											/>
										</button>
									</figure>
								<?php endforeach; ?>
							</div>

							<?php if ( $gallery_count > 1 ) : ?>
								<button type="button" class="room-gallery__nav room-gallery__nav--prev" data-gallery-prev aria-label="Предыдущее фото">
									<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 5L8 12l7 7" /></svg>
								</button>
								<button type="button" class="room-gallery__nav room-gallery__nav--next" data-gallery-next aria-label="Следующее фото">
									<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" /></svg>
								</button>
								<span class="room-gallery__counter" aria-live="polite">
									<b data-gallery-current>01</b><i>/</i><span data-gallery-total><?php echo esc_html( str_pad( (string) $gallery_count, 2, '0', STR_PAD_LEFT ) ); ?></span>
								</span>
							<?php endif; ?>
						</div>

						<?php if ( $gallery_count > 1 ) : ?>
							<div class="room-gallery__thumbs" role="tablist" aria-label="Миниатюры галереи">
								<?php foreach ( $gallery_items as $i => $photo ) : ?>
									<button
										type="button"
										class="room-gallery__thumb<?php echo 0 === $i ? ' is-active' : ''; ?>"
										data-gallery-thumb
										data-index="<?php echo (int) $i; ?>"
										aria-label="Фото <?php echo esc_attr( (string) ( $i + 1 ) ); ?>"
										aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
									>
										<img src="<?php echo esc_url( $photo['url'] ); ?>" alt="" loading="lazy" decoding="async" />
									</button>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<p class="room-detail__lead"><?php echo esc_html( (string) troya_field( 'room_desc' ) ); ?></p>
				<div class="text"><?php the_content(); ?></div>
				<?php if ( is_array( $feats ) && $feats ) : ?>
					<ul class="room-band__facts">
						<?php foreach ( $feats as $f ) : ?>
							<li><?php echo esc_html( $f['text'] ?? '' ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<aside class="room-bookcard room-bookcard--stacked reveal-right">
				<div class="room-bookcard__top">
					<p class="eyebrow">Этот номер</p>
					<h2 class="room-bookcard__title"><?php the_title(); ?></h2>
					<p class="room-bookcard__price"><?php echo esc_html( (string) troya_field( 'room_price' ) ); ?><small>за ночь</small></p>
					<p class="room-bookcard__note"><?php echo esc_html( troya_option( 'rooms_note' ) ); ?></p>
				</div>

				<?php troya_render_room_availability( get_the_ID(), true ); ?>

				<div class="room-bookcard__actions">
					<a class="btn btn--gold" href="<?php echo esc_url( $book_url ); ?>">Забронировать</a>
					<button type="button" class="btn btn--outline" data-open-modal data-room="<?php echo esc_attr( $room_title ); ?>">Забронировать по телефону</button>
					<a class="room-bookcard__phone" href="tel:<?php echo esc_attr( preg_replace( '/\D+/', '', troya_option( 'site_phone_1', '88435644646' ) ) ); ?>"><?php echo esc_html( troya_option( 'site_phone_1' ) ); ?></a>
				</div>
			</aside>
		</div>
	</section>

	<?php if ( ! empty( $gallery_items ) ) : ?>
		<div class="room-lightbox" id="room-lightbox" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="Просмотр фото номера">
			<button type="button" class="room-lightbox__close" data-lightbox-close aria-label="Закрыть">
				<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" /></svg>
			</button>
			<button type="button" class="room-lightbox__nav room-lightbox__nav--prev" data-lightbox-prev aria-label="Предыдущее фото">
				<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 5L8 12l7 7" /></svg>
			</button>
			<figure class="room-lightbox__figure">
				<img class="room-lightbox__img" id="room-lightbox-img" src="" alt="" />
			</figure>
			<button type="button" class="room-lightbox__nav room-lightbox__nav--next" data-lightbox-next aria-label="Следующее фото">
				<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" /></svg>
			</button>
			<span class="room-lightbox__counter" aria-live="polite">
				<b data-lightbox-current>01</b><i>/</i><span data-lightbox-total><?php echo esc_html( str_pad( (string) $gallery_count, 2, '0', STR_PAD_LEFT ) ); ?></span>
			</span>
		</div>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
