<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://activity-map/sergei
 * @since      1.0.0
 *
 * @package    Activity_Map
 * @subpackage Activity_Map/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Activity_Map
 * @subpackage Activity_Map/includes
 * @author     Sergei Kiprono <felixkipronovich@gmail.com>
 */
class Activity_Map_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{

		global $wpdb;
		$admin_role = get_role('administrator');
		if ($admin_role) {
			$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}activity_map`;");
		}
		delete_option('activity_map_db_version');
	}
}
