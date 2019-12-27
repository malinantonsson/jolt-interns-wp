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

function create_my_taxonomies() {
    register_taxonomy(
        'jolt_interns_class',
        'jolt_interns',
        array(
            'labels' => array(
                'name' => 'Jolt Classes',
                'add_new_item' => 'Add New Class',
                'new_item_name' => "New Movie Class"
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true
        )
    );
}

add_action( 'init', 'create_my_taxonomies', 0 );

function my_columns( $columns ) {
    $columns['hired'] = 'Hired';
    unset( $columns['comments'] );
    return $columns;
}

add_filter( 'manage_edit-jolt_interns_columns', 'my_columns' );

function populate_columns( $column ) {
  $meta = get_post_meta( get_the_ID(), 'jolt_interns', true );
    if ( 'hired' == $column ) {
        if ( $meta['hired'] === 'hired' ) {
            echo 'yes';
        } else {
          echo 'no';
        }

    }

}

add_action( 'manage_posts_custom_column', 'populate_columns' );

add_action( 'admin_init', 'my_admin' );


function my_admin() {
    add_meta_box( 'jolt_intern_meta_box',
        'Jolt Intern Details',
        'display_jolt_intern_meta_box',
        'jolt_interns',
        'normal',
        'high'
    );
}

function display_jolt_intern_meta_box( $jolt_intern ) {
    global $post;
    $meta = get_post_meta( $post->ID, 'jolt_interns', true );
    ?>


    <input type="hidden" name="your_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">
    <table>
      <tr>
        <td style="width: 100%">Hired?</td>
        <td><input type="checkbox" name="jolt_interns[hired]" id="jolt_interns[hired]" value="hired" <?php if ( $meta['hired'] === 'hired' ) echo 'checked'; ?> /></td>
      </tr>

        <tr>
          <td style="width: 100%">Company logo</td>
          <td>
            <p>
            	<label for="jolt_interns[company_logo]">Image Upload</label><br>
            	<input type="text" name="jolt_interns[company_logo]" id="jolt_interns[company_logo]" class="meta-image regular-text" value="<?php echo $meta['company_logo']; ?>">
            	<input type="button" class="button image-upload" value="Browse">
              <button id="jolt-intern-logo-remove" type="button" class="button" style="color: red; border-color: red;">Remove</button>
            </p>
            <div class="image-preview"><img class="jolt_interns_img_preview" src="<?php echo $meta['company_logo']; ?>" style="max-width: 250px;"></div>

            <script>
              jQuery(document).ready(function($) {
                $('#jolt-intern-logo-remove').click(function(e) {
                  var image_field = $(this).parent().children('.meta-image');
                    $(this).parent().children('.meta-image').val('')
                    $(this).parent().parent().find('.jolt_interns_img_preview').attr('src', '')

                })
                // Instantiates the variable that holds the media library frame.
                var meta_image_frame
                // Runs when the image button is clicked.
                $('.image-upload').click(function(e) {
                  // Get preview pane
                  var meta_image_preview = $(this)
                    .parent()
                    .parent()
                    .children('.image-preview')
                  // Prevents the default action from occuring.
                  e.preventDefault()

                  var meta_image = $(this)
                    .parent()
                    .children('.meta-image')
                  // // If the frame already exists, re-open it.
                  if (meta_image_frame) {
                    meta_image_frame.open()
                    return
                  }
                  // // Sets up the media library frame
                  meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
                    title: 'Add or select a company logo',
                    button: {
                      text: 'Select',
                    },
                  })
                  // // Runs when an image is selected.
                  meta_image_frame.on('select', function() {
                    // Grabs the attachment selection and creates a JSON representation of the model.
                    var media_attachment = meta_image_frame
                      .state()
                      .get('selection')
                      .first()
                      .toJSON()
                    // Sends the attachment URL to our custom image input field.
                    meta_image.val(media_attachment.url)
                    meta_image_preview.children('img').attr('src', media_attachment.url)
                  })
                  // Opens the media library frame.
                  meta_image_frame.open()
                })
              })
            </script>
          </td>
        </tr>
    </table>
    <?php
}

function save_intern_form( $intern_id, $intern ) {
    // Check post type for movie reviews
    if ( $intern->post_type == 'jolt_interns' ) {
      // verify nonce
    	if ( !wp_verify_nonce( $_POST['your_meta_box_nonce'], basename(__FILE__) ) ) {
    		return $intern_id;
    	}

      // check autosave
    	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    		return $intern_id;
    	}

      $old = get_post_meta( $intern_id, 'jolt_interns', true );
    	$new = $_POST['jolt_interns'];

      if ( $new && $new !== $old ) {
    		update_post_meta( $intern_id, 'jolt_interns', $new );
    	} elseif ( '' === $new && $old ) {
    		delete_post_meta( $intern_id, 'jolt_interns', $old );
    	}
    }
}

add_action( 'save_post', 'save_intern_form', 10, 2 );

function include_template_function( $template_path ) {
    if ( get_post_type() == 'jolt_interns' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-jolt_interns.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-jolt_interns.php';
            }
        }
    }
    return $template_path;
}

add_filter( 'template_include', 'include_template_function', 1 );
?>
