<?php
/**
 * Template Name: About Page
 *
 * Template for displaying the About Us page matching the PDS design mockup.
 *
 * @package JobScout
 */

get_header();

$theme_img_dir = get_template_directory_uri() . '/images/';

// Available images in themes/jobscout/images
$banner_bg   = $theme_img_dir . 'photo-1493976040374-85c8e12f0c0e.jpg';
$intro_img   = $theme_img_dir . 'photo-1524413840807-0c3cb6fa808d.jpg';
$company_img = $theme_img_dir . 'photo-1536098561742-ca998e48cbcc.jpg';
?>

<!-- 1. Hero Banner -->
<section class="about-hero-section" style="background-image: url('<?php echo esc_url( $banner_bg ); ?>');">
    <div class="about-hero-overlay"></div>
    <div class="container about-hero-container">
        <h1 class="about-hero-title">
            <span>SHARE “OMOTENASHI”</span>
            <span>WITH THE WORLD</span>
        </h1>
    </div>
</section>

<!-- 2. About Us: Vision, Mission, Core Value -->
<section class="about-intro-section">
    <div class="container">
        <h2 class="about-section-heading"><?php esc_html_e( 'ABOUT US', 'jobscout' ); ?></h2>
        
        <div class="about-intro-grid">
            <div class="about-intro-media">
                <img src="<?php echo esc_url( $intro_img ); ?>" alt="<?php esc_attr_e( 'About Plan Do See', 'jobscout' ); ?>" loading="lazy" />
            </div>
            
            <div class="about-intro-content">
                <div class="about-info-group">
                    <h3 class="about-group-title"><?php esc_html_e( 'Our Vision', 'jobscout' ); ?></h3>
                    <p class="about-group-text">
                        <?php esc_html_e( 'Create hotels and restaurants around the world that offer memorable experiences while building a lasting, positive relationship together with our guests, partners, team members and communities.', 'jobscout' ); ?>
                    </p>
                </div>

                <div class="about-info-group">
                    <h3 class="about-group-title"><?php esc_html_e( 'Ours Mission', 'jobscout' ); ?></h3>
                    <p class="about-group-text">
                        <?php esc_html_e( 'Share “Omotenashi” with the world', 'jobscout' ); ?>
                    </p>
                </div>

                <div class="about-info-group">
                    <h3 class="about-group-title"><?php esc_html_e( 'Our Core Value', 'jobscout' ); ?></h3>
                    <p class="about-group-subtext">“If I were the guest…”</p>
                    <p class="about-group-text">
                        <?php esc_html_e( 'To provide guests with the hospitality you would want to receive as a guest.', 'jobscout' ); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Hotels, restaurants, banquets/weddings management -->
<section class="about-management-section">
    <div class="container">
        <div class="about-management-inner">
            <h3 class="about-management-title"><?php esc_html_e( 'Hotels, restaurants, banquets/weddings management', 'jobscout' ); ?></h3>
            <p class="about-management-desc">
                <?php esc_html_e( 'Plan Do See developed and operates 17 properties worldwide including 3 award-winning resorts in Japan; 17 restaurants of diverse cuisines in cities including New York, Miami and Los Angeles; and other countries including Japan, Indonesia, Malaysia and Bali.', 'jobscout' ); ?>
            </p>
            <p class="about-management-desc">
                <?php esc_html_e( 'Each venue carries its own concept and design. Many of them are originally historical landmarks that were loved by the local people.', 'jobscout' ); ?>
            </p>
        </div>
    </div>
</section>

<!-- 4. Company Profile / Key Facts -->
<section class="about-company-section">
    <div class="container">
        <div class="about-company-grid">
            <div class="about-company-facts">
                <div class="company-fact-item">
                    <h4 class="fact-label"><?php esc_html_e( 'Established since', 'jobscout' ); ?></h4>
                    <p class="fact-value"><?php esc_html_e( 'April 1993', 'jobscout' ); ?></p>
                </div>

                <div class="company-fact-item">
                    <h4 class="fact-label"><?php esc_html_e( 'Head Office', 'jobscout' ); ?></h4>
                    <p class="fact-value"><?php esc_html_e( 'Marunouchi 2-1-1, Chiyoda, Tokyo', 'jobscout' ); ?></p>
                </div>

                <div class="company-fact-item">
                    <h4 class="fact-label"><?php esc_html_e( 'Capital', 'jobscout' ); ?></h4>
                    <p class="fact-value"><?php esc_html_e( '100,000,000 JPY', 'jobscout' ); ?></p>
                </div>

                <div class="company-fact-item">
                    <h4 class="fact-label"><?php esc_html_e( 'CEO', 'jobscout' ); ?></h4>
                    <p class="fact-value"><?php esc_html_e( 'Yutaka Noda', 'jobscout' ); ?></p>
                </div>

                <div class="company-fact-item">
                    <h4 class="fact-label"><?php esc_html_e( 'Number of Employees', 'jobscout' ); ?></h4>
                    <p class="fact-value"><?php esc_html_e( 'Full time: 830 / Total: 1,600', 'jobscout' ); ?></p>
                </div>
            </div>

            <div class="about-company-media">
                <img src="<?php echo esc_url( $company_img ); ?>" alt="<?php esc_attr_e( 'Tokyo Head Office', 'jobscout' ); ?>" loading="lazy" />
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
