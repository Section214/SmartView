<?php
/**
 * Admin actions
 *
 * @package     SmartView\Admin\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Process all actions sent via POST and GET by looking for the 'smartview-action'
 * request and running do_action() to call the function
 *
 * @since       1.0.0
 * @return      void
 */
function smartview_process_actions() {
    if( isset( $_POST['smartview-action'] ) ) {
        do_action( 'smartview_' . $_POST['smartview-action'], $_POST );
    }

    if( isset( $_GET['smartview-action'] ) ) {
        do_action( 'smartview_' . $_GET['smartview-action'], $_GET );
    }
}
add_action( 'admin_init', 'smartview_process_actions' );
