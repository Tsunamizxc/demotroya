<?php
/**
 * Import room photos from «Троя фото на сайт» into media + ACF.
 *
 * Run: php wp-cli.phar eval-file wp-content/themes/troya/scripts/import-room-photos.php
 *
 * @package Troya
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$map = array(
	7  => '2-местный Стандарт+ с раздельными кроватями',
	8  => '3-местный Стандарт+ STD Standart+',
	9  => 'БИЗНЕС – КОМФОРТ',
	10 => 'БИЗНЕС – КОМФОРТ СЕМЕЙНЫЙ',
	11 => 'БИЗНЕС – КОМФОРТ+',
);

$root = dirname( ABSPATH ) === ABSPATH
	? ABSPATH
	: rtrim( ABSPATH, '/\\' );

// Site lives in demotroya root (= ABSPATH).
$photos_root = trailingslashit( ABSPATH ) . 'Троя фото на сайт';

if ( ! is_dir( $photos_root ) ) {
	WP_CLI::error( 'Photos folder not found: ' . $photos_root );
}

$tmp_base = trailingslashit( WP_CONTENT_DIR ) . 'uploads/troya-import-tmp';
wp_mkdir_p( $tmp_base );

foreach ( $map as $post_id => $folder_name ) {
	$post = get_post( $post_id );
	if ( ! $post || 'troya_room' !== $post->post_type ) {
		WP_CLI::warning( "Skip missing room ID {$post_id}" );
		continue;
	}

	$folder = trailingslashit( $photos_root ) . $folder_name;
	if ( ! is_dir( $folder ) ) {
		WP_CLI::warning( "Folder not found for #{$post_id}: {$folder_name}" );
		continue;
	}

	$files = glob( $folder . '/*.{jpg,JPG,jpeg,JPEG,png,PNG,webp,WEBP}', GLOB_BRACE );
	if ( ! $files ) {
		// Fallback without brace (Windows).
		$files = array_values(
			array_filter(
				scandir( $folder ) ?: array(),
				static function ( $name ) use ( $folder ) {
					if ( '.' === $name || '..' === $name ) {
						return false;
					}
					$ext = strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );
					return in_array( $ext, array( 'jpg', 'jpeg', 'png', 'webp' ), true ) && is_file( $folder . '/' . $name );
				}
			)
		);
		$files = array_map(
			static fn( $name ) => $folder . '/' . $name,
			$files
		);
	}

	sort( $files, SORT_NATURAL | SORT_FLAG_CASE );

	if ( ! $files ) {
		WP_CLI::warning( "No images in {$folder_name}" );
		continue;
	}

	$room_tmp = trailingslashit( $tmp_base ) . 'room-' . $post_id;
	wp_mkdir_p( $room_tmp );

	$attachment_ids = array();
	$i              = 0;

	foreach ( $files as $src ) {
		$i++;
		$ext  = strtolower( pathinfo( $src, PATHINFO_EXTENSION ) ) ?: 'jpg';
		$dest = $room_tmp . '/photo-' . sprintf( '%02d', $i ) . '.' . $ext;

		if ( ! copy( $src, $dest ) ) {
			WP_CLI::warning( "Copy failed: {$src}" );
			continue;
		}

		$file_array = array(
			'name'     => basename( $dest ),
			'tmp_name' => $dest,
		);

		// media_handle_sideload moves the file; use a fresh copy path that WP expects as uploaded tmp.
		$upload = wp_upload_bits( basename( $dest ), null, file_get_contents( $dest ) );
		if ( ! empty( $upload['error'] ) ) {
			WP_CLI::warning( 'Upload error: ' . $upload['error'] );
			continue;
		}

		$filetype   = wp_check_filetype( $upload['file'], null );
		$attachment = array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => sanitize_text_field( $post->post_title . ' — фото ' . $i ),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_parent'    => $post_id,
		);

		$attach_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );
		if ( is_wp_error( $attach_id ) || ! $attach_id ) {
			WP_CLI::warning( 'Attachment insert failed for ' . basename( $src ) );
			continue;
		}

		$meta = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
		wp_update_attachment_metadata( $attach_id, $meta );
		update_post_meta( $attach_id, '_wp_attachment_image_alt', $post->post_title );

		$attachment_ids[] = (int) $attach_id;
	}

	if ( ! $attachment_ids ) {
		WP_CLI::warning( "No attachments imported for #{$post_id}" );
		continue;
	}

	$main_id = $attachment_ids[0];
	set_post_thumbnail( $post_id, $main_id );
	update_field( 'room_image', $main_id, $post_id );
	update_field( 'room_gallery', $attachment_ids, $post_id );
	delete_post_meta( $post_id, '_troya_room_img_path' );

	WP_CLI::success(
		sprintf(
			'#%d %s — %d photos (main #%d)',
			$post_id,
			$post->post_title,
			count( $attachment_ids ),
			$main_id
		)
	);
}

// Cleanup temp copies (uploaded files already moved/copied into uploads).
troya_rrmdir( $tmp_base );
WP_CLI::success( 'Room photo import finished.' );

/**
 * Recursively remove directory.
 */
function troya_rrmdir( string $dir ): void {
	if ( ! is_dir( $dir ) ) {
		return;
	}

	$items = scandir( $dir );
	if ( ! $items ) {
		return;
	}

	foreach ( $items as $item ) {
		if ( '.' === $item || '..' === $item ) {
			continue;
		}
		$path = $dir . DIRECTORY_SEPARATOR . $item;
		if ( is_dir( $path ) ) {
			troya_rrmdir( $path );
		} else {
			@unlink( $path );
		}
	}

	@rmdir( $dir );
}
