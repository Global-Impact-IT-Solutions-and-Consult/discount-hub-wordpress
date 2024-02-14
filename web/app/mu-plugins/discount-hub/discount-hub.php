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
