<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class AM_Hooks
{
	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct()
	{
		include(plugin_dir_path(ACTIVITY_MAP__FILE__) . '/hooks/class-am-hook-posts.php');
		include(plugin_dir_path(ACTIVITY_MAP__FILE__) . '/hooks/class-am-hook-users.php');
		include(plugin_dir_path(ACTIVITY_MAP__FILE__) . '/hooks/class-am-hook-comments.php');

		new Am_Hook_Posts();
		new Am_Hook_Users();
		new Am_Hook_Comments();
	}
}
