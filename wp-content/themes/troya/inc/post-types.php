<?php
/**
 * Custom post types: rooms + bookings.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'troya_register_post_types' );
add_filter( 'manage_troya_booking_posts_columns', 'troya_booking_columns' );
add_action( 'manage_troya_booking_posts_custom_column', 'troya_booking_column_content', 10, 2 );
add_action( 'add_meta_boxes', 'troya_booking_meta_boxes' );

function troya_register_post_types(): void {
	register_post_type(
		'troya_room',
		array(
			'labels'       => array(
				'name'          => 'Номера',
				'singular_name' => 'Номер',
				'add_new_item'  => 'Добавить номер',
				'edit_item'     => 'Редактировать номер',
				'menu_name'     => 'Номера',
			),
			'public'       => true,
			'show_ui'      => true,
			'menu_icon'    => 'dashicons-building',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'room' ),
			'show_in_rest' => true,
		)
	);

	register_post_type(
		'troya_booking',
		array(
			'labels'              => array(
				'name'               => 'Заявки',
				'singular_name'      => 'Заявка',
				'menu_name'          => 'Заявки',
				'all_items'          => 'Все заявки',
				'view_item'          => 'Просмотр заявки',
				'search_items'       => 'Искать заявки',
				'not_found'          => 'Заявок не найдено',
				'not_found_in_trash' => 'В корзине заявок нет',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-email-alt',
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
		)
	);
}

/**
 * @param array<string, mixed> $fields Form fields.
 */
function troya_save_booking( array $fields ): int {
	$name  = $fields['name'] ?? 'Заявка';
	$room  = $fields['room'] ?? '';
	$title = $room ? sprintf( '%s — %s', $name, $room ) : (string) $name;

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'troya_booking',
			'post_status' => 'publish',
			'post_title'  => sanitize_text_field( $title ),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return 0;
	}

	update_post_meta( $post_id, '_form_fields', $fields );
	update_post_meta( $post_id, '_client_ip', troya_get_client_ip() );
	update_post_meta( $post_id, '_submitted_at', current_time( 'mysql' ) );

	return (int) $post_id;
}

/**
 * @param string[] $columns Columns.
 * @return string[]
 */
function troya_booking_columns( array $columns ): array {
	return array(
		'cb'      => $columns['cb'] ?? '',
		'title'   => 'Заявка',
		'phone'   => 'Телефон',
		'room'    => 'Номер',
		'dates'   => 'Даты',
		'date'    => $columns['date'] ?? 'Дата',
	);
}

function troya_booking_column_content( string $column, int $post_id ): void {
	$fields = get_post_meta( $post_id, '_form_fields', true );
	if ( ! is_array( $fields ) ) {
		$fields = array();
	}

	if ( 'phone' === $column ) {
		echo esc_html( (string) ( $fields['phone'] ?? '' ) );
	}

	if ( 'room' === $column ) {
		echo esc_html( (string) ( $fields['room'] ?? '—' ) );
	}

	if ( 'dates' === $column ) {
		$in  = $fields['checkin'] ?? '';
		$out = $fields['checkout'] ?? '';
		echo esc_html( trim( $in . ' — ' . $out, ' —' ) );
	}
}

function troya_booking_meta_boxes(): void {
	add_meta_box(
		'troya_booking_details',
		'Данные заявки',
		'troya_booking_meta_box_render',
		'troya_booking',
		'normal',
		'high'
	);
}

function troya_booking_meta_box_render( WP_Post $post ): void {
	$fields = get_post_meta( $post->ID, '_form_fields', true );
	$ip     = (string) get_post_meta( $post->ID, '_client_ip', true );
	$at     = (string) get_post_meta( $post->ID, '_submitted_at', true );

	$labels = array(
		'name'     => 'Имя',
		'phone'    => 'Телефон',
		'email'    => 'Email',
		'checkin'  => 'Заезд',
		'checkout' => 'Выезд',
		'guests'   => 'Гостей',
		'room'     => 'Номер',
		'comment'  => 'Комментарий',
	);

	echo '<table class="widefat striped"><tbody>';

	if ( is_array( $fields ) ) {
		foreach ( $labels as $key => $label ) {
			if ( empty( $fields[ $key ] ) ) {
				continue;
			}
			printf(
				'<tr><th style="width:180px">%s</th><td>%s</td></tr>',
				esc_html( $label ),
				esc_html( (string) $fields[ $key ] )
			);
		}
	}

	printf( '<tr><th>IP</th><td>%s</td></tr>', esc_html( $ip ) );
	printf( '<tr><th>Отправлено</th><td>%s</td></tr>', esc_html( $at ) );
	echo '</tbody></table>';
}
