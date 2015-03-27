<?php
/**
 * Helper functions
 *
 * @package     SmartView\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Retrieve an array of possible content types
 *
 * @since       1.0.0
 * @return      array $types The available types
 */
function smartview_get_types() {
    $types = array();
    
    $post_types = get_post_types( array(
        'public'    => true,
    ), 'objects' );

    // Attachments are irrelevant
    unset( $post_types['attachment'] );

    foreach( $post_types as $post_type ) {
        $types[$post_type->name] = $post_type->labels->name;
    }

    return $types;
}
