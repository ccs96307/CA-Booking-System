<?php

if ( !class_exists( 'CAM' ) ) {
    /**
     * Main CAM Class
     *
     * @class CAM
     * @version 0.0.1
     *
     * @method CAM_bbPress_API bbPress_API()
     * @method CAM_Followers_API Friends_API()
     * @method CAM_Instagram_API Instagram_API()
     * @method CAM_MailChimp MailChimp()
     * @method CAM_Messageing_API Messaging_API()
     * @method CAM_myCRED myCRED()
     * @method CAM_Notices Notices()
     * @method CAM_Online Online()
     * @method CAM_Profile_Completeness_API Profile_Completeness_API()
     * @method CAM_reCAPTCHA reCAPTCHA()
     * @method CAM_Reviews Reviews()
     * @method CAM_Acitvity_API Activity_API()
     * @method CAM_Social_Login_API Social_Login_API()
     * @method CAM_User_Tags User_Tags()
     * @method CAM_Verified_Users_API Verified_Users_API()
     * @method CAM_WooCommerce_API WooCommerce_API()
     * @method CAM_Terms_Conditions Terms_Conditions()
     * @method CAM_Private_Content Private_Content()
     * @method CAM_User_Locations User_Locations()
     * @method CAM_Photos_API Photos_API()
     * @method CAM_Groups Groups()
     * @method CAM_Frontend_Posting Frontend_Posting()
     * @method CAM_Notes Notes()
     * @method CAM_User_Bookmarks User_Bookmarks()
     * @method CAM_Unsplash Unsplash()
     * @method CAM_ForumWP ForumWP()
     * @method CAM_Profile_Tabs Profile_Tabs()
     * @method CAM_JobBoardWP CAM_JobBoardWP()
     * @method CAM_Google_Authenticator Google_Authenticator()
     */
    final class CAM extends CAM_Functions {
        // @var CAM the single instance of the class
        protected static $instance = null;

        // @var array all plugin's classes
        public $classes = array();

        /**
         * @var bool Old variable
         * @todo deprecate this variable
         */
        public $is_filtering;

        /**
         * WP Native permalinks turned on?
         * @var
         */
        public $is_permalinks;


        /**
         * Main CAM Instance
         * Ensures only one instance of CAM is loaded or can be loaded.
         *
         * @since 1.0
         * @static
         * @see CAM()
         * @return CAM - Main instance
         */ 
        static public function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
                self::$instance->_cam_construct();
            }

            return self::$instance;
        }

        /**
         * Create plugin classes - not sure if it needs!!!!!!
         *
         * @since 1.0
         * @see CAM()
         *
         * @param $name
         * @param array $params
         * @return mixed
         */
        public function __call( $name, array $params ) {
            if (empty( $this->classes[ $name ] )) {
                /**
                 * CAM hook
                 * 
                 * @type filter
                 * @title cam_call_object_{$class_name}
                 * @description Extend call classes of Extensions for use CAM()->class_name()->method|function
                 * @input_vars
                 * [{"var":"$class", "type":"object","desc":"Class Instance"}]
                 *
                 * @change_log
                 * ["Since: 0.0.1"]
                 * @usage add_filter( 'cam_call_object_{$class_name}', 'function_name', 10, 1 );
                 * @example
                 * <?php
                 * add_filter( 'cam_call_object_{$class_name}', 'my_extension_class', 10, 1 );
                 * function my_extension_class( $class ) {
                 *     // your code here
                 *     return $class;
                 * }
                 * ?>
                 */
                $this->classes[ $name ] = apply_filters( 'cam_call_object_' . $name, false);
            }
            return $this->classes[ $name ];
        }


        /**
         * Function for add classes to $this->classes
         * for run using CAM()
         *
         * @since 0.0.1
         *
         * @param string $class_name
         * @param bool $instance
         */
        public function set_class( $class_name, $instance=flase ) {
            if (empty( $this->classes[ $class_name ] )) {
                $class = 'CAM_' . $class_name;
                $this->classes[ $class_name ] = $instance ? $class::instance() : new $class;
            }
        }

        /**
         * Cloning is forbidden
         * @since 0.0.1
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'CA-member' ), '0.0.1' );
        }

        /**
         * Unserializing instances of this class is forbidden
         * @since 0.0.1
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'CA-member' ), '0.0.1' );
        }

        /**
         * CAM constructor.
         *
         * @since 0.0.1
         */
        public function __construct() {
            parent::__construct();
        }

        /**
         * CAM pseudo-constructor
         *
         * @since 0.0.1
         */
        function _cam_construct() {
            // Register autoloader for include CAM classes
            spl_autoload_register( array( $this, 'cam__autoloader' ) );

            if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
                if ( get_option( 'permalink_structure' ) ) {
                    $this->is_permalinks = true;
                }
            }

            $this->is_filtering = 0;
            $this->honeypot = 'cam_request';

            // textdomain loading
            $this->localize();

            // include CAM classes
            $this->includes();

            // include hook files
            add_action( 'plugins_loaded', array( &$this, 'init' ), 0 );

            // run hook for extensions init
            add_action( 'plugin_loaded', array( &$this, 'extensions_init', -19 ) );
            add_action( 'init', array( &$this, 'old_update_patch' ), 0 );

            // run activation
            register_activation_hook( cam_plugin, array( &$this, 'activation' ) );

            if ( is_multisite() && !defined( 'DOING_AJAX' ) ) {
                add_action( 'wp_loaded', array( $this, 'maybe_network_activation' ) );
            }
            
            // init widgets
            add_action( 'widgets_init', array( &$this, 'widgets_init' ) );

            // include short non class functions
            require_once 'cam-short-functions.php';
            require_once 'cam-deprecated-functions.php';
        }
    }

    /**
     * Load CAM textdomain
     * 
     * 'ultimate-member' by default
     */
    function localize() {
        $language_locale = ( get_locale() != '' ) ? get_locale() : 'en_US';
    }




?>
