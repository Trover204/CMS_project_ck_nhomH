<?php 
/**
* JobScout Metabox for Sidebar Layout
*
* @package JobScout
*
*/ 

function jobscout_add_sidebar_layout_box(){
    $post_id   = isset( $_GET['post'] ) ? $_GET['post'] : '';
    $shop_id   = get_option( 'woocommerce_shop_page_id' );
    $template  = get_post_meta( $post_id, '_wp_page_template', true );
    $templates = array( 'templates/portfolio.php' );
    
    /**
     * Remove sidebar metabox in specific page template.
    */
    if( ! in_array( $template, $templates ) && ( $shop_id != $post_id ) ){
        add_meta_box( 
            'jobscout_sidebar_layout',
            __( 'Sidebar Layout', 'jobscout' ),
            'jobscout_sidebar_layout_callback', 
            'page',
            'normal',
            'high'
        );
    }

    //for post
    add_meta_box( 
        'jobscout_sidebar_layout',
        __( 'Sidebar Layout', 'jobscout' ),
        'jobscout_sidebar_layout_callback', 
        'post',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'jobscout_add_sidebar_layout_box' );


/**
 * Get sidebar layout data.
 *
 * @return array
 */
if( ! function_exists( 'jobscout_get_sidebar_layout_data' ) ){
    function jobscout_get_sidebar_layout_data(){
        return array(
            'default-sidebar'=> array(
                'value'     => 'default-sidebar',
                'label'     => __( 'Default Sidebar', 'jobscout' ),
                'thumbnail' => get_template_directory_uri() . '/images/default-sidebar.png'
            ),
            'no-sidebar'     => array(
                'value'     => 'no-sidebar',
                'label'     => __( 'Full Width', 'jobscout' ),
                'thumbnail' => get_template_directory_uri() . '/images/full-width.png'
            ),
            'left-sidebar' => array(
                'value'     => 'left-sidebar',
                'label'     => __( 'Left Sidebar', 'jobscout' ),
                'thumbnail' => get_template_directory_uri() . '/images/left-sidebar.png'         
            ),
            'right-sidebar' => array(
                'value'     => 'right-sidebar',
                'label'     => __( 'Right Sidebar', 'jobscout' ),
                'thumbnail' => get_template_directory_uri() . '/images/right-sidebar.png'         
            )    
        );
    }
}

function jobscout_sidebar_layout_callback(){
    global $post;
    $jobscout_sidebar_layout = jobscout_get_sidebar_layout_data();
    wp_nonce_field( basename( __FILE__ ), 'jobscout_nonce' ); ?>
    <table class="form-table">
        <tr>
            <td colspan="4"><em class="f13"><?php esc_html_e( 'Choose Sidebar Template', 'jobscout' ); ?></em></td>
        </tr>    
        <tr>
            <td>
                <?php  
                    foreach( $jobscout_sidebar_layout as $field ){  
                        $layout = get_post_meta( $post->ID, '_jobscout_sidebar_layout', true ); ?>
                        <div class="hide-radio radio-image-wrapper" style="float:left; margin-right:30px;">
                            <input id="<?php echo esc_attr( $field['value'] ); ?>" type="radio" name="jobscout_sidebar_layout" value="<?php echo esc_attr( $field['value'] ); ?>" <?php checked( $field['value'], $layout ); if( empty( $layout ) ){ checked( $field['value'], 'default-sidebar' ); }?>/>
                            <label class="description" for="<?php echo esc_attr( $field['value'] ); ?>">
                                <img src="<?php echo esc_url( $field['thumbnail'] ); ?>" alt="<?php echo esc_attr( $field['label'] ); ?>" />
                            </label>
                        </div>
                        <?php 
                    } // end foreach 
                ?>
                <div class="clear"></div>
            </td>
        </tr>  
    </table>
 <?php 
}

function jobscout_save_sidebar_layout( $post_id ){
    $jobscout_sidebar_layout = jobscout_get_sidebar_layout_data();


    // Verify the nonce before proceeding.
    if( !isset( $_POST[ 'jobscout_nonce' ] ) || !wp_verify_nonce( $_POST[ 'jobscout_nonce' ], basename( __FILE__ ) ) )
        return;
    
    // Stop WP from clearing custom fields on autosave
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )  
        return;

    if('page' == $_POST['post_type'] ){  
        if( ! current_user_can( 'edit_page', $post_id ) ) return $post_id;  
    }elseif( ! current_user_can( 'edit_post', $post_id ) ){  
        return $post_id;  
    }
    
    $layout = isset( $_POST['jobscout_sidebar_layout'] ) ? sanitize_key( $_POST['jobscout_sidebar_layout'] ) : 'default-sidebar';

    if( array_key_exists( $layout, $jobscout_sidebar_layout ) ){
        update_post_meta( $post_id, '_jobscout_sidebar_layout', $layout );
    }else{
        delete_post_meta( $post_id, '_jobscout_sidebar_layout' );
    }           
}
add_action( 'save_post' , 'jobscout_save_sidebar_layout' );

/**
 * ==========================================================
 * JobScout Company Photos Gallery Metabox (For WP Admin)
 * Allows managing company photos directly in WordPress Admin
 * ==========================================================
 */
function jobscout_add_company_photos_metabox() {
    $screens = array( 'job_listing', 'page' );
    foreach ( $screens as $screen ) {
        add_meta_box(
            'jobscout_company_photos_box',
            __( 'Company Photos (Bộ sưu tập ảnh công ty)', 'jobscout' ),
            'jobscout_company_photos_metabox_callback',
            $screen,
            'normal',
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'jobscout_add_company_photos_metabox' );

function jobscout_company_photos_metabox_callback( $post ) {
    wp_nonce_field( 'jobscout_company_photos_save', 'jobscout_company_photos_nonce' );
    wp_enqueue_media();

    $photos = get_post_meta( $post->ID, '_company_photos', true );
    if ( ! is_array( $photos ) ) {
        $photos = array();
    }
    ?>
    <div id="jobscout-company-photos-wrapper" style="padding: 10px 0;">
        <p style="margin-bottom: 12px; color: #555; font-size: 13px;">
            <?php esc_html_e( 'Quản lý bộ sưu tập ảnh công ty hiển thị trong trang chi tiết Job. Ảnh đầu tiên sẽ làm ảnh bìa của widget, toàn bộ ảnh sẽ hiển thị trong cửa sổ xem ảnh Lightbox.', 'jobscout' ); ?>
        </p>

        <div style="margin-bottom: 15px;">
            <button type="button" class="button button-primary" id="jobscout_upload_company_photos_btn">
                <span class="dashicons dashicons-images-alt2" style="vertical-align: middle; margin-top: -2px; margin-right: 4px;"></span>
                <?php esc_html_e( 'Chọn / Tải ảnh từ thư viện Media', 'jobscout' ); ?>
            </button>
            <button type="button" class="button button-secondary" id="jobscout_clear_company_photos_btn" style="margin-left: 8px; <?php echo empty( $photos ) ? 'display:none;' : ''; ?>">
                <?php esc_html_e( 'Xóa toàn bộ ảnh', 'jobscout' ); ?>
            </button>
        </div>

        <div id="jobscout-photos-preview-grid" style="display: flex; flex-wrap: wrap; gap: 12px; min-height: 50px; padding: 10px; background: #f9f9f9; border: 1px dashed #ccd0d4; border-radius: 4px;">
            <?php if ( empty( $photos ) ) : ?>
                <span class="jobscout-no-photos-hint" style="color: #999; font-style: italic; line-height: 40px; padding-left: 5px;">
                    <?php esc_html_e( 'Chưa có ảnh nào được chọn. Nhấp nút trên để thêm ảnh từ máy tính hoặc thư viện.', 'jobscout' ); ?>
                </span>
            <?php else : ?>
                <?php foreach ( $photos as $idx => $photo_url ) : ?>
                    <div class="jobscout-photo-item" style="position: relative; width: 120px; height: 90px; border: 1px solid #ccd0d4; border-radius: 4px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                        <img src="<?php echo esc_url( $photo_url ); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
                        <input type="hidden" name="jobscout_company_photos[]" value="<?php echo esc_url( $photo_url ); ?>" />
                        <button type="button" class="jobscout-remove-photo-btn" title="<?php esc_attr_e( 'Xóa ảnh này', 'jobscout' ); ?>" style="position: absolute; top: 3px; right: 3px; background: rgba(220, 53, 69, 0.9); color: #fff; border: none; border-radius: 50%; width: 22px; height: 22px; line-height: 20px; font-size: 14px; font-weight: bold; cursor: pointer; text-align: center; padding: 0;">&times;</button>
                        <?php if ( 0 === $idx ) : ?>
                            <span style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(234, 117, 30, 0.9); color: #fff; font-size: 10px; font-weight: bold; text-align: center; padding: 2px 0; text-transform: uppercase;">Ảnh bìa</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var mediaFrame;
        $('#jobscout_upload_company_photos_btn').on('click', function(e) {
            e.preventDefault();
            if (mediaFrame) {
                mediaFrame.open();
                return;
            }
            mediaFrame = wp.media({
                title: '<?php echo esc_js( __( 'Chọn ảnh công ty cho Job này', 'jobscout' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Sử dụng các ảnh đã chọn', 'jobscout' ) ); ?>' },
                multiple: true
            });

            mediaFrame.on('select', function() {
                var selection = mediaFrame.state().get('selection').toJSON();
                if (selection.length > 0) {
                    $('.jobscout-no-photos-hint').remove();
                    $('#jobscout_clear_company_photos_btn').show();

                    selection.forEach(function(attachment) {
                        var imgUrl = attachment.url;
                        var thumbUrl = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : imgUrl;
                        var isFirst = $('#jobscout-photos-preview-grid .jobscout-photo-item').length === 0;
                        var badgeHtml = isFirst ? '<span style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(234, 117, 30, 0.9); color: #fff; font-size: 10px; font-weight: bold; text-align: center; padding: 2px 0; text-transform: uppercase;">Ảnh bìa</span>' : '';

                        var itemHtml = '<div class="jobscout-photo-item" style="position: relative; width: 120px; height: 90px; border: 1px solid #ccd0d4; border-radius: 4px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">' +
                            '<img src="' + thumbUrl + '" style="width: 100%; height: 100%; object-fit: cover; display: block;" />' +
                            '<input type="hidden" name="jobscout_company_photos[]" value="' + imgUrl + '" />' +
                            '<button type="button" class="jobscout-remove-photo-btn" title="Xóa ảnh này" style="position: absolute; top: 3px; right: 3px; background: rgba(220, 53, 69, 0.9); color: #fff; border: none; border-radius: 50%; width: 22px; height: 22px; line-height: 20px; font-size: 14px; font-weight: bold; cursor: pointer; text-align: center; padding: 0;">&times;</button>' +
                            badgeHtml +
                            '</div>';

                        $('#jobscout-photos-preview-grid').append(itemHtml);
                    });
                }
            });

            mediaFrame.open();
        });

        // Xóa 1 ảnh
        $(document).on('click', '.jobscout-remove-photo-btn', function(e) {
            e.preventDefault();
            $(this).closest('.jobscout-photo-item').remove();
            if ($('#jobscout-photos-preview-grid .jobscout-photo-item').length === 0) {
                $('#jobscout-photos-preview-grid').html('<span class="jobscout-no-photos-hint" style="color: #999; font-style: italic; line-height: 40px; padding-left: 5px;">Chưa có ảnh nào được chọn. Nhấp nút trên để thêm ảnh.</span>');
                $('#jobscout_clear_company_photos_btn').hide();
            }
        });

        // Xóa toàn bộ
        $('#jobscout_clear_company_photos_btn').on('click', function(e) {
            e.preventDefault();
            if (confirm('<?php echo esc_js( __( 'Bạn có chắc chắn muốn xóa toàn bộ ảnh trong bộ sưu tập này?', 'jobscout' ) ); ?>')) {
                $('#jobscout-photos-preview-grid').html('<span class="jobscout-no-photos-hint" style="color: #999; font-style: italic; line-height: 40px; padding-left: 5px;">Chưa có ảnh nào được chọn. Nhấp nút trên để thêm ảnh.</span>');
                $(this).hide();
            }
        });
    });
    </script>
    <?php
}

function jobscout_save_company_photos( $post_id ) {
    if ( ! isset( $_POST['jobscout_company_photos_nonce'] ) || ! wp_verify_nonce( $_POST['jobscout_company_photos_nonce'], 'jobscout_company_photos_save' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['jobscout_company_photos'] ) && is_array( $_POST['jobscout_company_photos'] ) ) {
        $cleaned_photos = array();
        foreach ( $_POST['jobscout_company_photos'] as $p_url ) {
            $url = esc_url_raw( trim( $p_url ) );
            if ( ! empty( $url ) ) {
                $cleaned_photos[] = $url;
            }
        }
        if ( ! empty( $cleaned_photos ) ) {
            update_post_meta( $post_id, '_company_photos', $cleaned_photos );
        } else {
            delete_post_meta( $post_id, '_company_photos' );
        }
    } else {
        delete_post_meta( $post_id, '_company_photos' );
    }
}
add_action( 'save_post', 'jobscout_save_company_photos' );


/**
 * ==========================================================
 * News Page Settings Metabox (For WP Admin -> Pages -> News)
 * Synchronized two-way with WordPress Customizer (theme_mods)
 * ==========================================================
 */
function jobscout_add_news_page_metabox() {
    global $post;
    if ( ! $post ) {
        return;
    }
    $template = get_post_meta( $post->ID, '_wp_page_template', true );
    if ( 'page-news.php' === $template || 'news' === $post->post_name ) {
        add_meta_box(
            'jobscout_news_page_settings_box',
            __( 'News Page Customize Settings (Cấu hình trang News)', 'jobscout' ),
            'jobscout_news_page_metabox_callback',
            'page',
            'normal',
            'high'
        );
    }

    // Metabox for Individual Blog Posts (WP Admin -> Posts)
    add_meta_box(
        'jobscout_news_post_card_box',
        __( 'News Card & Detail Settings (Tùy chỉnh hiển thị trên trang News)', 'jobscout' ),
        'jobscout_news_post_card_metabox_callback',
        'post',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'jobscout_add_news_page_metabox' );

function jobscout_news_page_metabox_callback( $post ) {
    wp_nonce_field( 'jobscout_news_page_meta_save', 'jobscout_news_page_meta_nonce' );
    wp_enqueue_media();

    $banner_title     = get_theme_mod( 'news_banner_title', 'PDS NEWS' );
    $banner_bg        = function_exists( 'jobscout_get_news_banner_bg_url' ) ? jobscout_get_news_banner_bg_url() : get_template_directory_uri() . '/images/view-tokyo-cityscape-night-japan.jpg';
    $section_heading  = get_theme_mod( 'news_section_heading', 'NEWEST BLOG ENTRIES' );
    $posts_per_page   = absint( get_theme_mod( 'news_posts_per_page', 8 ) );
    $excerpt_length   = absint( get_theme_mod( 'news_excerpt_length', 18 ) );
    $readmore_text    = get_theme_mod( 'news_readmore_text', 'Read More' );
    $newsletter_line1 = get_theme_mod( 'news_newsletter_line1', 'Subscribe To' );
    $newsletter_line2 = get_theme_mod( 'news_newsletter_line2', 'Our Newsletter' );
    $newsletter_ph    = get_theme_mod( 'news_newsletter_placeholder', 'Nhập địa chỉ email của bạn' );
    $newsletter_btn   = get_theme_mod( 'news_newsletter_btn_text', 'ĐĂNG KÝ' );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="news_banner_title"><?php esc_html_e( 'Hero Banner Title', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_banner_title" name="news_banner_title" value="<?php echo esc_attr( $banner_title ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_banner_bg"><?php esc_html_e( 'Hero Banner Background Image', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_banner_bg" name="news_banner_bg" value="<?php echo esc_url( $banner_bg ); ?>" class="large-text" style="margin-bottom:8px;" />
                <button type="button" class="button" id="jobscout_select_news_banner_btn"><?php esc_html_e( 'Chọn ảnh từ Media Library', 'jobscout' ); ?></button>
                <div style="margin-top:8px;">
                    <img id="news_banner_bg_preview" src="<?php echo esc_url( $banner_bg ); ?>" style="max-width:280px; height:auto; border-radius:4px; border:1px solid #ccd0d4;" />
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="news_section_heading"><?php esc_html_e( 'Section Heading', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_section_heading" name="news_section_heading" value="<?php echo esc_attr( $section_heading ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_posts_per_page"><?php esc_html_e( 'Posts Per Page', 'jobscout' ); ?></label></th>
            <td>
                <input type="number" id="news_posts_per_page" name="news_posts_per_page" value="<?php echo esc_attr( $posts_per_page ); ?>" min="1" max="24" class="small-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_excerpt_length"><?php esc_html_e( 'Excerpt Length (Words)', 'jobscout' ); ?></label></th>
            <td>
                <input type="number" id="news_excerpt_length" name="news_excerpt_length" value="<?php echo esc_attr( $excerpt_length ); ?>" min="5" max="60" class="small-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_readmore_text"><?php esc_html_e( 'Read More Button Text', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_readmore_text" name="news_readmore_text" value="<?php echo esc_attr( $readmore_text ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_newsletter_line1"><?php esc_html_e( 'Newsletter Heading Line 1', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_newsletter_line1" name="news_newsletter_line1" value="<?php echo esc_attr( $newsletter_line1 ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_newsletter_line2"><?php esc_html_e( 'Newsletter Heading Line 2', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_newsletter_line2" name="news_newsletter_line2" value="<?php echo esc_attr( $newsletter_line2 ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_newsletter_placeholder"><?php esc_html_e( 'Newsletter Input Placeholder', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_newsletter_placeholder" name="news_newsletter_placeholder" value="<?php echo esc_attr( $newsletter_ph ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_newsletter_btn_text"><?php esc_html_e( 'Newsletter Button Text', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_newsletter_btn_text" name="news_newsletter_btn_text" value="<?php echo esc_attr( $newsletter_btn ); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <p class="description">
        <?php
        printf(
            /* translators: %s: Customizer URL */
            wp_kses_post( __( 'Bạn cũng có thể chỉnh sửa trực quan với biểu tượng bút chì màu xanh trong <a href="%s" target="_blank"><strong>Appearance &rarr; Customize (Giao diện &rarr; Tùy biến)</strong></a>.', 'jobscout' ) ),
            esc_url( admin_url( 'customize.php?url=' . rawurlencode( get_permalink( $post->ID ) ) ) )
        );
        ?>
    </p>
    <script>
    jQuery(document).ready(function($) {
        var frame;
        $('#jobscout_select_news_banner_btn').on('click', function(e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Chọn ảnh Banner cho trang News',
                button: { text: 'Sử dụng ảnh này' },
                multiple: false
            });
            frame.on('select', function() {
                var att = frame.state().get('selection').first().toJSON();
                $('#news_banner_bg').val(att.url);
                $('#news_banner_bg_preview').attr('src', att.url);
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function jobscout_save_news_page_metabox( $post_id ) {
    if ( ! isset( $_POST['jobscout_news_page_meta_nonce'] ) || ! wp_verify_nonce( $_POST['jobscout_news_page_meta_nonce'], 'jobscout_news_page_meta_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
    }

    $text_fields = array(
        'news_banner_title',
        'news_section_heading',
        'news_readmore_text',
        'news_newsletter_line1',
        'news_newsletter_line2',
        'news_newsletter_placeholder',
        'news_newsletter_btn_text',
    );
    foreach ( $text_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            set_theme_mod( $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
        }
    }

    if ( isset( $_POST['news_banner_bg'] ) ) {
        set_theme_mod( 'news_banner_bg', esc_url_raw( wp_unslash( $_POST['news_banner_bg'] ) ) );
    }
    if ( isset( $_POST['news_posts_per_page'] ) ) {
        set_theme_mod( 'news_posts_per_page', absint( $_POST['news_posts_per_page'] ) );
    }
    if ( isset( $_POST['news_excerpt_length'] ) ) {
        set_theme_mod( 'news_excerpt_length', absint( $_POST['news_excerpt_length'] ) );
    }
}
add_action( 'save_post_page', 'jobscout_save_news_page_metabox' );

function jobscout_news_post_card_metabox_callback( $post ) {
    wp_nonce_field( 'jobscout_news_post_card_save', 'jobscout_news_post_card_nonce' );
    wp_enqueue_media();

    $pid          = $post->ID;
    $location     = get_post_meta( $pid, 'location', true );
    if ( empty( $location ) ) {
        $location = get_theme_mod( 'news_post_location_' . $pid, 'Ho Chi Minh City' );
    }
    $custom_thumb = function_exists( 'jobscout_get_news_post_thumb_url' ) ? jobscout_get_news_post_thumb_url( $pid ) : get_the_post_thumbnail_url( $pid, 'jobscout-blog' );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="news_post_location"><?php esc_html_e( 'Location Badge (News Detail)', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_post_location" name="news_post_location" value="<?php echo esc_attr( $location ); ?>" class="regular-text" placeholder="Ho Chi Minh City" />
            </td>
        </tr>
        <tr>
            <th><label for="news_custom_thumb"><?php esc_html_e( 'Card Thumbnail Image', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_custom_thumb" name="news_custom_thumb" value="<?php echo esc_url( $custom_thumb ); ?>" class="large-text" style="margin-bottom:8px;" />
                <button type="button" class="button" id="jobscout_select_post_thumb_btn"><?php esc_html_e( 'Chọn ảnh từ Media Library', 'jobscout' ); ?></button>
                <?php if ( $custom_thumb ) : ?>
                    <div style="margin-top:8px;">
                        <img id="news_custom_thumb_preview" src="<?php echo esc_url( $custom_thumb ); ?>" style="width:140px; height:140px; object-fit:cover; border-radius:4px; border:1px solid #ccd0d4;" />
                    </div>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <script>
    jQuery(document).ready(function($) {
        var postThumbFrame;
        $('#jobscout_select_post_thumb_btn').on('click', function(e) {
            e.preventDefault();
            if (postThumbFrame) { postThumbFrame.open(); return; }
            postThumbFrame = wp.media({
                title: 'Chọn ảnh đại diện cho bài viết News',
                button: { text: 'Sử dụng ảnh này' },
                multiple: false
            });
            postThumbFrame.on('select', function() {
                var att = postThumbFrame.state().get('selection').first().toJSON();
                $('#news_custom_thumb').val(att.url);
                $('#news_custom_thumb_preview').attr('src', att.url);
            });
            postThumbFrame.open();
        });
    });
    </script>
    <?php
}

function jobscout_save_news_post_card_metabox( $post_id ) {
    if ( ! isset( $_POST['jobscout_news_post_card_nonce'] ) || ! wp_verify_nonce( $_POST['jobscout_news_post_card_nonce'], 'jobscout_news_post_card_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['news_post_location'] ) ) {
        $loc = sanitize_text_field( wp_unslash( $_POST['news_post_location'] ) );
        update_post_meta( $post_id, 'location', $loc );
        set_theme_mod( 'news_post_location_' . $post_id, $loc );
    }

    if ( isset( $_POST['news_custom_thumb'] ) ) {
        $thumb = esc_url_raw( wp_unslash( $_POST['news_custom_thumb'] ) );
        update_post_meta( $post_id, '_news_custom_thumb', $thumb );
        set_theme_mod( 'news_post_thumb_' . $post_id, $thumb );
    }
}
add_action( 'save_post_post', 'jobscout_save_news_post_card_metabox', 15 );
