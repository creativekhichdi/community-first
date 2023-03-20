<?php

/**
 * The template for displaying 404 page
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package    WordPress
 * @subpackage Zikzag
 * @since      1.0
 * @version    1.0
 */


get_header();

?>
<div class="wgl-container full-width">
  <div class="row">
    <div class="wgl_col-12">
      <section class="page_404_wrapper">
        <div class="page_404_wrapper-container">
          <div class="row">
            <div class="wgl_col-12 wgl_col-md-12">
              <div class="main_404-wrapper">
                <div class="banner_404"><img src="<?php echo esc_url(get_template_directory_uri() . "/img/404.png"); ?>" alt="<?php echo esc_attr__('404', 'zikzag'); ?>"></div>
                <h2 class="banner_404_title"><span><?php echo esc_html__('Sorry We Can\'t Find That Page!', 'zikzag'); ?></span></h2>
                <p class="banner_404_text"><?php echo esc_html__('The page you are looking for was moved, removed, renamed or never existed.', 'zikzag'); ?></p>
                <div class="zikzag_404_search">
                  <?php get_search_form(); ?>
                </div>
                <div class="zikzag_404__button">
                  <a class="zikzag_404__link wgl-button" href="<?php echo esc_url(home_url('/')); ?>">
                    <?php esc_html_e('Take Me Home', 'zikzag'); ?>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<?php get_footer();
