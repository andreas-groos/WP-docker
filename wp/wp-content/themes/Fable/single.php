<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
	<?php get_template_part( 'content', get_post_format() ); ?>

	<article class="et-additional-content">

	<?php if ( ! has_post_format( 'quote' ) ) : ?>
		<?php get_template_part( 'includes/breadcrumbs', 'single' ); ?>

		<div class="entry-content container clearfix">
			<?php if (et_get_option('fable_integration_single_top') <> '' && et_get_option('fable_integrate_singletop_enable') == 'on') echo(et_get_option('fable_integration_single_top')); ?>

			<?php if (et_get_option('fable_integration_single_bottom') <> '' && et_get_option('fable_integrate_singlebottom_enable') == 'on') echo(et_get_option('fable_integration_single_bottom')); ?>

		<?php
			the_content();

			wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'Fable' ), 'after' => '</div>' ) );
		?>

		<?php
			if ( et_get_option('fable_468_enable') == 'on' ){
				if ( et_get_option('fable_468_adsense') <> '' ) echo( et_get_option('fable_468_adsense') );
				else { ?>
				   <a href="<?php echo esc_url(et_get_option('fable_468_url')); ?>"><img src="<?php echo esc_attr(et_get_option('fable_468_image')); ?>" alt="468 ad" class="foursixeight" /></a>
		<?php 	}
			}
		?>
		</div> <!-- .entry-content -->
	<?php endif; ?>

	<?php
		if ( comments_open() && 'on' == et_get_option( 'fable_show_postcomments', 'on' ) )
			comments_template( '', true );
	?>

	</article> <!-- .et-additional-content -->

<?php endwhile; ?>

<?php get_footer(); ?>