<?php
/**
 * درون‌ریزی یک‌کلیکی محصولات نمونه، دسته‌بندی‌ها، برندها، برگه‌ها و منوها.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EM_Demo_Import {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_post_em_demo_import', array( $this, 'handle' ) );
		add_action( 'admin_notices', array( $this, 'notice' ) );
	}

	public function menu() {
		add_management_page( 'درون‌ریزی دموی الکترومارت', 'دموی الکترومارت', 'manage_options', 'em-demo-import', array( $this, 'page' ) );
	}

	public function notice() {
		if ( get_option( 'em_demo_imported' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && 'tools_page_em-demo-import' === $screen->id ) {
			return;
		}
		echo '<div class="notice notice-info"><p>برای ساخت محصولات نمونه با تصویر، صفحه اصلی و منوهای الکترومارت به <a href="' . esc_url( admin_url( 'tools.php?page=em-demo-import' ) ) . '">ابزارها › دموی الکترومارت</a> بروید.</p></div>';
	}

	public function page() {
		$done = get_option( 'em_demo_imported' );
		?>
		<div class="wrap">
			<h1>درون‌ریزی دموی الکترومارت</h1>
			<p>این ابزار ۷ دسته‌بندی، ۸ برند، ۱۲ محصول نمونه با تصویر و مشخصات فنی، برگه‌های «صفحه اصلی» و «علاقه‌مندی‌ها» و منوهای اصلی و فوتر را می‌سازد و صفحه اصلی را تنظیم می‌کند. اجرای دوباره، موارد تکراری نمی‌سازد.</p>
			<?php if ( isset( $_GET['imported'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
				<div class="notice notice-success"><p>دمو با موفقیت درون‌ریزی شد. <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">مشاهده سایت</a></p></div>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="em_demo_import">
				<?php wp_nonce_field( 'em_demo_import' ); ?>
				<?php submit_button( $done ? 'اجرای دوباره درون‌ریزی' : 'درون‌ریزی دمو' ); ?>
			</form>
		</div>
		<?php
	}

	public function handle() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'دسترسی غیرمجاز' );
		}
		check_admin_referer( 'em_demo_import' );
		self::run();
		wp_safe_redirect( admin_url( 'tools.php?page=em-demo-import&imported=1' ) );
		exit;
	}

	public static function categories() {
		return array(
			'mobile-tablet'   => array( 'موبایل و تبلت', 'phone' ),
			'laptop-computer' => array( 'لپ‌تاپ و کامپیوتر', 'laptop' ),
			'audio'           => array( 'هدفون و صوتی', 'headphones' ),
			'smartwatch'      => array( 'ساعت هوشمند', 'watch' ),
			'gaming'          => array( 'کنسول و گیمینگ', 'console' ),
			'camera'          => array( 'دوربین و عکاسی', 'camera' ),
			'network'         => array( 'شبکه و مودم', 'router' ),
		);
	}

	public static function products() {
		return array(
			array( 'EM-001', 'لپ‌تاپ ایسوس Zenbook 14 OLED', 'laptop-computer', 'ASUS', 68500000, 62900000, 'laptop', 'instock', 4.8, 142,
				array( 'model' => 'UX3405MA', 'processor' => 'Intel Core Ultra 7 155H', 'display' => '۱۴ اینچ OLED 3K، ۱۲۰ هرتز', 'memory' => '۱۶ گیگ رم / ۱ ترابایت SSD', 'connectivity' => 'Wi-Fi 6E، دو Thunderbolt 4، HDMI', 'battery' => '۷۵ وات‌ساعت، شارژ سریع ۶۵ وات', 'warranty' => '۱۸ ماه گارانتی' ),
				'اولترابوک سبک ۱.۲ کیلوگرمی با نمایشگر OLED خیره‌کننده و پردازنده هوش مصنوعی اینتل.' ),
			array( 'EM-002', 'گوشی سامسونگ Galaxy S25 Ultra ۵۱۲ گیگ', 'mobile-tablet', 'Samsung', 89000000, 0, 'phone', 'instock', 4.9, 318,
				array( 'model' => 'SM-S938B', 'processor' => 'Snapdragon 8 Elite for Galaxy', 'display' => '۶.۹ اینچ Dynamic AMOLED 2X', 'memory' => '۱۲ گیگ رم / ۵۱۲ گیگ', 'connectivity' => '5G، Wi-Fi 7، بلوتوث 5.4', 'battery' => '۵۰۰۰ میلی‌آمپر، شارژ ۴۵ وات', 'warranty' => '۱۸ ماه گارانتی' ),
				'پرچمدار با دوربین ۲۰۰ مگاپیکسلی، قلم S Pen و بدنه تیتانیومی.' ),
			array( 'EM-003', 'هدفون بی‌سیم سونی WH-1000XM5', 'audio', 'Sony', 21500000, 18900000, 'headphones', 'instock', 4.7, 265,
				array( 'model' => 'WH-1000XM5', 'processor' => 'تراشه QN1 + V1', 'display' => '—', 'memory' => '—', 'connectivity' => 'بلوتوث 5.2، LDAC، چندنقطه‌ای', 'battery' => '۳۰ ساعت پخش با ANC', 'warranty' => '۱۲ ماه گارانتی' ),
				'بهترین حذف نویز کلاس خود با صدای شفاف و راحتی کامل برای استفاده طولانی.' ),
			array( 'EM-004', 'ساعت هوشمند اپل Watch Series 10', 'smartwatch', 'Apple', 32000000, 0, 'watch', 'instock', 4.6, 97,
				array( 'model' => 'Series 10 GPS ۴۶ میلی‌متر', 'processor' => 'Apple S10 SiP', 'display' => 'OLED همیشه روشن LTPO3', 'memory' => '۶۴ گیگ', 'connectivity' => 'بلوتوث 5.3، Wi-Fi 4، NFC', 'battery' => '۱۸ ساعت', 'warranty' => '۱۲ ماه گارانتی' ),
				'باریک‌ترین اپل واچ با نمایشگر بزرگ‌تر و پایش پیشرفته سلامت و خواب.' ),
			array( 'EM-005', 'کنسول بازی PlayStation 5 Slim', 'gaming', 'Sony', 39800000, 36500000, 'console', 'instock', 4.9, 421,
				array( 'model' => 'CFI-2016 نسخه دیسک‌خور', 'processor' => 'AMD Zen 2 هشت‌هسته‌ای + RDNA 2', 'display' => 'خروجی 4K تا ۱۲۰ فریم', 'memory' => '۱ ترابایت SSD', 'connectivity' => 'Wi-Fi 6، USB-C، HDMI 2.1', 'battery' => '—', 'warranty' => '۱۲ ماه گارانتی' ),
				'نسل نهم کنسول سونی در بدنه‌ای کوچک‌تر با دسته DualSense.' ),
			array( 'EM-006', 'دوربین بدون آینه سونی Alpha 7 IV', 'camera', 'Sony', 165000000, 0, 'camera', 'instock', 4.8, 58,
				array( 'model' => 'ILCE-7M4 + لنز ۲۸-۷۰', 'processor' => 'BIONZ XR', 'display' => 'LCD لمسی چرخان ۳ اینچ', 'memory' => 'دو اسلات SD / CFexpress A', 'connectivity' => 'Wi-Fi، USB-C، HDMI کامل', 'battery' => 'NP-FZ100، حدود ۵۸۰ عکس', 'warranty' => '۱۸ ماه گارانتی' ),
				'سنسور فول‌فریم ۳۳ مگاپیکسلی و فیلم‌برداری 4K 60p برای حرفه‌ای‌ها.' ),
			array( 'EM-007', 'اسپیکر بلوتوثی JBL Charge 5', 'audio', 'JBL', 8900000, 7450000, 'speaker', 'instock', 4.5, 204,
				array( 'model' => 'Charge 5', 'processor' => '—', 'display' => '—', 'memory' => '—', 'connectivity' => 'بلوتوث 5.1، PartyBoost', 'battery' => '۲۰ ساعت پخش + پاوربانک', 'warranty' => '۱۲ ماه گارانتی' ),
				'صدای قدرتمند و ضدآب IP67 برای سفر، طبیعت و مهمانی.' ),
			array( 'EM-008', 'مانیتور گیمینگ ال‌جی UltraGear ۲۷ اینچ', 'laptop-computer', 'LG', 42000000, 0, 'monitor', 'instock', 4.6, 76,
				array( 'model' => '27GR95QE', 'processor' => '—', 'display' => '۲۷ اینچ OLED QHD، ۲۴۰ هرتز، ۰.۰۳ میلی‌ثانیه', 'memory' => '—', 'connectivity' => 'HDMI 2.1 ×۲، DisplayPort 1.4، USB', 'battery' => '—', 'warranty' => '۲۴ ماه گارانتی' ),
				'رنگ‌های واقعی OLED و سرعت حرفه‌ای برای بازی‌های رقابتی.' ),
			array( 'EM-009', 'هندزفری بی‌سیم اپل AirPods Pro 2', 'audio', 'Apple', 14500000, 0, 'earbuds', 'instock', 4.7, 389,
				array( 'model' => 'AirPods Pro 2 USB-C', 'processor' => 'Apple H2', 'display' => '—', 'memory' => '—', 'connectivity' => 'بلوتوث 5.3', 'battery' => '۶ ساعت (۳۰ ساعت با کیس)', 'warranty' => '۱۲ ماه گارانتی' ),
				'حذف نویز فعال دو برابر قوی‌تر و صدای فضایی شخصی‌سازی‌شده.' ),
			array( 'EM-010', 'تبلت شیائومی Pad 6 با قلم', 'mobile-tablet', 'Xiaomi', 19800000, 17900000, 'tablet', 'instock', 4.4, 133,
				array( 'model' => 'Pad 6 ۸/۲۵۶', 'processor' => 'Snapdragon 870', 'display' => '۱۱ اینچ 2.8K، ۱۴۴ هرتز', 'memory' => '۸ گیگ رم / ۲۵۶ گیگ', 'connectivity' => 'Wi-Fi 6، بلوتوث 5.2، USB-C', 'battery' => '۸۸۴۰ میلی‌آمپر، شارژ ۳۳ وات', 'warranty' => '۱۸ ماه گارانتی' ),
				'تبلت سریع و سبک با نمایشگر ۱۴۴ هرتز برای کار، درس و سرگرمی.' ),
			array( 'EM-011', 'ماوس گیمینگ لاجیتک G Pro X Superlight 2', 'gaming', 'Logitech', 9700000, 0, 'mouse', 'instock', 4.8, 87,
				array( 'model' => 'G Pro X Superlight 2', 'processor' => 'سنسور HERO 2، ۳۲۰۰۰ DPI', 'display' => '—', 'memory' => 'حافظه داخلی پروفایل', 'connectivity' => 'LIGHTSPEED بی‌سیم، USB-C', 'battery' => '۹۵ ساعت', 'warranty' => '۲۴ ماه گارانتی' ),
				'ماوس ۶۰ گرمی محبوب گیمرهای حرفه‌ای با کلیدهای هیبریدی.' ),
			array( 'EM-012', 'مودم روتر ایسوس RT-AX58U Wi-Fi 6', 'network', 'ASUS', 11200000, 0, 'router', 'outofstock', 4.5, 41,
				array( 'model' => 'RT-AX58U', 'processor' => 'پردازنده سه‌هسته‌ای ۱.۵ گیگاهرتز', 'display' => '—', 'memory' => '۵۱۲ مگ رم / ۲۵۶ مگ فلش', 'connectivity' => 'Wi-Fi 6 AX3000، ۴ پورت گیگابیت، USB 3.0', 'battery' => '—', 'warranty' => '۲۴ ماه گارانتی' ),
				'اینترنت پایدار و پرسرعت برای کل خانه با پشتیبانی از AiMesh.' ),
		);
	}

	/** ساخت تصویر در کتابخانه رسانه از فایل‌های داخل افزونه */
	private static function attach_image( $name, $title ) {
		$existing = get_posts( array(
			'post_type'   => 'attachment',
			'meta_key'    => '_em_demo_image', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $name, // phpcs:ignore WordPress.DB.SlowDBQuery
			'numberposts' => 1,
			'fields'      => 'ids',
		) );
		if ( $existing ) {
			return (int) $existing[0];
		}

		$src = EM_CORE_DIR . 'demo/images/' . $name . '.jpg';
		if ( ! file_exists( $src ) ) {
			return 0;
		}
		$upload = wp_upload_bits( 'electromart-' . $name . '.jpg', null, file_get_contents( $src ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( ! empty( $upload['error'] ) ) {
			return 0;
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_id = wp_insert_attachment( array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $title,
			'post_status'    => 'inherit',
		), $upload['file'] );
		wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $upload['file'] ) );
		update_post_meta( $attach_id, '_em_demo_image', $name );
		update_post_meta( $attach_id, '_wp_attachment_image_alt', $title );
		return $attach_id;
	}

	private static function term( $name, $taxonomy, $slug = '' ) {
		$found = get_term_by( $slug ? 'slug' : 'name', $slug ? $slug : $name, $taxonomy );
		if ( $found ) {
			return (int) $found->term_id;
		}
		$res = wp_insert_term( $name, $taxonomy, $slug ? array( 'slug' => $slug ) : array() );
		return is_wp_error( $res ) ? 0 : (int) $res['term_id'];
	}

	private static function ensure_page( $title, $slug, $content = '' ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			return $page->ID;
		}
		return wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		) );
	}

	public static function run() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		}

		// دسته‌بندی‌ها با تصویر بندانگشتی
		$cat_ids = array();
		foreach ( self::categories() as $slug => $data ) {
			$cat_ids[ $slug ] = self::term( $data[0], 'product_cat', $slug );
			if ( $cat_ids[ $slug ] && ! get_term_meta( $cat_ids[ $slug ], 'thumbnail_id', true ) ) {
				update_term_meta( $cat_ids[ $slug ], 'thumbnail_id', self::attach_image( $data[1], $data[0] ) );
			}
		}

		$has_brands = taxonomy_exists( 'product_brand' );

		foreach ( self::products() as $p ) {
			list( $sku, $name, $cat, $brand, $regular, $sale, $img, $stock, $rating, $sales, $specs, $short ) = $p;

			$product_id = wc_get_product_id_by_sku( $sku );
			$product    = $product_id ? wc_get_product( $product_id ) : new WC_Product_Simple();

			$product->set_name( $name );
			$product->set_sku( $sku );
			$product->set_status( 'publish' );
			$product->set_catalog_visibility( 'visible' );
			$product->set_regular_price( (string) $regular );
			$product->set_sale_price( $sale ? (string) $sale : '' );
			$product->set_stock_status( $stock );
			$product->set_short_description( $short );
			$product->set_description( '<p>' . $short . '</p><p>این محصول نمونه توسط درون‌ریز دموی الکترومارت ساخته شده است. مشخصات کامل را در تب «مشخصات فنی» ببینید.</p>' );
			$product->set_category_ids( array_filter( array( $cat_ids[ $cat ] ?? 0 ) ) );
			$product->set_image_id( self::attach_image( $img, $name ) );
			$product->set_featured( in_array( $sku, array( 'EM-002', 'EM-005', 'EM-008' ), true ) );
			$product->set_average_rating( $rating );
			$product->set_rating_counts( array( (int) round( $rating ) => 10 ) );
			$product->set_review_count( 10 );
			$product->set_total_sales( $sales );
			$product_id = $product->save();

			update_post_meta( $product_id, '_em_tech_specs', $specs );
			if ( $has_brands ) {
				wp_set_object_terms( $product_id, array( self::term( $brand, 'product_brand' ) ), 'product_brand' );
			}
		}

		// برگه‌ها
		$home     = self::ensure_page( 'صفحه اصلی', 'home' );
		$wishlist = self::ensure_page( 'علاقه‌مندی‌ها', 'wishlist', '[em_wishlist]' );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
		update_option( 'em_wishlist_page_id', $wishlist );

		// منوها
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$primary   = wp_get_nav_menu_object( 'منوی اصلی الکترومارت' );
		if ( ! $primary ) {
			$menu_id = wp_create_nav_menu( 'منوی اصلی الکترومارت' );
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'صفحه اصلی', 'menu-item-url' => home_url( '/' ), 'menu-item-status' => 'publish' ) );
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'فروشگاه', 'menu-item-object' => 'page', 'menu-item-object-id' => wc_get_page_id( 'shop' ), 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'تخفیف‌های ویژه', 'menu-item-url' => home_url( '/#em-special-deals' ), 'menu-item-status' => 'publish' ) );
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'علاقه‌مندی‌ها', 'menu-item-object' => 'page', 'menu-item-object-id' => $wishlist, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
			$locations['primary'] = $menu_id;
		}
		$footer = wp_get_nav_menu_object( 'منوی فوتر الکترومارت' );
		if ( ! $footer ) {
			$menu_id = wp_create_nav_menu( 'منوی فوتر الکترومارت' );
			foreach ( array_slice( $cat_ids, 0, 4 ) as $term_id ) {
				wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-object' => 'product_cat', 'menu-item-object-id' => $term_id, 'menu-item-type' => 'taxonomy', 'menu-item-status' => 'publish' ) );
			}
			$locations['footer'] = $menu_id;
		}
		set_theme_mod( 'nav_menu_locations', $locations );

		// قیمت‌ها بدون اعشار (تومان)
		update_option( 'woocommerce_price_num_decimals', 0 );

		update_option( 'em_demo_imported', time() );
		flush_rewrite_rules();
		return true;
	}
}
