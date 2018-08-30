<?php
function et_widgets_init() {
	register_sidebar( array(
		'name' => 'Footer Area #1',
		'id' => 'sidebar-1',
		'before_widget' => '<div id="%1$s" class="fwidget %2$s">',
		'after_widget' => '</div> <!-- end .fwidget -->',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	) );

	register_sidebar( array(
		'name' => 'Footer Area #2',
		'id' => 'sidebar-2',
		'before_widget' => '<div id="%1$s" class="fwidget %2$s">',
		'after_widget' => '</div> <!-- end .fwidget -->',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	) );

	register_sidebar( array(
		'name' => 'Footer Area #3',
		'id' => 'sidebar-3',
		'before_widget' => '<div id="%1$s" class="fwidget %2$s">',
		'after_widget' => '</div> <!-- end .fwidget -->',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	) );
}
add_action( 'widgets_init', 'et_widgets_init' );