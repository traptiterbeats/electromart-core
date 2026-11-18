<!-- ۱۴. فوتر کامل و حرفه‌ای -->
<footer class="em-footer">
	<div class="em-container em-footer-grid">
		<div class="em-footer-col">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="em-brand-text">Electro<span>Mart</span></a>
			<p class="em-footer-desc">مرجع تخصصی بررسی و خرید آنلاین لوازم الکترونیکی، لپ‌تاپ، موبایل، تجهیزات صوتی و گیمینگ با ضمانت اصالت و گارانتی معتبر.</p>
		</div>
		<div class="em-footer-col">
			<h4>دسته‌بندی‌های برتر</h4>
			<ul>
				<li><a href="#">لپ‌تاپ و اولترابوک</a></li>
				<li><a href="#">گوشی موبایل و تبلت</a></li>
				<li><a href="#">هدفون و تجهیزات صوتی</a></li>
				<li><a href="#">کنسول بازی و گیمینگ</a></li>
			</ul>
		</div>
		<div class="em-footer-col">
			<h4>خدمات مشتریان</h4>
			<ul>
				<li><a href="#">شرایط گارانتی و بازگشت کالا</a></li>
				<li><a href="#">پیگیری سفارش</a></li>
				<li><a href="#">راهنمای خرید اقساطی</a></li>
				<li><a href="#">تماس با پشتیبانی فنی</a></li>
			</ul>
		</div>
		<div class="em-footer-col">
			<h4>عضویت در خبرنامه تکنولوژی</h4>
			<p>از جدیدترین تخفیف‌ها و عرضه گجت‌های روز مطلع شوید.</p>
			<form class="em-newsletter-form" onsubmit="return false;">
				<input type="email" placeholder="ایمیل شما..." required>
				<button type="submit" class="em-btn em-btn-primary">ثبت</button>
			</form>
		</div>
	</div>
	<div class="em-container em-footer-bottom">
		<p>© <?php echo esc_html( date_i18n( 'Y' ) ); ?> کلیه حقوق این فروشگاه متعلق به ElectroMart است.</p>
	</div>
</footer>

<!-- سبد خرید کشویی (Mini Cart Drawer) -->
<div id="em-mini-cart-drawer" class="em-drawer">
	<div class="em-drawer-header">
		<h3>سبد خرید شما</h3>
		<button type="button" class="em-close-drawer">×</button>
	</div>
	<div class="em-mini-cart-body">
		<?php if ( class_exists( 'WooCommerce' ) ) { woocommerce_mini_cart(); } ?>
	</div>
</div>

<!-- مودال مشاهده سریع محصول (Quick View) -->
<div id="em-quickview-modal" class="em-modal">
	<div class="em-modal-content">
		<button type="button" class="em-modal-close">×</button>
		<div id="em-quickview-body"></div>
	</div>
</div>

<!-- مودال جدول مقایسه محصولات (Compare Modal) -->
<div id="em-compare-modal" class="em-modal">
	<div class="em-modal-content em-modal-large">
		<button type="button" class="em-modal-close">×</button>
		<h3>جدول مقایسه مشخصات فنی محصولات</h3>
		<div id="em-compare-body"></div>
	</div>
</div>

<div id="em-backdrop" class="em-backdrop"></div>

<?php wp_footer(); ?>
</body>
</html>

