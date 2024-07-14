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
		add_action('admin_enqueue_scripts',  array($this, 'enqueue_chartjs'));
	}

	public function enqueue_chartjs()
	{
		wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
	}

	/**
	 * add_plugin_menu
	 *
	 * @return void
	 */
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
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
?>
		<div class="header-bar">
			<div class="header-title">
				<span class="dashicons dashicons-chart-area"></span>
				<h1>Activity Map</h1>
			</div>

		</div>

		<?php $this->render_plugin_table(); ?>

	<?php
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
			'Comment' => '#3b82f6',
			'Post' => '#10b981',
			'Plugin' => '#FF00FF',
			'Page' => '#f59e0b',
			'User' => '#00FFFF',
			'Attachment' => '#008080',
			'Default' => '#6b7280',
		];
	?>
		<?php if ($activities) : ?>
			<div class="pagination">
				<?php
				$base_url = add_query_arg('paged', '%#%');
				echo paginate_links(array(
					'base' => $base_url,
					'format' => '',
					'prev_text' => __('&laquo; Previous'),
					'next_text' => __('Next &raquo;'),
					'total' => count($activities),
					'current' => $page,
				));
				?>
			</div>
		<?php else : ?>
			<p>No activities found.</p>
		<?php endif; ?>

		<div class="activity-map">

			<div class="my-grid">
				<div class="my-column">
					<ul class="activity-list">
						<?php foreach ($activities as $activity) : ?>
							<?php
							$user_name = "";
							$user = get_user_by('id', $activity->user_id);
							$user_name = $user ? $user->user_nicename : "";
							$avatar = get_avatar($activity->user_id, 24);
							$type_color = isset($type_colors[$activity->action_type]) ? $type_colors[$activity->action_type] : $type_colors['Default'];
							$action_details = ($activity->action_type ? 'Attachment' : json_decode($activity->action_details));

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
										<span class="activity-time"><?php echo esc_html(time_ago($activity->date_time)); ?></span>
									</div>
									<p class="activity-description"><?php echo esc_html($activity->message); ?>
									</p>

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
				</div>
				<div class="my-column">
					<div class="chart-card">
						<h2>Activity Summary</h2>
						<?php $this->render_activity_chart(); ?>
						<canvas id="activityChart"></canvas>
						<p>This chart summarizes the recent activity on your site.</p>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	function render_activity_chart()
	{
		$activity_data = load_activities_stats();
	?>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				var ctx = document.getElementById('activityChart').getContext('2d');
				var chart = new Chart(ctx, {
					type: 'bar',
					data: {
						labels: <?php echo json_encode($activity_data['labels']); ?>,
						datasets: [{
							label: 'Activity Count',
							data: <?php echo json_encode($activity_data['data']); ?>,
							backgroundColor: [
								'rgba(255, 99, 132, 0.6)',
								'rgba(54, 162, 235, 0.6)',
								'rgba(255, 206, 86, 0.6)',
								'rgba(75, 192, 192, 0.6)',
								'rgba(153, 102, 255, 0.6)'
							],
							borderColor: [
								'rgba(255, 99, 132, 1)',
								'rgba(54, 162, 235, 1)',
								'rgba(255, 206, 86, 1)',
								'rgba(75, 192, 192, 1)',
								'rgba(153, 102, 255, 1)'
							],
							borderWidth: 1
						}]
					},
					options: {
						responsive: true,
						scales: {
							y: {
								beginAtZero: true,
								title: {
									display: true,
									text: 'Count'
								}
							},
							x: {
								title: {
									display: true,
									text: 'Activity Type'
								}
							}
						},
						plugins: {
							legend: {
								display: false
							},
							title: {
								display: true,
								text: 'Top 5 Activity Types'
							}
						}
					}
				});
			});
		</script>
<?php
	}

	/**
	 * enqueue_admin_styles
	 *
	 * @return void
	 */
	public function enqueue_admin_styles($hook)
	{
		if ('toplevel_page_activity_map' !== $hook) {
			return;
		}
		wp_enqueue_style('activities-map-admin-style', plugin_dir_url(__FILE__) . 'css/activity-map-admin.css', array(), '1.0.0.1', 'all');
	}
}
