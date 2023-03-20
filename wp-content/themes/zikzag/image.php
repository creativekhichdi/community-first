<?php
/**
 * The template for displaying image attachments
 *
 * @package WordPress
 * @subpackage Zikzag
 * @since 1.0
 */
get_header();

$sb = Zikzag_Theme_Helper::render_sidebars();
$row_class = $sb['row_class'] ?? '';
$container_class = $sb['container_class'] ?? '';
$column = $sb['column'] ?? '';
?>
 
	<div class="wgl-container<?php echo apply_filters('zikzag_container_class', $container_class); ?>">
        <div class="row<?php echo apply_filters('zikzag_row_class', $row_class); ?>">
			<div id='main-content' class="wgl_col-<?php echo apply_filters('zikzag_column_class', $column); ?>">
				<?php
					// Start the loop.
					while ( have_posts() ) :
						the_post();

						/**
						* Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
						* or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
						*/
						$attachments = array_values(
							get_children(
								array(
									'post_parent' => $post->post_parent,
									'post_status' => 'inherit',
									'post_type' => 'attachment',
									'post_mime_type' => 'image',
									'order'   => 'ASC',
									'orderby' => 'menu_order ID',
								)
							)
						);
						
						foreach ( $attachments as $k => $attachment ) {
							if ( $attachment->ID == $post->ID ) {
								break;
							}
						}
						$k++;
						// If there is more than 1 attachment in a gallery
						if ( count( $attachments ) > 1 ) {
							if ( isset( $attachments[ $k ] ) ) {
								// get the URL of the next image attachment
								$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
							} else {                                        // or get the URL of the first image attachment
								$next_attachment_url = get_attachment_link( $attachments[0]->ID );
							}
						} else {
							// or, if there's only 1 image, get the URL of the image
							$next_attachment_url = wp_get_attachment_url();
						}
						?>				

						<div class="blog-post">
							<div class="single_meta attachment_media">
								<div class="blog-post_content">
									<?php
										echo '<h4 class="blog-post_title">' . esc_html(get_the_title()) . '</h4>';
									?>

									<div class="meta-wrapper">
										<?php
											Zikzag_Theme_Helper::posted_meta_on();
										?>
									</div>
									<div class="blog-post_media">
										<a href="<?php echo esc_url($next_attachment_url); ?>" title="<?php the_title_attribute(); ?>" rel="attachment">
											<?php
											$attachment_size = array(1170, 725); // image size.
											echo wp_get_attachment_image( get_the_ID(), $attachment_size );
											?>
										</a>
									</div>
									<?php the_content(); ?>
									
									<?php wp_link_pages(array('before' => '<div class="page-links">' . esc_html__('Pages:', 'zikzag'), 'after' => '</div>')); ?>
								</div>
							</div>		
						</div>

				<?php
					if ( comments_open() || '0' != get_comments_number() ) :
						comments_template();
					endif;
				?>
				<?php endwhile; // end of the loop. ?>
			</div>
			<?php
				echo (isset($sb['content']) && !empty($sb['content']) ) ? $sb['content'] : '';
			?>
		</div>

	</div>

<?php get_footer(); ?>