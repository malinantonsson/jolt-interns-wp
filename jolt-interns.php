<?php
   /*
   Plugin Name: Jolt interns
   description: a plugin to display jolt interns [joltClasses slug="class-slug"]
   Version: 1.0
   Author: Malin Antonsson
   Author URI: http://malin.dev
   License: GPL2
   */

/*
=========
  Create the shortcode
=========
*/
add_shortcode("joltClasses", "joltClasses_sc");


/*
=========
  Enqueue styles and scripts
=========
*/

function add_jolt_intern_scripts_and_styles() {
//   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
// <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    wp_enqueue_style( 'jquery-modal-style', "https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" );
    wp_enqueue_style( 'jolt-intern-style', plugins_url('jolt-interns/css/jolt-intern-style.css') );

    wp_enqueue_script( 'jquery-modal', "https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js", array ( 'jquery' ));
    wp_enqueue_script( 'jolt-intern-script', plugins_url('jolt-interns/js/jolt-interns-script.js'), array ( 'jquery', 'jquery-modal' ), 1.1, true);
    //wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'add_jolt_intern_scripts_and_styles' );

// function add_jolt_intern_style() {
// 	wp_enqueue_style('jolt-intern-style', plugins_url('jolt-interns/css/jolt-intern-style.css');
// }
// add_action( 'wp_enqueue_scripts', 'add_jolt_intern_style' );

//add_action( 'wp_enqueue_scripts', 'add_ak_carousel_script' );
// function add_ak_carousel_script() {
// 	wp_enqueue_script( 'ak-carousel-script', plugins_url('ak-carousel/js/ak-carousel-script.js'), array ( 'jquery' ), 1.1, true);
// }

/*
=========
  Display on website
=========
*/

function jolt_classes_content( $more_link_text = null, $strip_teaser = false) {
    $content = get_the_content( $more_link_text, $strip_teaser );
    $content = apply_filters( 'the_content', $content );
    $content = str_replace( ']]>', ']]&gt;', $content );
    return $content;
}

function joltClasses_sc($atts) {
	extract(shortcode_atts(array( "slug" => ''), $atts));
    global $post;

    $args = array(
    	'post_type' => 'jolt_interns',
    	'posts_per_page' => 50,
    	'order'=> 'DSC',
      'tax_query' => array(
    		array(
    			'taxonomy' => 'jolt_interns_class',
    			'field' => 'slug',
    			'terms' => $slug
    		)
    	),
    	'orderby' => 'date');

    $custom_posts = get_posts($args);
    $output = '';

	$index = 0;
    $output .= 	'
    	<div class="jolt-interns">
        <div class="jolt-interns-content">';

		    foreach($custom_posts as $post) : setup_postdata($post);
		    	$slug = basename(get_permalink());
		    	$name = get_the_title();
          $img = get_the_post_thumbnail_url();
		    	$bio = jolt_classes_content();
          $meta = get_post_meta( get_the_ID(), 'jolt_interns', true );
          $hired = $meta['hired'] === 'hired' ? '- hired' : '';
          $company_logo = $meta['company_logo'];


		    	$output .= 	'
		    	<div class="jolt-interns-item" id="'.$slug.'"
		    		data-index="'.$index.'" style="background-image: url('.$img.')">
            <a  href="#modal-'.$slug.'" rel="modal:open" class="jolt-interns-item__inner js-jolt-interns-item-btn"
              data-name="'.$name.'"
              data-bio="'.$bio.'"
              data-logo="'.$company_logo.'"
              data-img="'.$img.'"
              >
					   <h3 class="jolt-interns-item__name">
			        	'.$name.$hired.'</h3>
            </a>
			    </div>
          <div id="modal-'.$slug.'" class="modal jolt-intern-modal">
            <div class="jolt-intern-modal__inner">
              <div class="jolt-intern-modal__section jolt-intern-modal__section--left" style="background-image: url('.$img.')"></div>
              <div class="jolt-intern-modal__section jolt-intern-modal__section--right">
                <div class="jolt-intern-modal__header">
                  <h3 class="jolt-inter-modal__name">'.$name.'</h3>
                  <img class="jolt-inter-modal__logo" src="'.$company_logo.'" />
                </div>
                <div class="jolt-inter-modal__bio">'.$bio.'</div>
              </div>
              </div>
          </div>';

			$index++;
		    endforeach; wp_reset_postdata();
	    	$output .= 	'</div></div>';

	return $output;
	}

 /*
 =========
 Set up custom post type: jolt_interns
 =========
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

/*
=========
Set up custom taxonomy: jolt_interns_class
=========
*/

function create_jolt_taxonomy() {
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

add_action( 'init', 'create_jolt_taxonomy', 0 );


/*
=========
  Handle ordering/filtering of items in admin - INTERNS
=========
*/
function intern_columns( $columns ) {
    $columns['hired'] = 'Hired';
    $columns['class'] = 'Class';
    unset( $columns['comments'] );
    return $columns;
}

add_filter( 'manage_edit-jolt_interns_columns', 'intern_columns' );

function populate_intern_columns( $column_name, $post_id ) {
    if ( 'hired' == $column_name ) {
        $meta = get_post_meta( get_the_ID(), 'jolt_interns', true );
        if ( $meta['hired'] === 'hired' ) {
            echo 'yes';
        } else {
          echo 'no';
        }
    }

    if ('class' == $column_name) {
      $term_obj_list = get_the_terms( $post_id, 'jolt_interns_class' );
      $terms_string = join(', ', wp_list_pluck($term_obj_list, 'name'));
      echo $terms_string;
    }
}

function sort_by_hired( $columns ) {
    $columns['hired'] = 'jolt_interns[hired]';

    return $columns;
}

add_filter( 'manage_edit-jolt_interns_sortable_columns', 'sort_by_hired' );

function filter_by_classes() {
    $screen = get_current_screen();
    global $wp_query;
    if ( $screen->post_type == 'jolt_interns' ) {
        wp_dropdown_categories( array(
            'show_option_all' => 'Show All Classes',
            'taxonomy' => 'jolt_interns_class',
            'name' => 'jolt_interns_class',
            'orderby' => 'name',
            'selected' => ( isset( $wp_query->query['jolt_interns_class'] ) ? $wp_query->query['jolt_interns_class'] : '' ),
            'hierarchical' => false,
            'depth' => 3,
            'show_count' => false,
            'hide_empty' => true,
        ) );
    }
}

add_action( 'restrict_manage_posts', 'filter_by_classes' );

function perform_filtering( $query ) {
    $qv = &$query->query_vars;
    if ( ( $qv['jolt_interns_class'] ) && is_numeric( $qv['jolt_interns_class'] ) ) {
        $term = get_term_by( 'id', $qv['jolt_interns_class'], 'jolt_interns_class' );
        $qv['jolt_interns_class'] = $term->slug;
    }
}

add_filter( 'parse_query','perform_filtering' );

add_action( 'manage_posts_custom_column', 'populate_intern_columns', 10, 3 );


/*
=========
  Handle column in the CLASSES table
=========
*/



function classes_columns( $columns ) {
    $columns['shortcode'] = 'Shortcode';
    unset( $columns['description'] );
    return $columns;
}



add_filter( 'manage_edit-jolt_interns_class_columns', 'classes_columns' );



function populate_classes_columns( $content, $column_name, $term_id ) {

  // echo $term;
  if($column_name == 'shortcode') {
    $term = get_term($term_id, 'jolt_interns_class');
    echo '[joltClasses slug="'.$term->slug.'"]';
  } else {
    echo $column_name;
  }
}

add_action( 'manage_jolt_interns_class_custom_column', 'populate_classes_columns', 10, 3 );




/*
=========
  Set up custom metaboxes
=========
*/

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

/*
=========
 Display custom metaboxes in admin
=========
*/

function display_jolt_intern_meta_box( $jolt_intern ) {
    global $post;
    $meta = get_post_meta( $post->ID, 'jolt_interns', true );
    ?>


    <input type="hidden" name="your_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">
    <table>
      <tr>
        <td style="width: 100px">Hired?</td>
        <td><input type="checkbox" name="jolt_interns[hired]" id="jolt_interns[hired]" value="hired" <?php if ( $meta['hired'] === 'hired' ) echo 'checked'; ?> /></td>
      </tr>

        <tr>
          <td style="width: 100px">Company logo</td>
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

/*
=========
  Save the form in admin
=========
*/
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
