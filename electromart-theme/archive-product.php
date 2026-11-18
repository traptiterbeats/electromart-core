<?php get_header(); ?>

<div class="em-container em-shop-layout">
	<!-- سایدبار فیلترهای زنده AJAX -->
	<aside class="em-shop-sidebar">
		<form id="em-ajax-filter-form" class="em-filter-box">
			<h3>فیلتر پیشرفته محصولات</h3>

			<div class="em-filter-group">
				<label>دسته‌بندی محصول</label>
				<select name="category" class="em-filter-input">
					<option value="">همه دسته‌بندی‌ها</option>
					<?php
					$product_cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
					if ( ! is_wp_error( $product_cats ) ) {
						foreach ( $product_cats as $cat ) {
							echo '<option value="' . esc_attr( $cat->slug ) . '">' . esc_html( $cat->name ) . '</option>';
						}
					}
					?>
				</select>
			</div>

			<div class="em-filter-group">
				<label>برند سازنده</label>
				<select name="brand" class="em-filter-input">
					<option value="">همه برندها</option>
					<?php
					$brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) );
					if ( ! is_wp_error( $brands ) ) {
						foreach ( $brands as $brand ) {
							echo '<option value="' . esc_attr( $brand->slug ) . '">' . esc_html( $brand->name ) . '</option>';
						}
					}
					?>
				</select>
			</div>

			<div class="em-filter-group">
				<label>حداکثر قیمت (تومان)</label>
				<input type="number" name="max_price" class="em-filter-input" placeholder="مثلاً 50000000">
			</div>

			<div class="em-filter-group em-checkbox-row">
				<label>
					<input type="checkbox" name="in_stock" value="1" class="em-filter-input">
					فقط کالاهای موجود در انبار
				</label>
			</div>

			<button type="submit" class="em-btn em-btn-primary" style="width:100%;">اعمال فیلتر</button>
		</form>
	</aside>

	<!-- بخش اصلی لیست محصولات -->
	<section class="em-shop-main">
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
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					wc_get_template_part( 'content', 'product' );
				}
			} else {
				echo '<p class="em-no-results">محصولی یافت نشد.</p>';
			}
			?>
		</div>

		<div class="em-pagination">
			<?php woocommerce_pagination(); ?>
		</div>
	</section>
</div>

<?php get_footer(); ?>

