<?php 

use WglAddons\Templates\WglTeam;

$defaults = [
	'posts_per_line' => '3',
	'grid_gap' => '30',
	'hide_title' => '',
	'single_link_heading' => 'yes',
	'letter_count' => '100',
	'bg_color_type' => 'def',
	'hide_soc_icons' => '',
	'hide_department' => '',
	'info_align' => 'center',
	'hide_meta' => '',
	'hide_content' => true,
	'single_link_wrapper' => true,
	// Query
	'post_type' => 'team',
	'number_of_posts' => 'all',
	'order_by' => 'date',
	'grayscale_anim' => '',
	'info_anim' => '',
];
extract($defaults);

$style_gap = ($grid_gap != '0') ? ' style="margin-right: -'.esc_attr($grid_gap/2).'px; margin-left: -'.esc_attr($grid_gap/2).'px;"' : '';

$team_classes = ' team-col_'.$posts_per_line;
$team_classes .= ' a'.$info_align;

$team = new WglTeam();
ob_start();
	$team->render_wgl_team($defaults);
$team_items = ob_get_clean();


// Render
get_header();

echo '<div class="wgl-container">',
	'<div id="main-content">',
		'<div class="wgl_module_team', esc_attr($team_classes), '">',
			'<div class="team-items_wrap"', $style_gap, '>',
				$team_items,
			'</div>',
		'</div>',
	'</div>',
'</div>';

get_footer();
