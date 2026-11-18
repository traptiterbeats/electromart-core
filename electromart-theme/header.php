<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- 1. نوار اطلاع‌رسانی مشکی بالای سایت (مطابق تصویر مرجع) -->
<div class="em-topbar">
	<div class="em-container em-topbar-inner">
		<span class="em-topbar-text"><?php echo esc_html( get_theme_mod( 'em_topbar_text', 'فروش ویژه محصولات دیجیتال و گجت‌های هوشمند با ۳۰٪ تخفیف ویژه زمان محدود!' ) ); ?></span>
		<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="em-topbar-cta">خرید آنلاین ←</a>
	</div>
</div>

<!-- 2. هدر مینیمال به همراه مگامنوی چندستونه و جستجوی زنده -->
<header class="em-header">
	<div class="em-container em-header-inner">
		<button type="button" class="em-mobile-menu-toggle" aria-label="منوی موبایل">
			<span></span><span></span><span></span>
		</button>

		<div class="em-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="em-brand-text">Electro<span>Mart</span></a>
			<?php endif; ?>
		</div>

		<!-- 3. منوی اصلی + مگامنوی تصویری دسته‌بندی‌های الکترونیکی -->
		<nav class="em-main-nav">
			<ul class="em-nav-list">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">صفحه اصلی</a></li>
				<li class="em-has-megamenu">
					<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>">دسته‌بندی محصولات <span class="em-chevron">▾</span></a>
					<div class="em-megamenu">
						<div class="em-container em-megamenu-grid">
							<div class="em-mega-col">
								<h4>موبایل، لپ‌تاپ و کامپیوتر</h4>
								<ul>
									<li><a href="#">گوشی موبایل و تبلت</a></li>
									<li><a href="#">لپ‌تاپ و اولترابوک</a></li>
									<li><a href="#">مانیتور و تجهیزات جانبی</a></li>
									<li><a href="#">لوازم جانبی موبایل</a></li>
								</ul>
							</div>
							<div class="em-mega-col">
								<h4>صوتی، تصویری و گجت</h4>
								<ul>
									<li><a href="#">هدفون و هندزفری بی‌سیم</a></li>
									<li><a href="#">اسپیکر و تجهیزات صوتی</a></li>
									<li><a href="#">ساعت و مچ‌بند هوشمند</a></li>
									<li><a href="#">گجت‌های هوشمند خانه</a></li>
								</ul>
							</div>
							<div class="em-mega-col">
								<h4>گیمینگ، شبکه و عکاسی</h4>
								<ul>
									<li><a href="#">کنسول بازی (PS5 / Xbox)</a></li>
									<li><a href="#">تجهیزات حرفه‌ای گیمینگ</a></li>
									<li><a href="#">دوربین و تجهیزات عکاسی</a></li>
									<li><a href="#">مودم و تجهیزات شبکه</a></li>
								</ul>
							</div>
							<div class="em-mega-promo">
								<div class="em-mega-promo-card">
									<span class="em-badge-new">پیشنهاد ویژه</span>
									<h5>نسل جدید کنسول‌ها و هدست‌های واقعیت مجازی</h5>
									<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="em-link-arrow">مشاهده کالکشن ←</a>
								</div>
							</div>
						</div>
					</div>
				</li>
				<li><a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>">فروشگاه</a></li>
				<li><a href="#em-special-deals">تخفیف‌های ویژه</a></li>
				<li><a href="#em-brands-section">برندها</a></li>
			</ul>
		</nav>

		<!-- ابزارهای هدر: جستجوی زنده AJAX، مقایسه، علاقه‌مندی، حساب کاربری، سبد خرید کشویی -->
		<div class="em-header-actions">
			<div class="em-search-wrapper">
				<input type="text" id="em-live-search-input" placeholder="جستجوی لپ‌تاپ، موبایل، هدفون..." autocomplete="off">
				<div id="em-search-results" class="em-search-dropdown"></div>
			</div>

			<button type="button" class="em-icon-btn em-open-compare-modal" title="مقایسه محصولات">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/></svg>
				<span class="em-badge-count em-compare-count">0</span>
			</button>

			<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) : '#' ); ?>" class="em-icon-btn" title="حساب کاربری">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
			</a>

			<button type="button" class="em-icon-btn em-cart-trigger" title="سبد خرید">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
				<span class="em-badge-count em-cart-count"><?php echo ( class_exists( 'WooCommerce' ) && WC()->cart ) ? esc_html( WC()->cart->get_cart_contents_count() ) : 0; ?></span>
			</button>
		</div>
	</div>
</header>

