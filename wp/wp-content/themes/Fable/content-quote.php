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

		<figure class="quote">
			<blockquote>
				<?php the_content(); ?>
			</blockquote>
	<?php
		$quote_caption = get_post_meta( get_the_ID(), '_format_quote_source_name', true );
		$quote_url     = get_post_meta( get_the_ID(), '_format_quote_source_url', true );

		if ( '' !== $quote_caption && '' !== $quote_url )
			$quote_caption = sprintf( '<a href="%s">—&nbsp;%s</a>',
				esc_url( $quote_url ),
				esc_html( $quote_caption )
			);

		if ( '' !== $quote_caption )
			echo '<figcaption class="quote-caption">' . $quote_caption . '</figcaption>';
	?>
		</figure>

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