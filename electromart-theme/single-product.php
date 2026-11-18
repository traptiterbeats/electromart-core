<?php get_header(); ?>

<div class="em-container em-single-product-wrap">
	<?php while ( have_posts() ) : the_post(); global $product; ?>
		<?php do_action( 'woocommerce_before_single_product' ); ?>
		<article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'em-single-product', $product ); ?>>

			<div class="em-single-top-grid">
				<!-- گالری تصاویر استاندارد ووکامرس با قابلیت Zoom و Lightbox -->
				<div class="em-single-gallery">
					<?php woocommerce_show_product_images(); ?>
				</div>

				<!-- اطلاعات خرید، قیمت، ویژگی‌های متغیر و موجودی -->
				<div class="em-single-summary">
					<?php
					$brands = taxonomy_exists( 'product_brand' ) ? wp_get_post_terms( get_the_ID(), 'product_brand', array( 'fields' => 'names' ) ) : array();
					if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) :
					?>
						<span class="em-single-brand">برند: <?php echo esc_html( $brands[0] ); ?></span>
					<?php endif; ?>

					<h1 class="em-single-title"><?php the_title(); ?></h1>

					<div class="em-single-rating-stock">
						<?php woocommerce_template_single_rating(); ?>
						<span class="em-stock-badge <?php echo $product->is_in_stock() ? 'in-stock' : 'out-stock'; ?>">
							<?php echo $product->is_in_stock() ? 'موجود در انبار' : 'ناموجود'; ?>
						</span>
					</div>

					<div class="em-single-price">
						<?php woocommerce_template_single_price(); ?>
					</div>

					<div class="em-single-excerpt">
						<?php woocommerce_template_single_excerpt(); ?>
					</div>

					<!-- فرم افزودن به سبد خرید (پشتیبانی کامل از محصولات ساده و متغیر مانند رنگ، ظرفیت، مدل) -->
					<div class="em-single-add-to-cart">
						<?php woocommerce_template_single_add_to_cart(); ?>
					</div>

					<div class="em-single-utility-btns">
						<button type="button" class="em-btn em-btn-outline em-wishlist-btn" data-id="<?php the_ID(); ?>">♡ افزودن به علاقه‌مندی‌ها</button>
						<button type="button" class="em-btn em-btn-outline em-compare-btn" data-id="<?php the_ID(); ?>">⇄ مقایسه فنی محصول</button>
					</div>
				</div>
			</div>

			<!-- تب‌های توضیحات کامل، مشخصات فنی تخصصی و نظرات کاربران -->
			<div class="em-single-tabs">
				<div class="em-tabs-nav">
					<button type="button" class="em-tab-trigger active" data-target="tab-desc">بررسی تخصصی</button>
					<button type="button" class="em-tab-trigger" data-target="tab-specs">مشخصات فنی</button>
					<button type="button" class="em-tab-trigger" data-target="tab-reviews">نظرات کاربران (<?php echo esc_html( $product->get_review_count() ); ?>)</button>
				</div>

				<div id="tab-desc" class="em-tab-panel active">
					<?php the_content(); ?>
				</div>

				<div id="tab-specs" class="em-tab-panel">
					<h3>جدول مشخصات فنی</h3>
					<?php
					$specs  = get_post_meta( get_the_ID(), '_em_tech_specs', true );
					$specs  = is_array( $specs ) ? $specs : array();
					$labels = class_exists( 'ElectroMart_Core' ) ? ElectroMart_Core::spec_labels() : array();
					?>
					<table class="em-specs-table">
						<tbody>
							<?php foreach ( $labels as $k => $lbl ) : if ( ! empty( $specs[ $k ] ) && '—' !== $specs[ $k ] ) : ?>
								<tr>
									<th><?php echo esc_html( $lbl ); ?></th>
									<td><?php echo esc_html( $specs[ $k ] ); ?></td>
								</tr>
							<?php endif; endforeach; ?>
						</tbody>
					</table>
					<?php wc_display_product_attributes( $product ); ?>
				</div>

				<div id="tab-reviews" class="em-tab-panel">
					<?php comments_template(); ?>
				</div>
			</div>

			<!-- محصولات مکمل (Upsells) و محصولات مرتبط (Related Products) -->
			<div class="em-single-related">
				<?php woocommerce_upsell_display( 4, 4 ); ?>
				<?php woocommerce_output_related_products(); ?>
			</div>

		</article>
		<?php
		if ( isset( WC()->structured_data ) ) {
			WC()->structured_data->generate_product_data();
		}
		do_action( 'woocommerce_after_single_product' );
		?>
	<?php endwhile; ?>
</div>

<?php get_footer(); ?>

