<?php
/**
 * Elementor Compatibility File
 *
 * @package Polysaas
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Elementor Locations.
 *
 * This function registers theme locations for use in Elementor.
 */
function polysaas_register_elementor_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'polysaas_register_elementor_locations' );

/**
 * Enqueue Elementor specific styles and scripts.
 */
function polysaas_elementor_scripts() {
	// Enqueue custom styles for Elementor
	wp_enqueue_style(
		'polysaas-elementor-style',
		get_template_directory_uri() . '/css/elementor.css',
		array(),
		_S_VERSION
	);

	// Enqueue custom scripts for Elementor
	wp_enqueue_script(
		'polysaas-elementor-script',
		get_template_directory_uri() . '/js/elementor.js',
		array( 'jquery' ),
		_S_VERSION, 
		true
	);
}
// add_action( 'elementor/frontend/after_enqueue_styles', 'polysaas_elementor_scripts' );

/**
 * Custom Elementor Widgets.
 */
function polysaas_register_elementor_widgets( $widgets_manager ) {
	require get_template_directory() . '/inc/elementor-widgets/custom-widget.php';

	$widgets_manager->register( new \Polysaas_Elementor_Custom_Widget() );
}
add_action( 'elementor/widgets/register', 'polysaas_register_elementor_widgets' );

/**
 * Elementor customizations for theme compatibility.
 */
function polysaas_elementor_customizations() {
	// Add customizations here if needed
}
add_action( 'wp', 'polysaas_elementor_customizations' );

?>