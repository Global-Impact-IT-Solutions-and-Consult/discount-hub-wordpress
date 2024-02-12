<?php
/*
Plugin Name:  force-postname-permalinks
Description:  Force permalinks to post-name. This will make the rest-api work out of the box.
Version:      1.0.0
Author:       Ru Nacken
Author URI:   https://github.com/rnacken
License:      MIT License
*/

add_action('init', function () {
    if (get_option('permalink_structure') == '') {
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
    }
});
