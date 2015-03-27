<?php
/**
 * Register settings
 *
 * @package     SmartView\Admin\Settings\Register
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Retrieve the settings tabs
 *
 * @since       1.0.0
 * @return      array $tabs The registered settings tabs
 */
function smartview_get_settings_tabs() {
    $settings = smartview_get_registered_settings();

    $tabs               = array();
    $tabs['general']    = __( 'General', 'smartview' );
    $tabs['modal']      = __( 'Modal Style', 'smartview' );
    
    return apply_filters( 'smartview_settings_tabs', $tabs );
}


/**
 * Retrieve the array of plugin settings
 *
 * @since       1.0.0
 * @return      array $smartview_settings The registered settings
 */
function smartview_get_registered_settings() {
    $smartview_settings = array(
        // General Settings
        'general' => apply_filters( 'smartview_settings_general', array(
            array(
                'id'        => 'general_header',
                'name'      => __( 'General Settings', 'smartview' ),
                'desc'      => '',
                'type'      => 'header'
            ),
            array(
                'id'        => 'apply_on',
                'name'      => __( 'Apply SmartView On', 'smartview' ),
                'desc'      => __( 'Select the content types to use SmartView on.', 'smartview' ),
                'type'      => 'multicheck',
                'options'   => smartview_get_types()
            )
        ) ),
        // Modal Styles
        'modal' => apply_filters( 'smartview_settings_modal', array(
            array(
                'id'        => 'modal_header',
                'name'      => __( 'Modal Style Settings', 'smartview' ),
                'desc'      => '',
                'type'      => 'header'
            ),
            array(
                'id'        => 'modal_theme',
                'name'      => __( 'Modal Theme', 'smartview' ),
                'desc'      => __( 'Select the modal window theme to use.', 'smartview' ),
                'type'      => 'select',
                'options'   => array(
                    'example1'  => __( 'Elegant', 'smartview' ),
                    'example2'  => __( 'Light', 'smartview' ),
                    'example3'  => __( 'Dark', 'smartview' ),
                    'example4'  => __( 'Traditional', 'smartview' ),
                    'example5'  => __( 'Framed', 'smartview' )
                ),
                'std'       => 'example4'
            ),
            array(
                'id'        => 'modal_effect',
                'name'      => __( 'Transition Effect', 'smartview' ),
                'desc'      => __( 'Select the effect to use on modal window open/close.', 'smartview' ),
                'type'      => 'select',
                'options'   => array(
                    'none'      => __( 'None', 'smartview' ),
                    'elastic'   => __( 'Elastic', 'smartview' ),
                    'fade'      => __( 'Fade', 'smartview' )
                ),
                'std'       => 'none'
            ),
            array(
                'id'        => 'modal_speed',
                'name'      => __( 'Transition Speed', 'smartview' ),
                'desc'      => __( 'Set the speed of the modal elastic and fade transitions in milliseconds. (default: 350)', 'smartview' ),
                'type'      => 'number',
                'size'      => 'small',
                'min'       => 1,
                'step'      => 1,
                'std'       => 350
            ),
            array(
                'id'        => 'modal_opacity',
                'name'      => __( 'Overlay Opacity', 'smartview' ),
                'desc'      => __( 'Set the opacity of the modal window overlay. (default: .85)', 'smartview' ),
                'type'      => 'number',
                'size'      => 'small',
                'min'       => 0,
                'max'       => 1,
                'step'      => 0.01,
                'std'       => 0.85
            ),
            array(
                'id'        => 'modal_width',
                'name'      => __( 'Modal Width', 'smartview' ),
                'desc'      => __( 'Set the width of the modal window in \'px\' or \'%\'. (default: 80%)', 'smartview' ),
                'type'      => 'text',
                'size'      => 'small',
                'std'       => '80%'
            ),
            array(
                'id'        => 'modal_height',
                'name'      => __( 'Modal Height', 'smartview' ),
                'desc'      => __( 'Set the height of the modal window in \'px\' or \'%\'. (default: 80%)', 'smartview' ),
                'type'      => 'text',
                'size'      => 'small',
                'std'       => '80%'
            ),
            array(
                'id'        => 'modal_title',
                'name'      => __( 'Modal Window Title', 'smartview' ),
                'desc'      => __( 'Specify a custom title for the modal window. HTML is accepted.', 'smartview' ) . '<br />' . __( 'Available template tags:', 'smartview' ) . '<br />' . smartview_get_title_tags(),
                'type'      => 'text',
                'std'       => __( 'Brought to you by', 'smartview' ) . ' {sitename}'
            )
        ) )
    );

    return apply_filters( 'smartview_registered_settings', $smartview_settings );
}


/**
 * Retrieve an option
 *
 * @since       1.0.0
 * @global      array $smartview_options The SmartView options
 * @return      mixed
 */
function smartview_get_option( $key = '', $default = false ) {
    global $smartview_options;

    $value = ! empty( $smartview_options[$key] ) ? $smartview_options[$key] : $default;
    $value = apply_filters( 'smartview_get_option', $value, $key, $default );

    return apply_filters( 'smartview_get_option_' . $key, $value, $key, $default );
}


/**
 * Retrieve all options
 *
 * @since       1.0.0
 * @return      array $smartview_options The SmartView options
 */
function smartview_get_settings() {
    $smartview_settings = get_option( 'smartview_settings' );

    if( empty( $smartview_settings ) ) {
        $smartview_settings = array();

        update_option( 'smartview_settings', $smartview_settings );
    }

    return apply_filters( 'smartview_get_settings', $smartview_settings );
}


/**
 * Add settings sections and fields
 *
 * @since       1.0.0
 * @return      void
 */
function smartview_register_settings() {
    if( get_option( 'smartview_settings' ) == false ) {
        add_option( 'smartview_settings' );
    }

    foreach( smartview_get_registered_settings() as $tab => $settings ) {
        add_settings_section(
            'smartview_settings_' . $tab,
            __return_null(),
            '__return_false',
            'smartview_settings_' . $tab
        );

        foreach( $settings as $option ) {
            $name = isset( $option['name'] ) ? $option['name'] : '';

            add_settings_field(
                'smartview_settings[' . $option['id'] . ']',
                $name,
                function_exists( 'smartview_' . $option['type'] . '_callback' ) ? 'smartview_' . $option['type'] . '_callback' : 'smartview_missing_callback',
                'smartview_settings_' . $tab,
                'smartview_settings_' . $tab,
                array(
                    'section'       => $tab,
                    'id'            => isset( $option['id'] )           ? $option['id']             : null,
                    'desc'          => ! empty( $option['desc'] )       ? $option['desc']           : '',
                    'name'          => isset( $option['name'] )         ? $option['name']           : null,
                    'size'          => isset( $option['size'] )         ? $option['size']           : null,
                    'options'       => isset( $option['options'] )      ? $option['options']        : '',
                    'std'           => isset( $option['std'] )          ? $option['std']            : '',
                    'min'           => isset( $option['min'] )          ? $option['min']            : null,
                    'max'           => isset( $option['max'] )          ? $option['max']            : null,
                    'step'          => isset( $option['step'] )         ? $option['step']           : null,
                    'placeholder'   => isset( $option['placeholder'] )  ? $option['placeholder']    : null,
                    'rows'          => isset( $option['rows'] )         ? $option['rows']           : null,
                    'buttons'       => isset( $option['buttons'] )      ? $option['buttons']        : null,
                    'wpautop'       => isset( $option['wpautop'] )      ? $option['wpautop']        : null,
                    'teeny'         => isset( $option['teeny'] )        ? $option['teeny']          : null,
                    'notice'        => isset( $option['notice'] )       ? $option['notice']         : false,
                    'style'         => isset( $option['style'] )        ? $option['style']          : null,
                    'header'        => isset( $option['header'] )       ? $option['header']         : null,
                    'icon'          => isset( $option['icon'] )         ? $option['icon']           : null,
                    'class'         => isset( $option['class'] )        ? $option['class']          : null
                )
            );
        }
    }

    register_setting( 'smartview_settings', 'smartview_settings', 'smartview_settings_sanitize' );
}
add_action( 'admin_init', 'smartview_register_settings' );


/**
 * Settings sanitization
 *
 * @since       1.0.0
 * @param       array $input The value entered in the field
 * @global      array $smartview_options The SmartView options
 * @return      string $input The sanitized value
 */
function smartview_settings_sanitize( $input = array() ) {
    global $smartview_options;

    if( empty( $_POST['_wp_http_referer'] ) ) {
        return $input;
    }
    
    parse_str( $_POST['_wp_http_referer'], $referrer );

    $settings   = smartview_get_registered_settings();
    $tab        = isset( $referrer['tab'] ) ? $referrer['tab'] : 'settings';

    $input = $input ? $input : array();
    $input = apply_filters( 'smartview_settings_' . $tab . '_sanitize', $input );

    foreach( $input as $key => $value ) {
        $type = isset( $settings[$tab][$key]['type'] ) ? $settings[$tab][$key]['type'] : false;

        if( $type ) {
            // Field type specific filter
            $input[$key] = apply_filters( 'smartview_settings_sanitize_' . $type, $value, $key );
        }

        // General filter
        $input[$key] = apply_filters( 'smartview_settings_sanitize', $input[$key], $key );
    }

    if( ! empty( $settings[$tab] ) ) {
        foreach( $settings[$tab] as $key => $value ) {
            if( is_numeric( $key ) ) {
                $key = $value['id'];
            }

            if( empty( $input[$key] ) || ! isset( $input[$key] ) ) {
                unset( $smartview_options[$key] );
            }
        }
    }

    // Merge our new settings with the existing
    $input = array_merge( $smartview_options, $input );

    add_settings_error( 'smartview-notices', '', __( 'Settings updated.', 'smartview' ), 'updated' );

    return $input;
}


/**
 * Sanitize text fields
 *
 * @since       1.0.0
 * @param       array $input The value entered in the field
 * @return      string $input The sanitized value
 */
function smartview_sanitize_text_field( $input ) {
    return trim( $input );
}
add_filter( 'smartview_settings_sanitize_text', 'smartview_sanitize_text_field' );


/**
 * Header callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function smartview_header_callback( $args ) {
    echo '<hr />';
}


/**
 * Checkbox callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_checkbox_callback( $args ) {
    global $smartview_options;

    $checked = isset( $smartview_options[$args['id']] ) ? checked( 1, $smartview_options[$args['id']], false ) : '';

    $html  = '<input type="checkbox" id="smartview_settings[' . $args['id'] . ']" name="smartview_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>&nbsp;';
    $html .= '<label for="smartview_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Color callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the settings
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_color_callback( $args ) {
    global $smartview_options;

    if( isset( $smartview_options[$args['id']] ) ) {
        $value = $smartview_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $default = isset( $args['std'] ) ? $args['std'] : '';
    $size    = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="text" class="smartview-color-picker" id="smartview_settings[' . $args['id'] . ']" name="smartview_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />&nbsp;';
    $html .= '<span class="smartview-color-picker-label"><label for="smartview_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';

    echo $html;
}


/**
 * Editor callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_editor_callback( $args ) {
    global $smartview_options;

    if( isset( $smartview_options[$args['id']] ) ) {
        $value = $smartview_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $rows       = ( isset( $args['rows'] ) && ! is_numeric( $args['rows'] ) ) ? $args['rows'] : '10';
    $wpautop    = isset( $args['wpautop'] ) ? $args['wpautop'] : true;
    $buttons    = isset( $args['buttons'] ) ? $args['buttons'] : true;
    $teeny      = isset( $args['teeny'] ) ? $args['teeny'] : false;

    wp_editor(
        $value,
        'smartview_settings_' . $args['id'],
        array(
            'wpautop'       => $wpautop,
            'media_buttons' => $buttons,
            'textarea_name' => 'smartview_settings[' . $args['id'] . ']',
            'textarea_rows' => $rows,
            'teeny'         => $teeny
        )
    );
    echo '<br /><label for="smartview_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';
}


/**
 * Info callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_info_callback( $args ) {
    global $smartview_options;

    $notice = ( $args['notice'] == true ? '-notice' : '' );
    $class  = ( isset( $args['class'] ) ? $args['class'] : '' );
    $style  = ( isset( $args['style'] ) ? $args['style'] : 'normal' );
    $header = '';

    if( isset( $args['header'] ) ) {
        $header = '<b>' . $args['header'] . '</b><br />';
    }

    echo '<div id="smartview_settings[' . $args['id'] . ']" name="smartview_settings[' . $args['id'] . ']" class="smartview-info' . $notice . ' smartview-info-' . $style . '">';

    if( isset( $args['icon'] ) ) {
        echo '<p class="smartview-info-icon">';
        echo '<i class="fa fa-' . $args['icon'] . ' ' . $class . '"></i>';
        echo '</p>';
    }

    echo '<p class="smartview-info-desc">' . $header . $args['desc'] . '</p>';
    echo '</div>';
}


/**
 * Multicheck callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_multicheck_callback( $args ) {
    global $smartview_options;

    if( ! empty( $args['options'] ) ) {
        foreach( $args['options'] as $key => $option ) {
            $enabled = ( isset( $smartview_options[$args['id']][$key] ) ? $option : NULL );

            echo '<input name="smartview_settings[' . $args['id'] . '][' . $key . ']" id="smartview_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option, $enabled, false ) . ' />&nbsp;';
            echo '<label for="smartview_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br />';
        }
        echo '<p class="description">' . $args['desc'] . '</p>';
    }
}


/**
 * Number callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_number_callback( $args ) {
    global $smartview_options;

    if( isset( $smartview_options[$args['id']] ) ) {
        $value = $smartview_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $max    = isset( $args['max'] ) ? $args['max'] : 999999;
    $min    = isset( $args['min'] ) ? $args['min'] : 0;
    $step   = isset( $args['step'] ) ? $args['step'] : 1;
    $size   = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="smartview_settings[' . $args['id'] . ']" name="smartview_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />&nbsp;';
    $html .= '<label for="smartview_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Password callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the settings
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_password_callback( $args ) {
    global $smartview_options;

    if( isset( $smartview_options[$args['id']] ) ) {
        $value = $smartview_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="password" class="' . $size . '-text" id="smartview_settings[' . $args['id'] . ']" name="smartview_settings[' . $args['id'] . ']" value="' . esc_attr( $value )  . '" />&nbsp;';
    $html .= '<label for="smartview_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Radio callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_radio_callback( $args ) {
    global $smartview_options;

    if( ! empty( $args['options'] ) ) {
        foreach( $args['options'] as $key => $option ) {
            $checked = false;

            if( isset( $smartview_options[$args['id']] ) && $smartview_options[$args['id']] == $key ) {
                $checked = true;
            } elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $smartview_options[$args['id']] ) ) {
                $checked = true;
            }

            echo '<input name="smartview_settings[' . $args['id'] . ']" id="smartview_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>&nbsp;';
            echo '<label for="smartview_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br />';
        }

        echo '<p class="description">' . $args['desc'] . '</p>';
    }
}


/**
 * Select callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_select_callback( $args ) {
    global $smartview_options;

    if( isset( $smartview_options[$args['id']] ) ) {
        $value = $smartview_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

    $html = '<select id="smartview_settings[' . $args['id'] . ']" name="smartview_settings[' . $args['id'] . ']" placeholder="' . $placeholder . '" />';

    foreach( $args['options'] as $option => $name ) {
        $selected = selected( $option, $value, false );

        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    }

    $html .= '</select>&nbsp;';
    $html .= '<label for="smartview_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Text callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_text_callback( $args ) {
    global $smartview_options;

    if( isset( $smartview_options[$args['id']] ) ) {
        $value = $smartview_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="text" class="' . $size . '-text" id="smartview_settings[' . $args['id'] . ']" name="smartview_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) )  . '" />&nbsp;';
    $html .= '<label for="smartview_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Textarea callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_textarea_callback( $args ) {
    global $smartview_options;

    if( isset( $smartview_options[$args['id']] ) ) {
        $value = $smartview_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $html  = '<textarea class="large-text" cols="50" rows="5" id="smartview_settings[' . $args['id'] . ']" name="smartview_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>&nbsp;';
    $html .= '<label for="smartview_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Upload callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $smartview_options The SmartView options
 * @return      void
 */
function smartview_upload_callback( $args ) {
    global $smartview_options;

    if( isset( $smartview_options[$args['id']] ) ) {
        $value = $smartview_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="text" class="' . $size . '-text" id="smartview_settings[' . $args['id'] . ']" name="smartview_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />&nbsp;';
    $html .= '<span><input type="button" class="smartview_settings_upload_button button-secondary" value="' . __( 'Upload File', 'smartview' ) . '" /></span>&nbsp;';
    $html .= '<label for="smartview_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Hook callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function smartview_hook_callback( $args ) {
    do_action( 'smartview_' . $args['id'] );
}


/**
 * Missing callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function smartview_missing_callback( $args ) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'smartview' ), $args['id'] );
}
