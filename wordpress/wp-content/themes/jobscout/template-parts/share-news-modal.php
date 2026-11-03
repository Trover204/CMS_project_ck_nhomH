<?php
/**
 * Template part: Share News Popup Modal
 *
 * Displays a social share modal for news articles (single.php)
 * with 1-click sharing to Facebook, X (Twitter), and Pinterest,
 * plus instant Copy Link with Toast notification.
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$share_post_id    = get_queried_object_id() ? get_queried_object_id() : get_the_ID();
$share_post_title = function_exists( 'jobscout_get_news_post_title' ) ? jobscout_get_news_post_title( $share_post_id ) : get_the_title( $share_post_id );
$share_post_url   = get_permalink( $share_post_id );
$share_post_thumb = function_exists( 'jobscout_get_news_post_thumb_url' ) ? jobscout_get_news_post_thumb_url( $share_post_id ) : get_the_post_thumbnail_url( $share_post_id, 'jobscout-blog' );
if ( ! $share_post_thumb ) {
    $share_post_thumb = get_template_directory_uri() . '/images/banner-image.jpg';
}

$encoded_url   = rawurlencode( $share_post_url );
$encoded_title = rawurlencode( $share_post_title );
$encoded_media = rawurlencode( $share_post_thumb );

// Social share endpoints: Facebook, X, Pinterest
$share_links = array(
    'facebook'  => array(
        'name'  => 'Facebook',
        'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url,
        'color' => '#1877f2',
        'bg'    => '#e7f3ff',
        'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
    ),
    'x'         => array(
        'name'  => 'X',
        'url'   => 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title,
        'color' => '#0f1419',
        'bg'    => '#f3f4f6',
        'svg'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    ),
    'pinterest' => array(
        'name'  => 'Pinterest',
        'url'   => 'https://pinterest.com/pin/create/button/?url=' . $encoded_url . '&media=' . $encoded_media . '&description=' . $encoded_title,
        'color' => '#e60023',
        'bg'    => '#fde8eb',
        'svg'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.367 18.592 0 12.017 0z"/></svg>',
    ),
);
?>

<!-- Scoped CSS for Share News Modal -->
<style id="jobscout-share-news-modal-css">
#newsShareModal.news-share-modal {
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

#newsShareModal.news-share-modal.is-open {
    display: flex !important;
}

#newsShareModal .news-share-modal-overlay {
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

#newsShareModal .news-share-modal-dialog {
    position: relative !important;
    background: #ffffff !important;
    width: 100% !important;
    max-width: 460px !important;
    border-radius: 14px !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
    display: flex !important;
    flex-direction: column !important;
    z-index: 2 !important;
    overflow: hidden !important;
    animation: newsShareModalScale 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
}

@keyframes newsShareModalScale {
    from {
        opacity: 0;
        transform: scale(0.93) translateY(10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

#newsShareModal .news-share-modal-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: flex-start !important;
    padding: 20px 24px 16px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    background: #fafbfc !important;
}

#newsShareModal .news-share-modal-title {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    margin: 0 0 4px 0 !important;
    line-height: 1.3 !important;
}

#newsShareModal .news-share-modal-subtitle {
    font-size: 13px !important;
    color: #ea751e !important;
    font-weight: 600 !important;
    display: block !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    max-width: 340px !important;
}

#newsShareModal .news-share-modal-close {
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

#newsShareModal .news-share-modal-close:hover {
    background: #ea751e !important;
    color: #ffffff !important;
    transform: rotate(90deg) !important;
}

#newsShareModal .news-share-modal-body {
    padding: 24px !important;
}

/* Social Grid (3 columns for Facebook, X, Pinterest) */
#newsShareModal .news-share-grid {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 16px !important;
    margin-bottom: 22px !important;
}

#newsShareModal .news-share-btn {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 10px !important;
    text-decoration: none !important;
    background: #f8fafc !important;
    border: 1px solid #f1f5f9 !important;
    border-radius: 12px !important;
    padding: 16px 10px !important;
    cursor: pointer !important;
    transition: transform 0.15s ease, box-shadow 0.2s ease, border-color 0.2s ease !important;
}

#newsShareModal .news-share-btn:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
    border-color: #e2e8f0 !important;
}

#newsShareModal .news-share-icon-wrap {
    width: 50px !important;
    height: 50px !important;
    border-radius: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: transform 0.2s ease !important;
}

#newsShareModal .news-share-btn:hover .news-share-icon-wrap {
    transform: scale(1.06) !important;
}

#newsShareModal .news-share-label {
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #334155 !important;
    text-align: center !important;
    line-height: 1.2 !important;
}

/* Copy Link Container */
#newsShareModal .news-share-copy-box {
    margin-top: 6px !important;
}

#newsShareModal .news-share-copy-label {
    display: block !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    margin-bottom: 8px !important;
}

#newsShareModal .news-share-copy-row {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: #f8fafc !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 4px 6px 4px 12px !important;
    transition: border-color 0.2s ease !important;
}

#newsShareModal .news-share-copy-row:focus-within {
    border-color: #ea751e !important;
    box-shadow: 0 0 0 3px rgba(234, 117, 30, 0.12) !important;
}

#newsShareModal .news-share-copy-input {
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

#newsShareModal .news-share-copy-btn {
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

#newsShareModal .news-share-copy-btn:hover {
    background: #d36310 !important;
}

#newsShareModal .news-share-copy-btn.copied {
    background: #10b981 !important;
}

/* Toast Notification */
#jobscoutNewsShareToast {
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

#jobscoutNewsShareToast.is-shown {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}
</style>

<!-- Share News Modal Component -->
<div
    id="newsShareModal"
    class="news-share-modal"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="newsShareModalTitle"
>
    <!-- Overlay -->
    <div class="news-share-modal-overlay" onclick="window.jobscoutCloseNewsShareModal()"></div>

    <!-- Dialog -->
    <div class="news-share-modal-dialog">
        <div class="news-share-modal-header">
            <div>
                <h3 id="newsShareModalTitle" class="news-share-modal-title">
                    <?php esc_html_e( 'Share This Article', 'jobscout' ); ?>
                </h3>
                <span class="news-share-modal-subtitle"><?php echo esc_html( $share_post_title ); ?></span>
            </div>
            <button
                type="button"
                class="news-share-modal-close"
                onclick="window.jobscoutCloseNewsShareModal()"
                aria-label="<?php esc_attr_e( 'Close', 'jobscout' ); ?>"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="news-share-modal-body">
            <!-- Social Share Icons Grid: Facebook, X, Pinterest -->
            <div class="news-share-grid">
                <?php foreach ( $share_links as $key => $social ) : ?>
                    <a
                        href="<?php echo esc_url( $social['url'] ); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="news-share-btn news-share-btn-<?php echo esc_attr( $key ); ?>"
                        onclick="window.jobscoutOpenNewsShareWindow(event, '<?php echo esc_url( $social['url'] ); ?>', '<?php echo esc_attr( $social['name'] ); ?>')"
                        aria-label="<?php echo esc_attr( sprintf( __( 'Share on %s', 'jobscout' ), $social['name'] ) ); ?>"
                    >
                        <div class="news-share-icon-wrap" style="color:<?php echo esc_attr( $social['color'] ); ?>;background:<?php echo esc_attr( $social['bg'] ); ?>;">
                            <?php echo $social['svg']; // Safe SVG markup ?>
                        </div>
                        <span class="news-share-label"><?php echo esc_html( $social['name'] ); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Copy Link Box -->
            <div class="news-share-copy-box">
                <label for="jobscoutNewsShareLinkInput" class="news-share-copy-label">
                    <?php esc_html_e( 'Or copy article link:', 'jobscout' ); ?>
                </label>
                <div class="news-share-copy-row">
                    <input
                        type="text"
                        id="jobscoutNewsShareLinkInput"
                        class="news-share-copy-input"
                        value="<?php echo esc_url( $share_post_url ); ?>"
                        readonly
                        onclick="this.select()"
                    />
                    <button
                        type="button"
                        id="jobscoutNewsCopyLinkBtn"
                        class="news-share-copy-btn"
                        onclick="window.jobscoutCopyNewsShareLink()"
                    >
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span id="jobscoutNewsCopyBtnText"><?php esc_html_e( 'Copy', 'jobscout' ); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Toast -->
<div id="jobscoutNewsShareToast" role="status" aria-live="polite">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"></polyline>
    </svg>
    <span><?php esc_html_e( 'Link copied to clipboard!', 'jobscout' ); ?></span>
</div>

<script>
/**
 * Share News Modal Controller
 */
window.jobscoutShareNewsTitle = <?php echo wp_json_encode( $share_post_title ); ?>;
window.jobscoutShareNewsUrl   = <?php echo wp_json_encode( $share_post_url ); ?>;

window.jobscoutOpenNewsShareModal = function() {
    const modal = document.getElementById('newsShareModal');
    if (!modal) return;

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    const input = document.getElementById('jobscoutNewsShareLinkInput');
    if (input) {
        setTimeout(function() { input.select(); }, 150);
    }
};

window.jobscoutCloseNewsShareModal = function() {
    const modal = document.getElementById('newsShareModal');
    if (!modal) return;

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
};

window.jobscoutOpenNewsShareWindow = function(event, url, title) {
    if (event) event.preventDefault();
    const width = 600;
    const height = 520;
    const left = (window.innerWidth - width) / 2 + window.screenX;
    const top = (window.innerHeight - height) / 2 + window.screenY;
    const features = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=' + width + ',height=' + height + ',top=' + top + ',left=' + left;
    window.open(url, title, features);
};

window.jobscoutCopyNewsShareLink = function() {
    const url = window.jobscoutShareNewsUrl || window.location.href;
    const btn = document.getElementById('jobscoutNewsCopyLinkBtn');
    const btnText = document.getElementById('jobscoutNewsCopyBtnText');
    const toast = document.getElementById('jobscoutNewsShareToast');

    const onSuccess = function() {
        if (btn) {
            btn.classList.add('copied');
            if (btnText) btnText.textContent = '<?php echo esc_js( __( 'Copied! ✓', 'jobscout' ) ); ?>';
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
                if (btnText) btnText.textContent = '<?php echo esc_js( __( 'Copy', 'jobscout' ) ); ?>';
            }
        }, 2500);
    };

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(onSuccess).catch(function() {
            window.jobscoutNewsFallbackCopy(url, onSuccess);
        });
    } else {
        window.jobscoutNewsFallbackCopy(url, onSuccess);
    }
};

window.jobscoutNewsFallbackCopy = function(text, callback) {
    const tempInput = document.createElement('input');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    try {
        document.execCommand('copy');
        if (callback) callback();
    } catch (e) {
        prompt('Copy URL:', text);
    }
    document.body.removeChild(tempInput);
};

document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('newsShareModal');
    if (!modal || !modal.classList.contains('is-open')) return;

    if (event.key === 'Escape' || event.keyCode === 27) {
        window.jobscoutCloseNewsShareModal();
    }
});
</script>
