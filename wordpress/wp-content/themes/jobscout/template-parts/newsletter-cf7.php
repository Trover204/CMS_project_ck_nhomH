<?php
/**
 * Template part: Orange Newsletter Section (powered by Contact Form 7 with built-in fallback).
 *
 * Form lookup order:
 *  1. Theme mod `news_newsletter_cf7_id` (Contact Form 7 form ID), if set.
 *  2. Contact Form 7 form whose title is "newsletter".
 *  3. Built-in customizable HTML newsletter form fallback.
 *
 * Usage: get_template_part( 'template-parts/newsletter-cf7' );
 *
 * @package JobScout
 */

$jobscout_cf7_form = null;
$jobscout_cf7_id   = absint( get_theme_mod( 'news_newsletter_cf7_id', 0 ) );

if ( function_exists( 'wpcf7_contact_form' ) ) {
    if ( $jobscout_cf7_id ) {
        $jobscout_cf7_form = wpcf7_contact_form( $jobscout_cf7_id );
    }
    if ( ! $jobscout_cf7_form && function_exists( 'wpcf7_get_contact_form_by_title' ) ) {
        $jobscout_cf7_form = wpcf7_get_contact_form_by_title( 'newsletter' );
    }
}

$line1_text       = get_theme_mod( 'news_newsletter_line1', __( 'Subscribe To', 'jobscout' ) );
$line2_text       = get_theme_mod( 'news_newsletter_line2', __( 'Our Newsletter', 'jobscout' ) );
$placeholder_text = get_theme_mod( 'news_newsletter_placeholder', __( 'Input your email address', 'jobscout' ) );
$btn_text         = get_theme_mod( 'news_newsletter_btn_text', __( 'SUBSCRIBE', 'jobscout' ) );
?>
<!-- Orange Newsletter Section -->
<section class="news-newsletter-section">
    <div class="container">
        <div class="news-newsletter-inner">
            <div class="news-newsletter-title">
                <span class="news-newsletter-line1"><?php echo esc_html( $line1_text ); ?></span>
                <span class="news-newsletter-line2"><?php echo esc_html( $line2_text ); ?></span>
            </div>
            <div class="news-newsletter-form-wrap<?php echo $jobscout_cf7_form ? ' news-newsletter-cf7' : ''; ?>">
                <?php if ( $jobscout_cf7_form ) : ?>
                    <?php
                    echo do_shortcode(
                        sprintf(
                            '[contact-form-7 id="%1$d" title="%2$s" html_class="news-newsletter-form-cf7"]',
                            (int) $jobscout_cf7_form->id(),
                            esc_attr( $jobscout_cf7_form->title() )
                        )
                    );
                    ?>
                <?php else : ?>
                    <form class="news-newsletter-form" onsubmit="event.preventDefault(); alert('Thank you for subscribing to our newsletter!');">
                        <div class="news-input-group">
                            <span class="news-input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ea751e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </span>
                            <input type="email" class="news-newsletter-email-input" placeholder="<?php echo esc_attr( $placeholder_text ); ?>" required />
                        </div>
                        <button type="submit" class="news-newsletter-btn"><?php echo esc_html( $btn_text ); ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
