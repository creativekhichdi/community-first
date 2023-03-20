<?php

use WglAddons\Templates\WglTeam;

$sb = Zikzag_Theme_Helper::render_sidebars();
$row_class = $sb['row_class'];
$column = $sb['column'];
$container_class = $sb['container_class'];

$defaults = [
	'title' => '',
	'posts_per_line' => '2',
	'grid_gap' => '',
	'info_align' => 'center',
	'single_link_wrapper' => false,
	'single_link_heading' => true,
	'hide_title' => false,
	'hide_meta' => false,
	'hide_soc_icons' => false,
	'grayscale_anim' => false,
	'info_anim' => false,
];
extract($defaults);

$team_image_dims = ['width' => '940', 'height' => '1000']; // ratio = 0.96


// Render
get_header();

echo '<div class="wgl-container', apply_filters('zikzag_container_class', $container_class),'">';
	echo '<div class="row', esc_attr($row_class), '">';
		echo '<div id="main-content" class="wgl_col-', apply_filters('zikzag_column_class', $column), '">';

			while ( have_posts() ) : the_post();
				$item = new WglTeam();

				echo '<div class="row single_team_page">',
					'<div class="wgl_col-12">',
						$item->render_wgl_team_item(true, $defaults, $team_image_dims, false),
					'</div>',
					'<div class="wgl_col-12">',
						the_content( esc_html__('Read more!', 'zikzag-core') ),
					'</div>',
				'</div>';
			endwhile;
			wp_reset_postdata();

		echo '</div>';

		// Sidebar
		if (!empty($sb['content'])) echo $sb['content'];

	echo '</div>';
echo '</div>';

get_footer();
