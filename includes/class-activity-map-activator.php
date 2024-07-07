<?php

/**
 * Fired during plugin activation
 *
 * @link       https://activity-map/celestialcodex
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
	 * activate
	 *
	 * @return void
	 */
	public static function activate()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}activity_map` (
			`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
			`date_time` datetime NOT NULL,
			`user_id` int(11) NOT NULL DEFAULT '0',
			`fingerprint` text NULL,
			`action` varchar(255) NOT NULL,
			`action_type` varchar(255) NULL,
			`action_title` varchar(255) NULL DEFAULT '',
			`message` text NULL,
			`action_id` mediumint(11) NOT NULL DEFAULT '0',
			`action_details` LONGTEXT NULL,
			`action_changes` LONGTEXT NULL,
			`metadata` text NULL,
			PRIMARY KEY (id)
		) $charset_collate;";


		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
