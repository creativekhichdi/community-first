<?php

/**
 * Template for single posts navigation section.
 *
 *
 * @author      WebGeniusLab
 * @subpackage  Zikzag
 * @since       1.0
 * @version     1.0
 */

$prevPost = get_adjacent_post(false, '', true);
$nextPost  = get_adjacent_post(false, '', false);

// Allowed HTML render
$allowed_html = [
	'a' => [
		'href' => true, 'title' => true,
		'class' => true, 'style' => true,
	],
	'br' => [],
	'b' => [],
	'em' => [],
	'strong' => [],
];


if ($nextPost || $prevPost) :

	echo '<section class="zikzag-post-navigation">';

		if (is_a($prevPost, 'WP_Post') ) :
			$image_prev_url = wp_get_attachment_image_src(get_post_thumbnail_id($prevPost->ID), 'thumbnail');

			$class_image_prev = ! empty($image_prev_url[0]) ? ' image_exist' : ' no_image';
			$img_prev_html = "<span class='image_prev" . esc_attr($class_image_prev)."'>";
				if (! empty($image_prev_url[0])) {
					$img_prev_html .= "<img src='". esc_url( $image_prev_url[0] ) ."' alt='". esc_attr($prevPost->post_title) ."'/>";
				} else {
					$img_prev_html .= '<span class="no_image_post"></span>';
				}
			$img_prev_html .= '</span>';

			echo '<div class="prev-link_wrapper">',
				'<div class="info_wrapper">',
					'<a href="', esc_url(get_permalink($prevPost->ID)), '" title="', esc_attr($prevPost->post_title), '">',
						'<div class="prev-link-icon_wrapper"><span></span></div>',
						'<div class="prev-link-info_wrapper">',
							'<h4 class="prev_title">', wp_kses( $prevPost->post_title, $allowed_html ), '</h4>',
							'<span class="meta-wrapper">',
								'<span class="date_post">', esc_html(get_the_time(get_option( 'date_format' ), $prevPost->ID)), '</span>',
							'</span>',
						'</div>',
					'</a>',
				'</div>',
			'</div>';
		endif;

		if (is_a($nextPost, 'WP_Post') ) :
			$image_next_url = wp_get_attachment_image_src(get_post_thumbnail_id($nextPost->ID), 'thumbnail');

			$class_image_next = ! empty($image_next_url[0]) ? ' image_exist' : ' no_image';
			$img_next_html = "<span class='image_next".esc_attr($class_image_next)."'>";
				if (! empty($image_next_url[0])) {
					$img_next_html .= "<img src='" . esc_url( $image_next_url[0] ) . "' alt='". esc_attr( $nextPost->post_title ) ."'/>";
				} else {
					$img_next_html .= "<span class='no_image_post'></span>";
				}
			$img_next_html .= "</span>";
			echo '<div class="next-link_wrapper">',
				'<div class="info_wrapper">',
					'<a href="', esc_url(get_permalink($nextPost->ID)), '" title="', esc_attr( $nextPost->post_title ), '">',
						'<div class="next-link-info_wrapper">',
							'<h4 class="next_title">', wp_kses( $nextPost->post_title, $allowed_html ), '</h4>',
							'<span class="meta-wrapper">',
								'<span class="date_post">', esc_html(get_the_time(get_option( 'date_format' ), $nextPost->ID)), '</span>',
							'</span>',
						'</div>',
						'<div class="next-link-icon_wrapper"><span></span></div>',
					'</a>',
				'</div>',
			'</div>';
		endif;

	echo '</section>';

endif;