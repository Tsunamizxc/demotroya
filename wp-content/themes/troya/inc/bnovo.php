<?php
/**
 * Bnovo booking helpers: URLs, availability calendar, room type IDs.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

function troya_bnovo_uid(): string {
	return (string) troya_option( 'bnovo_uid', 'eb9cee17-77f8-4f25-9817-b3fc2080617b' );
}

/**
 * Default Bnovo room-type IDs keyed by WP room title.
 *
 * @return array<string, string>
 */
function troya_bnovo_default_room_map(): array {
	return array(
		'2-х местный Стандарт'       => '25813,24764',
		'3-х местный Стандарт'       => '25814,25815,24765',
		'Бизнес-комфорт'             => '25816,24766',
		'Бизнес-комфорт семейный'    => '24767,259946,264374',
		'Бизнес-комфорт+'            => '25817,24768,25818',
		// Legacy titles.
		'2-местный Стандарт+ с раздельными кроватями' => '25813,24764',
		'3-местный Стандарт+'                         => '25814,25815,24765',
		'БИЗНЕС – КОМФОРТ'                            => '25816,24766',
		'БИЗНЕС – КОМФОРТ СЕМЕЙНЫЙ'                   => '24767,259946,264374',
		'БИЗНЕС – КОМФОРТ+'                           => '25817,24768,25818',
		'Standard+'           => '25813,24764',
		'Standard+ Family'    => '25814,25815,24765',
		'Business Comfort'    => '25816,24766',
		'Business Family'     => '24767,259946,264374',
		'Business Comfort+'   => '25817,24768,25818',
	);
}

/**
 * @return int[]
 */
function troya_parse_bnovo_ids( string $raw ): array {
	$parts = preg_split( '/[\s,;]+/', $raw ) ?: array();
	$ids   = array();

	foreach ( $parts as $part ) {
		$id = (int) preg_replace( '/\D+/', '', (string) $part );
		if ( $id > 0 ) {
			$ids[] = $id;
		}
	}

	return array_values( array_unique( $ids ) );
}

/**
 * @return int[]
 */
function troya_room_bnovo_ids( $post_id = 0 ): array {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$raw     = (string) troya_field( 'room_bnovo_ids', '', $post_id );

	if ( '' === trim( $raw ) ) {
		$title = get_the_title( $post_id );
		$map   = troya_bnovo_default_room_map();
		$raw   = $map[ $title ] ?? '';
	}

	return troya_parse_bnovo_ids( $raw );
}

/**
 * @param array<string, scalar> $args Query args (dfrom, dto, adults, onlyrooms…).
 */
function troya_booking_url( array $args = array() ): string {
	$page_id = troya_option( 'booking_page_id', 0 );
	$url     = '';

	if ( is_numeric( $page_id ) && (int) $page_id > 0 ) {
		$url = (string) get_permalink( (int) $page_id );
	}

	if ( ! $url ) {
		$page = get_page_by_path( 'booking' );
		$url  = $page ? (string) get_permalink( $page ) : home_url( '/booking/' );
	}

	$allowed = array( 'dfrom', 'dto', 'adults', 'children', 'lang', 'currency', 'padding', 'radius', 'promo', 'onlyrooms', 'uid' );
	$query   = array();

	foreach ( $args as $key => $value ) {
		if ( ! in_array( $key, $allowed, true ) || '' === $value || null === $value ) {
			continue;
		}
		$query[ $key ] = $value;
	}

	if ( empty( $query['lang'] ) ) {
		$query['lang'] = 'ru';
	}

	return $query ? add_query_arg( $query, $url ) : $url;
}

/**
 * Booking URL for a concrete room category.
 *
 * @param int|array<int> $room_ids Room post ID or list of Bnovo type IDs.
 * @param array<string, scalar> $extra Extra query args.
 */
function troya_room_booking_url( $room_ids = 0, array $extra = array() ): string {
	if ( is_numeric( $room_ids ) || empty( $room_ids ) ) {
		$ids = troya_room_bnovo_ids( (int) $room_ids );
	} else {
		$ids = array_map( 'intval', (array) $room_ids );
	}

	$args = $extra;
	if ( $ids ) {
		$args['onlyrooms'] = implode( ',', $ids );
	}

	return troya_booking_url( $args );
}

/**
 * Fetch min prices / availability from Bnovo public API.
 *
 * @param int[] $room_type_ids
 * @return array<string, array{price:?int,guests:?int,available:bool}>
 */
function troya_bnovo_fetch_min_prices( array $room_type_ids = array(), int $days = 42 ): array {
	$uid = troya_bnovo_uid();
	if ( ! $uid ) {
		return array();
	}

	$days = max( 7, min( 90, $days ) );
	$from = wp_date( 'd-m-Y' );
	$to   = wp_date( 'd-m-Y', strtotime( '+' . $days . ' days' ) );
	$key  = 'troya_bnovo_mp_' . md5( $uid . '|' . implode( ',', $room_type_ids ) . '|' . $from . '|' . $to );

	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$params = array(
		'uid'    => $uid,
		'dfrom'  => $from,
		'dto'    => $to,
		'format' => 'd-m-Y',
	);

	if ( $room_type_ids ) {
		$params['room_type_id'] = implode( ',', $room_type_ids );
	}

	$url  = 'https://public-api.reservationsteps.ru/v1/api/min_prices?' . http_build_query( $params );
	$res  = wp_remote_get(
		$url,
		array(
			'timeout' => 12,
			'headers' => array(
				'Accept'  => 'application/json',
				'Referer' => 'https://reservationsteps.ru/',
			),
		)
	);

	if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		return array();
	}

	$data   = json_decode( (string) wp_remote_retrieve_body( $res ), true );
	$raw    = is_array( $data ) && isset( $data['min_prices'] ) && is_array( $data['min_prices'] ) ? $data['min_prices'] : array();
	$parsed = array();

	foreach ( $raw as $date => $row ) {
		$available = is_array( $row ) && isset( $row['p'] ) && null !== $row['p'];
		$parsed[ (string) $date ] = array(
			'price'     => $available ? (int) $row['p'] : null,
			'guests'    => $available && isset( $row['g'] ) ? (int) $row['g'] : null,
			'available' => $available,
		);
	}

	if ( $parsed ) {
		set_transient( $key, $parsed, HOUR_IN_SECONDS );
	}

	return $parsed;
}

/**
 * @param array<string, array{price:?int,guests:?int,available:bool}> $prices
 * @return array{total:int,free:int,busy:int,next:string,min_price:?int,days:array}
 */
function troya_bnovo_availability_stats( array $prices ): array {
	$total = count( $prices );
	$free  = 0;
	$busy  = 0;
	$next  = '';
	$min   = null;
	$days  = array();

	foreach ( $prices as $date => $row ) {
		$days[] = array(
			'date'      => $date,
			'available' => ! empty( $row['available'] ),
			'price'     => $row['price'] ?? null,
		);

		if ( ! empty( $row['available'] ) ) {
			++$free;
			if ( ! $next ) {
				$next = $date;
			}
			if ( null !== $row['price'] && ( null === $min || $row['price'] < $min ) ) {
				$min = (int) $row['price'];
			}
		} else {
			++$busy;
		}
	}

	return array(
		'total'     => $total,
		'free'      => $free,
		'busy'      => $busy,
		'next'      => $next,
		'min_price' => $min,
		'days'      => $days,
	);
}

function troya_format_price_rub( ?int $price ): string {
	if ( null === $price ) {
		return '';
	}

	return number_format_i18n( $price, 0 ) . ' ₽';
}

function troya_format_ru_date( string $ymd ): string {
	$ts = strtotime( $ymd );
	if ( ! $ts ) {
		return $ymd;
	}

	$months = array(
		1  => 'янв',
		2  => 'фев',
		3  => 'мар',
		4  => 'апр',
		5  => 'май',
		6  => 'июн',
		7  => 'июл',
		8  => 'авг',
		9  => 'сен',
		10 => 'окт',
		11 => 'ноя',
		12 => 'дек',
	);

	$m = (int) wp_date( 'n', $ts );

	return wp_date( 'j', $ts ) . ' ' . ( $months[ $m ] ?? wp_date( 'M', $ts ) );
}

/**
 * Render availability block for a room (Bnovo min_prices).
 *
 * @param int  $post_id  Room post ID.
 * @param bool $embedded Inside booking card (no outer card chrome).
 */
function troya_render_room_availability( int $post_id = 0, bool $embedded = false ): void {
	$post_id = $post_id ?: (int) get_the_ID();
	$ids     = troya_room_bnovo_ids( $post_id );
	$prices  = troya_bnovo_fetch_min_prices( $ids, 45 );

	if ( ! $prices ) {
		return;
	}

	$stats = troya_bnovo_availability_stats( $prices );
	$book  = troya_room_booking_url( $ids );

	$by_month = array();
	foreach ( $stats['days'] as $day ) {
		$ts  = strtotime( $day['date'] );
		$key = wp_date( 'Y-m', $ts );
		if ( ! isset( $by_month[ $key ] ) ) {
			$by_month[ $key ] = array(
				'label' => troya_month_label( (int) wp_date( 'n', $ts ), (int) wp_date( 'Y', $ts ) ),
				'days'  => array(),
			);
		}
		$by_month[ $key ]['days'][] = $day;
	}

	$wrap_class = 'room-avail' . ( $embedded ? ' room-avail--embedded' : '' );
	?>
	<div class="<?php echo esc_attr( $wrap_class ); ?>" data-bnovo-avail>
		<div class="room-avail__head">
			<p class="eyebrow"><?php echo $embedded ? 'Свободные даты' : 'Доступность · Bnovo'; ?></p>
			<?php if ( ! $embedded ) : ?>
				<p class="room-avail__lead">Свободные даты и цены по этому номеру из модуля онлайн-бронирования.</p>
			<?php endif; ?>
		</div>

		<div class="room-avail__stats">
			<div class="room-avail__stat">
				<strong><?php echo esc_html( (string) $stats['free'] ); ?></strong>
				<span>свободных из <?php echo esc_html( (string) $stats['total'] ); ?> дней</span>
			</div>
			<div class="room-avail__stat">
				<strong><?php echo $stats['next'] ? esc_html( troya_format_ru_date( $stats['next'] ) ) : '—'; ?></strong>
				<span>ближайший заезд</span>
			</div>
			<div class="room-avail__stat">
				<strong><?php echo $stats['min_price'] ? esc_html( 'от ' . troya_format_price_rub( $stats['min_price'] ) ) : '—'; ?></strong>
				<span>мин. цена за ночь</span>
			</div>
		</div>

		<div class="room-avail__calendars">
			<?php foreach ( array_slice( $by_month, 0, 2, true ) as $month ) : ?>
				<div class="room-avail__month">
					<p class="room-avail__month-label"><?php echo esc_html( $month['label'] ); ?></p>
					<div class="room-avail__weekdays" aria-hidden="true">
						<span>пн</span><span>вт</span><span>ср</span><span>чт</span><span>пт</span><span>сб</span><span>вс</span>
					</div>
					<div class="room-avail__grid">
						<?php
						$first_ts = strtotime( $month['days'][0]['date'] );
						$pad      = ( (int) wp_date( 'N', $first_ts ) ) - 1;
						for ( $i = 0; $i < $pad; $i++ ) {
							echo '<span class="room-avail__day is-empty"></span>';
						}
						foreach ( $month['days'] as $day ) :
							$ts      = strtotime( $day['date'] );
							$dfrom   = wp_date( 'd-m-Y', $ts );
							$dto     = wp_date( 'd-m-Y', strtotime( '+1 day', $ts ) );
							$href    = troya_room_booking_url(
								$ids,
								array(
									'dfrom'  => $dfrom,
									'dto'    => $dto,
									'adults' => 2,
								)
							);
							$avail   = ! empty( $day['available'] );
							$classes = 'room-avail__day' . ( $avail ? ' is-free' : ' is-busy' );
							$label   = $avail
								? sprintf( 'Свободно %s, от %s', troya_format_ru_date( $day['date'] ), troya_format_price_rub( (int) $day['price'] ) )
								: sprintf( 'Занято %s', troya_format_ru_date( $day['date'] ) );
							?>
							<?php if ( $avail ) : ?>
								<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $href ); ?>" title="<?php echo esc_attr( $label ); ?>">
									<span class="room-avail__num"><?php echo esc_html( wp_date( 'j', $ts ) ); ?></span>
									<?php if ( $day['price'] ) : ?>
										<span class="room-avail__price"><?php echo esc_html( number_format_i18n( (int) $day['price'] / 1000, 1 ) ); ?>к</span>
									<?php endif; ?>
								</a>
							<?php else : ?>
								<span class="<?php echo esc_attr( $classes ); ?>" title="<?php echo esc_attr( $label ); ?>">
									<span class="room-avail__num"><?php echo esc_html( wp_date( 'j', $ts ) ); ?></span>
								</span>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="room-avail__legend">
			<span><i class="is-free"></i> Свободно</span>
			<span><i class="is-busy"></i> Занято</span>
			<?php if ( ! $embedded ) : ?>
				<a class="room-avail__all" href="<?php echo esc_url( $book ); ?>">Открыть виджет бронирования →</a>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

function troya_month_label( int $month, int $year ): string {
	$names = array(
		1  => 'Январь',
		2  => 'Февраль',
		3  => 'Март',
		4  => 'Апрель',
		5  => 'Май',
		6  => 'Июнь',
		7  => 'Июль',
		8  => 'Август',
		9  => 'Сентябрь',
		10 => 'Октябрь',
		11 => 'Ноябрь',
		12 => 'Декабрь',
	);

	return ( $names[ $month ] ?? '' ) . ' ' . $year;
}
