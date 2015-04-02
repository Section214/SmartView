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

    $types['excerpt'] = __( 'Excerpts', 'smartview' );

    return $types;
}


/**
 * Retrieve a formatted list of all available template tags
 *
 * @since       1.0.0
 * @return      string $tags The tags list
 */
function smartview_get_title_tags() {
    $tags = apply_filters( 'smartview_title_tags', array(
        'title'     => __( 'Post/page title', 'smartview' ),
        'sitename'  => __( 'Your site name', 'smartview' )
    ) );

    $list = '<div class="smartview-tag-list">';

    foreach( $tags as $tag => $description ) {
        $list .= '<div class="smartview-tag-list-tag">{' . $tag . '}</div><div class="smartview-tag-list-description">' . $description . '</div><br />';
    }

    $list .= '</div>';

    return $list;
}


/**
 * Parse template tags
 *
 * @since       1.0.0
 * @param       string $content The content to parse
 * @global      object $wp_query The WordPress query object
 * @return      string $content The content with tags parsed
 */
function smartview_parse_title_tags( $content ) {
    global $wp_query;

    // Ensure at least one tag exists
    if( strpos( $content, '{' ) !== false ) {
        if( is_single() && isset( $wp_query->queried_object->post_title ) ) {
            $title = $wp_query->queried_object->post_title;
        } else {
            $title = '';
        }

        $content = str_replace( '{title}', $title, $content );
        $content = str_replace( '{sitename}', get_bloginfo( 'name' ), $content );
    }
    
    return $content;
}


/**
 * Check to see if X-Frame-Option is set to SAMEORIGIN for a given site
 *
 * @since       1.0.0
 * @param       string $url The URL to check
 * @return      bool $ret True if SAMEORIGIN, false otherwise
 */
function smartview_check_sameorigin( $url = '' ) {
    $ret = false;

    $url = parse_url( $url );
    $url = $url['scheme'] . '://' . $url['host'];

    if( $trans = get_transient( 'smartview_' . $url['host'] ) === false ) {
        $args = array(
            'timeout'   => 5,
            'sslverify' => false
        );

        $response = wp_remote_get( $url, $args );
        $response = wp_remote_retrieve_headers( $response );

        if( array_key_exists( 'x-frame-options', $response ) ) {
            if( strtolower( $response['x-frame-options'] ) == 'deny' ) {
                $ret = true;
            } elseif( strtolower( $response['x-frame-options'] ) == 'sameorigin' ) {
                $ret = true;
            }
        }

        if( $ret ) {
            set_transient( 'smartview_' . $url['host'], 'true', WEEK_IN_SECONDS );
        } else {
            set_transient( 'smartview_' . $url['host'], 'false', WEEK_IN_SECONDS );
        }
    } else {
        if( $transe == 'true' ) {
            $ret = true;
        }
    }            

    return $ret;
}
