<?php
/**
 * Plugin Name:       GAP > Sticky Posts
 * Description:       Block Loop for sticky posts
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Gautier-Antoine <direct@gautierantoine.com>
 * Author URI:        https://gautierantoine.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gap-sticky-posts
 * Domain Path:       languages
 *
 * @package           gap-sticky-posts
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function gap_sticky_posts_gap_sticky_posts_block_init()
{
	// $plugin_data = get_plugin_data( __FILE__ );
	// $plugin_version = $plugin_data['Version'];
	// var_dump($plugin_data);
    wp_register_script(
		'gap-sticky-posts-script',
		plugins_url('includes/load.js', __FILE__),
		array( 'wp-blocks', 'react', 'wp-i18n', 'wp-block-editor' ),
		strval(filemtime(__FILE__)),
		true
	);

    wp_set_script_translations(
        'gap-sticky-posts-script',
        'gap-sticky-posts',
        plugin_dir_path(__FILE__) . 'languages'
	);
	register_block_type(__DIR__ . '/build');

}
add_action('init', 'gap_sticky_posts_gap_sticky_posts_block_init');
