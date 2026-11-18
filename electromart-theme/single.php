<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main class="em-container em-page-wrap">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'em-page-content' ); ?>>
			<h1 class="em-page-title"><?php the_title(); ?></h1>
			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } ?>
			<div class="em-entry"><?php the_content(); ?></div>
		</article>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	<?php endwhile; ?>
</main>
<?php
get_footer();
