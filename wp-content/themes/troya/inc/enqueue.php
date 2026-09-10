<?php
/**
 * Assets.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'troya_enqueue_assets' );

function troya_enqueue_assets(): void {
	wp_enqueue_style(
		'troya-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Manrope:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'troya-main',
		troya_asset( 'styles.css' ),
		array( 'troya-fonts' ),
		TROYA_VERSION
	);

	wp_enqueue_script(
		'troya-main',
		troya_asset( 'main.js' ),
		array(),
		TROYA_VERSION,
		true
	);

	$amenities = troya_option( 'amenities_items', array() );
	$amen_out  = array();

	$icon_fallbacks = get_option( 'troya_amenity_icons', array() );
	if ( is_array( $amenities ) ) {
		foreach ( $amenities as $i => $row ) {
			$fallback = is_array( $icon_fallbacks ) && ! empty( $icon_fallbacks[ $i ] )
				? (string) $icon_fallbacks[ $i ]
				: troya_asset( 'amenities/parking.png' );
			$amen_out[] = array(
				'icon'  => troya_img_url( $row['icon'] ?? null, $fallback ),
				'title' => (string) ( $row['title'] ?? '' ),
				'desc'  => (string) ( $row['desc'] ?? '' ),
			);
		}
	}

	$reviews = troya_option( 'reviews_items', array() );
	$rev_out = array();

	if ( is_array( $reviews ) ) {
		foreach ( $reviews as $row ) {
			$rev_out[] = array(
				'name' => (string) ( $row['name'] ?? '' ),
				'text' => (string) ( $row['text'] ?? '' ),
			);
		}
	}

	$excursions = troya_option( 'excursions_items', array() );
	$exc_out    = array();

	if ( is_array( $excursions ) ) {
		foreach ( $excursions as $i => $row ) {
			$exc_out[] = array(
				'num'   => sprintf( '%02d', $i + 1 ),
				'title' => (string) ( $row['title'] ?? '' ),
				'desc'  => (string) ( $row['desc'] ?? '' ),
			);
		}
	}

	wp_localize_script(
		'troya-main',
		'TroyaData',
		array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'troya_forms' ),
			'rooms'      => troya_get_rooms_payload(),
			'amenities'  => $amen_out,
			'reviews'    => $rev_out,
			'excursions' => $exc_out,
			'homeUrl'    => home_url( '/' ),
			'roomsUrl'   => troya_rooms_url(),
		)
	);
}
