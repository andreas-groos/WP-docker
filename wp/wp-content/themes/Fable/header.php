<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<?php elegant_description(); ?>
	<?php elegant_keywords(); ?>
	<?php elegant_canonical(); ?>

	<?php do_action( 'et_head_meta' ); ?>

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php $template_directory_uri = get_template_directory_uri(); ?>
	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( $template_directory_uri . '/js/html5.js"' ); ?>" type="text/javascript"></script>
	<![endif]-->

	<script type="text/javascript">
		document.documentElement.className = 'js';
	</script>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<header id="main-header">
		<div class="container clearfix">
		<?php
			$logo = ( $user_logo = et_get_option( 'fable_logo' ) ) && '' != $user_logo
				? $user_logo
				: $template_directory_uri . '/images/logo.png';
		?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo esc_attr( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" id="logo" />
			</a>

			<nav id="top-menu">
			<?php
				$menuClass = 'nav';
				if ( 'on' == et_get_option( 'fable_disable_toptier' ) ) $menuClass .= ' et_disable_top_tier';
				$primaryNav = '';

				$primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );

				if ( '' == $primaryNav ) :
			?>
				<ul class="<?php echo esc_attr( $menuClass ); ?>">
					<?php if ( 'on' == et_get_option( 'fable_home_link' ) ) { ?>
						<li <?php if ( is_home() ) echo( 'class="current_page_item"' ); ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home','Fable' ); ?></a></li>
					<?php }; ?>

					<?php show_page_menu( $menuClass, false, false ); ?>
					<?php show_categories_menu( $menuClass, false ); ?>
				</ul>
			<?php
				else :
					echo( $primaryNav );
				endif;
			?>
			</nav>

			<div id="et-social-icons">
			<?php
				$social_icons = array();

				if ( 'on' == et_get_option( 'fable_show_google_icon', 'on' ) ) $social_icons['google'] = array( 'image' => $template_directory_uri . '/images/google.png', 'url' => et_get_option( 'fable_google_url' ), 'alt' => __( 'Google Plus', 'Fable' ) );
				if ( 'on' == et_get_option( 'fable_show_facebook_icon','on' ) ) $social_icons['facebook'] = array( 'image' => $template_directory_uri . '/images/facebook.png', 'url' => et_get_option( 'fable_facebook_url' ), 'alt' => __( 'Facebook', 'Fable' ) );
				if ( 'on' == et_get_option( 'fable_show_twitter_icon', 'on' ) ) $social_icons['twitter'] = array( 'image' => $template_directory_uri . '/images/twitter.png', 'url' => et_get_option( 'fable_twitter_url' ), 'alt' => __( 'Twitter', 'Fable' ) );

				$social_icons = apply_filters( 'et_social_icons', $social_icons );

				if ( ! empty( $social_icons ) ) {
					foreach ( $social_icons as $icon ) {
						if ( $icon['url'] )
							printf( '<a href="%s" target="_blank"><img src="%s" alt="%s" /></a>', esc_url( $icon['url'] ), esc_attr( $icon['image'] ), esc_attr( $icon['alt'] ) );
					}
				}
			?>
			</div> <!-- #et-social-icons -->

			<?php do_action( 'et_header_top' ); ?>
		</div> <!-- .container -->
	</header> <!-- #main-header -->