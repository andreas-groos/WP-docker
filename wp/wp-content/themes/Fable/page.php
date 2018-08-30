<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php get_template_part( 'content', 'page' ); ?>

	<article class="et-additional-content">
		<?php get_template_part( 'includes/breadcrumbs', 'single' ); ?>

		<div class="entry-content container clearfix">
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'Fable' ), 'after' => '</div>' ) ); ?>

		</div> <!-- .entry-content -->

	<?php
		if ( comments_open() && 'on' == et_get_option( 'fable_show_pagescomments', 'false' ) )
			comments_template( '', true );
	?>
	</article> <!-- .et-additional-content -->

<?php endwhile; ?>

<?php get_footer(); ?>