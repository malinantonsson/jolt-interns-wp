<?php
 /*Template Name: Single intern view
 */

get_header(); ?>
<div id="primary">
    <div id="content" role="main">
    <?php
    $mypost = array( 'post_type' => 'jolt_interns', );
    $loop = new WP_Query( $mypost );
    ?>
    <?php while ( $loop->have_posts() ) : $loop->the_post();?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">

                <!-- Display featured image in right-aligned floating div -->
                <div style="float: right; margin: 10px">
                    <?php the_post_thumbnail( array( 100, 100 ) ); ?>
                </div>

                <!-- Display Title and Author Name -->
                <strong>Name: </strong><?php the_title(); ?><br />
                <strong>Hired? </strong>
                <?php echo esc_html( get_post_meta( get_the_ID(), 'jolt_interns[hired]', true ) ); ?>
                <br />
            </header>

            <!-- Display movie review contents -->
            <div class="entry-content"><?php the_content(); ?></div>
        </article>

    <?php endwhile; ?>
    </div>
</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>
