<?php 
/**
* JobScout Metabox for Sidebar Layout, Company Photos & News Page/Post Settings
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
            __( 'Company Photos Gallery', 'jobscout' ),
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
            <?php esc_html_e( 'Manage the company photo gallery displayed on the job detail page. The first photo will serve as the widget cover, and all photos will appear in the Lightbox viewer.', 'jobscout' ); ?>
        </p>

        <div style="margin-bottom: 15px;">
            <button type="button" class="button button-primary" id="jobscout_upload_company_photos_btn">
                <span class="dashicons dashicons-images-alt2" style="vertical-align: middle; margin-top: -2px; margin-right: 4px;"></span>
                <?php esc_html_e( 'Upload / Select from Media Library', 'jobscout' ); ?>
            </button>
            <button type="button" class="button button-secondary" id="jobscout_clear_company_photos_btn" style="margin-left: 8px; <?php echo empty( $photos ) ? 'display:none;' : ''; ?>">
                <?php esc_html_e( 'Clear All Photos', 'jobscout' ); ?>
            </button>
        </div>

        <div id="jobscout-photos-preview-grid" style="display: flex; flex-wrap: wrap; gap: 12px; min-height: 50px; padding: 10px; background: #f9f9f9; border: 1px dashed #ccd0d4; border-radius: 4px;">
            <?php if ( empty( $photos ) ) : ?>
                <span class="jobscout-no-photos-hint" style="color: #999; font-style: italic; line-height: 40px; padding-left: 5px;">
                    <?php esc_html_e( 'No photos selected yet. Click the button above to add photos from your computer or media library.', 'jobscout' ); ?>
                </span>
            <?php else : ?>
                <?php foreach ( $photos as $idx => $photo_url ) : ?>
                    <div class="jobscout-photo-item" style="position: relative; width: 120px; height: 90px; border: 1px solid #ccd0d4; border-radius: 4px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                        <img src="<?php echo esc_url( $photo_url ); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
                        <input type="hidden" name="jobscout_company_photos[]" value="<?php echo esc_url( $photo_url ); ?>" />
                        <button type="button" class="jobscout-remove-photo-btn" title="<?php esc_attr_e( 'Remove photo', 'jobscout' ); ?>" style="position: absolute; top: 3px; right: 3px; background: rgba(220, 53, 69, 0.9); color: #fff; border: none; border-radius: 50%; width: 22px; height: 22px; line-height: 20px; font-size: 14px; font-weight: bold; cursor: pointer; text-align: center; padding: 0;">&times;</button>
                        <?php if ( 0 === $idx ) : ?>
                            <span style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(234, 117, 30, 0.9); color: #fff; font-size: 10px; font-weight: bold; text-align: center; padding: 2px 0; text-transform: uppercase;">Cover Photo</span>
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
                title: '<?php echo esc_js( __( 'Select company photos for this job', 'jobscout' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Use selected photos', 'jobscout' ) ); ?>' },
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
                        var badgeHtml = isFirst ? '<span style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(234, 117, 30, 0.9); color: #fff; font-size: 10px; font-weight: bold; text-align: center; padding: 2px 0; text-transform: uppercase;">Cover Photo</span>' : '';

                        var itemHtml = '<div class="jobscout-photo-item" style="position: relative; width: 120px; height: 90px; border: 1px solid #ccd0d4; border-radius: 4px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">' +
                            '<img src="' + thumbUrl + '" style="width: 100%; height: 100%; object-fit: cover; display: block;" />' +
                            '<input type="hidden" name="jobscout_company_photos[]" value="' + imgUrl + '" />' +
                            '<button type="button" class="jobscout-remove-photo-btn" title="Remove photo" style="position: absolute; top: 3px; right: 3px; background: rgba(220, 53, 69, 0.9); color: #fff; border: none; border-radius: 50%; width: 22px; height: 22px; line-height: 20px; font-size: 14px; font-weight: bold; cursor: pointer; text-align: center; padding: 0;">&times;</button>' +
                            badgeHtml +
                            '</div>';

                        $('#jobscout-photos-preview-grid').append(itemHtml);
                    });
                }
            });

            mediaFrame.open();
        });

        // Remove 1 photo
        $(document).on('click', '.jobscout-remove-photo-btn', function(e) {
            e.preventDefault();
            $(this).closest('.jobscout-photo-item').remove();
            if ($('#jobscout-photos-preview-grid .jobscout-photo-item').length === 0) {
                $('#jobscout-photos-preview-grid').html('<span class="jobscout-no-photos-hint" style="color: #999; font-style: italic; line-height: 40px; padding-left: 5px;">No photos selected yet. Click the button above to add photos.</span>');
                $('#jobscout_clear_company_photos_btn').hide();
            }
        });

        // Clear all photos
        $('#jobscout_clear_company_photos_btn').on('click', function(e) {
            e.preventDefault();
            if (confirm('<?php echo esc_js( __( 'Are you sure you want to remove all photos in this gallery?', 'jobscout' ) ); ?>')) {
                $('#jobscout-photos-preview-grid').html('<span class="jobscout-no-photos-hint" style="color: #999; font-style: italic; line-height: 40px; padding-left: 5px;">No photos selected yet. Click the button above to add photos.</span>');
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
 * News Page & News Detail Settings Metaboxes (WP Admin)
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
            __( 'News Page & Detail Customize Settings', 'jobscout' ),
            'jobscout_news_page_metabox_callback',
            'page',
            'normal',
            'high'
        );
    }

    // Metabox for Individual Blog Posts (WP Admin -> Posts)
    add_meta_box(
        'jobscout_news_post_card_box',
        __( 'News Card & Detail Settings', 'jobscout' ),
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
    $bc_home          = get_theme_mod( 'news_breadcrumb_home', 'Home' );
    $bc_all           = get_theme_mod( 'news_breadcrumb_all', 'All News' );
    $bc_detail        = get_theme_mod( 'news_breadcrumb_detail', 'News Detail' );
    $posted_prefix    = get_theme_mod( 'news_detail_posted_prefix', 'Posted: ' );
    $share_label      = get_theme_mod( 'news_detail_share_label', 'SHARE' );
    $newest_count     = absint( get_theme_mod( 'news_detail_newest_count', 6 ) );
    $newsletter_line1 = get_theme_mod( 'news_newsletter_line1', 'Subscribe To' );
    $newsletter_line2 = get_theme_mod( 'news_newsletter_line2', 'Our Newsletter' );
    $newsletter_ph    = get_theme_mod( 'news_newsletter_placeholder', 'Input your email address' );
    $newsletter_btn   = get_theme_mod( 'news_newsletter_btn_text', 'SUBSCRIBE' );
    ?>
    <table class="form-table">
        <tr>
            <th colspan="2" style="padding-bottom:0;"><h3 style="margin:0;color:#ea751e;"><?php esc_html_e( '1. News Page Settings (Blog Grid)', 'jobscout' ); ?></h3></th>
        </tr>
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
                <button type="button" class="button" id="jobscout_select_news_banner_btn"><?php esc_html_e( 'Select Image from Media Library', 'jobscout' ); ?></button>
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
            <th colspan="2" style="padding-bottom:0;"><h3 style="margin:10px 0 0;color:#ea751e;"><?php esc_html_e( '2. News Detail Page Settings', 'jobscout' ); ?></h3></th>
        </tr>
        <tr>
            <th><label for="news_breadcrumb_home"><?php esc_html_e( 'Breadcrumb: Home Text', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_breadcrumb_home" name="news_breadcrumb_home" value="<?php echo esc_attr( $bc_home ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_breadcrumb_all"><?php esc_html_e( 'Breadcrumb: All News Text', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_breadcrumb_all" name="news_breadcrumb_all" value="<?php echo esc_attr( $bc_all ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_breadcrumb_detail"><?php esc_html_e( 'Breadcrumb: News Detail Text', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_breadcrumb_detail" name="news_breadcrumb_detail" value="<?php echo esc_attr( $bc_detail ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_detail_posted_prefix"><?php esc_html_e( 'Date Prefix Text', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_detail_posted_prefix" name="news_detail_posted_prefix" value="<?php echo esc_attr( $posted_prefix ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_detail_share_label"><?php esc_html_e( 'Share Button Text', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_detail_share_label" name="news_detail_share_label" value="<?php echo esc_attr( $share_label ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="news_detail_newest_count"><?php esc_html_e( 'Newest Blog Entries Count (Detail Page)', 'jobscout' ); ?></label></th>
            <td>
                <input type="number" id="news_detail_newest_count" name="news_detail_newest_count" value="<?php echo esc_attr( $newest_count ); ?>" min="1" max="12" class="small-text" />
            </td>
        </tr>

        <tr>
            <th colspan="2" style="padding-bottom:0;"><h3 style="margin:10px 0 0;color:#ea751e;"><?php esc_html_e( '3. Newsletter Settings', 'jobscout' ); ?></h3></th>
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
            wp_kses_post( __( 'You can also visually customize this page using the blue pencil icons in <a href="%s" target="_blank"><strong>Appearance &rarr; Customize</strong></a>.', 'jobscout' ) ),
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
                title: 'Select Banner Image for News Page',
                button: { text: 'Use this image' },
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
        'news_breadcrumb_home',
        'news_breadcrumb_all',
        'news_breadcrumb_detail',
        'news_detail_posted_prefix',
        'news_detail_share_label',
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
    if ( isset( $_POST['news_detail_newest_count'] ) ) {
        set_theme_mod( 'news_detail_newest_count', absint( $_POST['news_detail_newest_count'] ) );
    }
}
add_action( 'save_post_page', 'jobscout_save_news_page_metabox' );

function jobscout_news_post_card_metabox_callback( $post ) {
    wp_nonce_field( 'jobscout_news_post_card_save', 'jobscout_news_post_card_nonce' );
    wp_enqueue_media();

    $pid          = $post->ID;
    $category_lbl = function_exists( 'jobscout_get_news_post_category' ) ? jobscout_get_news_post_category( $pid ) : get_post_meta( $pid, '_news_category_label', true );
    $location     = function_exists( 'jobscout_get_news_post_location' ) ? jobscout_get_news_post_location( $pid ) : get_post_meta( $pid, 'location', true );
    $display_date = get_post_meta( $pid, '_news_display_date', true );
    if ( empty( $display_date ) ) {
        $display_date = get_theme_mod( 'news_post_date_' . $pid, get_the_date( 'M d, Y', $pid ) );
    }
    $excerpt_val  = function_exists( 'jobscout_get_news_post_excerpt' ) ? jobscout_get_news_post_excerpt( $pid ) : $post->post_excerpt;
    $custom_thumb = get_the_post_thumbnail_url( $pid, 'full' );
    if ( ! $custom_thumb ) {
        $meta_t = get_post_meta( $pid, '_news_custom_thumb', true );
        if ( ! empty( $meta_t ) && strpos( $meta_t, 'banner-image.jpg' ) === false ) {
            $custom_thumb = $meta_t;
        }
    }
    $content_img1 = get_theme_mod( 'news_post_content_img1_' . $pid, get_post_meta( $pid, '_news_content_img1', true ) );
    $content_img2 = get_theme_mod( 'news_post_content_img2_' . $pid, get_post_meta( $pid, '_news_content_img2', true ) );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="news_post_category"><?php esc_html_e( 'Category Badge (News Detail)', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_post_category" name="news_post_category" value="<?php echo esc_attr( $category_lbl ); ?>" class="regular-text" placeholder="Category Name" />
                <p class="description"><?php esc_html_e( 'Category badge displayed below the post date on the News Detail page.', 'jobscout' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="news_post_location"><?php esc_html_e( 'Location Badge (News Detail)', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_post_location" name="news_post_location" value="<?php echo esc_attr( $location ); ?>" class="regular-text" placeholder="Ho Chi Minh City" />
                <p class="description"><?php esc_html_e( 'Location badge displayed next to the category badge on the News Detail page.', 'jobscout' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="news_post_date"><?php esc_html_e( 'Posted Date (News Detail)', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_post_date" name="news_post_date" value="<?php echo esc_attr( $display_date ); ?>" class="regular-text" placeholder="Oct 14, 2026" />
                <p class="description"><?php esc_html_e( 'Date displayed on the News Detail page (e.g. Oct 14, 2026).', 'jobscout' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="news_post_excerpt_meta"><?php esc_html_e( 'Card Excerpt', 'jobscout' ); ?></label></th>
            <td>
                <textarea id="news_post_excerpt_meta" name="news_post_excerpt_meta" rows="3" class="large-text"><?php echo esc_textarea( $excerpt_val ); ?></textarea>
                <p class="description"><?php esc_html_e( 'Short summary displayed on the News Card in the News page and Newest Blog Entries section.', 'jobscout' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="news_custom_thumb"><?php esc_html_e( 'Card & Detail Thumbnail Image', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_custom_thumb" name="news_custom_thumb" value="<?php echo esc_url( $custom_thumb ); ?>" class="large-text" style="margin-bottom:8px;" />
                <button type="button" class="button" id="jobscout_select_post_thumb_btn"><?php esc_html_e( 'Select Image from Media Library', 'jobscout' ); ?></button>
                <div style="margin-top:8px;">
                    <img id="news_custom_thumb_preview" src="<?php echo esc_url( $custom_thumb ); ?>" style="width:140px; height:140px; object-fit:cover; border-radius:4px; border:1px solid #ccd0d4; <?php echo empty( $custom_thumb ) ? 'display:none;' : ''; ?>" />
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="news_content_img1"><?php esc_html_e( 'Content Inline Image 1 (Optional)', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_content_img1" name="news_content_img1" value="<?php echo esc_url( $content_img1 ); ?>" class="large-text" style="margin-bottom:8px;" />
                <button type="button" class="button jobscout-select-inline-img" data-target="#news_content_img1" data-preview="#news_content_img1_preview"><?php esc_html_e( 'Select Image from Media Library', 'jobscout' ); ?></button>
                <div style="margin-top:8px;">
                    <img id="news_content_img1_preview" src="<?php echo esc_url( $content_img1 ); ?>" style="max-width:220px; height:auto; border-radius:4px; border:1px solid #ccd0d4; <?php echo empty( $content_img1 ) ? 'display:none;' : ''; ?>" />
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="news_content_img2"><?php esc_html_e( 'Content Inline Image 2 (Optional)', 'jobscout' ); ?></label></th>
            <td>
                <input type="text" id="news_content_img2" name="news_content_img2" value="<?php echo esc_url( $content_img2 ); ?>" class="large-text" style="margin-bottom:8px;" />
                <button type="button" class="button jobscout-select-inline-img" data-target="#news_content_img2" data-preview="#news_content_img2_preview"><?php esc_html_e( 'Select Image from Media Library', 'jobscout' ); ?></button>
                <div style="margin-top:8px;">
                    <img id="news_content_img2_preview" src="<?php echo esc_url( $content_img2 ); ?>" style="max-width:220px; height:auto; border-radius:4px; border:1px solid #ccd0d4; <?php echo empty( $content_img2 ) ? 'display:none;' : ''; ?>" />
                </div>
            </td>
        </tr>
    </table>
    <?php if ( 'publish' === $post->post_status ) : ?>
        <p class="description" style="margin-top: 10px;">
            <?php
            printf(
                /* translators: %s: Customizer URL */
                wp_kses_post( __( 'Tip: You can visually customize this News Detail page using the blue pencil icons in <a href="%s" target="_blank"><strong>Appearance &rarr; Customize (Open this News Detail in Customizer)</strong></a>.', 'jobscout' ) ),
                esc_url( admin_url( 'customize.php?url=' . rawurlencode( get_permalink( $pid ) ) ) )
            );
            ?>
        </p>
    <?php endif; ?>
    <script>
    jQuery(document).ready(function($) {
        var postThumbFrame;
        $('#jobscout_select_post_thumb_btn').on('click', function(e) {
            e.preventDefault();
            if (postThumbFrame) { postThumbFrame.open(); return; }
            postThumbFrame = wp.media({
                title: 'Select Thumbnail Image for News Post',
                button: { text: 'Use this image' },
                multiple: false
            });
            postThumbFrame.on('select', function() {
                var att = postThumbFrame.state().get('selection').first().toJSON();
                $('#news_custom_thumb').val(att.url);
                $('#news_custom_thumb_preview').attr('src', att.url).show();
            });
            postThumbFrame.open();
        });

        $('.jobscout-select-inline-img').on('click', function(e) {
            e.preventDefault();
            var targetInput = $($(this).data('target'));
            var targetPreview = $($(this).data('preview'));
            var inlineFrame = wp.media({
                title: 'Select Inline Image for Article Content',
                button: { text: 'Use this image' },
                multiple: false
            });
            inlineFrame.on('select', function() {
                var att = inlineFrame.state().get('selection').first().toJSON();
                targetInput.val(att.url);
                targetPreview.attr('src', att.url).show();
            });
            inlineFrame.open();
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

    if ( isset( $_POST['news_post_category'] ) ) {
        $cat = sanitize_text_field( wp_unslash( $_POST['news_post_category'] ) );
        update_post_meta( $post_id, '_news_category_label', $cat );
        set_theme_mod( 'news_post_category_' . $post_id, $cat );
    }

    if ( isset( $_POST['news_post_location'] ) ) {
        $loc = sanitize_text_field( wp_unslash( $_POST['news_post_location'] ) );
        update_post_meta( $post_id, 'location', $loc );
        set_theme_mod( 'news_post_location_' . $post_id, $loc );
    }

    if ( isset( $_POST['news_post_date'] ) ) {
        $date_str = sanitize_text_field( wp_unslash( $_POST['news_post_date'] ) );
        update_post_meta( $post_id, '_news_display_date', $date_str );
        set_theme_mod( 'news_post_date_' . $post_id, $date_str );
    }

    if ( isset( $_POST['news_post_excerpt_meta'] ) ) {
        $exc = sanitize_textarea_field( wp_unslash( $_POST['news_post_excerpt_meta'] ) );
        set_theme_mod( 'news_post_excerpt_' . $post_id, $exc );
    }

    if ( isset( $_POST['news_custom_thumb'] ) ) {
        $thumb = esc_url_raw( wp_unslash( $_POST['news_custom_thumb'] ) );
        if ( strpos( $thumb, 'banner-image.jpg' ) !== false ) {
            $thumb = '';
        }
        if ( has_post_thumbnail( $post_id ) ) {
            $feat_url = get_the_post_thumbnail_url( $post_id, 'jobscout-blog' );
            if ( ! $feat_url ) {
                $feat_url = get_the_post_thumbnail_url( $post_id, 'full' );
            }
            delete_post_meta( $post_id, '_news_custom_thumb' );
            set_theme_mod( 'news_post_thumb_' . $post_id, $feat_url );
        } elseif ( ! empty( $thumb ) ) {
            update_post_meta( $post_id, '_news_custom_thumb', $thumb );
            set_theme_mod( 'news_post_thumb_' . $post_id, $thumb );
            $att_id = attachment_url_to_postid( $thumb );
            if ( $att_id ) {
                set_post_thumbnail( $post_id, $att_id );
            }
        } else {
            delete_post_meta( $post_id, '_news_custom_thumb' );
            remove_theme_mod( 'news_post_thumb_' . $post_id );
        }
    }

    if ( isset( $_POST['news_content_img1'] ) ) {
        $img1 = esc_url_raw( wp_unslash( $_POST['news_content_img1'] ) );
        update_post_meta( $post_id, '_news_content_img1', $img1 );
        set_theme_mod( 'news_post_content_img1_' . $post_id, $img1 );
    }

    if ( isset( $_POST['news_content_img2'] ) ) {
        $img2 = esc_url_raw( wp_unslash( $_POST['news_content_img2'] ) );
        update_post_meta( $post_id, '_news_content_img2', $img2 );
        set_theme_mod( 'news_post_content_img2_' . $post_id, $img2 );
    }
}
add_action( 'save_post_post', 'jobscout_save_news_post_card_metabox', 15 );
