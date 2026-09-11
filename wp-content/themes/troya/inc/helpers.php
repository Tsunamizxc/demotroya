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

/**
 * Photo gallery page URL.
 */
function troya_gallery_url(): string {
	$page = get_page_by_path( 'galereya' );
	if ( $page ) {
		$url = get_permalink( $page );
		if ( $url ) {
			return $url;
		}
	}

	return home_url( '/galereya/' );
}

/**
 * Theme asset album for the photo gallery page.
 *
 * @return array<int, array{url:string,alt:string}>
 */
function troya_theme_gallery_album( string $slug, string $alt_prefix = '' ): array {
	$dir = TROYA_DIR . '/assets/photos/gallery/' . $slug;
	if ( ! is_dir( $dir ) ) {
		return array();
	}

	$files = array_values(
		array_filter(
			scandir( $dir ) ?: array(),
			static function ( $name ) use ( $dir ) {
				if ( '.' === $name || '..' === $name ) {
					return false;
				}
				$ext = strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );
				return in_array( $ext, array( 'jpg', 'jpeg', 'png', 'webp' ), true ) && is_file( $dir . '/' . $name );
			}
		)
	);

	natcasesort( $files );
	$files = array_values( $files );
	$items = array();
	$i     = 0;

	foreach ( $files as $name ) {
		$i++;
		$items[] = array(
			'url' => troya_asset( 'photos/gallery/' . $slug . '/' . $name ),
			'alt' => $alt_prefix ? sprintf( '%s — фото %d', $alt_prefix, $i ) : $name,
		);
	}

	return $items;
}

/**
 * Curated photo set for homepage gallery preview slider.
 *
 * @return array<int, array{url:string,alt:string}>
 */
function troya_home_gallery_preview_items( int $limit = 10 ): array {
	$albums = array(
		array( 'building', 'Отель Троя' ),
		array( 'reception', 'Ресепшен' ),
		array( 'dining', 'Столовая' ),
	);

	$items = array();
	foreach ( $albums as $album ) {
		foreach ( troya_theme_gallery_album( $album[0], $album[1] ) as $item ) {
			$items[] = $item;
		}
	}

	if ( $limit > 0 && count( $items ) > $limit ) {
		$items = array_slice( $items, 0, $limit );
	}

	return array_values( $items );
}

/**
 * Collect room gallery items for a post.
 *
 * @return array<int, array{url:string,alt:string}>
 */
function troya_room_gallery_items( int $post_id ): array {
	$gallery = troya_field( 'room_gallery', array(), $post_id );
	$items   = array();
	$title   = get_the_title( $post_id );

	if ( is_array( $gallery ) ) {
		foreach ( $gallery as $photo ) {
			$url = '';
			if ( is_array( $photo ) && ! empty( $photo['url'] ) ) {
				$url = (string) $photo['url'];
			} elseif ( is_numeric( $photo ) ) {
				$url = (string) ( wp_get_attachment_image_url( (int) $photo, 'large' ) ?: '' );
			}
			if ( ! $url ) {
				continue;
			}
			$items[] = array(
				'url' => $url,
				'alt' => $title,
			);
		}
	}

	if ( ! $items ) {
		$img = troya_img_url( troya_field( 'room_image', null, $post_id ), get_the_post_thumbnail_url( $post_id, 'large' ) ?: '' );
		if ( $img ) {
			$items[] = array(
				'url' => $img,
				'alt' => $title,
			);
		}
	}

	return $items;
}
