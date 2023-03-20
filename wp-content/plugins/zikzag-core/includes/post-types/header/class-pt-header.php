<?php

if (!class_exists('Zikzag_Theme_Helper')) {
    return;
}

/**
 * Class      Header
 * @package   PostType
 */

class Header
{
    /**
     * @var string
     *
     * Set post type params
     */
    private $type = 'header';
    private $slug;
    private $name;
    private $plural_name;

    /**
     * Header constructor.
     *
     * When class is instantiated
     */
    public function __construct() {
        // Register the post type
        $this->name = __( 'Header Templates', 'zikzag-core' );
        $this->slug = 'header';
        $this->plural_name = __( 'Header Templates', 'zikzag-core' );

        add_action('init', [$this, 'register']);

        add_filter('single_template', [$this, 'get_custom_pt_single_template']);
    }

    /**
     * Register post type
     */
    public function register() {
        $labels = [
            'name' => $this->name,
            'singular_name' => $this->name,
            'add_new' => sprintf( __('Add New Template', 'zikzag-core' ), $this->name ),
            'add_new_item' => sprintf( __('Add New %s', 'zikzag-core' ), $this->name ),
            'edit_item' => sprintf( __('Edit %s', 'zikzag-core'), $this->name ),
            'new_item' => sprintf( __('New %s', 'zikzag-core'), $this->name ),
            'all_items' => sprintf( __('All Templates', 'zikzag-core'), $this->plural_name ),
            'view_item' => sprintf( __('View %s', 'zikzag-core'), $this->name ),
            'search_items' => sprintf( __('Search %s', 'zikzag-core'), $this->name ),
            'not_found' => sprintf( __('No %s found' , 'zikzag-core'), strtolower($this->name) ),
            'not_found_in_trash' => sprintf( __('No %s found in Trash', 'zikzag-core'), strtolower($this->name) ),
            'parent_item_colon' => '',
            'menu_name' => $this->name
        ];
        $args = [
            'labels' => $labels,
            'public' => true,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'rewrite' => [ 'slug' => $this->slug ],
            'menu_position' =>  10,
            'show_in_rest'  => true,
            'supports' => ['title', 'editor', 'thumbnail', 'page-attributes'],
            'menu_icon' => 'dashicons-admin-page',
        ];
        register_post_type( $this->type, $args );
    }

    public function wrapper_header_open()
    {
        global $post;

        if ($post->post_type == $this->type) {
            echo '<header class="wgl-theme-header">';
                echo '<div class="wgl-site-header">';
                    echo '<div class="container-wrapper">';
        }
    }

    public function wrapper_header_close()
    {
        global $post;

        if ($post->post_type == $this->type) {
                    echo '</div>';
                echo '</div>';
            echo '</header>';
        }
    }

    // https://codex.wordpress.org/Plugin_API/Filter_Reference/single_template
    function get_custom_pt_single_template($single_template)
    {
        global $post;

        if ($post->post_type == $this->type) {

            if (defined('ELEMENTOR_PATH')) {
                $elementor_template = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

                if ( file_exists( $elementor_template ) ) {
                    add_action( 'elementor/page_templates/canvas/before_content' , [$this, 'wrapper_header_open']);
                    add_action( 'elementor/page_templates/canvas/after_content' , [$this, 'wrapper_header_close']);
                    return $elementor_template;
                }
            }
            if (file_exists(get_stylesheet_directory() . '/single-header.php')) return $single_template;

            if (file_exists(get_template_directory().'/single-header.php')) return $single_template;

            $single_template = plugin_dir_path( dirname( __FILE__ ) ) . 'header/templates/single-header.php';
        }
        return $single_template;
    }
}