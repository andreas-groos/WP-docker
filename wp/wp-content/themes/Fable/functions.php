<?php

if ( ! isset( $content_width ) ) $content_width = 960;

function et_setup_theme(){
	global $themename, $shortname, $et_store_options_in_one_row, $default_colorscheme;
	$themename = 'Fable';
	$shortname = 'fable';
	$et_store_options_in_one_row = true;

	$default_colorscheme = "Default";

	$template_directory = get_template_directory();

	require_once( $template_directory . '/epanel/custom_functions.php' );

	require_once( $template_directory . '/includes/functions/sanitization.php' );

	require_once( $template_directory . '/includes/functions/comments.php' );

	require_once( $template_directory . '/includes/functions/sidebars.php' );

	load_theme_textdomain( 'Fable', $template_directory . '/lang' );

	require_once( $template_directory . '/epanel/core_functions.php' );

	require_once( $template_directory . '/includes/post_thumbnails_fable.php' );

	include( $template_directory . '/includes/widgets.php' );

	remove_action( 'admin_init', 'et_epanel_register_portability' );

	register_nav_menus( array(
		'primary-menu' => __( 'Primary Menu', 'Fable' ),
	) );

	add_theme_support( 'title-tag' );

	add_theme_support( 'post-formats', array(
		'gallery', 'quote', 'video'
	) );

	// don't display the empty title bar if the widget title is not set
	remove_filter( 'widget_title', 'et_widget_force_title' );

	add_action( 'wp_enqueue_scripts', 'et_add_responsive_shortcodes_css', 11 );
}
add_action( 'after_setup_theme', 'et_setup_theme' );

if ( ! function_exists( '_wp_render_title_tag' ) ) :
/**
 * Manually add <title> tag in head for WordPress 4.1 below for backward compatibility
 * Title tag is automatically added for WordPress 4.1 above via theme support
 * @return void
 */
	function et_add_title_tag_back_compat() { ?>
		<title><?php wp_title( '-', true, 'right' ); ?></title>
<?php
	}
	add_action( 'wp_head', 'et_add_title_tag_back_compat' );
endif;

if ( ! function_exists( 'et_fable_fonts_url' ) ) :
function et_fable_fonts_url() {
	if ( ! et_core_use_google_fonts() ) {
		return '';
	}

	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Open Sans, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$open_sans = _x( 'on', 'Open Sans font: on or off', 'Fable' );

	/* Translators: If there are characters in your language that are not
	 * supported by Raleway, translate this to 'off'. Do not translate into your
	 * own language.
	 */
	$raleway = _x( 'on', 'Raleway font: on or off', 'Fable' );

	if ( 'off' !== $open_sans || 'off' !== $raleway ) {
		$font_families = array();

		if ( 'off' !== $open_sans )
			$font_families[] = 'Open+Sans:300italic,400italic,700italic,800italic,400,300,700,800';

		if ( 'off' !== $raleway )
			$font_families[] = 'Raleway:400,200,100,500,700,800';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => implode( '|', $font_families ),
			'subset' => 'latin,latin-ext',
		);
		$fonts_url = add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" );
	}

	return $fonts_url;
}
endif;

function et_fable_load_fonts() {
	$fonts_url = et_fable_fonts_url();
	if ( ! empty( $fonts_url ) )
		wp_enqueue_style( 'fable-fonts', esc_url_raw( $fonts_url ), array(), null );
}
add_action( 'wp_enqueue_scripts', 'et_fable_load_fonts' );

function et_add_home_link( $args ) {
	// add Home link to the custom menu WP-Admin page
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'et_add_home_link' );

function et_fable_load_scripts_styles(){
	global $wp_styles;

	$template_dir = get_template_directory_uri();

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_enqueue_script( 'superfish', $template_dir . '/js/superfish.min.js', array( 'jquery' ), '1.0', true );

	if ( 'on' === et_get_option( 'fable_animations_on_scroll', 'on' ) )
		wp_enqueue_script( 'waypoints', $template_dir . '/js/waypoints.min.js', array( 'jquery' ), '1.0', true );

	wp_enqueue_script( 'fable-custom-script', $template_dir . '/js/custom.js', array( 'jquery' ), '1.0', true );
	wp_localize_script( 'fable-custom-script', 'et_custom', array(
		'mobile_nav_text' 	=> esc_html__( 'Navigation Menu', 'Fable' ),
	) );

	$et_gf_enqueue_fonts = array();
	$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
	$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );

	if ( 'none' != $et_gf_heading_font ) $et_gf_enqueue_fonts[] = $et_gf_heading_font;
	if ( 'none' != $et_gf_body_font ) $et_gf_enqueue_fonts[] = $et_gf_body_font;

	if ( ! empty( $et_gf_enqueue_fonts ) ) et_gf_enqueue_fonts( $et_gf_enqueue_fonts );

	/*
	 * Loads the main stylesheet.
	 */
	wp_enqueue_style( 'fable-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'et_fable_load_scripts_styles' );

function et_add_mobile_navigation(){
	echo '<div id="et_mobile_nav_menu">' . '<a href="#" class="mobile_nav closed">' . esc_html__( 'Navigation Menu', 'Fable' ) . '<span></span>' . '</a>' . '</div>';
}
add_action( 'et_header_top', 'et_add_mobile_navigation' );

/**
 * Filters the main query on homepage
 */
function et_home_posts_query( $query = false ) {
	/* Don't proceed if it's not homepage or the main query */
	if ( ! is_home() || ! is_a( $query, 'WP_Query' ) || ! $query->is_main_query() ) return;

	/* Set the amount of posts per page on homepage */
	$query->set( 'posts_per_page', (int) et_get_option( 'fable_homepage_posts', 8 ) );

	$exclude_categories = et_get_option( 'fable_exlcats_recent', false, 'category' );

	if ( $exclude_categories ) $query->set( 'category__not_in', array_map( 'intval', $exclude_categories ) );
}
add_action( 'pre_get_posts', 'et_home_posts_query' );

function et_add_viewport_meta(){
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />';
}
add_action( 'wp_head', 'et_add_viewport_meta' );

function et_remove_additional_stylesheet( $stylesheet ){
	global $default_colorscheme;
	return $default_colorscheme;
}
add_filter( 'et_get_additional_color_scheme', 'et_remove_additional_stylesheet' );

if ( ! function_exists( 'et_list_pings' ) ) :
function et_list_pings($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
<?php }
endif;

if ( ! function_exists( 'et_get_the_author_posts_link' ) ) :
function et_get_the_author_posts_link(){
	global $authordata, $themename;

	$link = sprintf(
		'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
		esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
		esc_attr( sprintf( __( 'Posts by %s', $themename ), get_the_author() ) ),
		get_the_author()
	);
	return apply_filters( 'the_author_posts_link', $link );
}
endif;

if ( ! function_exists( 'et_get_comments_popup_link' ) ) :
function et_get_comments_popup_link( $zero = false, $one = false, $more = false ){
	global $themename;

	$id = get_the_ID();
	$number = get_comments_number( $id );

	if ( 0 == $number && !comments_open() && !pings_open() ) return;

	if ( $number > 1 )
		$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments', $themename) : $more);
	elseif ( $number == 0 )
		$output = ( false === $zero ) ? __('No Comments',$themename) : $zero;
	else // must be one
		$output = ( false === $one ) ? __('1 Comment', $themename) : $one;

	return '<span class="comments-number">' . '<a href="' . esc_url( get_permalink() . '#respond' ) . '">' . apply_filters('comments_number', $output, $number) . '</a>' . '</span>';
}
endif;

if ( ! function_exists( 'et_postinfo_meta' ) ) :
function et_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
	global $themename;

	$postinfo_meta = '';

	if ( in_array( 'author', $postinfo ) ){
		$postinfo_meta .= ' ' . esc_html__('By',$themename) . ' ' . et_get_the_author_posts_link();
	}

	if ( in_array( 'date', $postinfo ) )
		$postinfo_meta .= ' ' . esc_html__('on',$themename) . ' ' . get_the_time( $date_format );

	if ( in_array( 'categories', $postinfo ) )
		$postinfo_meta .= ' ' . esc_html__('in',$themename) . ' ' . get_the_category_list(', ');

	if ( in_array( 'comments', $postinfo ) )
		$postinfo_meta .= ' | ' . et_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );

	echo $postinfo_meta;
}
endif;

function et_add_post_meta_box() {
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'Fable' ), 'et_single_settings_meta_box', 'post', 'normal', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'Fable' ), 'et_single_settings_meta_box', 'page', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'et_add_post_meta_box' );

if ( ! function_exists( 'et_single_settings_meta_box' ) ) :
function et_single_settings_meta_box( $post ) {
	$post_id        = get_the_ID();
	$layouts        = array(
		'dark'  => __( 'Dark', 'Fable' ),
		'light' => __( 'Light', 'Fable' ),
	);
	$post_bg_color  = ( $bg_color = get_post_meta( $post_id, '_et_post_bg_color', true ) ) && '' !== $bg_color
		? $bg_color
		: '#ffffff';
	$post_bg_layout = ( $layout = get_post_meta( $post_id, '_et_post_bg_layout', true ) ) && '' !== $layout
		? $layout
		: 'dark';

	$post_video_url         = get_post_meta( $post_id, '_format_video_embed', true );
	$post_quote_source_name = get_post_meta( $post_id, '_format_quote_source_name', true );
	$post_quote_source_url  = get_post_meta( $post_id, '_format_quote_source_url', true );

	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );
?>

<?php if ( 'post' === $post->post_type ) : ?>
	<p class="et_fable_video_settings et_fable_format_setting" style="display: none;">
		<label for="et_post_video_url" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Video URL', 'Fable' ); ?>: </label>
		<input id="et_post_video_url" name="et_post_video_url" class="regular-text" type="text" value="<?php echo esc_attr( $post_video_url ); ?>" />
		<br/>
	</p>

	<p class="et_fable_quote_settings et_fable_format_setting" style="display: none;">
		<label for="et_post_quote_source_name" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Quote Source', 'Fable' ); ?>: </label>
		<input id="et_post_quote_source_name" name="et_post_quote_source_name" class="regular-text" type="text" value="<?php echo esc_attr( $post_quote_source_name ); ?>" />
		<br/>
	</p>

	<p class="et_fable_quote_settings et_fable_format_setting" style="display: none;">
		<label for="et_post_quote_source_url" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Quote Source Link', 'Fable' ); ?>: </label>
		<input id="et_post_quote_source_url" name="et_post_quote_source_url" class="regular-text" type="text" value="<?php echo esc_attr( $post_quote_source_url ); ?>" />
		<br/>
	</p>
<?php endif; ?>

	<p><?php esc_html_e( 'Note: The following settings apply to all post formats, except Gallery.', 'Fable' ); ?></p>
	<p>
		<label for="et_post_bg_color" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Background Color', 'Fable' ); ?>: </label>
		<input id="et_post_bg_color" name="et_post_bg_color" class="color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'Fable' ); ?>" value="<?php echo esc_attr( $post_bg_color ); ?>" data-default-color="#ffffff" />
		<br/>
		<small><?php esc_html_e( 'Here you can set a solid color background for the post.', 'Fable' ); ?></small>
	</p>
	<p>
		<label for="et_post_bg_layout" style="font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Post Layout', 'Fable' ); ?>: </label>
		<select id="et_post_bg_layout" name="et_post_bg_layout">
	<?php
		foreach ( $layouts as $layout_name => $layout_title )
			printf( '<option value="%s"%s>%s</option>',
				esc_attr( $layout_name ),
				selected( $layout_name, $post_bg_layout, false ),
				esc_html( $layout_title )
			);
	?>
		</select>
	</p>
	<p>
		<label for="et_use_bg_color" class="selectit">
			<input name="et_use_bg_color" type="checkbox" id="et_use_bg_color" value="" <?php checked( 'on', get_post_meta( $post_id, '_et_use_bg_color', true ) ); ?>> <?php esc_html_e( 'Use Background Color instead of the Featured Image.', 'Fable' ); ?>
		</label>
	</p>
<?php
}
endif;

function et_metabox_settings_save_details( $post_id, $post ){
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( !isset( $_POST['et_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) )
        return $post_id;

	if ( isset( $_POST['et_post_bg_color'] ) )
		update_post_meta( $post_id, '_et_post_bg_color', sanitize_text_field( $_POST['et_post_bg_color'] ) );
	else
		delete_post_meta( $post_id, '_et_post_bg_color' );

	if ( isset( $_POST['et_post_bg_layout'] ) )
		update_post_meta( $post_id, '_et_post_bg_layout', sanitize_text_field( $_POST['et_post_bg_layout'] ) );
	else
		delete_post_meta( $post_id, '_et_post_bg_layout' );

	if ( isset( $_POST['et_use_bg_color'] ) )
		update_post_meta( $post_id, '_et_use_bg_color', 'on' );
	else
		delete_post_meta( $post_id, '_et_use_bg_color' );

	if ( isset( $_POST['et_post_video_url'] ) )
		update_post_meta( $post_id, '_format_video_embed', esc_url_raw( $_POST['et_post_video_url'] ) );
	else
		delete_post_meta( $post_id, '_format_video_embed' );

	if ( isset( $_POST['et_post_quote_source_name'] ) )
		update_post_meta( $post_id, '_format_quote_source_name', sanitize_text_field( $_POST['et_post_quote_source_name'] ) );
	else
		delete_post_meta( $post_id, '_format_quote_source_name' );

	if ( isset( $_POST['et_post_quote_source_url'] ) )
		update_post_meta( $post_id, '_format_quote_source_url', esc_url_raw( $_POST['et_post_quote_source_url'] ) );
	else
		delete_post_meta( $post_id, '_format_quote_source_url' );
}
add_action( 'save_post', 'et_metabox_settings_save_details', 10, 2 );

function et_fable_post_admin_scripts_styles( $hook ) {
	global $typenow;

	if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) return;

	if ( ! isset( $typenow ) ) return;

	if ( in_array( $typenow, array( 'post', 'page' ) ) ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'et-admin-post-script', get_template_directory_uri() . '/js/admin_post_settings.js', array( 'jquery' ) );

		if ( 'page' === $typenow )
			wp_enqueue_script( 'et-admin-page-script', get_template_directory_uri() . '/js/admin_remove_fwidth.js', array( 'jquery' ) );
	}
}
add_action( 'admin_enqueue_scripts', 'et_fable_post_admin_scripts_styles' );

function et_fable_customize_register( $wp_customize ) {
	$google_fonts = et_get_google_fonts();

	$font_choices = array();
	$font_choices['none'] = 'Default Theme Font';
	foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
		$font_choices[ $google_font_name ] = $google_font_name;
	}

	$wp_customize->remove_section( 'title_tagline' );
	$wp_customize->remove_section( 'background_image' );

	$wp_customize->add_section( 'et_google_fonts' , array(
		'title'		=> __( 'Fonts', 'Fable' ),
		'priority'	=> 50,
	) );

	$wp_customize->add_section( 'et_color_schemes' , array(
		'title'       => __( 'Schemes', 'Fable' ),
		'priority'    => 60,
		'description' => __( 'Note: Color settings set above should be applied to the Default color scheme.', 'Fable' ),
	) );

	$wp_customize->add_setting( 'et_fable[link_color]', array(
		'default'		    => '#6ba7a5',
		'type'			    => 'option',
		'capability'	    => 'edit_theme_options',
		'transport'		    => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_fable[link_color]', array(
		'label'		=> __( 'Link Color', 'Fable' ),
		'section'	=> 'colors',
		'settings'	=> 'et_fable[link_color]',
	) ) );

	$wp_customize->add_setting( 'et_fable[font_color]', array(
		'default'		    => '#242424',
		'type'			    => 'option',
		'capability'	    => 'edit_theme_options',
		'transport'		    => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_fable[font_color]', array(
		'label'		=> __( 'Main Font Color', 'Fable' ),
		'section'	=> 'colors',
		'settings'	=> 'et_fable[font_color]',
	) ) );

	$wp_customize->add_setting( 'et_fable[header_bg]', array(
		'default'		    => '#292929',
		'type'			    => 'option',
		'capability'	    => 'edit_theme_options',
		'transport'		    => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_fable[header_bg]', array(
		'label'		=> __( 'Header Background Color', 'Fable' ),
		'section'	=> 'colors',
		'settings'	=> 'et_fable[header_bg]',
	) ) );

	$wp_customize->add_setting( 'et_fable[menu_link]', array(
		'default'		    => '#ffffff',
		'type'			    => 'option',
		'capability'	    => 'edit_theme_options',
		'transport'		    => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_fable[menu_link]', array(
		'label'		=> __( 'Menu Links Color', 'Fable' ),
		'section'	=> 'colors',
		'settings'	=> 'et_fable[menu_link]',
	) ) );

	$wp_customize->add_setting( 'et_fable[menu_link_active]', array(
		'default'		    => '#7fcbc8',
		'type'			    => 'option',
		'capability'	    => 'edit_theme_options',
		'transport'		    => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_fable[menu_link_active]', array(
		'label'		=> __( 'Active Menu Link Color', 'Fable' ),
		'section'	=> 'colors',
		'settings'	=> 'et_fable[menu_link_active]',
	) ) );

	$wp_customize->add_setting( 'et_fable[heading_font]', array(
		'default'		    => 'none',
		'type'			    => 'option',
		'capability'	    => 'edit_theme_options',
		'sanitize_callback' => 'et_sanitize_font_choices',
	) );

	$wp_customize->add_control( 'et_fable[heading_font]', array(
		'label'		=> __( 'Header Font', 'Fable' ),
		'section'	=> 'et_google_fonts',
		'settings'	=> 'et_fable[heading_font]',
		'type'		=> 'select',
		'choices'	=> $font_choices
	) );

	$wp_customize->add_setting( 'et_fable[body_font]', array(
		'default'		    => 'none',
		'type'			    => 'option',
		'capability'	    => 'edit_theme_options',
		'sanitize_callback' => 'et_sanitize_font_choices',
	) );

	$wp_customize->add_control( 'et_fable[body_font]', array(
		'label'		=> __( 'Body Font', 'Fable' ),
		'section'	=> 'et_google_fonts',
		'settings'	=> 'et_fable[body_font]',
		'type'		=> 'select',
		'choices'	=> $font_choices
	) );

	$wp_customize->add_setting( 'et_fable[color_schemes]', array(
		'default'		    => 'none',
		'type'			    => 'option',
		'capability'	    => 'edit_theme_options',
		'transport'		    => 'postMessage',
		'sanitize_callback' => 'et_sanitize_color_scheme',
	) );

	$wp_customize->add_control( 'et_fable[color_schemes]', array(
		'label'		=> __( 'Color Schemes', 'Fable' ),
		'section'	=> 'et_color_schemes',
		'settings'	=> 'et_fable[color_schemes]',
		'type'		=> 'select',
		'choices'	=> et_theme_color_scheme_choices(),
	) );
}
add_action( 'customize_register', 'et_fable_customize_register' );

if ( ! function_exists( 'et_theme_color_scheme_choices' ) ) :
/**
 * Returns list of color schemes
 * @return array
 */
function et_theme_color_scheme_choices() {
	return apply_filters( 'et_theme_color_scheme_choices', array(
		'none'   => __( 'Default', 'Fable' ),
		'blue'   => __( 'Blue', 'Fable' ),
		'green'  => __( 'Green', 'Fable' ),
		'purple' => __( 'Purple', 'Fable' ),
		'red'    => __( 'Red', 'Fable' ),
	) );
}
endif;

function et_fable_customize_preview_js() {
	wp_enqueue_script( 'fable-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), false, true );
}
add_action( 'customize_preview_init', 'et_fable_customize_preview_js' );

function et_fable_add_customizer_css(){ ?>
	<style>
		a { color: <?php echo esc_html( et_get_option( 'link_color', '#6ba7a5' ) ); ?>; }

		body { color: <?php echo esc_html( et_get_option( 'font_color', '#242424' ) ); ?>; }

		#main-header { background-color: <?php echo esc_html( et_get_option( 'header_bg', '#292929' ) ); ?>; }

		#top-menu a, .et_mobile_menu a { color: <?php echo esc_html( et_get_option( 'menu_link', '#ffffff' ) ); ?>; }

		#top-menu li.current-menu-item > a, .et_mobile_menu li.current-menu-item > a { color: <?php echo esc_html( et_get_option( 'menu_link_active', '#7fcbc8' ) ); ?>; }

	<?php
		$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
		$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );

		if ( 'none' != $et_gf_heading_font || 'none' != $et_gf_body_font ) :

			if ( 'none' != $et_gf_heading_font )
				et_gf_attach_font( $et_gf_heading_font, 'h1, h2, h3, h4, h5, h6, #comments, #reply-title, .entry header h1, .entry header h2' );

			if ( 'none' != $et_gf_body_font )
				et_gf_attach_font( $et_gf_body_font, 'body, input, textarea, select' );

		endif;
	?>
	</style>
<?php }
add_action( 'wp_head', 'et_fable_add_customizer_css' );
add_action( 'customize_controls_print_styles', 'et_fable_add_customizer_css' );

/*
 * Adds color scheme class to the body tag
 */
function et_customizer_color_scheme_class( $body_class ) {
	$color_scheme        = et_get_option( 'color_schemes', 'none' );
	$color_scheme_prefix = 'et_color_scheme_';

	if ( 'none' !== $color_scheme ) $body_class[] = $color_scheme_prefix . $color_scheme;

	return $body_class;
}
add_filter( 'body_class', 'et_customizer_color_scheme_class' );

function et_load_google_fonts_scripts() {
	wp_enqueue_script( 'et_google_fonts', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.js', array( 'jquery' ), '1.0', true );
}
add_action( 'customize_controls_print_footer_scripts', 'et_load_google_fonts_scripts' );

function et_load_google_fonts_styles() {
	wp_enqueue_style( 'et_google_fonts_style', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.css', array(), null );
}
add_action( 'customize_controls_print_styles', 'et_load_google_fonts_styles' );

/**
 * Removes galleries on single gallery posts, since we display images from all
 * galleries on top of the page
 */
function et_delete_post_gallery( $content ) {
	if ( is_single() && is_main_query() && has_post_format( 'gallery' ) ) :
		$regex = get_shortcode_regex();
		preg_match_all( "/{$regex}/s", $content, $matches );

		// $matches[2] holds an array of shortcodes names in the post
		foreach ( $matches[2] as $key => $shortcode_match ) {
			if ( 'gallery' === $shortcode_match )
				$content = str_replace( $matches[0][$key], '', $content );
		}
	endif;

	return $content;
}
add_filter( 'the_content', 'et_delete_post_gallery' );

if ( ! function_exists( 'et_fable_get_background' ) ) :
function et_fable_get_background() {
	if ( has_post_format( 'gallery' ) ) return '';

	$style = '';
	$post_bg_color     = ( $bg_color = get_post_meta( get_the_ID(), '_et_post_bg_color', true ) ) && '' !== $bg_color
		? $bg_color
		: '#ffffff';
	$post_use_bg_color = 'on' === get_post_meta( get_the_ID(), '_et_use_bg_color', true )
		? true
		: false;
	$background = array();

	$thumb = '';
	$width = (int) apply_filters( 'et_blog_image_width', 9999 );
	$height = (int) apply_filters( 'et_blog_image_height', 9999 );
	$classtext = '';
	$titletext = get_the_title();
	$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Indeximage' );
	$thumb = $thumbnail["thumb"];

	// Gallery posts use featured image for the picture in the video container
	// so don't allow it to use the featured image for background
	if ( '' !== $thumb && ! has_post_format( 'video' ) )
		$background['image'] = $thumb;

	if ( isset( $background['image'] ) )
		$style = sprintf( ' style="background-image: url(%s);"', esc_attr( $background['image'] ) );

	// Attaches solid background color to the post if the featured image is not set
	// or 'Background Color instead of the Featured Image' is enabled
	if ( '' === $style || $post_use_bg_color )
		$style = sprintf( ' style="background-color: %s;"', esc_attr( $post_bg_color ) );

	return $style;
}
endif;

if ( ! function_exists( 'et_fable_post_meta' ) ) :
function et_fable_post_meta() {
	$postinfo = is_single() ? et_get_option( 'fable_postinfo1' ) : et_get_option( 'fable_postinfo2' );

	if ( $postinfo ) :
		echo '<p class="meta-info">';
		et_postinfo_meta( $postinfo, et_get_option( 'fable_date_format', 'M j, Y' ), esc_html__( '0 comments', 'Fable' ), esc_html__( '1 comment', 'Fable' ), '% ' . esc_html__( 'comments', 'Fable' ) );
		echo '</p>';
	endif;
}
endif;

function et_fable_post_class( $post_class ) {
	$post_bg_color  = ( $bg_color = get_post_meta( get_the_ID(), '_et_post_bg_color', true ) ) && '' !== $bg_color
		? $bg_color
		: '#ffffff';
	$post_bg_layout = ( $layout = get_post_meta( get_the_ID(), '_et_post_bg_layout', true ) ) && '' !== $layout
		? $layout
		: 'dark';

	$post_class[] = 'et-bg-layout-' . $post_bg_layout;

	if ( false !== strpos( et_fable_get_background(), 'background-image' ) )
		$post_class[] = 'et-background-image';
	else if ( '#ffffff' === $post_bg_color )
		$post_class[] = 'et-white-bg';

	return $post_class;
}
add_filter( 'post_class', 'et_fable_post_class' );

if ( ! function_exists( 'et_gallery_images' ) ) :
function et_gallery_images() {
	$output = $images_ids = '';

	if ( function_exists( 'get_post_galleries' ) ) {
		$galleries = get_post_galleries( get_the_ID(), false );

		if ( empty( $galleries ) ) return false;

		foreach ( $galleries as $gallery ) {
			// Grabs all attachments ids from one or multiple galleries in the post
			$images_ids .= ( '' !== $images_ids ? ',' : '' ) . $gallery['ids'];
		}

		$attachments_ids = explode( ',', $images_ids );
		// Removes duplicate attachments ids
		$attachments_ids = array_unique( $attachments_ids );
	} else {
		$pattern = get_shortcode_regex();
		preg_match( "/$pattern/s", get_the_content(), $match );
		$atts = shortcode_parse_atts( $match[3] );

		if ( isset( $atts['ids'] ) )
			$attachments_ids = explode( ',', $atts['ids'] );
		else
			return false;
	}

	echo '<ul class="et-main-gallery clearfix">';
	foreach ( $attachments_ids as $attachment_id ) {
		$attachment = get_post( $attachment_id );
		$fullimage_attributes = wp_get_attachment_image_src( $attachment_id, 'full' );

		printf( '<li><a href="%s" class="fancybox" rel="gallery" title="%s">%s<span class="et-overlay"></span><span class="et-icon-zoom"></span></a></li>',
			esc_url( $fullimage_attributes[0] ),
			esc_attr( $attachment->post_title ),
			wp_get_attachment_image( $attachment_id, 'et-format-gallery-thumb' )
		);
	}
	echo '</ul>';

	return $output;
}
endif;

function et_fable_add_animations( $body_class ) {
	if ( 'on' === et_get_option( 'fable_animations_on_scroll', 'on' ) )
		$body_class[] = 'et-scroll-animations';

	return $body_class;
}
add_filter( 'body_class', 'et_fable_add_animations' );

function et_android_body_class( $body_class ) {
	if ( false !== stripos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'android' ) ) {
		$body_class[] = 'et-mobile-android';
	}

	return $body_class;
}
add_filter( 'body_class', 'et_android_body_class' );

function et_remove_additional_epanel_styles() {
	return true;
}
add_filter( 'et_epanel_is_divi', 'et_remove_additional_epanel_styles' );

function et_register_updates_component() {
	require_once( get_template_directory() . '/core/updates_init.php' );

	et_core_enable_automatic_updates( get_template_directory_uri(), et_get_theme_version() );
}
add_action( 'admin_init', 'et_register_updates_component' );

if ( ! function_exists( 'et_core_portability_link' ) && ! class_exists( 'ET_Builder_Plugin' ) ) :
function et_core_portability_link() {
	return '';
}
endif;

function et_theme_maybe_load_core() {
	if ( et_core_exists_in_active_plugins() ) {
		return;
	}

	if ( defined( 'ET_CORE' ) ) {
		return;
	}

	require_once get_template_directory() . '/core/init.php';

	et_core_setup( get_template_directory_uri() );
}
add_action( 'after_setup_theme', 'et_theme_maybe_load_core' );

