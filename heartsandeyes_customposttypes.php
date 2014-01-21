<?php
/*
Plugin Name: Hearts and Eyes Custom Post Types
Plugin URI: https://github.com/arnoesterhuizen/heartsandeyes_customposttypes
Description: Declares a plugin that will create custom post types for Hearts and Eyes.
Version: 1.1
Author: Arno Esterhuizen
Author URI: https://www.facebook.com/arno.esterhuizen
License: GPLv2
*/

if(!class_exists('HeartsAndEyes_CustomPostTypes'))
{
	class HeartsAndEyes_CustomPostTypes
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Initialize Settings
			require_once(sprintf("%s/settings.php", dirname(__FILE__)));
			$HeartsAndEyes_CustomPostTypes_Settings = new HeartsAndEyes_CustomPostTypes_Settings();

			// Register custom post types
			require_once(sprintf("%s/post-types/custom_post_types.php", dirname(__FILE__)));
			$Production = new CustomPostTypes(array('production' => 'productions'), array(), 'dashicons-exerpt-view');
			$Person     = new CustomPostTypes(array('person' => 'people'), array('role' => 'roles'), 'dashicons-admin-users');

			// Register shortcodes
			require_once(sprintf("%s/shortcodes.php", dirname(__FILE__)));
			$HeartsAndEyes_CustomShortcodes = new HeartsAndEyes_CustomShortcodes();

			// Register stylesheet
			add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );

			// Register settings pages
			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));

			add_action( 'dashboard_glance_items', array( $this, 'admin_dashboard_widget' ) );
		} // END public function __construct

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
		} // END public static function deactivate

		/**
		 * Register and enqueue style sheet.
		 */
		public function register_plugin_styles() {
			wp_register_style( 'heartsandeyes_customposttypes', plugins_url( 'heartsandeyes_customposttypes/assets/hne_cpt.css' ) );
			wp_enqueue_style( 'heartsandeyes_customposttypes' );
		}

		// Add the settings link to the plugins page
		public function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=heartsandeyes_customposttypes">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		} // END public function plugin_settings_link

		/**
		 * Add custom taxonomies and custom post types counts to dashboard
		 */
		public function admin_dashboard_widget () {
			$args = array(
				'public' => true ,
				'_builtin' => false
			);

			$output = 'object';
			$operator = 'and';

			$post_types = get_post_types( $args , $output , $operator );
			echo "</ul><ul>";
			foreach( $post_types as $post_type ) {
				if ( current_user_can( 'edit_posts' ) ) {
					$num_posts = wp_count_posts( $post_type->name );
					$num = number_format_i18n ( intval( $num_posts->publish ) );
					$text = _n( $post_type->labels->singular_name, $post_type->labels->name , $num );

					printf( '<li class="page-count"><a href="edit.php?post_type=%1$s">%3$s %2$s</a></li>', $post_type->name, $text, $num );
				}
			}

			$taxonomies = get_taxonomies( $args , $output , $operator );
			foreach( $taxonomies as $taxonomy ) {
				if ( current_user_can( 'manage_categories' ) ) {
					$num_terms  = wp_count_terms( $taxonomy->name );
					$num = number_format_i18n ( intval( $num_terms ) );
					$text = _n( $taxonomy->labels->name, $taxonomy->labels->name , ( intval( $num_terms ) ) );

					printf( '<li><a href="edit-tags.php?taxonomy=%1$s">%3$s %2$s</a></li>', $taxonomy->name, $text, $num );
				}
			}
			return;
		} // END public function admin_dashboard_widget

	} // END class HeartsAndEyes_CustomPostTypes
} // END if(!class_exists('HeartsAndEyes_CustomPostTypes'))

if(class_exists('HeartsAndEyes_CustomPostTypes'))
{
	// instantiate the plugin class
	$heartsandeyes_customposttypes = new HeartsAndEyes_CustomPostTypes();

	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('HeartsAndEyes_CustomPostTypes', 'activate'));
	register_activation_hook(__FILE__, 'flush_rewrite_rules');
	
	register_deactivation_hook(__FILE__, array('HeartsAndEyes_CustomPostTypes', 'deactivate'));
	register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
}
