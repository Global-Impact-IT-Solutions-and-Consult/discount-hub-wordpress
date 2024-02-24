<?php
/*
Plugin Name:  Discount Hub
Plugin URI:   https://giitsc.com/
Description:  Discount Hub Plugin
Version:      2.0.0
Author:       Johnson Olaoluwa
Author URI:   https://giitsc.com/
Text Domain:  roots
License:      MIT License
*/

// Function to fetch products from external API and add/update posts
// function fetch_and_add_products_from_api() {
//     // Your API endpoint URL
//     // $api_url = 'http://127.0.0.1:8000/crawl';
//     // $api_url = 'http://0.0.0.0:3033/crawl';
//     // $api_url = 'http://discount-hub-crawler:5000/crawl';
//     // $api_url = 'http://127.0.0.1:5000/crawl';
//     $api_url = 'http://0.0.0.0:5000/crawl';

//     // Fetch data from the API
//     $response = wp_remote_get( $api_url );

//     // Log the response to the PHP error log
//     error_log( print_r( $response, true ) );

//     // Check if the request was successful
//     if ( is_array( $response ) && ! is_wp_error( $response ) ) {
//         // Get the body of the response
//         $body = wp_remote_retrieve_body( $response );

//         // Log the body of the response to the PHP error log
//         error_log( $body );

//         // Convert JSON string to PHP array
//         $products = json_decode( $body );

//         // Check if products were fetched successfully
//         if ( $products ) {
//             // Loop through each product
//             foreach ( $products as $product ) {
//                 // Prepare post data
//                 $post_data = array(
//                     'post_title'   => $product->name,
//                     'post_content' => $product->description,
//                     'post_status'  => 'publish',
//                     'post_type'    => 'products'
//                     // You can add more fields as needed
//                 );

//                 // Insert or update the post
//                 $post_id = wp_insert_post( $post_data );

//                 // Check if post was inserted or updated successfully
//                 if ( $post_id ) {
//                     // Add meta data if available
//                     if ( isset( $product->meta ) && is_array( $product->meta ) ) {
//                         foreach ( $product->meta as $key => $value ) {
//                             // Update post meta
//                             update_post_meta( $post_id, $key, $value );
//                         }
//                     }

//                     // Assign taxonomies if available
//                     if ( isset( $product->categories ) && is_array( $product->categories ) ) {
//                         wp_set_object_terms( $post_id, $product->categories, 'category' );
//                     }

//                     if ( isset( $product->tags ) && is_array( $product->tags ) ) {
//                         wp_set_object_terms( $post_id, $product->tags, 'post_tag' );
//                     }

//                     // Add custom taxonomy terms if available
//                     // Example: wp_set_object_terms( $post_id, $product->custom_taxonomy_terms, 'your_taxonomy_slug' );
//                 }
//             }
//         }
//     }
// }

function fetch_and_add_products_from_api() {
    // Your API endpoint URL
    // $api_url = 'http://0.0.0.0:5000/crawl';
    // $api_url = 'http://127.0.0.1:5000/crawl';
    $api_url = 'http://host.docker.internal:5000/crawl';

    // Set custom timeout value (in seconds)
    $timeout = 60000; // Adjust the timeout value as needed

    // Make the request using wp_remote_get for a GET request
    $response = wp_remote_get( $api_url, array(
        'blocking'  => true,
        'headers'   => array(
            'Content-Type' => 'application/json',
        ),
        'timeout'   => $timeout, // Set custom timeout value
    ) );

    print($api_url);

    // Check if the request was successful
    if ( is_wp_error( $response ) ) {
        // error_log( 'Error fetching products from API: ' . $response->get_error_message() );
        error_log( print_r( $response, true ) );
        return;
    }

    // Get the body of the response
    $body = wp_remote_retrieve_body( $response );

    // Convert JSON string to PHP array
    $products = json_decode( $body );

    // Check if products were fetched successfully
    if ( ! $products ) {
        error_log( 'Error decoding JSON response from API' );
        return;
    }

    // Loop through each product
    foreach ( $products as $product ) {
        // Prepare post data
        $post_data = array(
            'post_title'   => $product->name,
            'post_content' => $product->description,
            'post_status'  => 'publish',
            'post_type'    => 'products'
            // You can add more fields as needed
        );

        // Insert or update the post
        $post_id = wp_insert_post( $post_data );

        // Check if post was inserted or updated successfully
        if ( $post_id ) {
            // Add meta data if available
            if ( isset( $product->meta ) && is_array( $product->meta ) ) {
                foreach ( $product->meta as $key => $value ) {
                    // Update post meta
                    update_post_meta( $post_id, $key, $value );
                }
            }

            // Assign taxonomies if available
            if ( isset( $product->categories ) && is_array( $product->categories ) ) {
                wp_set_object_terms( $post_id, $product->categories, 'category' );
            }

            if ( isset( $product->tags ) && is_array( $product->tags ) ) {
                wp_set_object_terms( $post_id, $product->tags, 'post_tag' );
            }

            // Add custom taxonomy terms if available
            // Example: wp_set_object_terms( $post_id, $product->custom_taxonomy_terms, 'your_taxonomy_slug' );
        }
    }
}


// Schedule the event to run daily at midnight
function schedule_daily_event() {
    if ( ! wp_next_scheduled( 'fetch_and_add_products_event' ) ) {
        // Schedule the event for the first time WordPress runs
        fetch_and_add_products_from_api();
        
        // Schedule daily event for subsequent calls
        wp_schedule_event( strtotime( 'midnight' ), 'daily', 'fetch_and_add_products_event' );
    }
}
add_action( 'wp', 'schedule_daily_event' );

//create post types
function create_posttype()
{
    //products
    register_post_type(
        'products',
        // CPT Options
        array(
            'labels' => array(
                'name' => __('Products'),
                'singular_name' => __('Product')
            ),
            'supports'            => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields',),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'hierarchical' => true, # set to false if you don't want parent/child relationships for the entries
            'show_in_graphql' => true, # Set to false if you want to exclude this type from the GraphQL Schema
            'graphql_single_name' => 'product',
            'graphql_plural_name' => 'products', # If set to the same name as graphql_single_name, the field name will default to `all${graphql_single_name}`, i.e. `allDocument`.
            'public' => true, # set to false if entries of the post_type should not have public URIs per entry
            'publicly_queryable' => true, # Set to false if entries should only be queryable in WPGraphQL by authenticated requests
            'rewrite' => array('slug' => 'product'),
            'taxonomies' => array('category', 'post_tag')
        )
    );

    //shops
    register_post_type(
        'shops',
        // CPT Options
        array(
            'labels' => array(
                'name' => __('Shops'),
                'singular_name' => __('Shop')
            ),
            'supports'            => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields',),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'hierarchical' => true, # set to false if you don't want parent/child relationships for the entries
            'show_in_graphql' => true, # Set to false if you want to exclude this type from the GraphQL Schema
            'graphql_single_name' => 'shop',
            'graphql_plural_name' => 'shops', # If set to the same name as graphql_single_name, the field name will default to `all${graphql_single_name}`, i.e. `allDocument`.
            'public' => true, # set to false if entries of the post_type should not have public URIs per entry
            'publicly_queryable' => true, # Set to false if entries should only be queryable in WPGraphQL by authenticated requests
            'rewrite' => array('slug' => 'shop'),
            'taxonomies' => array('category')
        )
    );
}
// Hooking up our function to theme setup
add_action('init', 'create_posttype');


//enable cors
add_action('rest_api_init', function () {
    add_action('rest_pre_serve_request', function () {
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Wpml-Language', true);
        header("Access-Control-Allow-Origin: *");
    });
}, 15);

function add_taxonomy()
{
    fetch_and_add_products_from_api();
    register_taxonomy(
        'company',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        'products',             // post type name
        array(
            'hierarchical' => true,
            'label' => 'Product Company', // display name
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'company',    // This controls the base slug that will display before each term
                'with_front' => false  // Don't display the category base before
            )
        )
    );

    register_taxonomy(
        'discountTypes',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        'products',             // post type name
        array(
            'hierarchical' => true,
            'label' => 'Discount Types', // display name
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'discount-type',    // This controls the base slug that will display before each term
                'with_front' => false  // Don't display the category base before
            )
        )
    );
}
add_action('init', 'add_taxonomy');
