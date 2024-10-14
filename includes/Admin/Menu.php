<?php

namespace ContactBook\Admin;

class Menu {

    public function __construct() {

        // add admin_menu
        add_action( 'admin_menu', [$this, 'cb_admin_menu'] );

        // admin script hook
        add_action( 'admin_enqueue_scripts', [$this, 'cb_admin_enqueue_scripts'] );

    }

    /**
     * Fires when enqueuing scripts for all admin pages.
     *
     * @param string $page_name The current admin page.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function cb_admin_enqueue_scripts( string $page_name ) {
        if ( 'toplevel_page_contactbook' !== $page_name ) {
            return;
        }
        $asset_file = CB_PATH . 'build/index.asset.php';

        if ( !file_exists( $asset_file ) ) {
            return;
        }

        $asset = include $asset_file;

        wp_register_script(
            'contact-book',
            CB_URL . '/build/index.js',
            $asset['dependencies'],
            $asset['version'],
            array(
                'in_footer' => true,
            )
        );

        wp_register_style(
            'contact-book',
            CB_URL . '/build/index.css',
        );

        /** call css and script */
        wp_enqueue_script( 'contact-book' );
        wp_enqueue_style( 'contact-book' );

        // localize script
        wp_localize_script('contact-book', 'ContactBook', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            '_ajax_nonce' => wp_create_nonce('contact-book'),
            'edit_nonce' => wp_create_nonce('edit-contact'),
            'delete_nonce' => wp_create_nonce('delete-nonce')
        ));

    }

    /**
     * Fires before the administration menu loads in the admin.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function cb_admin_menu() {
        $capability = 'manage_options';
        $parent_slug = 'contactbook';

        add_menu_page(
            __( 'Contact Book', 'contactbook' ),
            __( 'Contact Book', 'contactbook' ),
            $capability,
            $parent_slug,
            array( $this, 'cb_contactbook_admin_menu' ),
            'dashicons-id-alt',
            25
        );

        add_submenu_page(
            $parent_slug,
            __( 'All Contacts', 'contactbook' ),
            __( 'All Contacts', 'contactbook' ),
            $capability,
            $parent_slug,
            array( $this, 'cb_contactbook_admin_menu' ),
        );

        add_submenu_page(
            $parent_slug,
            __( 'Add New Contact', 'contactbook' ),
            __( 'Add New Contact', 'contactbook' ),
            $capability,
            'admin.php?page=contactbook#/add-new'
        );
    }

    /**
     * Admin menu callback
     *
     * @return void
     */
    public function cb_contactbook_admin_menu() {
        echo '<div class="wrap"><div id="contactbook-dashboard"></div></div>';
    }

}