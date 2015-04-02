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

    $parsed_url = parse_url( $url );
    $actual_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];

    if( $trans = get_transient( 'smartview_' . $parsed_url['host'] ) === false ) {
        $args = array(
            'timeout'   => 5,
            'sslverify' => false
        );

        $response = wp_remote_get( $actual_url, $args );
        $response = wp_remote_retrieve_headers( $response );

        if( array_key_exists( 'x-frame-options', $response ) ) {
            if( strtolower( $response['x-frame-options'] ) == 'deny' ) {
                $ret = true;
            } elseif( strtolower( $response['x-frame-options'] ) == 'sameorigin' ) {
                $ret = true;
            }
        }

        if( $ret ) {
            set_transient( 'smartview_' . $parsed_url['host'], 'true', WEEK_IN_SECONDS );
        } else {
            set_transient( 'smartview_' . $parsed_url['host'], 'false', WEEK_IN_SECONDS );
        }
    } else {
        if( $trans == 'true' ) {
            $ret = true;
        }
    }            

    return $ret;
}


/**
 * Add Menufication help if Menufication is installed
 *
 * @since       1.0.2
 * @param       array $settings The original settings array
 * @return      array $settings The modified settings array
 */
function smartview_maybe_add_menufication_help( $settings ) {
    if( class_exists( 'Menufication' ) ) {
        $new_settings = array(
            array(
                'id'        => 'menufication_help',
                'name'      => __( 'Menufication', 'smartview' ),
                'desc'      => '',
                'type'      => 'hook'
            )
        );

        $settings = array_merge( $settings, $new_settings );
    }

    return $settings;
}
add_filter( 'smartview_settings_help', 'smartview_maybe_add_menufication_help' );


/**
 * Help callback for Menufication plugin
 *
 * @since       1.0.2
 * @return      void
 */
function smartview_menufication_help() {
    $html  = '<p>' . __( 'The Menufication plugin is known to cause problems with certain mobile applications, including SmartView. This is because the developer didn\'t take other plugins into account and decided not to provide any hooks to allow us to remove it on pages that <em>shouldn\'t</em> have it active. As such, we provide a tweaked version of Menufication which <em>does</em> allow us to disable it where it doesn\'t belong! If you haven\'t already done so, please download the below plugin and replace your current copy of Menufication with it.', 'smartview' ) . '</p>';
    $html .= '<p>' . sprintf( __( 'If the author of Menufication notifies you that there has been an update, and we have not yet provided a matching update for SmartView and our fork of Menufication, please notify us immediately by emailing %s, and DO NOT install the upgrade they provide!', 'smartview' ), '<a href="mailto:support@section214.com">support@section214.com</a>' ) . '</p>';
    $html .= '<br />';
    $html .= '<p><a href="' . SMARTVIEW_URL . 'assets/plugins/menufication.zip" class="button">' . __( 'Download Menufication', 'smartview' ) . '</a></p>';

    echo $html;
}
add_action( 'smartview_menufication_help', 'smartview_menufication_help' );


/**
 * Help callback for permalinks
 *
 * @since       1.0.2
 * @return      void
 */
function smartview_permalinks_help() {
    $html  = '<p>' . __( 'If you are getting 404 errors on SmartBar enabled pages, you may need to flush the WordPress rewrite rules cache. Click the button below to do so now.', 'smartview' ) . '</p>';
    $html .= '<br />';
    $html .= '<p><a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'smartview-action' => 'flush_permalinks' ) ), 'smartview-permalinks-nonce' ) ) . '" class="button">' . __( 'Flush Rewrite Rules', 'smartview' ) . '</a></p>';

    echo $html;
}
add_action( 'smartview_permalinks_help', 'smartview_permalinks_help' );


/**
 * Flush permalinks
 *
 * @since       1.0.2
 * @return      void
 */
function smartview_flush_permalinks() {
    // Verify nonce
    if( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'smartview-permalinks-nonce' ) ) {
        return;
    }

    flush_rewrite_rules();

    add_action( 'admin_notices', 'smartview_permalinks_notice' );
}
add_action( 'smartview_flush_permalinks', 'smartview_flush_permalinks' );


/**
 * Display notice on permalinks flush
 *
 * @since       1.0.2
 * @return      void
 */
function smartview_permalinks_notice() {
    echo '<div class="updated"><p>' . __( 'Rewrite rules flushed successfully.', 'smartview' ) . '</p></div>';
}
