<?php
/**
 * Plugin Name: ElectroMart Core
 * Description: افزونه هسته فروشگاه لوازم الکترونیکی: برندها، مشخصات فنی، مقایسه، علاقه‌مندی‌ها، جستجوی زنده، فیلتر AJAX، خبرنامه و درون‌ریزی محصولات نمونه.
 * Version: 1.1.0
 * Author: ElectroMart Dev
 * Text Domain: electromart-core
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EM_CORE_VERSION', '1.1.0' );
define( 'EM_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'EM_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once EM_CORE_DIR . 'includes/class-em-demo-import.php';

class ElectroMart_Core {

	/** برچسب‌های مشخصات فنی (یک منبع واحد برای متاباکس، صفحه محصول و جدول مقایسه) */
	public static function spec_labels() {
		return array(
			'model'        => 'مدل دقیق دستگاه',
			'processor'    => 'پردازنده / تراشه',
			'display'      => 'صفحه نمایش',
			'memory'       => 'حافظه رم / داخلی',
			'connectivity' => 'ارتباطات و پورت‌ها',
			'battery'      => 'باتری و توان مصرفی',
			'warranty'     => 'گارانتی و خدمات پس از فروش',
		);
	}

	public function __construct() {
		// اگر ووکامرس فعال نباشد، فقط یک اعلان نشان می‌دهیم.
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'notice_requires_wc' ) );
			return;
		}

		// اولویت ۲۰: بعد از ووکامرس، تا اگر برندهای داخلی ووکامرس (نسخه ۹.۶ به بعد) فعال است، دوباره ثبت نشود.
		add_action( 'init', array( $this, 'register_brand_taxonomy' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'register_tech_specs_metabox' ) );
		add_action( 'save_post_product', array( $this, 'save_tech_specs_metabox' ) );
		add_action( 'template_redirect', array( $this, 'track_recently_viewed' ) );
		add_shortcode( 'em_wishlist', array( $this, 'shortcode_wishlist' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// درخواست‌های فقط‌خواندنی: بدون nonce تا با کش صفحه (LiteSpeed / WP Rocket) از کار نیفتند.
		$public = array( 'em_live_search', 'em_quick_view', 'em_compare_table', 'em_filter_products', 'em_get_wishlist', 'em_newsletter' );
		foreach ( $public as $action ) {
			add_action( 'wp_ajax_' . $action, array( $this, 'ajax_' . substr( $action, 3 ) ) );
			add_action( 'wp_ajax_nopriv_' . $action, array( $this, 'ajax_' . substr( $action, 3 ) ) );
		}
		add_action( 'wp_ajax_em_toggle_wishlist', array( $this, 'ajax_toggle_wishlist' ) );
		add_action( 'wp_ajax_nopriv_em_toggle_wishlist', array( $this, 'ajax_toggle_wishlist' ) );

		new EM_Demo_Import();
	}

	public function notice_requires_wc() {
		echo '<div class="notice notice-error"><p>افزونه <strong>ElectroMart Core</strong> برای کار به ووکامرس نیاز دارد. لطفاً ووکامرس را نصب و فعال کنید.</p></div>';
	}

	/**
	 * 1. طبقه‌بندی برندها
	 */
	public function register_brand_taxonomy() {
		if ( taxonomy_exists( 'product_brand' ) ) {
			return; // برندهای داخلی ووکامرس فعال است؛ از همان استفاده می‌کنیم.
		}
		register_taxonomy( 'product_brand', array( 'product' ), array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'          => 'برندها',
				'singular_name' => 'برند',
				'search_items'  => 'جستجوی برند',
				'all_items'     => 'همه برندها',
				'edit_item'     => 'ویرایش برند',
				'update_item'   => 'بروزرسانی برند',
				'add_new_item'  => 'افزودن برند جدید',
				'new_item_name' => 'نام برند جدید',
				'menu_name'     => 'برندها',
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'brand' ),
			'show_in_rest'      => true,
		) );
	}

	/**
	 * 2. متاباکس مشخصات فنی
	 */
	public function register_tech_specs_metabox() {
		add_meta_box( 'em_tech_specs', 'مشخصات فنی تخصصی محصول (جدول مشخصات و مقایسه)', array( $this, 'render_tech_specs_metabox' ), 'product', 'normal', 'high' );
	}

	public function render_tech_specs_metabox( $post ) {
		wp_nonce_field( 'em_save_tech_specs', 'em_tech_specs_nonce' );
		$specs = get_post_meta( $post->ID, '_em_tech_specs', true );
		echo '<table class="form-table"><tbody>';
		foreach ( self::spec_labels() as $key => $label ) {
			$val = isset( $specs[ $key ] ) ? $specs[ $key ] : '';
			printf(
				'<tr><th><label for="em_spec_%1$s">%2$s</label></th><td><input type="text" id="em_spec_%1$s" name="em_specs[%1$s]" value="%3$s" class="regular-text" /></td></tr>',
				esc_attr( $key ),
				esc_html( $label ),
				esc_attr( $val )
			);
		}
		echo '</tbody></table>';
	}

	public function save_tech_specs_metabox( $post_id ) {
		if ( ! isset( $_POST['em_tech_specs_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['em_tech_specs_nonce'] ), 'em_save_tech_specs' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['em_specs'] ) && is_array( $_POST['em_specs'] ) ) {
			$allowed = array_keys( self::spec_labels() );
			$raw     = wp_unslash( $_POST['em_specs'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$clean   = array();
			foreach ( $allowed as $key ) {
				$clean[ $key ] = isset( $raw[ $key ] ) ? sanitize_text_field( $raw[ $key ] ) : '';
			}
			update_post_meta( $post_id, '_em_tech_specs', $clean );
		}
	}

	/**
	 * 3. محصولات اخیراً مشاهده‌شده (کوکی)
	 */
	public function track_recently_viewed() {
		if ( ! is_singular( 'product' ) || headers_sent() ) {
			return;
		}
		$id     = get_queried_object_id();
		$viewed = self::cookie_ids( 'em_recently_viewed' );
		$viewed = array_diff( $viewed, array( $id ) );
		array_unshift( $viewed, $id );
		$viewed = array_slice( $viewed, 0, 8 );
		setcookie( 'em_recently_viewed', implode( ',', $viewed ), time() + DAY_IN_SECONDS * 14, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
	}

	public static function cookie_ids( $name ) {
		if ( empty( $_COOKIE[ $name ] ) ) {
			return array();
		}
		$raw = sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) );
		return array_values( array_unique( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) ) );
	}

	/** tax_query که محصولات مخفی (و در صورت تنظیم، ناموجود) را حذف می‌کند */
	public static function visibility_tax_query( $context = 'catalog' ) {
		$terms   = wc_get_product_visibility_term_ids();
		$exclude = array( 'search' === $context ? $terms['exclude-from-search'] : $terms['exclude-from-catalog'] );
		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$exclude[] = $terms['outofstock'];
		}
		return array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => array_filter( $exclude ),
			'operator' => 'NOT IN',
		);
	}

	/**
	 * 4. جستجوی زنده
	 */
	public function ajax_live_search() {
		$keyword = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';
		if ( mb_strlen( $keyword ) < 2 ) {
			wp_send_json_success( array() );
		}

		$query = new WP_Query( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			's'              => $keyword,
			'posts_per_page' => 6,
			'no_found_rows'  => true,
			'tax_query'      => array( self::visibility_tax_query( 'search' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
		) );

		$results = array();
		foreach ( $query->posts as $post ) {
			$product = wc_get_product( $post );
			if ( ! $product ) {
				continue;
			}
			$results[] = array(
				'title' => wp_strip_all_tags( $product->get_name() ),
				'url'   => esc_url_raw( get_permalink( $product->get_id() ) ),
				'price' => wp_kses_post( $product->get_price_html() ),
				'image' => esc_url_raw( wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) ?: wc_placeholder_img_src() ),
			);
		}
		wp_send_json_success( $results );
	}

	/**
	 * 5. مشاهده سریع
	 */
	public function ajax_quick_view() {
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$product    = wc_get_product( $product_id );
		if ( ! $product || 'publish' !== $product->get_status() ) {
			wp_send_json_error();
		}

		$brand_name = self::brand_name( $product_id, 'الکترومارت' );
		$image_url  = wp_get_attachment_image_url( $product->get_image_id(), 'large' ) ?: wc_placeholder_img_src();

		ob_start();
		?>
		<div class="em-qv-grid">
			<div class="em-qv-image">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
			</div>
			<div class="em-qv-details">
				<span class="em-qv-brand"><?php echo esc_html( $brand_name ); ?></span>
				<h3 class="em-qv-title"><?php echo esc_html( $product->get_name() ); ?></h3>
				<div class="em-qv-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
				<div class="em-qv-stock <?php echo $product->is_in_stock() ? 'in-stock' : 'out-stock'; ?>">
					<?php echo $product->is_in_stock() ? 'موجود در انبار (آماده ارسال)' : 'ناموجود'; ?>
				</div>
				<div class="em-qv-excerpt">
					<?php echo wp_kses_post( $product->get_short_description() ?: wp_trim_words( $product->get_description(), 35 ) ); ?>
				</div>
				<div class="em-qv-actions">
					<?php if ( $product->is_in_stock() && $product->is_type( 'simple' ) && $product->is_purchasable() ) : ?>
						<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" class="em-btn em-btn-primary add_to_cart_button ajax_add_to_cart">افزودن به سبد خرید</a>
					<?php else : ?>
						<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="em-btn em-btn-primary">انتخاب گزینه و خرید</a>
					<?php endif; ?>
					<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="em-btn em-btn-outline">مشاهده صفحه کامل محصول</a>
				</div>
			</div>
		</div>
		<?php
		wp_send_json_success( array( 'html' => ob_get_clean() ) );
	}

	public static function brand_name( $product_id, $fallback = '' ) {
		$brands = taxonomy_exists( 'product_brand' ) ? wp_get_post_terms( $product_id, 'product_brand', array( 'fields' => 'names' ) ) : array();
		return ( ! empty( $brands ) && ! is_wp_error( $brands ) ) ? $brands[0] : $fallback;
	}

	/**
	 * 6. علاقه‌مندی‌ها
	 */
	public static function get_wishlist() {
		if ( is_user_logged_in() ) {
			$list = get_user_meta( get_current_user_id(), '_em_wishlist', true );
			return is_array( $list ) ? array_values( array_filter( array_map( 'absint', $list ) ) ) : array();
		}
		return self::cookie_ids( 'em_wishlist' );
	}

	public function ajax_get_wishlist() {
		wp_send_json_success( array( 'ids' => self::get_wishlist() ) );
	}

	public function ajax_toggle_wishlist() {
		// برای کاربران واردشده (که صفحه‌شان کش نمی‌شود) nonce بررسی می‌شود.
		if ( is_user_logged_in() ) {
			check_ajax_referer( 'em_ajax_nonce', 'nonce' );
		}
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
			wp_send_json_error();
		}

		$wishlist = self::get_wishlist();
		if ( in_array( $product_id, $wishlist, true ) ) {
			$wishlist = array_values( array_diff( $wishlist, array( $product_id ) ) );
			$status   = 'removed';
		} else {
			$wishlist[] = $product_id;
			$wishlist   = array_slice( $wishlist, -50 );
			$status     = 'added';
		}

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), '_em_wishlist', $wishlist );
		} else {
			setcookie( 'em_wishlist', implode( ',', $wishlist ), time() + DAY_IN_SECONDS * 30, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		}

		wp_send_json_success( array(
			'status' => $status,
			'count'  => count( $wishlist ),
			'ids'    => $wishlist,
		) );
	}

	public function shortcode_wishlist() {
		$ids = self::get_wishlist();
		ob_start();
		echo '<div class="em-wishlist-page">';
		if ( empty( $ids ) ) {
			echo '<p class="em-empty-msg">لیست علاقه‌مندی‌های شما خالی است.</p>';
		} else {
			$query = new WP_Query( array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'post__in'       => $ids,
				'orderby'        => 'post__in',
				'posts_per_page' => 50,
			) );
			echo '<div class="em-products-grid">';
			while ( $query->have_posts() ) {
				$query->the_post();
				wc_get_template_part( 'content', 'product' );
			}
			wp_reset_postdata();
			echo '</div>';
		}
		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * 7. جدول مقایسه
	 */
	public function ajax_compare_table() {
		$ids = isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ? array_map( 'absint', wp_unslash( $_POST['ids'] ) ) : array();
		$products = array();
		foreach ( array_slice( array_filter( $ids ), 0, 4 ) as $id ) {
			$p = wc_get_product( $id );
			if ( $p && 'publish' === $p->get_status() ) {
				$products[ $id ] = $p;
			}
		}

		if ( empty( $products ) ) {
			wp_send_json_success( array( 'html' => '<p class="em-empty-msg">هیچ محصولی برای مقایسه انتخاب نشده است.</p>' ) );
		}

		ob_start();
		?>
		<div class="em-compare-wrapper">
			<table class="em-compare-table">
				<thead>
					<tr>
						<th>ویژگی / محصول</th>
						<?php foreach ( $products as $id => $product ) : ?>
							<th>
								<div class="em-comp-prod-head">
									<button type="button" class="em-remove-compare" data-id="<?php echo esc_attr( $id ); ?>">× حذف</button>
									<img src="<?php echo esc_url( wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) ?: wc_placeholder_img_src() ); ?>" alt="">
									<a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
									<div class="em-comp-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
								</div>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>وضعیت موجودی</td>
						<?php foreach ( $products as $product ) : ?>
							<td><?php echo $product->is_in_stock() ? '<span class="in-stock">موجود</span>' : '<span class="out-stock">ناموجود</span>'; ?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td>برند</td>
						<?php foreach ( $products as $id => $product ) : ?>
							<td><?php echo esc_html( self::brand_name( $id, '—' ) ); ?></td>
						<?php endforeach; ?>
					</tr>
					<?php foreach ( self::spec_labels() as $s_key => $s_label ) : ?>
						<tr>
							<td><?php echo esc_html( $s_label ); ?></td>
							<?php foreach ( $products as $id => $product ) :
								$specs = get_post_meta( $id, '_em_tech_specs', true );
								$val   = ( is_array( $specs ) && ! empty( $specs[ $s_key ] ) ) ? $specs[ $s_key ] : '—';
								?>
								<td><?php echo esc_html( $val ); ?></td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
		wp_send_json_success( array( 'html' => ob_get_clean() ) );
	}

	/**
	 * 8. فیلتر و مرتب‌سازی AJAX با صفحه‌بندی
	 */
	public function ajax_filter_products() {
		$per_page = max( 3, min( 5, absint( get_theme_mod( 'em_products_per_row', 4 ) ) ) ) * 3;
		$paged    = isset( $_POST['paged'] ) ? max( 1, absint( $_POST['paged'] ) ) : 1;

		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $paged,
		);

		$tax_query  = array( 'relation' => 'AND', self::visibility_tax_query() );
		$meta_query = array( 'relation' => 'AND' );

		$category = isset( $_POST['category'] ) ? sanitize_title( wp_unslash( $_POST['category'] ) ) : '';
		if ( $category ) {
			$tax_query[] = array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $category );
		}

		$brand = isset( $_POST['brand'] ) ? sanitize_title( wp_unslash( $_POST['brand'] ) ) : '';
		if ( $brand && taxonomy_exists( 'product_brand' ) ) {
			$tax_query[] = array( 'taxonomy' => 'product_brand', 'field' => 'slug', 'terms' => $brand );
		}

		if ( isset( $_POST['in_stock'] ) && '1' === $_POST['in_stock'] ) {
			$meta_query[] = array( 'key' => '_stock_status', 'value' => 'instock' );
		}

		$max_price = isset( $_POST['max_price'] ) ? (float) wp_unslash( $_POST['max_price'] ) : 0;
		if ( $max_price > 0 ) {
			$meta_query[] = array( 'key' => '_price', 'value' => $max_price, 'compare' => '<=', 'type' => 'NUMERIC' );
		}

		$orderby = isset( $_POST['orderby'] ) ? sanitize_key( wp_unslash( $_POST['orderby'] ) ) : 'date';
		switch ( $orderby ) {
			case 'price':
			case 'price-desc':
				$args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'price' === $orderby ? 'ASC' : 'DESC';
				break;
			case 'popularity':
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
		}

		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
		if ( count( $meta_query ) > 1 ) {
			$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery
		}

		$query = new WP_Query( $args );
		ob_start();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				wc_get_template_part( 'content', 'product' );
			}
			wp_reset_postdata();
		} else {
			echo '<div class="em-no-results">محصولی با فیلترهای انتخابی یافت نشد.</div>';
		}
		$html = ob_get_clean();

		$pagination = '';
		if ( $query->max_num_pages > 1 ) {
			$pagination = '<nav class="woocommerce-pagination em-ajax-pagination"><ul class="page-numbers">';
			for ( $i = 1; $i <= $query->max_num_pages; $i++ ) {
				$pagination .= $i === $paged
					? '<li><span aria-current="page" class="page-numbers current">' . $i . '</span></li>'
					: '<li><a class="page-numbers" href="#" data-page="' . $i . '">' . $i . '</a></li>';
			}
			$pagination .= '</ul></nav>';
		}

		wp_send_json_success( array(
			'html'       => $html,
			'pagination' => $pagination,
			'found'      => (int) $query->found_posts,
		) );
	}

	/**
	 * 9. خبرنامه
	 */
	public function ajax_newsletter() {
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => 'لطفاً یک ایمیل معتبر وارد کنید.' ) );
		}
		$list = get_option( 'em_newsletter_subscribers', array() );
		$list = is_array( $list ) ? $list : array();
		if ( ! isset( $list[ $email ] ) ) {
			$list[ $email ] = current_time( 'mysql' );
			update_option( 'em_newsletter_subscribers', $list, false );
		}
		wp_send_json_success( array( 'message' => 'عضویت شما با موفقیت ثبت شد.' ) );
	}

	public function admin_menu() {
		add_submenu_page( 'woocommerce', 'مشترکین خبرنامه', 'مشترکین خبرنامه', 'manage_woocommerce', 'em-newsletter', array( $this, 'render_newsletter_page' ) );
	}

	public function render_newsletter_page() {
		$list = get_option( 'em_newsletter_subscribers', array() );
		echo '<div class="wrap"><h1>مشترکین خبرنامه</h1><table class="widefat striped"><thead><tr><th>ایمیل</th><th>تاریخ عضویت</th></tr></thead><tbody>';
		if ( empty( $list ) ) {
			echo '<tr><td colspan="2">هنوز مشترکی ثبت نشده است.</td></tr>';
		} else {
			foreach ( $list as $email => $date ) {
				echo '<tr><td>' . esc_html( $email ) . '</td><td>' . esc_html( $date ) . '</td></tr>';
			}
		}
		echo '</tbody></table></div>';
	}
}

add_action( 'plugins_loaded', function () {
	new ElectroMart_Core();
} );
