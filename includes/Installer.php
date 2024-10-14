<?php

namespace ContactBook;

class Installer {

    public function run() {
        $this->add_version();
        $this->create_tables();
    }

    /**
     * Add time and version on DB
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_version() {
        $installed = get_option( 'cb_installed' );

        if ( !$installed ) {
            update_option( 'cb_installed', time() );
        }

        update_option( 'contactbook_version', CB_VERSION );
    }

    /**
     * Create database table
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function create_tables() {
        
        global $wpdb;

        $wp_contact_book = $wpdb->prefix . 'contactbook';

        $charset_collate = $wpdb->get_charset_collate();

        $query = "CREATE TABLE IF NOT EXISTS $wp_contact_book
            (
            id         INT(11) UNSIGNED NOT NULL auto_increment,
            name       VARCHAR(100) NOT NULL DEFAULT '\"\"',
            email       VARCHAR(100) NOT NULL DEFAULT '\"\"',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
            ) $charset_collate";

        if ( !function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta( $query );

    }
}