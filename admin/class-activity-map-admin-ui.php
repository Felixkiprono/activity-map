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
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
	}


	public function add_plugin_menu()
	{
		add_menu_page(
			'Activities',        // Page title
			'Activity Map',      // Menu title
			'manage_options',    // Capability required to access
			'activity_map',      // Menu slug (unique identifier)
			array($this, 'render_plugin_page'), // Callback function to render the page content
			'dashicons-admin-generic', // Icon (optional) - Replace with appropriate dashicon class
			6                 // Menu position (optional)
		);
	}

	/**
	 * Render the content for the plugin's admin page.
	 */
	public function render_plugin_page()
	{
?>
		<style>

		</style>
		<div class="header-bar">
			<div class="header-title">
				<span class="dashicons dashicons-chart-area"></span>
				<h1>Activity Map</h1>
			</div>
			<a href="<?php echo admin_url('admin.php?page=activities-map-settings'); ?>" class="settings-button">
				<span class="dashicons dashicons-admin-generic"></span>
				Settings
			</a>
		</div>
		<div class="wrap">
			<?php $this->render_plugin_table(); ?>
		</div>
	<?php
	}

	/**
	 * time_ago
	 *
	 * @param  mixed $datetime
	 * @return void
	 */
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
		$page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

		$activities = load_activities($page);

		// Define color mapping for activity types
		$type_colors = [
			'Comment' => '#3b82f6', // Blue
			'Post' => '#10b981',    // Green
			'Plugin' => '#FF00FF',    // 
			'Page' => '#f59e0b',    // Yellow
			'User' => '#00FFFF',
			'Default' => '#6b7280', // Gray (default color)
		];

	?>
		<div class="wrap">
			<?php if ($activities) : ?>
				<ul class="activity-list">
					<?php foreach ($activities as $activity) : ?>
						<?php
						$user_name = "";
						$user = get_user_by('id', $activity->user_id);
						$user_name = $user ? $user->user_nicename : "";
						$avatar = get_avatar($activity->user_id, 24);
						$type_color = isset($type_colors[$activity->action_type]) ? $type_colors[$activity->action_type] : $type_colors['Default'];
						?>
						<li class="activity-item">
							<div class="activity-content">
								<div class="activity-header">
									<div class="activity-title-container">
										<span class="activity-type" style="background-color: <?php echo esc_html($type_color); ?>">
											<?php echo esc_html($activity->action_type); ?>
										</span>
										<h3 class="activity-title"><?php echo esc_html(ucfirst($activity->action_title)); ?></h3>
									</div>
									<span class="activity-time"><?php echo esc_html($this->time_ago($activity->date_time)); ?></span>
								</div>
								<p class="activity-description"><?php echo esc_html($activity->message); ?></p>
								<div class="activity-meta">
									<span class="activity-user">
										<?php echo $avatar; ?>
										<?php echo esc_html(ucfirst($user_name)); ?>
									</span>
									<span class="activity-action"><?php echo esc_html(ucfirst($activity->action)); ?></span>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="pagination">
					<?php
					$base_url = add_query_arg('paged', '%#%');
					echo paginate_links(array(
						'base' => $base_url,
						'format' => '',
						'prev_text' => __('&laquo; Previous'),
						'next_text' => __('Next &raquo;'),
						'total' => 26,
						'current' => $page,
					));
					?>
				</div>
			<?php else : ?>
				<p>No activities found.</p>
			<?php endif; ?>
		</div>


<?php
	}


	public function enqueue_admin_styles()
	{
		wp_enqueue_style('activities-map-admin-style', plugin_dir_url(__FILE__) . 'css/activity-map-admin.css', array(), '1.0.0', 'all');
	}
}
