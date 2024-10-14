<?php

namespace ContactBook\Admin;

class Ajax {

    public function __construct() {

        // insert contact ajax
        add_action( 'wp_ajax_contact_book_form', array( $this, 'cb_contact_book_form_ajax' ) );

        // view contacts
        add_action( 'wp_ajax_contact_book_view', array( $this, 'cb_contact_books_view' ) );

        // edit contact book
        add_action( 'wp_ajax_edit_contact', array( $this, 'cb_contact_edit' ) );

        // single data fetch ajax
        add_action( 'wp_ajax_view_contact', array( $this, 'cb_contact_view' ) );

        // delete contact
        add_action( 'wp_ajax_delete_contact', array( $this, 'cb_delete_contact' ) );

        // all items count
        add_action( 'wp_ajax_contact_books_count', array( $this, 'cb_count_contact_books' ) );
    }

    /**
     * Insert contact on database
     *
     * @return mixed
     */
    public function cb_contact_book_form_ajax() {

        check_ajax_referer( 'contact-book' );

        $name = $_POST['contactname'] ? sanitize_text_field( $_POST['contactname'] ) : '';
        $email = $_POST['contactemail'] ? sanitize_email( $_POST['contactemail'] ) : '';

        $data = [
            'name'  => $name,
            'email' => $email,
        ];

        $insert_id = cb_insert_contact( $data );

        if ( is_wp_error( $insert_id ) ) {
            wp_send_json( ['status' => 'failed', 'message' => 'Something went wrong'], 400 );
            wp_die();
        }

        wp_send_json( ['success' => true, 'message' => 'Contact Inserted Successfully'], 200 );

    }

    public function cb_contact_books_view() {

        $number = isset( $_GET['number'] ) ? intval( $_GET['number'] ) : 0;
        $offset = isset( $_GET['offset'] ) ? intval( $_GET['offset'] ) : 0;

        $args = [
            'number'  => $number,
            'offset'  => $offset,
            'orderby' => 'id',
            'order'   => 'desc',
        ];

        $contacts = cb_get_contacts( $args );

        $data = [
            'success' => true,
            'data'    => $contacts,
        ];

        wp_send_json( $data, 200 );
    }

    public function cb_contact_edit() {

        check_ajax_referer( 'edit-contact' );

        $id = $_POST['id'] ? intval( $_POST['id'] ) : '';
        $name = $_POST['contactname'] ? sanitize_text_field( $_POST['contactname'] ) : '';
        $email = $_POST['contactemail'] ? sanitize_email( $_POST['contactemail'] ) : '';

        $data = [
            'id'    => $id,
            'name'  => $name,
            'email' => $email,
        ];

        $updated_id = cb_update_contact( $data );

        if ( is_wp_error( $updated_id ) ) {
            wp_send_json( ['status' => 'failed', 'message' => 'Something went wrong'], 400 );
            wp_die();
        }

        wp_send_json( ['success' => true, 'message' => 'Contact Updated Successfully'], 200 );

    }

    public function cb_contact_view() {
        $id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
        $item = cb_get_single_address( $id );

        wp_send_json( ['success' => true, 'data' => $item], 200 );
    }

    public function cb_delete_contact() {

        check_ajax_referer( 'delete-nonce' );

        $id = isset( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : 0;

        $delete_id = cb_delete_contact( $id );

        if ( !$delete_id ) {
            wp_send_json( ['status' => 'failed', 'message' => 'Something went wrong'], 400 );
            wp_die();
        }

        wp_send_json( ['success' => true, 'message' => 'Contact Delete Successfully'], 200 );

    }

    public function cb_count_contact_books() {
        $items_length = cb_get_data_count();

        wp_send_json( ['total_contact' => $items_length, 'success' => true], 200 );
    }

}