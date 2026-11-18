<!-- ۱۴. فوتر کامل و حرفه‌ای -->
<footer class="em-footer">
	<div class="em-container em-footer-grid">
		<div class="em-footer-col">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="em-brand-text">Electro<span>Mart</span></a>
			<p class="em-footer-desc">مرجع تخصصی بررسی و خرید آنلاین لوازم الکترونیکی، لپ‌تاپ، موبایل، تجهیزات صوتی و گیمینگ با ضمانت اصالت و گارانتی معتبر.</p>
		</div>
		<div class="em-footer-col">
			<h4>دسته‌بندی‌های برتر</h4>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1 ) );
			} elseif ( taxonomy_exists( 'product_cat' ) ) {
				electromart_footer_fallback();
			}
			?>
		</div>
		<div class="em-footer-col">
			<h4>خدمات مشتریان</h4>
			<ul>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">حساب کاربری و پیگیری سفارش</a></li>
					<li><a href="<?php echo esc_url( wc_get_cart_url() ); ?>">سبد خرید</a></li>
				<?php endif; ?>
				<li><a href="<?php echo esc_url( electromart_wishlist_url() ); ?>">علاقه‌مندی‌ها</a></li>
				<?php if ( get_privacy_policy_url() ) : ?>
					<li><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">حریم خصوصی</a></li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="em-footer-col">
			<h4>عضویت در خبرنامه تکنولوژی</h4>
			<p>از جدیدترین تخفیف‌ها و عرضه گجت‌های روز مطلع شوید.</p>
			<form class="em-newsletter-form" id="em-newsletter-form" novalidate>
				<input type="email" name="email" placeholder="ایمیل شما..." required aria-label="ایمیل">
				<button type="submit" class="em-btn em-btn-primary">ثبت</button>
			</form>
			<p class="em-newsletter-msg" aria-live="polite"></p>
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
		<button type="button" class="em-close-drawer" aria-label="بستن">×</button>
	</div>
	<div class="em-mini-cart-body">
		<?php if ( class_exists( 'WooCommerce' ) ) { woocommerce_mini_cart(); } ?>
	</div>
</div>

<!-- مودال مشاهده سریع محصول (Quick View) -->
<div id="em-quickview-modal" class="em-modal">
	<div class="em-modal-content">
		<button type="button" class="em-modal-close" aria-label="بستن">×</button>
		<div id="em-quickview-body"></div>
	</div>
</div>

<!-- مودال جدول مقایسه محصولات (Compare Modal) -->
<div id="em-compare-modal" class="em-modal">
	<div class="em-modal-content em-modal-large">
		<button type="button" class="em-modal-close" aria-label="بستن">×</button>
		<h3>جدول مقایسه مشخصات فنی محصولات</h3>
		<div id="em-compare-body"></div>
	</div>
</div>

<div id="em-backdrop" class="em-backdrop"></div>

<?php wp_footer(); ?>
</body>
</html>

