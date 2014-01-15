<?php
/*
Plugin Name: Hearts and Eyes Custom Post Types
Plugin URI: https://github.com/arnoesterhuizen/wp_heartsandeyes_plugin
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
			require_once(sprintf("%s/post-types/production.php", dirname(__FILE__)));
			$Production = new Production();
			require_once(sprintf("%s/post-types/person.php", dirname(__FILE__)));
			$Person = new Person();
			require_once(sprintf("%s/shortcodes.php", dirname(__FILE__)));
			$HeartsAndEyes_CustomShortcodes = new HeartsAndEyes_CustomShortcodes();

			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));
		} // END public function __construct

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// ATTENTION: This is *only* done during plugin activation hook in this example!
			// You should *NEVER EVER* do this on every page load!!
			flush_rewrite_rules();
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// ATTENTION: This is *only* done during plugin activation hook in this example!
			// You should *NEVER EVER* do this on every page load!!
			flush_rewrite_rules();
		} // END public static function deactivate

		// Add the settings link to the plugins page
		function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=heartsandeyes_customposttypes">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}

	} // END class HeartsAndEyes_CustomPostTypes
} // END if(!class_exists('HeartsAndEyes_CustomPostTypes'))

if(class_exists('HeartsAndEyes_CustomPostTypes'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('HeartsAndEyes_CustomPostTypes', 'activate'));
	register_deactivation_hook(__FILE__, array('HeartsAndEyes_CustomPostTypes', 'deactivate'));

	// instantiate the plugin class
	$heartsandeyes_customposttypes = new HeartsAndEyes_CustomPostTypes();

}
