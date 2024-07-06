<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class AM_Db_Store
{
	/**
	 * get_current_ip_address
	 *
	 * @return void
	 */
	protected function get_current_ip_address()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			// Check for IP from shared internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// Check for IP passed from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			// Get IP address from remote address
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return apply_filters('get_current_ip_address', $ip);
	}

	/**
	 * get_user_id
	 *
	 * @return void
	 */
	protected function get_user_id()
	{
		$user_id = get_current_user_id();
		if ($user_id) {
			return $user_id;
		}
		return null;
	}

	/**
	 * get_current_event_time
	 *
	 * @return void
	 */
	protected function get_current_event_time()
	{
		return current_time('timestamp');
	}


	/**
	 * insert
	 *
	 * @param  mixed $args
	 * @return void
	 */
	public function insert($args)
	{
		global $wpdb;

		$args = wp_parse_args(
			$args,
			array(
				'action'         => '',
				'event_type'    => '',
				'event_subtype' => '',
				'event_name'    => '',
				'event_id'      => '',
				'user_id' => $this->get_user_id(),
				'ip_address'        => $this->get_current_ip_address(),
				'event_time'      => $this->get_current_event_time(),
			)
		);

		$query = $wpdb->insert(
			$wpdb->activity_log,
			array(
				'action'         => $args['action'],
				'event_type'    => $args['event_type'],
				'event_subtype' => $args['event_subtype'],
				'event_name'    => $args['event_name'],
				'event_id'      => $args['event_id'],
				'user_id'       =>  $args['user_id'],
				'ip_address'    => $args['ip_address'],
				'event_time'     => $args['event_time'],
			),
			array('%s', '%s', '%s', '%s', '%d', '%d', '%s', '%d')
		);

		if (false === $query) {
			//$wpdb->print_error();
		} else {
			do_action('am_add_activity', $args);
		}
	}
}

/**
 * am_add_activity
 *
 * @param  mixed $args
 * @return void
 */
function am_add_activity($args = array())
{
	AM_Main::instance()->db_store->insert($args);
}
