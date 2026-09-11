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
define( 'TROYA_SCHEMA_VERSION', 5 );

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
