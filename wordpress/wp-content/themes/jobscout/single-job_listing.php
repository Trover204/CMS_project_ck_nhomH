<?php
/**
 * The template for displaying all single job posts (Job Detail Page)
 * Matching PDS Design
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package JobScout
 */

get_header();

$current_job_id = get_the_ID();

// Find URL of the Jobs page for Breadcrumbs
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

// Get current job data
$job_title = get_the_title();
if ( empty( $job_title ) || 'Auto Draft' === $job_title ) {
    $job_title = 'CHIEF OPERATING OFFICER HOTEL/ RESORT CHAIN';
}

$job_date = get_the_date( 'M d, Y' );
if ( empty( $job_date ) ) {
    $job_date = 'Oct 20, 2022';
}

// Company Logo
$company_logo = '';
if ( function_exists( 'get_the_company_logo' ) ) {
    $company_logo = get_the_company_logo( $current_job_id );
}
if ( empty( $company_logo ) ) {
    $company_logo = get_post_meta( $current_job_id, '_company_logo', true );
}
if ( empty( $company_logo ) && has_post_thumbnail( $current_job_id ) ) {
    $company_logo = get_the_post_thumbnail_url( $current_job_id, 'thumbnail' );
}
if ( empty( $company_logo ) ) {
    $company_logo = $fallback_logos[0]; // SODOH logo as in design
}

// Badges
$types = get_the_terms( $current_job_id, 'job_listing_type' );
$type_label = ( ! empty( $types ) && ! is_wp_error( $types ) ) ? $types[0]->name : 'Fulltime';

$cats = get_the_terms( $current_job_id, 'job_listing_category' );
$cat_label = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? $cats[0]->name : 'Category Name';

$loc = get_post_meta( $current_job_id, '_job_location', true );
if ( empty( $loc ) ) {
    $loc = get_post_meta( $current_job_id, 'location', true );
}
if ( empty( $loc ) ) {
    $loc = 'Ho Chi Minh City';
}

// Company photos gallery
$company_gallery = array();
$post_gallery_meta = get_post_meta( $current_job_id, '_company_photos', true );
if ( ! empty( $post_gallery_meta ) && is_array( $post_gallery_meta ) ) {
    $company_gallery = $post_gallery_meta;
}

if ( empty( $company_gallery ) ) {
    $attached_images = get_attached_media( 'image', $current_job_id );
    if ( ! empty( $attached_images ) && count( $attached_images ) > 1 ) {
        foreach ( $attached_images as $img_att ) {
            $company_gallery[] = wp_get_attachment_url( $img_att->ID );
        }
    }
}

if ( empty( $company_gallery ) ) {
    $company_gallery = array(
        get_template_directory_uri() . '/images/company-photo.jpg',
        get_template_directory_uri() . '/images/photo-1493976040374-85c8e12f0c0e.jpg',
        get_template_directory_uri() . '/images/photo-1503899036084-c55cdd92da26.jpg',
        get_template_directory_uri() . '/images/photo-1509099836639-18ba1795216d.jpg',
        get_template_directory_uri() . '/images/photo-1524413840807-0c3cb6fa808d.jpg',
        get_template_directory_uri() . '/images/photo-1528360983277-13d401cdc186.jpg',
    );
}

$company_photo      = ! empty( $company_gallery[0] ) ? $company_gallery[0] : ( get_template_directory_uri() . '/images/company-photo.jpg' );
$extra_photos_count = count( $company_gallery ) > 1 ? ( count( $company_gallery ) - 1 ) : 5;

// Other jobs data (6 cards for OTHER JOBS section)
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

// Other jobs query from database excluding current post
$other_jobs_query = new WP_Query( array(
    'post_type'           => 'job_listing',
    'post_status'         => 'publish',
    'posts_per_page'      => 6,
    'post__not_in'        => array( $current_job_id ),
    'ignore_sticky_posts' => 1,
    'orderby'             => 'date',
    'order'               => 'DESC',
) );

$other_jobs = array();
if ( $other_jobs_query->have_posts() ) {
    $idx = 0;
    while ( $other_jobs_query->have_posts() ) {
        $other_jobs_query->the_post();
        $jid = get_the_ID();

        $jlogo = '';
        if ( function_exists( 'get_the_company_logo' ) ) {
            $jlogo = get_the_company_logo( $jid );
        }
        if ( empty( $jlogo ) ) {
            $jlogo = get_post_meta( $jid, '_company_logo', true );
        }
        if ( empty( $jlogo ) && has_post_thumbnail( $jid ) ) {
            $jlogo = get_the_post_thumbnail_url( $jid, 'thumbnail' );
        }
        if ( empty( $jlogo ) ) {
            $jlogo = $fallback_logos[ $idx % count( $fallback_logos ) ];
        }

        $jtypes = get_the_terms( $jid, 'job_listing_type' );
        $jtype_label = ( ! empty( $jtypes ) && ! is_wp_error( $jtypes ) ) ? $jtypes[0]->name : 'Fulltime';

        $jcats = get_the_terms( $jid, 'job_listing_category' );
        $jcat_label = ( ! empty( $jcats ) && ! is_wp_error( $jcats ) ) ? $jcats[0]->name : 'Category Name';

        $jloc = get_post_meta( $jid, '_job_location', true );
        if ( empty( $jloc ) ) {
            $jloc = get_post_meta( $jid, 'location', true );
        }
        if ( empty( $jloc ) ) {
            $jloc = 'Ho Chi Minh City';
        }

        $other_jobs[] = array(
            'id'       => $jid,
            'title'    => get_the_title(),
            'url'      => get_permalink(),
            'date'     => get_the_date( 'M d, Y' ),
            'type'     => $jtype_label,
            'category' => $jcat_label,
            'location' => $jloc,
            'logo'     => $jlogo,
            'bullets'  => array(
                'Be responsible for the effective operational management of the hotel',
                'Excellent salary bonuses & recognition activities',
                'Foreign language allowance (up to 500USD/month)',
            ),
        );
        $idx++;
    }
    wp_reset_postdata();
}

if ( count( $other_jobs ) < 6 ) {
    $existing = count( $other_jobs );
    $needed = 6 - $existing;
    for ( $i = 0; $i < $needed; $i++ ) {
        $dj = $demo_jobs[ $i % count( $demo_jobs ) ];
        $other_jobs[] = array(
            'id'       => 'demo-other-' . ( $i + 1 ),
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

<div class="job-detail-page-wrapper">
    <!-- Breadcrumb Area -->
    <div class="job-detail-breadcrumb-wrap">
        <div class="container job-detail-container">
            <nav class="job-detail-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'jobscout' ); ?>">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumb-link"><?php esc_html_e( 'Home', 'jobscout' ); ?></a>
                <span class="breadcrumb-sep">/</span>
                <a href="<?php echo esc_url( $jobs_page_url ); ?>" class="breadcrumb-link"><?php esc_html_e( 'All Jobs', 'jobscout' ); ?></a>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current"><?php esc_html_e( 'Job Detail', 'jobscout' ); ?></span>
            </nav>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="container job-detail-container">
        <!-- Top Job Header Card -->
        <div class="job-detail-top-card">
            <div class="job-detail-logo">
                <img src="<?php echo esc_url( $company_logo ); ?>" alt="<?php echo esc_attr( $job_title ); ?>" />
            </div>
            <div class="job-detail-meta">
                <h1 class="job-detail-title"><?php echo esc_html( $job_title ); ?></h1>
                <div class="job-detail-date">
                    <?php printf( esc_html__( 'Created: %s', 'jobscout' ), esc_html( $job_date ) ); ?>
                </div>
                <div class="job-detail-badges">
                    <span class="job-badge job-badge-type"><?php echo esc_html( $type_label ); ?></span>
                    <span class="job-badge job-badge-cat"><?php echo esc_html( $cat_label ); ?></span>
                    <span class="job-badge job-badge-loc"><?php echo esc_html( $loc ); ?></span>
                </div>
            </div>
            <div class="job-detail-actions">
                <button type="button" class="job-btn-share" onclick="jobscoutShareJob()">
                    <?php esc_html_e( 'SHARE', 'jobscout' ); ?>
                </button>
                <button type="button" class="job-btn-apply" onclick="jobscoutApplyJob()">
                    <?php esc_html_e( 'APPLY JOB', 'jobscout' ); ?>
                </button>
            </div>
        </div>

        <!-- 2 Columns Layout -->
        <div class="job-detail-columns">
            <!-- Left Column: Details -->
            <div class="job-detail-main-col">
                <div class="job-detail-content-box">
                    <div class="job-detail-section-block">
                        <h2 class="job-section-heading"><?php esc_html_e( 'Overview about Company', 'jobscout' ); ?></h2>
                        <div class="job-section-body">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, at. Vivamus volutpat in nunc, porttitor dui. Ut placerat aenean accumsan a, aenean lacus eu. Aliquet urna, habitasse elit lorem id enim quam. Ea varius nulla nullam dignissim massa tempor, massa tortor. Eget auctor nulla maecenas ac tortor.</p>
                            <p>Ornare faucibus sed vitae dolor eu eu faucibus leo enim. Tincidunt quisque sed netus nibh pharetra. Gravida venenatis, lobortis id mi. Metus, ultrices duis pellentesque aliquet amet cras blandit. Aliquet purus quam laoreet ipsum pretium. Odio quis eu nunc, diam habitant nunc massa sed. Placerat arcu quis est, magna facilisis amet. Dignissim vel adipiscing elit facilisi praesent platea. Nulla posuere feugiat turpis magna etiam. Non nec felis eu praesent cras.</p>
                        </div>
                    </div>

                    <div class="job-detail-section-block">
                        <h2 class="job-section-heading"><?php esc_html_e( 'Our Key Skills', 'jobscout' ); ?></h2>
                        <div class="job-section-body">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, at. Vivamus volutpat in nunc, porttitor dui. Ut placerat aenean accumsan a, aenean lacus eu. Aliquet urna, habitasse elit lorem id enim quam. Ea varius nulla nullam dignissim massa tempor, massa tortor. Eget auctor nulla maecenas ac tortor.</p>
                        </div>
                    </div>

                    <div class="job-detail-section-block">
                        <h2 class="job-section-heading"><?php esc_html_e( 'Why You\'ll Love Working Here', 'jobscout' ); ?></h2>
                        <div class="job-section-body">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, at. Vivamus volutpat in nunc, porttitor dui. Ut placerat aenean accumsan a, aenean lacus eu. Aliquet urna, habitasse elit lorem id enim quam. Ea varius nulla nullam dignissim massa tempor, massa tortor. Eget auctor nulla maecenas ac tortor.</p>
                            <p>Ornare faucibus sed vitae dolor eu eu faucibus leo enim. Tincidunt quisque sed netus nibh pharetra. Gravida venenatis, lobortis id mi. Metus, ultrices duis pellentesque aliquet amet cras blandit. Aliquet purus quam laoreet ipsum pretium. Odio quis eu nunc, diam habitant nunc massa sed. Placerat arcu quis est, magna facilisis amet. Dignissim vel adipiscing elit facilisi praesent platea. Nulla posuere feugiat turpis magna etiam. Non nec felis eu praesent cras.</p>
                        </div>
                    </div>

                    <div class="job-detail-section-block">
                        <h2 class="job-section-heading"><?php esc_html_e( 'Location', 'jobscout' ); ?></h2>
                        <div class="job-section-body">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, at. Vivamus volutpat in nunc, porttitor dui.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <aside class="job-detail-sidebar-col">
                <!-- Staff Rating Widget -->
                <div class="job-sidebar-widget widget-staff-rating">
                    <h3 class="widget-title"><?php esc_html_e( 'Staff Rating', 'jobscout' ); ?></h3>
                    <div class="rating-stars-wrap">
                        <span class="rating-stars" aria-label="4 out of 5 stars">
                            <svg class="star-icon star-filled" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ea751e" width="20" height="20"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="star-icon star-filled" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ea751e" width="20" height="20"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="star-icon star-filled" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ea751e" width="20" height="20"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="star-icon star-filled" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ea751e" width="20" height="20"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <svg class="star-icon star-empty" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ea751e" stroke-width="2" width="20" height="20"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </span>
                        <span class="rating-score">4.0</span>
                    </div>
                </div>

                <!-- Company Photos Widget -->
                <div class="job-sidebar-widget widget-company-photos">
                    <h3 class="widget-title"><?php esc_html_e( 'Company Photos', 'jobscout' ); ?></h3>
                    <div class="company-photos-media" onclick="openCompanyPhotos(0)" title="<?php esc_attr_e( 'Click to view company photos', 'jobscout' ); ?>" role="button" tabindex="0">
                        <img src="<?php echo esc_url( $company_photo ); ?>" alt="<?php esc_attr_e( 'Company Photos', 'jobscout' ); ?>" loading="lazy" />
                        <div class="photos-overlay-badge">
                            <span class="photos-badge-text">+<?php echo esc_html( $extra_photos_count ); ?></span>
                            <span class="photos-hint-text"><?php esc_html_e( 'View Photos', 'jobscout' ); ?></span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <!-- OTHER JOBS Section -->
        <section class="job-detail-other-jobs">
            <h2 class="other-jobs-title"><?php esc_html_e( 'OTHER JOBS', 'jobscout' ); ?></h2>
            <div class="jobs-grid">
                <?php foreach ( $other_jobs as $ojob ) : ?>
                    <article class="job-card" onclick="window.location.href='<?php echo esc_url( $ojob['url'] ); ?>';">
                        <div class="job-card-inner">
                            <div class="job-card-header">
                                <div class="job-card-logo">
                                    <img src="<?php echo esc_url( $ojob['logo'] ); ?>" alt="<?php echo esc_attr( $ojob['title'] ); ?>" loading="lazy" />
                                </div>
                                <div class="job-card-heading">
                                    <h3 class="job-card-title">
                                        <a href="<?php echo esc_url( $ojob['url'] ); ?>" onclick="event.stopPropagation();">
                                            <?php echo esc_html( $ojob['title'] ); ?>
                                        </a>
                                    </h3>
                                    <div class="job-card-date">
                                        <?php printf( esc_html__( 'Created: %s', 'jobscout' ), esc_html( $ojob['date'] ) ); ?>
                                    </div>
                                    <div class="job-card-badges">
                                        <span class="job-badge job-badge-type"><?php echo esc_html( $ojob['type'] ); ?></span>
                                        <span class="job-badge job-badge-cat"><?php echo esc_html( $ojob['category'] ); ?></span>
                                        <span class="job-badge job-badge-loc"><?php echo esc_html( $ojob['location'] ); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="job-card-body">
                                <ul class="job-card-bullets">
                                    <?php foreach ( $ojob['bullets'] as $bullet ) : ?>
                                        <li><?php echo esc_html( $bullet ); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>

<?php // Orange Newsletter Section (Contact Form 7) ?>
<?php get_template_part( 'template-parts/newsletter-cf7' ); ?>

<!-- Company Photos Lightbox Modal -->
<div id="companyPhotosModal" class="company-photos-lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Company Photos Gallery', 'jobscout' ); ?>">
    <div class="lightbox-overlay" onclick="closeCompanyPhotos()"></div>
    <div class="lightbox-container">
        <div class="lightbox-header">
            <span class="lightbox-title"><?php esc_html_e( 'Company Photos', 'jobscout' ); ?></span>
            <span class="lightbox-counter"><span id="lightboxCurrentIdx">1</span> / <span id="lightboxTotalCount"><?php echo count( $company_gallery ); ?></span></span>
            <button type="button" class="lightbox-close-btn" onclick="closeCompanyPhotos()" aria-label="<?php esc_attr_e( 'Close', 'jobscout' ); ?>">&times;</button>
        </div>
        <div class="lightbox-main-view">
            <button type="button" class="lightbox-nav-btn lightbox-prev-btn" onclick="changeCompanyPhoto(-1)" aria-label="<?php esc_attr_e( 'Previous', 'jobscout' ); ?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <div class="lightbox-image-stage">
                <img id="lightboxActiveImg" src="<?php echo esc_url( $company_gallery[0] ); ?>" alt="<?php esc_attr_e( 'Company Photo', 'jobscout' ); ?>" />
            </div>
            <button type="button" class="lightbox-nav-btn lightbox-next-btn" onclick="changeCompanyPhoto(1)" aria-label="<?php esc_attr_e( 'Next', 'jobscout' ); ?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>
        <div class="lightbox-thumbs-bar">
            <?php foreach ( $company_gallery as $idx => $photo_url ) : ?>
                <div class="lightbox-thumb <?php echo 0 === $idx ? 'active' : ''; ?>" onclick="jumpToCompanyPhoto(<?php echo (int) $idx; ?>)" data-thumb-index="<?php echo (int) $idx; ?>">
                    <img src="<?php echo esc_url( $photo_url ); ?>" alt="Thumb <?php echo (int) ( $idx + 1 ); ?>" />
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
const companyGalleryImages = <?php echo json_encode( array_values( $company_gallery ) ); ?>;
let currentPhotoIndex = 0;

function openCompanyPhotos(startIndex) {
    if (!companyGalleryImages || !companyGalleryImages.length) return;
    currentPhotoIndex = (typeof startIndex === 'number' && startIndex >= 0 && startIndex < companyGalleryImages.length) ? startIndex : 0;
    updateLightboxView();
    const modal = document.getElementById('companyPhotosModal');
    if (modal) {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
}

function closeCompanyPhotos() {
    const modal = document.getElementById('companyPhotosModal');
    if (modal) {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
}

function changeCompanyPhoto(direction) {
    if (!companyGalleryImages || !companyGalleryImages.length) return;
    currentPhotoIndex = (currentPhotoIndex + direction + companyGalleryImages.length) % companyGalleryImages.length;
    updateLightboxView();
}

function jumpToCompanyPhoto(index) {
    if (typeof index === 'number' && index >= 0 && index < companyGalleryImages.length) {
        currentPhotoIndex = index;
        updateLightboxView();
    }
}

function updateLightboxView() {
    const activeImg = document.getElementById('lightboxActiveImg');
    const currentIdxSpan = document.getElementById('lightboxCurrentIdx');
    if (activeImg) {
        activeImg.style.opacity = '0.3';
        activeImg.src = companyGalleryImages[currentPhotoIndex];
        activeImg.onload = function() {
            activeImg.style.opacity = '1';
        };
        setTimeout(function() { activeImg.style.opacity = '1'; }, 200);
    }
    if (currentIdxSpan) {
        currentIdxSpan.textContent = currentPhotoIndex + 1;
    }
    const thumbs = document.querySelectorAll('.lightbox-thumb');
    thumbs.forEach(function(thumb, idx) {
        if (idx === currentPhotoIndex) {
            thumb.classList.add('active');
            thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        } else {
            thumb.classList.remove('active');
        }
    });
}

document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('companyPhotosModal');
    if (!modal || !modal.classList.contains('is-open')) return;
    if (e.key === 'Escape') {
        closeCompanyPhotos();
    } else if (e.key === 'ArrowLeft') {
        changeCompanyPhoto(-1);
    } else if (e.key === 'ArrowRight') {
        changeCompanyPhoto(1);
    }
});

function jobscoutShareJob() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        }).catch(function(err) {});
    } else {
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Job URL copied to clipboard!');
        }).catch(function() {
            prompt('Copy job link:', window.location.href);
        });
    }
}

function jobscoutApplyJob() {
    alert('Thank you for your interest! The application form is currently accepting submissions.');
}
</script>

<?php
get_footer();