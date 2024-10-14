<?php

/**
 * Contact Book
 *
 * @author Robiul Islam <therobiulislam12@gmail.com>
 * @copyright 2024 Robiul Islam
 * @license GPL v2 or later
 *
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Book
 * Plugin URI:        #
 * Description:       Simple user contact management plugin for everyone
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Robiul Islam
 * Author URI:        https://robiul-islam.netlify.app
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 * Text Domain:       contactbook
 */

// If direct access plugin
if ( !defined( 'ABSPATH' ) ) {
    header( 'location: /' );
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class Wp_ContactBook {

    // create a $_instance variable
    public static $_instance = null;

    /**
     * Plugin version
     *
     *
     * @var string
     */
    const version = '1.0.2';

    /**
     * Private constructor function
     */
    private function __construct() {

        // define constants
        $this->define_constants();

        // call activation method
        register_activation_hook( __FILE__, array( $this, 'cb_active' ) );

        // call plugin_loaded hook
        add_action( 'plugins_loaded', array( $this, 'cb_init_plugin' ) );

    }

    /**
     * Initialize the Wp_ContactBook Plugin
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function cb_init_plugin() {

        load_plugin_textdomain( 'contactbook', false, dirname( plugin_basename( __FILE__ ) ) . "/languages" );

        // settings action add
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [$this, 'cb_menu_page'] );

        if ( is_admin() ) {
            new ContactBook\Admin();
        } else {

        }

    }

    /**
     * Do what you want when plugin activation
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function cb_active() {
        $cb_installer = new ContactBook\Installer();

        $cb_installer->run();
    }

    /**
     * Define all constant for your plugin
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function define_constants() {
        define( 'CB_VERSION', self::version );
        define( 'CB_FILE', __FILE__ );
        define( 'CB_PATH', plugin_dir_path( __FILE__ ) );
        define( 'CB_URL', plugins_url( '', CB_FILE ) );
        define( 'CB_ASSETS_URL', CB_URL . '/assets' );
    }

    /**
     * Create a function instance
     *
     * @since 1.0.0
     *
     * @return Wp_ContactBook
     */
    public static function getInstance() {
        if ( !self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Added a links on plugin
     *
     * @param array $links
     *
     * @return string[]
     */
    public function cb_menu_page( $links ) {
        $settings_links = array(
            '<a href="' . admin_url( 'admin.php?page=contactbook' ) . '">Settings</a>',
        );
        $links = array_merge( $settings_links, $links );
        return $links;
    }

}

/**
 * Create a function for class call
 *
 * @since 1.0.0
 *
 * @return Wp_ContactBook
 */
function wp_contact_book() {
    return Wp_ContactBook::getInstance();
}

// call function for create instance
wp_contact_book();