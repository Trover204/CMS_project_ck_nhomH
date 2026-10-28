<?php
/**
 * Template Name: Job Dashboard Page
 *
 * Template for displaying the Job Dashboard matching the PDS Design System.
 *
 * @package JobScout
 */

get_header();

// Determine banner background image
$banner_img = get_the_post_thumbnail_url( get_the_ID(), 'full' );
if ( ! $banner_img ) {
    $banner_img = get_template_directory_uri() . '/images/career-banner.jpg';
}

$user_id = get_current_user_id();
$submit_job_form_page_id = get_option( 'job_manager_submit_job_form_page_id' );
$submit_job_url = $submit_job_form_page_id ? get_permalink( $submit_job_form_page_id ) : home_url( '/post-a-job/' );

// ---------------------------------------------------------------------------
// Calculate real stats for the logged-in user.
// Variables are intentionally kept separate so each KPI card shows
// meaningful, accurate data (Fixes P0 #1, P0 #2, P1 #3).
// ---------------------------------------------------------------------------
$total_jobs    = 0;
$active_jobs   = 0;
$filled_jobs   = 0;
$pending_jobs  = 0; // tin chờ duyệt (pending / pending_payment)
$expired_jobs  = 0; // tin hết hạn
$is_guest_view = ! is_user_logged_in(); // true when not authenticated

if ( $user_id ) {
    /**
     * Fix P0 #1: Added 'pending_payment' to post_status so it is actually
     * fetched, consistent with the counting logic below.
     *
     * Performance: no_found_rows + update_post_meta_cache = false keeps the
     * query lean since we only need post objects for counting (Fix P3 #6).
     */
    $user_jobs = get_posts( array(
        'post_type'              => 'job_listing',
        'post_status'            => array( 'publish', 'expired', 'pending', 'pending_payment', 'draft' ),
        'author'                 => $user_id,
        'posts_per_page'         => -1,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ) );

    $total_jobs = count( $user_jobs );

    foreach ( $user_jobs as $uj ) {
        if ( 'pending' === $uj->post_status || 'pending_payment' === $uj->post_status ) {
            $pending_jobs++;
        } elseif ( 'expired' === $uj->post_status ) {
            // Fix P1 #3: count expired separately so KPI cards are distinct.
            $expired_jobs++;
        } elseif ( 'publish' === $uj->post_status ) {
            if ( is_position_filled( $uj ) ) {
                $filled_jobs++;
            } else {
                $active_jobs++;
            }
        }
    }
}

/**
 * Fix P0 #2: Only apply demo/fallback stats for guests (not logged-in users
 * who genuinely have 0 jobs). A real user with 0 jobs should see all zeros.
 */
$show_demo_stats = $is_guest_view;
if ( $show_demo_stats ) {
    $total_jobs   = 6;
    $active_jobs  = 4;
    $filled_jobs  = 1;
    $pending_jobs = 1;
    $expired_jobs = 0;
}

// Fallback demo jobs for guest preview mode
$demo_dashboard_jobs = array(
    array(
        'id'          => 101,
        'title'       => 'HOTEL MANAGER',
        'type'        => 'Fulltime',
        'location'    => 'Ho Chi Minh City',
        'date'        => 'Oct 20, 2022',
        'expires'     => 'Nov 20, 2026',
        'status'      => 'active',
        'status_text' => 'Đang tuyển',
    ),
    array(
        'id'          => 102,
        'title'       => 'GENERAL MANAGER - LEADING HOTEL CHAIN',
        'type'        => 'Fulltime',
        'location'    => 'Ho Chi Minh City',
        'date'        => 'Oct 18, 2022',
        'expires'     => 'Nov 18, 2026',
        'status'      => 'active',
        'status_text' => 'Đang tuyển',
    ),
    array(
        'id'          => 103,
        'title'       => 'BANQUET MANAGER',
        'type'        => 'Fulltime',
        'location'    => 'Ho Chi Minh City',
        'date'        => 'Oct 15, 2022',
        'expires'     => 'Nov 15, 2026',
        'status'      => 'filled',
        'status_text' => 'Đã tuyển',
    ),
    array(
        'id'          => 104,
        'title'       => 'BELLMAN',
        'type'        => 'Fulltime',
        'location'    => 'Ho Chi Minh City',
        'date'        => 'Oct 12, 2022',
        'expires'     => 'Nov 12, 2026',
        'status'      => 'active',
        'status_text' => 'Đang tuyển',
    ),
    array(
        'id'          => 105,
        'title'       => 'CHIEF OPERATING OFFICER HOTEL/ RESORT CHAIN',
        'type'        => 'Fulltime',
        'location'    => 'Ho Chi Minh City',
        'date'        => 'Oct 10, 2022',
        'expires'     => 'Nov 10, 2026',
        'status'      => 'pending',
        'status_text' => 'Chờ duyệt',
    ),
    array(
        'id'          => 106,
        'title'       => 'LOSS PREVENTION OFFICER',
        'type'        => 'Fulltime',
        'location'    => 'Ho Chi Minh City',
        'date'        => 'Oct 05, 2022',
        'expires'     => 'Nov 05, 2026',
        'status'      => 'active',
        'status_text' => 'Đang tuyển',
    ),
);
?>

<!-- Hero Banner (Synchronized with Job Page Style) -->
<section class="jobs-banner-section dashboard-hero-banner" style="background-image: url('<?php echo esc_url( $banner_img ); ?>');">
    <div class="jobs-banner-overlay"></div>
    <div class="container jobs-banner-container">
        <h1 class="jobs-banner-title"><?php echo esc_html( get_theme_mod( 'dashboard_banner_title', 'JOB DASHBOARD' ) ); ?></h1>
        <p class="dashboard-banner-subtitle"><?php esc_html_e( 'Quản lý tin tuyển dụng và theo dõi hồ sơ ứng viên', 'jobscout' ); ?></p>
    </div>
</section>

<!-- Main Dashboard Container -->
<section class="jobs-main-section dashboard-main-section">
    <div class="container jobs-main-container dashboard-container">
        
        <!-- Breadcrumb Bar -->
        <div class="dashboard-breadcrumb-wrap">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumb-link"><?php esc_html_e( 'Home', 'jobscout' ); ?></a>
            <span class="breadcrumb-sep">/</span>
            <?php
            // Fix P1 #4: Use job_manager_get_permalink() so the URL resolves
            // correctly regardless of permalink structure or page slug changes.
            $jobs_page_url = job_manager_get_permalink( 'jobs' );
            if ( $jobs_page_url ) : ?>
                <a href="<?php echo esc_url( $jobs_page_url ); ?>" class="breadcrumb-link"><?php esc_html_e( 'Jobs', 'jobscout' ); ?></a>
            <?php endif; ?>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current"><?php esc_html_e( 'Job Dashboard', 'jobscout' ); ?></span>
        </div>

        <!-- 1. Stats Overview Cards (KPIs) -->
        <div class="dashboard-stats-grid">
            <!-- Total Jobs -->
            <div class="dashboard-stat-card stat-total">
                <div class="stat-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <span class="stat-number"><?php echo esc_html( $total_jobs ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'TỔNG TIN ĐĂNG', 'jobscout' ); ?></span>
                </div>
            </div>

            <!-- Active Jobs -->
            <div class="dashboard-stat-card stat-active">
                <div class="stat-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <span class="stat-number"><?php echo esc_html( $active_jobs ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'ĐANG TUYỂN DỤNG', 'jobscout' ); ?></span>
                </div>
            </div>

            <!-- Filled Jobs -->
            <div class="dashboard-stat-card stat-filled">
                <div class="stat-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <span class="stat-number"><?php echo esc_html( $filled_jobs ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'ĐÃ TUYỂN XONG', 'jobscout' ); ?></span>
                </div>
            </div>

            <!-- Pending Jobs — Fix P1 #3: separate card for each status -->
            <div class="dashboard-stat-card stat-pending">
                <div class="stat-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <span class="stat-number"><?php echo esc_html( $pending_jobs ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'CHỜ DUYỆT', 'jobscout' ); ?></span>
                </div>
            </div>

            <!-- Expired Jobs — Fix P1 #3: own card distinct from pending -->
            <div class="dashboard-stat-card stat-expired">
                <div class="stat-card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="15" y1="15" x2="12" y2="12"></line>
                    </svg>
                </div>
                <div class="stat-card-content">
                    <span class="stat-number"><?php echo esc_html( $expired_jobs ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'HẾT HẠN', 'jobscout' ); ?></span>
                </div>
            </div>
        </div>

        <!-- 2. Main Shortcode / Dashboard Container -->
        <div class="dashboard-content-box">
            <?php
            // Output the WP Job Manager shortcode
            echo do_shortcode( '[job_dashboard]' );
            ?>

            <!-- Interactive Demo Dashboard (Fix P2 #5: PHP gate — not rendered at all for logged-in users) -->
            <?php if ( $is_guest_view ) : ?>
            <div id="pdsDemoDashboardWrap" class="pds-demo-dashboard-wrap">
                <div class="demo-dashboard-header">
                    <div class="demo-badge-notice">
                        <span class="demo-dot"></span>
                        <span><?php esc_html_e( 'BẢNG QUẢN LÝ VIỆC LÀM (CHẾ ĐỘ XEM TRƯỚC THEO DESIGN SYSTEM PDS)', 'jobscout' ); ?></span>
                    </div>
                </div>

                <!-- Top Controls: Search, Filter, Add Job -->
                <div class="dashboard-controls-bar">
                    <div class="dashboard-search-wrap">
                        <div class="dashboard-input-group">
                            <span class="dashboard-input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </span>
                            <input type="text" id="pdsDemoSearchInput" class="dashboard-search-input"
                                placeholder="<?php esc_attr_e( 'Tìm kiếm theo chức danh, địa điểm...', 'jobscout' ); ?>"
                                onkeyup="pdsFilterDemoJobs()" />
                        </div>
                    </div>

                    <div class="dashboard-filter-wrap">
                        <select id="pdsDemoStatusFilter" class="jobs-sort-select dashboard-status-filter" onchange="pdsFilterDemoJobs()">
                            <option value="all"><?php esc_html_e( 'Tất cả trạng thái', 'jobscout' ); ?></option>
                            <option value="active"><?php esc_html_e( 'Đang tuyển (Active)', 'jobscout' ); ?></option>
                            <option value="filled"><?php esc_html_e( 'Đã tuyển (Filled)', 'jobscout' ); ?></option>
                            <option value="pending"><?php esc_html_e( 'Chờ duyệt (Pending)', 'jobscout' ); ?></option>
                        </select>
                    </div>

                    <div class="dashboard-actions-wrap">
                        <a class="pds-btn pds-btn-primary btn-add-job" href="<?php echo esc_url( $submit_job_url ); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span><?php esc_html_e( 'ĐĂNG TIN MỚI', 'jobscout' ); ?></span>
                        </a>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="dashboard-card-table-wrap">
                    <div class="dashboard-table-responsive">
                        <table class="pds-dashboard-table" id="pdsDemoJobsTable">
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
                                <?php foreach ( $demo_dashboard_jobs as $job ) : ?>
                                    <tr class="dashboard-job-row <?php echo 'filled' === $job['status'] ? 'job-filled' : ''; ?>" data-status="<?php echo esc_attr( $job['status'] ); ?>">
                                        <td class="col-title">
                                            <div class="dashboard-job-info">
                                                <a href="<?php echo esc_url( home_url( '/index.php/jobs-2/' ) ); ?>" class="dashboard-job-name">
                                                    <?php echo esc_html( $job['title'] ); ?>
                                                </a>
                                                <div class="dashboard-job-submeta">
                                                    <span class="submeta-type"><?php echo esc_html( $job['type'] ); ?></span>
                                                    <span class="submeta-sep">•</span>
                                                    <span class="submeta-loc"><?php echo esc_html( $job['location'] ); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="col-date">
                                            <span class="dashboard-text-date"><?php echo esc_html( $job['date'] ); ?></span>
                                        </td>
                                        <td class="col-expires">
                                            <span class="dashboard-text-date"><?php echo esc_html( $job['expires'] ); ?></span>
                                        </td>
                                        <td class="col-status">
                                            <?php if ( 'active' === $job['status'] ) : ?>
                                                <span class="pds-status-badge status-active">
                                                    <span class="status-dot"></span>
                                                    <?php echo esc_html( $job['status_text'] ); ?>
                                                </span>
                                            <?php elseif ( 'filled' === $job['status'] ) : ?>
                                                <span class="pds-status-badge status-filled">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                    <?php echo esc_html( $job['status_text'] ); ?>
                                                </span>
                                            <?php elseif ( 'pending' === $job['status'] ) : ?>
                                                <span class="pds-status-badge status-pending">
                                                    <?php echo esc_html( $job['status_text'] ); ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="col-actions">
                                            <div class="dashboard-row-actions">
                                                <button type="button" class="dashboard-action-btn btn-action-edit" onclick="alert('Chỉnh sửa tin: <?php echo esc_js( $job['title'] ); ?>')" title="<?php esc_attr_e( 'Chỉnh sửa', 'jobscout' ); ?>">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                                    <span><?php esc_html_e( 'Sửa', 'jobscout' ); ?></span>
                                                </button>
                                                <?php if ( 'active' === $job['status'] ) : ?>
                                                    <button type="button" class="dashboard-action-btn btn-action-filled" onclick="alert('Đã chuyển trạng thái sang Đã tuyển!')" title="<?php esc_attr_e( 'Đánh dấu đã tuyển', 'jobscout' ); ?>">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                                        <span><?php esc_html_e( 'Đã tuyển', 'jobscout' ); ?></span>
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="dashboard-action-btn action-danger" onclick="if(confirm('Bạn có chắc muốn xóa tin tuyển dụng này?')) { this.closest('tr').remove(); }" title="<?php esc_attr_e( 'Xóa tin', 'jobscout' ); ?>">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                    <span><?php esc_html_e( 'Xóa', 'jobscout' ); ?></span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="pdsDemoNoResultsRow" class="no-results-row" style="display:none;">
                                    <td colspan="5" style="text-align:center; padding:40px 20px; color:#64748b;">
                                        <p style="font-size:14px; margin-bottom:8px;"><?php esc_html_e( 'Không tìm thấy tin tuyển dụng nào phù hợp với từ khóa hoặc bộ lọc.', 'jobscout' ); ?></p>
                                        <button type="button" onclick="pdsResetDemoSearch()" class="pds-btn pds-btn-outline" style="height:34px; padding:0 16px; font-size:11.5px;">
                                            &larr; <?php esc_html_e( 'XÓA BỘ LỌC TÌM KIẾM', 'jobscout' ); ?>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- /.dashboard-card-table-wrap (demo) -->
            </div><!-- /#pdsDemoDashboardWrap -->
            <?php endif; // $is_guest_view ?>

        </div><!-- /.dashboard-content-box -->

    </div><!-- /.dashboard-container -->
</section>

<?php if ( $is_guest_view ) : ?>
<!-- Script for Interactive Demo Search/Filter (only loaded for guests) -->
<script>
function jobscoutToggleDemoDashboard() {
    var wrap = document.getElementById('pdsDemoDashboardWrap');
    if (wrap) {
        if (wrap.style.display === 'none') {
            wrap.style.display = 'block';
            wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            wrap.style.display = 'none';
        }
    }
}

function pdsFilterDemoJobs() {
    var query = (document.getElementById('pdsDemoSearchInput').value || '').trim().toLowerCase();
    var status = (document.getElementById('pdsDemoStatusFilter').value || 'all');
    var rows = document.querySelectorAll('#pdsDemoJobsTable tbody tr:not(.no-results-row)');
    var visibleCount = 0;

    rows.forEach(function(row) {
        var text = row.innerText.toLowerCase();
        var rowStatus = row.getAttribute('data-status');

        var matchQuery = query === '' || text.indexOf(query) !== -1;
        var matchStatus = status === 'all' || rowStatus === status;

        if (matchQuery && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    var noRes = document.getElementById('pdsDemoNoResultsRow');
    if (noRes) {
        noRes.style.display = (visibleCount === 0) ? '' : 'none';
    }
}

function pdsResetDemoSearch() {
    var input = document.getElementById('pdsDemoSearchInput');
    var filter = document.getElementById('pdsDemoStatusFilter');
    if (input) input.value = '';
    if (filter) filter.value = 'all';
    pdsFilterDemoJobs();
}
</script>
<?php endif; // $is_guest_view ?>

<?php // Orange Newsletter Section (CF7 integration) ?>
<?php get_template_part( 'template-parts/newsletter-cf7' ); ?>

<?php
get_footer();
