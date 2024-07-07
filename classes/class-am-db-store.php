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
		return 0;
	}

	/**
	 * get_current_browser_and_user_agent
	 *
	 * @return void
	 */
	function get_current_browser_and_user_agent()
	{
		$browser = '';
		$user_agent = '';

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];

			// Detect browser from user agent
			if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident') !== false) {
				$browser = 'Internet Explorer';
			} elseif (strpos($user_agent, 'Firefox') !== false) {
				$browser = 'Mozilla Firefox';
			} elseif (strpos($user_agent, 'Chrome') !== false) {
				$browser = 'Google Chrome';
			} elseif (strpos($user_agent, 'Safari') !== false) {
				$browser = 'Apple Safari';
			} elseif (strpos($user_agent, 'Opera') !== false || strpos($user_agent, 'OPR') !== false) {
				$browser = 'Opera';
			} elseif (strpos($user_agent, 'Edge') !== false) {
				$browser = 'Microsoft Edge';
			} elseif (strpos($user_agent, 'Trident') !== false) {
				$browser = 'Internet Explorer';
			} else {
				$browser = 'Unknown';
			}
		}

		return array(
			'browser' => $browser,
			'user_agent' => $user_agent
		);
	}

	/**
	 * get_current_event_time
	 *
	 * @return void
	 */
	protected function get_current_date_time()
	{
		$currentDateTime = date('Y-m-d H:i:s');
		return $currentDateTime;
	}

	/**
	 * get_user_fingerprint
	 *
	 * @return void
	 */
	public function get_user_fingerprint(): string
	{
		$user_info = get_userdata($this->get_user_id());
		$username = '';
		if ($user_info) {
			$username = $user_info->user_login;
		}
		// $current_info = get_current_browser_and_user_agent();
		$current_browser = isset($current_info['browser']) ? $current_info['browser'] : "";
		$current_user_agent = isset($current_info['user_agent']) ? $current_info['user_agent'] : "";

		$finger_print = [
			'user_id' => $this->get_user_id(),
			'user_name' => $username,
			'ip_address' => $this->get_current_ip_address(),
			'user_agent' => $current_user_agent,
			'browser' => $current_browser,
		];

		return json_encode($finger_print);
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
				'action_type'    => '',
				'action_title'   => '',
				'message'        => '',
				'action_id'      => '',
				'user_id' 		 => $this->get_user_id(),
				'fingerprint'    => $this->get_user_fingerprint(),
				'action_details' => '',
				'action_changes' => '',
				'date_time'      => $this->get_current_date_time(),
				'metadata'		 => ''
			)
		);

		$query = $wpdb->insert(
			$wpdb->activity_map,
			array(
				'user_id'         => $args['user_id'],
				'fingerprint'     => $args['fingerprint'],
				'action'          => $args['action'],
				'action_type'     => $args['action_type'],
				'action_title'    => $args['action_title'],
				'action_id'       => $args['action_id'],
				'action_details'  => $args['action_details'],
				'action_changes'  => $args['action_changes'],
				'date_time'       => $args['date_time'],
				'message'         => $args['message'],
				'metadata'        => $args['metadata'],
			),
			array('%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
		);

		if (false === $query) {
			//$wpdb->print_error();
		} else {
			do_action('am_add_activity', $args);
		}
	}

	/**
	 * fetch_all_logs
	 *
	 * @param  mixed $page
	 * @return void
	 */
	public function fetch_all_logs($page)
	{
		// Pagination settings
		$limit = 10; // Number of records per page
		$start_from = ($page - 1) * $limit;

		// Fetch records from the database
		global $wpdb;
		// $table_name = $wpdb->prefix . 'your_table_name';
		$total_records = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->activity_map");
		$total_pages = ceil($total_records / $limit);

		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->activity_map LIMIT %d, %d", $start_from, $limit));
		return $results;
	}
}

/**
 * log_activity
 *
 * @param  mixed $args
 * @return void
 */
function log_activity($args = array())
{
	AM_Main::instance()->db_store->insert($args);
}


/**
 * load_activities
 *
 * @return void
 */
function load_activities($page)
{
	$activities =  AM_Main::instance()->db_store->fetch_all_logs($page);
	return $activities;
}
