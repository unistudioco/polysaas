<?php
namespace Polysaas\Helpers;

use Walker_Nav_Menu;

/**
 * Navbar Custom Walker
 *
 * @package Polysaas
 */
class Navbar_Walker extends Walker_Nav_Menu {
        
    // Start Level
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        $submenu = ($depth > 0) ? ' uc-navbar-sub uc-navbar-dropdown-nav' : ' uc-navbar-dropdown-nav';
        $dropdown = ($depth > 0) ? "" : "<div class=\"uc-navbar-dropdown\">";
        $output .= "\n$indent$dropdown<ul class=\"uc-nav$submenu\">\n";
    }

    // Start Element
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        $li_attributes = '';
        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        if($args->walker->has_children) {
            $classes[] = 'uc-parent';
        }

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = ' class="' . esc_attr( $class_names ) . '"';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $value . $class_names . $li_attributes .'>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        if($args->walker->has_children) {
            $atts['class'] = ($depth > 0) ? '' : 'uc-navbar-toggle';
            $atts['uc-toggle'] = '';
            $atts['href']   = '#';
        }

        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        if($args->walker->has_children && $depth == 0) {
            $item_output .= "<span data-uc-navbar-parent-icon></span>";
        }
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    // End Element
    function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= "</li>\n";
    }

    // End Level
    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        $dropdown = ($depth > 0) ? "" : "</div>";
        $output .= "$indent</ul>$dropdown\n";
    }
}
?>