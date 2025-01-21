<?php

require_once 'default-settings.php';

/**
 * Class ReactWordPressPluginSettings
 *
 * This class registers the plugin settings in WordPress and adds settings sections and fields.
 */
class ReactWordPressPluginSettings
{

    /**
     * Register plugin settings in WordPress
     *
     * This function registers the plugin settings in WordPress and adds settings sections and fields.
     *
     * @return void
     */
    public function register_settings()
    {
        add_option('react_wordpress_plugin_settings', []);
        load_default_settings(); // Load default settings
    }

    /**
     * Display main settings section description
     *
     * This function displays the main settings section description.
     *
     * @return void
     */
    public function section_text()
    {
        // ...existing code...
    }

    /**
     * Validate and sanitize settings input
     *
     * This function validates and sanitizes the settings input.
     *
     * @param array $input Settings input
     * @return array Sanitized settings input
     */
    public function callback($input)
    {
        // ...existing code...
        return $input; // Ensure the input is returned after validation
    }

    /**
     * Register API routes for settings
     *
     * This function registers the API routes for settings.
     *
     * @return void
     */
    public function register_api_routes()
    {
        add_action('rest_api_init', function () {
            register_rest_route('react-wordpress-plugin/v1', '/settings', array(
                'methods' => 'GET',
                'callback' => function () {
                    $settings = get_option('react_wordpress_plugin_settings');
                    return rest_ensure_response($settings);
                },
            ));

            register_rest_route('react-wordpress-plugin/v1', '/settings', array(
                'methods' => 'POST',
                'callback' => function ($request) {
                    $params = $request->get_json_params();
                    $validated_params = $this->callback($params['settings']);
                    update_option('react_wordpress_plugin_settings', $validated_params);
                    return rest_ensure_response($validated_params);
                },
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                }
            ));
        });
    }
}

// Instantiate the class and register settings and API routes
$reactWordPressPluginSettings = new ReactWordPressPluginSettings();
$reactWordPressPluginSettings->register_settings();
$reactWordPressPluginSettings->section_text();
$reactWordPressPluginSettings->register_api_routes();
?>
