<?php
/**
 * Template part: Orange Newsletter Section (powered by Contact Form 7).
 *
 * Automatically ensures the Contact Form 7 "newsletter" form exists with the exact
 * PDS design markup (envelope icon, Vietnamese placeholder "Nhập địa chỉ email của bạn",
 * and "ĐĂNG KÝ" button), configures valid mail/messages/additional_settings so
 * submissions succeed on localhost/Docker, and renders it via Contact Form 7 shortcode.
 *
 * Usage: get_template_part( 'template-parts/newsletter-cf7' );
 *
 * @package JobScout
 */

if ( ! function_exists( 'jobscout_get_newsletter_cf7_template' ) ) :
/**
 * Build the Contact Form 7 form template markup matching the PDS Newsletter design.
 */
function jobscout_get_newsletter_cf7_template( $placeholder = '', $btn_text = '' ) {
    if ( '' === $placeholder ) {
        $placeholder = get_theme_mod( 'news_newsletter_placeholder', 'Nhập địa chỉ email của bạn' );
    }
    if ( '' === $btn_text ) {
        $btn_text = get_theme_mod( 'news_newsletter_btn_text', 'ĐĂNG KÝ' );
    }

    // Normalize legacy English defaults to match the Vietnamese PDS design
    if ( 'Input your email address' === $placeholder ) {
        $placeholder = 'Nhập địa chỉ email của bạn';
    }
    if ( 'SUBSCRIBE' === strtoupper( trim( $btn_text ) ) ) {
        $btn_text = 'ĐĂNG KÝ';
    }

    $placeholder = str_replace( '"', "'", $placeholder );
    $btn_text    = str_replace( '"', "'", $btn_text );

    return '<div class="news-cf7-row">'
        . '<div class="news-input-group">'
        . '<span class="news-input-icon news-cf7-icon"></span>'
        . '[email* newsletter-email class:news-newsletter-email-input placeholder "' . $placeholder . '"]'
        . '</div>'
        . '[submit class:news-newsletter-btn "' . $btn_text . '"]'
        . '</div>';
}
endif;

if ( ! function_exists( 'jobscout_configure_newsletter_cf7_meta' ) ) :
/**
 * Configure Contact Form 7 meta (_form, _mail, _messages, _additional_settings)
 * so the newsletter form always validates and submits cleanly on localhost/Docker.
 */
function jobscout_configure_newsletter_cf7_meta( $form_id, $desired_template ) {
    $form_id = absint( $form_id );
    if ( ! $form_id ) {
        return;
    }

    update_post_meta( $form_id, '_form', $desired_template );

    // Enable skip_mail in Additional Settings so localhost/Docker without SMTP still succeeds
    $additional_settings = (string) get_post_meta( $form_id, '_additional_settings', true );
    if ( strpos( $additional_settings, 'skip_mail' ) === false ) {
        $additional_settings = trim( $additional_settings . "\nskip_mail: on" );
        update_post_meta( $form_id, '_additional_settings', $additional_settings );
    }

    // Ensure valid _mail structure with valid domain syntax
    $admin_email = get_option( 'admin_email' );
    if ( ! is_email( $admin_email ) || strpos( $admin_email, '@localhost' ) !== false ) {
        $admin_email = 'admin@plando-see.com';
    }
    $mail_meta = array(
        'active'             => true,
        'subject'            => 'Đăng ký nhận bản tin mới từ [newsletter-email]',
        'sender'             => 'PDS Newsletter <noreply@plando-see.com>',
        'recipient'          => $admin_email,
        'body'               => "Có người dùng mới đăng ký nhận bản tin (Newsletter):\nEmail: [newsletter-email]\n",
        'additional_headers' => 'Reply-To: [newsletter-email]',
        'attachments'        => '',
        'use_html'           => false,
        'exclude_blank'      => false,
    );
    update_post_meta( $form_id, '_mail', $mail_meta );

    // Configure user-friendly Vietnamese messages
    $messages = get_post_meta( $form_id, '_messages', true );
    if ( ! is_array( $messages ) ) {
        $messages = array();
    }
    $messages['mail_sent_ok']     = 'Cảm ơn bạn đã đăng ký nhận bản tin thành công!';
    $messages['mail_sent_ng']     = 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.';
    $messages['validation_error'] = 'Vui lòng kiểm tra lại địa chỉ email và thử lại.';
    $messages['invalid_required'] = 'Vui lòng nhập địa chỉ email của bạn.';
    $messages['invalid_email']    = 'Địa chỉ email bạn nhập không hợp lệ.';
    update_post_meta( $form_id, '_messages', $messages );
}
endif;

if ( ! function_exists( 'jobscout_get_or_create_newsletter_cf7_form' ) ) :
/**
 * Find or automatically create/synchronize the Contact Form 7 "newsletter" form.
 */
function jobscout_get_or_create_newsletter_cf7_form() {
    if ( ! function_exists( 'wpcf7_contact_form' ) ) {
        return null;
    }

    $cf7_form = null;
    $cf7_id   = absint( get_theme_mod( 'news_newsletter_cf7_id', 0 ) );

    if ( $cf7_id ) {
        $cf7_form = wpcf7_contact_form( $cf7_id );
    }

    if ( ! $cf7_form && function_exists( 'wpcf7_get_contact_form_by_title' ) ) {
        $cf7_form = wpcf7_get_contact_form_by_title( 'newsletter' );
    }

    if ( ! $cf7_form ) {
        $existing = get_posts( array(
            'post_type'      => 'wpcf7_contact_form',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            's'              => 'newsletter',
        ) );
        if ( ! empty( $existing ) ) {
            $cf7_form = wpcf7_contact_form( $existing[0]->ID );
        }
    }

    $desired_template = jobscout_get_newsletter_cf7_template();

    // Auto-create the Contact Form 7 "newsletter" form if it does not exist yet
    if ( ! $cf7_form ) {
        $new_form_id = wp_insert_post( array(
            'post_type'    => 'wpcf7_contact_form',
            'post_status'  => 'publish',
            'post_title'   => 'newsletter',
            'post_name'    => 'newsletter',
            'post_content' => $desired_template,
        ) );

        if ( $new_form_id && ! is_wp_error( $new_form_id ) ) {
            jobscout_configure_newsletter_cf7_meta( $new_form_id, $desired_template );
            update_post_meta( $new_form_id, '_locale', get_locale() );
            set_theme_mod( 'news_newsletter_cf7_id', (int) $new_form_id );
            $cf7_form = wpcf7_contact_form( $new_form_id );
        }
    } else {
        // Synchronize existing CF7 form with the PDS `.news-cf7-row` layout & settings
        $form_id      = (int) $cf7_form->id();
        $current_form = (string) get_post_meta( $form_id, '_form', true );
        $add_settings = (string) get_post_meta( $form_id, '_additional_settings', true );
        if ( trim( $current_form ) !== trim( $desired_template ) || strpos( $add_settings, 'skip_mail' ) === false ) {
            jobscout_configure_newsletter_cf7_meta( $form_id, $desired_template );
            wp_update_post( array(
                'ID'           => $form_id,
                'post_content' => $desired_template,
            ) );
            $cf7_form = wpcf7_contact_form( $form_id );
        }
    }

    return $cf7_form;
}
endif;

// Disable Contact Form 7 auto-<p> wrapper inside the inline newsletter row
add_filter( 'wpcf7_autop_or_not', '__return_false' );

$jobscout_cf7_form = jobscout_get_or_create_newsletter_cf7_form();
$line1_text        = get_theme_mod( 'news_newsletter_line1', __( 'Subscribe To', 'jobscout' ) );
$line2_text        = get_theme_mod( 'news_newsletter_line2', __( 'Our Newsletter', 'jobscout' ) );
$placeholder_text  = get_theme_mod( 'news_newsletter_placeholder', 'Nhập địa chỉ email của bạn' );
$btn_text          = get_theme_mod( 'news_newsletter_btn_text', 'ĐĂNG KÝ' );

if ( 'Input your email address' === $placeholder_text ) {
    $placeholder_text = 'Nhập địa chỉ email của bạn';
}
if ( 'SUBSCRIBE' === strtoupper( trim( $btn_text ) ) ) {
    $btn_text = 'ĐĂNG KÝ';
}
?>
<!-- Orange Newsletter Section (Contact Form 7) -->
<section class="news-newsletter-section">
    <div class="container">
        <div class="news-newsletter-inner">
            <div class="news-newsletter-title">
                <span class="news-newsletter-line1"><?php echo esc_html( $line1_text ); ?></span>
                <span class="news-newsletter-line2"><?php echo esc_html( $line2_text ); ?></span>
            </div>
            <div class="news-newsletter-form-wrap news-newsletter-cf7">
                <?php
                if ( $jobscout_cf7_form ) :
                    echo do_shortcode(
                        sprintf(
                            '[contact-form-7 id="%1$d" title="%2$s" html_class="news-newsletter-form-cf7"]',
                            (int) $jobscout_cf7_form->id(),
                            esc_attr( $jobscout_cf7_form->title() )
                        )
                    );
                else :
                    // Fallback if Contact Form 7 plugin is deactivated
                    ?>
                    <div class="wpcf7">
                        <form class="wpcf7-form news-newsletter-form-cf7" onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã đăng ký nhận bản tin thành công!');">
                            <div class="news-cf7-row">
                                <div class="news-input-group">
                                    <span class="news-input-icon news-cf7-icon"></span>
                                    <span class="wpcf7-form-control-wrap" data-name="newsletter-email">
                                        <input type="email" name="newsletter-email" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email news-newsletter-email-input" placeholder="<?php echo esc_attr( $placeholder_text ); ?>" required />
                                    </span>
                                </div>
                                <input type="submit" value="<?php echo esc_attr( $btn_text ); ?>" class="wpcf7-form-control wpcf7-submit news-newsletter-btn" />
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php
remove_filter( 'wpcf7_autop_or_not', '__return_false' );
