<?php
/**
 * Plugin Name: ElectroMart Core
 * Description: افزونه هسته و امکانات پیشرفته فروشگاه اینترنتی لوازم الکترونیکی (برندها، مقایسه، علاقه‌مندی‌ها، جستجوی زنده و فیلتر AJAX).
 * Version: 1.0.0
 * Author: ElectroMart Dev
 * Text Domain: electromart-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ElectroMart_Core {

	public function __construct() {
		add_action( 'init', array( $this, 'register_brand_taxonomy' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_tech_specs_metabox' ) );
		add_action( 'save_post_product', array( $this, 'save_tech_specs_metabox' ) );
		add_action( 'template_redirect', array( $this, 'track_recently_viewed' ) );

		// AJAX Endpoints
		add_action( 'wp_ajax_em_live_search', array( $this, 'ajax_live_search' ) );
		add_action( 'wp_ajax_nopriv_em_live_search', array( $this, 'ajax_live_search' ) );

		add_action( 'wp_ajax_em_quick_view', array( $this, 'ajax_quick_view' ) );
		add_action( 'wp_ajax_nopriv_em_quick_view', array( $this, 'ajax_quick_view' ) );

		add_action( 'wp_ajax_em_toggle_wishlist', array( $this, 'ajax_toggle_wishlist' ) );
		add_action( 'wp_ajax_nopriv_em_toggle_wishlist', array( $this, 'ajax_toggle_wishlist' ) );

		add_action( 'wp_ajax_em_compare_table', array( $this, 'ajax_compare_table' ) );
		add_action( 'wp_ajax_nopriv_em_compare_table', array( $this, 'ajax_compare_table' ) );

		add_action( 'wp_ajax_em_filter_products', array( $this, 'ajax_filter_products' ) );
		add_action( 'wp_ajax_nopriv_em_filter_products', array( $this, 'ajax_filter_products' ) );
	}

	/**
	 * 1. ثبت طبقه‌بندی برندها (Brands Taxonomy)
	 */
	public function register_brand_taxonomy() {
		$labels = array(
			'name'              => 'برندها',
			'singular_name'     => 'برند',
			'search_items'      => 'جستجوی برند',
			'all_items'         => 'همه برندها',
			'edit_item'         => 'ویرایش برند',
			'update_item'       => 'بروزرسانی برند',
			'add_new_item'      => 'افزودن برند جدید',
			'new_item_name'     => 'نام برند جدید',
			'menu_name'         => 'برندهای الکترونیکی',
		);

		register_taxonomy( 'product_brand', array( 'product' ), array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'brand' ),
			'show_in_rest'      => true,
		) );
	}

	/**
	 * 2. متاباکس مشخصات فنی تخصصی لوازم الکترونیکی
	 */
	public function register_tech_specs_metabox() {
		add_meta_box(
			'em_tech_specs',
			'مشخصات فنی تخصصی محصول (جدول مشخصات و مقایسه)',
			array( $this, 'render_tech_specs_metabox' ),
			'product',
			'normal',
			'high'
		);
	}

	public function render_tech_specs_metabox( $post ) {
		wp_nonce_field( 'em_save_tech_specs', 'em_tech_specs_nonce' );
		$specs = get_post_meta( $post->ID, '_em_tech_specs', true );
		$fields = array(
			'model'        => 'مدل دقیق دستگاه',
			'processor'    => 'پردازنده / تراشه',
			'display'      => 'صفحه نمایش / پنل',
			'memory'       => 'حافظه رم / داخلی',
			'connectivity' => 'ارتباطات و پورت‌ها',
			'battery'      => 'باتری و توان مصرفی',
			'warranty'     => 'گارانتی و خدمات پس از فروش',
		);
		echo '<table class="form-table"><tbody>';
		foreach ( $fields as $key => $label ) {
			$val = isset( $specs[ $key ] ) ? esc_attr( $specs[ $key ] ) : '';
			echo "<tr><th><label for='em_spec_{$key}'>{$label}</label></th>";
			echo "<td><input type='text' id='em_spec_{$key}' name='em_specs[{$key}]' value='{$val}' class='regular-text' /></td></tr>";
		}
		echo '</tbody></table>';
	}

	public function save_tech_specs_metabox( $post_id ) {
		if ( ! isset( $_POST['em_tech_specs_nonce'] ) || ! wp_verify_nonce( $_POST['em_tech_specs_nonce'], 'em_save_tech_specs' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( isset( $_POST['em_specs'] ) && is_array( $_POST['em_specs'] ) ) {
			$clean_specs = array_map( 'sanitize_text_field', wp_unslash( $_POST['em_specs'] ) );
			update_post_meta( $post_id, '_em_tech_specs', $clean_specs );
		}
	}

	/**
	 * 3. ردیابی محصولات اخیراً مشاهده شده با کوکی
	 */
	public function track_recently_viewed() {
		if ( ! is_singular( 'product' ) ) {
			return;
		}
		global $post;
		$viewed = isset( $_COOKIE['em_recently_viewed'] ) ? explode( ',', sanitize_text_field( $_COOKIE['em_recently_viewed'] ) ) : array();
		$viewed = array_diff( $viewed, array( $post->ID ) );
		array_unshift( $viewed, $post->ID );
		$viewed = array_slice( array_filter( array_map( 'absint', $viewed ) ), 0, 8 );
		setcookie( 'em_recently_viewed', implode( ',', $viewed ), time() + ( 86400 * 14 ), COOKIEPATH, COOKIE_DOMAIN );
	}

	/**
	 * 4. جستجوی زنده AJAX (Live Search)
	 */
	public function ajax_live_search() {
		check_ajax_referer( 'em_ajax_nonce', 'nonce' );
		$keyword = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';
		if ( mb_strlen( $keyword ) < 2 ) {
			wp_send_json_error();
		}

		$query = new WP_Query( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			's'              => $keyword,
			'posts_per_page' => 6,
		) );

		$results = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$product = wc_get_product( get_the_ID() );
				if ( ! $product ) continue;
				$results[] = array(
					'id'    => $product->get_id(),
					'title' => $product->get_name(),
					'url'   => get_permalink( $product->get_id() ),
					'price' => $product->get_price_html(),
					'image' => wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) ?: wc_placeholder_img_src(),
				);
			}
			wp_reset_postdata();
		}
		wp_send_json_success( $results );
	}

	/**
	 * 5. مشاهده سریع محصول (Quick View Modal)
	 */
	public function ajax_quick_view() {
		check_ajax_referer( 'em_ajax_nonce', 'nonce' );
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$product    = wc_get_product( $product_id );
		if ( ! $product ) {
			wp_send_json_error();
		}

		$brands     = wp_get_post_terms( $product_id, 'product_brand', array( 'fields' => 'names' ) );
		$brand_name = ! empty( $brands ) && ! is_wp_error( $brands ) ? $brands[0] : 'الکترومارت';
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
				<div class="em-qv-price"><?php echo $product->get_price_html(); ?></div>
				<div class="em-qv-stock <?php echo $product->is_in_stock() ? 'in-stock' : 'out-stock'; ?>">
					<?php echo $product->is_in_stock() ? 'موجود در انبار (آماده ارسال)' : 'ناموجود'; ?>
				</div>
				<div class="em-qv-excerpt">
					<?php echo wp_kses_post( $product->get_short_description() ?: wp_trim_words( $product->get_description(), 35 ) ); ?>
				</div>
				<div class="em-qv-actions">
					<?php if ( $product->is_in_stock() && $product->is_type( 'simple' ) ) : ?>
						<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" class="em-btn em-btn-primary add_to_cart_button ajax_add_to_cart">افزودن به سبد خرید</a>
					<?php else : ?>
						<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="em-btn em-btn-primary">انتخاب گزینه و خرید</a>
					<?php endif; ?>
					<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="em-btn em-btn-outline">مشاهده صفحه کامل محصول</a>
				</div>
			</div>
		</div>
		<?php
		$html = ob_get_clean();
		wp_send_json_success( array( 'html' => $html ) );
	}

	/**
	 * 6. علاقه‌مندی‌ها (پشتیبانی همزمان از کاربران واردشده و کاربران مهمان)
	 */
	public function ajax_toggle_wishlist() {
		check_ajax_referer( 'em_ajax_nonce', 'nonce' );
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		if ( ! $product_id ) {
			wp_send_json_error();
		}

		$status = 'added';
		if ( is_user_logged_in() ) {
			$user_id  = get_current_user_id();
			$wishlist = get_user_meta( $user_id, '_em_wishlist', true );
			$wishlist = is_array( $wishlist ) ? $wishlist : array();

			if ( in_array( $product_id, $wishlist, true ) ) {
				$wishlist = array_diff( $wishlist, array( $product_id ) );
				$status   = 'removed';
			} else {
				$wishlist[] = $product_id;
			}
			update_user_meta( $user_id, '_em_wishlist', array_values( $wishlist ) );
			$count = count( $wishlist );
		} else {
			$cookie   = isset( $_COOKIE['em_wishlist'] ) ? explode( ',', sanitize_text_field( $_COOKIE['em_wishlist'] ) ) : array();
			$wishlist = array_filter( array_map( 'absint', $cookie ) );
			if ( in_array( $product_id, $wishlist, true ) ) {
				$wishlist = array_diff( $wishlist, array( $product_id ) );
				$status   = 'removed';
			} else {
				$wishlist[] = $product_id;
			}
			setcookie( 'em_wishlist', implode( ',', $wishlist ), time() + ( 86400 * 30 ), COOKIEPATH, COOKIE_DOMAIN );
			$count = count( $wishlist );
		}

		wp_send_json_success( array(
			'status' => $status,
			'count'  => $count,
		) );
	}

	/**
	 * 7. جدول مقایسه مشخصات فنی محصولات (Compare Table)
	 */
	public function ajax_compare_table() {
		check_ajax_referer( 'em_ajax_nonce', 'nonce' );
		$ids = isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ? array_map( 'absint', $_POST['ids'] ) : array();
		$ids = array_filter( array_slice( $ids, 0, 4 ) );

		if ( empty( $ids ) ) {
			wp_send_json_success( array( 'html' => '<p class="em-empty-msg">هیچ محصولی برای مقایسه انتخاب نشده است.</p>' ) );
		}

		$spec_labels = array(
			'model'        => 'مدل دستگاه',
			'processor'    => 'پردازنده / تراشه',
			'display'      => 'صفحه نمایش',
			'memory'       => 'حافظه',
			'connectivity' => 'ارتباطات',
			'battery'      => 'باتری و تغذیه',
			'warranty'     => 'گارانتی',
		);

		ob_start();
		?>
		<div class="em-compare-wrapper">
			<table class="em-compare-table">
				<thead>
					<tr>
						<th>ویژگی / محصول</th>
						<?php foreach ( $ids as $id ) : $product = wc_get_product( $id ); if ( ! $product ) continue; ?>
							<th>
								<div class="em-comp-prod-head">
									<button type="button" class="em-remove-compare" data-id="<?php echo esc_attr( $id ); ?>">× حذف</button>
									<img src="<?php echo esc_url( wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) ?: wc_placeholder_img_src() ); ?>" alt="">
									<a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
									<div class="em-comp-price"><?php echo $product->get_price_html(); ?></div>
								</div>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>وضعیت موجودی</td>
						<?php foreach ( $ids as $id ) : $product = wc_get_product( $id ); if ( ! $product ) continue; ?>
							<td><?php echo $product->is_in_stock() ? '<span class="in-stock">موجود</span>' : '<span class="out-stock">ناموجود</span>'; ?></td>
						<?php endforeach; ?>
					</tr>
					<?php foreach ( $spec_labels as $s_key => $s_label ) : ?>
						<tr>
							<td><?php echo esc_html( $s_label ); ?></td>
							<?php foreach ( $ids as $id ) :
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
		$html = ob_get_clean();
		wp_send_json_success( array( 'html' => $html ) );
	}

	/**
	 * 8. فیلتر و مرتب‌سازی زنده AJAX در صفحات فروشگاه
	 */
	public function ajax_filter_products() {
		check_ajax_referer( 'em_ajax_nonce', 'nonce' );

		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => get_theme_mod( 'em_products_per_row', 4 ) * 3,
		);

		$tax_query  = array( 'relation' => 'AND' );
		$meta_query = array( 'relation' => 'AND' );

		if ( ! empty( $_POST['category'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( wp_unslash( $_POST['category'] ) ),
			);
		}

		if ( ! empty( $_POST['brand'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_brand',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( wp_unslash( $_POST['brand'] ) ),
			);
		}

		if ( ! empty( $_POST['in_stock'] ) && '1' === $_POST['in_stock'] ) {
			$meta_query[] = array(
				'key'     => '_stock_status',
				'value'   => 'instock',
				'compare' => '=',
			);
		}

		if ( isset( $_POST['max_price'] ) && is_numeric( $_POST['max_price'] ) && $_POST['max_price'] > 0 ) {
			$meta_query[] = array(
				'key'     => '_price',
				'value'   => floatval( $_POST['max_price'] ),
				'compare' => '<=',
				'type'    => 'NUMERIC',
			);
		}

		$orderby = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'date';
		switch ( $orderby ) {
			case 'price':
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'price-desc':
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'popularity':
				$args['meta_key'] = 'total_sales';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
		}

		if ( count( $tax_query ) > 1 ) {
			$args['tax_query'] = $tax_query;
		}
		if ( count( $meta_query ) > 1 ) {
			$args['meta_query'] = $meta_query;
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
		wp_send_json_success( array( 'html' => $html ) );
	}
}

new ElectroMart_Core();

