<?php
/**
 * Seed default ACF options and rooms once.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'troya_seed_content' );
// Initial seed is also triggered from migrations when needed.

function troya_seed_content(): void {
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$rooms_page = get_page_by_path( 'nomera' );
	if ( $rooms_page ) {
		update_post_meta( $rooms_page->ID, '_wp_page_template', 'page-nomera.php' );
		update_field( 'rooms_page_id', $rooms_page->ID, 'option' );
	}

	$defaults = array(
		'site_phone_1'            => '8 (843) 564-46-46',
		'site_phone_2'            => '8 (904) 678-56-00',
		'site_phone_accounting'   => '8 (843) 564-83-19',
		'accounting_hours'        => 'с 9:00 до 15:00',
		'legal_name'              => 'ООО «ТРОЯ»',
		'legal_inn'               => '1657131940',
		'legal_eroksti'           => 'С162024017530',
		'site_email'              => 'hoteltroya@mail.ru',
		'site_address'            => '420095, РФ, РТ, г.Казань, ул.Восстания 119',
		'site_address_short'      => 'Казань · ул. Восстания, 119',
		'site_hours'              => 'Круглосуточно, 24/7',
		'seo_site_name'           => 'Отель Троя',
		'seo_home_title'          => 'Отель Троя в Казани — номера, завтраки и сервис 24/7',
		'seo_home_description'    => 'Отель «Троя» в Казани на ул. Восстания, 119. ООО «ТРОЯ», ИНН 1657131940. Современные номера, завтраки, парковка и сервис 24/7. Бронирование: 8 (843) 564-46-46.',
		'seo_rooms_title'         => 'Номера — Отель Троя, Казань',
		'seo_rooms_description'   => 'Каталог номеров отеля «Троя» в Казани: Standard+, Business Comfort, семейные и премиум-категории.',
		'smtp_host'               => 'smtp.gmail.com',
		'smtp_port'               => 465,
		'smtp_encryption'         => 'ssl',
		'smtp_from_name'          => 'Отель Троя',
		'smtp_to_email'           => 'hoteltroya@mail.ru',
		'header_cta'              => 'Забронировать',
		'hero_eyebrow'            => 'Отель в Казани',
		'hero_title'              => 'Отель <em>Троя</em>',
		'hero_text'               => 'Комфортабельный отель в самом сердце Казани. Современные номера, завтраки, парковка и организация экскурсий.',
		'hero_cta_primary'        => 'Забронировать номер',
		'hero_cta_secondary'      => 'Смотреть номера',
		'about_eyebrow'           => 'Об отеле',
		'about_title'             => "Место, где<em> история</em><br />встречает комфорт",
		'about_text_1'            => 'Отель «Троя» расположен на улице Восстания в Казани — в нескольких минутах от главных достопримечательностей татарской столицы. Мы предлагаем уютные современные номера, внимательный персонал и всё необходимое для комфортного отдыха или деловой поездки.',
		'about_text_2'            => 'Казань — уникальный город, где слились русская и татарская культуры. Мы поможем вам открыть его через авторские экскурсии, трансферы и индивидуальные маршруты.',
		'about_card_label'        => 'Адрес',
		'about_card_title'        => 'ул. Восстания, 119',
		'about_card_sub'          => 'Казань, Татарстан',
		'rooms_eyebrow'           => 'Номерной фонд',
		'rooms_title'             => 'Наши номера',
		'rooms_note'              => 'Завтрак — 300 ₽ · Доп. кровать — 1 200 ₽',
		'amenities_eyebrow'       => 'Сервис',
		'amenities_title'         => "Всё для вашего<br /><em>комфорта</em>",
		'amenities_aside'         => 'Не список галочек — а атмосфера заботы, которую вы чувствуете с первой минуты.',
		'excursions_eyebrow'      => 'Экскурсии',
		'excursions_title'        => "Откройте Татарстан<br /><em>вместе с нами</em>",
		'excursions_lead'         => 'Мы организуем авторские экскурсии по Казани и всему Татарстану — от ночных прогулок по городу до речных путешествий на остров Свияжск.',
		'excursions_cta'          => 'Узнать подробности и цены',
		'reviews_eyebrow'         => 'Отзывы гостей',
		'reviews_title'           => "Голоса тех,<br /><em>кто уже был</em>",
		'contact_eyebrow'         => 'Контакты',
		'contact_title'           => "Свяжитесь<br /><em>с нами</em>",
		'contact_cta'             => 'Позвонить и забронировать',
		'footer_brand'            => 'Отель <span>Троя</span>',
		'footer_meta'             => 'ООО «ТРОЯ» · ИНН 1657131940 · ЕРОКСТИ С162024017530 · 420095, РФ, РТ, г.Казань, ул.Восстания 119',
		'footer_copy'             => '© 2026 Все права защищены',
		'modal_title'             => 'Бронирование по телефону',
		'modal_success_title'     => 'Заявка отправлена',
		'modal_success_text'      => 'Мы свяжемся с вами в ближайшее время для подтверждения бронирования.',
		'modal_hint'              => '* ОБЯЗАТЕЛЬНЫЕ ПОЛЯ · ЗАВТРАК +300 ₽ · ДОП. КРОВАТЬ +1 200 ₽',
	);

	foreach ( $defaults as $key => $value ) {
		update_field( $key, $value, 'option' );
	}

	update_field(
		'about_stats',
		array(
			array( 'num' => '5+', 'label' => 'Типов номеров' ),
			array( 'num' => '12', 'label' => 'Экскурсий' ),
			array( 'num' => '24/7', 'label' => 'Сервис' ),
		),
		'option'
	);

	update_field(
		'marquee_items',
		array_map(
			static fn( $t ) => array( 'text' => $t ),
			array( 'Бесплатный Wi-Fi', 'Охраняемая парковка', 'Завтрак включён', 'Кондиционер во всех номерах', 'Организация экскурсий', 'Трансфер', 'Прачечная', 'Хранение багажа' )
		),
		'option'
	);

	$amenity_defs = array(
		array( 'parking.png', 'Охраняемая парковка', 'Большая видеонаблюдаемая парковка прямо у входа.' ),
		array( 'breakfast.png', 'Континентальный завтрак', 'Свежий завтрак каждое утро за 300 ₽ с персоны.' ),
		array( 'taxi.png', 'Такси', 'Заказ такси для гостей отеля в любое время суток.' ),
		array( 'transfer.png', 'Трансфер', 'Организация трансфера из аэропорта и по городу.' ),
		array( 'laundry.png', 'Прачечная', 'Услуги стирки и глажки для постояльцев отеля.' ),
		array( 'contact.png', 'Связь 24/7', 'Круглосуточная связь с администрацией по телефону и email.' ),
		array( 'excursions.png', 'Экскурсии', 'Организация авторских экскурсий по Казани и Татарстану.' ),
	);

	$amenities = array();
	foreach ( $amenity_defs as $a ) {
		$amenities[] = array(
			'icon'  => troya_asset( 'amenities/' . $a[0] ),
			'title' => $a[1],
			'desc'  => $a[2],
		);
	}
	// Store as URL strings in icon field via temporary approach - ACF image expects ID.
	// For seed we save title/desc and use URL fallback in enqueue if icon empty.
	$amen_rows = array();
	foreach ( $amenity_defs as $a ) {
		$amen_rows[] = array(
			'title' => $a[1],
			'desc'  => $a[2],
		);
	}
	update_field( 'amenities_items', $amen_rows, 'option' );

	// Also store icon URLs in option for JS fallback.
	update_option(
		'troya_amenity_icons',
		array_map(
			static fn( $a ) => troya_asset( 'amenities/' . $a[0] ),
			$amenity_defs
		)
	);

	update_field(
		'reviews_items',
		array(
			array(
				'name' => 'Ольга',
				'text' => 'Очень понравился отель! Персонал встречает с улыбкой, атмосфера уюта и тишины ощущается по всей территории. Чувствуешь себя как дома, но лучше.',
			),
			array(
				'name' => 'Анастасия',
				'text' => 'Внимательный сервис, уютные номера, чистое постельное бельё и кондиционер в каждом номере. Обязательно вернёмся снова! Рекомендуем всем.',
			),
			array(
				'name' => 'Дмитрий',
				'text' => 'Удобное расположение, тихие номера и быстрое бронирование. Организовали трансфер без лишних вопросов — всё чётко и по-деловому.',
			),
			array(
				'name' => 'Мария',
				'text' => 'Брали экскурсию в Свияжск через отель — маршрут отличный, гид живой, а после прогулки приятно вернуться в тёплый номер.',
			),
		),
		'option'
	);

	$excursions = array(
		array( 'Спортивная Казань', 'Казань Арена, дворец водных видов спорта, конный комплекс, остров Свияжск, прогулка по Волге.' ),
		array( 'Храм всех религий', 'Раифский монастырь, Свияжск, Макарьевская пустынь — речное возвращение.' ),
		array( 'Чистополь', 'Купеческая история, музей Пастернака, интерактивная программа.' ),
		array( 'Тетюши', 'Мультикультурная история: русские, татары, чуваши, мордва.' ),
		array( 'Йошкар-Ола', 'Кремль, набережная Брюгге, комплекс 12 апостолов, площадь Оболенского-Ноготкова.' ),
		array( 'Купеческая Казань', 'Петропавловский собор, деревянные купеческие дома, музеи писателей.' ),
		array( 'Елабуга', 'Музей Шишкина, мемориал Цветаевой, усадьба Дурова, Чёртово Городище.' ),
		array( 'Болгар', 'Памятник XIII–XIV вв.: соборная мечеть, ханская усыпальница, Чёрная и Белая палаты.' ),
		array( 'Ночная Казань', 'Подсвеченные достопримечательности, легенды озера Кабан.' ),
		array( 'Обзорная Казань', 'Мечеть Кул-Шариф, башня Сююмбике, Благовещенский собор, панорамные виды.' ),
	);

	update_field(
		'excursions_items',
		array_map(
			static fn( $e ) => array(
				'title' => $e[0],
				'desc'  => $e[1],
			),
			$excursions
		),
		'option'
	);

	troya_seed_rooms();

	update_option( 'troya_seeded_v1', 1 );
	flush_rewrite_rules( false );
}

function troya_seed_rooms(): void {
	$existing = get_posts(
		array(
			'post_type'      => 'troya_room',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( $existing ) {
		return;
	}

	$rooms = array(
		array(
			'title'    => 'Standard+',
			'tag'      => 'Стандарт',
			'subtitle' => 'Двухместный',
			'price'    => 'от 4 000 ₽',
			'desc'     => 'Уютный номер с двумя кроватями, всеми удобствами и современной ванной комнатой.',
			'features' => array( 'Две кровати', 'Телевизор', 'Кондиционер', 'Мини-бар', 'Холодильник' ),
			'img'      => 'photos/room-1.jpg',
			'order'    => 1,
		),
		array(
			'title'    => 'Standard+ Family',
			'tag'      => 'Семейный',
			'subtitle' => 'Трёхместный',
			'price'    => 'от 5 000 ₽',
			'desc'     => 'Просторный номер для семьи или компании с тремя раздельными кроватями и плазменным ТВ.',
			'features' => array( 'Три кровати', 'Плазменный ТВ', 'Кондиционер', 'Мини-бар', 'Холодильник' ),
			'img'      => 'photos/room-2.jpg',
			'order'    => 2,
		),
		array(
			'title'    => 'Business Comfort',
			'tag'      => 'Бизнес',
			'subtitle' => 'Улучшенный',
			'price'    => 'от 5 700 ₽',
			'desc'     => 'Двуспальная кровать, полный набор удобств. Идеально для деловых поездок.',
			'features' => array( 'Двуспальная кровать', 'Телевизор', 'Кондиционер', 'Телефон', 'Мини-бар' ),
			'img'      => 'photos/room-3.jpg',
			'order'    => 3,
		),
		array(
			'title'    => 'Business Family',
			'tag'      => 'Люкс',
			'subtitle' => 'Дизайнерский',
			'price'    => 'от 6 400 ₽',
			'desc'     => 'Уникальный дизайн, халаты, тапочки, большой плазменный ТВ и чайный сервиз.',
			'features' => array( 'Дизайнерский интерьер', 'Халат и тапочки', 'Большой ТВ', 'Чайный сервиз', 'Мини-кухня' ),
			'img'      => 'photos/room-4.jpg',
			'order'    => 4,
		),
		array(
			'title'    => 'Business Comfort+',
			'tag'      => 'Премиум',
			'subtitle' => 'Премиум',
			'price'    => 'от 6 700 ₽',
			'desc'     => 'Большая двуспальная кровать, халаты, тапочки и полный комплект премиальных удобств.',
			'features' => array( 'Большая кровать', 'Халат и тапочки', 'Кондиционер', 'Полный мини-бар', 'Доп. кровать' ),
			'img'      => 'photos/room-5.jpg',
			'order'    => 5,
		),
	);

	foreach ( $rooms as $room ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'troya_room',
				'post_status'  => 'publish',
				'post_title'   => $room['title'],
				'post_content' => $room['desc'],
				'menu_order'   => $room['order'],
			)
		);

		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}

		update_field( 'room_tag', $room['tag'], $id );
		update_field( 'room_subtitle', $room['subtitle'], $id );
		update_field( 'room_price', $room['price'], $id );
		update_field( 'room_desc', $room['desc'], $id );
		$map = troya_bnovo_default_room_map();
		if ( ! empty( $map[ $room['title'] ] ) ) {
			update_field( 'room_bnovo_ids', $map[ $room['title'] ], $id );
		}
		update_field(
			'room_features',
			array_map(
				static fn( $t ) => array( 'text' => $t ),
				$room['features']
			),
			$id
		);

		// Store relative theme asset path for image URL fallback via post meta.
		update_post_meta( $id, '_troya_room_img_path', $room['img'] );
	}
}

// Prefer theme asset path meta when ACF image empty.
add_filter(
	'troya_room_image_fallback',
	static function ( $url, $post_id ) {
		$path = (string) get_post_meta( $post_id, '_troya_room_img_path', true );
		return $path ? troya_asset( $path ) : $url;
	},
	10,
	2
);
