<?php
if(!class_exists('CustomPostTypes'))
{
	/**
	 * A Person class that provides 3 additional meta fields
	 */
	class CustomPostTypes
	{
		protected $_post_type        = '';
		protected $_post_type_plural = '';
		protected $_meta             = array();
		protected $_taxonomies       = array();
		protected $_menu_icon        = '';

		/**
		 * The Constructor
		 * @param array Post type array('singular' => 'plural')
		 * @param array Taxonomy array('singular' => 'plural')
		 * @param string icon name
		 */
		public function __construct($post_type, $taxonomies = array(), $menu_icon = '' )
		{
			foreach ($post_type as $singular => $plural) {
				$this->_post_type = $singular;
				$this->_post_type_plural = $plural;
				break;
			}

			if (is_array($taxonomies)) {
				$this->_taxonomies = $taxonomies;
			}

			if (is_string($menu_icon)) {
				$this->_menu_icon = $menu_icon;
			}

			// register actions
			add_action('init', array(&$this, 'init'));
			add_action('admin_init', array(&$this, 'admin_init'));
		} // END public function __construct()

		/**
		 * hook into WP's init action hook
		 */
		public function init()
		{
			// Initialize Post Type
			$this->create_post_type();
			$this->create_taxonomies();
			add_action('save_post', array(&$this, 'save_post'));
		} // END public function init()

		/**
		 * Create the post type
		 */
		public function create_post_type()
		{
			$labels = array(
				'name'                => __(ucwords(str_replace("_", " ", $this->_post_type_plural))),
				'singular_name'       => __(ucwords(str_replace("_", " ", $this->_post_type))),
				'menu_name'           => __(ucwords(str_replace("_", " ", $this->_post_type_plural))),
				'parent_item_colon'   => __( '' ),
				'all_items'           => __(sprintf('All %s', ucwords(str_replace("_", " ", $this->_post_type_plural)))),
				'view_item'           => __(sprintf('View %s', ucwords(str_replace("_", " ", $this->_post_type)))),
				'add_new_item'        => __(sprintf('Add New %s', ucwords(str_replace("_", " ", $this->_post_type)))),
				'add_new'             => __( 'Add New' ),
				'edit_item'           => __(sprintf('Edit %s', ucwords(str_replace("_", " ", $this->_post_type)))),
				'update_item'         => __(sprintf('Update %s', ucwords(str_replace("_", " ", $this->_post_type)))),
				'search_items'        => __(sprintf('Search %s', ucwords(str_replace("_", " ", $this->_post_type_plural)))),
				'not_found'           => __(sprintf('No %s found', str_replace("_", " ", $this->_post_type_plural))),
				'not_found_in_trash'  => __(sprintf('No %s found in Trash', str_replace("_", " ", $this->_post_type_plural)))
			);

			$rewrite = array(
				'slug'                => $this->_post_type_plural,
				'with_front'          => true,
				'pages'               => true,
				'feeds'               => true,
			);

			$args = array(
				'description'         => __(sprintf('%s information pages', ucwords(str_replace("_", " ", $this->_post_type)))),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes' ),
				'public'              => true,
				'menu_position'       => 5,
				'menu_icon'           => $this->_menu_icon,
				'can_export'          => true,
				'has_archive'         => true,
				'taxonomies'          => $this->_taxonomies,
				'public'              => true,
				'rewrite'             => $rewrite,
				'capability_type'     => 'page',
			);

			register_post_type( $this->_post_type, $args );
		}

		/**
		 * Create taxonomies
		 */
		public function create_taxonomies() {
			foreach($this->_taxonomies as $taxonomy => $taxonomy_plural)
			{
				// Create individual taxonomy
				$this->create_taxonomy($taxonomy, $taxonomy_plural);
			}
		} // END public function create_taxonomies()

		/**
		 * Create a single taxonomy
		 */
		public function create_taxonomy($taxonomy, $taxonomy_plural) {
			$labels = array(
				'name'                       => _x( ucwords(str_replace("_", " ", $taxonomy_plural)), 'taxonomy general name' ),
				'singular_name'              => _x( ucwords(str_replace("_", " ", $taxonomy)), 'taxonomy singular name' ),
				'search_items'               => __( sprintf('Search %s', ucwords(str_replace("_", " ", $taxonomy_plural))) ),
				'all_items'                  => __( sprintf('All %s', ucwords(str_replace("_", " ", $taxonomy_plural))) ),
				'edit_item'                  => __( sprintf('Edit %s', ucwords(str_replace("_", " ", $taxonomy))) ),
				'update_item'                => __( sprintf('Update %s', ucwords(str_replace("_", " ", $taxonomy))) ),
				'add_new_item'               => __( sprintf('Add New %s', ucwords(str_replace("_", " ", $taxonomy))) ),
				'new_item_name'              => __( sprintf('New %s Name', ucwords(str_replace("_", " ", $taxonomy))) ),
				'menu_name'                  => __( ucwords(str_replace("_", " ", $taxonomy_plural)) ),
				'separate_items_with_commas' => __( sprintf('Seperate %s with commas', str_replace("_", " ", $taxonomy_plural)) ),
				'add_or_remove_items'        => __( sprintf('Add or remove %s', str_replace("_", " ", $taxonomy_plural)) ),
				'choose_from_most_used'      => __( sprintf('Choose from the most used %s', str_replace("_", " ", $taxonomy_plural)) ),
			);

			$args = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => sprintf('%s', $taxonomy_plural) ),
				'public'                => true,
				'show_in_nav_menus'     => true,
				'show_tagcloud'         => true,
			);

			register_taxonomy( $taxonomy_plural, array( $this->_post_type ), $args );
		} // END public function create_taxonomy()

		/**
		 * Save the metaboxes for this custom post type
		 */
		public function save_post($post_id)
		{
			// verify if this is an auto save routine.
			// If it is our form has not been submitted, so we dont want to do anything
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			{
				return;
			}

			if($_POST['post_type'] == $this->_post_type && current_user_can('edit_post', $post_id))
			{
				foreach($this->_meta as $field_name)
				{
					// Update the post's meta field
					update_post_meta($post_id, $field_name, $_POST[$field_name]);
				}
			}
			else
			{
				return;
			} // if($_POST['post_type'] == $this->_post_type && current_user_can('edit_post', $post_id))
		} // END public function save_post($post_id)

		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{
			// Add metaboxes
			add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
		} // END public function admin_init()

		/**
		 * hook into WP's add_meta_boxes action hook
		 */
		public function add_meta_boxes()
		{
			// Add this metabox to every selected post
			add_meta_box(
				sprintf('heartsandeyes_customposttypes_%s_section', $this->_post_type),
				sprintf('%s Information', ucwords(str_replace("_", " ", $this->_post_type))),
				array(&$this, 'add_inner_meta_boxes'),
				$this->_post_type
			);
		} // END public function add_meta_boxes()

		/**
		 * called off of the add meta box
		 */
		public function add_inner_meta_boxes($post)
		{
			// Render the job order metabox
			include(sprintf("%s/../templates/%s_metabox.php", dirname(__FILE__), $this->_post_type));
		} // END public function add_inner_meta_boxes($post)

		/**
		 * hook into WP's activation registration hook
		 */
		function activate() {}

		/**
		 * hook into WP's deactivation registration hook
		 */
		function deactivate() {}
	} // END class CustomPostType
} // END if(!class_exists('CustomPostType'))
