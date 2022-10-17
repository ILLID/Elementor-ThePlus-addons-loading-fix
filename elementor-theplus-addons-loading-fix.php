<?php
/*
Plugin Name: Elementor + ThePlus addons loading fix
Description: Fix the issue with Elementor editor loading when using together with ThePlus plugin addons
Version: 1.00
Author: ILLID
Author URI: https://wpmichael.com
Text Domain: elementor-theplus-addons-loading-fix
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Elementor_ThePlus_Fix {

    /**
     * @var Elementor_ThePlus_Fix The single instance of the class
     */
    protected static $_instance = null;

    /**
     * Main Elementor_ThePlus_Fix Instance
     *
     * Ensures only one instance of Elementor_ThePlus_Fix is loaded or can be loaded.
     *
     * @static
     * @return Elementor_ThePlus_Fix - Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'elementor/ajax/register_actions', array( $this, 'ajax_get_widgets_config_action' ), 999 );
    }

    /*
     * Register Elementor ajax actions
     */
    public function ajax_get_widgets_config_action( $ajax_manager ) {
        $ajax_manager->register_ajax_action( 'get_widgets_config',  array( $this, 'get_widgets_config' ) );
    }

    /*
     * Exclude all ThePlus widgets controls from config
     */
    public function get_widgets_config( array $data ) {

        $config = array();
        $widget_types = Elementor\Plugin::$instance->widgets_manager->get_widget_types();

        foreach ( $widget_types as $widget_key => $widget ) {

            if ( isset( $data['exclude'][ $widget_key ] ) ) {
                continue;
            }

            if ( strpos( $widget_key, 'tp-' ) === 0 ) {
                continue;
            }

            $controls = $widget->get_stack( false )['controls'];
            $config[ $widget_key ] = [
                'controls' => $controls,
                'tabs_controls' => $widget->get_tabs_controls(),
            ];

        }

        return $config;

    }

}

Elementor_ThePlus_Fix::instance();