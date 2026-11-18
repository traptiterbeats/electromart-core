<?php
/**
 * برگه‌ها: سبد خرید، تسویه حساب، حساب کاربری، علاقه‌مندی‌ها و برگه‌های معمولی
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main class="em-container em-page-wrap">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'em-page-content' ); ?>>
			<h1 class="em-page-title"><?php the_title(); ?></h1>
			<div class="em-entry"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
