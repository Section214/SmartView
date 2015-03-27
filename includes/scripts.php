<?php
/**
 * Scripts
 *
 * @package     SmartView\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @param       string $hook The page hook
 * @return      void
 */
function smartview_admin_scripts( $hook ) {
    if( ! apply_filters( 'smartview_load_admin_scripts', smartview_is_admin_page( $hook ), $hook ) ) {
        return;
    }

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
    $ui_style   = ( get_user_option( 'admin_color' ) == 'classic' ) ? 'classic' : 'fresh';

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_media();
    wp_enqueue_style( 'jquery-ui-css', SMARTVIEW_URL . 'assets/css/jquery-ui-' . $ui_style . $suffix . '.css' );
    wp_enqueue_script( 'media-upload' );
    wp_enqueue_style( 'thickbox' );
    wp_enqueue_script( 'thickbox' );

    wp_enqueue_style( 'smartview-fa', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
    wp_enqueue_style( 'smartview', SMARTVIEW_URL . 'assets/css/admin' . $suffix . '.css', array(), SMARTVIEW_VER );
    wp_enqueue_script( 'smartview', SMARTVIEW_URL . 'assets/js/admin' . $suffix . '.js', array( 'jquery' ), SMARTVIEW_VER );
    wp_localize_script( 'smartview', 'smartview_vars', array(
        'image_media_button'    => __( 'Insert Image', 'smartview' ),
        'image_media_title'     => __( 'Select Image', 'smartview' ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'smartview_admin_scripts', 100 );


/**
 * Load frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function smartview_scripts() {
    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Get options
    $modal_theme    = smartview_get_option( 'modal_theme', 'example4' );

    wp_enqueue_script( 'smartview-mobile', SMARTVIEW_URL . 'assets/js/isMobile.js', array( 'jquery' ), '0.3.6' );
    wp_enqueue_style( 'smartview-colorbox', SMARTVIEW_URL . 'assets/js/colorbox/' . $modal_theme . '/colorbox.css', array(), '1.6.0' );
    wp_enqueue_script( 'smartview-colorbox', SMARTVIEW_URL . 'assets/js/colorbox/jquery.colorbox-min.js', array( 'jquery' ), '1.6.0' );
    wp_enqueue_style( 'smartview', SMARTVIEW_URL . 'assets/css/smartview' . $suffix . '.css', array(), SMARTVIEW_VER );
    wp_enqueue_script( 'smartview', SMARTVIEW_URL . 'assets/js/smartview' . $suffix . '.js', array(), SMARTVIEW_VER );
    wp_localize_script( 'smartview', 'smartview_vars', array(
        'modal_effect'  => smartview_get_option( 'modal_effect', 'none' ),
        'modal_speed'   => smartview_get_option( 'modal_speed', 350 ),
        'modal_title'   => smartview_parse_title_tags( smartview_get_option( 'modal_title', __( 'Brought to you by', 'smartview' ) . ' {sitename}' ) ),
        'modal_opacity' => smartview_get_option( 'modal_opacity', 0.85 ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'smartview_scripts' );
