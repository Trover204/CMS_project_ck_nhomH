<?php
/**
 * Job listing in the loop.
 *
 * Override template: content-job_listing.php
 */

if (!defined('ABSPATH')) {
    exit; // Chống truy cập trực tiếp
}

global $post;

// 1. Chốt cứng lấy đúng ID của công việc hiện tại trong vòng lặp, tuyệt đối KHÔNG dùng get_the_ID() ở đây
$job_id = $post->ID;

// 2. Lấy đường dẫn chuẩn của Job
$job_permalink = get_permalink($job_id);

// Ưu tiên dùng hàm riêng của WP Job Manager nếu có
if (function_exists('get_the_job_permalink')) {
    $job_permalink = get_the_job_permalink();
}

// 2. Lấy Logo công ty từ DB (Nếu trống thì lấy Ảnh đại diện / Featured Image của bài viết)
$company_logo = '';
if (function_exists('get_the_company_logo')) {
    $company_logo = get_the_company_logo($job_id);
}
if (empty($company_logo)) {
    $company_logo = get_post_meta($job_id, '_company_logo', true);
}
if (empty($company_logo) && has_post_thumbnail($job_id)) {
    $company_logo = get_the_post_thumbnail_url($job_id, 'thumbnail');
}
// Nếu vẫn trống thì dùng ảnh mặc định của theme
if (empty($company_logo)) {
    $company_logo = get_template_directory_uri() . '/images/job-logos/logo-sodoh.png';
}

// 3. Lấy Loại công việc (Job Type) đồng bộ theo Taxonomy của WP
$types = get_the_terms($job_id, 'job_listing_type');
$type_label = (!empty($types) && !is_wp_error($types)) ? implode(', ', wp_list_pluck($types, 'name')) : 'Chưa phân loại';

// 4. Lấy Danh mục công việc (Job Category) đồng bộ theo Taxonomy của WP
$cats = get_the_terms($job_id, 'job_listing_category');
$cat_label = (!empty($cats) && !is_wp_error($cats)) ? implode(', ', wp_list_pluck($cats, 'name')) : 'Ngành nghề';

// 5. Lấy Địa điểm (Location) thực tế từ Meta Data
$loc = get_post_meta($job_id, '_job_location', true);
if (empty($loc)) {
    $loc = get_post_meta($job_id, 'location', true);
}
if (empty($loc)) {
    $loc = 'Toàn quốc'; // Fallback tiếng Việt linh hoạt
}

// 6. Xử lý Nội dung/Mô tả (Đồng bộ linh hoạt từ bài viết thực tế)
$content = get_the_content();
$bullets = array();

// Cách A: Quét các thẻ <li> nếu có
if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $content, $matches)) {
    foreach ($matches[1] as $m) {
        $clean_bullet = trim(wp_strip_all_tags($m));
        if (!empty($clean_bullet)) {
            $bullets[] = $clean_bullet;
        }
        if (count($bullets) >= 3)
            break;
    }
}

// Cách B: Nếu không có thẻ <li>, tự động lấy Tóm tắt (Excerpt) hoặc câu đầu bài viết
if (empty($bullets)) {
    $excerpt = get_the_excerpt($job_id);
    if (!empty($excerpt)) {
        $bullets[] = wp_strip_all_tags($excerpt);
    } else {
        $clean_content = wp_strip_all_tags($content);
        if (!empty($clean_content)) {
            $bullets[] = mb_substr($clean_content, 0, 120) . '...';
        } else {
            $bullets[] = 'Xem chi tiết thông tin tuyển dụng và yêu cầu công việc.';
        }
    }
}

$job_title = get_the_title();
?>
<article <?php job_listing_class('job-card pds-job-card'); ?>
    data-longitude="<?php echo esc_attr(isset($post->geolocation_lat) ? $post->geolocation_lat : ''); ?>"
    data-latitude="<?php echo esc_attr(isset($post->geolocation_long) ? $post->geolocation_long : ''); ?>"
    onclick="window.location.href='<?php echo esc_url($job_permalink); ?>';"
    style="cursor: pointer; position: relative;">

    <!-- Link bao phủ toàn bộ thẻ card -->
    <a href="<?php echo esc_url($job_permalink); ?>" class="job-card-stretched-link"
        aria-label="<?php echo esc_attr($job_title); ?>"></a>

    <div class="job-card-inner">
        <div class="job-card-header">
            <div class="job-card-logo">
                <a href="<?php echo esc_url($job_permalink); ?>">
                    <img src="<?php echo esc_url($company_logo); ?>" alt="<?php echo esc_attr($job_title); ?>" loading="lazy" />
                </a>
            </div>
            <div class="job-card-heading">
                <h3 class="job-card-title entry-title">
                    <a href="<?php echo esc_url($job_permalink); ?>">
                        <?php echo esc_html($job_title); ?>
                    </a>
                </h3>
                <div class="job-card-date">
                    <?php printf(esc_html__('Ngày đăng: %s', 'jobscout'), esc_html(get_the_date('d/m/Y'))); ?>
                </div>
                <div class="job-card-badges">
                    <span class="job-badge job-badge-type"><?php echo esc_html($type_label); ?></span>
                    <span class="job-badge job-badge-cat"><?php echo esc_html($cat_label); ?></span>
                    <span class="job-badge job-badge-loc"><?php echo esc_html($loc); ?></span>
                </div>
            </div>
        </div>
        <div class="job-card-body">
            <ul class="job-card-bullets">
                <?php foreach ($bullets as $bullet): ?>
                    <li><?php echo esc_html($bullet); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

</article>