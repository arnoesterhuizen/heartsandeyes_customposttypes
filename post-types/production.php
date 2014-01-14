<?php
if(!class_exists('Production'))
{
	require_once('custom_post_type.php');
	/**
	 * A Person class that provides 3 additional meta fields
	 */
	class Production extends CustomPostType
	{
		const POST_TYPE          = "production";
		const POST_TYPE_PLURAL   = "productions";
		protected $_menu_icon    = 'dashicons-exerpt-view';

		/**
		 * The Constructor
		 */
		public function __construct()
		{
			parent::__construct();
		} // END public function __construct()
	} // END class Person
} // END if(!class_exists('Person'))
