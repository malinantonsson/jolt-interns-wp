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
            'supports' => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'images/users.png', __FILE__ ),
            'has_archive' => true
        )
    );
}

add_action( 'admin_init', 'my_admin' );

function my_admin() {
    add_meta_box( 'jolt_intern_meta_box',
        'Jolt Intern Details',
        'display_jolt_intern_meta_box',
        'jolt_interns', 'normal', 'high'
    );
}

function display_jolt_intern_meta_box( $jolt_intern ) {
    // Retrieve current name of the Director and Movie Rating based on review ID
    $movie_director = esc_html( get_post_meta( $jolt_intern->ID, 'movie_director', true ) );
    $movie_rating = intval( get_post_meta( $jolt_intern->ID, 'movie_rating', true ) );
    $intern_hired = get_post_meta( $jolt_intern->ID, 'intern_hired', true );
    ?>
    <table>
        <tr>
          <td style="width: 100%">Hired?</td>
          <td><input type="checkbox" name="jolt_intern_hired" value="hired" <?php if(checked( $intern_hired, 1, false )) echo "checked"; ?> /></td>
        </tr>
        <tr>
            <td style="width: 100%">Movie Director</td>
            <td><input type="text" size="80" name="jolt_intern_director_name" value="<?php echo $movie_director; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 150px">Movie Rating</td>
            <td>
                <select style="width: 100px" name="jolt_intern_rating">
                <?php
                // Generate all items of drop-down list
                for ( $rating = 5; $rating >= 1; $rating -- ) {
                ?>
                    <option value="<?php echo $rating; ?>" <?php echo selected( $rating, $movie_rating ); ?>>
                    <?php echo $rating; ?> stars <?php } ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post', 'add_movie_review_fields', 10, 2 );

function add_movie_review_fields( $intern_id, $intern ) {
    // Check post type for movie reviews
    if ( $intern->post_type == 'jolt_interns' ) {
        // Store data in post meta table if present in post data
        $is_hired = $_POST['jolt_intern_hired'] ? true : false;
        update_post_meta( $intern_id, 'intern_hired', $is_hired );

        // if ( isset( $_POST['jolt_intern_hired'] ) && $_POST['jolt_intern_hired'] != '' ) {
        //     update_post_meta( $intern_id, 'intern_hired', $_POST['jolt_intern_hired'] );
        // }
        // if ( isset( $_POST['movie_review_director_name'] ) && $_POST['movie_review_director_name'] != '' ) {
        //     update_post_meta( $intern_id, 'movie_director', $_POST['movie_review_director_name'] );
        // }
        // if ( isset( $_POST['movie_review_rating'] ) && $_POST['movie_review_rating'] != '' ) {
        //     update_post_meta( $intern_id, 'movie_rating', $_POST['movie_review_rating'] );
        // }
    }
}
?>
