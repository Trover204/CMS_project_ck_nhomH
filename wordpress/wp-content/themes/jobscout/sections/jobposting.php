<?php
/**
 * Job Posting / Top Jobs Section on Homepage
 * 
 * @package JobScout
 */

$ed_jobposting = get_theme_mod( 'ed_jobposting', true );
if ( ! $ed_jobposting ) {
    return;
}

$job_title = get_theme_mod( 'job_posting_section_title', 'TOP JOBS' );
if ( empty( $job_title ) || 'Job Posting' === $job_title ) {
    $job_title = 'TOP JOBS';
}

// Find URL of the Jobs page
$jobs_page = get_page_by_path( 'jobs' );
if ( ! $jobs_page ) {
    $jobs_page = get_page_by_path( 'jobs-2' );
}
if ( ! $jobs_page ) {
    $jobs_pages = jobscout_get_page_id_by_template( 'page-jobs.php' );
    if ( ! empty( $jobs_pages ) ) {
        $jobs_page = $jobs_pages[0];
    }
}
$jobs_page_url = $jobs_page ? get_permalink( $jobs_page->ID ) : home_url( '/index.php/jobs/' );

$logo_base = get_template_directory_uri() . '/images/job-logos/';
$fallback_logos = array(
    $logo_base . 'logo-sodoh.png',
    $logo_base . 'logo-fortune.png',
    $logo_base . 'logo-banquet.png',
    $logo_base . 'logo-sevenhouse.png',
    $logo_base . 'logo-phothin.png',
    $logo_base . 'logo-triangle.png',
);

// Fallback demo jobs matching PDS design
$demo_jobs = array(
    array(
        'title'    => 'HOTEL MANAGER',
        'date'     => 'Oct 20, 2022',
        'type'     => 'Fulltime',
        'category' => 'Category Name',
        'location' => 'Ho Chi Minh City',
        'logo'     => $logo_base . 'logo-sodoh.png',
        'bullets'  => array(
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)',
        ),
    ),
    array(
        'title'    => 'GENERAL MANAGER - LEADING HOTEL CHAIN',
        'date'     => 'Oct 20, 2022',
        'type'     => 'Fulltime',
        'category' => 'Category Name',
        'location' => 'Ho Chi Minh City',
        'logo'     => $logo_base . 'logo-fortune.png',
        'bullets'  => array(
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)',
        ),
    ),
    array(
        'title'    => 'BANQUET MANAGER',
        'date'     => 'Oct 20, 2022',
        'type'     => 'Fulltime',
        'category' => 'Category Name',
        'location' => 'Ho Chi Minh City',
        'logo'     => $logo_base . 'logo-banquet.png',
        'bullets'  => array(
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)',
        ),
    ),
    array(
        'title'    => 'BELLMAN',
        'date'     => 'Oct 20, 2022',
        'type'     => 'Fulltime',
        'category' => 'Category Name',
        'location' => 'Ho Chi Minh City',
        'logo'     => $logo_base . 'logo-sevenhouse.png',
        'bullets'  => array(
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)',
        ),
    ),
    array(
        'title'    => 'CHIEF OPERATING OFFICER HOTEL/ RESORT CHAIN',
        'date'     => 'Oct 20, 2022',
        'type'     => 'Fulltime',
        'category' => 'Category Name',
        'location' => 'Ho Chi Minh City',
        'logo'     => $logo_base . 'logo-phothin.png',
        'bullets'  => array(
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)',
        ),
    ),
    array(
        'title'    => 'LOSS PREVENTION OFFICER',
        'date'     => 'Oct 20, 2022',
        'type'     => 'Fulltime',
        'category' => 'Category Name',
        'location' => 'Ho Chi Minh City',
        'logo'     => $logo_base . 'logo-triangle.png',
        'bullets'  => array(
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)',
        ),
    ),
);

// Query up to 6 published jobs from database
$rendered_jobs = array();
$real_jobs_query = new WP_Query( array(
    'post_type'           => 'job_listing',
    'post_status'         => 'publish',
    'posts_per_page'      => 6,
    'ignore_sticky_posts' => 1,
    'orderby'             => 'date',
    'order'               => 'DESC',
) );

if ( $real_jobs_query->have_posts() ) {
    $idx = 0;
    while ( $real_jobs_query->have_posts() ) {
        $real_jobs_query->the_post();
        $job_id = get_the_ID();

        // Logo
        $logo = '';
        if ( function_exists( 'get_the_company_logo' ) ) {
            $logo = get_the_company_logo( $job_id );
        }
        if ( empty( $logo ) ) {
            $logo = get_post_meta( $job_id, '_company_logo', true );
        }
        if ( empty( $logo ) && has_post_thumbnail( $job_id ) ) {
            $logo = get_the_post_thumbnail_url( $job_id, 'thumbnail' );
        }
        if ( empty( $logo ) ) {
            $logo = $fallback_logos[ $idx % count( $fallback_logos ) ];
        }

        // Type
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

        $rendered_jobs[] = array(
            'id'       => $job_id,
            'title'    => get_the_title(),
            'url'      => get_permalink(),
            'date'     => get_the_date( 'M d, Y' ),
            'type'     => $type_label,
            'category' => $cat_label,
            'location' => $loc,
            'logo'     => $logo,
            'bullets'  => $bullets,
        );
        $idx++;
    }
    wp_reset_postdata();
}

// If real jobs count is less than 6, backfill with demo jobs to ensure exactly 6 cards like design
if ( count( $rendered_jobs ) < 6 ) {
    $existing_count = count( $rendered_jobs );
    $needed = 6 - $existing_count;
    for ( $i = 0; $i < $needed; $i++ ) {
        $dj = $demo_jobs[ $i % count( $demo_jobs ) ];
        $rendered_jobs[] = array(
            'id'       => 'demo-' . ( $i + 1 ),
            'title'    => $dj['title'],
            'url'      => $jobs_page_url,
            'date'     => $dj['date'],
            'type'     => $dj['type'],
            'category' => $dj['category'],
            'location' => $dj['location'],
            'logo'     => $dj['logo'],
            'bullets'  => $dj['bullets'],
        );
    }
}
?>

<section id="job-posting-section" class="top-job-section home-top-jobs-section">
    <div class="container">
        <?php if ( $job_title ) : ?>
            <h2 class="section-title jobs-home-title"><?php echo esc_html( $job_title ); ?></h2>
        <?php endif; ?>

        <div class="jobs-grid home-jobs-grid">
            <?php foreach ( $rendered_jobs as $job ) : ?>
                <article class="job-card" onclick="window.location.href='<?php echo esc_url( $job['url'] ); ?>';">
                    <div class="job-card-inner">
                        <div class="job-card-header">
                            <div class="job-card-logo">
                                <img src="<?php echo esc_url( $job['logo'] ); ?>" alt="<?php echo esc_attr( $job['title'] ); ?>" loading="lazy" />
                            </div>
                            <div class="job-card-heading">
                                <h3 class="job-card-title">
                                    <a href="<?php echo esc_url( $job['url'] ); ?>" onclick="event.stopPropagation();">
                                        <?php echo esc_html( $job['title'] ); ?>
                                    </a>
                                </h3>
                                <div class="job-card-date">
                                    <?php printf( esc_html__( 'Created: %s', 'jobscout' ), esc_html( $job['date'] ) ); ?>
                                </div>
                                <div class="job-card-badges">
                                    <span class="job-badge job-badge-type"><?php echo esc_html( $job['type'] ); ?></span>
                                    <span class="job-badge job-badge-cat"><?php echo esc_html( $job['category'] ); ?></span>
                                    <span class="job-badge job-badge-loc"><?php echo esc_html( $job['location'] ); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="job-card-body">
                            <ul class="job-card-bullets">
                                <?php foreach ( $job['bullets'] as $bullet ) : ?>
                                    <li><?php echo esc_html( $bullet ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- View More Jobs Button -->
        <div class="jobs-loadmore-wrap home-viewmore-wrap">
            <a href="<?php echo esc_url( $jobs_page_url ); ?>" class="jobs-loadmore-btn home-viewmore-btn">
                <?php esc_html_e( 'VIEW MORE JOBS', 'jobscout' ); ?>
            </a>
        </div>
    </div>
</section>