<?php
/**
 * JobScout Customizer Partials
 *
 * @package JobScout
 */

/**
 * Render the site title for the selective refresh partial.
 *
 */
function jobscout_customize_partial_blogname() {
	$blog_name = get_bloginfo( 'name' );

    if ( $blog_name ){
        return esc_html( $blog_name );
    } else {
        return false;
    }
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 */
function jobscout_customize_partial_blogdescription() {
	$blog_description = get_bloginfo( 'description' );

    if ( $blog_description ){
        return esc_html( $blog_description );
    } else {
        return false;
    }
}

/*
 * Header post job button
 */
function jobscout_get_header_post_job_label(){
    $btn_label = get_theme_mod( 'post_job_label', __( 'Post Jobs', 'jobscout' ) );

    if ( $btn_label ){
        return esc_html( $btn_label );
    } else {
        return false;
    }
}

/**
 * Banner Section Title
*/
function jobscout_get_banner_title(){
    $banner_title = get_theme_mod( 'banner_title', __( 'Aim Higher, Dream Bigger', 'jobscout' ) );

    if ( $banner_title ){
        return esc_html( $banner_title );
    } else {
        return false;
    }
}

/**
 * Banner Section Sub Title
*/
function jobscout_get_banner_sub_title(){
    $banner_subtitle = get_theme_mod( 'banner_subtitle', __( 'Each month, more than 7 million JobScout turn to website in their search for work, making over 160,000 applications every day.', 'jobscout' ) );

    if ( $banner_subtitle ){
        return wpautop( wp_kses_post( $banner_subtitle ) );
    } else {
        return false;
    }
}

/**
 * Popular Section Title
*/
function jobscout_get_popular_section_title(){
    $popular_section_title = get_theme_mod( 'popular_section_title', __( 'Popular Categories', 'jobscout' ) );

    if ( $popular_section_title ){
        return esc_html( $popular_section_title );
    } else {
        return false;
    }
}

/**
 * Job Posting Section Title
*/
function jobscout_get_job_posting_section_title(){
    $job_posting_section_title = get_theme_mod( 'job_posting_section_title', __( 'Job Posting', 'jobscout' ) );

    if ( $job_posting_section_title ){
        return esc_html( $job_posting_section_title );
    } else {
        return false;
    }
}

/**
 * Blog Section Title
*/
function jobscout_get_blog_section_title(){
    $blog_section_title = get_theme_mod( 'blog_section_title', __( 'Latest Articles', 'jobscout' ) );

    if ( $blog_section_title ){
        return esc_html( $blog_section_title );
    } else {
        return false;
    }
}

/**
 * Blog Section Description
*/
function jobscout_get_blog_section_description(){
    $blog_section_subtitle = get_theme_mod( 'blog_section_subtitle', __( 'We will help you find it. We are your first step to becoming everything you want to be.', 'jobscout' ) );

    if ( $blog_section_subtitle ){
        return wpautop( wp_kses_post( $blog_section_subtitle ) );
    } else {
        return false;
    }
}

/**
 * Blog View All Button
*/
function jobscout_get_blog_view_all_btn(){
    $view_all_btn = get_theme_mod( 'blog_view_all', __( 'Browse All', 'jobscout' ) );
    if ( $view_all_btn ){
        return esc_html( $view_all_btn );
    } else {
        return false;
    }
}


/**
 * Testimonial Section Title
*/
function jobscout_get_testimonial_section_title(){
    $testimonial_section_title = get_theme_mod( 'testimonial_section_title', __( 'Clients Testimonials', 'jobscout' ) );

    if ( $testimonial_section_title ){
        return esc_html( $testimonial_section_title );
    } else {
        return false;
    }
}

/**
 * Testimonial Section Description
*/
function jobscout_get_testimonial_section_description(){
    $testimonial_section_subtitle = get_theme_mod( 'testimonial_section_subtitle', __( 'We will help you find it. We are your first step to becoming everything you want to be.', 'jobscout' ) );

    if ( $testimonial_section_subtitle ){
        return wpautop( wp_kses_post( $testimonial_section_subtitle ) );
    } else {
        return false;
    }
}

/**
 * Display blog readmore button
*/
function jobscout_get_read_more(){
    $read_more_text = get_theme_mod( 'read_more_text', __( 'Read More', 'jobscout' ) ); 

    if( $read_more_text ){
        return esc_html( $read_more_text );
    } else {
        return false;
    }
}

/**
 * Display blog readmore button
*/
function jobscout_get_author_title(){
    $author_title = get_theme_mod( 'author_title', __( 'About Author', 'jobscout' ) );

    if( $author_title ){
        return esc_html( $author_title );
    } else {
        return false;
    }
}

if( ! function_exists( 'jobscout_get_footer_copyright' ) ) :
/**
 * Footer Copyright
*/
function jobscout_get_footer_copyright(){
    $copyright = get_theme_mod( 'footer_copyright' );
    echo '<span class="copyright-text">';
    if( $copyright ){
        echo wp_kses_post( $copyright );
    }else{
        esc_html_e( '&copy; Copyright ', 'jobscout' );
        echo date_i18n( esc_html__( 'Y', 'jobscout' ) );
        echo ' <a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>. ';
        esc_html_e( 'All Rights Reserved. ', 'jobscout' );
    }
    echo '</span>';
}
endif;

if( ! function_exists( 'jobscout_ed_author_link' ) ) :
/**
 * Show/Hide Author link in footer
*/
function jobscout_ed_author_link(){
    echo esc_html__( 'JobScout | Developed By ', 'jobscout' ) . '<a class="author-link" href="' . esc_url( 'https://rarathemes.com/' ) .'" rel="nofollow" target="_blank">' . esc_html__( 'Rara Theme', 'jobscout' ) . '</a>.';
}
endif;

if( ! function_exists( 'jobscout_ed_wp_link' ) ) :
/**
 * Show/Hide WordPress link in footer
*/
function jobscout_ed_wp_link(){
     printf( esc_html__( ' Powered by %s', 'jobscout' ), '<a class="wp-link" href="'. esc_url( __( 'https://wordpress.org/', 'jobscout' ) ) .'" target="_blank">WordPress</a>.' );
}
endif;


/**
 * ==========================================================
 * News Page Customizer Partials & Helper Functions
 * ==========================================================
 */

if ( ! function_exists( 'jobscout_get_news_default_post_images' ) ) :
function jobscout_get_news_default_post_images() {
    $theme_img_dir = get_template_directory_uri() . '/images/';
    return array(
        977 => $theme_img_dir . 'photo-1528360983277-13d401cdc186.jpg',
        979 => $theme_img_dir . 'photo-1493976040374-85c8e12f0c0e.jpg',
        981 => $theme_img_dir . 'photo-1524413840807-0c3cb6fa808d.jpg',
        982 => $theme_img_dir . 'photo-1503899036084-c55cdd92da26.jpg',
    );
}
endif;

if ( ! function_exists( 'jobscout_get_news_post_thumb_url' ) ) :
function jobscout_get_news_post_thumb_url( $post_id ) {
    $post_id = absint( $post_id );
    if ( ! $post_id ) {
        return get_template_directory_uri() . '/images/banner-image.jpg';
    }

    // 1. Check Customizer theme_mod override for this post
    $mod_thumb = get_theme_mod( 'news_post_thumb_' . $post_id, '' );
    if ( ! empty( $mod_thumb ) ) {
        return $mod_thumb;
    }

    // 2. Check custom meta set via Post editor metabox
    $meta_thumb = get_post_meta( $post_id, '_news_custom_thumb', true );
    if ( ! empty( $meta_thumb ) ) {
        return $meta_thumb;
    }

    // 3. Check Featured Image set in WP Admin -> Posts
    $featured_url = get_the_post_thumbnail_url( $post_id, 'jobscout-blog' );
    $default_map  = jobscout_get_news_default_post_images();

    if ( $featured_url && strpos( $featured_url, 'banner-image.jpg' ) === false ) {
        return $featured_url;
    }

    // 4. Fallback to predefined PDS demo images for initial posts
    if ( isset( $default_map[ $post_id ] ) ) {
        return $default_map[ $post_id ];
    }

    if ( $featured_url ) {
        return $featured_url;
    }

    return get_template_directory_uri() . '/images/banner-image.jpg';
}
endif;

if ( ! function_exists( 'jobscout_get_news_post_title' ) ) :
function jobscout_get_news_post_title( $post_id ) {
    $post_id   = absint( $post_id );
    $raw_title = get_the_title( $post_id );
    $mod_title = get_theme_mod( 'news_post_title_' . $post_id, '' );
    if ( '' !== trim( (string) $mod_title ) ) {
        return $mod_title;
    }
    return $raw_title;
}
endif;

if ( ! function_exists( 'jobscout_get_news_post_excerpt' ) ) :
function jobscout_get_news_post_excerpt( $post_id ) {
    $post_id        = absint( $post_id );
    $excerpt_length = absint( get_theme_mod( 'news_excerpt_length', 18 ) );
    if ( $excerpt_length < 1 ) {
        $excerpt_length = 18;
    }

    $mod_excerpt = get_theme_mod( 'news_post_excerpt_' . $post_id, '' );
    if ( '' !== trim( (string) $mod_excerpt ) ) {
        return $mod_excerpt;
    }

    $post_obj = get_post( $post_id );
    if ( ! $post_obj ) {
        return '';
    }

    $raw_excerpt = has_excerpt( $post_id ) ? $post_obj->post_excerpt : $post_obj->post_content;
    $raw_excerpt = strip_shortcodes( wp_strip_all_tags( $raw_excerpt ) );
    return wp_trim_words( $raw_excerpt, $excerpt_length, '...' );
}
endif;

if ( ! function_exists( 'jobscout_get_news_banner_bg_url' ) ) :
function jobscout_get_news_banner_bg_url() {
    $bg = get_theme_mod( 'news_banner_bg', '' );
    if ( ! empty( $bg ) ) {
        return $bg;
    }
    $news_page = get_page_by_path( 'news' );
    if ( $news_page && has_post_thumbnail( $news_page->ID ) ) {
        $page_thumb = get_the_post_thumbnail_url( $news_page->ID, 'full' );
        if ( $page_thumb ) {
            return $page_thumb;
        }
    }
    return get_template_directory_uri() . '/images/view-tokyo-cityscape-night-japan.jpg';
}
endif;

if ( ! function_exists( 'jobscout_get_news_banner_title' ) ) :
function jobscout_get_news_banner_title() {
    return esc_html( get_theme_mod( 'news_banner_title', __( 'PDS NEWS', 'jobscout' ) ) );
}
endif;

if ( ! function_exists( 'jobscout_get_news_section_heading' ) ) :
function jobscout_get_news_section_heading() {
    return esc_html( get_theme_mod( 'news_section_heading', __( 'NEWEST BLOG ENTRIES', 'jobscout' ) ) );
}
endif;

if ( ! function_exists( 'jobscout_get_news_readmore_text' ) ) :
function jobscout_get_news_readmore_text() {
    return esc_html( get_theme_mod( 'news_readmore_text', __( 'Read More', 'jobscout' ) ) );
}
endif;

if ( ! function_exists( 'jobscout_get_news_newsletter_line1' ) ) :
function jobscout_get_news_newsletter_line1() {
    return esc_html( get_theme_mod( 'news_newsletter_line1', __( 'Subscribe To', 'jobscout' ) ) );
}
endif;

if ( ! function_exists( 'jobscout_get_news_newsletter_line2' ) ) :
function jobscout_get_news_newsletter_line2() {
    return esc_html( get_theme_mod( 'news_newsletter_line2', __( 'Our Newsletter', 'jobscout' ) ) );
}
endif;

if ( ! function_exists( 'jobscout_get_news_newsletter_btn_text' ) ) :
function jobscout_get_news_newsletter_btn_text() {
    return esc_html( get_theme_mod( 'news_newsletter_btn_text', __( 'SUBSCRIBE', 'jobscout' ) ) );
}
endif;

if ( ! function_exists( 'jobscout_get_news_detail_share_label' ) ) :
function jobscout_get_news_detail_share_label() {
    return esc_html( get_theme_mod( 'news_detail_share_label', __( 'SHARE', 'jobscout' ) ) );
}
endif;
