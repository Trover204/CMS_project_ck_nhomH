<?php
/**
 * Job listing in the loop.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/content-job_listing.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager
 * @category    Template
 * @since       1.0.0
 * @version     1.27.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;

$job_id = get_the_ID();

// Get permalink - guaranteed to have a valid detail link
$job_permalink = get_permalink( $job_id );
if ( function_exists( 'get_the_job_permalink' ) ) {
    $custom_permalink = get_the_job_permalink( $job_id );
    if ( ! empty( $custom_permalink ) ) {
        $job_permalink = $custom_permalink;
    }
}

// Logo bases & fallbacks
$logo_base = get_template_directory_uri() . '/images/job-logos/';
$fallback_logos = array(
    $logo_base . 'logo-sodoh.png',
    $logo_base . 'logo-fortune.png',
    $logo_base . 'logo-banquet.png',
    $logo_base . 'logo-sevenhouse.png',
    $logo_base . 'logo-phothin.png',
    $logo_base . 'logo-triangle.png',
);

// Determine company logo
$company_logo = '';
if ( function_exists( 'get_the_company_logo' ) ) {
    $company_logo = get_the_company_logo( $job_id );
}
if ( empty( $company_logo ) ) {
    $company_logo = get_post_meta( $job_id, '_company_logo', true );
}
if ( empty( $company_logo ) && has_post_thumbnail( $job_id ) ) {
    $company_logo = get_the_post_thumbnail_url( $job_id, 'thumbnail' );
}
if ( empty( $company_logo ) ) {
    $company_logo = $fallback_logos[ $job_id % count( $fallback_logos ) ];
}

// Job Type
$types = get_the_terms( $job_id, 'job_listing_type' );
$type_label = ( ! empty( $types ) && ! is_wp_error( $types ) ) ? $types[0]->name : 'Fulltime';

// Category
$cats = get_the_terms( $job_id, 'job_listing_category' );
$cat_label = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? $cats[0]->name : 'Category Name';

// Location
$loc = get_post_meta( $job_id, '_job_location', true );
if ( empty( $loc ) ) {
    $loc = get_post_meta( $job_id, 'location', true );
}
if ( empty( $loc ) ) {
    $loc = 'Ho Chi Minh City';
}

// Bullets extraction from content
$content = get_the_content();
$bullets = array();
if ( preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $content, $matches ) ) {
    foreach ( $matches[1] as $m ) {
        $clean_bullet = trim( wp_strip_all_tags( $m ) );
        if ( ! empty( $clean_bullet ) ) {
            $bullets[] = $clean_bullet;
        }
        if ( count( $bullets ) >= 3 ) break;
    }
}
if ( empty( $bullets ) ) {
    $bullets = array(
        'Be responsible for the effective operational management of the hotel',
        'Excellent salary bonuses & recognition activities',
        'Foreign language allowance (up to 500USD/month)',
    );
}

$job_title = get_the_title();
if ( empty( $job_title ) ) {
    $job_title = 'HOTEL MANAGER';
}
?>
<article <?php job_listing_class( 'job-card pds-job-card' ); ?> 
         data-longitude="<?php echo esc_attr( $post->geolocation_lat ); ?>" 
         data-latitude="<?php echo esc_attr( $post->geolocation_long ); ?>"
         onclick="window.location.href='<?php echo esc_url( $job_permalink ); ?>';"
         style="cursor: pointer; position: relative;">

    <!-- Full-card clickable link to ensure click always navigates to job detail -->
    <a href="<?php echo esc_url( $job_permalink ); ?>" class="job-card-stretched-link" aria-label="<?php echo esc_attr( $job_title ); ?>"></a>

    <div class="job-card-inner">
        <div class="job-card-header">
            <div class="job-card-logo">
                <a href="<?php echo esc_url( $job_permalink ); ?>">
                    <img src="<?php echo esc_url( $company_logo ); ?>" alt="<?php echo esc_attr( $job_title ); ?>" loading="lazy" />
                </a>
            </div>
            <div class="job-card-heading">
                <h3 class="job-card-title entry-title">
                    <a href="<?php echo esc_url( $job_permalink ); ?>">
                        <?php echo esc_html( $job_title ); ?>
                    </a>
                </h3>
                <div class="job-card-date">
                    <?php printf( esc_html__( 'Created: %s', 'jobscout' ), esc_html( get_the_date( 'M d, Y' ) ) ); ?>
                </div>
                <div class="job-card-badges">
                    <span class="job-badge job-badge-type"><?php echo esc_html( $type_label ); ?></span>
                    <span class="job-badge job-badge-cat"><?php echo esc_html( $cat_label ); ?></span>
                    <span class="job-badge job-badge-loc"><?php echo esc_html( $loc ); ?></span>
                </div>
            </div>
        </div>
        <div class="job-card-body">
            <ul class="job-card-bullets">
                <?php foreach ( $bullets as $bullet ) : ?>
                    <li><?php echo esc_html( $bullet ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

</article>
