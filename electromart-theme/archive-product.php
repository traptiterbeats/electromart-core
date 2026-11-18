<?php
defined( 'ABSPATH' ) || exit;
get_header();

$em_current_cat = is_product_category() ? get_queried_object()->slug : '';
?>

<div class="em-container em-shop-layout">
	<!-- سایدبار فیلترهای زنده AJAX -->
	<aside class="em-shop-sidebar">
		<form id="em-ajax-filter-form" class="em-filter-box">
			<h3>فیلتر پیشرفته محصولات</h3>

			<div class="em-filter-group">
				<label for="em-filter-cat">دسته‌بندی محصول</label>
				<select name="category" id="em-filter-cat" class="em-filter-input">
					<option value="">همه دسته‌بندی‌ها</option>
					<?php
					$product_cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true ) );
					if ( ! is_wp_error( $product_cats ) ) {
						foreach ( $product_cats as $cat ) {
							echo '<option value="' . esc_attr( $cat->slug ) . '"' . selected( $em_current_cat, $cat->slug, false ) . '>' . esc_html( $cat->name ) . '</option>';
						}
					}
					?>
				</select>
			</div>

			<?php if ( taxonomy_exists( 'product_brand' ) ) : ?>
				<div class="em-filter-group">
					<label for="em-filter-brand">برند سازنده</label>
					<select name="brand" id="em-filter-brand" class="em-filter-input">
						<option value="">همه برندها</option>
						<?php
						$brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true ) );
						if ( ! is_wp_error( $brands ) ) {
							foreach ( $brands as $brand ) {
								echo '<option value="' . esc_attr( $brand->slug ) . '">' . esc_html( $brand->name ) . '</option>';
							}
						}
						?>
					</select>
				</div>
			<?php endif; ?>

			<div class="em-filter-group">
				<label for="em-filter-price">حداکثر قیمت (<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>)</label>
				<input type="number" id="em-filter-price" name="max_price" class="em-filter-input" placeholder="مثلاً 50000000" min="0" step="1000">
			</div>

			<div class="em-filter-group em-checkbox-row">
				<label>
					<input type="checkbox" name="in_stock" value="1">
					فقط کالاهای موجود در انبار
				</label>
			</div>

			<button type="submit" class="em-btn em-btn-primary em-btn-block">اعمال فیلتر</button>
		</form>
	</aside>

	<!-- لیست محصولات -->
	<section class="em-shop-main">
		<?php woocommerce_output_all_notices(); ?>

		<div class="em-shop-toolbar">
			<h1 class="em-page-title"><?php woocommerce_page_title(); ?></h1>
			<div class="em-sort-box">
				<label for="em-sort-select">مرتب‌سازی:</label>
				<select id="em-sort-select" class="em-filter-input">
					<option value="date">جدیدترین محصولات</option>
					<option value="popularity">پرفروش‌ترین‌ها</option>
					<option value="price">ارزان‌ترین به گران‌ترین</option>
					<option value="price-desc">گران‌ترین به ارزان‌ترین</option>
				</select>
			</div>
		</div>

		<div id="em-products-container" class="em-products-grid">
			<?php
			if ( woocommerce_product_loop() && have_posts() ) {
				while ( have_posts() ) {
					the_post();
					wc_get_template_part( 'content', 'product' );
				}
			} else {
				echo '<p class="em-no-results">محصولی یافت نشد.</p>';
			}
			?>
		</div>

		<div class="em-pagination" id="em-pagination">
			<?php woocommerce_pagination(); ?>
		</div>
	</section>
</div>

<?php
get_footer();
