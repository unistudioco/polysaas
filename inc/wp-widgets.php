<?php
/**
 * WP Custom Widgets
 * 
 * @package Polysaas
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( class_exists( 'Polysaas_Recent_Posts_Widget' ) ) {
    include_once( dirname( __FILE__ ) . '/inc/wp-widgets/recent-posts.php' );
}