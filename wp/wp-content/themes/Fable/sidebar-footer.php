<?php
	if ( ! is_active_sidebar( 'sidebar-1' ) && ! is_active_sidebar( 'sidebar-2' ) && ! is_active_sidebar( 'sidebar-3'  ) )
		return;
?>

<footer id="main-footer">
	<div class="container">
		<div id="footer-widgets" class="clearfix">
		<?php
			$footer_sidebars = array( 'sidebar-1', 'sidebar-2', 'sidebar-3' );
			foreach ( $footer_sidebars as $key => $footer_sidebar ){
				if ( is_active_sidebar( $footer_sidebar ) ) {
					echo '<div class="footer-widget' . (  2 == $key ? ' last' : '' ) . '">';
					dynamic_sidebar( $footer_sidebar );
					echo '</div> <!-- end .footer-widget -->';
				}
			}
		?>
		</div> <!-- #footer-widgets -->
	</div> <!-- .container -->
</footer> <!-- #main-footer -->