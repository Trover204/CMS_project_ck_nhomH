<?php
/**
 * Template part: Orange Newsletter Section (powered by Contact Form 7).
 *
 * Form lookup order:
 *  1. Theme mod `news_newsletter_cf7_id` (Contact Form 7 form ID), if set.
 *  2. Contact Form 7 form whose title is "newsletter".
 *
 * Usage: get_template_part( 'template-parts/newsletter-cf7' );
 *
 * @package JobScout
 */

// Contact Form 7 is not active: show a hint to admins only, nothing to visitors.
if ( ! function_exists( 'wpcf7_contact_form' ) ) {
    if ( current_user_can( 'activate_plugins' ) ) {
        echo '<p class="news-newsletter-notice">' . esc_html__( 'Newsletter: please install and activate the Contact Form 7 plugin.', 'jobscout' ) . '</p>';
    }
    return;
}

$jobscout_cf7_form = null;
$jobscout_cf7_id   = absint( get_theme_mod( 'news_newsletter_cf7_id', 0 ) );

if ( $jobscout_cf7_id ) {
    $jobscout_cf7_form = wpcf7_contact_form( $jobscout_cf7_id );
}

if ( ! $jobscout_cf7_form && function_exists( 'wpcf7_get_contact_form_by_title' ) ) {
    $jobscout_cf7_form = wpcf7_get_contact_form_by_title( 'newsletter' );
}

// Form not found: show a hint to admins only.
if ( ! $jobscout_cf7_form ) {
    if ( current_user_can( 'edit_posts' ) ) {
        echo '<p class="news-newsletter-notice">' . esc_html__( 'Newsletter: create a Contact Form 7 form titled "newsletter" (Contact > Add New).', 'jobscout' ) . '</p>';
    }
    return;
}
?>
<!-- Orange Newsletter Section (Contact Form 7) -->
<section class="news-newsletter-section">
    <div class="container">
        <div class="news-newsletter-inner">
            <div class="news-newsletter-title">
                <span><?php echo esc_html( get_theme_mod( 'news_newsletter_line1', 'Subscribe To' ) ); ?></span>
                <span><?php echo esc_html( get_theme_mod( 'news_newsletter_line2', 'Our Newsletter' ) ); ?></span>
            </div>
            <div class="news-newsletter-form-wrap news-newsletter-cf7">
                <?php
                echo do_shortcode(
                    sprintf(
                        '[contact-form-7 id="%1$d" title="%2$s" html_class="news-newsletter-form-cf7"]',
                        (int) $jobscout_cf7_form->id(),
                        esc_attr( $jobscout_cf7_form->title() )
                    )
                );
                ?>
            </div>
        </div>
    </div>
</section>
