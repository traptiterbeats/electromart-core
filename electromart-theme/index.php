<?php
/**
 * قالب پایه (الزامی وردپرس): بلاگ، جستجو، آرشیوها و ۴۰۴
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main class="em-container em-page-wrap">
	<?php if ( is_404() ) : ?>
		<div class="em-empty-state">
			<h1 class="em-page-title">صفحه پیدا نشد</h1>
			<p>صفحه‌ای که دنبالش بودید وجود ندارد یا جابه‌جا شده است.</p>
			<a class="em-btn em-btn-primary" href="<?php echo esc_url( electromart_shop_url() ); ?>">رفتن به فروشگاه</a>
		</div>
	<?php else : ?>
		<h1 class="em-page-title">
			<?php
			if ( is_search() ) {
				echo esc_html( 'نتایج جستجو برای: ' . get_search_query() );
			} elseif ( is_archive() ) {
				echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
			} else {
				echo esc_html( get_bloginfo( 'name' ) );
			}
			?>
		</h1>
		<?php if ( have_posts() ) : ?>
			<div class="em-post-list">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'em-post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
						<?php endif; ?>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="em-post-excerpt"><?php the_excerpt(); ?></div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="em-pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p class="em-no-results">موردی یافت نشد.</p>
		<?php endif; ?>
	<?php endif; ?>
</main>
<?php
get_footer();
