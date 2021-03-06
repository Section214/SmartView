<?php
/**
 * Plugin Name:     SmartView
 * Plugin URI:      http://section214.com
 * Description:     Provides a simple, mobile-friendly solution for displaying external content without leaving a site
 * Version:         2.0.1
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     smartview
 *
 * @package         SmartView
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 * @copyright       Copyright (c) 2014, Daniel J Griffiths
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


if( ! class_exists( 'SmartView' ) ) {


    /**
     * Main SmartView class
     *
     * @since       1.0.0
     */
    class SmartView {


        /**
         * @access      private
         * @since       1.0.0
         * @var         SmartView $instance The one true SmartView
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      self::$instance The one true SmartView
         */
        public static function instance() {
            if( ! self::$instance ) {
                self::$instance = new SmartView();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'SMARTVIEW_VER', '2.0.0' );

            // Plugin path
            define( 'SMARTVIEW_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'SMARTVIEW_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include required files
         *
         * @access      private
         * @since       1.0.0
         * @global      array $smartview_options The SmartView options array
         * @return      void
         */
        private function includes() {
            global $smartview_options;

            require_once SMARTVIEW_DIR . 'includes/admin/settings/register.php';
            $smartview_options = smartview_get_settings();

            require_once SMARTVIEW_DIR . 'includes/libraries/simple_html_dom.php';
            require_once SMARTVIEW_DIR . 'includes/scripts.php';
            require_once SMARTVIEW_DIR . 'includes/functions.php';

            if( is_admin() ) {
                require_once SMARTVIEW_DIR . 'includes/admin/actions.php';
                require_once SMARTVIEW_DIR . 'includes/admin/pages.php';
                require_once SMARTVIEW_DIR . 'includes/admin/settings/display.php';
            }

            if( ! class_exists( 'WP_GitHub_Updater' ) ) {
                require_once SMARTVIEW_DIR . 'includes/libraries/updater/updater.php';
            }
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            // Maybe add SmartView
            add_filter( 'the_content', array( $this, 'maybe_add_smartview' ), PHP_INT_MAX, 1 );

            // Add rewrite endpoint
            add_action( 'init', array( $this, 'add_endpoint' ) );

            // Add query var
            add_filter( 'query_vars', array( $this, 'query_vars' ), -1 );

            // Handle redirect
            add_action( 'wp_head', array( $this, 'redirect' ) );

            if( is_admin() ) {
                $update = new WP_GitHub_Updater( array(
                    'slug'                  => plugin_basename( __FILE__ ),
                    'proper_folder_name'    => 'smartview',
                    'api_url'               => 'https://api.github.com/repos/Section214/SmartView',
                    'raw_url'               => 'https://raw.github.com/Section214/SmartView/master',
                    'github_url'            => 'https://github.com/Section214/SmartView',
                    'zip_url'               => 'https://github.com/Section214/SmartView/zipball/master',
                    'sslverify'             => true,
                    'requires'              => '3.0',
                    'tested'                => '4.2.2',
                    'readme'                => 'README.md'
                ) );
            }
        }


        /**
         * Maybe add SmartView to pages/posts
         *
         * @access      public
         * @since       1.0.0
         * @param       string $content The page/post content
         * @global      object $post The WordPress object for this post/page
         * @return      string $content The modified content
         */
        public function maybe_add_smartview( $content ) {
            global $post;

            // Bail if we shouldn't apply here
            $apply_on = smartview_get_option( 'apply_on', false );
            if( ! array_key_exists( $post->post_type, $apply_on ) ) {
                return $content;
            }

            if( ! array_key_exists( 'excerpt', $apply_on ) ) {
                if( ! is_object( $post ) || empty( $post->ID ) ) {
                    return $content;
                }
            }

            // Get html content
            $html = str_get_html( $content );

            // What ISN'T an external domain?
            $domain = get_home_url();

            // Should we use the SmartBar?
            $smartbar = false;

            if( wp_is_mobile() || smartview_get_option( 'desktop_type', 'modal' ) == 'smartbar' ) {
                $smartbar = true;
            }

            // Do the magic!
            if( is_object( $html ) ) {
                foreach( $html->find( 'a' ) as $link ) {
                    if( ! preg_match( '/^.*' . preg_quote( $domain, '/' ) . '.*/i', $link->href ) ) {
                        if( smartview_check_sameorigin( $link->href ) ) {
                            if( smartview_get_option( 'sameorigin_fallback', false ) ) {
                                $link->target = '_blank';
                            } else {
                                if( $smartbar ) {
                                    $link->href = $domain . '/smartview?url=' . bin2hex( $link->href );
                                    $link->target = '_self';
                                } else {
                                    if( isset( $link->class ) ) {
                                        if( ! strpos( $link->class, 'smartview-error' ) ) {
                                            $link->class = $link->class . ' smartview-error';
                                        }
                                    } else {
                                        $link->class = 'smartview-error';
                                    }

                                    $link->href = smartview_parse_url( $link->href );
                                }
                            }
                        } else {
                            if( $smartbar ) {
                                $link->href = $domain . '/smartview?url=' . bin2hex( $link->href );
                                $link->target = '_self';
                            } else {
                                if( isset( $link->class ) ) {
                                    if( ! strpos( $link->class, 'smartview' ) ) {
                                        $link->class = $link->class . ' smartview';
                                    }
                                } else {
                                    $link->class = 'smartview';
                                }
                            
                                $link->href = smartview_parse_url( $link->href );
                            }
                        }
                    }

                    // Add nofollow
                    $link->rel = 'nofollow';
                }

                // Reset the content variable
                $content = $html;
            }

            return $content;
        }


        /**
         * Registers a new rewrite endpoint
         *
         * @access      public
         * @since       1.0.0
         * @param       array $rewrite_rules The existing rewrite rules
         * @return      void
         */
        public function add_endpoint( $rewrite_rules ) {
            add_rewrite_endpoint( 'smartview', EP_ALL );
        }


        /**
         * Add our new query var
         *
         * @access      public
         * @since       1.0.0
         * @param       array $vars The existing query vars
         * @return      array $vars The updated query vars
         */
        public function query_vars( $vars ) {
            $vars[] = 'url';

            return $vars;
        }


        /**
         * Listen for the smartview endpoint and handle accordingly
         *
         * @access      public
         * @since       1.0.0
         * @global      object $wp_query The WordPress query object
         * @return      void
         */
        public function redirect() {
            global $wp_query;

            // Bail if this isn't a smartview query
            if( ! isset( $wp_query->query_vars['smartview'] ) ) {
                return;
            }

            $styles = array();
            $styles[] = 'background-color: ' . smartview_get_option( 'smartbar_background_color', '#333333' ) . ';';

            if( $background_image = smartview_get_option( 'smartbar_background_image', false ) ) {
                $styles[] = 'background-image: url( \'' . $background_image . '\' );';
                $styles[] = 'background-repeat: repeat;';
            }

            $html  = '<div class="smartbar" style="' . implode( ' ', $styles ) . '">';

            // Logo
            if( $logo_url = smartview_get_option( 'smartbar_logo', SMARTVIEW_URL . 'assets/img/logo.png' ) ) {
                $height = smartview_get_option( 'smartbar_logo_height', 33 ) . 'px';
                $offset = smartview_get_option( 'smartbar_logo_offset', 0 ) . 'px';

                $html .= '<div class="smartbar-logo-frame">';
                $html .= '<a href="' . get_home_url() . '" class="smartbar-logo-link"><img src="' . $logo_url . '" class="smartbar-logo" style="height: ' . $height . '; margin-top: ' . $offset . '" /></a>';
                $html .= '</div>';
            }

            // Back link
            $font  = smartview_get_option( 'smartbar_back_font', 'Roboto' );

            $html .= '<div class="smartbar-back-frame">';
            $html .= '<span class="smartbar-back-link" style="color: ' . smartview_get_option( 'smartbar_back_color', '#ffffff' ) . '; font-family: \'' . $font . '\'">';
            $html .= smartview_get_option( 'smartbar_back_text', sprintf( __( 'Back to %s', 'smartview' ), get_bloginfo( 'name' ) ) );
            $html .= '</span>';
            $html .= '</div>';
            
            $html .= '</div>';

            echo $html;

            $url = hex2bin( $wp_query->query_vars['url'] );
            $url = smartview_parse_url( $url );

            echo '<script type="text/javascript">document.write(\'<iframe class="smartbar-frame" src="' . $url . '" frameborder="0" noresize="noresize" height="\' + window.innerHeight + \'px" width="\' + window.innerWidth + \'px">test</iframe>\');</script>';

            exit;
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
            $lang_dir = apply_filters( 'smartview_language_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), '' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'smartview', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/smartview/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/smartview/ folder
                load_textdomain( 'smartview', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/smartview/languages/ folder
                load_textdomain( 'smartview', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'smartview', false, $lang_dir );
            }
        }
    }
}


/**
 * The main function responsible for returning the one true SmartView
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      SmartView The one true SmartView
 */
function SmartView() {
    return SmartView::instance();
}
SmartView();
