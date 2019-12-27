<?php
   /*
   Plugin Name: Jolt interns
   description: a plugin to display jolt interns
   Version: 1.0
   Author: Malin Antonsson
   Author URI: http://malin.dev
   License: GPL2
   */

   add_action( 'init', 'create_jolt_interns' );

   function create_jolt_interns() {
    register_post_type( 'jolt_interns',
        array(
            'labels' => array(
                'name' => 'Jolt Interns',
                'singular_name' => 'Jolt Intern',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Jolt Intern',
                'edit' => 'Edit',
                'edit_item' => 'Edit Jolt Intern',
                'new_item' => 'New Jolt Intern',
                'view' => 'View',
                'view_item' => 'View Jolt Intern',
                'search_items' => 'Search Jolt Interns',
                'not_found' => 'No Jolt Interns found',
                'not_found_in_trash' => 'No Jolt Interns found in Trash',
                'parent' => 'Parent Jolt Intern'
            ),

            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'images/users.png', __FILE__ ),
            'has_archive' => true
        )
    );
}
?>
