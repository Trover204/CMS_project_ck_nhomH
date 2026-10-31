<?php
/**
 * Job Applications Management Module for JobScout
 *
 * Provides:
 * 1. Custom Post Type `job_application` under Job Listings menu
 * 2. Admin List Table columns with status badges, direct CV download, and quick contact
 * 3. Detailed Meta Boxes for viewing applicant info and updating recruitment status/notes
 * 4. AJAX handler for frontend applications with secure CV file upload & email notification
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JobScout_Job_Applications {

    public function __construct() {
        // Register post type
        add_action( 'init', array( $this, 'register_post_type' ) );

        // Admin columns
        add_filter( 'manage_job_application_posts_columns', array( $this, 'register_columns' ) );
        add_action( 'manage_job_application_posts_custom_column', array( $this, 'render_columns' ), 10, 2 );
        add_filter( 'manage_edit-job_application_sortable_columns', array( $this, 'sortable_columns' ) );

        // Meta boxes for editing application
        add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
        add_action( 'save_post_job_application', array( $this, 'save_meta_boxes' ) );

        // AJAX handlers for application submissions
        add_action( 'wp_ajax_jobscout_submit_application', array( $this, 'handle_ajax_submission' ) );
        add_action( 'wp_ajax_nopriv_jobscout_submit_application', array( $this, 'handle_ajax_submission' ) );

        // Admin styles for balanced table structure
        add_action( 'admin_head', array( $this, 'admin_styles' ) );

        // Add filter by Job in admin list table
        add_action( 'restrict_manage_posts', array( $this, 'filter_by_job' ) );
        add_filter( 'parse_query', array( $this, 'filter_query' ) );

        // 1-Click Quick Status Change handler
        add_action( 'admin_action_jobscout_change_app_status', array( $this, 'handle_quick_status_change' ) );
        add_action( 'admin_notices', array( $this, 'quick_status_admin_notice' ) );

        // Clean candidate name in admin list table
        add_filter( 'the_title', array( $this, 'filter_applicant_title' ), 10, 2 );

        // One-time cleanup for any legacy post title
        add_action( 'admin_init', array( $this, 'cleanup_legacy_titles' ) );
    }

    public function cleanup_legacy_titles() {
        if ( get_transient( 'jobscout_cleaned_app_titles_v2' ) ) {
            return;
        }
        $apps = get_posts( array(
            'post_type'   => 'job_application',
            'numberposts' => 50,
            'post_status' => 'any',
        ) );
        foreach ( $apps as $app ) {
            $name = get_post_meta( $app->ID, '_applicant_name', true );
            if ( ! empty( $name ) && $app->post_title !== $name ) {
                wp_update_post( array(
                    'ID'         => $app->ID,
                    'post_title' => $name,
                ) );
            }
        }
        set_transient( 'jobscout_cleaned_app_titles_v2', 1, DAY_IN_SECONDS );
    }

    /**
     * 1. Register Custom Post Type: job_application
     */
    public function register_post_type() {
        $labels = array(
            'name'               => __( 'Hồ sơ ứng tuyển', 'jobscout' ),
            'singular_name'      => __( 'Hồ sơ ứng tuyển', 'jobscout' ),
            'menu_name'          => __( 'Hồ sơ ứng tuyển', 'jobscout' ),
            'name_admin_bar'     => __( 'Hồ sơ ứng tuyển', 'jobscout' ),
            'add_new'            => __( 'Thêm hồ sơ', 'jobscout' ),
            'add_new_item'       => __( 'Thêm hồ sơ mới', 'jobscout' ),
            'new_item'           => __( 'Hồ sơ mới', 'jobscout' ),
            'edit_item'          => __( 'Chi tiết hồ sơ ứng tuyển', 'jobscout' ),
            'view_item'          => __( 'Xem hồ sơ', 'jobscout' ),
            'all_items'          => __( 'Hồ sơ ứng tuyển', 'jobscout' ),
            'search_items'       => __( 'Tìm kiếm hồ sơ ứng viên...', 'jobscout' ),
            'not_found'          => __( 'Chưa có hồ sơ ứng tuyển nào.', 'jobscout' ),
            'not_found_in_trash' => __( 'Thùng rác trống.', 'jobscout' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=job_listing', // Nested directly under Job Listings menu
            'query_var'          => false,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' ),
        );

        register_post_type( 'job_application', $args );
    }

    /**
     * Status definition helper
     */
    public static function get_statuses() {
        return array(
            'new'        => array(
                'label' => __( 'Mới tiếp nhận', 'jobscout' ),
                'color' => '#2563eb',
                'bg'    => '#eff6ff',
            ),
            'contacted'  => array(
                'label' => __( 'Đã liên hệ', 'jobscout' ),
                'color' => '#7c3aed',
                'bg'    => '#f5f3ff',
            ),
            'interview'  => array(
                'label' => __( 'Hẹn phỏng vấn', 'jobscout' ),
                'color' => '#d97706',
                'bg'    => '#fffbeb',
            ),
            'accepted'   => array(
                'label' => __( 'Trúng tuyển', 'jobscout' ),
                'color' => '#059669',
                'bg'    => '#ecfdf5',
            ),
            'rejected'   => array(
                'label' => __( 'Không phù hợp', 'jobscout' ),
                'color' => '#dc2626',
                'bg'    => '#fef2f2',
            ),
        );
    }

    /**
     * 2. Admin Columns Setup
     */
    public function register_columns( $columns ) {
        $new_columns = array(
            'cb'                => $columns['cb'],
            'title'             => __( 'Ứng viên (Họ và tên)', 'jobscout' ),
            'applied_job'       => __( 'Vị trí ứng tuyển', 'jobscout' ),
            'applicant_contact' => __( 'Liên hệ (Email & SĐT)', 'jobscout' ),
            'applicant_cv'      => __( 'CV / Hồ sơ', 'jobscout' ),
            'app_status'        => __( 'Trạng thái & Duyệt', 'jobscout' ),
            'date'              => __( 'Ngày nộp', 'jobscout' ),
        );
        return $new_columns;
    }

    public function sortable_columns( $columns ) {
        $columns['applied_job'] = 'applied_job';
        $columns['app_status']  = 'app_status';
        return $columns;
    }

    public function render_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'applied_job':
                $job_id    = get_post_meta( $post_id, '_job_id', true );
                $job_title = get_post_meta( $post_id, '_job_title', true );
                if ( empty( $job_title ) && $job_id ) {
                    $job_title = get_the_title( $job_id );
                }
                if ( ! empty( $job_id ) && get_post( $job_id ) ) {
                    echo '<strong><a href="' . esc_url( get_edit_post_link( $job_id ) ) . '">' . esc_html( $job_title ) . '</a></strong>';
                    echo '<br><a href="' . esc_url( get_permalink( $job_id ) ) . '" target="_blank" style="font-size:11.5px;color:#64748b;text-decoration:none;">' . esc_html__( 'Xem bài đăng ↗', 'jobscout' ) . '</a>';
                } elseif ( ! empty( $job_title ) ) {
                    echo '<strong>' . esc_html( $job_title ) . '</strong>';
                } else {
                    echo '<span style="color:#94a3b8;">—</span>';
                }
                break;

            case 'applicant_contact':
                $email = get_post_meta( $post_id, '_applicant_email', true );
                $phone = get_post_meta( $post_id, '_applicant_phone', true );
                if ( ! empty( $email ) ) {
                    echo '<a href="' . esc_url( 'mailto:' . $email ) . '" style="text-decoration:none;display:inline-flex;align-items:center;gap:4px;color:#2563eb;">';
                    echo '<span class="dashicons dashicons-email-alt" style="font-size:15px;line-height:1;color:#64748b;"></span> ' . esc_html( $email );
                    echo '</a><br>';
                }
                if ( ! empty( $phone ) ) {
                    echo '<a href="' . esc_url( 'tel:' . $phone ) . '" style="text-decoration:none;display:inline-flex;align-items:center;gap:4px;color:#059669;font-weight:600;margin-top:2px;">';
                    echo '<span class="dashicons dashicons-phone" style="font-size:15px;line-height:1;"></span> ' . esc_html( $phone );
                    echo '</a>';
                }
                if ( empty( $email ) && empty( $phone ) ) {
                    echo '<span style="color:#94a3b8;">—</span>';
                }
                break;

            case 'applicant_cv':
                $cv_url  = get_post_meta( $post_id, '_applicant_cv_url', true );
                $cv_name = get_post_meta( $post_id, '_applicant_cv_name', true );
                if ( ! empty( $cv_url ) ) {
                    $ext = strtoupper( pathinfo( $cv_name, PATHINFO_EXTENSION ) );
                    $ext_label = $ext ? '(' . $ext . ')' : '';
                    echo '<a href="' . esc_url( $cv_url ) . '" target="_blank" class="button button-small jobscout-cv-btn" title="' . esc_attr( $cv_name ) . '">';
                    echo '<span class="dashicons dashicons-media-document" style="font-size:14px;color:#ea580c;line-height:1.2;"></span> ';
                    echo '<span>' . esc_html__( 'Xem CV', 'jobscout' ) . ' ' . esc_html( $ext_label ) . '</span>';
                    echo '</a>';
                } else {
                    echo '<span style="color:#94a3b8;font-size:12px;">' . esc_html__( 'Không có CV', 'jobscout' ) . '</span>';
                }
                break;

            case 'app_status':
                $status   = get_post_meta( $post_id, '_application_status', true );
                if ( empty( $status ) ) {
                    $status = 'new';
                }
                $statuses = self::get_statuses();
                $current  = $statuses[ $status ] ?? $statuses['new'];

                echo '<div class="jobscout-status-cell">';
                echo '<span class="jobscout-status-badge status-' . esc_attr( $status ) . '" style="color:' . esc_attr( $current['color'] ) . ';background:' . esc_attr( $current['bg'] ) . ';border:1px solid ' . esc_attr( $current['color'] ) . '44;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:700;display:inline-block;letter-spacing:0.3px;">';
                echo esc_html( $current['label'] );
                echo '</span>';

                // Quick Status Change Dropdown
                echo '<div style="margin-top:6px;">';
                echo '<select class="jobscout-quick-status-dropdown" onchange="if(this.value){window.location.href=this.value;}">';
                echo '<option value="" selected disabled>' . esc_html__( '⚡ Đổi trạng thái...', 'jobscout' ) . '</option>';
                foreach ( $statuses as $key => $st ) {
                    if ( $key === $status ) {
                        continue;
                    }
                    $action_url = wp_nonce_url(
                        admin_url( 'admin.php?action=jobscout_change_app_status&post_id=' . $post_id . '&new_status=' . $key ),
                        'jobscout_quick_status_' . $post_id
                    );
                    echo '<option value="' . esc_url( $action_url ) . '">' . esc_html( $st['label'] ) . '</option>';
                }
                echo '</select>';
                echo '</div>';
                echo '</div>';
                break;
        }
    }

    /**
     * Filter applicant title in list table to show clean name
     */
    public function filter_applicant_title( $title, $id ) {
        if ( is_admin() && 'job_application' === get_post_type( $id ) ) {
            $name = get_post_meta( $id, '_applicant_name', true );
            if ( ! empty( $name ) ) {
                return $name;
            }
        }
        return $title;
    }

    /**
     * Filter applications by Job Listing dropdown in admin
     */
    public function filter_by_job() {
        global $typenow;
        if ( 'job_application' === $typenow ) {
            $jobs = get_posts( array(
                'post_type'      => 'job_listing',
                'posts_per_page' => -1,
                'post_status'    => array( 'publish', 'draft' ),
                'orderby'        => 'title',
                'order'          => 'ASC',
            ) );

            $selected = isset( $_GET['filter_job_id'] ) ? (int) $_GET['filter_job_id'] : 0;
            ?>
            <select name="filter_job_id">
                <option value=""><?php esc_html_e( '— Tất cả vị trí tuyển dụng —', 'jobscout' ); ?></option>
                <?php foreach ( $jobs as $job ) : ?>
                    <option value="<?php echo esc_attr( $job->ID ); ?>" <?php selected( $selected, $job->ID ); ?>>
                        <?php echo esc_html( $job->post_title ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
        }
    }

    public function filter_query( $query ) {
        global $pagenow, $typenow;
        if ( is_admin() && 'edit.php' === $pagenow && 'job_application' === $typenow && ! empty( $_GET['filter_job_id'] ) && $query->is_main_query() ) {
            $query->set( 'meta_key', '_job_id' );
            $query->set( 'meta_value', (int) $_GET['filter_job_id'] );
        }
    }

    /**
     * 3. Register Meta Boxes in Single Application View
     */
    public function register_meta_boxes() {
        add_meta_box(
            'jobscout_application_details',
            __( '👤 Chi tiết ứng viên & Thư giới thiệu', 'jobscout' ),
            array( $this, 'render_details_meta_box' ),
            'job_application',
            'normal',
            'high'
        );

        add_meta_box(
            'jobscout_application_recruitment',
            __( '📌 Đánh giá & Trạng thái tuyển dụng', 'jobscout' ),
            array( $this, 'render_recruitment_meta_box' ),
            'job_application',
            'side',
            'high'
        );
    }

    public function render_details_meta_box( $post ) {
        wp_nonce_field( 'jobscout_save_application_action', 'jobscout_application_nonce' );

        $job_id    = get_post_meta( $post->ID, '_job_id', true );
        $job_title = get_post_meta( $post->ID, '_job_title', true );
        if ( empty( $job_title ) && $job_id ) {
            $job_title = get_the_title( $job_id );
        }
        $name    = get_post_meta( $post->ID, '_applicant_name', true );
        $email   = get_post_meta( $post->ID, '_applicant_email', true );
        $phone   = get_post_meta( $post->ID, '_applicant_phone', true );
        $message = get_post_meta( $post->ID, '_applicant_message', true );
        $cv_url  = get_post_meta( $post->ID, '_applicant_cv_url', true );
        $cv_name = get_post_meta( $post->ID, '_applicant_cv_name', true );
        ?>
        <table class="form-table" style="margin-top:0;">
            <tr>
                <th style="width:180px;"><label><?php esc_html_e( 'Vị trí ứng tuyển:', 'jobscout' ); ?></label></th>
                <td>
                    <?php if ( $job_id && get_post( $job_id ) ) : ?>
                        <strong style="font-size:15px;color:#1e293b;"><?php echo esc_html( $job_title ); ?></strong>
                        <a href="<?php echo esc_url( get_edit_post_link( $job_id ) ); ?>" target="_blank" class="button button-small" style="margin-left:8px;"><?php esc_html_e( 'Xem tin tuyển dụng ↗', 'jobscout' ); ?></a>
                    <?php else : ?>
                        <strong style="font-size:15px;"><?php echo esc_html( $job_title ? $job_title : '—' ); ?></strong>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'Họ và tên:', 'jobscout' ); ?></label></th>
                <td><strong style="font-size:15px;"><?php echo esc_html( $name ? $name : $post->post_title ); ?></strong></td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'Email:', 'jobscout' ); ?></label></th>
                <td>
                    <a href="<?php echo esc_url( 'mailto:' . $email ); ?>" style="font-size:14px;">
                        <?php echo esc_html( $email ? $email : '—' ); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'Số điện thoại:', 'jobscout' ); ?></label></th>
                <td>
                    <a href="<?php echo esc_url( 'tel:' . $phone ); ?>" style="font-size:14px;font-weight:600;color:#059669;">
                        <?php echo esc_html( $phone ? $phone : '—' ); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'File CV đính kèm:', 'jobscout' ); ?></label></th>
                <td>
                    <?php if ( ! empty( $cv_url ) ) : ?>
                        <a href="<?php echo esc_url( $cv_url ); ?>" target="_blank" class="button button-primary" style="display:inline-flex;align-items:center;gap:6px;">
                            <span class="dashicons dashicons-download" style="line-height:1.4;"></span>
                            <?php echo esc_html( $cv_name ? $cv_name : __( 'Mở / Tải file CV', 'jobscout' ) ); ?>
                        </a>
                    <?php else : ?>
                        <span style="color:#94a3b8;"><?php esc_html_e( 'Không có file CV đính kèm.', 'jobscout' ); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'Thư giới thiệu / Lời nhắn:', 'jobscout' ); ?></label></th>
                <td>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:14px;white-space:pre-wrap;font-size:13.5px;color:#334155;line-height:1.6;max-height:220px;overflow-y:auto;">
                        <?php echo esc_html( $message ? $message : __( '(Ứng viên không để lại lời nhắn)', 'jobscout' ) ); ?>
                    </div>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_recruitment_meta_box( $post ) {
        $status = get_post_meta( $post->ID, '_application_status', true );
        if ( empty( $status ) ) {
            $status = 'new';
        }
        $notes    = get_post_meta( $post->ID, '_application_notes', true );
        $statuses = self::get_statuses();
        ?>
        <div style="margin-bottom:14px;">
            <label for="app_status_select" style="font-weight:600;display:block;margin-bottom:6px;">
                <?php esc_html_e( 'Trạng thái ứng viên:', 'jobscout' ); ?>
            </label>
            <select name="_application_status" id="app_status_select" class="widefat" style="font-size:13px;padding:6px 8px;">
                <?php foreach ( $statuses as $key => $st ) : ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>>
                        <?php echo esc_html( $st['label'] ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="app_notes_textarea" style="font-weight:600;display:block;margin-bottom:6px;">
                <?php esc_html_e( 'Ghi chú nội bộ (Nhà tuyển dụng):', 'jobscout' ); ?>
            </label>
            <textarea
                name="_application_notes"
                id="app_notes_textarea"
                class="widefat"
                rows="5"
                placeholder="<?php esc_attr_e( 'Ví dụ: Đã gọi điện hẹn phỏng vấn 10h thứ 3 tới; Tiếng Anh tốt; Đạt vòng 1...', 'jobscout' ); ?>"
            ><?php echo esc_textarea( $notes ); ?></textarea>
            <span style="font-size:11.5px;color:#64748b;display:block;margin-top:4px;">
                <?php esc_html_e( 'Chỉ quản trị viên & nhà tuyển dụng mới xem được ghi chú này.', 'jobscout' ); ?>
            </span>
        </div>
        <?php
    }

    /**
     * Save Meta Boxes
     */
    public function save_meta_boxes( $post_id ) {
        if (
            ! isset( $_POST['jobscout_application_nonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jobscout_application_nonce'] ) ), 'jobscout_save_application_action' )
        ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['_application_status'] ) ) {
            $valid_statuses = array_keys( self::get_statuses() );
            $new_status     = sanitize_text_field( wp_unslash( $_POST['_application_status'] ) );
            if ( in_array( $new_status, $valid_statuses, true ) ) {
                update_post_meta( $post_id, '_application_status', $new_status );
            }
        }

        if ( isset( $_POST['_application_notes'] ) ) {
            update_post_meta( $post_id, '_application_notes', sanitize_textarea_field( wp_unslash( $_POST['_application_notes'] ) ) );
        }
    }

    /**
     * 4. AJAX Application Submission Handler (Frontend Modal)
     */
    public function handle_ajax_submission() {
        // Nonce check
        if (
            ! isset( $_POST['jobscout_apply_nonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jobscout_apply_nonce'] ) ), 'jobscout_apply_nonce_action' )
        ) {
            wp_send_json_error( array( 'message' => __( 'Lỗi bảo mật (Token không hợp lệ). Vui lòng tải lại trang.', 'jobscout' ) ) );
        }

        // Validate required fields
        $name  = isset( $_POST['applicant_name'] ) ? sanitize_text_field( wp_unslash( $_POST['applicant_name'] ) ) : '';
        $email = isset( $_POST['applicant_email'] ) ? sanitize_email( wp_unslash( $_POST['applicant_email'] ) ) : '';
        $phone = isset( $_POST['applicant_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['applicant_phone'] ) ) : '';

        if ( empty( $name ) || empty( $email ) || empty( $phone ) ) {
            wp_send_json_error( array( 'message' => __( 'Vui lòng điền đầy đủ Họ tên, Email và Số điện thoại.', 'jobscout' ) ) );
        }

        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => __( 'Địa chỉ email không đúng định dạng.', 'jobscout' ) ) );
        }

        $job_id    = isset( $_POST['job_id'] ) ? (int) $_POST['job_id'] : 0;
        $job_title = isset( $_POST['job_title'] ) ? sanitize_text_field( wp_unslash( $_POST['job_title'] ) ) : '';
        if ( empty( $job_title ) && $job_id ) {
            $job_title = get_the_title( $job_id );
        }
        $message = isset( $_POST['applicant_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['applicant_message'] ) ) : '';

        // Handle CV File Upload (if attached)
        $cv_url  = '';
        $cv_name = '';

        if ( ! empty( $_FILES['applicant_cv']['name'] ) ) {
            $file = $_FILES['applicant_cv'];

            // Validate file extension
            $allowed_exts = array( 'pdf', 'doc', 'docx' );
            $file_ext     = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

            if ( ! in_array( $file_ext, $allowed_exts, true ) ) {
                wp_send_json_error( array( 'message' => __( 'File CV không đúng định dạng. Chỉ chấp nhận tệp PDF, DOC hoặc DOCX.', 'jobscout' ) ) );
            }

            // Max file size: 5MB
            if ( $file['size'] > ( 5 * 1024 * 1024 ) ) {
                wp_send_json_error( array( 'message' => __( 'Dung lượng file CV vượt quá giới hạn (tối đa 5MB).', 'jobscout' ) ) );
            }

            // Use WordPress wp_handle_upload for secure upload
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }

            $upload_overrides = array(
                'test_form' => false,
                'mimes'     => array(
                    'pdf'  => 'application/pdf',
                    'doc'  => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ),
            );

            $moved_file = wp_handle_upload( $file, $upload_overrides );

            if ( $moved_file && ! isset( $moved_file['error'] ) ) {
                $cv_url  = $moved_file['url'];
                $cv_name = sanitize_text_field( $file['name'] );
            } else {
                wp_send_json_error( array( 'message' => __( 'Không thể tải lên file CV: ', 'jobscout' ) . ( $moved_file['error'] ?? '' ) ) );
            }
        }

        // Insert job application post with candidate name only (clean)
        $post_title = $name;
        $app_id     = wp_insert_post( array(
            'post_type'   => 'job_application',
            'post_status' => 'publish',
            'post_title'  => $post_title,
        ) );

        if ( is_wp_error( $app_id ) || ! $app_id ) {
            wp_send_json_error( array( 'message' => __( 'Không thể lưu hồ sơ ứng tuyển. Vui lòng thử lại.', 'jobscout' ) ) );
        }

        // Save application meta
        update_post_meta( $app_id, '_job_id', $job_id );
        update_post_meta( $app_id, '_job_title', $job_title );
        update_post_meta( $app_id, '_applicant_name', $name );
        update_post_meta( $app_id, '_applicant_email', $email );
        update_post_meta( $app_id, '_applicant_phone', $phone );
        update_post_meta( $app_id, '_applicant_message', $message );
        update_post_meta( $app_id, '_applicant_cv_url', $cv_url );
        update_post_meta( $app_id, '_applicant_cv_name', $cv_name );
        update_post_meta( $app_id, '_application_status', 'new' );

        // Send email alert to admin
        $admin_email = get_option( 'admin_email' );
        if ( ! empty( $admin_email ) ) {
            $subject = sprintf( __( '[Hồ Sơ Mới] %s ứng tuyển vị trí %s', 'jobscout' ), $name, $job_title );
            $body    = sprintf(
                __( "Chào bạn,\n\nBạn vừa nhận được một hồ sơ ứng tuyển mới trên website:\n\n- Vị trí: %s\n- Họ tên: %s\n- Email: %s\n- Số điện thoại: %s\n- File CV: %s\n- Lời nhắn: %s\n\nXem chi tiết trong wp-admin: %s\n\nTrân trọng.", 'jobscout' ),
                $job_title,
                $name,
                $email,
                $phone,
                $cv_url ? $cv_url : 'Không đính kèm',
                $message ? $message : 'Không có',
                admin_url( 'post.php?post=' . $app_id . '&action=edit' )
            );
            @wp_mail( $admin_email, $subject, $body );
        }

        wp_send_json_success( array(
            'message'   => sprintf( __( 'Ứng tuyển thành công! Hồ sơ của bạn cho vị trí "%s" đã được ghi nhận. Nhà tuyển dụng sẽ sớm liên hệ qua email hoặc số điện thoại của bạn.', 'jobscout' ), $job_title ),
            'job_title' => $job_title,
        ) );
    }

    /**
     * Admin Styles for Balanced Table Structure
     */
    public function admin_styles() {
        global $typenow;
        if ( 'job_application' === $typenow ) {
            echo '<style>
                .wp-list-table .column-cb { width: 3% !important; }
                .wp-list-table .column-title { width: 18% !important; }
                .wp-list-table .column-applied_job { width: 18% !important; }
                .wp-list-table .column-applicant_contact { width: 22% !important; }
                .wp-list-table .column-applicant_cv { width: 14% !important; }
                .wp-list-table .column-app_status { width: 15% !important; }
                .wp-list-table .column-date { width: 10% !important; }
                .jobscout-status-badge { text-transform: uppercase; letter-spacing: 0.3px; }
                .jobscout-status-cell { display: flex; flex-direction: column; align-items: flex-start; gap: 4px; }
                .jobscout-quick-status-dropdown {
                    font-size: 11.5px !important;
                    height: 26px !important;
                    line-height: 1.4 !important;
                    padding: 2px 6px !important;
                    border-radius: 4px !important;
                    border-color: #cbd5e1 !important;
                    background: #f8fafc !important;
                    cursor: pointer;
                    max-width: 140px;
                    width: 100%;
                }
                .jobscout-cv-btn {
                    display: inline-flex !important;
                    align-items: center !important;
                    gap: 4px !important;
                    font-size: 12px !important;
                    white-space: nowrap !important;
                    padding: 3px 8px !important;
                    height: auto !important;
                    line-height: 1.4 !important;
                    max-width: 130px !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }
            </style>';
        }
    }

    /**
     * Handle 1-Click Status Change Request
     */
    public function handle_quick_status_change() {
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_die( esc_html__( 'Bạn không có quyền thực hiện thao tác này.', 'jobscout' ) );
        }

        $post_id    = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;
        $new_status = isset( $_GET['new_status'] ) ? sanitize_text_field( wp_unslash( $_GET['new_status'] ) ) : '';

        check_admin_referer( 'jobscout_quick_status_' . $post_id );

        $valid_statuses = array_keys( self::get_statuses() );
        if ( $post_id && in_array( $new_status, $valid_statuses, true ) ) {
            update_post_meta( $post_id, '_application_status', $new_status );
            $sendback = add_query_arg(
                array(
                    'post_type'      => 'job_application',
                    'status_updated' => 1,
                    'app_id'         => $post_id,
                ),
                admin_url( 'edit.php' )
            );
            wp_safe_redirect( $sendback );
            exit;
        }

        wp_safe_redirect( admin_url( 'edit.php?post_type=job_application' ) );
        exit;
    }

    /**
     * Admin notice when status updated
     */
    public function quick_status_admin_notice() {
        global $pagenow, $typenow;
        if ( 'edit.php' === $pagenow && 'job_application' === $typenow && ! empty( $_GET['status_updated'] ) ) {
            $app_id = isset( $_GET['app_id'] ) ? (int) $_GET['app_id'] : 0;
            $name   = $app_id ? get_post_meta( $app_id, '_applicant_name', true ) : '';
            $status = $app_id ? get_post_meta( $app_id, '_application_status', true ) : '';
            $st_info = self::get_statuses()[ $status ] ?? null;
            $st_label = $st_info ? $st_info['label'] : $status;

            echo '<div class="notice notice-success is-dismissible"><p>';
            if ( $name ) {
                printf( esc_html__( 'Đã cập nhật trạng thái hồ sơ của ứng viên "%1$s" thành "%2$s".', 'jobscout' ), '<strong>' . esc_html( $name ) . '</strong>', '<strong>' . esc_html( $st_label ) . '</strong>' );
            } else {
                esc_html_e( 'Đã cập nhật trạng thái hồ sơ ứng tuyển thành công.', 'jobscout' );
            }
            echo '</p></div>';
        }
    }
}

new JobScout_Job_Applications();
