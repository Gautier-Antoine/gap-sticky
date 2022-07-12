<?php
/**
 * Plugin Name:       Gap Sticky Posts
 * Description:       Example block written with ESNext standard and JSX support – build step required.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gap-sticky-posts
 *
 * @package           create-block
 */

function create_block_gap_sticky_posts_block_init() {
	register_block_type( __DIR__ . '/build',
	array(
		'render_callback' => 'gautier_antoine_sticky_block_renderer',
	) );
}
add_action( 'init', 'create_block_gap_sticky_posts_block_init' );



/**
 * Rendering the frontend
 *
 * @param array $attributes
 * @return html
 */
function gautier_antoine_sticky_block_renderer($attributes){
    $query = gautier_antoine_sticky_block_get_query($attributes);
    // $postType = $attributes['postType'];
    // $numberPosts = $attributes['numberPosts'];
    // $classes = ( isset($attributes['className']) ) ? $attributes['className'] : '';

    // gautier_antoine_sticky_block_enqueue_method();

    ob_start();
	gautier_antoine_sticky_block_get_list($query, $attributes);
    return ob_get_clean();
}

/**
 * If list, render query
 *
 * @param object $query
 * @param array $attributes
 * @return html
 */
function gautier_antoine_sticky_block_get_list($query, $attributes) {

    // $postType = $attributes['postType'];
    // $classes = ( isset($attributes['className']) ) ? $attributes['className'] : ''; 
    // echo '<div class="wp-block-create-block-gautier_antoine-posts-block has-list '. $classes .'">';

    while ($query->have_posts()) : $query->the_post();
		$theme_file = get_stylesheet_directory() . '/template/post-call.php';
		if (file_exists($theme_file)) {
			require $theme_file;
		} else {
			echo 'no theme';
		}
    endwhile;
// echo '</div>';
}

/**
 * Get the query
 *
 * @param array $attributes
 * @return object
 */
function gautier_antoine_sticky_block_get_query($attributes) {

    $postType = $attributes['postType'];
    $numberPosts = $attributes['numberPosts'];
    // $classes = ( isset($attributes['className']) ) ? $attributes['className'] : ''; 

    $args = array(
        'post_type' => $postType,
        'posts_per_page' => $numberPosts,
        // ! 'offset' => $offsetPosts,
        'post_status'           => array('publish'), // Also support: pending, draft, auto-draft, future, private, inherit, trash, any
        'order'                 => 'DESC', // Also support: ASC
        'orderby'               => 'date',

    );
	$sticky = get_option('sticky_posts');
	if (!empty($sticky)) {
		rsort($sticky);
		$args['post__in'] = $sticky;
	}
    return new WP_Query($args);
}

/**
 * Enqueue Scripts
 *
 * @return script
 */
// function gautier_antoine_sticky_block_enqueue_method() {
//     wp_enqueue_script('impact-block', plugin_dir_url(__FILE__) . 'inc/impact-block.js', array('jquery'), null, true );
// }
