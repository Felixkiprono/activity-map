<?php
if (!defined('ABSPATH')) exit;
class AM_Map_Admin_Ui
{
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct()
	{
		add_action('admin_menu', array($this, 'add_plugin_menu'));
	}


	public function add_plugin_menu()
	{
		add_menu_page(
			'Activities',        // Page title
			'Activity Map',      // Menu title
			'manage_options',    // Capability required to access
			'activity_map',      // Menu slug (unique identifier)
			array($this, 'render_plugin_page'), // Callback function to render the page content
			'dashicons-admin-plugins', // Icon (optional) - Replace with appropriate dashicon class
			6                 // Menu position (optional)
		);
	}

	/**
	 * Render the content for the plugin's admin page.
	 */
	public function render_plugin_page()
	{

?>
		<div class="wrap">
			<h1>My Activities Map</h1>
			<!-- Add your table here -->
			<?php $this->render_plugin_table(); ?>
		</div>
	<?php
	}

	public function time_ago($datetime)
	{
		$now = new DateTime();
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		if ($diff->y > 0) {
			return $diff->y . " year" . ($diff->y > 1 ? "s" : "") . " ago";
		} elseif ($diff->m > 0) {
			return $diff->m . " month" . ($diff->m > 1 ? "s" : "") . " ago";
		} elseif ($diff->d > 0) {
			return $diff->d . " day" . ($diff->d > 1 ? "s" : "") . " ago";
		} elseif ($diff->h > 0) {
			return $diff->h . " hour" . ($diff->h > 1 ? "s" : "") . " ago";
		} elseif ($diff->i > 0) {
			return $diff->i . " min" . ($diff->i > 1 ? "s" : "") . " ago";
		} else {
			return "just now";
		}
	}

	/**
	 * Render the table content for the plugin's admin page.
	 */
	public function render_plugin_table()
	{
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		// Example: Fetching and displaying data in the table rows
		// $users = get_users();
		$page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
		$activities = load_activities($page);
	?>
		<div class="wrap">
			<!-- <h1>Activity Map <small>Monitor User Activities</small></h1> -->
			<table class="wp-list-table widefat striped">
				<thead>
					<tr>
						<th>Time</th>
						<th>Username</th>
						<th>Action</th>
						<th>Type</th>
						<th>Title</th>
						<th>Message</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($activities) : ?>
						<?php foreach ($activities as $activity) : ?>
							<?php
							$user_name = "";

							$user = get_user_by('id', $activity->user_id);
							$user_name = $user ? $user->user_nicename : "";
							?>
							<tr>
								<td><?php echo esc_html($this->time_ago($activity->date_time)); ?></td>
								<td><?php echo esc_html(ucfirst($user_name)); ?></td>
								<td><?php echo esc_html(ucfirst($activity->action)); ?></td>
								<td><?php echo esc_html($activity->action_type); ?></td>
								<td><?php echo esc_html(ucfirst($activity->action_title)); ?></td>
								<td><?php echo esc_html($activity->message); ?></td>
							</tr>

						<?php endforeach; ?>
						<tr>
							<td colspan="6">Total <?php echo count($activities) ?></td>
						</tr>
					<?php else : ?>
						<tr>
							<td colspan="6">No activities found.</td>
						</tr>

					<?php endif; ?>
				</tbody>
			</table>

			<!-- Pagination -->
			<div class="tablenav">
				<div class="tablenav-pages">
					<?php
					$base_url = add_query_arg('paged', '%#%');
					echo paginate_links(array(
						'base' => $base_url,
						'format' => '',
						'prev_text' => __('&laquo;'),
						'next_text' => __('&raquo;'),
						'total' => 26,
						'current' => $page,
					));
					?>
				</div>
			</div>
		</div>
		<style>
			.wrap {
				position: relative;
				padding-bottom: 50px;
				/* Adjust based on the height of the pagination */
			}

			.wrap h1 {
				display: flex;
				align-items: center;
			}

			.wrap h1 small {
				margin-left: 10px;
				font-size: 16px;
				color: #777;
			}

			.tablenav {
				margin-top: 20px;
				display: flex;
				justify-content: space-between;
				align-items: center;
			}

			.tablenav-pages {
				display: flex;
				align-items: center;
			}

			.tablenav-pages a,
			.tablenav-pages span {
				padding: 6px 12px;
				border: 1px solid #ccc;
				margin-right: 5px;
				text-decoration: none;
				color: #0073aa;
				background-color: #f9f9f9;
				border-radius: 4px;
				transition: all 0.3s ease;
			}

			.tablenav-pages a:hover {
				background-color: #0073aa;
				color: #fff;
				border-color: #0073aa;
			}

			.tablenav-pages .current {
				padding: 6px 12px;
				border: 1px solid #0073aa;
				background-color: #0073aa;
				color: #fff;
				border-radius: 4px;
			}
		</style>

<?php
	}
}
