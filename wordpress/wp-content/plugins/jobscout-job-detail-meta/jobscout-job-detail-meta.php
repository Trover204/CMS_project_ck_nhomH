<?php
/**
 * Plugin Name: JobScout Job Detail Meta Boxes
 * Description: Thêm các meta box vào trang chỉnh sửa Job Listing trong wp-admin để quản lý nội dung trang chi tiết: mô tả công ty, kỹ năng, phúc lợi, địa điểm, đánh giá nhân viên và ảnh công ty.
 * Version: 1.0.0
 * Author: JobScout Dev
 * Text Domain: jobscout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ============================================================
// 1. Enqueue Media Uploader for admin
// ============================================================
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    global $post;
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }
    if ( ! isset( $post ) || 'job_listing' !== get_post_type( $post ) ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_style(
        'jobscout-meta-boxes',
        plugin_dir_url( __FILE__ ) . 'assets/meta-boxes.css',
        array(),
        '1.0.0'
    );
    wp_enqueue_script(
        'jobscout-meta-boxes',
        plugin_dir_url( __FILE__ ) . 'assets/meta-boxes.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );
} );

// ============================================================
// 2. Register all meta boxes
// ============================================================
add_action( 'add_meta_boxes', function () {
    $screens = array( 'job_listing' );

    add_meta_box(
        'jobscout_company_overview',
        __( '📝 Tổng quan về công ty (Overview about Company)', 'jobscout' ),
        'jobscout_render_company_overview_meta_box',
        $screens,
        'normal',
        'high'
    );

    add_meta_box(
        'jobscout_key_skills',
        __( '🎯 Kỹ năng yêu cầu (Our Key Skills)', 'jobscout' ),
        'jobscout_render_key_skills_meta_box',
        $screens,
        'normal',
        'high'
    );

    add_meta_box(
        'jobscout_why_work_here',
        __( '❤️ Vì sao nên làm việc ở đây (Why You\'ll Love Working Here)', 'jobscout' ),
        'jobscout_render_why_work_meta_box',
        $screens,
        'normal',
        'high'
    );

    add_meta_box(
        'jobscout_job_location_detail',
        __( '📍 Mô tả địa điểm (Location Detail)', 'jobscout' ),
        'jobscout_render_location_detail_meta_box',
        $screens,
        'normal',
        'default'
    );

    add_meta_box(
        'jobscout_staff_rating',
        __( '⭐ Đánh giá nhân viên (Staff Rating)', 'jobscout' ),
        'jobscout_render_staff_rating_meta_box',
        $screens,
        'side',
        'default'
    );

    add_meta_box(
        'jobscout_company_photos',
        __( '🖼️ Ảnh công ty (Company Photos)', 'jobscout' ),
        'jobscout_render_company_photos_meta_box',
        $screens,
        'normal',
        'default'
    );
} );

// ============================================================
// 3. Render: Company Overview
// ============================================================
function jobscout_render_company_overview_meta_box( $post ) {
    wp_nonce_field( 'jobscout_job_detail_save', 'jobscout_job_detail_nonce' );
    $value = get_post_meta( $post->ID, '_company_overview', true );
    ?>
    <p class="jsmb-desc"><?php esc_html_e( 'Nội dung hiển thị trong phần "Overview about Company" trên trang chi tiết job.', 'jobscout' ); ?></p>
    <?php
    wp_editor(
        wp_kses_post( $value ),
        'company_overview_editor',
        array(
            'textarea_name' => '_company_overview',
            'textarea_rows' => 8,
            'media_buttons' => false,
            'teeny'         => false,
        )
    );
}

// ============================================================
// 4. Render: Key Skills
// ============================================================
function jobscout_render_key_skills_meta_box( $post ) {
    $value = get_post_meta( $post->ID, '_key_skills', true );
    ?>
    <p class="jsmb-desc"><?php esc_html_e( 'Nội dung hiển thị trong phần "Our Key Skills".', 'jobscout' ); ?></p>
    <?php
    wp_editor(
        wp_kses_post( $value ),
        'key_skills_editor',
        array(
            'textarea_name' => '_key_skills',
            'textarea_rows' => 6,
            'media_buttons' => false,
            'teeny'         => false,
        )
    );
}

// ============================================================
// 5. Render: Why Work Here
// ============================================================
function jobscout_render_why_work_meta_box( $post ) {
    $value = get_post_meta( $post->ID, '_why_work_here', true );
    ?>
    <p class="jsmb-desc"><?php esc_html_e( 'Nội dung hiển thị trong phần "Why You\'ll Love Working Here".', 'jobscout' ); ?></p>
    <?php
    wp_editor(
        wp_kses_post( $value ),
        'why_work_here_editor',
        array(
            'textarea_name' => '_why_work_here',
            'textarea_rows' => 6,
            'media_buttons' => false,
            'teeny'         => false,
        )
    );
}

// ============================================================
// 6. Render: Location Detail
// ============================================================
function jobscout_render_location_detail_meta_box( $post ) {
    $value = get_post_meta( $post->ID, '_location_detail', true );
    ?>
    <p class="jsmb-desc"><?php esc_html_e( 'Mô tả chi tiết địa điểm làm việc (khác với địa chỉ ngắn trong _job_location).', 'jobscout' ); ?></p>
    <?php
    wp_editor(
        wp_kses_post( $value ),
        'location_detail_editor',
        array(
            'textarea_name' => '_location_detail',
            'textarea_rows' => 4,
            'media_buttons' => false,
            'teeny'         => true,
        )
    );
}

// ============================================================
// 7. Render: Staff Rating
// ============================================================
function jobscout_render_staff_rating_meta_box( $post ) {
    $rating = get_post_meta( $post->ID, '_staff_rating', true );
    if ( '' === $rating ) {
        $rating = '4.0';
    }
    ?>
    <p class="jsmb-desc"><?php esc_html_e( 'Điểm đánh giá nhân viên (0.0 – 5.0).', 'jobscout' ); ?></p>
    <div class="jsmb-field">
        <label for="jsmb_staff_rating"><?php esc_html_e( 'Điểm đánh giá:', 'jobscout' ); ?></label>
        <input
            type="number"
            id="jsmb_staff_rating"
            name="_staff_rating"
            value="<?php echo esc_attr( $rating ); ?>"
            min="0"
            max="5"
            step="0.1"
            style="width:80px;"
        />
        <span class="jsmb-hint"><?php esc_html_e( '/ 5.0', 'jobscout' ); ?></span>
    </div>
    <div class="jsmb-rating-preview" id="jsmb_rating_preview">
        <?php echo jobscout_render_stars_html( (float) $rating ); ?>
    </div>
    <?php
}

/**
 * Generate star SVGs for preview (1–5 filled/empty).
 */
function jobscout_render_stars_html( $rating ) {
    $rating = max( 0, min( 5, (float) $rating ) );
    $html   = '<span class="jsmb-stars">';
    for ( $i = 1; $i <= 5; $i++ ) {
        if ( $rating >= $i ) {
            $html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ea751e" width="18" height="18"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
        } else {
            $html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ea751e" stroke-width="2" width="18" height="18"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
        }
    }
    $html .= '</span> <span class="jsmb-rating-val">' . esc_html( number_format( $rating, 1 ) ) . '</span>';
    return $html;
}

// ============================================================
// 8. Render: Company Photos Gallery
// ============================================================
function jobscout_render_company_photos_meta_box( $post ) {
    $photos = get_post_meta( $post->ID, '_company_photos', true );
    if ( ! is_array( $photos ) ) {
        $photos = array();
    }
    ?>
    <p class="jsmb-desc">
        <?php esc_html_e( 'Ảnh hiển thị trong widget "Company Photos" và lightbox trên trang chi tiết job. Ảnh đầu tiên là ảnh đại diện.', 'jobscout' ); ?>
    </p>

    <div id="jsmb_photos_preview" class="jsmb-photos-grid">
        <?php foreach ( $photos as $url ) : ?>
            <div class="jsmb-photo-item" data-url="<?php echo esc_attr( $url ); ?>">
                <img src="<?php echo esc_url( $url ); ?>" alt="" />
                <button type="button" class="jsmb-photo-remove" title="<?php esc_attr_e( 'Xóa ảnh', 'jobscout' ); ?>">×</button>
            </div>
        <?php endforeach; ?>
    </div>

    <input type="hidden" id="jsmb_photos_data" name="_company_photos_json" value="<?php echo esc_attr( wp_json_encode( $photos ) ); ?>" />

    <button type="button" id="jsmb_add_photos_btn" class="button button-primary" style="margin-top:10px;">
        <?php esc_html_e( '+ Thêm ảnh', 'jobscout' ); ?>
    </button>
    <?php
}

// ============================================================
// 9. Save all meta
// ============================================================
add_action( 'save_post_job_listing', function ( $post_id ) {
    // Nonce check
    if (
        ! isset( $_POST['jobscout_job_detail_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['jobscout_job_detail_nonce'] ) ),
            'jobscout_job_detail_save'
        )
    ) {
        return;
    }

    // Skip autosave / bulk edit
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // --- Text / HTML fields ---
    $html_fields = array(
        '_company_overview',
        '_key_skills',
        '_why_work_here',
        '_location_detail',
    );
    foreach ( $html_fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta(
                $post_id,
                $field,
                wp_kses_post( wp_unslash( $_POST[ $field ] ) )
            );
        }
    }

    // --- Staff Rating (numeric 0–5) ---
    if ( isset( $_POST['_staff_rating'] ) ) {
        $rating = (float) $_POST['_staff_rating'];
        $rating = max( 0, min( 5, $rating ) );
        update_post_meta( $post_id, '_staff_rating', $rating );
    }

    // --- Company Photos (JSON array of URLs) ---
    if ( isset( $_POST['_company_photos_json'] ) ) {
        $raw    = sanitize_text_field( wp_unslash( $_POST['_company_photos_json'] ) );
        $photos = json_decode( $raw, true );
        if ( ! is_array( $photos ) ) {
            $photos = array();
        }
        // Sanitize every URL
        $photos = array_values(
            array_filter(
                array_map( 'esc_url_raw', $photos )
            )
        );
        update_post_meta( $post_id, '_company_photos', $photos );
    }
} );
