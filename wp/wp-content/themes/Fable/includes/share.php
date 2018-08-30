<?php
	if ( 'false' === et_get_option( 'fable_show_avatar_share_on_posts', 'on' ) )
		return;
?>
<div class="et-author">
	<span class="et-avatar">
		<?php echo get_avatar( get_the_author_meta( 'ID' ), 62 ); ?>

		<span class="et-share-buttons">
		<?php
			$thumbnail       = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' );
			$title_attribute = the_title_attribute( 'echo=0' );
			$post_permalink  = get_permalink();

			printf( '<a href="https://plus.google.com/share?url=%1$s" target="_blank" class="et-share-button et-share-google">%2$s</a>',
				esc_url( $post_permalink ),
				esc_html__( 'Share On Google', 'Fable' )
			);

			printf( '<a href="http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=%1$s&amp;p[images][0]=%2$s&amp;p[title]=%3$s" target="_blank" class="et-share-button et-share-facebook">%4$s</a>',
				esc_url( $post_permalink ),
				esc_attr( $thumbnail[0] ),
				$title_attribute,
				esc_html__( 'Share On Facebook', 'Fable' )
			);

			printf( '<a href="https://twitter.com/intent/tweet?url=%1$s&amp;text=%2$s" target="_blank" class="et-share-button et-share-twitter">%3$s</a>',
				esc_url( $post_permalink ),
				$title_attribute,
				esc_html__( 'Share On Twitter', 'Fable' )
			);
		?>
		</span>
	</span>
</div>