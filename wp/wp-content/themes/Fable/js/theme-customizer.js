/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	wp.customize( 'et_fable[link_color]', function( value ) {
		value.bind( function( to ) {
			var style_id = '#et_link_color',
				$style_content = "<style id='et_link_color'>\
									a { color: " + to + "; }\
								</style>";

			if ( $( style_id ).length ) {
				$( style_id ).replaceWith( $style_content );
			} else {
				$( 'head' ).append( $style_content );
			}
		} );
	} );

	wp.customize( 'et_fable[font_color]', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_fable[header_bg]', function( value ) {
		value.bind( function( to ) {
			$( '#main-header' ).css( 'background-color', to );
		} );
	} );

	wp.customize( 'et_fable[menu_link]', function( value ) {
		value.bind( function( to ) {
			$( '#top-menu a, .et_mobile_menu a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_fable[menu_link_active]', function( value ) {
		value.bind( function( to ) {
			$( '#top-menu li.current-menu-item > a, .et_mobile_menu li.current-menu-item > a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_fable[color_schemes]', function( value ) {
		value.bind( function( to ) {
			var $body = $( 'body' ),
				body_classes = $body.attr( 'class' ),
				et_customizer_color_scheme_prefix = 'et_color_scheme_',
				body_class;

			body_class = body_classes.replace( /et_color_scheme_[^\s]+/, '' );
			$body.attr( 'class', $.trim( body_class ) );

			if ( 'none' !== to  )
				$body.addClass( et_customizer_color_scheme_prefix + to );
		} );
	} );
} )( jQuery );