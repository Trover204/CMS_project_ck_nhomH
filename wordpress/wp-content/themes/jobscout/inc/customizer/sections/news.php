<?php
/**
 * News Page Customizer Settings & Selective Refresh Partials
 *
 * Allows full customization of the News Page, News Cards, News Detail Page,
 * Newsletter Section, and PDS Footer via WordPress Customizer (with blue pencil icons)
 * and synchronizes changes two-way with WP Admin Dashboard -> Posts.
 *
 * @package JobScout
 */

if ( ! function_exists( 'jobscout_customize_register_news_page' ) ) :

function jobscout_customize_register_news_page( $wp_customize ) {

    /**
     * Panel: News Page Settings
     */
    $wp_customize->add_panel(
        'news_page_panel',
        array(
            'title'       => __( 'News Page Settings', 'jobscout' ),
            'priority'    => 42,
            'capability'  => 'edit_theme_options',
            'description' => __( 'Customize all elements on the News Page, Blog Cards, News Detail Page, Newsletter, and Footer.', 'jobscout' ),
        )
    );

    /* ==========================================================
     * 1. Section: News Hero Banner
     * ========================================================== */
    $wp_customize->add_section(
        'news_banner_section',
        array(
            'title'    => __( '1. Hero Banner Settings', 'jobscout' ),
            'priority' => 10,
            'panel'    => 'news_page_panel',
        )
    );

    // Banner Title
    $wp_customize->add_setting(
        'news_banner_title',
        array(
            'default'           => __( 'PDS NEWS', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'news_banner_title',
        array(
            'label'   => __( 'Banner Title', 'jobscout' ),
            'section' => 'news_banner_section',
            'type'    => 'text',
        )
    );

    // Banner Background Image
    $wp_customize->add_setting(
        'news_banner_bg',
        array(
            'default'           => get_template_directory_uri() . '/images/view-tokyo-cityscape-night-japan.jpg',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'news_banner_bg',
            array(
                'label'       => __( 'Banner Background Image', 'jobscout' ),
                'description' => __( 'Choose the background image for the News Page hero banner.', 'jobscout' ),
                'section'     => 'news_banner_section',
            )
        )
    );

    if ( isset( $wp_customize->selective_refresh ) ) {
        $wp_customize->selective_refresh->add_partial(
            'news_banner_title',
            array(
                'selector'            => '.news-banner-title',
                'render_callback'     => 'jobscout_get_news_banner_title',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );

        $wp_customize->selective_refresh->add_partial(
            'news_banner_bg',
            array(
                'selector'            => '.news-banner-bg-shortcut',
                'render_callback'     => '__return_empty_string',
                'container_inclusive' => false,
                'fallback_refresh'    => true,
            )
        );
    }

    /* ==========================================================
     * 2. Section: News Content & Blog Grid
     * ========================================================== */
    $wp_customize->add_section(
        'news_grid_section',
        array(
            'title'    => __( '2. News Content & Grid Settings', 'jobscout' ),
            'priority' => 20,
            'panel'    => 'news_page_panel',
        )
    );

    // Section Heading
    $wp_customize->add_setting(
        'news_section_heading',
        array(
            'default'           => __( 'NEWEST BLOG ENTRIES', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'news_section_heading',
        array(
            'label'   => __( 'Section Heading', 'jobscout' ),
            'section' => 'news_grid_section',
            'type'    => 'text',
        )
    );

    // Posts Per Page
    $wp_customize->add_setting(
        'news_posts_per_page',
        array(
            'default'           => 8,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'news_posts_per_page',
        array(
            'label'       => __( 'Number of Posts Per Page', 'jobscout' ),
            'description' => __( 'How many blog posts to display per page on the News page.', 'jobscout' ),
            'section'     => 'news_grid_section',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1,
                'max'  => 24,
                'step' => 1,
            ),
        )
    );

    // Sort Order
    $wp_customize->add_setting(
        'news_posts_orderby',
        array(
            'default'           => 'date-desc',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'news_posts_orderby',
        array(
            'label'   => __( 'Posts Sort Order', 'jobscout' ),
            'section' => 'news_grid_section',
            'type'    => 'select',
            'choices' => array(
                'date-desc'  => __( 'Newest First (Date DESC)', 'jobscout' ),
                'date-asc'   => __( 'Oldest First (Date ASC)', 'jobscout' ),
                'title-asc'  => __( 'Title (A - Z)', 'jobscout' ),
                'title-desc' => __( 'Title (Z - A)', 'jobscout' ),
            ),
        )
    );

    // Excerpt Word Length
    $wp_customize->add_setting(
        'news_excerpt_length',
        array(
            'default'           => 18,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        )
    );

    $wp_customize->add_control(
        'news_excerpt_length',
        array(
            'label'       => __( 'Excerpt Length (Words)', 'jobscout' ),
            'section'     => 'news_grid_section',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 5,
                'max'  => 60,
                'step' => 1,
            ),
        )
    );

    // Read More Button Label
    $wp_customize->add_setting(
        'news_readmore_text',
        array(
            'default'           => __( 'Read More', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'news_readmore_text',
        array(
            'label'   => __( 'Card "Read More" Text', 'jobscout' ),
            'section' => 'news_grid_section',
            'type'    => 'text',
        )
    );

    if ( isset( $wp_customize->selective_refresh ) ) {
        $wp_customize->selective_refresh->add_partial(
            'news_section_heading',
            array(
                'selector'            => '.news-section-title',
                'render_callback'     => 'jobscout_get_news_section_heading',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );

        $wp_customize->selective_refresh->add_partial(
            'news_posts_per_page',
            array(
                'selector'            => '.news-grid-shortcut',
                'render_callback'     => '__return_empty_string',
                'container_inclusive' => false,
                'fallback_refresh'    => true,
            )
        );

        $wp_customize->selective_refresh->add_partial(
            'news_readmore_text',
            array(
                'selector'            => '.news-card-action .news-card-readmore',
                'render_callback'     => 'jobscout_get_news_readmore_text',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );
    }

    /* ==========================================================
     * 3. Section: Individual News Post Cards & Detail Content (Direct Edit)
     * ========================================================== */
    $wp_customize->add_section(
        'news_cards_section',
        array(
            'title'       => __( '3. Edit Individual News Posts', 'jobscout' ),
            'priority'    => 30,
            'panel'       => 'news_page_panel',
            'description' => __( 'Click any blue pencil icon on a News Card or News Detail Page to customize its Image, Title, Date, Category Badge, Location Badge, Excerpt, and Full Content. Saving also updates the Post in WordPress Admin -> Posts.', 'jobscout' ),
        )
    );

    $news_posts = get_posts(
        array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 20,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );

    $default_images = jobscout_get_news_default_post_images();

    foreach ( $news_posts as $index => $post_item ) {
        $pid       = $post_item->ID;
        $num       = $index + 1;
        $def_thumb = get_the_post_thumbnail_url( $pid, 'jobscout-blog' );
        if ( ! $def_thumb || strpos( $def_thumb, 'banner-image.jpg' ) !== false ) {
            $def_thumb = isset( $default_images[ $pid ] ) ? $default_images[ $pid ] : ( $def_thumb ? $def_thumb : get_template_directory_uri() . '/images/banner-image.jpg' );
        }
        $raw_excerpt = has_excerpt( $pid ) ? $post_item->post_excerpt : wp_trim_words( strip_shortcodes( wp_strip_all_tags( $post_item->post_content ) ), 18, '...' );

        // Default Category Badge
        $def_cat    = get_post_meta( $pid, '_news_category_label', true );
        if ( empty( $def_cat ) ) {
            $def_cat = 'Category Name';
            $cats    = get_the_category( $pid );
            if ( ! empty( $cats ) ) {
                foreach ( $cats as $c ) {
                    if ( ! in_array( $c->slug, array( 'uncategorized', 'chua-phan-loai' ), true ) ) {
                        $def_cat = $c->name;
                        break;
                    }
                }
            }
        }

        // Default Location Badge
        $def_loc = get_post_meta( $pid, 'location', true );
        if ( empty( $def_loc ) ) {
            $def_loc = 'Ho Chi Minh City';
        }

        // Default Date Display
        $def_date = get_post_meta( $pid, '_news_display_date', true );
        if ( empty( $def_date ) ) {
            $def_date = get_the_date( 'M d, Y', $pid );
        }

        // 1. Post Title
        $wp_customize->add_setting(
            'news_post_title_' . $pid,
            array(
                'default'           => $post_item->post_title,
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'news_post_title_' . $pid,
            array(
                'label'   => sprintf( __( 'Post #%1$d Title (ID: %2$d)', 'jobscout' ), $num, $pid ),
                'section' => 'news_cards_section',
                'type'    => 'text',
            )
        );

        // 2. Post Thumbnail Image
        $wp_customize->add_setting(
            'news_post_thumb_' . $pid,
            array(
                'default'           => $def_thumb,
                'sanitize_callback' => 'esc_url_raw',
                'transport'         => 'refresh',
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize,
                'news_post_thumb_' . $pid,
                array(
                    'label'   => sprintf( __( 'Post #%1$d Image (ID: %2$d)', 'jobscout' ), $num, $pid ),
                    'section' => 'news_cards_section',
                )
            )
        );

        // 3. Post Date Display (News Detail)
        $wp_customize->add_setting(
            'news_post_date_' . $pid,
            array(
                'default'           => $def_date,
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'news_post_date_' . $pid,
            array(
                'label'       => sprintf( __( 'Post #%1$d Date (News Detail)', 'jobscout' ), $num ),
                'description' => __( 'Example: Oct 14, 2026', 'jobscout' ),
                'section'     => 'news_cards_section',
                'type'        => 'text',
            )
        );

        // 4. Post Category Badge (News Detail)
        $wp_customize->add_setting(
            'news_post_category_' . $pid,
            array(
                'default'           => $def_cat,
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'news_post_category_' . $pid,
            array(
                'label'   => sprintf( __( 'Post #%1$d Category Badge (News Detail)', 'jobscout' ), $num ),
                'section' => 'news_cards_section',
                'type'    => 'text',
            )
        );

        // 5. Post Location Badge (News Detail)
        $wp_customize->add_setting(
            'news_post_location_' . $pid,
            array(
                'default'           => $def_loc,
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'news_post_location_' . $pid,
            array(
                'label'   => sprintf( __( 'Post #%1$d Location Badge (News Detail)', 'jobscout' ), $num ),
                'section' => 'news_cards_section',
                'type'    => 'text',
            )
        );

        // 6. Post Excerpt (News Grid Card)
        $wp_customize->add_setting(
            'news_post_excerpt_' . $pid,
            array(
                'default'           => $raw_excerpt,
                'sanitize_callback' => 'sanitize_textarea_field',
                'transport'         => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'news_post_excerpt_' . $pid,
            array(
                'label'   => sprintf( __( 'Post #%1$d Card Excerpt (ID: %2$d)', 'jobscout' ), $num, $pid ),
                'section' => 'news_cards_section',
                'type'    => 'textarea',
            )
        );

        // 7. Post Full Content (News Detail Article Body)
        $wp_customize->add_setting(
            'news_post_content_' . $pid,
            array(
                'default'           => $post_item->post_content,
                'sanitize_callback' => 'wp_kses_post',
                'transport'         => 'postMessage',
            )
        );

        $wp_customize->add_control(
            'news_post_content_' . $pid,
            array(
                'label'       => sprintf( __( 'Post #%1$d Detail Content (ID: %2$d)', 'jobscout' ), $num, $pid ),
                'description' => __( 'Full article content displayed on the News Detail page (supports text & HTML, synced two-way with WP Admin Posts).', 'jobscout' ),
                'section'     => 'news_cards_section',
                'type'        => 'textarea',
            )
        );

        // 8. Optional Content Inline Image 1
        $wp_customize->add_setting(
            'news_post_content_img1_' . $pid,
            array(
                'default'           => get_post_meta( $pid, '_news_content_img1', true ),
                'sanitize_callback' => 'esc_url_raw',
                'transport'         => 'refresh',
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize,
                'news_post_content_img1_' . $pid,
                array(
                    'label'       => sprintf( __( 'Post #%1$d Content Image 1 (Optional)', 'jobscout' ), $num ),
                    'description' => __( 'Optional inline image displayed inside the article content.', 'jobscout' ),
                    'section'     => 'news_cards_section',
                )
            )
        );

        // 9. Optional Content Inline Image 2
        $wp_customize->add_setting(
            'news_post_content_img2_' . $pid,
            array(
                'default'           => get_post_meta( $pid, '_news_content_img2', true ),
                'sanitize_callback' => 'esc_url_raw',
                'transport'         => 'refresh',
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize,
                'news_post_content_img2_' . $pid,
                array(
                    'label'       => sprintf( __( 'Post #%1$d Content Image 2 (Optional)', 'jobscout' ), $num ),
                    'description' => __( 'Second optional inline image displayed inside the article content.', 'jobscout' ),
                    'section'     => 'news_cards_section',
                )
            )
        );

        if ( isset( $wp_customize->selective_refresh ) ) {
            $wp_customize->selective_refresh->add_partial(
                'news_post_thumb_' . $pid,
                array(
                    'selector'            => '#post-' . $pid . ' .news-card-thumb, .post-id-' . $pid . '-detail-thumb',
                    'render_callback'     => function() use ( $pid ) {
                        $url = jobscout_get_news_post_thumb_url( $pid );
                        return '<a href="' . esc_url( get_permalink( $pid ) ) . '"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( jobscout_get_news_post_title( $pid ) ) . '" loading="lazy" /></a>';
                    },
                    'container_inclusive' => false,
                    'fallback_refresh'    => true,
                )
            );

            $wp_customize->selective_refresh->add_partial(
                'news_post_title_' . $pid,
                array(
                    'selector'            => '#post-' . $pid . ' .news-card-title a, .post-id-' . $pid . '-detail-title',
                    'render_callback'     => function() use ( $pid ) {
                        return esc_html( jobscout_get_news_post_title( $pid ) );
                    },
                    'container_inclusive' => false,
                    'fallback_refresh'    => false,
                )
            );

            $wp_customize->selective_refresh->add_partial(
                'news_post_date_' . $pid,
                array(
                    'selector'            => '.post-id-' . $pid . '-detail-date',
                    'render_callback'     => function() use ( $pid ) {
                        return esc_html( jobscout_get_news_post_date_display( $pid ) );
                    },
                    'container_inclusive' => false,
                    'fallback_refresh'    => false,
                )
            );

            $wp_customize->selective_refresh->add_partial(
                'news_post_category_' . $pid,
                array(
                    'selector'            => '.post-id-' . $pid . '-category-badge',
                    'render_callback'     => function() use ( $pid ) {
                        return esc_html( jobscout_get_news_post_category( $pid ) );
                    },
                    'container_inclusive' => false,
                    'fallback_refresh'    => false,
                )
            );

            $wp_customize->selective_refresh->add_partial(
                'news_post_location_' . $pid,
                array(
                    'selector'            => '.post-id-' . $pid . '-location-badge',
                    'render_callback'     => function() use ( $pid ) {
                        return esc_html( jobscout_get_news_post_location( $pid ) );
                    },
                    'container_inclusive' => false,
                    'fallback_refresh'    => false,
                )
            );

            $wp_customize->selective_refresh->add_partial(
                'news_post_excerpt_' . $pid,
                array(
                    'selector'            => '#post-' . $pid . ' .news-card-excerpt-link',
                    'render_callback'     => function() use ( $pid ) {
                        return esc_html( jobscout_get_news_post_excerpt( $pid ) );
                    },
                    'container_inclusive' => false,
                    'fallback_refresh'    => false,
                )
            );

            $wp_customize->selective_refresh->add_partial(
                'news_post_content_' . $pid,
                array(
                    'selector'            => '.post-id-' . $pid . '-detail-content',
                    'render_callback'     => function() use ( $pid ) {
                        return jobscout_get_news_post_content( $pid );
                    },
                    'container_inclusive' => false,
                    'fallback_refresh'    => false,
                )
            );
        }
    }

    /* ==========================================================
     * 4. Section: News Detail Page Settings (single.php)
     * ========================================================== */
    $wp_customize->add_section(
        'news_detail_customizer_section',
        array(
            'title'    => __( '4. News Detail Page Settings', 'jobscout' ),
            'priority' => 40,
            'panel'    => 'news_page_panel',
        )
    );

    $wp_customize->add_setting(
        'news_breadcrumb_home',
        array(
            'default'           => __( 'Home', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'news_breadcrumb_home',
        array(
            'label'   => __( 'Breadcrumb: Home Text', 'jobscout' ),
            'section' => 'news_detail_customizer_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'news_breadcrumb_all',
        array(
            'default'           => __( 'All News', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'news_breadcrumb_all',
        array(
            'label'   => __( 'Breadcrumb: All News Text', 'jobscout' ),
            'section' => 'news_detail_customizer_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'news_breadcrumb_detail',
        array(
            'default'           => __( 'News Detail', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'news_breadcrumb_detail',
        array(
            'label'   => __( 'Breadcrumb: Current Detail Text', 'jobscout' ),
            'section' => 'news_detail_customizer_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'news_detail_posted_prefix',
        array(
            'default'           => 'Posted: ',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        )
    );
    $wp_customize->add_control(
        'news_detail_posted_prefix',
        array(
            'label'   => __( 'Date Prefix Text (e.g. "Posted: ")', 'jobscout' ),
            'section' => 'news_detail_customizer_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'news_detail_share_label',
        array(
            'default'           => __( 'SHARE', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'news_detail_share_label',
        array(
            'label'   => __( 'Share Button Text', 'jobscout' ),
            'section' => 'news_detail_customizer_section',
            'type'    => 'text',
        )
    );

    $wp_customize->add_setting(
        'news_detail_newest_count',
        array(
            'default'           => 6,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        )
    );
    $wp_customize->add_control(
        'news_detail_newest_count',
        array(
            'label'       => __( 'Number of Newest Blog Entries on Detail Page', 'jobscout' ),
            'section'     => 'news_detail_customizer_section',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1,
                'max'  => 12,
                'step' => 1,
            ),
        )
    );

    if ( isset( $wp_customize->selective_refresh ) ) {
        $wp_customize->selective_refresh->add_partial(
            'news_breadcrumb_home',
            array(
                'selector'            => '.breadcrumb-home-link',
                'render_callback'     => 'jobscout_get_news_breadcrumb_home',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );

        $wp_customize->selective_refresh->add_partial(
            'news_breadcrumb_all',
            array(
                'selector'            => '.breadcrumb-all-link',
                'render_callback'     => 'jobscout_get_news_breadcrumb_all',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );

        $wp_customize->selective_refresh->add_partial(
            'news_breadcrumb_detail',
            array(
                'selector'            => '.breadcrumb-detail-text',
                'render_callback'     => 'jobscout_get_news_breadcrumb_detail',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );

        $wp_customize->selective_refresh->add_partial(
            'news_detail_share_label',
            array(
                'selector'            => '.news-detail-share-wrap',
                'render_callback'     => function() {
                    $label = jobscout_get_news_detail_share_label();
                    return '<button type="button" class="news-detail-share-btn" onclick="if(event.target.closest(\'.customize-partial-edit-shortcut\'))return;if(window.jobscoutOpenNewsShareModal){window.jobscoutOpenNewsShareModal();}"><span class="news-detail-share-text">' . $label . '</span></button>';
                },
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );

        $wp_customize->selective_refresh->add_partial(
            'news_detail_newest_count',
            array(
                'selector'            => '.news-detail-newest-grid-shortcut',
                'render_callback'     => '__return_empty_string',
                'container_inclusive' => false,
                'fallback_refresh'    => true,
            )
        );
    }

    /* ==========================================================
     * 5. Section: Newsletter & PDS Footer Settings
     * ========================================================== */
    $wp_customize->add_section(
        'news_newsletter_customizer_section',
        array(
            'title'    => __( '5. Newsletter & Footer Settings', 'jobscout' ),
            'priority' => 50,
            'panel'    => 'news_page_panel',
        )
    );

    // Newsletter Line 1
    $wp_customize->add_setting(
        'news_newsletter_line1',
        array(
            'default'           => __( 'Subscribe To', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'news_newsletter_line1',
        array(
            'label'   => __( 'Newsletter Title - Line 1', 'jobscout' ),
            'section' => 'news_newsletter_customizer_section',
            'type'    => 'text',
        )
    );

    // Newsletter Line 2
    $wp_customize->add_setting(
        'news_newsletter_line2',
        array(
            'default'           => __( 'Our Newsletter', 'jobscout' ),
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'news_newsletter_line2',
        array(
            'label'   => __( 'Newsletter Title - Line 2', 'jobscout' ),
            'section' => 'news_newsletter_customizer_section',
            'type'    => 'text',
        )
    );

    // Newsletter Email Placeholder
    $wp_customize->add_setting(
        'news_newsletter_placeholder',
        array(
            'default'           => 'Input your email address',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'news_newsletter_placeholder',
        array(
            'label'   => __( 'Newsletter Email Placeholder', 'jobscout' ),
            'section' => 'news_newsletter_customizer_section',
            'type'    => 'text',
        )
    );

    // Newsletter Button Text
    $wp_customize->add_setting(
        'news_newsletter_btn_text',
        array(
            'default'           => 'SUBSCRIBE',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );
    $wp_customize->add_control(
        'news_newsletter_btn_text',
        array(
            'label'   => __( 'Newsletter Subscribe Button Text', 'jobscout' ),
            'section' => 'news_newsletter_customizer_section',
            'type'    => 'text',
        )
    );

    // Contact Form 7 ID
    $wp_customize->add_setting(
        'news_newsletter_cf7_id',
        array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        )
    );
    $wp_customize->add_control(
        'news_newsletter_cf7_id',
        array(
            'label'       => __( 'Contact Form 7 ID (Optional)', 'jobscout' ),
            'description' => __( 'Leave 0 to auto-detect form titled "newsletter" or use the built-in newsletter form.', 'jobscout' ),
            'section'     => 'news_newsletter_customizer_section',
            'type'        => 'number',
        )
    );

    // Footer Social URLs
    $social_defaults = array(
        'pds_social_fb'   => array( 'label' => __( 'Footer Facebook URL', 'jobscout' ), 'default' => 'https://facebook.com' ),
        'pds_social_gg'   => array( 'label' => __( 'Footer Google URL', 'jobscout' ), 'default' => 'https://google.com' ),
        'pds_social_line' => array( 'label' => __( 'Footer Line URL', 'jobscout' ), 'default' => 'https://line.me' ),
        'pds_social_tw'   => array( 'label' => __( 'Footer Twitter URL', 'jobscout' ), 'default' => 'https://twitter.com' ),
    );

    foreach ( $social_defaults as $mod_key => $meta ) {
        $wp_customize->add_setting(
            $mod_key,
            array(
                'default'           => $meta['default'],
                'sanitize_callback' => 'esc_url_raw',
                'transport'         => 'refresh',
            )
        );
        $wp_customize->add_control(
            $mod_key,
            array(
                'label'   => $meta['label'],
                'section' => 'news_newsletter_customizer_section',
                'type'    => 'url',
            )
        );
    }

    if ( isset( $wp_customize->selective_refresh ) ) {
        $wp_customize->selective_refresh->add_partial(
            'news_newsletter_line1',
            array(
                'selector'            => '.news-newsletter-line1',
                'render_callback'     => 'jobscout_get_news_newsletter_line1',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );

        $wp_customize->selective_refresh->add_partial(
            'news_newsletter_line2',
            array(
                'selector'            => '.news-newsletter-line2',
                'render_callback'     => 'jobscout_get_news_newsletter_line2',
                'container_inclusive' => false,
                'fallback_refresh'    => false,
            )
        );

        $wp_customize->selective_refresh->add_partial(
            'news_newsletter_btn_text',
            array(
                'selector'            => '.news-newsletter-form-wrap',
                'render_callback'     => '__return_false',
                'container_inclusive' => false,
                'fallback_refresh'    => true,
            )
        );

        $wp_customize->selective_refresh->add_partial(
            'pds_social_fb',
            array(
                'selector'            => '.pds-footer-socials',
                'render_callback'     => '__return_false',
                'container_inclusive' => false,
                'fallback_refresh'    => true,
            )
        );
    }
}
endif;
add_action( 'customize_register', 'jobscout_customize_register_news_page' );

/**
 * Sync Customizer post edits back to WordPress Posts when Customizer is saved/published
 */
if ( ! function_exists( 'jobscout_sync_customizer_news_posts_to_db' ) ) :
function jobscout_sync_customizer_news_posts_to_db() {
    $news_posts = get_posts(
        array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 20,
        )
    );

    remove_action( 'save_post_post', 'jobscout_sync_db_post_to_customizer', 20 );

    foreach ( $news_posts as $post_item ) {
        $pid         = $post_item->ID;
        $mod_title   = get_theme_mod( 'news_post_title_' . $pid, null );
        $mod_exc     = get_theme_mod( 'news_post_excerpt_' . $pid, null );
        $mod_content = get_theme_mod( 'news_post_content_' . $pid, null );
        $mod_thumb   = get_theme_mod( 'news_post_thumb_' . $pid, null );
        $mod_cat     = get_theme_mod( 'news_post_category_' . $pid, null );
        $mod_loc     = get_theme_mod( 'news_post_location_' . $pid, null );
        $mod_date    = get_theme_mod( 'news_post_date_' . $pid, null );
        $mod_img1    = get_theme_mod( 'news_post_content_img1_' . $pid, null );
        $mod_img2    = get_theme_mod( 'news_post_content_img2_' . $pid, null );

        $update_args  = array( 'ID' => $pid );
        $needs_update = false;

        if ( null !== $mod_title && '' !== trim( $mod_title ) && $mod_title !== $post_item->post_title ) {
            $update_args['post_title'] = sanitize_text_field( $mod_title );
            $needs_update = true;
        }

        if ( null !== $mod_exc && '' !== trim( $mod_exc ) && $mod_exc !== $post_item->post_excerpt ) {
            $update_args['post_excerpt'] = sanitize_textarea_field( $mod_exc );
            $needs_update = true;
        }

        if ( null !== $mod_content && '' !== trim( $mod_content ) && $mod_content !== $post_item->post_content ) {
            $update_args['post_content'] = wp_kses_post( $mod_content );
            $needs_update = true;
        }

        if ( $needs_update ) {
            wp_update_post( $update_args );
        }

        if ( null !== $mod_thumb && '' !== trim( $mod_thumb ) ) {
            update_post_meta( $pid, '_news_custom_thumb', esc_url_raw( $mod_thumb ) );
            $att_id = attachment_url_to_postid( $mod_thumb );
            if ( $att_id ) {
                set_post_thumbnail( $pid, $att_id );
            }
        }

        if ( null !== $mod_cat && '' !== trim( $mod_cat ) ) {
            $clean_cat = sanitize_text_field( $mod_cat );
            update_post_meta( $pid, '_news_category_label', $clean_cat );
            if ( 'Category Name' !== $clean_cat ) {
                $term = term_exists( $clean_cat, 'category' );
                if ( ! $term ) {
                    $term = wp_insert_term( $clean_cat, 'category' );
                }
                if ( ! is_wp_error( $term ) && isset( $term['term_id'] ) ) {
                    wp_set_post_categories( $pid, array( (int) $term['term_id'] ), false );
                }
            }
        }

        if ( null !== $mod_loc && '' !== trim( $mod_loc ) ) {
            update_post_meta( $pid, 'location', sanitize_text_field( $mod_loc ) );
        }

        if ( null !== $mod_date && '' !== trim( $mod_date ) ) {
            update_post_meta( $pid, '_news_display_date', sanitize_text_field( $mod_date ) );
        }

        if ( null !== $mod_img1 ) {
            update_post_meta( $pid, '_news_content_img1', esc_url_raw( $mod_img1 ) );
        }

        if ( null !== $mod_img2 ) {
            update_post_meta( $pid, '_news_content_img2', esc_url_raw( $mod_img2 ) );
        }
    }

    add_action( 'save_post_post', 'jobscout_sync_db_post_to_customizer', 20, 3 );
}
endif;
add_action( 'customize_save_after', 'jobscout_sync_customizer_news_posts_to_db' );

/**
 * Sync WP Admin Post edits to Customizer theme_mods so Admin Dashboard edits always take effect
 */
if ( ! function_exists( 'jobscout_sync_db_post_to_customizer' ) ) :
function jobscout_sync_db_post_to_customizer( $post_id, $post, $update ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) || 'publish' !== $post->post_status ) {
        return;
    }

    set_theme_mod( 'news_post_title_' . $post_id, $post->post_title );
    set_theme_mod( 'news_post_content_' . $post_id, $post->post_content );

    if ( ! empty( $post->post_excerpt ) ) {
        set_theme_mod( 'news_post_excerpt_' . $post_id, $post->post_excerpt );
    } else {
        remove_theme_mod( 'news_post_excerpt_' . $post_id );
    }

    $meta_thumb = get_post_meta( $post_id, '_news_custom_thumb', true );
    $feat_thumb = get_the_post_thumbnail_url( $post_id, 'jobscout-blog' );
    if ( ! empty( $meta_thumb ) ) {
        set_theme_mod( 'news_post_thumb_' . $post_id, $meta_thumb );
    } elseif ( ! empty( $feat_thumb ) && strpos( $feat_thumb, 'banner-image.jpg' ) === false ) {
        set_theme_mod( 'news_post_thumb_' . $post_id, $feat_thumb );
    }

    $meta_cat = get_post_meta( $post_id, '_news_category_label', true );
    if ( ! empty( $meta_cat ) ) {
        set_theme_mod( 'news_post_category_' . $post_id, $meta_cat );
    } else {
        $cats = get_the_category( $post_id );
        if ( ! empty( $cats ) ) {
            foreach ( $cats as $c ) {
                if ( ! in_array( $c->slug, array( 'uncategorized', 'chua-phan-loai' ), true ) ) {
                    set_theme_mod( 'news_post_category_' . $post_id, $c->name );
                    break;
                }
            }
        }
    }
}
endif;
add_action( 'save_post_post', 'jobscout_sync_db_post_to_customizer', 20, 3 );
