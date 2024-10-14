<?php

/**
 * Helper function for print and check data
 *
 * @param mixed $value
 *
 * @return void
 */
function inspect( $value ) {
    echo '<pre>';
    print_r( $value );
    echo '</pre>';
}

/**
 * Insert New data
 *
 * @param array $args
 *
 * @return int | WP_Error
 */
function cb_insert_contact( $args ) {

    global $wpdb;

    if ( empty( $args['name'] ) ) {
        return new \WP_Error( 'no-name', __( 'You must Provide a name', 'contactbook' ) );
    }

    $defaults = [
        'name'       => '',
        'email'      => '',
        'created_at' => current_time( 'mysql' ),
    ];

    $data = wp_parse_args( $args, $defaults );
    $format = [
        '%s',
        '%s',
        '%s',
    ];

    $wp_contact_book = $wpdb->prefix . 'contactbook';

    $inserted = $wpdb->insert(
        $wp_contact_book,
        $data,
        $format
    );

    if ( !$inserted ) {
        return new \WP_Error( 'failed-to-insert', __( 'Failed to insert data', 'contactbook' ) );
    }

    return $wpdb->insert_id;

}

/**
 * Get all contacts
 *
 * @return array
 */
function cb_get_contacts( $args = [] ) {
    global $wpdb;

    $wp_contact_book = $wpdb->prefix . 'contactbook';

    $defaults = [
        'number'  => 5,
        'offset'  => 1,
        'orderby' => 'id',
        'order'   => 'desc',
    ];

    $args = wp_parse_args( $args, $defaults );

    $query = $wpdb->prepare(
        "SELECT * FROM $wp_contact_book
        ORDER BY {$args['orderby']} {$args['order']}
        LIMIT %d, %d",
        $args['offset'], $args['number'] );

    $contacts = $wpdb->get_results( $query );

    return $contacts;
}

/**
 * Single Item View
 *
 * @param string $id
 * @return object
 */
function cb_get_single_address( $id ) {
    global $wpdb;

    $wp_contact_book = $wpdb->prefix . 'contactbook';

    return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wp_contact_book WHERE id = %d", $id ) );
}

/**
 * All Data count
 * 
 * @return int
 */
function cb_get_data_count(){
    global $wpdb;

    $wp_contact_book = $wpdb->prefix . 'contactbook';

    return (int) $wpdb->get_var("SELECT count(id) FROM $wp_contact_book");
}

/**
 * Delete item
 *
 * @param int $id
 * @return bool|int
 */
function cb_delete_contact( $id ) {
    global $wpdb;

    $wp_contact_book = $wpdb->prefix . 'contactbook';

    return $wpdb->delete(
        $wp_contact_book,
        ['id' => $id],
        ['%d']
    );
}

function cb_update_contact( $args ) {
    global $wpdb;

    if ( empty( $args['name'] ) ) {
        return new \WP_Error( 'no-name', __( 'You must Provide a name', 'contactbook' ) );
    }

    $defaults = [
        'name'       => '',
        'email'      => '',
        'created_at' => current_time( 'mysql' ),
    ];

    $data = wp_parse_args( $args, $defaults );

    if ( isset( $data['id'] ) ) {
        $id = $data['id'];
        unset( $data['id'] );
    }
    $format = [
        '%s',
        '%s',
        '%s',
    ];

    $wp_contact_book = $wpdb->prefix . 'contactbook';

    $updated = $wpdb->update(
        $wp_contact_book,
        $data,
        ['id' => $id],
        $format,
        ['%d']
    );

    return $updated;
}