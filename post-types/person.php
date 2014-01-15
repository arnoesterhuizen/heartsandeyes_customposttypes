<?php
if(!class_exists('Person'))
{
	require_once(sprintf("%s/custom_post_types.php", dirname(__FILE__)));
	/**
	 * A Person class that provides 3 additional meta fields
	 */
	class Person extends CustomPostTypes
	{
		const POST_TYPE          = "person";
		const POST_TYPE_PLURAL   = "people";
		protected $_taxonomies   = array('role' => 'roles' );
		protected $_menu_icon    = 'dashicons-admin-users';
	} // END class Person
} // END if(!class_exists('Person'))
