<?php
defined( 'ABSPATH' ) || exit;

global $product;
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$product_id = $product->get_id();
$brands     = wp_get_post_terms( $product_id, 'product_brand', array( 'fields' => 'names' ) );
$brand_name = ( ! empty( $brands ) && ! is_wp_error( $brands ) ) ? $brands[0] : 'ElectroMart';

// محاسبه درصد تخفیف واقعی
$discount_pct = 0;
if ( $product->is_on_sale() && $product->get_regular_price() > 0 ) {
	$discount_pct = round( ( ( (float) $product->get_regular_price() - (float) $product->get_sale_price() ) / (float) $product->get_regular_price() ) * 100 );
}
?>
<div <?php wc_product_class( 'em-product-card', $product ); ?>>
	<div class="em-card-media">
		<!-- نشان‌های بالای کارت (NEW / درصد تخفیف / وضعیت موجودی) -->
		<div class="em-card-badges">
			<?php if ( ! $product->is_in_stock() ) : ?>
				<span class="em-badge em-badge-out">ناموجود</span>
			<?php elseif ( $discount_pct > 0 ) : ?>
				<span class="em-badge em-badge-discount">-<?php echo esc_html( $discount_pct ); ?>%</span>
			<?php else : ?>
				<span class="em-badge em-badge-new">NEW</span>
			<?php endif; ?>
		</div>

		<!-- دکمه‌های شناور علاقه‌مندی، مقایسه و مشاهده سریع -->
		<div class="em-card-quick-tools">
			<button type="button" class="em-tool-btn em-wishlist-btn" data-id="<?php echo esc_attr( $product_id ); ?>" title="افزودن به علاقه‌مندی‌ها">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
			</button>
			<button type="button" class="em-tool-btn em-compare-btn" data-id="<?php echo esc_attr( $product_id ); ?>" title="افزودن به لیست مقایسه">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5"/></svg>
			</button>
			<?php if ( get_theme_mod( 'em_enable_quickview', true ) ) : ?>
				<button type="button" class="em-tool-btn em-quickview-btn" data-id="<?php echo esc_attr( $product_id ); ?>" title="مشاهده سریع">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
				</button>
			<?php endif; ?>
		</div>

		<!-- تصویر محصول با بارگذاری تنبل (Lazy Loading) -->
		<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="em-card-img-link">
			<?php echo $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); ?>
		</a>

		<!-- دکمه افزودن به سبد خرید شناور پایین باکس تصویر (مطابق تصویر مرجع) -->
		<div class="em-card-cta-overlay">
			<?php if ( $product->is_in_stock() ) : ?>
				<?php if ( $product->is_type( 'simple' ) ) : ?>
					<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product_id ); ?>" class="em-card-add-btn add_to_cart_button ajax_add_to_cart">افزودن به سبد خرید</a>
				<?php else : ?>
					<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="em-card-add-btn">انتخاب مدل و خرید</a>
				<?php endif; ?>
			<?php else : ?>
				<span class="em-card-add-btn disabled">اتمام موجودی</span>
			<?php endif; ?>
		</div>
	</div>

	<!-- اطلاعات زیر کارت محصول -->
	<div class="em-card-info">
		<div class="em-card-meta-row">
			<div class="em-card-stars" aria-label="امتیاز کاربران">
				<?php
				$rating = $product->get_average_rating();
				$stars  = $rating > 0 ? round( $rating ) : 5;
				echo str_repeat( '★', $stars ) . str_repeat( '☆', 5 - $stars );
				?>
			</div>
			<span class="em-card-brand"><?php echo esc_html( $brand_name ); ?></span>
		</div>

		<h3 class="em-card-title">
			<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
		</h3>

		<div class="em-card-price-row">
			<div class="em-price"><?php echo $product->get_price_html(); ?></div>
			<span class="em-stock-dot <?php echo $product->is_in_stock() ? 'is-available' : 'is-unavailable'; ?>">
				<?php echo $product->is_in_stock() ? 'موجود' : 'ناموجود'; ?>
			</span>
		</div>
	</div>
</div>

