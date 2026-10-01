<?php
/**
 * Footer + booking modal.
 *
 * @package Troya
 */

defined( 'ABSPATH' ) || exit;

$logo       = troya_img_url( troya_option( 'site_logo' ), troya_asset( 'logo.png' ) );
$legal      = troya_option( 'legal_name', 'ООО «ТРОЯ»' );
$inn        = troya_option( 'legal_inn', '1657131940' );
$eroksti    = troya_option( 'legal_eroksti', 'С162024017530' );
$address    = troya_option( 'site_address', '420095, РФ, РТ, г.Казань, ул.Восстания 119' );
$copy       = troya_option( 'footer_copy', '© 2026 Все права защищены' );
$phone1     = troya_option( 'site_phone_1', '8 (843) 564-46-46' );
$phone2     = troya_option( 'site_phone_2', '8 (904) 678-56-00' );
$phone_acc  = troya_option( 'site_phone_accounting', '8 (843) 564-83-19' );
$acc_hours  = troya_option( 'accounting_hours', 'с 9:00 до 15:00' );
$email      = troya_option( 'site_email', 'hoteltroya@mail.ru' );
$hours      = troya_option( 'site_hours', 'Круглосуточно, 24/7' );
$policy_url = troya_asset( 'docs/privacy-policy.pdf' );
$tel        = static fn( $p ) => 'tel:' . preg_replace( '/\D+/', '', (string) $p );
$m_title    = troya_option( 'modal_title', 'Бронирование по телефону' );
$s_title    = troya_option( 'modal_success_title', 'Заявка отправлена' );
$s_text     = troya_option( 'modal_success_text', 'Мы свяжемся с вами в ближайшее время для подтверждения бронирования.' );
$hint       = troya_option( 'modal_hint', '* ОБЯЗАТЕЛЬНЫЕ ПОЛЯ · ЗАВТРАК +300 ₽' );
?>

<footer class="footer">
	<div class="container footer__container">
		<div class="footer__crest">
			<span class="footer__rule" aria-hidden="true"></span>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer__logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — на главную">
				<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="150" height="150" loading="lazy" decoding="async" />
			</a>
			<span class="footer__rule" aria-hidden="true"></span>
		</div>

		<p class="footer__motto"><?php echo wp_kses_post( (string) troya_option( 'footer_motto', 'Место, где <em>история</em> встречает комфорт' ) ); ?></p>

		<div class="footer__grid">
			<div class="footer__col">
				<h3 class="footer__heading">Адрес</h3>
				<p class="footer__text"><?php echo esc_html( $address ); ?></p>
				<a class="footer__link footer__link--arrow" href="<?php echo esc_url( home_url( '/#directions' ) ); ?>">Как добраться</a>
			</div>

			<div class="footer__col">
				<h3 class="footer__heading">Бронирование</h3>
				<a class="footer__phone" href="<?php echo esc_attr( $tel( $phone1 ) ); ?>"><?php echo esc_html( $phone1 ); ?></a>
				<?php if ( $phone2 ) : ?>
					<a class="footer__phone" href="<?php echo esc_attr( $tel( $phone2 ) ); ?>"><?php echo esc_html( $phone2 ); ?></a>
				<?php endif; ?>
				<a class="footer__link" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				<p class="footer__note"><?php echo esc_html( $hours ); ?></p>
			</div>

			<nav class="footer__col" aria-label="Навигация в футере">
				<h3 class="footer__heading">Отель</h3>
				<ul class="footer__menu">
					<li><a class="footer__link" href="<?php echo esc_url( troya_rooms_url() ); ?>">Номера</a></li>
					<li><a class="footer__link" href="<?php echo esc_url( troya_gallery_url() ); ?>">Галерея</a></li>
					<li><a class="footer__link" href="<?php echo esc_url( home_url( '/#amenities' ) ); ?>">Удобства</a></li>
					<li><a class="footer__link" href="<?php echo esc_url( home_url( '/#excursions' ) ); ?>">Экскурсии</a></li>
					<li><a class="footer__link" href="<?php echo esc_url( home_url( '/#reviews' ) ); ?>">Отзывы</a></li>
					<li><a class="footer__link" href="<?php echo esc_url( troya_booking_url() ); ?>">Онлайн-бронирование</a></li>
				</ul>
			</nav>

			<div class="footer__col">
				<h3 class="footer__heading">Реквизиты</h3>
				<p class="footer__text"><?php echo esc_html( $legal ); ?></p>
				<p class="footer__note">ИНН <?php echo esc_html( $inn ); ?></p>
				<p class="footer__note">ЕРОКСТИ <?php echo esc_html( $eroksti ); ?></p>
				<p class="footer__note">Бухгалтерия: <a class="footer__link" href="<?php echo esc_attr( $tel( $phone_acc ) ); ?>"><?php echo esc_html( $phone_acc ); ?></a>, <?php echo esc_html( $acc_hours ); ?></p>
			</div>
		</div>

		<div class="footer__bottom">
			<p class="footer__copy"><?php echo esc_html( $copy ); ?></p>
			<a class="footer__policy" href="<?php echo esc_url( $policy_url ); ?>" target="_blank" rel="noopener">Политика конфиденциальности</a>
			<a class="footer__credit" href="https://cursiva.ru/" target="_blank" rel="noopener noreferrer" aria-label="Cursiva — веб-студия">
				<span>Создание сайта</span>
				<img src="<?php echo esc_url( troya_asset( 'logo-cursiva-white.png' ) ); ?>" alt="Cursiva" width="120" height="40" loading="lazy" decoding="async" />
			</a>
		</div>
	</div>
</footer>

<div class="modal" id="booking-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-title">
	<div class="modal__backdrop" data-close-modal></div>
	<div class="modal__dialog">
		<div class="modal__header">
			<div class="modal__brand">
				<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
				<div>
					<p class="modal__title" id="modal-title"><?php echo esc_html( $m_title ); ?></p>
					<p class="modal__room" id="modal-room" hidden></p>
				</div>
			</div>
			<button type="button" class="modal__close" data-close-modal aria-label="Закрыть">✕</button>
		</div>

		<div class="modal__success" id="modal-success" hidden>
			<div class="modal__success-icon">✓</div>
			<h3><?php echo esc_html( $s_title ); ?></h3>
			<p>
				<?php echo esc_html( $s_text ); ?><br />
				<strong><?php echo esc_html( $phone1 ); ?></strong>
			</p>
			<button type="button" class="btn btn--gold" data-close-modal>Закрыть</button>
		</div>

		<form class="modal__form" id="booking-form">
			<input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hp-field" aria-hidden="true" style="position:absolute;left:-9999px;opacity:0;height:0;width:0;" />

			<div class="form-row form-row--2">
				<div class="field">
					<label for="name">Ваше имя *</label>
					<input id="name" name="name" required placeholder="Иван Иванов" />
				</div>
				<div class="field">
					<label for="phone">Телефон *</label>
					<input id="phone" name="phone" type="tel" required placeholder="+7 (___) ___-__-__" />
				</div>
			</div>

			<div class="field">
				<label for="email">Email</label>
				<input id="email" name="email" type="email" placeholder="your@email.com" />
			</div>

			<div class="form-row form-row--3">
				<div class="field">
					<label for="checkin">Заезд *</label>
					<input id="checkin" name="checkin" type="date" required />
				</div>
				<div class="field">
					<label for="checkout">Выезд *</label>
					<input id="checkout" name="checkout" type="date" required />
				</div>
				<div class="field">
					<label for="guests">Гостей</label>
					<select id="guests" name="guests">
						<option value="1">1 гость</option>
						<option value="2" selected>2 гостя</option>
						<option value="3">3 гостя</option>
						<option value="4">4 гостя</option>
					</select>
				</div>
			</div>

			<div class="field" id="room-field" hidden>
				<label for="room-type">Тип номера</label>
				<input id="room-type" name="room" readonly />
			</div>

			<div class="field">
				<label for="comment">Комментарий</label>
				<textarea id="comment" name="comment" rows="3" placeholder="Особые пожелания, ранний заезд..."></textarea>
			</div>

			<p class="modal__hint"><?php echo esc_html( $hint ); ?></p>
			<?php troya_render_payment_policy( 'compact' ); ?>
			<p class="modal__error" id="modal-error" hidden></p>
			<button type="submit" class="btn btn--gold btn--block">Отправить заявку</button>
		</form>
	</div>
</div>

</div><!-- #root -->
<?php wp_footer(); ?>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
	(w[c] = w[c] || []).push(function () {
		try {
			w.yaCounter50056810 = new Ya.Metrika2({
				id: 50056810,
				clickmap: true,
				trackLinks: true,
				accurateTrackBounce: true,
				webvisor: true
			});
		} catch (e) {}
	});

	var n = d.getElementsByTagName('script')[0],
		s = d.createElement('script'),
		f = function () { n.parentNode.insertBefore(s, n); };
	s.type = 'text/javascript';
	s.async = true;
	s.src = 'https://mc.yandex.ru/metrika/tag.js';

	if (w.opera == '[object Opera]') {
		d.addEventListener('DOMContentLoaded', f, false);
	} else {
		f();
	}
})(document, window, 'yandex_metrika_callbacks2');
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/50056810" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<!-- BEGIN JIVOSITE CODE -->
<script type="text/javascript">
(function () {
	var widget_id = 'Ydug0EQhLS';
	var d = document;
	var w = window;
	function l() {
		var s = document.createElement('script');
		s.type = 'text/javascript';
		s.async = true;
		s.src = '//code.jivosite.com/script/widget/' + widget_id;
		var ss = document.getElementsByTagName('script')[0];
		ss.parentNode.insertBefore(s, ss);
	}
	if (d.readyState === 'complete') {
		l();
	} else if (w.attachEvent) {
		w.attachEvent('onload', l);
	} else {
		w.addEventListener('load', l, false);
	}
})();
</script>
<!-- END JIVOSITE CODE -->

</body>
</html>
