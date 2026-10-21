<?php
/**
 * Template Name: Contact Page
 *
 * Template for displaying the Contact Us page matching the PDS design.
 *
 * @package JobScout
 */

get_header();
//contact page template
// Determine banner background image (use page featured image if set, otherwise kimono street image)
$banner_img = get_the_post_thumbnail_url( get_the_ID(), 'full' );
if ( ! $banner_img ) {
    $upload_dir = wp_get_upload_dir();
    $banner_img = $upload_dir['baseurl'] . '/2026/10/project-development.jpg';
}

// Get News page URL for the "blog posts" link
$news_page     = get_page_by_path( 'news' );
$news_page_url = $news_page ? get_permalink( $news_page->ID ) : home_url( '/index.php/news/' );
?>

<!-- Contact Us Hero Banner -->
<section class="contact-banner-section" style="background-image: url('<?php echo esc_url( $banner_img ); ?>');">
    <div class="contact-banner-overlay"></div>
    <div class="container contact-banner-container">
        <h1 class="contact-banner-title"><?php echo esc_html( get_theme_mod( 'contact_banner_title', 'CONTACT US' ) ); ?></h1>
    </div>
</section>

<!-- Our Headquarters Address Section -->
<section class="contact-hq-section">
    <div class="container">
        <h2 class="contact-hq-title"><?php echo esc_html( get_theme_mod( 'contact_hq_title', 'Our Headquarters Address' ) ); ?></h2>
        <p class="contact-hq-address">
            <?php echo esc_html( get_theme_mod( 'contact_hq_address', '60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City, Viet Nam' ) ); ?>
        </p>
    </div>
</section>

<!-- For Employers & For Jobseekers Section -->
<section class="contact-details-section">
    <div class="container">
        <div class="contact-columns">
            <!-- Left Column: For Employers -->
            <div class="contact-col">
                <h3 class="contact-col-title"><?php esc_html_e( 'For Employers', 'jobscout' ); ?></h3>
                <p class="contact-col-subtitle"><?php esc_html_e( 'Call our Sales Hotline', 'jobscout' ); ?></p>

                <div class="contact-location-block">
                    <h4 class="contact-location-name"><?php esc_html_e( 'Ho Chi Minh', 'jobscout' ); ?></h4>
                    <p class="contact-phone">
                        <a href="tel:+84977460589"><?php echo esc_html( get_theme_mod( 'contact_hcm_phone', '+84 977 460 589' ) ); ?></a>
                    </p>
                </div>

                <div class="contact-location-block">
                    <h4 class="contact-location-name"><?php esc_html_e( 'Ha Noi', 'jobscout' ); ?></h4>
                    <p class="contact-phone">
                        <a href="tel:+84988460589"><?php echo esc_html( get_theme_mod( 'contact_hn_phone', '+84 988 460 589' ) ); ?></a>
                    </p>
                </div>

                <div class="contact-col-note">
                    <p>
                        <?php esc_html_e( 'Request a call from one of our', 'jobscout' ); ?><br />
                        <?php esc_html_e( 'Customer Love Account Managers', 'jobscout' ); ?><br />
                        <?php esc_html_e( "We're ready to help you grow!", 'jobscout' ); ?>
                    </p>
                </div>
            </div>

            <!-- Right Column: For Jobseekers -->
            <div class="contact-col">
                <h3 class="contact-col-title"><?php esc_html_e( 'For Jobseekers', 'jobscout' ); ?></h3>
                <div class="contact-jobseeker-links">
                    <p>
                        <?php esc_html_e( 'Ask a question on our', 'jobscout' ); ?>
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="contact-orange-link"><?php esc_html_e( 'Facebook', 'jobscout' ); ?></a>
                        <?php esc_html_e( 'page', 'jobscout' ); ?>
                    </p>
                    <p>
                        <?php esc_html_e( 'Read our', 'jobscout' ); ?>
                        <a href="<?php echo esc_url( $news_page_url ); ?>" class="contact-orange-link"><?php esc_html_e( 'blog posts', 'jobscout' ); ?></a>
                        <?php esc_html_e( 'on interview and CV tips', 'jobscout' ); ?>
                    </p>
                </div>

                <div class="contact-location-block contact-call-us-block">
                    <h4 class="contact-location-name"><?php esc_html_e( 'Call us at', 'jobscout' ); ?></h4>
                    <p class="contact-phone">
                        <a href="tel:+842838277206"><?php echo esc_html( get_theme_mod( 'contact_jobseeker_phone', '+84 28 3827 7206' ) ); ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


<?php // Orange Newsletter Section (Contact Form 7) ?>
<?php get_template_part( 'template-parts/newsletter-cf7' ); ?>

<?php
get_footer();
