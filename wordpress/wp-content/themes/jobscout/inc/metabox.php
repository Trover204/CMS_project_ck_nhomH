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