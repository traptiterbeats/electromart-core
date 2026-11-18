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
 * بارگذاری بهینه فایل‌های CSS، فونت فارسی Vazirmatn و اسکریپت‌های تعاملی
 */
function electromart_scripts() {
	wp_enqueue_style( 'vazirmatn-font', 'https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css', array(), '33.003' );
	wp_enqueue_style( 'electromart-main', get_template_directory_uri() . '/assets/css/main.css', array( 'vazirmatn-font' ), '1.0.0' );

	// تزریق متغیرهای رنگی از پنل تنظیمات وردپرس
	$primary_color  = get_theme_mod( 'em_primary_color', '#0052FF' );
	$discount_color = get_theme_mod( 'em_discount_color', '#EF4444' );
	$grid_cols      = absint( get_theme_mod( 'em_products_per_row', 4 ) );

	$custom_css = "
		:root {
			--em-primary: " . esc_attr( $primary_color ) . ";
			--em-discount: " . esc_attr( $discount_color ) . ";
			--em-grid-cols: " . esc_attr( $grid_cols ) . ";
		}
	";
	wp_add_inline_style( 'electromart-main', $custom_css );

	wp_enqueue_script( 'electromart-js', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), '1.0.0', true );
	wp_localize_script( 'electromart-js', 'EM_Config', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'em_ajax_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'electromart_scripts' );

/**
 * بروزرسانی زنده شمارنده و محتوای Mini Cart کشویی با AJAX ووکامرس
 */
function electromart_cart_fragments( $fragments ) {
	ob_start();
	?>
	<span class="em-cart-count"><?php echo WC()->cart ? esc_html( WC()->cart->get_cart_contents_count() ) : 0; ?></span>
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

