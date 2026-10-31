<?php
/**
 * Template part: Share Job Popup Modal
 *
 * Displays a modern, accessible social share modal for job listings
 * with 1-click sharing to Facebook, LinkedIn, Twitter/X, Zalo, WhatsApp,
 * Web Share API support on Mobile, and instant Copy Link with Toast notification.
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$share_job_id    = get_the_ID();
$share_job_title = get_the_title( $share_job_id );
$share_job_url   = get_permalink( $share_job_id );

$encoded_url   = rawurlencode( $share_job_url );
$encoded_title = rawurlencode( $share_job_title );
$encoded_text  = rawurlencode( $share_job_title . ' — ' . $share_job_url );

// Social share endpoints
$share_links = array(
    'facebook' => array(
        'name'  => 'Facebook',
        'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url,
        'color' => '#1877f2',
        'bg'    => '#e7f3ff',
        'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
    ),
    'linkedin' => array(
        'name'  => 'LinkedIn',
        'url'   => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url,
        'color' => '#0a66c2',
        'bg'    => '#e8f4fd',
        'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 8.76a1.62 1.62 0 1 0 0-3.24 1.62 1.62 0 0 0 0 3.24M7.85 18.5V10.1H5.06v8.4h2.79z"/></svg>',
    ),
    'twitter'  => array(
        'name'  => 'Twitter / X',
        'url'   => 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title,
        'color' => '#0f1419',
        'bg'    => '#f3f4f6',
        'svg'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    ),
    'zalo'     => array(
        'name'  => 'Zalo',
        'url'   => 'https://sp.zalo.me/plugins/share?url=' . $encoded_url,
        'color' => '#0068ff',
        'bg'    => '#e6f0ff',
        'svg'   => '<svg width="22" height="22" viewBox="0 0 48 48" fill="currentColor"><path d="M24 4C12.95 4 4 12.95 4 24c0 4.25 1.32 8.19 3.59 11.44L5.17 42.1c-.34 1.14.7 2.18 1.84 1.84l6.66-2.42C16.92 43.14 20.35 44 24 44c11.05 0 20-8.95 20-20S35.05 4 24 4zm-7.6 15.6h15.2c.88 0 1.6.72 1.6 1.6s-.72 1.6-1.6 1.6H19.86l12.54 9.12c.52.38.8.98.8 1.6 0 1.1-.9 2-2 2H16.4c-.88 0-1.6-.72-1.6-1.6s.72-1.6 1.6-1.6h11.34l-12.54-9.12c-.52-.38-.8-.98-.8-1.6 0-1.1.9-2 2-2z"/></svg>',
    ),
    'whatsapp' => array(
        'name'  => 'WhatsApp',
        'url'   => 'https://api.whatsapp.com/send?text=' . $encoded_text,
        'color' => '#25d366',
        'bg'    => '#eafbf0',
        'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.33 7.34 9.04 7.41 8.78 7.69C8.52 7.97 7.78 8.66 7.78 10.07C7.78 11.48 8.81 12.84 8.95 13.03C9.09 13.22 11.08 16.28 14.12 17.6C14.84 17.91 15.41 18.1 15.85 18.24C16.57 18.47 17.23 18.44 17.75 18.36C18.33 18.27 19.53 17.63 19.78 16.92C20.03 16.21 20.03 15.6 19.96 15.48C19.89 15.35 19.7 15.28 19.41 15.13C19.12 14.99 17.7 14.29 17.44 14.19C17.18 14.1 16.99 14.05 16.8 14.34C16.61 14.62 16.06 15.28 15.89 15.48C15.72 15.67 15.55 15.7 15.26 15.55C14.97 15.41 14.03 15.1 12.92 14.11C12.06 13.34 11.48 12.39 11.31 12.1C11.14 11.81 11.29 11.66 11.44 11.51C11.57 11.38 11.73 11.17 11.87 11.01C12.01 10.85 12.06 10.74 12.16 10.55C12.26 10.36 12.21 10.19 12.14 10.05C12.06 9.91 11.51 8.55 11.29 8C11.07 7.46 10.85 7.54 10.68 7.53C10.52 7.52 10.33 7.52 10.14 7.52L9.53 7.34Z"/></svg>',
    ),
);
?>

<!-- Scoped CSS for Share Job Modal (Cache-proof) -->
<style id="jobscout-share-modal-css">
#jobShareModal.job-share-modal {
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

#jobShareModal.job-share-modal.is-open {
    display: flex !important;
}

#jobShareModal .job-share-modal-overlay {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background: rgba(15, 23, 42, 0.72) !important;
    backdrop-filter: blur(4px) !important;
    -webkit-backdrop-filter: blur(4px) !important;
    cursor: pointer !important;
    z-index: 1 !important;
}

#jobShareModal .job-share-modal-dialog {
    position: relative !important;
    background: #ffffff !important;
    width: 100% !important;
    max-width: 500px !important;
    border-radius: 14px !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
    display: flex !important;
    flex-direction: column !important;
    z-index: 2 !important;
    overflow: hidden !important;
    animation: jobShareModalScale 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
}

@keyframes jobShareModalScale {
    from {
        opacity: 0;
        transform: scale(0.93) translateY(10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

#jobShareModal .job-share-modal-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: flex-start !important;
    padding: 20px 24px 16px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    background: #fafbfc !important;
}

#jobShareModal .job-share-modal-title {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    margin: 0 0 4px 0 !important;
    line-height: 1.3 !important;
}

#jobShareModal .job-share-modal-subtitle {
    font-size: 13px !important;
    color: #ea751e !important;
    font-weight: 600 !important;
    display: block !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    max-width: 380px !important;
}

#jobShareModal .job-share-modal-close {
    background: #f1f5f9 !important;
    border: none !important;
    width: 32px !important;
    height: 32px !important;
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

#jobShareModal .job-share-modal-close:hover {
    background: #ea751e !important;
    color: #ffffff !important;
    transform: rotate(90deg) !important;
}

#jobShareModal .job-share-modal-body {
    padding: 22px 24px 24px !important;
}

/* Social Grid */
#jobShareModal .job-share-grid {
    display: grid !important;
    grid-template-columns: repeat(5, 1fr) !important;
    gap: 12px !important;
    margin-bottom: 22px !important;
}

#jobShareModal .job-share-btn {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 8px !important;
    text-decoration: none !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    cursor: pointer !important;
    transition: transform 0.15s ease !important;
}

#jobShareModal .job-share-btn:hover {
    transform: translateY(-3px) !important;
}

#jobShareModal .job-share-icon-wrap {
    width: 48px !important;
    height: 48px !important;
    border-radius: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: box-shadow 0.2s ease !important;
}

#jobShareModal .job-share-btn:hover .job-share-icon-wrap {
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12) !important;
}

#jobShareModal .job-share-label {
    font-size: 11.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    text-align: center !important;
    line-height: 1.2 !important;
}

/* Copy Link Container */
#jobShareModal .job-share-copy-box {
    margin-top: 6px !important;
}

#jobShareModal .job-share-copy-label {
    display: block !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    margin-bottom: 8px !important;
}

#jobShareModal .job-share-copy-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: #f8fafc !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 4px 6px 4px 12px !important;
    transition: border-color 0.2s ease !important;
}

#jobShareModal .job-share-copy-row:focus-within {
    border-color: #ea751e !important;
    box-shadow: 0 0 0 3px rgba(234, 117, 30, 0.12) !important;
}

#jobShareModal .job-share-copy-input {
    flex: 1 !important;
    border: none !important;
    background: transparent !important;
    font-size: 13px !important;
    color: #334155 !important;
    outline: none !important;
    padding: 6px 0 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

#jobShareModal .job-share-copy-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 8px 14px !important;
    background: #ea751e !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 6px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: background 0.2s ease, transform 0.1s ease !important;
    flex-shrink: 0 !important;
    line-height: 1.4 !important;
}

#jobShareModal .job-share-copy-btn:hover {
    background: #d36310 !important;
}

#jobShareModal .job-share-copy-btn.copied {
    background: #10b981 !important;
}

/* Toast Notification */
#jobscoutShareToast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: #1e293b;
    color: #ffffff;
    padding: 10px 20px;
    border-radius: 30px;
    font-size: 13.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    z-index: 10000000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s ease;
    pointer-events: none;
}

#jobscoutShareToast.is-shown {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}

@media (max-width: 520px) {
    #jobShareModal .job-share-grid {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 16px 8px !important;
    }
}
</style>

<!-- Share Job Modal Component -->
<div
    id="jobShareModal"
    class="job-share-modal"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="jobShareModalTitle"
>
    <!-- Overlay -->
    <div class="job-share-modal-overlay" onclick="window.jobscoutCloseShareModal()"></div>

    <!-- Dialog -->
    <div class="job-share-modal-dialog">
        <div class="job-share-modal-header">
            <div>
                <h3 id="jobShareModalTitle" class="job-share-modal-title">
                    <?php esc_html_e( 'Chia Sẻ Tin Tuyển Dụng', 'jobscout' ); ?>
                </h3>
                <span class="job-share-modal-subtitle"><?php echo esc_html( $share_job_title ); ?></span>
            </div>
            <button
                type="button"
                class="job-share-modal-close"
                onclick="window.jobscoutCloseShareModal()"
                aria-label="<?php esc_attr_e( 'Đóng', 'jobscout' ); ?>"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="job-share-modal-body">
            <!-- Social Share Icons Grid -->
            <div class="job-share-grid">
                <?php foreach ( $share_links as $key => $social ) : ?>
                    <a
                        href="<?php echo esc_url( $social['url'] ); ?>"
                        class="job-share-btn job-share-btn-<?php echo esc_attr( $key ); ?>"
                        onclick="window.jobscoutOpenShareWindow(event, '<?php echo esc_url( $social['url'] ); ?>', '<?php echo esc_attr( $social['name'] ); ?>')"
                        aria-label="<?php echo esc_attr( sprintf( __( 'Chia sẻ lên %s', 'jobscout' ), $social['name'] ) ); ?>"
                    >
                        <div class="job-share-icon-wrap" style="color:<?php echo esc_attr( $social['color'] ); ?>;background:<?php echo esc_attr( $social['bg'] ); ?>;">
                            <?php echo $social['svg']; // SVG is safe markup ?>
                        </div>
                        <span class="job-share-label"><?php echo esc_html( $social['name'] ); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Copy Link Box -->
            <div class="job-share-copy-box">
                <label for="jobscoutShareLinkInput" class="job-share-copy-label">
                    <?php esc_html_e( 'Hoặc sao chép đường dẫn bài đăng:', 'jobscout' ); ?>
                </label>
                <div class="job-share-copy-row">
                    <input
                        type="text"
                        id="jobscoutShareLinkInput"
                        class="job-share-copy-input"
                        value="<?php echo esc_url( $share_job_url ); ?>"
                        readonly
                        onclick="this.select()"
                    />
                    <button
                        type="button"
                        id="jobscoutCopyLinkBtn"
                        class="job-share-copy-btn"
                        onclick="window.jobscoutCopyShareLink()"
                    >
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span id="jobscoutCopyBtnText"><?php esc_html_e( 'Sao chép', 'jobscout' ); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Toast -->
<div id="jobscoutShareToast" role="status" aria-live="polite">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"></polyline>
    </svg>
    <span><?php esc_html_e( 'Đã sao chép liên kết vào bộ nhớ tạm!', 'jobscout' ); ?></span>
</div>

<script>
/**
 * Share Job Modal Controller
 */
window.jobscoutShareJobTitle = <?php echo wp_json_encode( $share_job_title ); ?>;
window.jobscoutShareJobUrl   = <?php echo wp_json_encode( $share_job_url ); ?>;

window.jobscoutOpenShareModal = function() {
    // If mobile browser supports Web Share API, give seamless native share experience with fallback
    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    if (isMobile && navigator.share) {
        navigator.share({
            title: window.jobscoutShareJobTitle,
            text: window.jobscoutShareJobTitle,
            url: window.jobscoutShareJobUrl
        }).catch(function(err) {
            // If user dismissed or blocked, open our modal fallback
            if (err.name !== 'AbortError') {
                window.jobscoutShowShareDialog();
            }
        });
        return;
    }

    window.jobscoutShowShareDialog();
};

window.jobscoutShowShareDialog = function() {
    const modal = document.getElementById('jobShareModal');
    if (!modal) return;

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    // Auto-select copy input
    const input = document.getElementById('jobscoutShareLinkInput');
    if (input) {
        setTimeout(function() { input.select(); }, 150);
    }
};

window.jobscoutCloseShareModal = function() {
    const modal = document.getElementById('jobShareModal');
    if (!modal) return;

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
};

// Global hook for button onclick="jobscoutShareJob()"
window.jobscoutShareJob = function() {
    window.jobscoutOpenShareModal();
};

// Popup window helper for social sharing (600x520 centered popup)
window.jobscoutOpenShareWindow = function(event, url, title) {
    if (event) event.preventDefault();
    const width = 600;
    const height = 520;
    const left = (window.innerWidth - width) / 2 + window.screenX;
    const top = (window.innerHeight - height) / 2 + window.screenY;
    const features = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left;
    window.open(url, title, features);
};

// Copy Link with Toast notification
window.jobscoutCopyShareLink = function() {
    const url = window.jobscoutShareJobUrl || window.location.href;
    const btn = document.getElementById('jobscoutCopyLinkBtn');
    const btnText = document.getElementById('jobscoutCopyBtnText');
    const toast = document.getElementById('jobscoutShareToast');

    const onSuccess = function() {
        if (btn) {
            btn.classList.add('copied');
            if (btnText) btnText.textContent = '<?php echo esc_js( __( 'Đã chép! ✓', 'jobscout' ) ); ?>';
        }
        if (toast) {
            toast.classList.add('is-shown');
            setTimeout(function() {
                toast.classList.remove('is-shown');
            }, 2500);
        }
        setTimeout(function() {
            if (btn) {
                btn.classList.remove('copied');
                if (btnText) btnText.textContent = '<?php echo esc_js( __( 'Sao chép', 'jobscout' ) ); ?>';
            }
        }, 2500);
    };

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(onSuccess).catch(function() {
            window.jobscoutFallbackCopy(url, onSuccess);
        });
    } else {
        window.jobscoutFallbackCopy(url, onSuccess);
    }
};

window.jobscoutFallbackCopy = function(text, callback) {
    const tempInput = document.createElement('input');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    try {
        document.execCommand('copy');
        if (callback) callback();
    } catch (e) {
        prompt('Copy job URL:', text);
    }
    document.body.removeChild(tempInput);
};

// Accessibility: ESC key closes modal
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('jobShareModal');
    if (!modal || !modal.classList.contains('is-open')) return;

    if (event.key === 'Escape' || event.keyCode === 27) {
        window.jobscoutCloseShareModal();
    }
});

// Explicit event listener binding on DOM ready for .job-btn-share
document.addEventListener('DOMContentLoaded', function() {
    const shareButtons = document.querySelectorAll('.job-btn-share');
    shareButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.jobscoutOpenShareModal();
        });
    });
});
</script>
