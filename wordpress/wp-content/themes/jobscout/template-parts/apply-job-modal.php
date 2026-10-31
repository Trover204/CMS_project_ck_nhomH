<?php
/**
 * Template part: Apply Job Popup Modal
 *
 * Displays an accessible, responsive modal dialog with application form
 * supporting Shortcode from Contact Form 7, WPForms, Fluent Forms, or a native fallback form.
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_job_id    = get_the_ID();
$current_job_title = get_the_title( $current_job_id );
$company_name      = get_post_meta( $current_job_id, '_company_name', true );
if ( empty( $company_name ) ) {
    $company_name = get_bloginfo( 'name' );
}

// 1. Resolve Apply Form Shortcode:
// Priority: Per-job meta > Theme Mod (Customizer) > Filter hook
$apply_shortcode = get_post_meta( $current_job_id, '_apply_form_shortcode', true );

if ( empty( $apply_shortcode ) ) {
    $apply_shortcode = get_theme_mod( 'jobscout_apply_form_shortcode', '' );
}

/**
 * Filter the apply form shortcode.
 *
 * @param string $apply_shortcode The shortcode string.
 * @param int    $current_job_id  Current job listing post ID.
 */
$apply_shortcode = apply_filters( 'jobscout_apply_job_shortcode', $apply_shortcode, $current_job_id );
?>

<!-- Scoped Critical CSS for Apply Modal (Cache-proof) -->
<style id="jobscout-apply-modal-css">
body.job-apply-modal-open {
    overflow: hidden !important;
}

/* Modal is strictly hidden by default so it never leaks into page flow */
#jobApplyModal.job-apply-modal {
    display: none !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 9999999 !important;
    padding: 20px !important;
    box-sizing: border-box !important;
    align-items: center !important;
    justify-content: center !important;
}

/* When active, show as fixed overlay */
#jobApplyModal.job-apply-modal.is-open {
    display: flex !important;
}

#jobApplyModal .job-apply-modal-overlay {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background: rgba(15, 23, 42, 0.75) !important;
    backdrop-filter: blur(4px) !important;
    -webkit-backdrop-filter: blur(4px) !important;
    cursor: pointer !important;
    z-index: 1 !important;
}

#jobApplyModal .job-apply-modal-dialog {
    position: relative !important;
    background: #ffffff !important;
    width: 100% !important;
    max-width: 600px !important;
    max-height: 90vh !important;
    border-radius: 14px !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
    display: flex !important;
    flex-direction: column !important;
    z-index: 2 !important;
    overflow: hidden !important;
    animation: jobModalScaleIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
}

@keyframes jobModalScaleIn {
    from {
        opacity: 0;
        transform: scale(0.93) translateY(12px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

#jobApplyModal .job-apply-modal-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: flex-start !important;
    padding: 22px 26px 16px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    background: #fafbfc !important;
}

#jobApplyModal .job-apply-modal-header-content {
    flex: 1 !important;
    padding-right: 16px !important;
}

#jobApplyModal .job-apply-modal-badge {
    display: inline-block !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.5px !important;
    text-transform: uppercase !important;
    color: #ea751e !important;
    background: rgba(234, 117, 30, 0.1) !important;
    padding: 3px 8px !important;
    border-radius: 4px !important;
    margin-bottom: 6px !important;
}

#jobApplyModal .job-apply-modal-title {
    font-size: 20px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    margin: 0 0 6px 0 !important;
    line-height: 1.3 !important;
}

#jobApplyModal .job-apply-target-info {
    font-size: 13px !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 6px !important;
}

#jobApplyModal .job-apply-target-label {
    color: #94a3b8 !important;
}

#jobApplyModal .job-apply-target-title {
    color: #ea751e !important;
    font-weight: 600 !important;
}

#jobApplyModal .job-apply-target-sep {
    color: #cbd5e1 !important;
}

#jobApplyModal .job-apply-target-company {
    color: #475569 !important;
}

#jobApplyModal .job-apply-modal-close {
    background: #f1f5f9 !important;
    border: none !important;
    width: 36px !important;
    height: 36px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #64748b !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    padding: 0 !important;
    flex-shrink: 0 !important;
    line-height: 1 !important;
}

#jobApplyModal .job-apply-modal-close:hover {
    background: #ea751e !important;
    color: #ffffff !important;
    transform: rotate(90deg) !important;
}

#jobApplyModal .job-apply-modal-body {
    padding: 24px 26px 26px !important;
    overflow-y: auto !important;
    max-height: calc(90vh - 110px) !important;
}

#jobApplyModal .job-apply-modal-body::-webkit-scrollbar {
    width: 6px;
}

#jobApplyModal .job-apply-modal-body::-webkit-scrollbar-track {
    background: #f8fafc;
}

#jobApplyModal .job-apply-modal-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#jobApplyModal .job-apply-form-row {
    display: flex !important;
    gap: 16px !important;
}

#jobApplyModal .job-apply-form-row .job-apply-form-group {
    flex: 1 !important;
}

#jobApplyModal .job-apply-form-group {
    margin-bottom: 16px !important;
}

#jobApplyModal .job-apply-label {
    display: block !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #334155 !important;
    margin-bottom: 6px !important;
}

#jobApplyModal .job-apply-label .required {
    color: #ef4444 !important;
}

#jobApplyModal input[type="text"],
#jobApplyModal input[type="email"],
#jobApplyModal input[type="tel"],
#jobApplyModal input[type="url"],
#jobApplyModal textarea {
    width: 100% !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: #1e293b !important;
    box-sizing: border-box !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}

#jobApplyModal input[type="text"]:focus,
#jobApplyModal input[type="email"]:focus,
#jobApplyModal input[type="tel"]:focus,
#jobApplyModal textarea:focus {
    border-color: #ea751e !important;
    box-shadow: 0 0 0 3px rgba(234, 117, 30, 0.15) !important;
    outline: none !important;
}

#jobApplyModal textarea {
    resize: vertical !important;
    min-height: 85px !important;
}

#jobApplyModal .job-apply-file-wrap {
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
}

#jobApplyModal .job-apply-file-input {
    font-size: 13px !important;
    color: #475569 !important;
}

#jobApplyModal .job-apply-file-hint {
    font-size: 12px !important;
    color: #94a3b8 !important;
}

#jobApplyModal .job-apply-form-actions {
    margin-top: 20px !important;
}

#jobApplyModal .job-apply-submit-btn,
#jobApplyModal input[type="submit"] {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    width: 100% !important;
    padding: 13px 24px !important;
    background: #ea751e !important;
    color: #ffffff !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 8px !important;
    cursor: pointer !important;
    letter-spacing: 0.5px !important;
    transition: background 0.2s ease, transform 0.15s ease !important;
    box-shadow: 0 4px 12px rgba(234, 117, 30, 0.25) !important;
}

#jobApplyModal .job-apply-submit-btn:hover,
#jobApplyModal input[type="submit"]:hover {
    background: #d36310 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(234, 117, 30, 0.35) !important;
}

#jobApplyModal .job-apply-notice {
    margin-top: 14px !important;
    padding: 12px 16px !important;
    border-radius: 8px !important;
    font-size: 13.5px !important;
    line-height: 1.5 !important;
}

#jobApplyModal .job-apply-notice-success {
    background: #ecfdf5 !important;
    color: #065f46 !important;
    border: 1px solid #a7f3d0 !important;
}

#jobApplyModal .job-apply-notice-error {
    background: #fef2f2 !important;
    color: #991b1b !important;
    border: 1px solid #fecaca !important;
}

#jobApplyModal .job-apply-admin-hint {
    margin-top: 18px !important;
    padding: 12px 14px !important;
    background: #f8fafc !important;
    border: 1px dashed #cbd5e1 !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: flex-start !important;
    gap: 10px !important;
    font-size: 12.5px !important;
    color: #64748b !important;
    line-height: 1.5 !important;
}

#jobApplyModal .job-apply-admin-hint .dashicons {
    color: #ea751e !important;
    font-size: 18px !important;
    margin-top: 2px !important;
    flex-shrink: 0 !important;
}

#jobApplyModal .job-apply-admin-hint strong {
    color: #334155 !important;
}

#jobApplyModal .job-apply-admin-hint em {
    color: #ea751e !important;
    font-style: normal !important;
    font-weight: 600 !important;
}

@media (max-width: 640px) {
    #jobApplyModal {
        padding: 12px !important;
    }
    #jobApplyModal .job-apply-modal-header {
        padding: 16px 18px 12px !important;
    }
    #jobApplyModal .job-apply-modal-body {
        padding: 16px 18px 20px !important;
    }
    #jobApplyModal .job-apply-form-row {
        flex-direction: column !important;
        gap: 0 !important;
    }
}
</style>

<!-- Apply Job Popup Modal -->
<div
    id="jobApplyModal"
    class="job-apply-modal"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="jobApplyModalTitle"
>
    <!-- Overlay / Backdrop -->
    <div class="job-apply-modal-overlay" onclick="window.jobscoutCloseApplyModal()"></div>

    <!-- Dialog Box -->
    <div class="job-apply-modal-dialog">
        <!-- Modal Header -->
        <div class="job-apply-modal-header">
            <div class="job-apply-modal-header-content">
                <span class="job-apply-modal-badge"><?php esc_html_e( 'ỨNG TUYỂN TRỰC TUYẾN', 'jobscout' ); ?></span>
                <h3 id="jobApplyModalTitle" class="job-apply-modal-title">
                    <?php esc_html_e( 'Nộp Hồ Sơ Ứng Tuyển', 'jobscout' ); ?>
                </h3>
                <div class="job-apply-target-info">
                    <span class="job-apply-target-label"><?php esc_html_e( 'Vị trí:', 'jobscout' ); ?></span>
                    <strong class="job-apply-target-title"><?php echo esc_html( $current_job_title ); ?></strong>
                    <?php if ( ! empty( $company_name ) ) : ?>
                        <span class="job-apply-target-sep">•</span>
                        <span class="job-apply-target-company"><?php echo esc_html( $company_name ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <button
                type="button"
                class="job-apply-modal-close"
                onclick="window.jobscoutCloseApplyModal()"
                aria-label="<?php esc_attr_e( 'Đóng cửa sổ', 'jobscout' ); ?>"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Modal Body: Form Area -->
        <div class="job-apply-modal-body">
            <?php if ( ! empty( $apply_shortcode ) ) : ?>
                <div class="job-apply-shortcode-wrap">
                    <?php echo do_shortcode( $apply_shortcode ); ?>
                </div>
            <?php else : ?>
                <!-- Default Native Application Form (when no shortcode configured yet) -->
                <form id="jobscoutNativeApplyForm" class="job-apply-native-form" method="post" enctype="multipart/form-data" onsubmit="window.jobscoutHandleNativeApply(event)">
                    <input type="hidden" name="action" value="jobscout_submit_application" />
                    <input type="hidden" name="job_id" value="<?php echo esc_attr( $current_job_id ); ?>" />
                    <input type="hidden" name="job_title" value="<?php echo esc_attr( $current_job_title ); ?>" />
                    <?php wp_nonce_field( 'jobscout_apply_nonce_action', 'jobscout_apply_nonce' ); ?>

                    <div class="job-apply-form-group">
                        <label for="apply_fullname" class="job-apply-label">
                            <?php esc_html_e( 'Họ và tên', 'jobscout' ); ?> <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="apply_fullname"
                            name="applicant_name"
                            class="job-apply-input"
                            placeholder="<?php esc_attr_e( 'Ví dụ: Nguyễn Văn A', 'jobscout' ); ?>"
                            required
                        />
                    </div>

                    <div class="job-apply-form-row">
                        <div class="job-apply-form-group">
                            <label for="apply_email" class="job-apply-label">
                                <?php esc_html_e( 'Email liên hệ', 'jobscout' ); ?> <span class="required">*</span>
                            </label>
                            <input
                                type="email"
                                id="apply_email"
                                name="applicant_email"
                                class="job-apply-input"
                                placeholder="<?php esc_attr_e( 'email@example.com', 'jobscout' ); ?>"
                                required
                            />
                        </div>
                        <div class="job-apply-form-group">
                            <label for="apply_phone" class="job-apply-label">
                                <?php esc_html_e( 'Số điện thoại', 'jobscout' ); ?> <span class="required">*</span>
                            </label>
                            <input
                                type="tel"
                                id="apply_phone"
                                name="applicant_phone"
                                class="job-apply-input"
                                placeholder="<?php esc_attr_e( '0912 345 678', 'jobscout' ); ?>"
                                required
                            />
                        </div>
                    </div>

                    <div class="job-apply-form-group">
                        <label for="apply_cv_file" class="job-apply-label">
                            <?php esc_html_e( 'Đính kèm CV (PDF, DOC, DOCX)', 'jobscout' ); ?>
                        </label>
                        <div class="job-apply-file-wrap">
                            <input
                                type="file"
                                id="apply_cv_file"
                                name="applicant_cv"
                                class="job-apply-file-input"
                                accept=".pdf,.doc,.docx"
                            />
                            <span class="job-apply-file-hint"><?php esc_html_e( 'Tối đa 5MB. Định dạng hỗ trợ: PDF, DOC, DOCX', 'jobscout' ); ?></span>
                        </div>
                    </div>

                    <div class="job-apply-form-group">
                        <label for="apply_cover_letter" class="job-apply-label">
                            <?php esc_html_e( 'Thư giới thiệu / Lời nhắn', 'jobscout' ); ?>
                        </label>
                        <textarea
                            id="apply_cover_letter"
                            name="applicant_message"
                            class="job-apply-textarea"
                            rows="4"
                            placeholder="<?php esc_attr_e( 'Giới thiệu ngắn gọn kinh nghiệm và lý do bạn phù hợp với vị trí này...', 'jobscout' ); ?>"
                        ></textarea>
                    </div>

                    <div class="job-apply-form-actions">
                        <button type="submit" class="job-apply-submit-btn" id="jobApplySubmitBtn">
                            <span><?php esc_html_e( 'GỬI HỒ SƠ ỨNG TUYỂN', 'jobscout' ); ?></span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </div>

                    <div id="jobApplyFormNotice" class="job-apply-notice" style="display:none;"></div>
                </form>

                <?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
                    <div class="job-apply-admin-hint">
                        <span class="dashicons dashicons-admin-generic"></span>
                        <div class="job-apply-admin-hint-text">
                            <strong><?php esc_html_e( 'Dành cho Quản trị viên:', 'jobscout' ); ?></strong>
                            <?php esc_html_e( 'Bạn có thể thay thế form này bằng shortcode Contact Form 7 hoặc WPForms trong', 'jobscout' ); ?>
                            <em><?php esc_html_e( 'Tùy biến → Jobs Page Settings → 3. Apply Form Settings', 'jobscout' ); ?></em>
                            <?php esc_html_e( 'hoặc trong khung "Form ứng tuyển" khi chỉnh sửa tin này.', 'jobscout' ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
/**
 * Apply Job Modal Controller & Context Data Injector
 */
window.jobscoutCurrentJobId = <?php echo (int) $current_job_id; ?>;
window.jobscoutCurrentJobTitle = <?php echo wp_json_encode( $current_job_title ); ?>;

window.jobscoutOpenApplyModal = function() {
    const modal = document.getElementById('jobApplyModal');
    if (!modal) return;

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('job-apply-modal-open');
    document.body.style.overflow = 'hidden';

    // Auto-inject Job Context Data (Title & ID) into form fields
    window.jobscoutInjectJobContext(modal);

    // Focus first input or close button
    const firstInput = modal.querySelector('input:not([type="hidden"]), textarea, button.job-apply-modal-close');
    if (firstInput) {
        setTimeout(function() {
            firstInput.focus();
        }, 100);
    }
};

window.jobscoutCloseApplyModal = function() {
    const modal = document.getElementById('jobApplyModal');
    if (!modal) return;

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('job-apply-modal-open');
    document.body.style.overflow = '';
};

window.jobscoutApplyJob = function() {
    window.jobscoutOpenApplyModal();
};

window.jobscoutInjectJobContext = function(modalEl) {
    if (!modalEl) return;

    const titleSelectors = [
        'input[name="job_title"]',
        'input[name="job-title"]',
        'input[name="your-job"]',
        'input[name="job_name"]',
        'input[name="position"]',
        '#cf7-job-title',
        '#job_title',
        '#job-title'
    ];
    const idSelectors = [
        'input[name="job_id"]',
        'input[name="job-id"]',
        'input[name="post_id"]',
        '#cf7-job-id',
        '#job_id',
        '#job-id'
    ];

    modalEl.querySelectorAll(titleSelectors.join(',')).forEach(function(input) {
        if (!input.value) {
            input.value = window.jobscoutCurrentJobTitle;
        }
    });

    modalEl.querySelectorAll(idSelectors.join(',')).forEach(function(input) {
        if (!input.value) {
            input.value = window.jobscoutCurrentJobId;
        }
    });

    modalEl.querySelectorAll('form').forEach(function(form) {
        if (!form.querySelector('input[name="job_title"], input[name="job-title"], input[name="your-job"]')) {
            const hiddenTitle = document.createElement('input');
            hiddenTitle.type = 'hidden';
            hiddenTitle.name = 'job_title';
            hiddenTitle.value = window.jobscoutCurrentJobTitle;
            form.appendChild(hiddenTitle);
        }
        if (!form.querySelector('input[name="job_id"], input[name="job-id"]')) {
            const hiddenId = document.createElement('input');
            hiddenId.type = 'hidden';
            hiddenId.name = 'job_id';
            hiddenId.value = window.jobscoutCurrentJobId;
            form.appendChild(hiddenId);
        }
    });
};

// Accessibility: ESC Key closes modal
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('jobApplyModal');
    if (!modal || !modal.classList.contains('is-open')) return;

    if (event.key === 'Escape' || event.keyCode === 27) {
        window.jobscoutCloseApplyModal();
    }
});

// Explicit event listener binding for all apply buttons on the page
document.addEventListener('DOMContentLoaded', function() {
    const applyButtons = document.querySelectorAll('.job-btn-apply');
    applyButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.jobscoutOpenApplyModal();
        });
    });
});

window.jobscoutHandleNativeApply = function(event) {
    event.preventDefault();
    const form = event.target || document.getElementById('jobscoutNativeApplyForm');
    const btn = document.getElementById('jobApplySubmitBtn');
    const notice = document.getElementById('jobApplyFormNotice');

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span><?php echo esc_js( __( 'ĐANG GỬI HỒ SƠ...', 'jobscout' ) ); ?></span>';
    }
    if (notice) {
        notice.style.display = 'none';
        notice.innerHTML = '';
    }

    const formData = new FormData(form);
    const ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';

    fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(result) {
        if (result && result.success) {
            if (notice) {
                notice.style.display = 'block';
                notice.className = 'job-apply-notice job-apply-notice-success';
                notice.innerHTML = '<strong>' + (result.data && result.data.message ? result.data.message : '<?php echo esc_js( __( 'Ứng tuyển thành công!', 'jobscout' ) ); ?>') + '</strong>';
            }
            if (btn) {
                btn.innerHTML = '<span><?php echo esc_js( __( 'ĐÃ GỬI THÀNH CÔNG', 'jobscout' ) ); ?> ✓</span>';
                btn.style.backgroundColor = '#10b981';
            }
            form.reset();
            setTimeout(function() {
                window.jobscoutCloseApplyModal();
                if (btn) {
                    btn.disabled = false;
                    btn.style.backgroundColor = '';
                    btn.innerHTML = '<span><?php echo esc_js( __( 'GỬI HỒ SƠ ỨNG TUYỂN', 'jobscout' ) ); ?></span>';
                }
                if (notice) {
                    notice.style.display = 'none';
                }
            }, 2500);
        } else {
            const errorMsg = (result && result.data && result.data.message) ? result.data.message : '<?php echo esc_js( __( 'Có lỗi xảy ra khi nộp hồ sơ. Vui lòng kiểm tra lại.', 'jobscout' ) ); ?>';
            if (notice) {
                notice.style.display = 'block';
                notice.className = 'job-apply-notice job-apply-notice-error';
                notice.innerHTML = '<strong>' + errorMsg + '</strong>';
            }
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span><?php echo esc_js( __( 'THỬ LẠI', 'jobscout' ) ); ?></span>';
            }
        }
    })
    .catch(function(err) {
        if (notice) {
            notice.style.display = 'block';
            notice.className = 'job-apply-notice job-apply-notice-error';
            notice.innerHTML = '<strong><?php echo esc_js( __( 'Không thể kết nối đến máy chủ. Vui lòng thử lại.', 'jobscout' ) ); ?></strong>';
        }
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span><?php echo esc_js( __( 'THỬ LẠI', 'jobscout' ) ); ?></span>';
        }
    });
};
</script>