<?php
/**
 * Plugin Name: JobScout Flush Rewrite Rules (One-time)
 * Description: Tự động flush rewrite rules khi job_listing permalink bị hỏng. Xóa plugin này sau khi vấn đề được giải quyết.
 * Version: 1.0
 * Author: JobScout Dev
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Flush rewrite rules on init, once per hour using transient.
 * This forces WordPress to rebuild the rewrite table so
 * job_listing permalinks resolve correctly instead of falling
 * back to ?post_type=job_listing&p=xxx.
 */
add_action( 'init', function () {
    if ( get_transient( 'jobscout_rewrite_flushed' ) ) {
        return;
    }

    // Register post types first, then flush.
    flush_rewrite_rules( false );

    // Lock for 1 hour — prevents flush on every request.
    set_transient( 'jobscout_rewrite_flushed', '1', HOUR_IN_SECONDS );
}, 999 );
