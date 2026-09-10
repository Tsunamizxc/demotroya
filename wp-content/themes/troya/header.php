<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="root">
	<div class="page-glow" aria-hidden="true"></div>

	<?php
	$logo      = troya_img_url( troya_option( 'site_logo' ), troya_asset( 'logo.png' ) );
	$cta       = troya_option( 'header_cta', 'Забронировать' );
	$menu_img  = troya_img_url( troya_option( 'menu_image' ), troya_asset( 'photos/menu.jpg' ) );
	$addr      = troya_option( 'site_address_short', 'Казань · ул. Восстания, 119' );
	$rooms_url = troya_rooms_url();
	?>

	<header class="header" id="header">
		<div class="container header__inner">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__logo">
				<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="header__logo-img" />
			</a>

			<nav class="header__nav header__nav--desktop">
				<a href="<?php echo esc_url( $rooms_url ); ?>" class="nav-link">Номера</a>
				<a href="<?php echo esc_url( home_url( '/#amenities' ) ); ?>" class="nav-link">Удобства</a>
				<a href="<?php echo esc_url( home_url( '/#excursions' ) ); ?>" class="nav-link">Экскурсии</a>
				<a href="<?php echo esc_url( home_url( '/#reviews' ) ); ?>" class="nav-link">Отзывы</a>
				<a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>" class="nav-link">Контакты</a>
				<button type="button" class="btn btn--gold btn--sm" data-open-modal><?php echo esc_html( $cta ); ?></button>
			</nav>

			<button type="button" class="burger" id="burger" aria-label="Меню" aria-expanded="false">
				<span class="burger__ring"></span>
				<span class="burger__lines"><i></i><i></i><i></i></span>
				<span class="burger__label" data-open="Меню" data-close="Закрыть">Меню</span>
			</button>
		</div>
	</header>

	<div class="menu-overlay" id="menu-overlay" aria-hidden="true">
		<div class="menu-overlay__wash"></div>
		<div class="menu-overlay__photo">
			<img src="<?php echo esc_url( $menu_img ); ?>" alt="" />
		</div>
		<nav class="menu-overlay__nav" id="nav">
			<button type="button" class="menu-overlay__close" id="menu-close" aria-label="Закрыть меню">
				<span></span><span></span>
			</button>
			<a href="<?php echo esc_url( $rooms_url ); ?>" class="menu-overlay__link" data-i="01"><span>Номера</span></a>
			<a href="<?php echo esc_url( home_url( '/#amenities' ) ); ?>" class="menu-overlay__link" data-i="02"><span>Удобства</span></a>
			<a href="<?php echo esc_url( home_url( '/#excursions' ) ); ?>" class="menu-overlay__link" data-i="03"><span>Экскурсии</span></a>
			<a href="<?php echo esc_url( home_url( '/#reviews' ) ); ?>" class="menu-overlay__link" data-i="04"><span>Отзывы</span></a>
			<a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>" class="menu-overlay__link" data-i="05"><span>Контакты</span></a>
			<button type="button" class="btn btn--gold menu-overlay__cta" data-open-modal><?php echo esc_html( $cta ); ?></button>
		</nav>
		<p class="menu-overlay__meta"><?php echo esc_html( $addr ); ?></p>
	</div>
