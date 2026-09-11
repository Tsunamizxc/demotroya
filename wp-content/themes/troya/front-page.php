<?php
/**
 * Front page.
 *
 * @package Troya
 */

get_header();

$rooms_url = troya_rooms_url();
$phone1    = troya_option( 'site_phone_1', '8 (843) 564-46-46' );
$phone1tel = preg_replace( '/\D+/', '', $phone1 );
$phone2    = troya_option( 'site_phone_2', '8 (904) 678-56-00' );
$phone2tel = preg_replace( '/\D+/', '', $phone2 );
$phone_acc = troya_option( 'site_phone_accounting', '8 (843) 564-83-19' );
$acc_tel   = preg_replace( '/\D+/', '', $phone_acc );
$acc_hours = troya_option( 'accounting_hours', 'с 9:00 до 15:00' );
$email     = troya_option( 'site_email', 'hoteltroya@mail.ru' );
$address   = troya_option( 'site_address', '420095, РФ, РТ, г.Казань, ул.Восстания 119' );
$hours     = troya_option( 'site_hours', 'Круглосуточно, 24/7' );
$legal     = troya_option( 'legal_name', 'ООО «ТРОЯ»' );
$inn       = troya_option( 'legal_inn', '1657131940' );
$eroksti   = troya_option( 'legal_eroksti', 'С162024017530' );
$map_url   = troya_option( 'contact_map_url', 'https://yandex.ru/maps/-/CThvQI9G' );
$map_embed = troya_option(
	'contact_map_embed',
	'https://yandex.ru/map-widget/v1/?ll=49.051859%2C55.831398&mode=search&ol=geo&ouri=ymapsbm1%3A%2F%2Fgeo%3Fdata%3DCgg1NjI3NTI5NxJ80KDQvtGB0YHQuNGPLCDQoNC10YHQv9GD0LHQu9C40LrQsCDQotCw0YLQsNGA0YHRgtCw0L0gKNCi0LDRgtCw0YDRgdGC0LDQvSksINCa0LDQt9Cw0L3RjCwg0YPQu9C40YbQsCDQktC-0YHRgdGC0LDQvdC40Y8sIDExOSIKDRs1REIVWlNfQg%2C%2C&z=17.13'
);

$hero_poster = troya_img_url( troya_option( 'hero_poster' ), troya_asset( 'photos/hero-building.jpg' ) );
$hero_video  = troya_option( 'hero_video' );
$hero_video_url = is_array( $hero_video ) && ! empty( $hero_video['url'] ) ? $hero_video['url'] : troya_asset( 'videos/hero-flythrough.mp4' );

$marquee = troya_option( 'marquee_items', array() );
if ( ! is_array( $marquee ) || ! $marquee ) {
	$marquee = array_map(
		static fn( $t ) => array( 'text' => $t ),
		array( 'Бесплатный Wi-Fi', 'Охраняемая парковка', 'Завтрак включён', 'Кондиционер во всех номерах', 'Организация экскурсий', 'Трансфер', 'Прачечная', 'Хранение багажа' )
	);
}

$stats = troya_option( 'about_stats', array() );
if ( ! is_array( $stats ) || ! $stats ) {
	$stats = array(
		array( 'num' => '5+', 'label' => 'Типов номеров' ),
		array( 'num' => '12', 'label' => 'Экскурсий' ),
		array( 'num' => '24/7', 'label' => 'Сервис' ),
	);
}
?>

<section class="hero hero--video" id="hero">
	<div class="hero__bg">
		<video class="hero__video" id="hero-video" autoplay muted playsinline preload="auto" poster="<?php echo esc_url( $hero_poster ); ?>" aria-hidden="true">
			<source src="<?php echo esc_url( $hero_video_url ); ?>" type="video/mp4" />
		</video>
		<img class="hero__img hero__img--fallback" src="<?php echo esc_url( $hero_poster ); ?>" alt="" hidden />
		<div class="hero__overlay"></div>
		<div class="hero__grain"></div>
	</div>

	<div class="container hero__content">
		<p class="hero__eyebrow anim-up" style="--d: 0.25s"><?php echo esc_html( troya_option( 'hero_eyebrow', 'Отель в Казани' ) ); ?></p>
		<h1 class="hero__title anim-up" style="--d: 0.4s"><?php echo wp_kses_post( troya_option( 'hero_title', 'Отель <em>Троя</em>' ) ); ?></h1>
		<p class="hero__text anim-up" style="--d: 0.55s"><?php echo esc_html( troya_option( 'hero_text', 'Комфортабельный отель в самом сердце Казани.' ) ); ?></p>
		<div class="hero__actions anim-up" style="--d: 0.7s">
			<a href="<?php echo esc_url( troya_booking_url() ); ?>" class="btn btn--gold"><?php echo esc_html( troya_option( 'hero_cta_primary', 'Забронировать номер' ) ); ?></a>
			<a href="<?php echo esc_url( $rooms_url ); ?>" class="btn btn--ghost"><?php echo esc_html( troya_option( 'hero_cta_secondary', 'Смотреть номера' ) ); ?></a>
		</div>
	</div>

	<div class="bnovo-widget anim-up" style="--d: 0.85s">
		<div class="bnovo-widget__inner" id="_bn_widget_">
			<a href="https://bnovo.ru/" id="_bnovo_link_" target="_blank" rel="noopener noreferrer">Bnovo</a>
		</div>
	</div>
	<script src="//widget.reservationsteps.ru/js/bnovo.js"></script>
	<script>
	(function () {
		if (typeof Bnovo_Widget === 'undefined') return;
		Bnovo_Widget.init(function () {
			Bnovo_Widget.open('_bn_widget_', {
				type: 'horizontal',
				uid: <?php echo wp_json_encode( troya_option( 'bnovo_uid', 'eb9cee17-77f8-4f25-9817-b3fc2080617b' ) ); ?>,
				lang: 'ru',
				width: '100%',
				background: '#e8c64e',
				bg_alpha: '80',
				padding: '20',
				border_radius: '1',
				font_type: 'arial',
				font_size: '16',
				title_color: '#3e2319',
				title_size: '18',
				inp_color: '#222222',
				inp_bordhover: '#3796e5',
				inp_bordcolor: '#cccccc',
				inp_alpha: '100',
				btn_background: '#5b3325',
				btn_background_over: '#754d40',
				btn_textcolor: '#ffffff',
				btn_textover: '#ffffff',
				btn_bordcolor: '#5b3325',
				btn_bordhover: '#754d40',
				text_concierge: 'Получи скидку через Bnovo Concierge',
				url: <?php echo wp_json_encode( home_url( '/booking/' ) ); ?>,
				adults_default: '2',
				dates_preset: 'on',
				dfrom_tomorrow: 'on',
				dto_nextday: 'on'
			});
		});
	})();
	</script>
</section>

<div class="marquee">
	<div class="marquee__track">
		<?php for ( $i = 0; $i < 2; $i++ ) : ?>
			<?php foreach ( $marquee as $item ) : ?>
				<span><?php echo esc_html( $item['text'] ?? '' ); ?></span><i>◆</i>
			<?php endforeach; ?>
		<?php endfor; ?>
	</div>
</div>

<section class="about section">
	<div class="container about__grid">
		<div class="reveal-left">
			<p class="eyebrow"><?php echo esc_html( troya_option( 'about_eyebrow', 'Об отеле' ) ); ?></p>
			<h2 class="heading gold-line"><?php echo wp_kses_post( troya_option( 'about_title', 'Место, где<em> история</em><br />встречает комфорт' ) ); ?></h2>
			<p class="text"><?php echo esc_html( troya_option( 'about_text_1' ) ); ?></p>
			<p class="text"><?php echo esc_html( troya_option( 'about_text_2' ) ); ?></p>
			<div class="about__stats">
				<?php foreach ( $stats as $stat ) : ?>
					<div>
						<p class="about__stat-num"><?php echo esc_html( $stat['num'] ?? '' ); ?></p>
						<p class="about__stat-label"><?php echo esc_html( $stat['label'] ?? '' ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="about__media reveal-right">
			<div class="about__img-wrap">
				<img src="<?php echo esc_url( troya_img_url( troya_option( 'about_image' ), troya_asset( 'photos/hero-building.jpg' ) ) ); ?>" alt="Гостиница Троя — фасад здания" loading="lazy" decoding="async" />
			</div>
			<div class="about__card">
				<p class="about__card-label"><?php echo esc_html( troya_option( 'about_card_label', 'Адрес' ) ); ?></p>
				<p class="about__card-title"><?php echo esc_html( troya_option( 'about_card_title', 'ул. Восстания, 119' ) ); ?></p>
				<p class="about__card-sub"><?php echo esc_html( troya_option( 'about_card_sub', 'Казань, Татарстан' ) ); ?></p>
			</div>
		</div>
	</div>
</section>

<section id="rooms" class="rooms-story" data-rooms-slider>
	<div class="rooms-story__frame">
		<div class="rooms-story__media" id="rooms-videos" aria-hidden="true"></div>
		<div class="rooms-story__veil"></div>

		<div class="rooms-story__ui">
			<div class="rooms-story__header">
				<div>
					<p class="eyebrow"><?php echo esc_html( troya_option( 'rooms_eyebrow', 'Номерной фонд' ) ); ?></p>
					<h2 class="heading heading--light"><?php echo esc_html( troya_option( 'rooms_title', 'Наши номера' ) ); ?></h2>
				</div>
				<div class="rooms-story__meta">
					<div class="rooms-story__controls">
						<button type="button" class="slider-nav slider-nav--ghost" id="rooms-prev" aria-label="Предыдущий номер">
							<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 5L8 12l7 7" /></svg>
						</button>
						<span class="rooms__counter rooms__counter--light">
							<b id="rooms-current">01</b><i>/</i><span id="rooms-total">05</span>
						</span>
						<button type="button" class="slider-nav slider-nav--ghost" id="rooms-next" aria-label="Следующий номер">
							<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" /></svg>
						</button>
					</div>
					<a class="rooms__all rooms__all--light" href="<?php echo esc_url( $rooms_url ); ?>">Все номера →</a>
				</div>
			</div>

			<article class="rooms-story__card is-visible" id="rooms-card" aria-live="polite">
				<figure class="rooms-story__card-media">
					<img id="room-card-img" src="" alt="" />
				</figure>
				<div class="rooms-story__card-body">
					<span class="rooms__tag" id="room-tag"></span>
					<h3 class="rooms__name" id="room-name"></h3>
					<p class="rooms__subtitle" id="room-subtitle"></p>
					<p class="rooms__price" id="room-price"></p>
					<p class="rooms__desc text" id="room-desc"></p>
					<div class="rooms__features" id="room-features"></div>
					<p class="rooms__note"><?php echo esc_html( troya_option( 'rooms_note', 'Завтрак — 300 ₽ · Доп. кровать — 1 200 ₽' ) ); ?></p>
					<div class="rooms__footer-actions">
						<a class="btn btn--outline" id="room-more" href="<?php echo esc_url( $rooms_url ); ?>">Подробнее</a>
						<a class="btn btn--gold" id="room-book" href="<?php echo esc_url( troya_booking_url() ); ?>">Забронировать</a>
					</div>
				</div>
			</article>

			<div class="rooms-story__footer">
				<div class="rooms-story__progress" id="rooms-progress" role="tablist" aria-label="Номера"></div>
				<!-- <p class="rooms-story__hint">Стрелки переключают номера</p> -->
			</div>
		</div>
	</div>
</section>

<section id="amenities" class="amenities section">
	<div class="container">
		<div class="amenities__intro reveal">
			<div>
				<p class="eyebrow"><?php echo esc_html( troya_option( 'amenities_eyebrow', 'Сервис' ) ); ?></p>
				<h2 class="heading gold-line"><?php echo wp_kses_post( troya_option( 'amenities_title', "Всё для вашего<br /><em>комфорта</em>" ) ); ?></h2>
			</div>
			<p class="amenities__aside text"><?php echo esc_html( troya_option( 'amenities_aside' ) ); ?></p>
		</div>
		<div class="amenities__mosaic" id="amenities-grid"></div>
	</div>
</section>

<section id="excursions" class="excursions section section--sand">
	<div class="container">
		<div class="excursions__header reveal">
			<div>
				<p class="eyebrow"><?php echo esc_html( troya_option( 'excursions_eyebrow', 'Экскурсии' ) ); ?></p>
				<h2 class="heading gold-line"><?php echo wp_kses_post( troya_option( 'excursions_title', "Откройте Татарстан<br /><em>вместе с нами</em>" ) ); ?></h2>
			</div>
			<p class="text excursions__lead"><?php echo esc_html( troya_option( 'excursions_lead' ) ); ?></p>
		</div>

		<div class="excursions__photo reveal delay-200">
			<img src="<?php echo esc_url( troya_img_url( troya_option( 'excursions_image' ), troya_asset( 'photos/excursions.jpg' ) ) ); ?>" alt="Экскурсии" loading="lazy" decoding="async" />
			<div class="excursions__photo-fade"></div>
		</div>

		<div class="excursions__grid" id="excursions-grid"></div>

		<div class="excursions__cta reveal">
			<a href="tel:<?php echo esc_attr( $phone1tel ); ?>" class="btn btn--outline"><?php echo esc_html( troya_option( 'excursions_cta', 'Узнать подробности и цены' ) ); ?></a>
		</div>
	</div>
</section>

<section id="reviews" class="reviews section">
	<div class="container reviews__layout">
		<div class="reviews__intro reveal">
			<p class="eyebrow"><?php echo esc_html( troya_option( 'reviews_eyebrow', 'Отзывы гостей' ) ); ?></p>
			<h2 class="heading gold-line"><?php echo wp_kses_post( troya_option( 'reviews_title', "Голоса тех,<br /><em>кто уже был</em>" ) ); ?></h2>
			<div class="reviews__controls">
				<button type="button" class="slider-nav" id="reviews-prev" aria-label="Предыдущий отзыв">
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 5L8 12l7 7" /></svg>
				</button>
				<button type="button" class="slider-nav" id="reviews-next" aria-label="Следующий отзыв">
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" /></svg>
				</button>
			</div>
		</div>
		<div class="reviews-deck reveal delay-200" id="reviews-deck" aria-live="polite"></div>
	</div>
</section>

<section id="contact" class="contact section section--mist">
	<div class="container contact__grid">
		<div class="reveal-left">
			<p class="eyebrow"><?php echo esc_html( troya_option( 'contact_eyebrow', 'Контакты' ) ); ?></p>
			<h2 class="heading gold-line"><?php echo wp_kses_post( troya_option( 'contact_title', "Свяжитесь<br /><em>с нами</em>" ) ); ?></h2>

			<div class="contact__list">
				<div class="contact__item">
					<p class="contact__label">Организация</p>
					<p class="text"><?php echo esc_html( $legal ); ?></p>
					<p class="text">ИНН <?php echo esc_html( $inn ); ?></p>
					<p class="text">Номер в ЕРОКСТИ: <?php echo esc_html( $eroksti ); ?></p>
				</div>
				<div class="contact__item">
					<p class="contact__label">Тел. бронирования</p>
					<a href="tel:<?php echo esc_attr( $phone1tel ); ?>" class="contact__phone"><?php echo esc_html( $phone1 ); ?></a>
					<a href="tel:<?php echo esc_attr( $phone2tel ); ?>" class="contact__phone"><?php echo esc_html( $phone2 ); ?></a>
				</div>
				<div class="contact__item">
					<p class="contact__label">Бухгалтерия (<?php echo esc_html( $acc_hours ); ?>)</p>
					<a href="tel:<?php echo esc_attr( $acc_tel ); ?>" class="contact__phone"><?php echo esc_html( $phone_acc ); ?></a>
				</div>
				<div class="contact__item">
					<p class="contact__label">Email</p>
					<a href="mailto:<?php echo esc_attr( $email ); ?>" class="contact__email"><?php echo esc_html( $email ); ?></a>
				</div>
				<div class="contact__item">
					<p class="contact__label">Адрес</p>
					<?php foreach ( preg_split( "/\r\n|\n|\r/", (string) $address ) as $line ) : ?>
						<?php if ( trim( $line ) ) : ?>
							<p class="text">
								<a class="contact__map-link" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $line ); ?>
								</a>
							</p>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="contact__aside reveal-right">
			<div class="contact__map">
				<div class="contact__map-pin">
					<iframe
						src="<?php echo esc_url( $map_embed ); ?>"
						title="Отель Троя на Яндекс Картах — ул. Восстания, 119"
						width="560"
						height="400"
						frameborder="0"
						allowfullscreen="true"
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
					></iframe>
				</div>
			</div>
			<a href="tel:<?php echo esc_attr( $phone1tel ); ?>" class="btn btn--gold btn--block"><?php echo esc_html( troya_option( 'contact_cta', 'Позвонить и забронировать' ) ); ?></a>
			<div class="contact__hours">
				<p class="contact__label">Режим работы администрации</p>
				<p class="contact__hours-text"><?php echo esc_html( $hours ); ?></p>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
