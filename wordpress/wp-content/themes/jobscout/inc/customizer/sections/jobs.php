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
            'description' => __( 'Title displayed in the Jobs page banner', 'jobscout' ),
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
                'description' => __( 'Select background image for the Jobs banner', 'jobscout' ),
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
            'description' => __( 'Job listing section heading (default: ALL JOBS)', 'jobscout' ),
            'section'     => 'jobs_content_section',
            'type'        => 'text',
        )
    );

    /* ==========================================================
     * 3. Section: Job Detail & Apply Form Settings
     * ========================================================== */
    $wp_customize->add_section(
        'jobs_apply_modal_section',
        array(
            'title'       => __( '3. Apply Form Settings', 'jobscout' ),
            'priority'    => 30,
            'panel'       => 'jobs_page_panel',
            'description' => __( 'Configure the application form displayed in the modal popup when clicking "APPLY JOB".', 'jobscout' ),
        )
    );

    // Apply Form Shortcode
    $wp_customize->add_setting(
        'jobscout_apply_form_shortcode',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'jobscout_apply_form_shortcode',
        array(
            'label'       => __( 'Default Apply Form Shortcode', 'jobscout' ),
            'description' => __( 'Enter shortcode from Contact Form 7, WPForms, or Fluent Forms. e.g. [contact-form-7 id="123" title="Apply Form"]. Admins can also set a specific shortcode per job on its edit page.', 'jobscout' ),
            'section'     => 'jobs_apply_modal_section',
            'type'        => 'text',
        )
    );

    /* ==========================================================
     * Selective Refresh Partials
     * ========================================================== */
    if ( isset( $wp_customize->selective_refresh ) ) {
        // Pencil icon for Banner title "CAREER WITH US"
        $wp_customize->selective_refresh->add_partial(
            'jobs_banner_title',
            array(
                'selector'            => '.jobs-banner-title',
                'render_callback'     => 'jobscout_get_jobs_banner_title',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );

        // Pencil icon for Banner background image
        $wp_customize->selective_refresh->add_partial(
            'jobs_banner_bg',
            array(
                'selector'            => '.jobs-banner-section',
                'render_callback'     => '__return_empty_string',
                'container_inclusive' => false,
                'fallback_refresh'    => true,
            )
        );

        // Pencil icon for "ALL JOBS" title
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
