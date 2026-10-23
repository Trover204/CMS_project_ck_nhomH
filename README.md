# LeVanTien-CMS

# neu dung wamp/xamp: thay doi o config:

clone file wp-config-docker.php ra 1 file wp-config.php
rồi đổi các dòng bên dưới lại

define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );

cf7:
<div class="news-cf7-row">
    <div class="news-input-group">
        <span class="news-input-icon news-cf7-icon"></span>
        [email* newsletter-email class:news-newsletter-email-input placeholder "Nhập địa chỉ email của bạn"]
    </div>
    [submit class:news-newsletter-btn "ĐĂNG KÝ"]
</div>