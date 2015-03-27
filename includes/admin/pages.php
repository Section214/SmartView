<?php
/**
 * Admin pages
 *
 * @package     SmartView\Admin\Pages
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Create the settings menu pages
 *
 * @since       1.0.0
 * @global      string $smartview_settings_page The SmartView settings page hook
 * @return      void
 */
function smartview_add_settings_pages() {
    global $smartview_settings_page;

    $smartview_settings_page = add_submenu_page( 'options-general.php', __( 'SmartView Settings', 'smartview' ), __( 'SmartView', 'smartview' ), 'manage_options', 'smartview-settings', 'smartview_render_settings_page' );
}
add_action( 'admin_menu', 'smartview_add_settings_pages', 10 );


/**
 * Determines whether or not the current admin page is an SmartView page
 *
 * @since       1.0.0
 * @param       string $hook The hook for this page
 * @global      string $typenow The post type we are viewing
 * @global      string $pagenow The page we are viewing
 * @global      string $smartview_settings_page The SmartView settings page hook
 * @return      bool $ret True if SmartView page, false otherwise
 */
function smartview_is_admin_page( $hook ) {
    global $typenow, $pagenow, $smartview_settings_page;

    $ret    = false;
    $pages  = apply_filters( 'smartview_admin_pages', array( $smartview_settings_page ) );

    if( in_array( $hook, $pages ) ) {
        $ret = true;
    }

    return (bool) apply_filters( 'smartview_is_admin_page', $ret );
}
