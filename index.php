<?php
/*
Plugin Name: My React Plugin
Description: A WordPress plugin with a React frontend.
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
define('DIR', plugin_dir_path(__FILE__));

// loading backend
require_once DIR . 'backend/backend.php';
require_once DIR . 'backend/API/settings/settings.php';

// Enqueue scripts and styles
function my_react_plugin_enqueue_scripts()
{
    wp_enqueue_script(
        'my-react-plugin-script',
        plugins_url('/build/index.js', __FILE__),
        array('wp-element'), // Dependency on WordPress React wrapper
        '1.0',
        true
    );

    wp_enqueue_style(
        'my-react-plugin-style',
        plugins_url('/style.css', __FILE__)
    );

    wp_enqueue_style(
        'my-react-plugin-tailwind',
        plugins_url('/src/output.css', __FILE__)
    );

    // Localize script to pass the token
    wp_localize_script('my-react-plugin-script', 'reactPluginData', array(
        'rest_base' => esc_url(rest_url()), // Base REST API URL
        'token' => wp_create_nonce('wp_rest')
    ));
}
add_action('admin_enqueue_scripts', 'my_react_plugin_enqueue_scripts');

// Add an admin menu page
function my_react_plugin_add_admin_page()
{
    add_menu_page(
        'React Plugin Test',     // Page title
        'React Plugin',          // Menu title
        'manage_options',        // Capability
        'my-react-plugin',       // Menu slug
        'my_react_plugin_render_page', // Callback function
        'dashicons-admin-generic',     // Icon
        100                      // Position
    );
}
add_action('admin_menu', 'my_react_plugin_add_admin_page');

// Render the admin page content
function my_react_plugin_render_page()
{
    echo '<div id="my-react-plugin-root"></div>';
    echo '<h4>Loading the React app...</h4>';
}

// Load default settings on plugin activation
register_activation_hook(__FILE__, 'load_default_settings');
?>
