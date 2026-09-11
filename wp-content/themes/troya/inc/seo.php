<?php
/**
 * Favicon + SEO titles / meta.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_head', 'troya_output_favicon', 1 );
add_action( 'wp_head', 'troya_output_seo_meta', 2 );
add_filter( 'document_title_parts', 'troya_document_title_parts' );
add_filter( 'document_title_separator', static fn() => '—' );

function troya_favicon_uri( string $file ): string {
	return troya_asset( 'favic/' . ltrim( $file, '/' ) );
}

function troya_output_favicon(): void {
	$v = TROYA_VERSION;
	printf( '<link rel="icon" href="%s?v=%s" sizes="any">' . "\n", esc_url( troya_favicon_uri( 'favicon.ico' ) ), esc_attr( $v ) );
	printf( '<link rel="icon" href="%s?v=%s" type="image/svg+xml">' . "\n", esc_url( troya_favicon_uri( 'favicon.svg' ) ), esc_attr( $v ) );
	printf( '<link rel="icon" type="image/png" href="%s?v=%s" sizes="96x96">' . "\n", esc_url( troya_favicon_uri( 'favicon-96x96.png' ) ), esc_attr( $v ) );
	printf( '<link rel="apple-touch-icon" href="%s?v=%s" sizes="180x180">' . "\n", esc_url( troya_favicon_uri( 'apple-touch-icon.png' ) ), esc_attr( $v ) );
	printf( '<link rel="manifest" href="%s?v=%s">' . "\n", esc_url( troya_favicon_uri( 'site.webmanifest' ) ), esc_attr( $v ) );
	echo '<meta name="theme-color" content="#a67c52">' . "\n";
}

/**
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function troya_document_title_parts( array $parts ): array {
	$site = troya_option( 'seo_site_name', get_bloginfo( 'name' ) ?: 'Отель Троя' );

	if ( is_front_page() ) {
		$parts['title'] = troya_option(
			'seo_home_title',
			'Отель Троя в Казани — номера, завтраки и сервис 24/7'
		);
		unset( $parts['tagline'], $parts['site'] );
		return $parts;
	}

	if ( is_page( 'booking' ) || is_page_template( 'page-booking.php' ) ) {
		$parts['title'] = troya_option( 'seo_booking_title', 'Онлайн-бронирование — Отель Троя, Казань' );
		unset( $parts['tagline'], $parts['site'] );
		return $parts;
	}

	if ( is_page( 'galereya' ) || is_page_template( 'page-gallery.php' ) ) {
		$parts['title'] = 'Фотогалерея — Отель Троя, Казань';
		unset( $parts['tagline'], $parts['site'] );
		return $parts;
	}

	if ( is_page( 'nomera' ) || is_page_template( 'page-nomera.php' ) ) {
		$parts['title'] = troya_option( 'seo_rooms_title', 'Номера — Отель Троя, Казань' );
		unset( $parts['tagline'], $parts['site'] );
		return $parts;
	}

	if ( is_singular( 'troya_room' ) ) {
		$parts['title'] = sprintf(
			'%s — номер в отеле Троя, Казань',
			get_the_title()
		);
		unset( $parts['tagline'], $parts['site'] );
		return $parts;
	}

	if ( ! empty( $parts['title'] ) ) {
		$parts['site'] = $site;
	}

	return $parts;
}

function troya_get_seo_description(): string {
	if ( is_front_page() ) {
		return (string) troya_option(
			'seo_home_description',
			'Отель «Троя» в Казани на ул. Восстания, 119. Современные номера, завтраки, парковка, трансфер и экскурсии. Бронирование: 8 (843) 564-46-46.'
		);
	}

	if ( is_page( 'booking' ) || is_page_template( 'page-booking.php' ) ) {
		return (string) troya_option(
			'seo_booking_description',
			'Забронируйте номер в отеле «Троя» в Казани онлайн. Прямое бронирование без комиссии, ул. Восстания, 119.'
		);
	}

	if ( is_page( 'galereya' ) || is_page_template( 'page-gallery.php' ) ) {
		return 'Фотогалерея отеля «Троя» в Казани: номера, ресепшен, столовая и виды здания.';
	}

	if ( is_page( 'nomera' ) || is_page_template( 'page-nomera.php' ) ) {
		return (string) troya_option(
			'seo_rooms_description',
			'Каталог номеров отеля «Троя» в Казани: стандарт, бизнес-комфорт и семейные категории. Цены от 4 000 ₽.'
		);
	}

	if ( is_singular( 'troya_room' ) ) {
		$desc = (string) troya_field( 'room_desc', '' );
		if ( $desc ) {
			return wp_strip_all_tags( $desc );
		}
		return sprintf(
			'%s — номер в отеле «Троя», Казань, ул. Восстания, 119. Забронировать онлайн.',
			get_the_title()
		);
	}

	if ( is_singular() ) {
		$excerpt = get_the_excerpt();
		if ( $excerpt ) {
			return wp_strip_all_tags( $excerpt );
		}
	}

	return (string) troya_option(
		'seo_home_description',
		'Отель «Троя» в Казани. Комфортные номера и сервис 24/7.'
	);
}

function troya_output_seo_meta(): void {
	$desc  = troya_get_seo_description();
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
	if ( is_front_page() ) {
		$url = home_url( '/' );
	}
	$image = troya_img_url( troya_option( 'hero_poster' ), troya_asset( 'photos/hero.jpg' ) );
	$site  = troya_option( 'seo_site_name', 'Отель Троя' );

	if ( is_singular( 'troya_room' ) ) {
		$image = troya_img_url(
			troya_field( 'room_image' ),
			get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: $image
		);
	}

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta name="author" content="%s">' . "\n", esc_attr( troya_option( 'legal_name', 'ООО «ТРОЯ»' ) ) );
	echo '<meta name="robots" content="index, follow">' . "\n";

	printf( '<meta property="og:type" content="%s">' . "\n", is_front_page() ? 'website' : 'article' );
	printf( '<meta property="og:locale" content="ru_RU">' . "\n" );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );

	printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );

	// Organization / Hotel JSON-LD on homepage.
	if ( is_front_page() ) {
		$phone1 = troya_option( 'site_phone_1', '8 (843) 564-46-46' );
		$data   = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Hotel',
			'name'        => $site,
			'legalName'   => troya_option( 'legal_name', 'ООО «ТРОЯ»' ),
			'taxID'       => troya_option( 'legal_inn', '1657131940' ),
			'url'         => home_url( '/' ),
			'telephone'   => $phone1,
			'email'       => troya_option( 'site_email', 'hoteltroya@mail.ru' ),
			'image'       => $image,
			'description' => $desc,
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => 'ул. Восстания, 119',
				'addressLocality' => 'Казань',
				'addressRegion'   => 'Республика Татарстан',
				'postalCode'      => '420095',
				'addressCountry'  => 'RU',
			),
		);

		$eroksti = troya_option( 'legal_eroksti', '' );
		if ( $eroksti ) {
			$data['identifier'] = array(
				'@type' => 'PropertyValue',
				'name'  => 'ЕРОКСТИ',
				'value' => $eroksti,
			);
		}

		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
		);
	}
}
