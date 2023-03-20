<?php

defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'Zikzag_Core' ) ) {
		return;
} 

/**
* Wgl Categories 
*
*
* @class        Wgl_Cat_Images
* @version      1.0
* @category     Class
* @author       WebGeniusLab
*/
if ( ! class_exists( 'Wgl_Cat_Images' ) ) {

class Wgl_Cat_Images {
 
 /*
	* https://catapultthemes.com/adding-an-image-upload-field-to-categories/
	* Initialize the class and start calling our hooks and filters
	* @since 1.0.0
 */
public function init() {
	 add_action( 'category_add_form_fields', array ( $this, 'add_category_image' ), 10, 2 );
	 add_action( 'category_edit_form_fields', array ( $this, 'update_category_image' ), 10, 2 );
	 add_action( 'created_category', array ( $this, 'updated_category_image' ), 10, 2 );
	 add_action( 'edited_category', array ( $this, 'updated_category_image' ), 10, 2 );
	 add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );


			// Add form
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_icons' ) );
		add_action( 'product_cat_edit_form_fields', array( $this, 'update_category_product_icons' ), 10 );
		add_action( 'created_term', array( $this, 'save_category_fields_icon' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields_icon' ), 10, 3 );

} 

public function load_media() {
	wp_enqueue_script('zikzag-cat-meta', get_template_directory_uri() . '/core/admin/js/cat_img_upload.js', array(), false, false);
	wp_enqueue_media();
	if( null !== ( $screen = get_current_screen() ) && 'edit-category' !== $screen->id ) {
			return;
	}
} 
 
 /*
	* Add a form field in the new category page
	* @since 1.0.0
 */ 
 public function add_category_image ( $taxonomy ) {
		?>
	 <div class="form-field term-group wgl-image-form">
		 <label for="category-icon-image-id"><?php esc_html_e('Icon Image', 'zikzag'); ?></label>
		 <input type="hidden" id="category-icon-image-id" name="category-icon-image-id" class="custom_media_url" value="">
		 <div class="category-image-wrapper"></div>
		 <p>
			 <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button_icon" name="ct_tax_media_button" value="<?php esc_attr_e( 'Add Image', 'zikzag' ); ?>" />
			 <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove_icon" name="ct_tax_media_remove" value="<?php esc_attr_e( 'Remove Image', 'zikzag' ); ?>" />
		</p>
	 </div>
 <?php
 }

 /*
	* Add a form field in the new category page
	* @since 1.0.0
 */ 
 public function add_category_icons ( ) {    ?>

	 <div class="form-field term-group wgl-image-form">
		 <label for="category-icon-image-id"><?php esc_html_e('Icon Image', 'zikzag'); ?></label>
		 <input type="hidden" id="category-icon-image-id" name="category-icon-image-id" class="custom_media_url" value="">
		 <div class="category-image-wrapper"></div>
		 <p>
			 <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button_icon" name="ct_tax_media_button" value="<?php esc_attr_e( 'Add Image', 'zikzag' ); ?>" />
			 <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove_icon" name="ct_tax_media_remove" value="<?php esc_attr_e( 'Remove Image', 'zikzag' ); ?>" />
		</p>
	 </div>
 <?php
 } 
 
 /*
	* Edit the form field
	* @since 1.0.0
 */
 public function update_category_image ( $term, $taxonomy ) { ?>
	 <tr class="form-field term-group-wrap wgl-image-form">
		 <th scope="row">
			 <label for="category-icon-image-id"><?php esc_html_e( 'Icon Image', 'zikzag' ); ?></label>
		 </th>
		 <td>
			 <?php $image_id = get_term_meta ( $term -> term_id, 'category-icon-image-id', true ); ?>
			 <input type="hidden" id="category-icon-image-id" name="category-icon-image-id" class="custom_media_url" value="<?php echo esc_attr($image_id); ?>">
			 <div class="category-image-wrapper">
				 <?php if ( $image_id ) { ?>
					 <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
				 <?php } ?>
			 </div>
			 <p>
				 <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button_icon" name="ct_tax_media_button" value="<?php esc_attr_e( 'Add Image', 'zikzag' ); ?>" />
				 <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove_icon" name="ct_tax_media_remove" value="<?php esc_attr_e( 'Remove Image', 'zikzag' ); ?>" />
			 </p>
		 </td>
	 </tr>
 <?php
 } /*
	* Edit the form field
	* @since 1.0.0
 */
 public function update_category_product_icons ( $term ) { ?>
	 <tr class="form-field term-group-wrap wgl-image-form">
		 <th scope="row">
			 <label for="category-icon-image-id"><?php esc_html_e( 'Icon Image', 'zikzag' ); ?></label>
		 </th>
		 <td>
			 <?php $image_id = get_term_meta ( $term -> term_id, 'category-icon-image-id', true ); ?>
			 <input type="hidden" id="category-icon-image-id" name="category-icon-image-id" class="custom_media_url" value="<?php echo esc_attr($image_id); ?>">
			 <div class="category-image-wrapper">
				 <?php if ( $image_id ) { ?>
					 <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
				 <?php } ?>
			 </div>
			 <p>
				 <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button_icon" name="ct_tax_media_button" value="<?php esc_attr_e( 'Add Image', 'zikzag' ); ?>" />
				 <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove_icon" name="ct_tax_media_remove" value="<?php esc_attr_e( 'Remove Image', 'zikzag' ); ?>" />
			 </p>
		 </td>
	 </tr>
 <?php
 }

/*
 * Update the form field value
 * @since 1.0.0
 */
 public function updated_category_image ( $term_id, $tt_id ) {
	if( isset( $_POST['category-icon-image-id'] ) && '' !== $_POST['category-icon-image-id'] ){
		$image = sanitize_text_field($_POST['category-icon-image-id']);
		update_term_meta ( $term_id, 'category-icon-image-id', $image );
	} else {
		update_term_meta ( $term_id, 'category-icon-image-id', '' );
	}

 }

 /*
 * Update the form field value
 * @since 1.0.0
 */
 public function save_category_fields_icon ( $term_id, $tt_id ) {
	if( isset( $_POST['category-icon-image-id'] ) && '' !== $_POST['category-icon-image-id'] ){
		$image = sanitize_text_field($_POST['category-icon-image-id']);
		update_term_meta ( $term_id, 'category-icon-image-id', $image );
	} else {
		update_term_meta ( $term_id, 'category-icon-image-id', '' );
	}
 }
}
 
$Wgl_Cat_Images = new Wgl_Cat_Images();
$Wgl_Cat_Images -> init();
 
}