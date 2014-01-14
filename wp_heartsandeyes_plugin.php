<?php
/*
Plugin Name: WP Hearts & Eyes Plugin
Plugin URI: https://github.com/arnoesterhuizen/wp_heartsandeyes_plugin
Description: A simple wordpress plugin template
Version: 1.0
Author: Arno Esterhuizen
Author URI: http://heartsandeyes.co.za
License: GPL2
*/

if(!class_exists('WP_HeartsAndEyes_Plugin'))
{
	class WP_HeartsAndEyes_Plugin
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Initialize Settings
			require_once(sprintf("%s/settings.php", dirname(__FILE__)));
			$WP_HeartsAndEyes_Plugin_Settings = new WP_HeartsAndEyes_Plugin_Settings();

			// Register custom post types
			require_once(sprintf("%s/post-types/production.php", dirname(__FILE__)));
			$Production = new Production();
			require_once(sprintf("%s/post-types/person.php", dirname(__FILE__)));
			$Person = new Person();

			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));
		} // END public function __construct

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		} // END public static function deactivate

		// Add the settings link to the plugins page
		function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=wp_heartsandeyes_plugin">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}


	} // END class WP_HeartsAndEyes_Plugin
} // END if(!class_exists('WP_HeartsAndEyes_Plugin'))

if(class_exists('WP_HeartsAndEyes_Plugin'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('WP_HeartsAndEyes_Plugin', 'activate'));
	register_deactivation_hook(__FILE__, array('WP_HeartsAndEyes_Plugin', 'deactivate'));

	// instantiate the plugin class
	$wp_heartsandeyes_plugin = new WP_HeartsAndEyes_Plugin();

}
