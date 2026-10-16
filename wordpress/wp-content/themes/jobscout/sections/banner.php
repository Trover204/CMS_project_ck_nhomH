<?php
/**
 * Banner Section - FIND YOUR DREAM JOBS
 * 
 * @package JobScout
 */

$ed_banner         = get_theme_mod( 'ed_banner_section', true );
$banner_title      = get_theme_mod( 'banner_title', __( 'FIND YOUR DREAM JOBS', 'jobscout' ) );
$banner_subtitle   = get_theme_mod( 'banner_subtitle', __( 'The secret behind our company is simple: to always put ourselves in the other person\'s shoes-employee, guest or conference. This allows us to see the world through their eyes, anticipate their needs and better understand their feelings.', 'jobscout' ) );
$find_a_job_link   = get_option( 'job_manager_jobs_page_id', 0 );
        
if( $ed_banner ){ ?>
    <div id="banner-section" class="site-banner pds-home-banner<?php if( has_header_video() ) echo esc_attr( ' video-banner' ); ?>">
        <div class="item">
            <?php 
            if( has_custom_header() ){
                the_custom_header_markup();
            } else {
                echo '<img src="' . esc_url( get_template_directory_uri() . '/images/banner-image.jpg' ) . '" alt="' . esc_attr( $banner_title ) . '" />';
            }
            ?>
            <div class="banner-caption">
                <div class="container">
                    <div class="caption-inner">
                        <?php 
                            if( $banner_title ) echo '<h2 class="title">' . esc_html( $banner_title ) . '</h2>';
                            if( $banner_subtitle ) echo '<div class="description">' . wpautop( wp_kses_post( $banner_subtitle ) ) . '</div>';
                        ?>
                        <div class="form-wrap">
                            <div class="search-filter-wrap">
                            <?php 
                                get_template_part('template-parts/header','form');
                            ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}