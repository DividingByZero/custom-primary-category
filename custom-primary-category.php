<?php
/*
Plugin Name: Custom Primary Category
Version: 1.0
Plugin URI: https://www.linkedin.com/in/nathan-supplee-62892522/
Author: Nathan Supplee 
Author URI: https://www.linkedin.com/in/nathan-supplee-62892522/
Description: Add functionality to allow selection of a primary category for posts.
Text Domain: custom-primary-category
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

// If this file is accessed directly, exit
if (!defined('ABSPATH')) { exit; }

// Add custom primary category meta box to posts (can add other post types as well)
function cpc_add_meta_boxes($post) {
    add_meta_box('product_meta_box', __('Custom Primary Category', 'custom-primary-category'), 'cpc_action_meta_box', 'post', 'side' );
}
add_action('add_meta_boxes', 'cpc_add_meta_boxes');

// Build out meta box fields and text
function cpc_action_meta_box($post) {
    wp_nonce_field(basename(__FILE__), 'cpc_meta_box_nonce');
    
    // Get meta data associated with this field
	global $post;
    $primary_category = get_post_meta($post->ID, '_cpc_primary_category', true);
    
    // If no data in DB yet, set to zero
    if (empty($primary_category)) { $primary_category = 0; }
	?>
        <label for="cpc_primary_category"><b><?php _e('Select Primary Category', 'custom-primary-category'); ?></b></label>
        <?php 
        // Arguments for wp_dropdown_categories()
        $args = array(
            'show_option_none'  => __( 'Select category', 'textdomain' ),
            'show_count'        => 0,
            'orderby'           => 'name',
            'echo'              => 1,
            'name'              => 'cpc_primary_category',
            'id'                => 'cpc_primary_category',
            'class'             => 'regular-text',
            'selected'          => $primary_category
        );
        wp_dropdown_categories( $args ); ?>
        <br><small><?php _e('Select categories from "Categories" meta box and then select primary category from this dropdown.', 'custom-primary-category'); ?></small>

	<?php
}

// Store the data selected in custom meta box
function cpc_save_meta_box_data($post_id) {
    // Verify nonce
    if (!isset($_POST['cpc_meta_box_nonce']) || !wp_verify_nonce($_POST['cpc_meta_box_nonce'], basename(__FILE__))) { return; }
    // Verify user permissions
    if (!current_user_can('edit_post', $post_id)) { return; }
    // Store cpc_primary_category value as meta data
    if (isset($_REQUEST['cpc_primary_category'])) {
        update_post_meta($post_id, '_cpc_primary_category', sanitize_text_field($_POST['cpc_primary_category']));
    }
}
add_action('save_post', 'cpc_save_meta_box_data');