<?php
/**
 * Job dashboard shortcode content if user is not logged in.
 *
 * Matching PDS Design System
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$login_url    = apply_filters( 'job_manager_job_dashboard_login_url', wp_login_url( get_permalink() ) );
$register_url = wp_registration_url();
?>
<div class="job-dashboard-auth-card">
	<div class="auth-card-icon">
		<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
			<path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
		</svg>
	</div>
	<h3 class="auth-card-title"><?php esc_html_e( 'YÊU CẦU ĐĂNG NHẬP', 'jobscout' ); ?></h3>
	<p class="auth-card-desc">
		<?php esc_html_e( 'Bạn cần đăng nhập vào tài khoản Nhà tuyển dụng để truy cập Bảng điều khiển, xem thống kê và quản lý các tin đăng tuyển dụng của mình.', 'jobscout' ); ?>
	</p>
	<div class="auth-card-actions">
		<a href="<?php echo esc_url( $login_url ); ?>" class="pds-btn pds-btn-primary">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
				<polyline points="10 17 15 12 10 7"></polyline>
				<line x1="15" y1="12" x2="3" y2="12"></line>
			</svg>
			<?php esc_html_e( 'ĐĂNG NHẬP (SIGN IN)', 'jobscout' ); ?>
		</a>
		<?php if ( get_option( 'users_can_register' ) ) : ?>
			<a href="<?php echo esc_url( $register_url ); ?>" class="pds-btn pds-btn-outline">
				<?php esc_html_e( 'ĐĂNG KÝ TÀI KHOẢN', 'jobscout' ); ?>
			</a>
		<?php endif; ?>
	</div>
	<div class="auth-card-demo-prompt">
		<button type="button" class="btn-toggle-demo-dashboard" onclick="jobscoutToggleDemoDashboard()">
			<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<circle cx="12" cy="12" r="10"></circle>
				<line x1="12" y1="16" x2="12" y2="12"></line>
				<line x1="12" y1="8" x2="12.01" y2="8"></line>
			</svg>
			<span><?php esc_html_e( 'Xem giao diện bảng quản lý việc làm (Chế độ xem trước)', 'jobscout' ); ?></span>
		</button>
	</div>
</div>
