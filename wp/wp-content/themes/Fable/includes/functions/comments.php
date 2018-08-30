<?php if ( ! function_exists( 'et_custom_comments_display' ) ) :
function et_custom_comments_display($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
			<div class="comment-author">
				<span class="et-avatar">
					<?php echo get_avatar( $comment, $size = '63' ); ?>
				</span>
				<strong><?php printf('<span class="fn">%s</span>', get_comment_author_link()) ?></strong>
				<p>
					<span class="comment_date">
					<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s', 'Fable' ), get_comment_date() );
					?>
					</span>
					<?php edit_comment_link( esc_html__( '(Edit)', 'Fable' ), ' ' ); ?>
				</p>
				<?php
					$et_comment_reply_link = get_comment_reply_link( array_merge( $args, array(
							'reply_text' => esc_attr__( 'Post a Reply', 'Fable' ),
							'depth'      => (int) $depth,
							'max_depth'  => (int) $args['max_depth'],
						) )
					);

					if ( $et_comment_reply_link ) echo '<span class="reply-container">' . $et_comment_reply_link . '</span>';
				?>
			</div> <!-- .comment-author -->

			<div class="comment_area">
				<?php if ( '0' == $comment->comment_approved ) : ?>
					<em class="moderation"><?php esc_html_e('Your comment is awaiting moderation.','Fable') ?></em>
					<br />
				<?php endif; ?>

				<div class="comment-content clearfix">
					<?php comment_text() ?>
				</div> <!-- end comment-content-->
			</div> <!-- end comment_area-->

			<div class="comment-bottom">
				<?php
					if ( $et_comment_reply_link ) echo '<span class="reply-container">' . $et_comment_reply_link . '</span>';
				?>
			</div> <!-- .comment-bottom -->
		</article> <!-- .comment-body -->
<?php }
endif;