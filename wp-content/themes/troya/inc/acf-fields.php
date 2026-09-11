<?php
/**
 * ACF options + field groups for all homepage blocks.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', 'troya_register_acf' );

function troya_register_acf(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => 'Настройки сайта',
				'menu_title' => 'Настройки сайта',
				'menu_slug'  => 'troya-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
				'icon_url'   => 'dashicons-admin-settings',
				'position'   => 59,
			)
		);
	}

	troya_acf_general_settings();
	troya_acf_smtp_settings();
	troya_acf_header_fields();
	troya_acf_hero_fields();
	troya_acf_marquee_fields();
	troya_acf_about_fields();
	troya_acf_rooms_section_fields();
	troya_acf_amenities_fields();
	troya_acf_excursions_fields();
	troya_acf_reviews_fields();
	troya_acf_contact_fields();
	troya_acf_footer_fields();
	troya_acf_modal_fields();
	troya_acf_room_fields();
}

function troya_acf_location_options(): array {
	return array(
		array(
			array(
				'param'    => 'options_page',
				'operator' => '==',
				'value'    => 'troya-settings',
			),
		),
	);
}

function troya_acf_general_settings(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_general',
			'title'  => 'Общие настройки',
			'fields' => array(
				array(
					'key'   => 'field_site_logo',
					'label' => 'Логотип',
					'name'  => 'site_logo',
					'type'  => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
				),
				array(
					'key'           => 'field_legal_name',
					'label'         => 'Юридическое название',
					'name'          => 'legal_name',
					'type'          => 'text',
					'default_value' => 'ООО «ТРОЯ»',
				),
				array(
					'key'           => 'field_legal_inn',
					'label'         => 'ИНН',
					'name'          => 'legal_inn',
					'type'          => 'text',
					'default_value' => '1657131940',
				),
				array(
					'key'           => 'field_legal_eroksti',
					'label'         => 'Номер в ЕРОКСТИ',
					'name'          => 'legal_eroksti',
					'type'          => 'text',
					'default_value' => 'С162024017530',
				),
				array(
					'key'           => 'field_site_phone_1',
					'label'         => 'Телефон бронирования 1',
					'name'          => 'site_phone_1',
					'type'          => 'text',
					'default_value' => '8 (843) 564-46-46',
				),
				array(
					'key'           => 'field_site_phone_2',
					'label'         => 'Телефон бронирования 2',
					'name'          => 'site_phone_2',
					'type'          => 'text',
					'default_value' => '8 (904) 678-56-00',
				),
				array(
					'key'           => 'field_site_phone_accounting',
					'label'         => 'Телефон бухгалтерии',
					'name'          => 'site_phone_accounting',
					'type'          => 'text',
					'default_value' => '8 (843) 564-83-19',
				),
				array(
					'key'           => 'field_accounting_hours',
					'label'         => 'Часы бухгалтерии',
					'name'          => 'accounting_hours',
					'type'          => 'text',
					'default_value' => 'с 9:00 до 15:00',
				),
				array(
					'key'           => 'field_site_email',
					'label'         => 'Email',
					'name'          => 'site_email',
					'type'          => 'email',
					'default_value' => 'hoteltroya@mail.ru',
				),
				array(
					'key'           => 'field_site_address',
					'label'         => 'Адрес',
					'name'          => 'site_address',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => '420095, РФ, РТ, г.Казань, ул.Восстания 119',
				),
				array(
					'key'           => 'field_site_address_short',
					'label'         => 'Короткий адрес',
					'name'          => 'site_address_short',
					'type'          => 'text',
					'default_value' => 'Казань · ул. Восстания, 119',
				),
				array(
					'key'           => 'field_site_hours',
					'label'         => 'Режим работы администрации',
					'name'          => 'site_hours',
					'type'          => 'text',
					'default_value' => 'Круглосуточно, 24/7',
				),
				array(
					'key'           => 'field_rooms_page_id',
					'label'         => 'Страница каталога номеров',
					'name'          => 'rooms_page_id',
					'type'          => 'post_object',
					'post_type'     => array( 'page' ),
					'allow_null'    => 1,
					'return_format' => 'id',
					'ui'            => 1,
				),
				array(
					'key'           => 'field_bnovo_uid',
					'label'         => 'Bnovo UID модуля',
					'name'          => 'bnovo_uid',
					'type'          => 'text',
					'default_value' => 'eb9cee17-77f8-4f25-9817-b3fc2080617b',
					'instructions'  => 'Меню Bnovo → Каналы продаж → UID модуля онлайн-бронирования',
				),
				array(
					'key'           => 'field_booking_page_id',
					'label'         => 'Страница бронирования',
					'name'          => 'booking_page_id',
					'type'          => 'post_object',
					'post_type'     => array( 'page' ),
					'allow_null'    => 1,
					'return_format' => 'id',
					'ui'            => 1,
				),
				array(
					'key'           => 'field_seo_site_name',
					'label'         => 'SEO: название сайта',
					'name'          => 'seo_site_name',
					'type'          => 'text',
					'default_value' => 'Отель Троя',
				),
				array(
					'key'           => 'field_seo_home_title',
					'label'         => 'SEO: title главной',
					'name'          => 'seo_home_title',
					'type'          => 'text',
					'default_value' => 'Отель Троя в Казани — номера, завтраки и сервис 24/7',
				),
				array(
					'key'           => 'field_seo_home_description',
					'label'         => 'SEO: description главной',
					'name'          => 'seo_home_description',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'Отель «Троя» в Казани на ул. Восстания, 119. Современные номера, завтраки, парковка, трансфер и экскурсии. Бронирование: 8 (843) 564-46-46.',
				),
				array(
					'key'           => 'field_seo_rooms_title',
					'label'         => 'SEO: title страницы номеров',
					'name'          => 'seo_rooms_title',
					'type'          => 'text',
					'default_value' => 'Номера — Отель Троя, Казань',
				),
				array(
					'key'           => 'field_seo_rooms_description',
					'label'         => 'SEO: description страницы номеров',
					'name'          => 'seo_rooms_description',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'Каталог номеров отеля «Троя» в Казани: Standard+, Business Comfort, семейные и премиум-категории. Цены от 4 000 ₽.',
				),
				array(
					'key'           => 'field_seo_booking_title',
					'label'         => 'SEO: title бронирования',
					'name'          => 'seo_booking_title',
					'type'          => 'text',
					'default_value' => 'Онлайн-бронирование — Отель Троя, Казань',
				),
				array(
					'key'           => 'field_seo_booking_description',
					'label'         => 'SEO: description бронирования',
					'name'          => 'seo_booking_description',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'Забронируйте номер в отеле «Троя» в Казани онлайн. Прямое бронирование без комиссии.',
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 0,
		)
	);
}

function troya_acf_smtp_settings(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_smtp',
			'title'  => 'Настройки SMTP',
			'fields' => array(
				array(
					'key'           => 'field_smtp_host',
					'label'         => 'SMTP сервер',
					'name'          => 'smtp_host',
					'type'          => 'text',
					'default_value' => 'smtp.gmail.com',
					'instructions'  => 'Заявки всегда сохраняются в разделе «Заявки». SMTP нужен для дублирования на email.',
				),
				array(
					'key'           => 'field_smtp_port',
					'label'         => 'Порт',
					'name'          => 'smtp_port',
					'type'          => 'number',
					'default_value' => 465,
				),
				array(
					'key'           => 'field_smtp_encryption',
					'label'         => 'Шифрование',
					'name'          => 'smtp_encryption',
					'type'          => 'select',
					'choices'       => array(
						'ssl'  => 'SSL',
						'tls'  => 'TLS',
						'none' => 'Без шифрования',
					),
					'default_value' => 'ssl',
				),
				array(
					'key'   => 'field_smtp_username',
					'label' => 'Логин SMTP',
					'name'  => 'smtp_username',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_smtp_password',
					'label' => 'Пароль SMTP',
					'name'  => 'smtp_password',
					'type'  => 'password',
				),
				array(
					'key'   => 'field_smtp_from_email',
					'label' => 'Email отправителя',
					'name'  => 'smtp_from_email',
					'type'  => 'email',
				),
				array(
					'key'           => 'field_smtp_from_name',
					'label'         => 'Имя отправителя',
					'name'          => 'smtp_from_name',
					'type'          => 'text',
					'default_value' => 'Отель Троя',
				),
				array(
					'key'          => 'field_smtp_to_email',
					'label'        => 'Email получателей заявок',
					'name'         => 'smtp_to_email',
					'type'         => 'textarea',
					'rows'         => 3,
					'instructions' => 'Один или несколько адресов через запятую.',
					'default_value'=> 'hoteltroya@mail.ru',
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 1,
		)
	);
}

function troya_acf_header_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_header',
			'title'  => 'Блок: Шапка',
			'fields' => array(
				array(
					'key'          => 'field_header_cta',
					'label'        => 'Текст кнопки',
					'name'         => 'header_cta',
					'type'         => 'text',
					'default_value'=> 'Забронировать',
				),
				array(
					'key'          => 'field_menu_image',
					'label'        => 'Фото мобильного меню',
					'name'         => 'menu_image',
					'type'         => 'image',
					'return_format'=> 'array',
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 10,
		)
	);
}

function troya_acf_hero_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_hero',
			'title'  => 'Блок: Hero',
			'fields' => array(
				array(
					'key'           => 'field_hero_eyebrow',
					'label'         => 'Надзаголовок',
					'name'          => 'hero_eyebrow',
					'type'          => 'text',
					'default_value' => 'Отель в Казани',
				),
				array(
					'key'           => 'field_hero_title',
					'label'         => 'Заголовок (HTML)',
					'name'          => 'hero_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Отель <em>Троя</em>',
					'instructions'  => 'Можно использовать тег &lt;em&gt; для акцента.',
				),
				array(
					'key'           => 'field_hero_text',
					'label'         => 'Текст',
					'name'          => 'hero_text',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'Комфортабельный отель в самом сердце Казани. Современные номера, завтраки, парковка и организация экскурсий.',
				),
				array(
					'key'           => 'field_hero_cta_primary',
					'label'         => 'Кнопка 1',
					'name'          => 'hero_cta_primary',
					'type'          => 'text',
					'default_value' => 'Забронировать номер',
				),
				array(
					'key'           => 'field_hero_cta_secondary',
					'label'         => 'Кнопка 2',
					'name'          => 'hero_cta_secondary',
					'type'          => 'text',
					'default_value' => 'Смотреть номера',
				),
				array(
					'key'           => 'field_hero_poster',
					'label'         => 'Poster / фото',
					'name'          => 'hero_poster',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'           => 'field_hero_video',
					'label'         => 'Видео (MP4 URL или файл)',
					'name'          => 'hero_video',
					'type'          => 'file',
					'return_format' => 'array',
					'mime_types'    => 'mp4',
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 20,
		)
	);
}

function troya_acf_marquee_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_marquee',
			'title'  => 'Блок: Бегущая строка',
			'fields' => array(
				array(
					'key'          => 'field_marquee_items',
					'label'        => 'Пункты',
					'name'         => 'marquee_items',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Добавить',
					'sub_fields'   => array(
						array(
							'key'   => 'field_marquee_text',
							'label' => 'Текст',
							'name'  => 'text',
							'type'  => 'text',
						),
					),
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 30,
		)
	);
}

function troya_acf_about_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_about',
			'title'  => 'Блок: Об отеле',
			'fields' => array(
				array(
					'key'           => 'field_about_eyebrow',
					'label'         => 'Надзаголовок',
					'name'          => 'about_eyebrow',
					'type'          => 'text',
					'default_value' => 'Об отеле',
				),
				array(
					'key'           => 'field_about_title',
					'label'         => 'Заголовок (HTML)',
					'name'          => 'about_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => "Место, где<em> история</em><br />встречает комфорт",
				),
				array(
					'key'           => 'field_about_text_1',
					'label'         => 'Абзац 1',
					'name'          => 'about_text_1',
					'type'          => 'textarea',
					'rows'          => 4,
					'default_value' => 'Отель «Троя» расположен на улице Восстания в Казани — в нескольких минутах от главных достопримечательностей татарской столицы. Мы предлагаем уютные современные номера, внимательный персонал и всё необходимое для комфортного отдыха или деловой поездки.',
				),
				array(
					'key'           => 'field_about_text_2',
					'label'         => 'Абзац 2',
					'name'          => 'about_text_2',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'Казань — уникальный город, где слились русская и татарская культуры. Мы поможем вам открыть его через авторские экскурсии, трансферы и индивидуальные маршруты.',
				),
				array(
					'key'          => 'field_about_stats',
					'label'        => 'Статистика',
					'name'         => 'about_stats',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Добавить',
					'sub_fields'   => array(
						array(
							'key'   => 'field_about_stat_num',
							'label' => 'Число',
							'name'  => 'num',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_about_stat_label',
							'label' => 'Подпись',
							'name'  => 'label',
							'type'  => 'text',
						),
					),
				),
				array(
					'key'           => 'field_about_image',
					'label'         => 'Изображение',
					'name'          => 'about_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'           => 'field_about_card_label',
					'label'         => 'Карточка: метка',
					'name'          => 'about_card_label',
					'type'          => 'text',
					'default_value' => 'Адрес',
				),
				array(
					'key'           => 'field_about_card_title',
					'label'         => 'Карточка: заголовок',
					'name'          => 'about_card_title',
					'type'          => 'text',
					'default_value' => 'ул. Восстания, 119',
				),
				array(
					'key'           => 'field_about_card_sub',
					'label'         => 'Карточка: подпись',
					'name'          => 'about_card_sub',
					'type'          => 'text',
					'default_value' => 'Казань, Татарстан',
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 40,
		)
	);
}

function troya_acf_rooms_section_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_rooms_section',
			'title'  => 'Блок: Номера (секция на главной)',
			'fields' => array(
				array(
					'key'           => 'field_rooms_eyebrow',
					'label'         => 'Надзаголовок',
					'name'          => 'rooms_eyebrow',
					'type'          => 'text',
					'default_value' => 'Номерной фонд',
				),
				array(
					'key'           => 'field_rooms_title',
					'label'         => 'Заголовок',
					'name'          => 'rooms_title',
					'type'          => 'text',
					'default_value' => 'Выберите свой номер',
				),
				array(
					'key'           => 'field_rooms_note',
					'label'         => 'Примечание',
					'name'          => 'rooms_note',
					'type'          => 'text',
					'default_value' => 'Завтрак — 300 ₽ · Доп. кровать — 1 200 ₽',
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 50,
		)
	);
}

function troya_acf_amenities_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_amenities',
			'title'  => 'Блок: Удобства',
			'fields' => array(
				array(
					'key'           => 'field_amenities_eyebrow',
					'label'         => 'Надзаголовок',
					'name'          => 'amenities_eyebrow',
					'type'          => 'text',
					'default_value' => 'Сервис',
				),
				array(
					'key'           => 'field_amenities_title',
					'label'         => 'Заголовок (HTML)',
					'name'          => 'amenities_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => "Всё для вашего<br /><em>комфорта</em>",
				),
				array(
					'key'           => 'field_amenities_aside',
					'label'         => 'Текст справа',
					'name'          => 'amenities_aside',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => 'Не список галочек — а атмосфера заботы, которую вы чувствуете с первой минуты.',
				),
				array(
					'key'          => 'field_amenities_items',
					'label'        => 'Список удобств',
					'name'         => 'amenities_items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Добавить удобство',
					'sub_fields'   => array(
						array(
							'key'           => 'field_amenity_icon',
							'label'         => 'Иконка',
							'name'          => 'icon',
							'type'          => 'image',
							'return_format' => 'array',
						),
						array(
							'key'   => 'field_amenity_title',
							'label' => 'Заголовок',
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_amenity_desc',
							'label' => 'Описание',
							'name'  => 'desc',
							'type'  => 'textarea',
							'rows'  => 2,
						),
					),
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 60,
		)
	);
}

function troya_acf_excursions_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_excursions',
			'title'  => 'Блок: Экскурсии',
			'fields' => array(
				array(
					'key'           => 'field_excursions_eyebrow',
					'label'         => 'Надзаголовок',
					'name'          => 'excursions_eyebrow',
					'type'          => 'text',
					'default_value' => 'Экскурсии',
				),
				array(
					'key'           => 'field_excursions_title',
					'label'         => 'Заголовок (HTML)',
					'name'          => 'excursions_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => "Откройте Татарстан<br /><em>вместе с нами</em>",
				),
				array(
					'key'           => 'field_excursions_lead',
					'label'         => 'Лид',
					'name'          => 'excursions_lead',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'Мы организуем авторские экскурсии по Казани и всему Татарстану — от ночных прогулок по городу до речных путешествий на остров Свияжск.',
				),
				array(
					'key'           => 'field_excursions_image',
					'label'         => 'Фото',
					'name'          => 'excursions_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'           => 'field_excursions_cta',
					'label'         => 'Текст кнопки',
					'name'          => 'excursions_cta',
					'type'          => 'text',
					'default_value' => 'Узнать подробности и цены',
				),
				array(
					'key'          => 'field_excursions_items',
					'label'        => 'Список экскурсий',
					'name'         => 'excursions_items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Добавить экскурсию',
					'sub_fields'   => array(
						array(
							'key'   => 'field_excursion_title',
							'label' => 'Название',
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_excursion_desc',
							'label' => 'Описание',
							'name'  => 'desc',
							'type'  => 'textarea',
							'rows'  => 2,
						),
					),
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 70,
		)
	);
}

function troya_acf_reviews_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_reviews',
			'title'  => 'Блок: Отзывы',
			'fields' => array(
				array(
					'key'           => 'field_reviews_eyebrow',
					'label'         => 'Надзаголовок',
					'name'          => 'reviews_eyebrow',
					'type'          => 'text',
					'default_value' => 'Отзывы гостей',
				),
				array(
					'key'           => 'field_reviews_title',
					'label'         => 'Заголовок (HTML)',
					'name'          => 'reviews_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => "Голоса тех,<br /><em>кто уже был</em>",
				),
				array(
					'key'          => 'field_reviews_items',
					'label'        => 'Отзывы',
					'name'         => 'reviews_items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Добавить отзыв',
					'sub_fields'   => array(
						array(
							'key'   => 'field_review_name',
							'label' => 'Имя',
							'name'  => 'name',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_review_text',
							'label' => 'Текст',
							'name'  => 'text',
							'type'  => 'textarea',
							'rows'  => 3,
						),
					),
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 80,
		)
	);
}

function troya_acf_contact_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_contact',
			'title'  => 'Блок: Контакты',
			'fields' => array(
				array(
					'key'           => 'field_contact_eyebrow',
					'label'         => 'Надзаголовок',
					'name'          => 'contact_eyebrow',
					'type'          => 'text',
					'default_value' => 'Контакты',
				),
				array(
					'key'           => 'field_contact_title',
					'label'         => 'Заголовок (HTML)',
					'name'          => 'contact_title',
					'type'          => 'textarea',
					'rows'          => 2,
					'default_value' => "Свяжитесь<br /><em>с нами</em>",
				),
				array(
					'key'           => 'field_contact_image',
					'label'         => 'Фото / карта',
					'name'          => 'contact_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'           => 'field_contact_cta',
					'label'         => 'Текст кнопки',
					'name'          => 'contact_cta',
					'type'          => 'text',
					'default_value' => 'Позвонить и забронировать',
				),
				array(
					'key'           => 'field_contact_map_url',
					'label'         => 'Ссылка на Яндекс Карты',
					'name'          => 'contact_map_url',
					'type'          => 'url',
					'default_value' => 'https://yandex.ru/maps/-/CThvQI9G',
				),
				array(
					'key'           => 'field_contact_map_embed',
					'label'         => 'URL iframe карты',
					'name'          => 'contact_map_embed',
					'type'          => 'url',
					'default_value' => 'https://yandex.ru/map-widget/v1/?ll=49.051859%2C55.831398&mode=search&ol=geo&ouri=ymapsbm1%3A%2F%2Fgeo%3Fdata%3DCgg1NjI3NTI5NxJ80KDQvtGB0YHQuNGPLCDQoNC10YHQv9GD0LHQu9C40LrQsCDQotCw0YLQsNGA0YHRgtCw0L0gKNCi0LDRgtCw0YDRgdGC0LDQvSksINCa0LDQt9Cw0L3RjCwg0YPQu9C40YbQsCDQktC-0YHRgdGC0LDQvdC40Y8sIDExOSIKDRs1REIVWlNfQg%2C%2C&z=17.13',
					'instructions'  => 'src из виджета Яндекс Карт',
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 90,
		)
	);
}

function troya_acf_footer_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_footer',
			'title'  => 'Блок: Подвал',
			'fields' => array(
				array(
					'key'           => 'field_footer_brand',
					'label'         => 'Бренд (HTML)',
					'name'          => 'footer_brand',
					'type'          => 'text',
					'default_value' => 'Отель <span>Троя</span>',
				),
				array(
					'key'           => 'field_footer_meta',
					'label'         => 'Реквизиты',
					'name'          => 'footer_meta',
					'type'          => 'text',
					'default_value' => 'ООО «ТРОЯ» · ИНН 1657131940 · Казань, ул. Восстания, 119',
				),
				array(
					'key'           => 'field_footer_copy',
					'label'         => 'Копирайт',
					'name'          => 'footer_copy',
					'type'          => 'text',
					'default_value' => '© 2026 Все права защищены',
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 100,
		)
	);
}

function troya_acf_modal_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_modal',
			'title'  => 'Блок: Модалка бронирования',
			'fields' => array(
				array(
					'key'           => 'field_modal_title',
					'label'         => 'Заголовок',
					'name'          => 'modal_title',
					'type'          => 'text',
					'default_value' => 'Онлайн-бронирование',
				),
				array(
					'key'           => 'field_modal_success_title',
					'label'         => 'Успех: заголовок',
					'name'          => 'modal_success_title',
					'type'          => 'text',
					'default_value' => 'Заявка отправлена',
				),
				array(
					'key'           => 'field_modal_success_text',
					'label'         => 'Успех: текст',
					'name'          => 'modal_success_text',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'Мы свяжемся с вами в ближайшее время для подтверждения бронирования.',
				),
				array(
					'key'           => 'field_modal_hint',
					'label'         => 'Подсказка под формой',
					'name'          => 'modal_hint',
					'type'          => 'text',
					'default_value' => '* ОБЯЗАТЕЛЬНЫЕ ПОЛЯ · ЗАВТРАК +300 ₽ · ДОП. КРОВАТЬ +1 200 ₽',
				),
			),
			'location'   => troya_acf_location_options(),
			'menu_order' => 110,
		)
	);
}

function troya_acf_room_fields(): void {
	acf_add_local_field_group(
		array(
			'key'    => 'group_troya_room',
			'title'  => 'Поля номера',
			'fields' => array(
				array(
					'key'   => 'field_room_tag',
					'label' => 'Тег',
					'name'  => 'room_tag',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_room_subtitle',
					'label' => 'Подзаголовок',
					'name'  => 'room_subtitle',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_room_price',
					'label' => 'Цена',
					'name'  => 'room_price',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_room_desc',
					'label' => 'Краткое описание',
					'name'  => 'room_desc',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'           => 'field_room_image',
					'label'         => 'Главное фото',
					'name'          => 'room_image',
					'type'          => 'image',
					'return_format' => 'array',
				),
				array(
					'key'           => 'field_room_gallery',
					'label'         => 'Галерея номера',
					'name'          => 'room_gallery',
					'type'          => 'gallery',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'insert'        => 'append',
					'library'       => 'all',
				),
				array(
					'key'          => 'field_room_features',
					'label'        => 'Удобства номера',
					'name'         => 'room_features',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Добавить',
					'sub_fields'   => array(
						array(
							'key'   => 'field_room_feature_text',
							'label' => 'Пункт',
							'name'  => 'text',
							'type'  => 'text',
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'troya_room',
					),
				),
			),
		)
	);
}
