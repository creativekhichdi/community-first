<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Zikzag
 * @since 1.0
 * @version 1.0
 */

get_header();
$sb = Zikzag_Theme_Helper::render_sidebars();
$row_class = $sb['row_class'] ?? '';
$column = $sb['column'] ?? '';
$container_class = $sb['container_class'] ?? '';
?>
    <div class="wgl-container<?php echo apply_filters('zikzag_container_class', $container_class); ?>">
        <div class="row<?php echo apply_filters('zikzag_row_class', $row_class); ?>">
            <div id='main-content' class="wgl_col-<?php echo apply_filters('zikzag_column_class', $column); ?>">
                <?php
                    get_template_part('templates/post/posts-list');
                    echo Zikzag_Theme_Helper::pagination();
                ?>
            </div>
            <?php
            // Sidebar
                echo (isset($sb['content']) && !empty($sb['content']) ) ? $sb['content'] : '';
            ?>
        </div>
    </div>
    <?php
get_footer();
?>
