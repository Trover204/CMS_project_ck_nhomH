<?php
/**
 * Job Applications Management & Form Builder Module for JobScout
 *
 * Provides:
 * 1. Custom Post Type `job_application` under Job Listings menu
 * 2. Visual Form Builder in wp-admin (Job Listings -> Cấu hình Form ứng tuyển)
 * 3. Dynamic Application form rendering on frontend and detail view in wp-admin
 * 4. Admin List Table columns with status badges, direct CV download, and quick contact
 * 5. Detailed Meta Boxes for viewing applicant info and updating recruitment status/notes
 * 6. AJAX handler for frontend applications with file upload & dynamic data persistence
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

        // Admin menu: Form Builder
        add_action( 'admin_menu', array( $this, 'register_form_builder_menu' ) );
        add_action( 'admin_post_jobscout_save_form_fields', array( $this, 'handle_save_form_fields' ) );

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
            'show_in_menu'       => 'edit.php?post_type=job_listing',
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
     * Helper: Get Configured Form Fields
     */
    public static function get_form_fields() {
        $default_fields = array(
            array(
                'id'          => 'applicant_name',
                'label'       => __( 'Họ và tên', 'jobscout' ),
                'type'        => 'text',
                'placeholder' => __( 'Ví dụ: Nguyễn Văn A', 'jobscout' ),
                'required'    => true,
                'is_system'   => true,
            ),
            array(
                'id'          => 'applicant_email',
                'label'       => __( 'Email liên hệ', 'jobscout' ),
                'type'        => 'email',
                'placeholder' => 'email@example.com',
                'required'    => true,
                'is_system'   => true,
            ),
            array(
                'id'          => 'applicant_phone',
                'label'       => __( 'Số điện thoại', 'jobscout' ),
                'type'        => 'tel',
                'placeholder' => '0912 345 678',
                'required'    => true,
                'is_system'   => true,
            ),
            array(
                'id'          => 'applicant_cv',
                'label'       => __( 'Đính kèm CV (PDF, DOC, DOCX)', 'jobscout' ),
                'type'        => 'file',
                'placeholder' => '',
                'required'    => false,
                'is_system'   => false,
            ),
            array(
                'id'          => 'applicant_message',
                'label'       => __( 'Thư giới thiệu / Lời nhắn', 'jobscout' ),
                'type'        => 'textarea',
                'placeholder' => __( 'Giới thiệu ngắn gọn kinh nghiệm và lý do bạn phù hợp với vị trí này...', 'jobscout' ),
                'required'    => false,
                'is_system'   => false,
            ),
        );

        $saved_fields = get_option( 'jobscout_application_form_fields' );
        if ( ! empty( $saved_fields ) && is_array( $saved_fields ) ) {
            return $saved_fields;
        }

        return $default_fields;
    }

    /**
     * Helper: Field Types Dictionary
     */
    public static function get_field_types() {
        return array(
            'text'     => __( 'Văn bản ngắn (Text)', 'jobscout' ),
            'select'   => __( 'Menu chọn thả xuống (Dropdown: Nam, Nữ...)', 'jobscout' ),
            'radio'    => __( 'Nút chọn tròn (Radio: Nam, Nữ...)', 'jobscout' ),
            'email'    => __( 'Địa chỉ Email (Email)', 'jobscout' ),
            'tel'      => __( 'Số điện thoại (Tel)', 'jobscout' ),
            'number'   => __( 'Số / Mức lương / Năm kinh nghiệm (Number)', 'jobscout' ),
            'url'      => __( 'Đường dẫn liên kết (Link Portfolio, LinkedIn, GitHub...)', 'jobscout' ),
            'textarea' => __( 'Đoạn văn dài (Textarea)', 'jobscout' ),
            'file'     => __( 'Tệp đính kèm (File CV, Bằng cấp)', 'jobscout' ),
        );
    }

    /**
     * 2. Register Form Builder Admin Submenu
     */
    public function register_form_builder_menu() {
        add_submenu_page(
            'edit.php?post_type=job_listing',
            __( 'Cấu hình Form ứng tuyển', 'jobscout' ),
            __( '⚙ Cấu hình Form ứng tuyển', 'jobscout' ),
            'manage_options',
            'jobscout-apply-form-builder',
            array( $this, 'render_form_builder_page' )
        );
    }

    /**
     * Render Form Builder Page in wp-admin
     */
    public function render_form_builder_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Bạn không có quyền truy cập trang này.', 'jobscout' ) );
        }

        $fields      = self::get_form_fields();
        $field_types = self::get_field_types();
        ?>
        <div class="wrap jobscout-form-builder-wrap" style="max-width:1100px;">
            <h1 class="wp-heading-inline" style="font-weight:700;display:flex;align-items:center;gap:8px;">
                <span class="dashicons dashicons-forms" style="font-size:28px;width:28px;height:28px;color:#ea751e;"></span>
                <?php esc_html_e( 'Cấu hình Form Ứng Tuyển (Application Form Builder)', 'jobscout' ); ?>
            </h1>

            <?php if ( ! empty( $_GET['settings-updated'] ) ) : ?>
                <div class="notice notice-success is-dismissible" style="margin-top:14px;">
                    <p><strong><?php esc_html_e( '✓ Đã lưu cấu hình Form ứng tuyển thành công! Tất cả các thay đổi đã được cập nhật ngoài website.', 'jobscout' ); ?></strong></p>
                </div>
            <?php endif; ?>

            <div class="notice notice-info" style="margin-top:14px;background:#f8fafc;border-left-color:#ea751e;">
                <p>
                    <strong>💡 <?php esc_html_e( 'Hướng dẫn:', 'jobscout' ); ?></strong>
                    <?php esc_html_e( 'Bạn có thể chỉnh sửa tên trường, thêm trường dữ liệu mới (như Giới tính, Link Portfolio, Mức lương mong muốn, Địa chỉ...). Với các trường có lựa chọn như Giới tính, bạn chọn loại "Menu chọn thả xuống (Dropdown)" hoặc "Nút chọn tròn (Radio)", sau đó nhập các lựa chọn vào ô bên cạnh cách nhau bằng dấu phẩy (ví dụ: Nam, Nữ). Khi bấm "Lưu Cấu Hình", form ngoài web sẽ tự động hiển thị chính xác.', 'jobscout' ); ?>
                </p>
            </div>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="jobscoutFormBuilderForm" style="margin-top:20px;">
                <input type="hidden" name="action" value="jobscout_save_form_fields" />
                <?php wp_nonce_field( 'jobscout_save_form_fields_action', 'jobscout_form_fields_nonce' ); ?>

                <div class="postbox" style="padding:0;overflow:hidden;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,0.06);">
                    <table class="wp-list-table widefat fixed striped" id="jobscout_fields_table">
                        <thead>
                            <tr style="background:#f1f5f9;">
                                <th style="width:40px;text-align:center;">#</th>
                                <th style="width:24%;font-weight:700;"><?php esc_html_e( 'Tên trường hiển thị (Label) *', 'jobscout' ); ?></th>
                                <th style="width:18%;font-weight:700;"><?php esc_html_e( 'Mã định danh (ID)', 'jobscout' ); ?></th>
                                <th style="width:20%;font-weight:700;"><?php esc_html_e( 'Loại dữ liệu (Type)', 'jobscout' ); ?></th>
                                <th style="width:22%;font-weight:700;"><?php esc_html_e( 'Gợi ý / Các lựa chọn (cách nhau bằng dấu phẩy)', 'jobscout' ); ?></th>
                                <th style="width:9%;text-align:center;font-weight:700;"><?php esc_html_e( 'Bắt buộc?', 'jobscout' ); ?></th>
                                <th style="width:7%;text-align:center;font-weight:700;"><?php esc_html_e( 'Xóa', 'jobscout' ); ?></th>
                            </tr>
                        </thead>
                        <tbody id="jobscout_fields_tbody">
                            <?php foreach ( $fields as $idx => $field ) : 
                                $is_sys = ! empty( $field['is_system'] );
                            ?>
                                <tr class="jobscout-field-row" data-index="<?php echo (int) $idx; ?>">
                                    <td style="text-align:center;color:#94a3b8;font-weight:600;" class="row-num"><?php echo (int) ( $idx + 1 ); ?></td>
                                    <td>
                                        <input
                                            type="text"
                                            name="fields[<?php echo (int) $idx; ?>][label]"
                                            value="<?php echo esc_attr( $field['label'] ); ?>"
                                            class="widefat"
                                            required
                                            placeholder="<?php esc_attr_e( 'Nhập tên trường...', 'jobscout' ); ?>"
                                            style="font-weight:600;"
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            name="fields[<?php echo (int) $idx; ?>][id]"
                                            value="<?php echo esc_attr( $field['id'] ); ?>"
                                            class="widefat code"
                                            <?php if ( $is_sys ) echo 'readonly style="background:#f1f5f9;color:#64748b;"'; ?>
                                            required
                                        />
                                        <?php if ( $is_sys ) : ?>
                                            <input type="hidden" name="fields[<?php echo (int) $idx; ?>][is_system]" value="1" />
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select name="fields[<?php echo (int) $idx; ?>][type]" class="widefat" <?php if ( $is_sys && in_array( $field['id'], array( 'applicant_name', 'applicant_email', 'applicant_phone' ), true ) ) echo 'style="pointer-events:none;background:#f8fafc;"'; ?>>
                                            <?php foreach ( $field_types as $t_key => $t_label ) : ?>
                                                <option value="<?php echo esc_attr( $t_key ); ?>" <?php selected( $field['type'], $t_key ); ?>>
                                                    <?php echo esc_html( $t_label ); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            name="fields[<?php echo (int) $idx; ?>][placeholder]"
                                            value="<?php echo esc_attr( $field['placeholder'] ?? '' ); ?>"
                                            class="widefat"
                                            placeholder="<?php esc_attr_e( 'Gợi ý mẫu khi nhập...', 'jobscout' ); ?>"
                                        />
                                    </td>
                                    <td style="text-align:center;">
                                        <input
                                            type="checkbox"
                                            name="fields[<?php echo (int) $idx; ?>][required]"
                                            value="1"
                                            <?php checked( ! empty( $field['required'] ) ); ?>
                                            <?php if ( $is_sys && in_array( $field['id'], array( 'applicant_name', 'applicant_email', 'applicant_phone' ), true ) ) echo 'onclick="return false;"'; ?>
                                        />
                                    </td>
                                    <td style="text-align:center;">
                                        <?php if ( $is_sys ) : ?>
                                            <span class="dashicons dashicons-lock" style="color:#cbd5e1;" title="<?php esc_attr_e( 'Trường cốt lõi bắt buộc của hệ thống', 'jobscout' ); ?>"></span>
                                        <?php else : ?>
                                            <button type="button" class="button button-link-delete jobscout-remove-field-btn" title="<?php esc_attr_e( 'Xóa trường này', 'jobscout' ); ?>" style="color:#ef4444;">
                                                <span class="dashicons dashicons-trash" style="vertical-align:middle;"></span>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="padding:16px 20px;background:#fafbfc;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
                        <button type="button" id="jobscout_add_field_btn" class="button button-secondary" style="display:inline-flex;align-items:center;gap:6px;font-weight:600;">
                            <span class="dashicons dashicons-plus-alt2" style="color:#ea751e;"></span>
                            <?php esc_html_e( '+ Thêm trường dữ liệu mới', 'jobscout' ); ?>
                        </button>

                        <div style="display:flex;gap:10px;">
                            <button type="submit" class="button button-primary button-large" style="background:#ea751e;border-color:#d36310;font-weight:700;padding:4px 24px;">
                                <?php esc_html_e( 'Lưu Cấu Hình Form', 'jobscout' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script>
        (function() {
            const addBtn = document.getElementById('jobscout_add_field_btn');
            const tbody = document.getElementById('jobscout_fields_tbody');

            const fieldTypesOptions = `<?php
                foreach ( $field_types as $k => $l ) {
                    echo '<option value="' . esc_attr( $k ) . '">' . esc_html( $l ) . '</option>';
                }
            ?>`;

            function updateRowNumbers() {
                const rows = tbody.querySelectorAll('.jobscout-field-row');
                rows.forEach((row, i) => {
                    const numCol = row.querySelector('.row-num');
                    if (numCol) numCol.textContent = i + 1;
                });
            }

            if (addBtn && tbody) {
                addBtn.addEventListener('click', function() {
                    const timestamp = Date.now().toString(36);
                    const newIndex = tbody.querySelectorAll('.jobscout-field-row').length;
                    const autoId = 'custom_field_' + timestamp;

                    const tr = document.createElement('tr');
                    tr.className = 'jobscout-field-row';
                    tr.dataset.index = newIndex;
                    tr.innerHTML = `
                        <td style="text-align:center;color:#94a3b8;font-weight:600;" class="row-num">${newIndex + 1}</td>
                        <td>
                            <input
                                type="text"
                                name="fields[${newIndex}][label]"
                                value=""
                                class="widefat field-label-input"
                                required
                                placeholder="<?php echo esc_js( __( 'Ví dụ: Mức lương mong muốn, Link Portfolio...', 'jobscout' ) ); ?>"
                                style="font-weight:600;"
                            />
                        </td>
                        <td>
                            <input
                                type="text"
                                name="fields[${newIndex}][id]"
                                value="${autoId}"
                                class="widefat code field-id-input"
                                required
                            />
                        </td>
                        <td>
                            <select name="fields[${newIndex}][type]" class="widefat">
                                ${fieldTypesOptions}
                            </select>
                        </td>
                        <td>
                            <input
                                type="text"
                                name="fields[${newIndex}][placeholder]"
                                value=""
                                class="widefat"
                                placeholder="<?php echo esc_js( __( 'Gợi ý khi nhập...', 'jobscout' ) ); ?>"
                            />
                        </td>
                        <td style="text-align:center;">
                            <input type="checkbox" name="fields[${newIndex}][required]" value="1" />
                        </td>
                        <td style="text-align:center;">
                            <button type="button" class="button button-link-delete jobscout-remove-field-btn" title="<?php echo esc_js( __( 'Xóa trường này', 'jobscout' ) ); ?>" style="color:#ef4444;">
                                <span class="dashicons dashicons-trash" style="vertical-align:middle;"></span>
                            </button>
                        </td>
                    `;

                    tbody.appendChild(tr);
                    updateRowNumbers();

                    const typeSelect = tr.querySelector('select');
                    const phInput = tr.querySelector('input[name*="placeholder"]');
                    if (typeSelect && phInput) {
                        typeSelect.addEventListener('change', function() {
                            if (this.value === 'select' || this.value === 'radio') {
                                phInput.placeholder = '<?php echo esc_js( __( 'Ví dụ: Nam, Nữ', 'jobscout' ) ); ?>';
                            } else {
                                phInput.placeholder = '<?php echo esc_js( __( 'Gợi ý khi nhập...', 'jobscout' ) ); ?>';
                            }
                        });
                    }

                    // Auto-slugify label to ID
                    const labelInput = tr.querySelector('.field-label-input');
                    const idInput = tr.querySelector('.field-id-input');
                    if (labelInput && idInput) {
                        labelInput.focus();
                        labelInput.addEventListener('input', function() {
                            const val = labelInput.value.trim().toLowerCase()
                                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                                .replace(/[^a-z0-9]/g, '_')
                                .replace(/_+/g, '_');
                            if (val) {
                                idInput.value = 'app_' + val;
                            }
                        });
                    }
                });
            }

            // Event delegation for delete buttons
            tbody.addEventListener('click', function(e) {
                const btn = e.target.closest('.jobscout-remove-field-btn');
                if (!btn) return;
                const row = btn.closest('.jobscout-field-row');
                if (row && confirm('<?php echo esc_js( __( 'Bạn có chắc chắn muốn xóa trường dữ liệu này khỏi form?', 'jobscout' ) ); ?>')) {
                    row.remove();
                    updateRowNumbers();
                }
            });
        })();
        </script>
        <?php
    }

    /**
     * Handle Save Form Fields Submission
     */
    public function handle_save_form_fields() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Bạn không có quyền thực hiện thao tác này.', 'jobscout' ) );
        }

        check_admin_referer( 'jobscout_save_form_fields_action', 'jobscout_form_fields_nonce' );

        $raw_fields     = isset( $_POST['fields'] ) ? (array) $_POST['fields'] : array();
        $cleaned_fields = array();

        foreach ( $raw_fields as $item ) {
            $label = isset( $item['label'] ) ? sanitize_text_field( wp_unslash( $item['label'] ) ) : '';
            if ( empty( $label ) ) {
                continue;
            }

            $id = isset( $item['id'] ) ? sanitize_key( wp_unslash( $item['id'] ) ) : '';
            if ( empty( $id ) ) {
                $id = sanitize_key( 'custom_' . sanitize_title( $label ) );
            }

            // Ensure unique ID
            $base_id = $id;
            $counter = 1;
            while ( isset( $cleaned_fields[ $id ] ) ) {
                $id = $base_id . '_' . $counter;
                $counter++;
            }

            $type        = isset( $item['type'] ) ? sanitize_key( $item['type'] ) : 'text';
            $placeholder = isset( $item['placeholder'] ) ? sanitize_text_field( wp_unslash( $item['placeholder'] ) ) : '';
            $required    = ! empty( $item['required'] );
            $is_system   = ! empty( $item['is_system'] );

            $cleaned_fields[ $id ] = array(
                'id'          => $id,
                'label'       => $label,
                'type'        => $type,
                'placeholder' => $placeholder,
                'required'    => $required,
                'is_system'   => $is_system,
            );
        }

        update_option( 'jobscout_application_form_fields', array_values( $cleaned_fields ) );

        wp_safe_redirect( add_query_arg( array(
            'post_type'        => 'job_listing',
            'page'             => 'jobscout-apply-form-builder',
            'settings-updated' => 1,
        ), admin_url( 'edit.php' ) ) );
        exit;
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
     * 3. Admin Columns Setup
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

    public function cleanup_legacy_titles() {
        if ( get_transient( 'jobscout_cleaned_app_titles_v3' ) ) {
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
        set_transient( 'jobscout_cleaned_app_titles_v3', 1, DAY_IN_SECONDS );
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
     * 4. Register Meta Boxes in Single Application View
     */
    public function register_meta_boxes() {
        add_meta_box(
            'jobscout_application_details',
            __( '👤 Chi tiết hồ sơ ứng viên (Đầy đủ các trường dữ liệu)', 'jobscout' ),
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

    /**
     * Render Details Meta Box (Dynamically loops through all configured fields)
     */
    public function render_details_meta_box( $post ) {
        wp_nonce_field( 'jobscout_save_application_action', 'jobscout_application_nonce' );

        $job_id    = get_post_meta( $post->ID, '_job_id', true );
        $job_title = get_post_meta( $post->ID, '_job_title', true );
        if ( empty( $job_title ) && $job_id ) {
            $job_title = get_the_title( $job_id );
        }

        $fields = self::get_form_fields();
        ?>
        <table class="form-table" style="margin-top:0;">
            <tr>
                <th style="width:200px;"><label><?php esc_html_e( 'Vị trí ứng tuyển:', 'jobscout' ); ?></label></th>
                <td>
                    <?php if ( $job_id && get_post( $job_id ) ) : ?>
                        <strong style="font-size:15px;color:#1e293b;"><?php echo esc_html( $job_title ); ?></strong>
                        <a href="<?php echo esc_url( get_edit_post_link( $job_id ) ); ?>" target="_blank" class="button button-small" style="margin-left:8px;"><?php esc_html_e( 'Xem tin tuyển dụng ↗', 'jobscout' ); ?></a>
                    <?php else : ?>
                        <strong style="font-size:15px;"><?php echo esc_html( $job_title ? $job_title : '—' ); ?></strong>
                    <?php endif; ?>
                </td>
            </tr>

            <?php foreach ( $fields as $f ) : 
                $f_id   = $f['id'];
                $f_lbl  = $f['label'];
                $f_type = $f['type'];

                if ( 'file' === $f_type ) {
                    $file_url  = get_post_meta( $post->ID, '_' . $f_id . '_url', true );
                    $file_name = get_post_meta( $post->ID, '_' . $f_id . '_name', true );
                    if ( empty( $file_url ) && 'applicant_cv' === $f_id ) {
                        // Fallback
                        $file_url  = get_post_meta( $post->ID, '_applicant_cv_url', true );
                        $file_name = get_post_meta( $post->ID, '_applicant_cv_name', true );
                    }
                    ?>
                    <tr>
                        <th><label><?php echo esc_html( $f_lbl ); ?>:</label></th>
                        <td>
                            <?php if ( ! empty( $file_url ) ) : ?>
                                <a href="<?php echo esc_url( $file_url ); ?>" target="_blank" class="button button-primary" style="display:inline-flex;align-items:center;gap:6px;">
                                    <span class="dashicons dashicons-download" style="line-height:1.4;"></span>
                                    <?php echo esc_html( $file_name ? $file_name : __( 'Mở / Tải file', 'jobscout' ) ); ?>
                                </a>
                            <?php else : ?>
                                <span style="color:#94a3b8;"><?php esc_html_e( '(Không đính kèm tệp)', 'jobscout' ); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                } elseif ( 'textarea' === $f_type ) {
                    $val = get_post_meta( $post->ID, '_' . $f_id, true );
                    ?>
                    <tr>
                        <th><label><?php echo esc_html( $f_lbl ); ?>:</label></th>
                        <td>
                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:12px 14px;white-space:pre-wrap;font-size:13.5px;color:#334155;line-height:1.6;max-height:220px;overflow-y:auto;">
                                <?php echo ! empty( $val ) ? esc_html( $val ) : '<span style="color:#94a3b8;">' . esc_html__( '(Không có)', 'jobscout' ) . '</span>'; ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                } elseif ( 'email' === $f_type ) {
                    $val = get_post_meta( $post->ID, '_' . $f_id, true );
                    ?>
                    <tr>
                        <th><label><?php echo esc_html( $f_lbl ); ?>:</label></th>
                        <td>
                            <?php if ( ! empty( $val ) ) : ?>
                                <a href="<?php echo esc_url( 'mailto:' . $val ); ?>" style="font-size:14px;color:#2563eb;text-decoration:none;">
                                    <?php echo esc_html( $val ); ?>
                                </a>
                            <?php else : ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                } elseif ( 'tel' === $f_type ) {
                    $val = get_post_meta( $post->ID, '_' . $f_id, true );
                    ?>
                    <tr>
                        <th><label><?php echo esc_html( $f_lbl ); ?>:</label></th>
                        <td>
                            <?php if ( ! empty( $val ) ) : ?>
                                <a href="<?php echo esc_url( 'tel:' . $val ); ?>" style="font-size:14px;font-weight:600;color:#059669;text-decoration:none;">
                                    <?php echo esc_html( $val ); ?>
                                </a>
                            <?php else : ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                } elseif ( 'url' === $f_type ) {
                    $val = get_post_meta( $post->ID, '_' . $f_id, true );
                    ?>
                    <tr>
                        <th><label><?php echo esc_html( $f_lbl ); ?>:</label></th>
                        <td>
                            <?php if ( ! empty( $val ) ) : ?>
                                <a href="<?php echo esc_url( $val ); ?>" target="_blank" style="font-size:14px;font-weight:600;color:#2563eb;">
                                    <?php echo esc_html( $val ); ?> ↗
                                </a>
                            <?php else : ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                } else {
                    $val = get_post_meta( $post->ID, '_' . $f_id, true );
                    ?>
                    <tr>
                        <th><label><?php echo esc_html( $f_lbl ); ?>:</label></th>
                        <td>
                            <strong style="font-size:14.5px;color:#1e293b;">
                                <?php echo ! empty( $val ) ? esc_html( $val ) : '<span style="color:#94a3b8;font-weight:normal;">—</span>'; ?>
                            </strong>
                        </td>
                    </tr>
                    <?php
                }
            endforeach; ?>
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
     * 5. AJAX Application Submission Handler (Handles Dynamic Form Fields)
     */
    public function handle_ajax_submission() {
        // Nonce check
        if (
            ! isset( $_POST['jobscout_apply_nonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jobscout_apply_nonce'] ) ), 'jobscout_apply_nonce_action' )
        ) {
            wp_send_json_error( array( 'message' => __( 'Lỗi bảo mật (Token không hợp lệ). Vui lòng tải lại trang.', 'jobscout' ) ) );
        }

        $fields         = self::get_form_fields();
        $submitted_data = array();

        foreach ( $fields as $f ) {
            $fid   = $f['id'];
            $flbl  = $f['label'];
            $ftype = $f['type'];
            $freq  = ! empty( $f['required'] );

            if ( 'file' === $ftype ) {
                $file_url  = '';
                $file_name = '';

                if ( ! empty( $_FILES[ $fid ]['name'] ) ) {
                    $file         = $_FILES[ $fid ];
                    $allowed_exts = array( 'pdf', 'doc', 'docx' );
                    $file_ext     = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

                    if ( ! in_array( $file_ext, $allowed_exts, true ) ) {
                        wp_send_json_error( array( 'message' => sprintf( __( 'Tệp đính kèm "%s" không đúng định dạng. Chỉ chấp nhận PDF, DOC hoặc DOCX.', 'jobscout' ), $flbl ) ) );
                    }

                    if ( $file['size'] > ( 5 * 1024 * 1024 ) ) {
                        wp_send_json_error( array( 'message' => sprintf( __( 'Dung lượng tệp "%s" vượt quá giới hạn 5MB.', 'jobscout' ), $flbl ) ) );
                    }

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
                        $file_url  = $moved_file['url'];
                        $file_name = sanitize_text_field( $file['name'] );
                        $submitted_data[ $fid ] = array(
                            'url'  => $file_url,
                            'name' => $file_name,
                        );
                    } else {
                        wp_send_json_error( array( 'message' => __( 'Lỗi tải tệp: ', 'jobscout' ) . ( $moved_file['error'] ?? '' ) ) );
                    }
                } elseif ( $freq ) {
                    wp_send_json_error( array( 'message' => sprintf( __( 'Vui lòng đính kèm tệp cho trường "%s".', 'jobscout' ), $flbl ) ) );
                }
            } else {
                $val = isset( $_POST[ $fid ] ) ? wp_unslash( $_POST[ $fid ] ) : '';

                if ( $freq && '' === trim( $val ) ) {
                    wp_send_json_error( array( 'message' => sprintf( __( 'Vui lòng điền thông tin trường "%s".', 'jobscout' ), $flbl ) ) );
                }

                if ( 'email' === $ftype && ! empty( $val ) && ! is_email( $val ) ) {
                    wp_send_json_error( array( 'message' => sprintf( __( 'Địa chỉ email ở trường "%s" không hợp lệ.', 'jobscout' ), $flbl ) ) );
                }

                if ( 'url' === $ftype ) {
                    $val = esc_url_raw( $val );
                } elseif ( 'textarea' === $ftype ) {
                    $val = sanitize_textarea_field( $val );
                } else {
                    $val = sanitize_text_field( $val );
                }

                $submitted_data[ $fid ] = $val;
            }
        }

        $job_id    = isset( $_POST['job_id'] ) ? (int) $_POST['job_id'] : 0;
        $job_title = isset( $_POST['job_title'] ) ? sanitize_text_field( wp_unslash( $_POST['job_title'] ) ) : '';
        if ( empty( $job_title ) && $job_id ) {
            $job_title = get_the_title( $job_id );
        }

        // Applicant Name as Post Title
        $applicant_name = ! empty( $submitted_data['applicant_name'] ) ? $submitted_data['applicant_name'] : __( 'Ứng viên', 'jobscout' );

        // Insert job application post
        $app_id = wp_insert_post( array(
            'post_type'   => 'job_application',
            'post_status' => 'publish',
            'post_title'  => $applicant_name,
        ) );

        if ( is_wp_error( $app_id ) || ! $app_id ) {
            wp_send_json_error( array( 'message' => __( 'Không thể lưu hồ sơ ứng tuyển. Vui lòng thử lại.', 'jobscout' ) ) );
        }

        // Save core meta
        update_post_meta( $app_id, '_job_id', $job_id );
        update_post_meta( $app_id, '_job_title', $job_title );
        update_post_meta( $app_id, '_application_status', 'new' );

        // Save dynamic field data
        foreach ( $submitted_data as $fid => $val ) {
            if ( is_array( $val ) ) {
                update_post_meta( $app_id, '_' . $fid . '_url', $val['url'] );
                update_post_meta( $app_id, '_' . $fid . '_name', $val['name'] );
                // Backward compatibility alias for default CV
                if ( 'applicant_cv' === $fid ) {
                    update_post_meta( $app_id, '_applicant_cv_url', $val['url'] );
                    update_post_meta( $app_id, '_applicant_cv_name', $val['name'] );
                }
            } else {
                update_post_meta( $app_id, '_' . $fid, $val );
            }
        }

        // Send email alert to admin
        $admin_email = get_option( 'admin_email' );
        if ( ! empty( $admin_email ) ) {
            $subject = sprintf( __( '[Hồ Sơ Mới] %s ứng tuyển vị trí %s', 'jobscout' ), $applicant_name, $job_title );
            $body_lines = array(
                __( "Chào bạn,\n\nBạn vừa nhận được một hồ sơ ứng tuyển mới trên website:\n", 'jobscout' ),
                sprintf( __( "- Vị trí: %s", 'jobscout' ), $job_title ),
            );

            foreach ( $fields as $f ) {
                $fid = $f['id'];
                if ( isset( $submitted_data[ $fid ] ) ) {
                    if ( is_array( $submitted_data[ $fid ] ) ) {
                        $body_lines[] = sprintf( "- %s: %s", $f['label'], $submitted_data[ $fid ]['url'] );
                    } else {
                        $body_lines[] = sprintf( "- %s: %s", $f['label'], $submitted_data[ $fid ] ? $submitted_data[ $fid ] : 'Không có' );
                    }
                }
            }

            $body_lines[] = sprintf( __( "\nXem chi tiết trong wp-admin: %s\n\nTrân trọng.", 'jobscout' ), admin_url( 'post.php?post=' . $app_id . '&action=edit' ) );
            $body = implode( "\n", $body_lines );

            @wp_mail( $admin_email, $subject, $body );
        }

        wp_send_json_success( array(
            'message'   => sprintf( __( 'Ứng tuyển thành công! Hồ sơ của bạn cho vị trí "%s" đã được ghi nhận. Nhà tuyển dụng sẽ sớm liên hệ với bạn.', 'jobscout' ), $job_title ),
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
