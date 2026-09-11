<?php
/**
 * Auto-migrations so production works right after theme deploy / git pull.
 *
 * Creates required pages, sets templates, flushes rewrites, stores Bnovo UID.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

/** Bump when deploy needs DB-side fixes without manual admin clicks. */
define( 'TROYA_SCHEMA_VERSION', 9 );

add_action( 'init', 'troya_maybe_run_migrations', 5 );
add_action( 'after_switch_theme', 'troya_force_run_migrations' );

function troya_force_run_migrations(): void {
	delete_option( 'troya_schema_version' );
	troya_run_migrations();
}

function troya_maybe_run_migrations(): void {
	$current = (int) get_option( 'troya_schema_version', 0 );
	if ( $current >= TROYA_SCHEMA_VERSION ) {
		return;
	}

	troya_run_migrations();
}

function troya_run_migrations(): void {
	$from = (int) get_option( 'troya_schema_version', 0 );

	if ( $from < 1 ) {
		if ( function_exists( 'troya_seed_content' ) && ! get_option( 'troya_seeded_v1' ) ) {
			troya_seed_content();
		}
	}

	if ( $from < 2 ) {
		troya_ensure_core_pages();
	}

	if ( $from < 3 ) {
		troya_ensure_booking_stack();
	}

	if ( $from < 4 ) {
		troya_ensure_bnovo_room_ids();
	}

	if ( $from < 5 ) {
		if ( function_exists( 'update_field' ) ) {
			update_field( 'rooms_title', 'Наши номера', 'option' );
		}
	}

	if ( $from < 6 ) {
		troya_rename_rooms_v6();
	}

	if ( $from < 7 ) {
		troya_refresh_rooms_content_v7();
		troya_ensure_gallery_page();
	}

	if ( $from < 8 ) {
		troya_dedupe_all_room_galleries();
	}

	if ( $from < 9 ) {
		troya_configure_smtp_beget_v9();
	}

	update_option( 'troya_schema_version', TROYA_SCHEMA_VERSION );
	update_option( 'troya_last_migrated_at', current_time( 'mysql' ) );
	flush_rewrite_rules( false );
}

/**
 * Ensure home / rooms pages exist and front page is assigned.
 */
function troya_ensure_core_pages(): void {
	$home_id = (int) get_option( 'page_on_front' );
	if ( ! $home_id ) {
		$home = get_page_by_path( 'home' );
		if ( ! $home ) {
			$home_id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_title'  => 'Главная',
					'post_name'   => 'home',
				)
			);
		} else {
			$home_id = (int) $home->ID;
		}

		if ( $home_id && ! is_wp_error( $home_id ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home_id );
		}
	}

	$rooms = get_page_by_path( 'nomera' );
	if ( ! $rooms ) {
		$rooms_id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'Номера',
				'post_name'   => 'nomera',
			)
		);
	} else {
		$rooms_id = (int) $rooms->ID;
	}

	if ( $rooms_id && ! is_wp_error( $rooms_id ) ) {
		update_post_meta( $rooms_id, '_wp_page_template', 'page-nomera.php' );
		if ( function_exists( 'update_field' ) ) {
			update_field( 'rooms_page_id', $rooms_id, 'option' );
		}
	}

	troya_ensure_gallery_page();
}

/**
 * Ensure /galereya page exists.
 */
function troya_ensure_gallery_page(): void {
	$page = get_page_by_path( 'galereya' );
	if ( ! $page ) {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Галерея',
				'post_name'    => 'galereya',
				'post_content' => '',
			)
		);
	} else {
		$page_id = (int) $page->ID;
		if ( 'publish' !== $page->post_status ) {
			wp_update_post(
				array(
					'ID'          => $page_id,
					'post_status' => 'publish',
				)
			);
		}
	}

	if ( ! $page_id || is_wp_error( $page_id ) ) {
		return;
	}

	update_post_meta( $page_id, '_wp_page_template', 'page-gallery.php' );
}

/**
 * Configure Beget SMTP for booking notifications.
 */
function troya_configure_smtp_beget_v9(): void {
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	update_field( 'smtp_host', 'smtp.beget.com', 'option' );
	update_field( 'smtp_port', 465, 'option' );
	update_field( 'smtp_encryption', 'ssl', 'option' );
	update_field( 'smtp_username', 'troya-hotel-tickets@troy-a-hotel.ru', 'option' );
	update_field( 'smtp_password', 'G*1sBGDtHtQD', 'option' );
	update_field( 'smtp_from_email', 'troya-hotel-tickets@troy-a-hotel.ru', 'option' );
	update_field( 'smtp_from_name', 'Отель Троя', 'option' );
	update_field( 'smtp_to_email', 'tech@cursiva.ru', 'option' );
}

/**
 * Ensure /booking page + Bnovo options exist (fixes empty troy-a-hotel.ru/booking).
 */
function troya_ensure_booking_stack(): void {
	$booking = get_page_by_path( 'booking' );
	if ( ! $booking ) {
		$booking_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Бронирование',
				'post_name'    => 'booking',
				'post_content' => '',
			)
		);
	} else {
		$booking_id = (int) $booking->ID;
		if ( 'publish' !== $booking->post_status ) {
			wp_update_post(
				array(
					'ID'          => $booking_id,
					'post_status' => 'publish',
				)
			);
		}
	}

	if ( ! $booking_id || is_wp_error( $booking_id ) ) {
		return;
	}

	update_post_meta( $booking_id, '_wp_page_template', 'page-booking.php' );

	if ( function_exists( 'update_field' ) ) {
		$uid = troya_option( 'bnovo_uid', '' );
		if ( ! $uid ) {
			update_field( 'bnovo_uid', 'eb9cee17-77f8-4f25-9817-b3fc2080617b', 'option' );
		}
		update_field( 'booking_page_id', $booking_id, 'option' );
		update_field( 'seo_booking_title', 'Онлайн-бронирование — Отель Троя, Казань', 'option' );
		update_field(
			'seo_booking_description',
			'Забронируйте номер в отеле «Троя» в Казани онлайн. Прямое бронирование без комиссии.',
			'option'
		);
		update_field( 'contact_map_url', 'https://yandex.ru/maps/-/CThvQI9G', 'option' );
	}

	// Permalink structure for pretty /booking/
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
}

/**
 * Rename room titles to Russian catalogue names.
 */
function troya_rename_rooms_v6(): void {
	$map = array(
		'Standard+'           => '2-местный Стандарт+ с раздельными кроватями',
		'Standard+ Family'    => '3-местный Стандарт+',
		'Standart+ Family'    => '3-местный Стандарт+',
		'Business Comfort'    => 'БИЗНЕС – КОМФОРТ',
		'Business Family'     => 'БИЗНЕС – КОМФОРТ СЕМЕЙНЫЙ',
		'Business Comfort+'   => 'БИЗНЕС – КОМФОРТ+',
	);

	$q = new WP_Query(
		array(
			'post_type'      => 'troya_room',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		)
	);

	foreach ( $q->posts as $id ) {
		$id    = (int) $id;
		$title = get_the_title( $id );
		if ( empty( $map[ $title ] ) ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'         => $id,
				'post_title' => $map[ $title ],
			)
		);
	}

	if ( function_exists( 'update_field' ) ) {
		update_field( 'hero_video', null, 'option' );
		update_field(
			'seo_rooms_description',
			'Каталог номеров отеля «Троя» в Казани: стандарт, бизнес-комфорт и семейные категории.',
			'option'
		);
	}
}

/**
 * Canonical room catalogue used by migration v7.
 *
 * @return array<int, array<string, mixed>>
 */
function troya_canonical_rooms_v7(): array {
	return array(
		1 => array(
			'title'    => '2-х местный Стандарт',
			'tag'      => 'Стандарт',
			'subtitle' => 'Двухместный',
			'desc'     => 'Уютный номер с двумя раздельными кроватями, мини-холодильником и современным санузлом.',
			'features' => array( 'Две кровати', 'Телевизор', 'Кондиционер', 'Мини-холодильник', 'Современный санузел' ),
		),
		2 => array(
			'title'    => '3-х местный Стандарт',
			'tag'      => 'Стандарт',
			'subtitle' => 'Трёхместный',
			'desc'     => 'Просторный номер для семьи или компании с тремя раздельными кроватями, мини-холодильником и современным санузлом.',
			'features' => array( 'Три кровати', 'Плазменный ТВ', 'Кондиционер', 'Мини-холодильник', 'Современный санузел' ),
		),
		3 => array(
			'title'    => 'Бизнес-комфорт',
			'tag'      => 'Бизнес',
			'subtitle' => 'Улучшенный',
			'desc'     => 'Двуспальная кровать, мини-холодильник и современный санузел. Удобный выбор для деловых поездок.',
			'features' => array( 'Двуспальная кровать', 'Телевизор', 'Кондиционер', 'Мини-холодильник', 'Современный санузел' ),
		),
		4 => array(
			'title'    => 'Бизнес-комфорт семейный',
			'tag'      => 'Семейный',
			'subtitle' => 'Трёхместный',
			'desc'     => 'Рассчитан на трёхместное размещение: основное спальное место и третье — на мягком диване. Есть мини-холодильник и современный санузел.',
			'features' => array( 'Трёхместное размещение', 'Мягкий диван', 'Халат и тапочки', 'Мини-холодильник', 'Современный санузел' ),
		),
		5 => array(
			'title'    => 'Бизнес-комфорт+',
			'tag'      => 'Премиум',
			'subtitle' => 'Трёхместный',
			'desc'     => 'Рассчитан на трёхместное размещение: большая кровать и третье место на мягком диване. Мини-холодильник и современный санузел.',
			'features' => array( 'Большая кровать', 'Мягкий диван', 'Халат и тапочки', 'Мини-холодильник', 'Современный санузел' ),
		),
	);
}

/**
 * Rename rooms, refresh copy/features, remove duplicate gallery shots, drop extra-bed notes.
 */
function troya_refresh_rooms_content_v7(): void {
	$canonical = troya_canonical_rooms_v7();

	$title_aliases = array(
		'Standard+'                                       => 1,
		'2-местный Стандарт+ с раздельными кроватями'     => 1,
		'2-х местный Стандарт'                            => 1,
		'Standard+ Family'                                => 2,
		'Standart+ Family'                                => 2,
		'3-местный Стандарт+'                             => 2,
		'3-х местный Стандарт'                            => 2,
		'Business Comfort'                                => 3,
		'БИЗНЕС – КОМФОРТ'                                => 3,
		'Бизнес-комфорт'                                  => 3,
		'Business Family'                                 => 4,
		'БИЗНЕС – КОМФОРТ СЕМЕЙНЫЙ'                       => 4,
		'Бизнес-комфорт семейный'                         => 4,
		'Business Comfort+'                               => 5,
		'БИЗНЕС – КОМФОРТ+'                               => 5,
		'Бизнес-комфорт+'                                 => 5,
	);

	$q = new WP_Query(
		array(
			'post_type'      => 'troya_room',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);

	$index = 0;
	foreach ( $q->posts as $post ) {
		$index++;
		$id    = (int) $post->ID;
		$title = get_the_title( $id );
		$slot  = $title_aliases[ $title ] ?? ( $canonical[ $index ] ? $index : 0 );
		if ( ! $slot || empty( $canonical[ $slot ] ) ) {
			troya_dedupe_room_gallery( $id );
			continue;
		}

		$data = $canonical[ $slot ];
		wp_update_post(
			array(
				'ID'          => $id,
				'post_title'  => $data['title'],
				'menu_order'  => (int) $slot,
			)
		);

		if ( function_exists( 'update_field' ) ) {
			update_field( 'room_tag', $data['tag'], $id );
			update_field( 'room_subtitle', $data['subtitle'], $id );
			update_field( 'room_desc', $data['desc'], $id );
			update_field(
				'room_features',
				array_map(
					static fn( $text ) => array( 'text' => $text ),
					$data['features']
				),
				$id
			);
		}

		troya_dedupe_room_gallery( $id );
	}

	if ( function_exists( 'update_field' ) ) {
		update_field( 'rooms_note', 'Завтрак — 300 ₽', 'option' );
		update_field( 'modal_hint', '* ОБЯЗАТЕЛЬНЫЕ ПОЛЯ · ЗАВТРАК +300 ₽', 'option' );
		update_field(
			'seo_rooms_description',
			'Каталог номеров отеля «Троя» в Казани: стандарт, бизнес-комфорт и семейные категории.',
			'option'
		);
	}
}

/**
 * Drop near-duplicate gallery images (same shot / recolor variants).
 */
function troya_dedupe_room_gallery( int $post_id ): void {
	if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$gallery = get_field( 'room_gallery', $post_id );
	if ( ! is_array( $gallery ) || ! $gallery ) {
		return;
	}

	$ids = array();
	foreach ( $gallery as $item ) {
		if ( is_numeric( $item ) ) {
			$ids[] = (int) $item;
		} elseif ( is_array( $item ) && ! empty( $item['ID'] ) ) {
			$ids[] = (int) $item['ID'];
		} elseif ( is_object( $item ) && isset( $item->ID ) ) {
			$ids[] = (int) $item->ID;
		}
	}

	$ids = array_values( array_unique( array_filter( $ids ) ) );
	if ( count( $ids ) < 2 ) {
		return;
	}

	$keep       = array();
	$seen_base  = array();
	$seen_hash  = array();

	foreach ( $ids as $attach_id ) {
		$file = (string) get_attached_file( $attach_id );
		$name = $file ? wp_basename( $file ) : (string) get_the_title( $attach_id );
		$base = preg_replace( '/\s*\(\d+\)(?=\.[^.]+$)/u', '', $name );
		$base = mb_strtolower( (string) $base );

		if ( $base && isset( $seen_base[ $base ] ) ) {
			continue;
		}

		$hash = troya_attachment_avg_hash( $attach_id );
		if ( $hash ) {
			$dup = false;
			foreach ( $seen_hash as $prev ) {
				if ( troya_hash_hamming( $hash, $prev ) <= 10 ) {
					$dup = true;
					break;
				}
			}
			if ( $dup ) {
				continue;
			}
			$seen_hash[] = $hash;
		}

		if ( $base ) {
			$seen_base[ $base ] = true;
		}
		$keep[] = $attach_id;
	}

	if ( count( $keep ) !== count( $ids ) ) {
		update_field( 'room_gallery', $keep, $post_id );
	}
}

/**
 * Average-hash fingerprint for near-duplicate detection.
 */
function troya_attachment_avg_hash( int $attach_id ): string {
	$file = (string) get_attached_file( $attach_id );
	if ( ! $file || ! is_file( $file ) || ! function_exists( 'imagecreatefromstring' ) ) {
		return '';
	}

	$data = @file_get_contents( $file );
	if ( ! $data ) {
		return '';
	}

	$src = @imagecreatefromstring( $data );
	if ( ! $src ) {
		return '';
	}

	$small = imagecreatetruecolor( 8, 8 );
	imagecopyresampled( $small, $src, 0, 0, 0, 0, 8, 8, imagesx( $src ), imagesy( $src ) );

	$sum  = 0;
	$gray = array();
	for ( $y = 0; $y < 8; $y++ ) {
		for ( $x = 0; $x < 8; $x++ ) {
			$rgb    = imagecolorat( $small, $x, $y );
			$r      = ( $rgb >> 16 ) & 0xFF;
			$g      = ( $rgb >> 8 ) & 0xFF;
			$b      = $rgb & 0xFF;
			$v      = (int) ( ( $r + $g + $b ) / 3 );
			$gray[] = $v;
			$sum   += $v;
		}
	}

	imagedestroy( $src );
	imagedestroy( $small );

	$avg  = $sum / 64;
	$bits = '';
	foreach ( $gray as $v ) {
		$bits .= ( $v >= $avg ) ? '1' : '0';
	}

	return $bits;
}

function troya_hash_hamming( string $a, string $b ): int {
	$len = strlen( $a );
	if ( ! $len || $len !== strlen( $b ) ) {
		return 64;
	}
	$d = 0;
	for ( $i = 0; $i < $len; $i++ ) {
		if ( $a[ $i ] !== $b[ $i ] ) {
			$d++;
		}
	}
	return $d;
}

function troya_dedupe_all_room_galleries(): void {
	$q = new WP_Query(
		array(
			'post_type'      => 'troya_room',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		)
	);

	foreach ( $q->posts as $id ) {
		troya_dedupe_room_gallery( (int) $id );
	}
}

/**
 * Map WP rooms to Bnovo rate/category IDs for calendars and onlyrooms filter.
 */
function troya_ensure_bnovo_room_ids(): void {
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$map = troya_bnovo_default_room_map();
	$q   = new WP_Query(
		array(
			'post_type'      => 'troya_room',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
		)
	);

	foreach ( $q->posts as $id ) {
		$title = get_the_title( (int) $id );
		if ( empty( $map[ $title ] ) ) {
			continue;
		}
		$current = (string) troya_field( 'room_bnovo_ids', '', (int) $id );
		if ( '' === trim( $current ) ) {
			update_field( 'room_bnovo_ids', $map[ $title ], (int) $id );
		}
	}

	$modal_title = (string) troya_option( 'modal_title', '' );
	if ( '' === $modal_title || 'Онлайн-бронирование' === $modal_title ) {
		update_field( 'modal_title', 'Бронирование по телефону', 'option' );
	}
}
