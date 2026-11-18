<?php get_header(); ?>

<main class="em-front-page">

	<!-- بخش ۴: بنر اصلی بزرگ (Hero Section) با چیدمان دو ستونه تصویر مرجع -->
	<section class="em-hero-section">
		<div class="em-container em-hero-grid">
			<div class="em-hero-content">
				<span class="em-hero-kicker">نسل جدید تکنولوژی و دیجیتال</span>
				<h1 class="em-hero-title"><?php echo esc_html( get_theme_mod( 'em_hero_title', 'فراتر از مرزهای تکنولوژی و صدا.' ) ); ?></h1>
				<p class="em-hero-subtitle"><?php echo esc_html( get_theme_mod( 'em_hero_subtitle', 'تجربه نسل جدید لپ‌تاپ‌ها، موبایل، تجهیزات صوتی و گیمینگ با ضمانت اصالت کالا' ) ); ?></p>
				<div class="em-hero-actions">
					<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="em-btn em-btn-dark">مشاهده و خرید آنلاین</a>
					<a href="#em-shop-collection" class="em-btn em-btn-primary">کاوش دسته‌بندی‌ها</a>
				</div>
			</div>
			<div class="em-hero-visual">
				<?php $hero_img = get_theme_mod( 'em_hero_image' ); ?>
				<?php if ( $hero_img ) : ?>
					<img src="<?php echo esc_url( $hero_img ); ?>" alt="محصول شاخص" class="em-hero-img">
				<?php else : ?>
					<div class="em-hero-mockup">
						<div class="em-floating-device">PRO SERIES X</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- ۴ کارت ویژگی خدماتی خاکستری روشن زیر بنر اصلی (مطابق تصویر مرجع: ارسال رایگان، ضمانت بازگشت، پرداخت امن، پشتیبانی ۲۴/۷) -->
	<section class="em-features-strip">
		<div class="em-container em-features-grid">
			<div class="em-feature-card">
				<div class="em-feature-icon">🚚</div>
				<h4>ارسال سریع و رایگان</h4>
				<p>برای سفارش‌های بالای ۵ میلیون تومان</p>
			</div>
			<div class="em-feature-card">
				<div class="em-feature-icon">🔄</div>
				<h4>۳۰ روز ضمانت بازگشت</h4>
				<p>تضمین اصالت و سلامت فیزیکی کالا</p>
			</div>
			<div class="em-feature-card">
				<div class="em-feature-icon">🔒</div>
				<h4>پرداخت امن آنلاین</h4>
				<p>درگاه بانکی ایمن و امکان خرید اقساطی</p>
			</div>
			<div class="em-feature-card">
				<div class="em-feature-icon">🎧</div>
				<h4>مشاوره تخصصی ۲۴/۷</h4>
				<p>پشتیبانی فنی پیش و پس از خرید</p>
			</div>
		</div>
	</section>

	<!-- بخش ۵: دسته‌بندی‌های محبوب لوازم الکترونیکی با کارت‌های تصویری -->
	<section class="em-section em-popular-cats">
		<div class="em-container">
			<div class="em-section-header">
				<h2 class="em-section-title">دسته‌بندی‌های تخصصی الکترونیک</h2>
				<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="em-view-all">همه دسته‌بندی‌ها ←</a>
			</div>
			<div class="em-cat-pills-grid">
				<?php
				$electronic_cats = array(
					array( 'title' => 'موبایل و تبلت', 'sub' => 'پرچمدار و میان‌رده' ),
					array( 'title' => 'لپ‌تاپ و کامپیوتر', 'sub' => 'مهندسی و اولترابوک' ),
					array( 'title' => 'هدفون و هندزفری', 'sub' => 'حذف نویز هوشمند' ),
					array( 'title' => 'ساعت هوشمند', 'sub' => 'پایش سلامت و ورزش' ),
					array( 'title' => 'کنسول و گیمینگ', 'sub' => 'PS5، Xbox و جانبی' ),
					array( 'title' => 'دوربین و عکاسی', 'sub' => 'بدون آینه و لنز' ),
				);
				foreach ( $electronic_cats as $cat_item ) :
				?>
					<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="em-cat-pill-card">
						<strong><?php echo esc_html( $cat_item['title'] ); ?></strong>
						<span><?php echo esc_html( $cat_item['sub'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- بخش ۶: محصولات جدید (New Arrivals) -->
	<section class="em-section">
		<div class="em-container">
			<div class="em-section-header">
				<h2 class="em-section-title">جدیدترین محصولات (New Arrivals)</h2>
				<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="em-view-all">مشاهده همه ←</a>
			</div>
			<div class="em-products-grid">
				<?php
				if ( class_exists( 'WooCommerce' ) ) {
					$new_query = new WP_Query( array(
						'post_type'      => 'product',
						'posts_per_page' => 8,
						'orderby'        => 'date',
						'order'          => 'DESC',
					) );
					if ( $new_query->have_posts() ) {
						while ( $new_query->have_posts() ) {
							$new_query->the_post();
							wc_get_template_part( 'content', 'product' );
						}
						wp_reset_postdata();
					}
				}
				?>
			</div>
		</div>
	</section>

	<!-- بخش ۷: کالکشن منتخب (Shop Collection) با چیدمان Bento Grid نامتقارن دقیقاً مطابق تصویر مرجع -->
	<section id="em-shop-collection" class="em-section">
		<div class="em-container">
			<div class="em-section-header">
				<h2 class="em-section-title">کالکشن منتخب (Shop Collection)</h2>
			</div>
			<div class="em-bento-collection">
				<div class="em-bento-card em-bento-tall">
					<div class="em-bento-text">
						<span class="em-bento-tag">صدا و تصویر حرفه‌ای</span>
						<h3>هدفون، اسپیکر و تجهیزات صوتی</h3>
						<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="em-bento-link">مشاهده کالکشن ←</a>
					</div>
				</div>
				<div class="em-bento-stack">
					<div class="em-bento-card em-bento-wide">
						<div class="em-bento-text">
							<span class="em-bento-tag">پردازش قدرتمند</span>
							<h3>لپ‌تاپ، مانیتور و تجهیزات کامپیوتر</h3>
							<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="em-bento-link">مشاهده کالکشن ←</a>
						</div>
					</div>
					<div class="em-bento-card em-bento-wide">
						<div class="em-bento-text">
							<span class="em-bento-tag">اکوسیستم هوشمند</span>
							<h3>موبایل، ساعت هوشمند و لوازم جانبی</h3>
							<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="em-bento-link">مشاهده کالکشن ←</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- بخش ۸: محصولات محبوب (Popular Products) -->
	<section class="em-section">
		<div class="em-container">
			<div class="em-section-header">
				<h2 class="em-section-title">محصولات محبوب (Popular Products)</h2>
			</div>
			<div class="em-products-grid">
				<?php
				if ( class_exists( 'WooCommerce' ) ) {
					$popular_query = new WP_Query( array(
						'post_type'      => 'product',
						'posts_per_page' => 8,
						'meta_key'       => '_wc_average_rating',
						'orderby'        => 'meta_value_num',
						'order'          => 'DESC',
					) );
					if ( $popular_query->have_posts() ) {
						while ( $popular_query->have_posts() ) {
							$popular_query->the_post();
							wc_get_template_part( 'content', 'product' );
						}
						wp_reset_postdata();
					}
				}
				?>
			</div>
		</div>
	</section>

	<!-- بخش ۹ و ۱۲: بنرهای تبلیغاتی و پیشنهادهای شگفت‌انگیز (Special Deals) -->
	<section id="em-special-deals" class="em-section">
		<div class="em-container">
			<div class="em-promo-banner">
				<div class="em-promo-content">
					<span class="em-promo-badge">پیشنهاد ویژه محدود</span>
					<h2>تجهیزات گیمینگ، کنسول‌های نسل نهم و مانیتورهای اولتراواید</h2>
					<p>با تخفیف ویژه و ارسال فوری به سراسر کشور</p>
					<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#' ); ?>" class="em-btn em-btn-primary">مشاهده پیشنهادهای ویژه</a>
				</div>
			</div>

			<?php if ( class_exists( 'WooCommerce' ) ) :
				$sale_ids = wc_get_product_ids_on_sale();
				if ( ! empty( $sale_ids ) ) :
			?>
				<div class="em-section-header" style="margin-top: 40px;">
					<h2 class="em-section-title">محصولات دارای تخفیف ویژه</h2>
				</div>
				<div class="em-products-grid">
					<?php
					$sale_query = new WP_Query( array(
						'post_type'      => 'product',
						'posts_per_page' => 4,
						'post__in'       => $sale_ids,
					) );
					while ( $sale_query->have_posts() ) {
						$sale_query->the_post();
						wc_get_template_part( 'content', 'product' );
					}
					wp_reset_postdata();
					?>
				</div>
			<?php endif; endif; ?>
		</div>
	</section>

	<!-- بخش ۱۰: محصولات پرفروش (Best Sellers) -->
	<section class="em-section">
		<div class="em-container">
			<div class="em-section-header">
				<h2 class="em-section-title">پرفروش‌ترین‌های فروشگاه</h2>
			</div>
			<div class="em-products-grid">
				<?php
				if ( class_exists( 'WooCommerce' ) ) {
					$best_query = new WP_Query( array(
						'post_type'      => 'product',
						'posts_per_page' => 4,
						'meta_key'       => 'total_sales',
						'orderby'        => 'meta_value_num',
						'order'          => 'DESC',
					) );
					if ( $best_query->have_posts() ) {
						while ( $best_query->have_posts() ) {
							$best_query->the_post();
							wc_get_template_part( 'content', 'product' );
						}
						wp_reset_postdata();
					}
				}
				?>
			</div>
		</div>
	</section>

	<!-- بخش ۱۱: برندهای معتبر جهانی همراه با لینک به صفحه آرشیو برند -->
	<section id="em-brands-section" class="em-section em-brands-strip">
		<div class="em-container">
			<div class="em-section-header">
				<h2 class="em-section-title">برندهای معتبر جهانی</h2>
			</div>
			<div class="em-brands-grid">
				<?php
				$brand_terms = get_terms( array(
					'taxonomy'   => 'product_brand',
					'hide_empty' => false,
					'number'     => 8,
				) );
				if ( ! empty( $brand_terms ) && ! is_wp_error( $brand_terms ) ) :
					foreach ( $brand_terms as $b_term ) :
				?>
					<a href="<?php echo esc_url( get_term_link( $b_term ) ); ?>" class="em-brand-box"><?php echo esc_html( $b_term->name ); ?></a>
				<?php
					endforeach;
				else :
					$default_brands = array( 'Apple', 'Samsung', 'Sony', 'ASUS', 'Bose', 'JBL', 'Logitech', 'Xiaomi' );
					foreach ( $default_brands as $b_name ) :
						echo '<div class="em-brand-box">' . esc_html( $b_name ) . '</div>';
					endforeach;
				endif;
				?>
			</div>
		</div>
	</section>

	<!-- بخش ۱۳: محصولات اخیراً مشاهده‌شده توسط کاربر -->
	<?php
	$viewed_ids = isset( $_COOKIE['em_recently_viewed'] ) ? array_filter( array_map( 'absint', explode( ',', sanitize_text_field( $_COOKIE['em_recently_viewed'] ) ) ) ) : array();
	if ( ! empty( $viewed_ids ) && class_exists( 'WooCommerce' ) ) :
	?>
	<section class="em-section">
		<div class="em-container">
			<div class="em-section-header">
				<h2 class="em-section-title">بازدیدهای اخیر شما</h2>
			</div>
			<div class="em-products-grid">
				<?php
				$viewed_query = new WP_Query( array(
					'post_type'      => 'product',
					'posts_per_page' => 4,
					'post__in'       => $viewed_ids,
					'orderby'        => 'post__in',
				) );
				while ( $viewed_query->have_posts() ) {
					$viewed_query->the_post();
					wc_get_template_part( 'content', 'product' );
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php endif; ?>

</main>

<?php get_footer(); ?>

