# LeVanTien-CMS

# neu dung wamp/xamp: thay doi o config:

clone file wp-config-docker.php ra 1 file wp-config.php
rồi đổi các dòng bên dưới lại

define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );