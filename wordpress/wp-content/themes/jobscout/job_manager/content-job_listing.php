<?php
/**
 * Job listing in the loop.
 *
 * Override template: content-job_listing.php
 */

if (!defined('ABSPATH')) {
    exit;
}

global $post;

// 1. Get exact post ID in loop
$job_id = $post->ID;

// 2. Get canonical job permalink
$job_permalink = get_permalink($job_id);

// Prefer WP Job Manager function if available
if (function_exists('get_the_job_permalink')) {
    $job_permalink = get_the_job_permalink();
}

// 2. Get company logo from DB or featured image
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
// Fallback to default theme logo
if (empty($company_logo)) {
    $company_logo = get_template_directory_uri() . '/images/job-logos/logo-sodoh.png';
}

// 3. Get Job Type from taxonomy
$types = get_the_terms($job_id, 'job_listing_type');
$type_label = (!empty($types) && !is_wp_error($types)) ? implode(', ', wp_list_pluck($types, 'name')) : 'Uncategorized';

// 4. Get Job Category from taxonomy
$cats = get_the_terms($job_id, 'job_listing_category');
$cat_label = (!empty($cats) && !is_wp_error($cats)) ? implode(', ', wp_list_pluck($cats, 'name')) : 'Category';

// 5. Get location from post meta
$loc = get_post_meta($job_id, '_job_location', true);
if (empty($loc)) {
    $loc = get_post_meta($job_id, 'location', true);
}
if (empty($loc)) {
    $loc = 'Nationwide'; // Fallback location
}

// 6. Content / description parsing
$content = get_the_content();
$bullets = array();

// Method A: Extract <li> tags
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

// Method B: Fallback to excerpt or first sentence
if (empty($bullets)) {
    $excerpt = get_the_excerpt($job_id);
    if (!empty($excerpt)) {
        $bullets[] = wp_strip_all_tags($excerpt);
    } else {
        $clean_content = wp_strip_all_tags($content);
        if (!empty($clean_content)) {
            $bullets[] = mb_substr($clean_content, 0, 120) . '...';
        } else {
            $bullets[] = 'View job details and requirements.';
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

    <!-- Card full link overlay -->
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
                    <?php printf(esc_html__('Posted: %s', 'jobscout'), esc_html(get_the_date('M j, Y'))); ?>
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