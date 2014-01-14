<?php
if(!class_exists('Person'))
{
	require_once('custom_post_type.php');
	/**
	 * A Person class that provides 3 additional meta fields
	 */
	class Person extends CustomPostType
	{
		const POST_TYPE          = "person";
		const POST_TYPE_PLURAL   = "people";
		protected $_taxonomies   = array('role' => 'roles' );
		protected $_menu_icon    = 'dashicons-admin-users';

		/**
		 * The Constructor
		 */
		public function __construct()
		{
			parent::__construct();
		} // END public function __construct()
	} // END class Person
} // END if(!class_exists('Person'))
