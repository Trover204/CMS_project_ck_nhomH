<?php
/**
 * Template Name: Jobs Page
 *
 * Template for displaying the Careers / All Jobs page matching the PDS design.
 *
 * @package JobScout
 */

get_header();

// Determine banner background image
$banner_img = get_the_post_thumbnail_url( get_the_ID(), 'full' );
if ( ! $banner_img ) {
    $banner_img = get_template_directory_uri() . '/images/career-banner.jpg';
}

$logo_base = get_template_directory_uri() . '/images/job-logos/';
$fallback_logos = array(
    $logo_base . 'logo-sodoh.png',
    $logo_base . 'logo-fortune.png',
    $logo_base . 'logo-banquet.png',
    $logo_base . 'logo-sevenhouse.png',
    $logo_base . 'logo-phothin.png',
    $logo_base . 'logo-triangle.png',
);

// Fallback demo jobs if database has no job listings or fewer than needed
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

// Query real jobs from database if available
$real_jobs_query = new WP_Query( array(
    'post_type'           => 'job_listing',
    'post_status'         => 'publish',
    'posts_per_page'      => -1,
    'ignore_sticky_posts' => 1,
    'orderby'             => 'date',
    'order'               => 'DESC',
) );

$rendered_jobs = array();

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
            'timestamp'=> get_the_date( 'U' ),
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

// If real jobs count is less than 6, blend or use demo jobs to guarantee identical design
if ( count( $rendered_jobs ) < 6 ) {
    $existing_count = count( $rendered_jobs );
    $needed = 12 - $existing_count;
    for ( $i = 0; $i < $needed; $i++ ) {
        $dj = $demo_jobs[ $i % count( $demo_jobs ) ];
        $rendered_jobs[] = array(
            'id'       => 'demo-' . ( $i + 1 ),
            'title'    => $dj['title'],
            'url'      => '#',
            'date'     => $dj['date'],
            'timestamp'=> strtotime( '2022-10-20' ) - ( $i * 86400 ),
            'type'     => $dj['type'],
            'category' => $dj['category'],
            'location' => $dj['location'],
            'logo'     => $dj['logo'],
            'bullets'  => $dj['bullets'],
        );
    }
}
?>

<!-- Careers Hero Banner -->
<section class="jobs-banner-section" style="background-image: url('<?php echo esc_url( $banner_img ); ?>');">
    <div class="jobs-banner-overlay"></div>
    <div class="container jobs-banner-container">
        <h1 class="jobs-banner-title"><?php echo esc_html( get_theme_mod( 'jobs_banner_title', 'CAREER WITH US' ) ); ?></h1>
    </div>
</section>

<!-- All Jobs Section -->
<section class="jobs-main-section">
    <div class="container jobs-main-container">
        <!-- Top Filter/Sort Bar -->
        <div class="jobs-top-bar">
            <h2 class="jobs-section-title"><?php echo esc_html( get_theme_mod( 'jobs_section_heading', 'ALL JOBS' ) ); ?></h2>
            <div class="jobs-sort-wrap">
                <select id="jobs-sort-select" class="jobs-sort-select" onchange="jobscoutSortJobs(this.value)" aria-label="<?php esc_attr_e( 'Sort Jobs', 'jobscout' ); ?>">
                    <option value="latest"><?php esc_html_e( 'Latest Jobs', 'jobscout' ); ?></option>
                    <option value="oldest"><?php esc_html_e( 'Oldest Jobs', 'jobscout' ); ?></option>
                    <option value="title-asc"><?php esc_html_e( 'Title A-Z', 'jobscout' ); ?></option>
                </select>
            </div>
        </div>

        <!-- Jobs Grid -->
        <div class="jobs-grid" id="jobs-grid">
            <?php foreach ( $rendered_jobs as $index => $job ) : 
                $hidden_class = ( $index >= 6 ) ? ' job-card-hidden' : '';
            ?>
                <article class="job-card<?php echo esc_attr( $hidden_class ); ?>" 
                         data-index="<?php echo esc_attr( $index ); ?>"
                         data-timestamp="<?php echo esc_attr( $job['timestamp'] ); ?>"
                         data-title="<?php echo esc_attr( strtolower( $job['title'] ) ); ?>"
                         onclick="if('<?php echo esc_js( $job['url'] ); ?>' !== '#') { window.location.href='<?php echo esc_url( $job['url'] ); ?>'; }">
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

        <!-- Load More Jobs Button -->
        <?php if ( count( $rendered_jobs ) > 6 ) : ?>
            <div class="jobs-loadmore-wrap">
                <button type="button" class="jobs-loadmore-btn" id="jobs-loadmore-btn" onclick="jobscoutLoadMoreJobs()">
                    <?php esc_html_e( 'LOAD MORE JOBS', 'jobscout' ); ?>
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php // Orange Newsletter Section (Contact Form 7) ?>
<?php get_template_part( 'template-parts/newsletter-cf7' ); ?>

<script>
function jobscoutLoadMoreJobs() {
    var hiddenCards = document.querySelectorAll('.job-card.job-card-hidden');
    var countToShow = 6;
    for (var i = 0; i < hiddenCards.length && i < countToShow; i++) {
        hiddenCards[i].classList.remove('job-card-hidden');
        hiddenCards[i].classList.add('job-card-fadein');
    }
    var remaining = document.querySelectorAll('.job-card.job-card-hidden');
    if (remaining.length === 0) {
        var btn = document.getElementById('jobs-loadmore-btn');
        if (btn) {
            btn.style.display = 'none';
        }
    }
}

function jobscoutSortJobs(sortMode) {
    var grid = document.getElementById('jobs-grid');
    if (!grid) return;
    var cards = Array.from(grid.getElementsByClassName('job-card'));

    cards.sort(function(a, b) {
        if (sortMode === 'latest') {
            return parseInt(b.getAttribute('data-timestamp') || 0) - parseInt(a.getAttribute('data-timestamp') || 0);
        } else if (sortMode === 'oldest') {
            return parseInt(a.getAttribute('data-timestamp') || 0) - parseInt(b.getAttribute('data-timestamp') || 0);
        } else if (sortMode === 'title-asc') {
            return (a.getAttribute('data-title') || '').localeCompare(b.getAttribute('data-title') || '');
        }
        return 0;
    });

    cards.forEach(function(card) {
        grid.appendChild(card);
    });
}
</script>

<?php
get_footer();
