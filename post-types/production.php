<?php
if(!class_exists('Production'))
{
	require_once(sprintf("%s/custom_post_types.php", dirname(__FILE__)));
	/**
	 * A Person class that provides 3 additional meta fields
	 */
	class Production extends CustomPostTypes
	{
		const POST_TYPE          = "production";
		const POST_TYPE_PLURAL   = "productions";
		protected $_menu_icon    = 'dashicons-exerpt-view';
	} // END class Person
} // END if(!class_exists('Person'))
