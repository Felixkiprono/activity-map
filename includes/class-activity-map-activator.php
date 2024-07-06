<?php

/**
 * Fired during plugin activation
 *
 * @link       https://activity-map/sergei
 * @since      1.0.0
 *
 * @package    Activity_Map
 * @subpackage Activity_Map/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Activity_Map
 * @subpackage Activity_Map/includes
 * @author     Sergei Kiprono <felixkipronovich@gmail.com>
 */

class Activity_Map_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `{$wpdb->prefix}activity_map` (
			`id` mediumint(9) NOT NULL AUTO_INCREMENT,
			`action` varchar(255) NOT NULL,
			`event_type` varchar(255) NOT NULL,
			`event_subtype` varchar(255) NOT NULL DEFAULT '',
			`event_name` varchar(255) NOT NULL,
			`event_id` int(11) NOT NULL DEFAULT '0',
			`user_id` int(11) NOT NULL DEFAULT '0',
			`ip_address` varchar(55) NOT NULL DEFAULT 'localhost',
			`event_time` int(11) NOT NULL DEFAULT '0',
			`metadata` text NULL,
			PRIMARY KEY (id)
		) $charset_collate;";


		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
