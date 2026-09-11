<?php
/**
 * Template Name: Фотогалерея
 *
 * @package Troya
 */

get_header();

$hotel_sections = array(
	array(
		'id'    => 'building',
		'title' => 'Здание и территория',
		'items' => troya_theme_gallery_album( 'building', 'Отель Троя' ),
	),
	array(
		'id'    => 'reception',
		'title' => 'Ресепшен',
		'items' => troya_theme_gallery_album( 'reception', 'Ресепшен' ),
	),
	array(
		'id'    => 'dining',
		'title' => 'Столовая',
		'items' => troya_theme_gallery_album( 'dining', 'Столовая' ),
	),
);

$rooms_q = new WP_Query(
	array(
		'post_type'      => 'troya_room',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	)
);

$all_photos = array();
foreach ( $hotel_sections as $section ) {
	foreach ( $section['items'] as $item ) {
		$all_photos[] = $item;
	}
}

$room_blocks = array();
while ( $rooms_q->have_posts() ) {
	$rooms_q->the_post();
	$room_items = troya_room_gallery_items( get_the_ID() );
	if ( ! $room_items ) {
		continue;
	}
	$start = count( $all_photos );
	foreach ( $room_items as $item ) {
		$all_photos[] = $item;
	}
	$room_blocks[] = array(
		'title' => get_the_title(),
		'url'   => get_permalink(),
		'items' => $room_items,
		'start' => $start,
	);
}
wp_reset_postdata();

$total = count( $all_photos );
?>
<section class="hero hero--page">
	<div class="hero__bg">
		<img class="hero__img" src="<?php echo esc_url( troya_img_url( troya_option( 'hero_poster' ), troya_asset( 'photos/hero-building.jpg' ) ) ); ?>" alt="Фотогалерея отеля Троя" />
		<div class="hero__overlay"></div>
		<div class="hero__grain"></div>
	</div>
	<p class="hero__city"><?php echo esc_html( troya_option( 'site_address_short', 'Казань — ул. Восстания, 119' ) ); ?></p>
	<div class="container hero__content">
		<p class="crumbs anim-up" style="--d: 0.2s">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a><span>/</span><span>Галерея</span>
		</p>
		<p class="hero__eyebrow anim-up" style="--d: 0.35s">Фотографии</p>
		<h1 class="hero__title anim-up" style="--d: 0.5s">Галерея отеля</h1>
		<p class="hero__text anim-up" style="--d: 0.65s">Номера, ресепшен, столовая и виды здания — все фото в одном месте.</p>
	</div>
</section>

<section class="photo-gallery section" data-site-gallery>
	<div class="container">
		<nav class="photo-gallery__tabs" aria-label="Разделы галереи">
			<a href="#building" class="photo-gallery__tab">Здание</a>
			<a href="#reception" class="photo-gallery__tab">Ресепшен</a>
			<a href="#dining" class="photo-gallery__tab">Столовая</a>
			<a href="#rooms-photos" class="photo-gallery__tab">Номера</a>
		</nav>

		<?php
		$cursor = 0;
		foreach ( $hotel_sections as $section ) :
			if ( empty( $section['items'] ) ) {
				continue;
			}
			?>
			<div class="photo-gallery__block reveal" id="<?php echo esc_attr( $section['id'] ); ?>">
				<h2 class="heading gold-line"><?php echo esc_html( $section['title'] ); ?></h2>
				<div class="photo-gallery__grid">
					<?php foreach ( $section['items'] as $item ) : ?>
						<button
							type="button"
							class="photo-gallery__item"
							data-gallery-open
							data-index="<?php echo (int) $cursor; ?>"
							aria-label="<?php echo esc_attr( $item['alt'] ); ?>"
						>
							<img src="<?php echo esc_url( $item['url'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" loading="lazy" decoding="async" />
						</button>
						<?php $cursor++; ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="photo-gallery__block reveal" id="rooms-photos">
			<h2 class="heading gold-line">Номера</h2>
			<?php foreach ( $room_blocks as $block ) : ?>
				<div class="photo-gallery__room">
					<div class="photo-gallery__room-head">
						<h3 class="photo-gallery__room-title"><?php echo esc_html( $block['title'] ); ?></h3>
						<a class="photo-gallery__room-link" href="<?php echo esc_url( (string) $block['url'] ); ?>">Страница номера</a>
					</div>
					<div class="photo-gallery__grid">
						<?php foreach ( $block['items'] as $i => $item ) : ?>
							<button
								type="button"
								class="photo-gallery__item"
								data-gallery-open
								data-index="<?php echo (int) ( $block['start'] + $i ); ?>"
								aria-label="<?php echo esc_attr( $item['alt'] ); ?>"
							>
								<img src="<?php echo esc_url( $item['url'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" loading="lazy" decoding="async" />
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="room-lightbox" id="site-lightbox" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="Просмотр фото">
		<button type="button" class="room-lightbox__close" data-lightbox-close aria-label="Закрыть">
			<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" /></svg>
		</button>
		<button type="button" class="room-lightbox__nav room-lightbox__nav--prev" data-lightbox-prev aria-label="Предыдущее фото">
			<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 5L8 12l7 7" /></svg>
		</button>
		<figure class="room-lightbox__figure">
			<img class="room-lightbox__img" id="site-lightbox-img" src="" alt="" />
		</figure>
		<button type="button" class="room-lightbox__nav room-lightbox__nav--next" data-lightbox-next aria-label="Следующее фото">
			<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" /></svg>
		</button>
		<span class="room-lightbox__counter" aria-live="polite">
			<b data-lightbox-current>01</b><i>/</i><span data-lightbox-total><?php echo esc_html( str_pad( (string) max( 1, $total ), 2, '0', STR_PAD_LEFT ) ); ?></span>
		</span>
	</div>

	<script type="application/json" id="site-gallery-data"><?php echo wp_json_encode( array_values( $all_photos ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
</section>

<?php
get_footer();
