<?php
/**
 * Helpers.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param mixed $default Default value.
 * @return mixed
 */
function troya_option( string $field, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $field, 'option' );

	return ( null === $value || false === $value || '' === $value ) ? $default : $value;
}

/**
 * @param mixed $default Default value.
 * @return mixed
 */
function troya_field( string $field, $default = '', $post_id = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $field, $post_id );

	return ( null === $value || false === $value || '' === $value ) ? $default : $value;
}

function troya_img_url( $image, string $fallback = '' ): string {
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		return (string) $image['url'];
	}

	if ( is_numeric( $image ) ) {
		$url = wp_get_attachment_image_url( (int) $image, 'full' );
		if ( $url ) {
			return $url;
		}
	}

	if ( is_string( $image ) && $image !== '' ) {
		return $image;
	}

	return $fallback;
}

function troya_asset( string $path ): string {
	return trailingslashit( TROYA_URI ) . 'assets/' . ltrim( $path, '/' );
}

/**
 * @return array<int, string>
 */
function troya_parse_email_list( string $raw ): array {
	$parts = preg_split( '/[\s,;]+/', $raw ) ?: array();
	$out   = array();

	foreach ( $parts as $email ) {
		$email = sanitize_email( $email );
		if ( $email && is_email( $email ) ) {
			$out[] = $email;
		}
	}

	return array_values( array_unique( $out ) );
}

function troya_get_client_ip(): string {
	$keys = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );

	foreach ( $keys as $key ) {
		if ( empty( $_SERVER[ $key ] ) ) {
			continue;
		}

		$raw = (string) wp_unslash( $_SERVER[ $key ] );
		$ip  = trim( explode( ',', $raw )[0] );

		if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return $ip;
		}
	}

	return '';
}

function troya_rooms_url(): string {
	$val = troya_option( 'rooms_page_id', 0 );

	if ( is_numeric( $val ) && (int) $val > 0 ) {
		$url = get_permalink( (int) $val );
		if ( $url ) {
			return $url;
		}
	}

	if ( is_string( $val ) && $val !== '' ) {
		return $val;
	}

	$page = get_page_by_path( 'nomera' );
	if ( $page ) {
		return get_permalink( $page ) ?: home_url( '/nomera/' );
	}

	return home_url( '/nomera/' );
}

/**
 * Rooms payload for front-end slider.
 *
 * @return array<int, array<string, mixed>>
 */
function troya_get_rooms_payload(): array {
	$query = new WP_Query(
		array(
			'post_type'      => 'troya_room',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);

	$rooms = array();
	$index = 0;

	foreach ( $query->posts as $post ) {
		$index++;
		$features = troya_field( 'room_features', array(), $post->ID );
		$feat     = array();

		if ( is_array( $features ) ) {
			foreach ( $features as $row ) {
				if ( ! empty( $row['text'] ) ) {
					$feat[] = (string) $row['text'];
				}
			}
		}

		$fallback = get_the_post_thumbnail_url( $post->ID, 'large' );
		if ( ! $fallback ) {
			$gallery = troya_field( 'room_gallery', array(), $post->ID );
			if ( is_array( $gallery ) && ! empty( $gallery[0]['url'] ) ) {
				$fallback = (string) $gallery[0]['url'];
			}
		}
		if ( ! $fallback ) {
			$path     = (string) get_post_meta( $post->ID, '_troya_room_img_path', true );
			$fallback = $path ? troya_asset( $path ) : troya_asset( 'photos/room-1.jpg' );
		}
		$img = troya_img_url( troya_field( 'room_image', null, $post->ID ), $fallback );

		$bnovo_ids = troya_room_bnovo_ids( $post->ID );

		$video_rel  = 'videos/room-scroll-' . $index . '.mp4';
		$video_path = TROYA_DIR . '/assets/' . $video_rel;
		$video_url  = file_exists( $video_path ) ? troya_asset( $video_rel ) : '';

		$rooms[] = array(
			'name'     => get_the_title( $post ),
			'subtitle' => (string) troya_field( 'room_subtitle', '', $post->ID ),
			'price'    => (string) troya_field( 'room_price', '', $post->ID ),
			'desc'     => (string) troya_field( 'room_desc', '', $post->ID ),
			'features' => $feat,
			'img'      => $img,
			'tag'      => (string) troya_field( 'room_tag', '', $post->ID ),
			'href'     => get_permalink( $post ),
			'bookUrl'  => troya_room_booking_url( $bnovo_ids ),
			'bnovoIds' => $bnovo_ids,
			'video'    => $video_url,
		);
	}

	wp_reset_postdata();

	return $rooms;
}
