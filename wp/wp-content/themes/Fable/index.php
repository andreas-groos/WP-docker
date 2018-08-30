<?php get_header(); ?>

<?php
	if ( have_posts() ) :
		while ( have_posts() ) : the_post();
			get_template_part( 'content', get_post_format() );
		endwhile;

		if ( function_exists( 'wp_pagenavi' ) )
			wp_pagenavi();
		else
			get_template_part( 'includes/navigation', 'index' );
	else :
		get_template_part( 'includes/no-results', 'index' );
	endif;
?>

<?php get_footer(); ?>