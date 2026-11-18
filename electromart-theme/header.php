<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- 1. نوار اطلاع‌رسانی بالای سایت -->
<div class="em-topbar">
	<div class="em-container em-topbar-inner">
		<span class="em-topbar-text"><?php echo esc_html( get_theme_mod( 'em_topbar_text', 'فروش ویژه محصولات دیجیتال و گجت‌های هوشمند با ۳۰٪ تخفیف ویژه زمان محدود!' ) ); ?></span>
		<a href="<?php echo esc_url( electromart_shop_url() ); ?>" class="em-topbar-cta">خرید آنلاین ←</a>
	</div>
</div>

<!-- 2. هدر با مگامنو و جستجوی زنده -->
<header class="em-header">
	<div class="em-container em-header-inner">
		<button type="button" class="em-mobile-menu-toggle" aria-label="منوی موبایل" aria-expanded="false">
			<span></span><span></span><span></span>
		</button>

		<div class="em-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="em-brand-text">Electro<span>Mart</span></a>
			<?php endif; ?>
		</div>

		<nav class="em-main-nav" aria-label="منوی اصلی">
			<ul class="em-nav-list">
				<?php
				// مگامنوی دسته‌بندی‌ها: از دسته‌بندی‌های واقعی ووکامرس ساخته می‌شود.
				$em_cats = taxonomy_exists( 'product_cat' ) ? get_terms( array(
					'taxonomy'   => 'product_cat',
					'parent'     => 0,
					'hide_empty' => false,
					'exclude'    => get_option( 'default_product_cat' ),
					'number'     => 9,
				) ) : array();
				if ( ! empty( $em_cats ) && ! is_wp_error( $em_cats ) ) :
					?>
					<li class="em-has-megamenu">
						<a href="<?php echo esc_url( electromart_shop_url() ); ?>">دسته‌بندی محصولات <span class="em-chevron">▾</span></a>
						<div class="em-megamenu">
							<div class="em-container em-megamenu-grid">
								<?php foreach ( array_chunk( $em_cats, 3 ) as $chunk ) : ?>
									<div class="em-mega-col">
										<ul>
											<?php foreach ( $chunk as $cat ) :
												$thumb = get_term_meta( $cat->term_id, 'thumbnail_id', true );
												?>
												<li>
													<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="em-mega-link">
														<?php if ( $thumb ) : ?>
															<?php echo wp_get_attachment_image( $thumb, 'thumbnail', false, array( 'class' => 'em-mega-thumb', 'loading' => 'lazy' ) ); ?>
														<?php endif; ?>
														<span><?php echo esc_html( $cat->name ); ?></span>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endforeach; ?>
								<div class="em-mega-promo">
									<div class="em-mega-promo-card">
										<img src="<?php echo esc_url( electromart_img( 'mega-vr.jpg' ) ); ?>" alt="هدست واقعیت مجازی" loading="lazy">
										<span class="em-badge-new">پیشنهاد ویژه</span>
										<h5>نسل جدید کنسول‌ها و هدست‌های واقعیت مجازی</h5>
										<a href="<?php echo esc_url( electromart_cat_link( 'gaming' ) ); ?>" class="em-link-arrow">مشاهده کالکشن ←</a>
									</div>
								</div>
							</div>
						</div>
					</li>
				<?php endif; ?>

				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'depth'          => 1,
					) );
				} else {
					electromart_primary_fallback();
				}
				?>
			</ul>
		</nav>

		<div class="em-header-actions">
			<form class="em-search-wrapper" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" name="s" id="em-live-search-input" placeholder="جستجوی لپ‌تاپ، موبایل، هدفون..." autocomplete="off" aria-label="جستجوی محصولات">
				<input type="hidden" name="post_type" value="product">
				<div id="em-search-results" class="em-search-dropdown"></div>
			</form>

			<button type="button" class="em-icon-btn em-open-compare-modal" title="مقایسه محصولات" aria-label="مقایسه محصولات">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/></svg>
				<span class="em-badge-count em-compare-count">0</span>
			</button>

			<a href="<?php echo esc_url( electromart_wishlist_url() ); ?>" class="em-icon-btn" title="علاقه‌مندی‌ها" aria-label="علاقه‌مندی‌ها">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
				<span class="em-badge-count em-wishlist-count">0</span>
			</a>

			<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>" class="em-icon-btn" title="حساب کاربری" aria-label="حساب کاربری">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
			</a>

			<button type="button" class="em-icon-btn em-cart-trigger" title="سبد خرید" aria-label="سبد خرید">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
				<span class="em-badge-count em-cart-count"><?php echo ( class_exists( 'WooCommerce' ) && WC()->cart ) ? esc_html( WC()->cart->get_cart_contents_count() ) : 0; ?></span>
			</button>
		</div>
	</div>
</header>
