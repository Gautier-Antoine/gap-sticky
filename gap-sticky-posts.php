<?php
/**
 * Plugin Name: GAP > Sticky Posts
 * Description: Block loop sticky posts or not
 * Requires PHP: 7.0
 * Requires at least: 5.8
 * Version: 0.1.0
 * Author: Gautier-Antoine <direct@gautierantoine.com>
 * Author URI: http://gautierantoine.com
 * Text Domain: gap-sticky
 * Domain Path: /lang
 */
// ! CHECK I18N IN REACT
// ! Make dynamic

if (!defined('ABSPATH')) {
    exit;
}
/**
 * Register block
 *
 * @return void
 */
function gap_sticky_block_init()
{
	register_block_type(
        __DIR__ . '/build',
		array('render_callback' => 'gap_sticky_render'),
    );
}
add_action('init', 'gap_sticky_block_init');

/**
 * Rendering the frontend
 *
 * @param array $attributes
 * @return html
 */
function gap_sticky_render($attributes)
{
    $query = gap_sticky_query($attributes);
    ob_start();
	gap_sticky_html($query, $attributes);
    return ob_get_clean();
}

/**
 * If list, render query
 *
 * @param object $query
 * @param array $attributes
 * @return html
 */
function gap_sticky_html($query, $attributes)
{
    // $classes = ( isset($attributes['className']) ) ? ' ' .$attributes['className'] : ''; 
    // echo '<div class="wp-block-gap-sticky-posts'. $classes .'">';

    while ($query->have_posts()) : $query->the_post();

        $title = get_the_title();
        $link = get_the_permalink();
        $list = explode(', ', get_the_excerpt());
        $html = '';
        foreach ($list as $elem) {
            $html .= '<!-- wp:list-item --><li>'. $elem .'</li><!-- /wp:list-item -->';
        }

        echo do_blocks('
            <!-- wp:cover {
                "useFeaturedImage":true,
                "dimRatio":0,
                "focalPoint":{"x":0,"y":0},
                "isDark":false,
                "className":"post-call",
                "layout":{"type":"constrained"}
            } -->
            <div class="wp-block-cover is-light post-call">
                <span
                    aria-hidden="true"
                    class="wp-block-cover__background has-background-dim-0 has-background-dim"
                ></span>
                <div class="wp-block-cover__inner-container">
                    <!-- wp:group {
                        "backgroundColor":"white",
                        "layout":{"type":"flex", "orientation":"vertical", "verticalAlignment":"center"}
                    } -->
                    <div class="wp-block-group has-white-background-color has-background">
                        <!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
                        <div class="wp-block-group">
                            <!-- wp:post-title /-->
                            <!-- wp:post-date {"format":"M Y"} /-->
                        </div>
                        <!-- /wp:group -->

                        <!-- wp:gap/stacks /-->

                        <!-- wp:list -->
                        <ul>' . $html . '</ul>
                        <!-- /wp:list -->

                        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
                        <div class="wp-block-buttons">
                            <!-- wp:button {"textAlign":"center"} -->
                            <div class="wp-block-button">
                                <a
                                    class="wp-block-button__link has-text-align-center wp-element-button"
                                    href="' . $link . '"
                                >Go to ' . $title . '</a>
                            </div>
                            <!-- /wp:button -->
                        </div>
                        <!-- /wp:buttons -->
                    </div>
                    <!-- /wp:group -->
                </div>
            </div>
            <!-- /wp:cover -->
        ');
    endwhile;
    // echo '</div>';
}

/**
 * Get the query
 *
 * @param array $attributes
 * @return object
 */
function gap_sticky_query($attributes)
{

    $postType = $attributes['postType'];
    $numberPosts = $attributes['numberPosts'];
    $isSticky = $attributes['isSticky'];

    $args = array(
        'post_type' => $postType,
        'posts_per_page' => $numberPosts,
        'post_status'           => array('publish'),
        'order'                 => 'DESC',
        'orderby'               => 'date',
        'ignore_sticky_posts' => true,
    );
    if ($isSticky) {
        $sticky = get_option('sticky_posts');
        if (!empty($sticky)) {
            rsort($sticky);
            $args['post__in'] = $sticky;
        }
    }
    return new WP_Query($args);
}
