<?php
/**
 * Troya Hotel theme bootstrap.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

define( 'TROYA_VERSION', '1.7.2' );
define( 'TROYA_DIR', get_template_directory() );
define( 'TROYA_URI', get_template_directory_uri() );

require TROYA_DIR . '/inc/helpers.php';
require TROYA_DIR . '/inc/bnovo.php';
require TROYA_DIR . '/inc/post-types.php';
require TROYA_DIR . '/inc/acf-fields.php';
require TROYA_DIR . '/inc/enqueue.php';
require TROYA_DIR . '/inc/forms.php';
require TROYA_DIR . '/inc/seo.php';
require TROYA_DIR . '/inc/setup-content.php';
require TROYA_DIR . '/inc/migrate.php';

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

		register_nav_menus(
			array(
				'primary' => 'Основное меню',
			)
		);
	}
);

add_filter( 'use_block_editor_for_post', '__return_false', 100 );
add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
add_filter( 'use_widgets_block_editor', '__return_false' );
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
	},
	100
);
