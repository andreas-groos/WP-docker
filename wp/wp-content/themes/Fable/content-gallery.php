<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?><?php echo et_fable_get_background(); ?>>
	<div class="container clearfix">
		<header class="entry-title">
		<?php if ( is_single() ) : ?>
			<h1><?php the_title(); ?></h1>
		<?php else : ?>
			<h2>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>
		<?php endif; ?>

			<?php et_fable_post_meta(); ?>
		</header>

		<?php et_gallery_images(); ?>

	<?php if ( ! is_single() ) : ?>
		<footer class="entry-footer">
			<a href="<?php the_permalink(); ?>" class="read-more"><span><?php esc_html_e( 'Read More', 'Fable' ); ?></span></a>
		</footer>
	<?php endif; ?>
	</div> <!-- .container -->

	<?php if ( is_single() ) : ?>
		<?php get_template_part( 'includes/share', get_post_format() ); ?>
	<?php endif; ?>
</article> <!-- .entry-->