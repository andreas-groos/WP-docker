<?php

// Register assets that need to be fired at head
function et_fb_enqueue_assets_head() {
	// Setup WP media.
	wp_enqueue_media();
}
add_action( 'wp_enqueue_scripts', 'et_fb_enqueue_assets_head' );

// TODO, make this fire late enough, so that the_content has fired and ET_Builder_Element::get_computed_vars() is ready
// currently its being called in temporary_app_boot() in view.php
// add_action( 'wp_enqueue_scripts', 'et_fb_enqueue_assets' );
function et_fb_enqueue_main_assets() {
	$ver    = ET_BUILDER_VERSION;
	$root   = ET_BUILDER_URI;
	$assets = ET_FB_ASSETS_URI;

	wp_register_style( 'et_pb_admin_date_css', "{$root}/styles/jquery-ui-1.10.4.custom.css", array(), $ver );

	if ( et_is_builder_plugin_active() || et_builder_post_is_of_custom_post_type() ) {
		$responsive_preview_styles = 'responsive-preview-wrapped.css';
	} else {
		$responsive_preview_styles = 'responsive-preview.css';
	}

	wp_register_style( 'et-fb-responsive-preview', "{$assets}/css/{$responsive_preview_styles}", array(), $ver );

	// Enqueue the appropriate bundle CSS (hot/start/build)
	et_fb_enqueue_bundle( 'et-frontend-builder', 'bundle.css', array(
		'et_pb_admin_date_css',
		'wp-mediaelement',
		'wp-color-picker',
		'et-core-admin',
		'et-fb-responsive-preview',
	));

	// Load Divi Builder style.css file with hardcore CSS resets and Full Open Sans font if the Divi Builder plugin is active
	if ( et_is_builder_plugin_active() ) {
		// `bundle.css` was removed from `divi-builder-style.css` and is now enqueued separately for the DBP as well.
		wp_enqueue_style(
			'et-builder-divi-builder-styles',
			"{$assets}/css/divi-builder-style.css",
			array( 'et-core-admin', 'wp-color-picker', 'et-fb-responsive-preview' ),
			$ver
		);
	}

	if ( ! et_core_use_google_fonts() || et_is_builder_plugin_active() ) {
		et_fb_enqueue_open_sans();
	}

	wp_enqueue_style( 'et-frontend-builder-failure-modal', "{$assets}/css/failure_modal.css", array(), $ver );
	wp_enqueue_style( 'et-frontend-builder-notification-modal', "{$root}/styles/notification_popup_styles.css", array(), $ver );
}
add_action( 'wp_enqueue_scripts', 'et_fb_enqueue_main_assets' );

function et_fb_enqueue_open_sans() {
	$protocol = is_ssl() ? 'https' : 'http';
	$query_args = array(
		'family' => 'Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800',
		'subset' => 'latin,latin-ext',
	);

	wp_enqueue_style( 'et-fb-fonts', esc_url_raw( add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ) ), array(), null );
}

function et_fb_enqueue_google_maps_dependency( $dependencies ) {

	if ( et_pb_enqueue_google_maps_script() ) {
		$dependencies[] = 'google-maps-api';
	}

	return $dependencies;
}
add_filter( 'et_fb_bundle_dependencies', 'et_fb_enqueue_google_maps_dependency' );

function et_fb_load_portability() {
	et_core_register_admin_assets();
	et_core_load_component( 'portability' );

	// Register the Builder individual layouts portability.
	et_core_portability_register( 'et_builder', array(
		'name' =>  esc_html__( 'Divi Builder Layout', 'et_builder' ),
		'type' => 'post',
		'view' => true,
	) );
}

function et_fb_enqueue_assets() {
	global $wp_version;

	et_fb_load_portability();

	$ver    = ET_BUILDER_VERSION;
	$root   = ET_BUILDER_URI;
	$app    = ET_FB_URI;
	$assets = ET_FB_ASSETS_URI;

	// Get WP major version
	$wp_major_version = substr( $wp_version, 0, 3 );

	// Register styles.
	// wp_enqueue_style( 'et-frontend-builder', "{$assets}/css/frontend-builder.css", null, $ver );

	// Register scripts.
	// wp_register_script( 'minicolors', "{$root}/scripts/ext/jquery.minicolors.js" );

	wp_register_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
	wp_register_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), false, 1 );

	if ( version_compare( $wp_major_version, '4.9', '>=' ) ) {
		wp_register_script( 'wp-color-picker-alpha', "{$root}/scripts/ext/wp-color-picker-alpha.min.js", array( 'jquery', 'wp-color-picker' ), ET_BUILDER_VERSION, true );
	} else {
		wp_register_script( 'wp-color-picker-alpha', "{$root}/scripts/ext/wp-color-picker-alpha-48.min.js", array( 'jquery', 'wp-color-picker' ), ET_BUILDER_VERSION, true );
	}

	$colorpicker_l10n = array(
		'clear'         => esc_html__( 'Clear', 'et_builder' ),
		'defaultString' => esc_html__( 'Default', 'et_builder' ),
		'pick'          => esc_html__( 'Select Color', 'et_builder' ),
		'current'       => esc_html__( 'Current Color', 'et_builder' ),
	);

	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );

	wp_register_script( 'react-tiny-mce', "{$assets}/vendors/tinymce.min.js" );

	if ( version_compare( $wp_major_version, '4.5', '<' ) ) {
		$jQuery_ui = 'et_pb_admin_date_js';
		wp_register_script( $jQuery_ui, "{$root}/scripts/ext/jquery-ui-1.10.4.custom.min.js", array( 'jquery' ), $ver, true );
	} else {
		$jQuery_ui = 'jquery-ui-datepicker';
	}

	wp_register_script( 'et_pb_admin_date_addon_js', "{$root}/scripts/ext/jquery-ui-timepicker-addon.js", array( $jQuery_ui ), $ver, true );

	// `wp-shortcode` script handle is used by Gutenberg
	wp_register_script( 'et-wp-shortcode', includes_url() . 'js/shortcode.js', array(), $wp_version );

	wp_register_script( 'jquery-tablesorter', ET_BUILDER_URI . '/scripts/ext/jquery.tablesorter.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

	wp_register_script( 'chart', ET_BUILDER_URI . '/scripts/ext/chart.min.js', array(), ET_BUILDER_VERSION, true );

	$dependencies_list = array(
		'jquery',
		'jquery-ui-core',
		'jquery-ui-draggable',
		'jquery-ui-resizable',
		'underscore',
		'jquery-ui-sortable',
		'jquery-effects-core',
		'iris',
		'wp-color-picker',
		'wp-color-picker-alpha',
		'react-tiny-mce',
		'et_pb_admin_date_addon_js',
		'et-wp-shortcode',
		'heartbeat',
		'wp-mediaelement',
		'jquery-tablesorter',
		'chart',
		'react',
		'react-dom',
	);

	// Add dependency on et-shortcode-js only if Divi Theme is used or ET Shortcodes plugin activated
	if ( ! et_is_builder_plugin_active() || et_is_shortcodes_plugin_active() ) {
		$dependencies_list[] = 'et-shortcodes-js';
	}

	$fb_bundle_dependencies = apply_filters( 'et_fb_bundle_dependencies', $dependencies_list );

	// Adding concatenated script as dependencies for script debugging
	if ( et_load_unminified_scripts() ) {
		array_push( $fb_bundle_dependencies, 'easypiechart', 'salvattore', 'hashchange' );
	}

	// enqueue the Avada script before 'et-frontend-builder' to make sure easypiechart ( and probably some others ) override the scripts from Avada.
	if ( wp_script_is( 'avada' ) ) {
		// dequeue Avada script
		wp_dequeue_script( 'avada' );
		// enqueue it before 'et-frontend-builder'
		wp_enqueue_script( 'avada' );
	}

	$DEBUG        = defined( 'ET_DEBUG' ) && ET_DEBUG;
	$core_scripts = ET_CORE_URL . '/admin/js';

	if ( $DEBUG || DiviExtensions::is_debugging_extension() ) {
		wp_enqueue_script( 'react', 'https://cdn.jsdelivr.net/npm/react@16.3.2/umd/react.development.js', array(), '16.3.2', true );
		wp_enqueue_script( 'react-dom', 'https://cdn.jsdelivr.net/npm/react-dom@16.3.2/umd/react-dom.development.js', array( 'react' ), '16.3.2', true );
		add_filter( 'script_loader_tag', 'et_core_add_crossorigin_attribute', 10, 3 );
	} else {
		wp_enqueue_script( 'react', "{$core_scripts}/react.production.min.js", array(), '16.3.2', true );
		wp_enqueue_script( 'react-dom', "{$core_scripts}/react-dom.production.min.js", array( 'react' ), '16.3.2', true );
	}

	// Enqueue the appropriate bundle js (hot/start/build)
	et_fb_enqueue_bundle( 'et-frontend-builder', 'bundle.js', $fb_bundle_dependencies );

	// Search for additional bundles
	$additional_bundles = array();
	// CSS is now splitted as well.
	foreach ( glob( ET_BUILDER_DIR . 'frontend-builder/build/bundle.*.{css,js}' , GLOB_BRACE ) as $chunk ) {
		$additional_bundles[] = "{$app}/build/" . basename( $chunk );
	}
	// Pass bundle path and additional bundles to preload
	wp_localize_script( 'et-frontend-builder', 'et_webpack_bundle', array(
		'path'    => "{$app}/build/",
		'preload' => $additional_bundles,
	));

	// Enqueue failure notice script.
	wp_enqueue_script( 'et-frontend-builder-failure', "{$assets}/scripts/failure_notice.js", array(), $ver, true );
	wp_localize_script( 'et-frontend-builder-failure', 'et_fb_options', array(
		'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
		'et_admin_load_nonce'        => wp_create_nonce( 'et_admin_load_nonce' ),
		'memory_limit_increased'     => esc_html__( 'Your memory limit has been increased', 'et_builder' ),
		'memory_limit_not_increased' => esc_html__( "Your memory limit can't be changed automatically", 'et_builder' ),
	) );

	// WP Auth Check (allows user to log in again when session expires).
	wp_enqueue_style( 'wp-auth-check' );
	wp_enqueue_script( 'wp-auth-check' );
	add_action( 'wp_print_footer_scripts', 'et_fb_output_wp_auth_check_html', 5 );

	do_action( 'et_fb_enqueue_assets' );
}

function et_fb_output_wp_auth_check_html() {
	// A <button> element is used for the close button which looks ugly in Chrome. Use <a> element instead.
	ob_start();
	wp_auth_check_html();
	$output = ob_get_contents();
	ob_end_clean();

	$output = str_replace(
		array( '<button type="button"', '</button>' ),
		array( '<a href="#"', '</a>' ),
		$output
	);

	echo $output;
}

function et_fb_set_editor_available_cookie() {
	global $post;
	$post_id = isset( $post->ID ) ? $post->ID : false;
	if ( ! headers_sent() && !empty( $post_id ) ) {
		setcookie( 'et-editor-available-post-' . $post_id . '-fb', 'fb', time() + ( MINUTE_IN_SECONDS * 30 ), SITECOOKIEPATH, false, is_ssl() );
	}
}
add_action( 'et_fb_framework_loaded', 'et_fb_set_editor_available_cookie' );
