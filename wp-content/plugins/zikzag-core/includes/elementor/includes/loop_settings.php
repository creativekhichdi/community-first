<?php

namespace WglAddons\Includes;

use Elementor\Plugin;
use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
* Wgl Elementor Loop Settings
*
*
* @class        Wgl_Loop_Settings
* @version      1.0
* @category     Class
* @author       WebGeniusLab
*/

if (! class_exists('Wgl_Loop_Settings')) {

	class Wgl_Loop_Settings
	{

		private static $instance = null;

		public static function get_instance()
		{
			if (null == self::$instance) {
				self::$instance = new self( );
			}
			return self::$instance;
		}

		public static function buildQuery($query)
		{
			$query_builder = new Wgl_Query_Builder( $query );
			return $query_builder->build();
		}

		/**
		 * Cache Query
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public static function cache_query($args = [])
		{
			$cache_key = http_build_query($args);
			$custom_query = wp_cache_get($cache_key, 'zikzag_theme_cache');
			if (false === $custom_query) {
				$custom_query = new \WP_Query($args);
				if (!is_wp_error($custom_query) && $custom_query->have_posts()) {

					wp_cache_set($cache_key, $custom_query, 'zikzag_theme_cache');
				}
			}
			return $custom_query;
		}

		public static function init($self, $atts = [])
		{
			if (! $self) return;

			$self->start_controls_section(
				'query_section',
				[
					'label' => esc_html__( 'Query', 'zikzag-core' ),
					'tab' => Controls_Manager::TAB_SETTINGS
				]
			);

			$self->add_control(
				'number_of_posts',
				[
					'label' => esc_html__( 'Post count', 'zikzag-core' ),
					'type' => Controls_Manager::NUMBER,
					'default' => '12',
					'min' => 1,
					'step' => 1
				]
			);

			$self->add_control(
				'order_by',
				[
					'label' => esc_html__( 'Order by', 'zikzag-core' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'date',
					'options' => [
						'date' => esc_html__( 'Date', 'zikzag-core' ),
						'title' => esc_html__( 'Title', 'zikzag-core' ),
						'author' => esc_html__( 'Author', 'zikzag-core' ),
						'modified' => esc_html__( 'Modified', 'zikzag-core' ),
						'rand' => esc_html__( 'Random', 'zikzag-core' ),
						'comment_count' => esc_html__( 'Comments', 'zikzag-core' ),
						'menu_order' => esc_html__( 'Menu Order', 'zikzag-core' ),
					],
				]
			);

			$self->add_control(
				'order',
				[
					'label' => esc_html__( 'Order', 'zikzag-core' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'DESC' => esc_html__( 'Descending', 'zikzag-core' ),
						'ASC' => esc_html__( 'Ascending', 'zikzag-core' ),
					],
					'default' => 'DESC',
				]
			);

			if (! isset($atts['hide_cats'])) {
				$self->add_control(
					'hr_cats',
					[ 'type' => Controls_Manager::DIVIDER ]
				);
				$self->add_control(
					'categories',
					[
						'label' => esc_html__( 'Filter By Category Slug', 'zikzag-core' ),
						'type' => Controls_Manager::SELECT2,
						'multiple' => true,
						'label_block' => true,
						'options' => self::categories_suggester(),
					]
				);
				$self->add_control(
					'exclude_categories',
					[
						'label' => esc_html__( 'Exclude These Categories', 'zikzag-core' ),
						'type' => Controls_Manager::SWITCHER,
						'description' => esc_html__( 'Leave empty for all', 'zikzag-core' ),
					]
				);
			}

			if (! isset($atts['hide_tags']) ) :

				$self->add_control(
					'hr_tags',
					[ 'type' => Controls_Manager::DIVIDER ]
				);

				$self->add_control(
					'tags',
					[
						'label' => esc_html__( 'Filter By Tags Slug', 'zikzag-core' ),
						'type' => Controls_Manager::SELECT2,
						'multiple' => true,
						'label_block' => true,
						'options' => self::tags_suggester(),
					]
				);

				$self->add_control(
					'exclude_tags',
					[
						'label' => esc_html__( 'Exclude These Tags', 'zikzag-core' ),
						'type' => Controls_Manager::SWITCHER,
						'description' => esc_html__( 'Leave empty for all', 'zikzag-core' ),
					]
				);

			endif;

			$self->add_control(
				'hr_tax',
				[ 'type' => Controls_Manager::DIVIDER ]
			);

			$self->add_control(
				'taxonomies',
				[
					'label' => esc_html__( 'Taxonomies', 'zikzag-core' ),
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'label_block' => true,
					'options' => self::taxonomies_suggester( $atts ),
				]
			);
			$self->add_control(
				'exclude_taxonomies',
				[
					'label' => esc_html__( 'Exclude These Taxonomies', 'zikzag-core' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'On', 'zikzag-core' ),
					'label_off' => esc_html__( 'Off', 'zikzag-core' ),
					'description' => esc_html__( 'Filter output by custom taxonomies categories, enter category names here.', 'zikzag-core' ),
				]
			);

			if (! isset($atts['hide_individual_posts'])) {
				$self->add_control(
					'hr_posts',
					[ 'type' => Controls_Manager::DIVIDER ]
				);

				$self->add_control(
					'by_posts',
					[
						'label' => esc_html__( 'Individual Posts/Pages/Custom Post Types', 'zikzag-core' ),
						'type' => Controls_Manager::SELECT2,
						'multiple' => true,
						'label_block' => true,
						'options' => self::by_posts_suggester( $atts ),
					]
				);

				$self->add_control(
					'exclude_any',
					[
						'label' => esc_html__( 'Exclude These Posts/Pages/Custom Post Types', 'zikzag-core' ),
						'type' => Controls_Manager::SWITCHER,
						'description' => esc_html__( 'Individual Posts/Pages/Custom Post Types', 'zikzag-core' ),
					]
				);
			}

			$self->add_control(
				'hr_author',
				[ 'type' => Controls_Manager::DIVIDER ]
			);

			$self->add_control(
				'author',
				[
					'label' => esc_html__( 'Author', 'zikzag-core' ),
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'label_block' => true,
					'options' => self::by_author_suggester(),
				]
			);
			$self->add_control(
				'exclude_author',
				[
					'label' => esc_html__( 'Exclude Author', 'zikzag-core' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'On', 'zikzag-core' ),
					'label_off' => esc_html__( 'Off', 'zikzag-core' ),
					'description' => esc_html__( 'Filter by author name', 'zikzag-core' ),
				]
			);

			$self->end_controls_section();
		}

		/**
		 * @param $taxonomy
		 * @param $helper
		 *
		 * @since 1.0
		 */
		public static function get_term_parents_list($term_id, $taxonomy, $args = [])
		{
			$list = '';
			$term = get_term( $term_id, $taxonomy );

			if (is_wp_error($term)) {
				return $term;
			}

			if (! $term) {
				return $list;
			}

			$term_id = $term->term_id;

			$defaults = [
				'format' => 'name',
				'separator' => '/',
				'inclusive' => true,
			];

			$args = wp_parse_args( $args, $defaults );

			foreach (['inclusive'] as $bool) {
				$args[ $bool ] = wp_validate_boolean( $args[ $bool ] );
			}

			$parents = get_ancestors( $term_id, $taxonomy, 'taxonomy' );

			if ($args['inclusive']) {
				array_unshift( $parents, $term_id );
			}

			$a = count($parents) - 1;
			foreach (array_reverse( $parents ) as $index => $term_id) {
				$parent = get_term( $term_id, $taxonomy );
				$temp_sep = $args['separator'];
				$lastElement = reset($parents);
				$first = end($parents);

				if ($index == $a - 1) {
					$temp_sep = '';
				}

				if ($term_id != $lastElement) {
					$name = $parent->name;
					$list .= $name . $temp_sep;
				}
			}

			return $list;
		}

		public static function categories_suggester() {
			$content = [];

			foreach (get_categories() as $cat) {
				$args = [
				  'separator' => ' > ',
				  'format' => 'name',
				];
				$parent = self::get_term_parents_list( $cat->cat_ID, 'category', [] );

				$content[(string) $cat->slug] = $cat->cat_name.(! empty($parent) ? esc_html__( ' (Parent categories: (', 'zikzag-core') .$parent.'))' : "");
			}

			return $content;
		}


		/**
		 * @param query tags
		 *
		 * @since 1.0
		 * @return array
		 */
		public static function tags_suggester()
		{
			$content = [];

			foreach (get_tags() as $tag) {
				$content[(string)$tag->slug] = $tag->name;
			}

			return $content;
		}

		public static function getTaxonomies()
		{
			$taxonomy_exclude = (array) apply_filters( 'get_categories_taxonomy', 'category' );
			$taxonomy_exclude[] = 'post_tag';
			$taxonomies = [];

			foreach (get_taxonomies() as $taxonomy) {
				if (! in_array( $taxonomy, $taxonomy_exclude )) {
					$taxonomies[] = $taxonomy;
				}
			}
			return $taxonomies;
		}


		/**
		 * @param query taxonomies
		 *
		 * @since 1.0
		 * @return array
		 */
		public static function taxonomies_suggester($atts)
		{
			$content = $args = [];
			$taxonomies = self::getTaxonomies();

			if (isset($atts['post_type'])) {
				$type = $atts['post_type'];

				$taxonomies = array_reduce($taxonomies, function($arr, $item) use ($type) {
					if (strpos($item, $type) !== false) {
						$arr[] = $item;
					}
					return $arr;
				});
			}

			$result = get_terms($taxonomies, $args);

			foreach ($result as $tag) {
				$args = [
					'separator' => ' > ',
					'format' => 'name',
				];
				$parent = self::get_term_parents_list($tag->term_id, $tag->taxonomy, $args);
				$content[ $tag->taxonomy.":".$tag->slug ] = $tag->name . ' (' . $tag->taxonomy . ')' . (! empty($parent) && !is_wp_error($parent) ? esc_html__( ' (Parent categories: (', 'zikzag-core') . $parent . '))' : '');
			}
			return $content;
		}


		/**
		 * @param query posts
		 *
		 * @since 1.0
		 * @return array
		 */
		public static function by_posts_suggester($atts)
		{
			$content = $args = [];

			if (! isset($atts['post_type']) ) :
				$args['post_type'] = 'any';
			else :
				$args['post_type'] = $atts['post_type'];
			endif;

			$args['numberposts'] = -1;
			$posts = get_posts( $args );
			foreach ($posts as $post) {
				$content[$post->post_name] = $post->post_title;
			}

			return $content;
		}


		/**
		 * @param query posts
		 *
		 * @since 1.0
		 * @return array
		 */
		public static function by_author_suggester()
		{
			$content = [];
			foreach (get_users() as $user) {
				$content[(string) $user->ID] = (string) $user->data->user_nicename;
			}
			return $content;
		}


	}
	new Wgl_Loop_Settings();
}

/**
* Wgl Query Builder
*
*
* @class       Wgl_Query_Builder
* @version     1.0
* @category    Class
* @author      WebGeniusLab
*/
if (! class_exists('Wgl_Query_Builder')) {
	class Wgl_Query_Builder
	{
		/**
		 * @since   1.0
		 * @var     array
		 */
		private $args = [
			'post_status' => 'publish', // show only published posts #1098
		];

		private $data_attr = [];

		private static $instance = null;
		public static function get_instance()
		{
			if (null == self::$instance) {
				self::$instance = new self( );
			}

			return self::$instance;
		}

		function __construct($data)
		{
			// Include Item
			foreach ($data as $key => $value) {
				$method = 'parse_' . $key;
				if (stripos($key, 'exclude_') === false) {
					if (method_exists( $this, $method )) {
						if (! empty($value)) {
							$this->$method( $value );
						}
					}
				}
			}

			// Exclude Item
			foreach ($data as $k => $v) {
				$method = 'parse_' . $k;
				if (stripos($k, 'exclude_') !== false) {
					if (method_exists( $this, $method )) {
						if (! empty($v)) {
							$this->$method( $v );
						}
					}
				}
			}
		}

		/**
		 * Pages count
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_number_of_posts($value)
		{
			$this->args['posts_per_page'] = 'All' === $value ? - 1 : (int) $value;
		}

		/**
		 * Sorting field
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_order_by($value)
		{
			$this->args['orderby'] = $value;
		}

		/**
		 * Sorting order
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_order($value)
		{
			$this->args['order'] = $value;
		}

		/**
		 * By author
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_author($value)
		{
			$value = implode(',', $value);
			$this->data_attr['author_id'] = $value;
			$this->args['author'] = $value;
		}

		/**
		 * Exclude author
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_exclude_author($value)
		{
			if (! isset($this->data_attr['author_id'])) {
				return;
			}
			if (isset($this->args['author'])) {
				unset($this->args['author']);
			}
			$author_id = [];
			$author_id[] = $this->data_attr['author_id'];
			$this->args['author__not_in'] = $author_id;
		}

		/**
		 * By categories
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_categories($value)
		{
			if (empty($value)) {
				return;
			}

			$this->args['category_name'] = implode(", ", (array) $value);
		}

		/**
		 * Exclude categories
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_exclude_categories($value)
		{
			if (! isset($this->args['category_name'])) {
				return;
			}

			$list = explode(", ", $this->args['category_name']);

			$id_list = [];
			foreach ($list as $key => $value) {
				$idObj = get_category_by_slug($value);
				$id_list[] = '-'.$idObj->term_id;
			}
			$id_list = implode(",", $id_list);
			$this->args['cat'] = $id_list;
			unset($this->args['category_name']);
		}

		/**
		 * Get Taxonomies
		 * @since 1.0
		 *
		 * @param $value
		 */
		private function get_tax($value)
		{

			$terms = (array) $value;
			$this->args['tax_query'] = [ 'relation' => 'AND' ];

			$item = $id_list = [];

			$taxonomies = get_terms( Wgl_Loop_Settings::getTaxonomies() );
			foreach ($terms as $key => $value) {
				$item_t = explode(":", $value);
				if (isset($item_t[1])) {
					$idObj = get_term_by('slug', $item_t[1], $item_t[0]);
					$id_list[] = $idObj->term_id;
				}
			}

			$terms = get_terms( Wgl_Loop_Settings::getTaxonomies(),
				[ 'include' => array_map( 'abs', $id_list ) ] );
			foreach ($terms as $t) {
				$item[ $t->taxonomy ][] = $t->slug;
			}

			return $item;
		}

		/**
		 * By taxonomies
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_taxonomies($value)
		{
			if (empty($value)) {
				return;
			}

			$this->data_attr['taxonomies'] = $value;

			$item = $this->get_tax($value);

			foreach ($item as $taxonomy => $terms) {
				$this->args['tax_query'][] = [
					'field' => 'slug',
					'taxonomy' => $taxonomy,
					'terms' => $terms,
					'operator' => 'IN',
				];
			}
		}

		/**
		 * Exclude tax slugs
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_exclude_taxonomies()
		{
			if (! isset($this->data_attr['taxonomies'])) {
				return;
			}
			if (isset($this->args['tax_query'])) {
				unset($this->args['tax_query']);
			}

			$value = $this->data_attr['taxonomies'];

			$item = $this->get_tax($value);

			foreach ($item as $taxonomy => $terms) {
				$this->args['tax_query'][] = [
					'field' => 'slug',
					'taxonomy' => $taxonomy,
					'terms' => $terms,
					'operator' => 'NOT IN',
				];
			}
		}

		/**
		 * By tags slugs
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_tags($value)
		{
			if (empty($value)) {
				return;
			}
			$this->data_attr['tags'] = $value;
			$in = $not_in = [];
			$tags_slugs = $value;
			foreach ($tags_slugs as $tag) {
				$in[] = $tag;
			}
			$this->args['tag_slug__in'] = $in;
		}

		/**
		 * Exclude tags slugs
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_exclude_tags($value)
		{
			if (! isset($this->data_attr['tags'])) {
				return;
			}

			$list = $this->data_attr['tags'];
			$id_list = [];
			foreach ($list as $key => $value) {
				$idObj = get_term_by('slug', $value,'post_tag');
				$id_list[] = (int) $idObj->term_id;

			}

			$this->args['tag__not_in'] = $id_list;

			unset($this->args['tag_slug__in']);
		}

		/**
		 * By posts slugs
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_by_posts($value)
		{
			$in = [];
			$this->data_attr['posts_in'] = $value;
			$slugs = $value;
			if (! empty($slugs)) {
				foreach ($slugs as $slug) {
					$in[] = $slug;
				}
				$this->args['post_name__in'] = $in;
			}
		}

		/**
		 * Exclude posts slugs
		 * @since 1.0
		 *
		 * @param $value
		 */
		protected function parse_exclude_any($value)
		{
			global $post;
			if (! isset($this->data_attr['posts_in'])) {
				return;
			}
			if (isset($this->args['post_name__in'])) {
				unset($this->args['post_name__in']);
			}

			$options = [];
			$value = $this->data_attr['posts_in'];

			$list = new \WP_Query( [
				'post_type' => 'any',
				'post_name__in' => $value,
			] );
			foreach ($list->posts as $obj) {
				$options[] = $obj->ID;
			}
			$this->args['post__not_in'] = $options;
		}

		/**
		 * @since 1.0
		 *
		 * @param $id
		 */
		public function excludeId($id)
		{
			if (! isset( $this->args['post__not_in'] )) {
				$this->args['post__not_in'] = [];
			}
			if (is_array( $id )) {
				$this->args['post__not_in'] = array_merge( $this->args['post__not_in'], $id );
			} else {
				$this->args['post__not_in'][] = $id;
			}
		}

		public function filter_where($where = '')
		{
			return $where . ' AND '. $this->args['where'];
		}

		public function build()
		{

			if (isset($this->args['where'])) {
				add_filter( 'posts_where', [ $this, 'filter_where' ] );
			}

			$output = [ $this->args, new \WP_Query( $this->args ) ];

			if (isset($this->args['where'])) {
				remove_filter( 'posts_where', [ $this, 'filter_where' ] );
			}

			return $output;
		}
	}
}

?>