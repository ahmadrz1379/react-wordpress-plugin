<?php

/**
 * Class for registering custom post types in WordPress
 */
class CustomPostType
{

    /**
     * The post type key
     * @var string
     */
    private $post_type;

    /**
     * The arguments for registering the post type
     * @var array
     */
    private $args;

    /**
     * The labels for the post type
     * @var array
     */
    private $labels;

    /**
     * The taxonomies for the post type
     * @var array
     */
    private $taxonomies;

    /**
     * The meta boxes for the post type
     * @var array
     */
    private $meta_boxes;

    /**
     * Constructor to initialize the custom post type
     *
     * @param string $post_type Post type key (slug).
     * @param array $args Arguments for registering the post type.
     * @param array $labels Labels for the post type.
     * @param array $taxonomies Taxonomies for the post type.
     * @param array $meta_boxes Meta boxes for the post type.
     */
    public function __construct($post_type, $args = [], $labels = [], $taxonomies = [], $meta_boxes = [])
    {
        $this->post_type = $post_type;
        $this->args = $args;
        $this->labels = $labels;
        $this->taxonomies = $taxonomies;
        $this->meta_boxes = $meta_boxes;

        // Hook into WordPress to register the post type
        add_action('init', [$this, 'registerPostType']);
        add_action('init', [$this, 'registerTaxonomies']);
        add_action('add_meta_boxes', [$this, 'addMetaBoxes']);
        add_action('save_post', [$this, 'saveMetaBoxes']);
    }

    /**
     * Registers the custom post type with WordPress
     */
    public function registerPostType()
    {
        // Merge user-provided labels with defaults
        $default_labels = [
            'name' => ucfirst($this->post_type) . 's',
            'singular_name' => ucfirst($this->post_type),
            'menu_name' => ucfirst($this->post_type) . 's',
            'name_admin_bar' => ucfirst($this->post_type),
            'add_new' => 'Add New',
            'add_new_item' => 'Add New ' . ucfirst($this->post_type),
            'edit_item' => 'Edit ' . ucfirst($this->post_type),
            'new_item' => 'New ' . ucfirst($this->post_type),
            'view_item' => 'View ' . ucfirst($this->post_type),
            'search_items' => 'Search ' . ucfirst($this->post_type) . 's',
            'not_found' => 'No ' . strtolower($this->post_type) . 's found.',
            'not_found_in_trash' => 'No ' . strtolower($this->post_type) . 's found in Trash.',
            'all_items' => 'All ' . ucfirst($this->post_type) . 's',
            'archives' => ucfirst($this->post_type) . ' Archives',
            'insert_into_item' => 'Insert into ' . strtolower($this->post_type),
            'uploaded_to_this_item' => 'Uploaded to this ' . strtolower($this->post_type),
            'featured_image' => 'Featured Image',
            'set_featured_image' => 'Set featured image',
            'remove_featured_image' => 'Remove featured image',
            'use_featured_image' => 'Use as featured image',
            'menu_name' => ucfirst($this->post_type) . 's',
            'filter_items_list' => 'Filter ' . strtolower($this->post_type) . 's list',
            'items_list_navigation' => ucfirst($this->post_type) . 's list navigation',
            'items_list' => ucfirst($this->post_type) . 's list',
        ];
        $labels = array_merge($default_labels, $this->labels);

        // Merge user-provided arguments with defaults
        $default_args = [
            'label' => ucfirst($this->post_type),
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => ['slug' => $this->post_type],
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'comments'],
            'show_in_rest' => true, // Enable block editor support
        ];
        $args = array_merge($default_args, $this->args);

        // Register the post type
        register_post_type($this->post_type, $args);
    }

    /**
     * Registers the taxonomies for the custom post type
     */
    public function registerTaxonomies()
    {
        foreach ($this->taxonomies as $taxonomy) {
            register_taxonomy_for_object_type($taxonomy, $this->post_type);
        }
    }

    /**
     * Adds custom meta boxes to the post type
     */
    public function addMetaBoxes()
    {
        foreach ($this->meta_boxes as $meta_box) {
            add_meta_box(
                $meta_box['id'],
                $meta_box['title'],
                [$this, 'renderMetaBox'],
                $this->post_type,
                $meta_box['context'],
                $meta_box['priority'],
                $meta_box['callback_args']
            );
        }
    }

    /**
     * Renders the custom meta box
     *
     * @param WP_Post $post The post object.
     * @param array $meta_box The meta box arguments.
     */
    public function renderMetaBox($post, $meta_box)
    {
        $callback_args = $meta_box['args'];
        call_user_func($callback_args['callback'], $post);
    }

    /**
     * Saves the custom meta box data
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function saveMetaBoxes($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        foreach ($this->meta_boxes as $meta_box) {
            if (isset($_POST[$meta_box['id']])) {
                update_post_meta($post_id, $meta_box['id'], sanitize_text_field($_POST[$meta_box['id']]));
            }
        }
    }
}

// Example usage
new CustomPostType(
    'book',
    [
        'menu_icon' => 'dashicons-book',
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'rewrite' => ['slug' => 'books'],
        'has_archive' => true,
    ],
    [
        'singular_name' => 'Book',
        'name' => 'Books',
    ],
    [
        'category',
        'post_tag'
    ],
    [
        [
            'id' => 'book_author',
            'title' => 'Book Author',
            'context' => 'normal',
            'priority' => 'high',
            'callback_args' => [
                'callback' => function ($post) {
                    $value = get_post_meta($post->ID, 'book_author', true);
                    echo '<input type="text" name="book_author" value="' . esc_attr($value) . '" class="widefat">';
                }
            ]
        ]
    ]
);
