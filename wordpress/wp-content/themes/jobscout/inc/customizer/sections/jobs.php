<?php
/**
 * Jobs Page Customizer Settings & Selective Refresh Partials
 *
 * Allows customization of the Jobs Page (Hero Banner Image, Banner Title, All Jobs Title)
 * via WordPress Customizer with blue pencil edit icons (selective refresh) and instant live preview.
 *
 * @package JobScout
 */

if ( ! function_exists( 'jobscout_customize_register_jobs_page' ) ) :

function jobscout_customize_register_jobs_page( $wp_customize ) {

    /**
     * Panel: Jobs Page Settings
     */
    $wp_customize->add_panel(
        'jobs_page_panel',
        array(
            'title'       => __( 'Jobs Page Settings', 'jobscout' ),
            'priority'    => 41,
            'capability'  => 'edit_theme_options',
            'description' => __( 'Customize elements on the Jobs Page: Banner Title, Background Image, and Heading.', 'jobscout' ),
        )
    );

    /* ==========================================================
     * 1. Section: Jobs Hero Banner
     * ========================================================== */
    $wp_customize->add_section(
        'jobs_banner_section',
        array(
            'title'    => __( '1. Hero Banner Settings', 'jobscout' ),
            'priority' => 10,
            'panel'    => 'jobs_page_panel',
        )
    );

    // Banner Title
    $wp_customize->add_setting(
        'jobs_banner_title',
        array(
            'default'           => __( 'CAREER WITH US', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'jobs_banner_title',
        array(
            'label'       => __( 'Banner Title', 'jobscout' ),
            'description' => __( 'Tiêu đề hiển thị giữa banner trang Jobs', 'jobscout' ),
            'section'     => 'jobs_banner_section',
            'type'        => 'text',
        )
    );

    // Banner Background Image
    $wp_customize->add_setting(
        'jobs_banner_bg',
        array(
            'default'           => get_template_directory_uri() . '/images/career-banner.jpg',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'jobs_banner_bg',
            array(
                'label'       => __( 'Banner Background Image', 'jobscout' ),
                'description' => __( 'Chọn hình nền cho Banner trang Jobs', 'jobscout' ),
                'section'     => 'jobs_banner_section',
            )
        )
    );

    /* ==========================================================
     * 2. Section: Jobs Content Section
     * ========================================================== */
    $wp_customize->add_section(
        'jobs_content_section',
        array(
            'title'    => __( '2. Jobs Content Heading', 'jobscout' ),
            'priority' => 20,
            'panel'    => 'jobs_page_panel',
        )
    );

    // Section Heading (ALL JOBS)
    $wp_customize->add_setting(
        'jobs_section_heading',
        array(
            'default'           => __( 'ALL JOBS', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'jobs_section_heading',
        array(
            'label'       => __( 'Section Heading', 'jobscout' ),
            'description' => __( 'Tiêu đề danh sách việc làm (mặc định: ALL JOBS)', 'jobscout' ),
            'section'     => 'jobs_content_section',
            'type'        => 'text',
        )
    );

    /* ==========================================================
     * Selective Refresh Partials (Hiển thị icon bút chì xanh)
     * ========================================================== */
    if ( isset( $wp_customize->selective_refresh ) ) {
        // Icon bút chì cho tiêu đề Banner "CAREER WITH US"
        $wp_customize->selective_refresh->add_partial(
            'jobs_banner_title',
            array(
                'selector'            => '.jobs-banner-title',
                'render_callback'     => 'jobscout_get_jobs_banner_title',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );

        // Icon bút chì cho ảnh nền Banner
        $wp_customize->selective_refresh->add_partial(
            'jobs_banner_bg',
            array(
                'selector'            => '.jobs-banner-section',
                'render_callback'     => '__return_empty_string',
                'container_inclusive' => false,
                'fallback_refresh'    => true,
            )
        );

        // Icon bút chì cho tiêu đề "ALL JOBS"
        $wp_customize->selective_refresh->add_partial(
            'jobs_section_heading',
            array(
                'selector'            => '.jobs-section-title',
                'render_callback'     => 'jobscout_get_jobs_section_heading',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );
    }
}
endif;
add_action( 'customize_register', 'jobscout_customize_register_jobs_page' );
