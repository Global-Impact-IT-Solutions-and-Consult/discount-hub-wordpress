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


function create_posttype()
{
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
            'rewrite' => array('slug' => 'product'),
            'taxonomies' => array('category', 'post_tag')
        )
    );
}
// Hooking up our function to theme setup
add_action('init', 'create_posttype');
