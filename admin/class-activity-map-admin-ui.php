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
			'Activity Map',         // Page title
			'Activity Map',         // Menu title
			'manage_options',    // Capability required to access
			'activity_map',    // Menu slug (unique identifier)
			array($this, 'render_plugin_page'), // Callback function to render the page content
			'dashicons-admin-plugins', // Icon (optional) - Replace with appropriate dashicon class
			80                    // Menu position (optional)
		);
	}

	/**
	 * Render the content for the plugin's admin page.
	 */
	public function render_plugin_page()
	{
?>
		<div class="wrap">
			<h1>My Activity Map Events</h1>
			<!-- Add your table here -->
			<?php $this->render_plugin_table(); ?>
		</div>
	<?php
	}

	/**
	 * Render the table content for the plugin's admin page.
	 */
	public function render_plugin_table()
	{
		// Example: Fetching and displaying data in the table rows
		$users = get_users();
	?>
		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Email</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($users as $user) {
					echo '<tr>';
					echo '<td>' . $user->ID . '</td>';
					echo '<td>' . $user->display_name . '</td>';
					echo '<td>' . $user->user_email . '</td>';
					echo '</tr>';
				}
				?>
			</tbody>
		</table>
<?php
	}
}
