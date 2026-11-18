<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * راه‌اندازی اولیه قالب و پشتیبانی از ووکامرس
 */
function electromart_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 48,
		'width'       => 180,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	// پشتیبانی کامل از WooCommerce و گالری بزرگ‌نمایی محصول
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => 'منوی اصلی و مگامنو',
		'footer'  => 'منوی فوتر',
	) );
}
add_action( 'after_setup_theme', 'electromart_setup' );

/**
 * آدرس تصاویر داخلی قالب
 */
function electromart_img( $name ) {
	return get_template_directory_uri() . '/assets/images/' . $name;
}

/**
 * بارگذاری فایل‌های CSS، فونت Vazirmatn و اسکریپت‌ها
 */
function electromart_scripts() {
	$ver = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'vazirmatn-font', 'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css', array(), '33.003' );
	wp_enqueue_style( 'electromart-style', get_stylesheet_uri(), array(), $ver );
	wp_style_add_data( 'electromart-style', 'rtl', 'replace' );
	wp_enqueue_style( 'electromart-main', get_template_directory_uri() . '/assets/css/main.css', array( 'vazirmatn-font', 'electromart-style' ), $ver );

	$primary_color  = sanitize_hex_color( get_theme_mod( 'em_primary_color', '#0052FF' ) ) ?: '#0052FF';
	$discount_color = sanitize_hex_color( get_theme_mod( 'em_discount_color', '#EF4444' ) ) ?: '#EF4444';
	$grid_cols      = max( 3, min( 5, absint( get_theme_mod( 'em_products_per_row', 4 ) ) ) );

	wp_add_inline_style( 'electromart-main', ":root{--em-primary:{$primary_color};--em-discount:{$discount_color};--em-grid-cols:{$grid_cols};}" );

	wp_enqueue_script( 'electromart-js', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), $ver, true );
	wp_localize_script( 'electromart-js', 'EM_Config', array(
		'ajax_url'  => admin_url( 'admin-ajax.php' ),
		'nonce'     => is_user_logged_in() ? wp_create_nonce( 'em_ajax_nonce' ) : '',
		'category'  => ( function_exists( 'is_product_category' ) && is_product_category() ) ? get_queried_object()->slug : '',
		'i18n'      => array(
			'noResults' => 'محصولی یافت نشد.',
			'loading'   => 'در حال بارگذاری...',
		),
	) );
}
add_action( 'wp_enqueue_scripts', 'electromart_scripts' );

/**
 * کوئری محصولات صفحه اصلی با رعایت تنظیمات نمایش ووکامرس (مخفی / ناموجود)
 */
function electromart_product_query( $args ) {
	$defaults = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'no_found_rows'  => true,
	);
	$args = wp_parse_args( $args, $defaults );
	if ( class_exists( 'ElectroMart_Core' ) ) {
		$args['tax_query'] = array( ElectroMart_Core::visibility_tax_query() ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	return new WP_Query( $args );
}

function electromart_render_products( $args ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	$query = electromart_product_query( $args );
	while ( $query->have_posts() ) {
		$query->the_post();
		wc_get_template_part( 'content', 'product' );
	}
	wp_reset_postdata();
}

/** لینک یک دسته‌بندی محصول بر اساس نامک؛ اگر نبود، فروشگاه */
function electromart_cat_link( $slug ) {
	if ( taxonomy_exists( 'product_cat' ) ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term ) {
			return get_term_link( $term );
		}
	}
	return electromart_shop_url();
}

function electromart_shop_url() {
	return class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

function electromart_wishlist_url() {
	$id = (int) get_option( 'em_wishlist_page_id' );
	return $id ? get_permalink( $id ) : electromart_shop_url();
}

/**
 * منوی پیش‌فرض وقتی هنوز منویی به جایگاه «اصلی» اختصاص داده نشده
 */
function electromart_primary_fallback() {
	echo '<li><a href="' . esc_url( electromart_shop_url() ) . '">فروشگاه</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/#em-special-deals' ) ) . '">تخفیف‌های ویژه</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/#em-brands-section' ) ) . '">برندها</a></li>';
}

function electromart_footer_fallback() {
	$cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 4, 'parent' => 0, 'exclude' => get_option( 'default_product_cat' ) ) );
	echo '<ul>';
	if ( ! is_wp_error( $cats ) ) {
		foreach ( $cats as $cat ) {
			echo '<li><a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
		}
	}
	echo '</ul>';
}

/**
 * بروزرسانی زنده شمارنده و محتوای Mini Cart کشویی با AJAX ووکامرس
 */
function electromart_cart_fragments( $fragments ) {
	ob_start();
	?>
	<span class="em-badge-count em-cart-count"><?php echo WC()->cart ? esc_html( WC()->cart->get_cart_contents_count() ) : 0; ?></span>
	<?php
	$fragments['span.em-cart-count'] = ob_get_clean();

	ob_start();
	?>
	<div class="em-mini-cart-body">
		<?php woocommerce_mini_cart(); ?>
	</div>
	<?php
	$fragments['div.em-mini-cart-body'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'electromart_cart_fragments' );

/**
 * پنل تنظیمات اختصاصی قالب در WordPress Customizer
 */
function electromart_customize_register( $wp_customize ) {
	$wp_customize->add_panel( 'em_theme_panel', array(
		'title'    => 'تنظیمات اختصاصی قالب ElectroMart',
		'priority' => 10,
	) );

	// 1. رنگ‌ها و هویت بصری
	$wp_customize->add_section( 'em_colors_section', array(
		'title' => 'رنگ‌ها و استایل',
		'panel' => 'em_theme_panel',
	) );

	$wp_customize->add_setting( 'em_primary_color', array(
		'default'           => '#0052FF',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'em_primary_color', array(
		'label'   => 'رنگ اصلی (آبی الکتریکی)',
		'section' => 'em_colors_section',
	) ) );

	$wp_customize->add_setting( 'em_discount_color', array(
		'default'           => '#EF4444',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'em_discount_color', array(
		'label'   => 'رنگ نشان‌های تخفیف',
		'section' => 'em_colors_section',
	) ) );

	// 2. تنظیمات هدر و نوار اطلاع‌رسانی بالا
	$wp_customize->add_section( 'em_header_section', array(
		'title' => 'نوار اطلاع‌رسانی و بنر اصلی (Hero)',
		'panel' => 'em_theme_panel',
	) );

	$wp_customize->add_setting( 'em_topbar_text', array(
		'default'           => 'فروش ویژه محصولات دیجیتال و گجت‌های هوشمند با ۳۰٪ تخفیف ویژه زمان محدود!',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'em_topbar_text', array(
		'label'   => 'متن نوار اطلاع‌رسانی بالای سایت',
		'section' => 'em_header_section',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'em_hero_title', array(
		'default'           => 'فراتر از مرزهای تکنولوژی و صدا.',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'em_hero_title', array(
		'label'   => 'تیتر اصلی بنر Hero',
		'section' => 'em_header_section',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'em_hero_subtitle', array(
		'default'           => 'تجربه نسل جدید لپ‌تاپ‌ها، موبایل، تجهیزات صوتی و گیمینگ با ضمانت اصالت کالا',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'em_hero_subtitle', array(
		'label'   => 'زیرتیتر بنر Hero',
		'section' => 'em_header_section',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'em_hero_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		// خالی = استفاده از تصویر پیش‌فرض داخل قالب
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'em_hero_image', array(
		'label'   => 'تصویر شاخص بنر Hero',
		'section' => 'em_header_section',
	) ) );

	// 3. شبکه محصولات و امکانات کارت
	$wp_customize->add_section( 'em_catalog_section', array(
		'title' => 'تنظیمات کارت محصول و شبکه نمایش',
		'panel' => 'em_theme_panel',
	) );

	$wp_customize->add_setting( 'em_products_per_row', array(
		'default'           => 4,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'em_products_per_row', array(
		'label'   => 'تعداد محصولات در هر ردیف (دسکتاپ)',
		'section' => 'em_catalog_section',
		'type'    => 'number',
		'input_attrs' => array( 'min' => 3, 'max' => 5 ),
	) );

	$wp_customize->add_setting( 'em_enable_quickview', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	) );
	$wp_customize->add_control( 'em_enable_quickview', array(
		'label'   => 'فعال‌سازی مشاهده سریع (Quick View)',
		'section' => 'em_catalog_section',
		'type'    => 'checkbox',
	) );
}
add_action( 'customize_register', 'electromart_customize_register' );

