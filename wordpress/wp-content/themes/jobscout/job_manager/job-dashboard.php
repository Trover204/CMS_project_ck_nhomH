<?php
/**
 * Job dashboard shortcode content.
 *
 * This template overrides wp-job-manager/templates/job-dashboard.php
 * Matching PDS Design System.
 *
 * Changes from review:
 * - Item 6:  Strict validation of $job_actions entries; use sanitize_html_class().
 * - Item 7:  Distinguish Case A (no jobs ever) vs Case B (search returned 0 results).
 * - Item 8:  $date_format declared once outside loop; DateTimeInterface check via helper.
 * - Item 9:  Removed unused `use` imports (Job_Overlay, Notice, UI_Elements).
 * - Item 10: No inline CSS — presentation moved to CSS classes.
 * - Item 11: Pagination via plugin template (preserves ?search= automatically).
 * - Item 12: Loop consumes jobscout_get_job_dashboard_item_data() for data/UI separation.
 * - Item 13: Status badge rendered from jobscout_get_job_dashboard_status_data() results.
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Prepare shared values used across the template.
// ---------------------------------------------------------------------------
$submit_job_form_page_id = get_option( 'job_manager_submit_job_form_page_id' );
$submit_job_url          = $submit_job_form_page_id
	? get_permalink( $submit_job_form_page_id )
	: home_url( '/post-a-job/' );

/**
 * Date format — declared once, passed into item-data helper so both
 * "date posted" and "expiration date" use the same setting (Item 8).
 */
$date_format = get_option( 'date_format' ) ?: 'M d, Y';

/**
 * Normalise the search keyword coming from WP Job Manager's shortcode
 * controller. The controller already sanitises it but we guard against
 * $search_input not being defined when the template is loaded in other ways.
 */
$search_input = isset( $search_input ) && is_string( $search_input )
	? sanitize_text_field( $search_input )
	: '';
?>

<div id="job-manager-job-dashboard" class="pds-dashboard-wrapper jm-dashboard">

	<!-- ================================================================
	     Top Controls: Search + Add-Job button
	     ================================================================ -->
	<div class="dashboard-controls-bar">

		<div class="dashboard-search-wrap">
			<form method="GET" action="<?php echo esc_url( get_permalink() ); ?>" class="dashboard-search-form">
				<?php
				// Keep page_id for sites using plain permalinks.
				if ( ! get_option( 'permalink_structure' ) && get_the_ID() ) {
					echo '<input type="hidden" name="page_id" value="' . esc_attr( get_the_ID() ) . '" />';
				}
				?>
				<div class="dashboard-input-group">
					<span class="dashboard-input-icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
							fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"></circle>
							<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
						</svg>
					</span>
					<input
						type="search"
						name="search"
						class="dashboard-search-input"
						placeholder="<?php esc_attr_e( 'Tìm kiếm tin tuyển dụng...', 'jobscout' ); ?>"
						value="<?php echo esc_attr( $search_input ); ?>"
						aria-label="<?php esc_attr_e( 'Search Jobs', 'jobscout' ); ?>"
					/>
					<?php if ( ! empty( $search_input ) ) : ?>
						<a
							href="<?php echo esc_url( remove_query_arg( 'search' ) ); ?>"
							class="dashboard-search-clear-btn"
							title="<?php esc_attr_e( 'Xóa tìm kiếm', 'jobscout' ); ?>"
						>
							&times;
						</a>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<div class="dashboard-actions-wrap">
			<?php if ( job_manager_user_can_submit_job_listing() ) : ?>
				<a class="pds-btn pds-btn-primary btn-add-job" href="<?php echo esc_url( $submit_job_url ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
						fill="none" stroke="currentColor" stroke-width="2.5"
						stroke-linecap="round" stroke-linejoin="round">
						<line x1="12" y1="5" x2="12" y2="19"></line>
						<line x1="5" y1="12" x2="19" y2="12"></line>
					</svg>
					<span><?php esc_html_e( 'ĐĂNG TIN MỚI', 'jobscout' ); ?></span>
				</a>
			<?php endif; ?>
		</div>

	</div><!-- /.dashboard-controls-bar -->

	<!-- ================================================================
	     Job Management Table Card
	     ================================================================ -->
	<div class="dashboard-card-table-wrap">

		<?php if ( empty( $jobs ) ) : ?>
			<!-- ---------------------------------------------------------
			     Empty State — Item 7: two distinct cases
			     Case A: user has NO jobs at all (no search active)
			     Case B: user searched but keyword matched nothing
			     --------------------------------------------------------- -->
			<div class="dashboard-empty-state">
				<div class="empty-state-icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
						fill="none" stroke="currentColor" stroke-width="1.5"
						stroke-linecap="round" stroke-linejoin="round">
						<rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
						<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
					</svg>
				</div>

				<?php if ( ! empty( $search_input ) ) : ?>
					<!-- Case B: search returned no results -->
					<h4 class="empty-state-title">
						<?php
						printf(
							/* translators: %s: search keyword */
							esc_html__( 'Không tìm thấy tin tuyển dụng nào phù hợp với từ khóa "%s".', 'jobscout' ),
							esc_html( $search_input )
						);
						?>
					</h4>
					<p class="empty-state-desc">
						<?php esc_html_e( 'Hãy thử từ khóa khác hoặc xóa bộ lọc để xem lại toàn bộ danh sách.', 'jobscout' ); ?>
					</p>
					<div class="empty-state-actions">
						<a
							href="<?php echo esc_url( remove_query_arg( 'search' ) ); ?>"
							class="pds-btn pds-btn-outline empty-state-clear-btn"
						>
							&larr; <?php esc_html_e( 'XEM TẤT CẢ TIN ĐĂNG', 'jobscout' ); ?>
						</a>
					</div>

				<?php else : ?>
					<!-- Case A: user has not posted any jobs yet -->
					<h4 class="empty-state-title">
						<?php esc_html_e( 'Bạn chưa có tin tuyển dụng nào được đăng.', 'jobscout' ); ?>
					</h4>
					<p class="empty-state-desc">
						<?php esc_html_e( 'Hãy bắt đầu tạo tin tuyển dụng đầu tiên của bạn để tiếp cận hàng ngàn ứng viên tiềm năng.', 'jobscout' ); ?>
					</p>
					<?php if ( job_manager_user_can_submit_job_listing() ) : ?>
						<div class="empty-state-actions">
							<a
								href="<?php echo esc_url( $submit_job_url ); ?>"
								class="pds-btn pds-btn-primary empty-state-submit-btn"
							>
								+ <?php esc_html_e( 'ĐĂNG TIN MỚI', 'jobscout' ); ?>
							</a>
						</div>
					<?php endif; ?>

				<?php endif; ?>
			</div><!-- /.dashboard-empty-state -->

		<?php else : ?>
			<!-- ---------------------------------------------------------
			     Jobs table — Items 8, 12, 13
			     --------------------------------------------------------- -->
			<div class="dashboard-table-responsive">
				<table class="pds-dashboard-table">
					<thead>
						<tr>
							<th class="col-title"><?php esc_html_e( 'VIỆC LÀM', 'jobscout' ); ?></th>
							<th class="col-date"><?php esc_html_e( 'NGÀY ĐĂNG', 'jobscout' ); ?></th>
							<th class="col-expires"><?php esc_html_e( 'HẾT HẠN', 'jobscout' ); ?></th>
							<th class="col-status"><?php esc_html_e( 'TRẠNG THÁI', 'jobscout' ); ?></th>
							<th class="col-actions"><?php esc_html_e( 'THAO TÁC', 'jobscout' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $jobs as $job ) :

							/**
							 * Item 12: Delegate data preparation to the helper function.
							 * Template only consumes the returned array — no business logic here.
							 *
							 * Item 8: $date_format is passed from outside the loop (single declaration).
							 */
							$job_data = jobscout_get_job_dashboard_item_data( $job, $date_format );

							if ( empty( $job_data ) ) {
								continue; // Skip if helper returned empty (e.g. post not found).
							}

							$job_id      = $job_data['id'];
							$status_data = $job_data['status_data']; // Item 13: pre-computed via helper.
							$row_class   = ( 'status-filled' === $status_data['class'] ) ? 'job-filled' : '';
						?>
						<tr class="dashboard-job-row <?php echo esc_attr( $row_class ); ?>">

							<!-- Job Title & Meta -->
							<td class="col-title">
								<div class="dashboard-job-info">
									<a href="<?php echo esc_url( $job_data['permalink'] ); ?>" class="dashboard-job-name">
										<?php echo esc_html( $job_data['title'] ); ?>
									</a>
									<div class="dashboard-job-submeta">
										<span class="submeta-type"><?php echo esc_html( $job_data['type_label'] ); ?></span>
										<span class="submeta-sep">&bull;</span>
										<span class="submeta-loc"><?php echo esc_html( $job_data['location'] ); ?></span>
									</div>
								</div>
							</td>

							<!-- Date Posted — Item 8: format/value come from helper -->
							<td class="col-date">
								<span class="dashboard-text-date"><?php echo esc_html( $job_data['date_posted'] ); ?></span>
							</td>

							<!-- Expiration Date -->
							<td class="col-expires">
								<span class="dashboard-text-date"><?php echo esc_html( $job_data['expires_text'] ); ?></span>
							</td>

							<!-- Status Badge — Item 13: rendered from helper data, no if/elseif chains -->
							<td class="col-status">
								<span class="pds-status-badge <?php echo esc_attr( $status_data['class'] ); ?>">
									<?php if ( 'status-active' === $status_data['class'] ) : ?>
										<span class="status-dot"></span>
									<?php elseif ( 'status-filled' === $status_data['class'] ) : ?>
										<svg width="12" height="12" viewBox="0 0 24 24" fill="none"
											stroke="currentColor" stroke-width="2.5">
											<polyline points="20 6 9 17 4 12"></polyline>
										</svg>
									<?php endif; ?>
									<?php echo esc_html( $status_data['label'] ); ?>
								</span>
							</td>

							<!-- Actions — Item 6: strict validation + sanitize_html_class() -->
							<td class="col-actions">
								<div class="dashboard-row-actions">
									<?php
									/**
									 * Item 6: Safe access with null-coalesce; validate every key
									 * before using it to prevent PHP notices on malformed entries.
									 */
									$actions = isset( $job_actions[ $job_id ] ) && is_array( $job_actions[ $job_id ] )
										? $job_actions[ $job_id ]
										: array();

									foreach ( $actions as $action ) {
										// Guard: action must be an array with required string keys.
										if (
											! is_array( $action ) ||
											! isset( $action['name'], $action['url'], $action['label'] ) ||
											! is_string( $action['name'] ) ||
											! is_string( $action['url'] ) ||
											! is_string( $action['label'] )
										) {
											continue;
										}

										$action_name = $action['name'];
										$action_url  = $action['url'];
										$action_lbl  = $action['label'];

										/**
										 * Item 6: sanitize_html_class() normalises the class value;
										 * esc_attr() handles escaping when it is written into HTML.
										 */
										$btn_class = 'btn-action-' . sanitize_html_class( $action_name );
										$is_danger = 'delete' === $action_name;
										?>
										<a
											href="<?php echo esc_url( $action_url ); ?>"
											class="dashboard-action-btn <?php echo esc_attr( $btn_class ); ?><?php echo $is_danger ? ' action-danger' : ''; ?>"
											title="<?php echo esc_attr( $action_lbl ); ?>"
										>
											<?php if ( 'edit' === $action_name ) : ?>
												<svg width="14" height="14" viewBox="0 0 24 24" fill="none"
													stroke="currentColor" stroke-width="2">
													<path d="M12 20h9"></path>
													<path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
												</svg>
											<?php elseif ( 'delete' === $action_name ) : ?>
												<svg width="14" height="14" viewBox="0 0 24 24" fill="none"
													stroke="currentColor" stroke-width="2">
													<polyline points="3 6 5 6 21 6"></polyline>
													<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
												</svg>
											<?php elseif ( 'mark_filled' === $action_name || 'mark_not_filled' === $action_name ) : ?>
												<svg width="14" height="14" viewBox="0 0 24 24" fill="none"
													stroke="currentColor" stroke-width="2">
													<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
													<polyline points="22 4 12 14.01 9 11.01"></polyline>
												</svg>
											<?php elseif ( 'duplicate' === $action_name ) : ?>
												<svg width="14" height="14" viewBox="0 0 24 24" fill="none"
													stroke="currentColor" stroke-width="2">
													<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
													<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
												</svg>
											<?php endif; ?>
											<span><?php echo esc_html( $action_lbl ); ?></span>
										</a>
										<?php
									}
									?>
								</div>
							</td>

						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div><!-- /.dashboard-table-responsive -->

		<?php endif; ?>
	</div><!-- /.dashboard-card-table-wrap -->

	<!-- ================================================================
	     Pagination — Item 11: plugin template preserves ?search= automatically
	     ================================================================ -->
	<?php if ( isset( $max_num_pages ) && $max_num_pages > 1 ) : ?>
		<div class="dashboard-pagination-wrap">
			<?php
			get_job_manager_template(
				'pagination.php',
				array( 'max_num_pages' => $max_num_pages )
			);
			?>
		</div>
	<?php endif; ?>

</div><!-- /#job-manager-job-dashboard -->
