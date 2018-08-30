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

	<?php
		global $wp_embed;

		$video_width = (int) apply_filters( 'fable_video_width', 865 );
		$video_height = (int) apply_filters( 'fable_video_height', 471 );

		$et_video_url = get_post_meta( get_the_ID(), '_format_video_embed', true );
		$video = apply_filters( 'the_content', $wp_embed->shortcode( '', esc_url( $et_video_url ) ) );

		$video = preg_replace('/<embed /','<embed wmode="transparent" ',$video);
		$video = preg_replace('/<\/object>/','<param name="wmode" value="transparent" /></object>',$video);

		$video = preg_replace("/width=\"[0-9]*\"/", "width={$video_width}", $video);
		$video = preg_replace("/height=\"[0-9]*\"/", "height={$video_height}", $video);

		$thumb = '';
		$classtext = '';
		$titletext = get_the_title();
		$thumbnail = get_thumbnail( $video_width, $video_height, $classtext, $titletext, $titletext, false, 'Videoimage' );
		$thumb = $thumbnail["thumb"];
	?>

	<?php if ( '' !== $video && '' !== $thumb ) : ?>
		<div class="entry-content clearfix">
			<div class="et-video-container">
				<div class="et-video-wrap">
					<div class="et-video-box">
						<?php echo $video; ?>
					</div> <!-- .et-video-box -->

					<div class="video-image">
						<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $video_width, $video_height, $classtext ); ?>
						<span class="video-play"></span>
					</div> <!-- .video-image -->
				</div> <!-- .et-video-wrap -->
			</div> <!-- .et-video-container -->
		</div> <!-- .entry-content -->
	<?php endif; ?>

	<?php if ( ! is_single() ) : ?>
		<footer class="entry-footer">
			<a href="<?php the_permalink(); ?>" class="read-more"><span><?php esc_html_e( 'Read More', 'Fable' ); ?></span></a>
		</footer>
	<?php else : ?>
		<?php get_template_part( 'includes/share', get_post_format() ); ?>
	<?php endif; ?>
	</div> <!-- .container -->
</article> <!-- .entry-->