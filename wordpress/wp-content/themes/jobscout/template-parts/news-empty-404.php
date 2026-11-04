<?php
/**
 * Template part: News 404 / Empty State
 *
 * Displayed on the 404 page (non-existent post/URL), the News page when empty,
 * the Home "Newest Blog Entries" section, and the News Detail "Newest Blog Entries" section.
 *
 * Usage:
 * get_template_part( 'template-parts/news-empty-404', null, array( 'context' => 'page' ) );
 * Contexts: '404' (non-existent post / 404 page), 'page' (empty News page), 'home' / 'detail' (compact block).
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$context = isset( $args['context'] ) ? $args['context'] : 'page';
$compact = ( 'home' === $context || 'detail' === $context );

$default_title = ( '404' === $context ) ? 'Post Not Found' : 'No News Found';
$default_desc  = ( '404' === $context )
    ? 'The news article you are looking for might have been removed, had its name changed, or is temporarily unavailable.'
    : 'There are no news posts to display right now. Please check back later for the latest updates from us.';

$title = get_theme_mod( 'news_empty_title', $default_title );
if ( '404' === $context && 'No News Found' === $title ) {
    $title = 'Post Not Found';
}
$desc  = get_theme_mod( 'news_empty_desc', $default_desc );
if ( '404' === $context && 'There are no news posts to display right now. Please check back later for the latest updates from us.' === $desc ) {
    $desc = $default_desc;
}
$btn   = get_theme_mod( 'news_empty_btn_text', 'Back To Home' );

$news_page = get_page_by_path( 'news' );
if ( ! $news_page && function_exists( 'jobscout_get_page_id_by_template' ) ) {
    $news_pages = jobscout_get_page_id_by_template( 'page-news.php' );
    if ( ! empty( $news_pages ) ) {
        $news_page = get_post( $news_pages[0] );
    }
}
$news_page_url = $news_page ? get_permalink( $news_page->ID ) : home_url( '/index.php/news/' );
?>
<div class="news-empty-404<?php echo $compact ? ' news-empty-404--compact' : ''; ?>" role="status">
    <div class="news-empty-404-visual" aria-hidden="true">
        <span class="news-empty-404-digit">4</span>
        <span class="news-empty-404-icon">
            <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <rect x="10" y="12" width="44" height="40" rx="4"></rect>
                <line x1="18" y1="22" x2="46" y2="22"></line>
                <line x1="18" y1="30" x2="38" y2="30"></line>
                <line x1="18" y1="38" x2="42" y2="38"></line>
                <line x1="18" y1="46" x2="30" y2="46"></line>
            </svg>
        </span>
        <span class="news-empty-404-digit">4</span>
    </div>

    <h3 class="news-empty-404-title"><?php echo esc_html( $title ); ?></h3>
    <p class="news-empty-404-desc"><?php echo esc_html( $desc ); ?></p>

    <?php if ( ! $compact ) : ?>
        <div class="news-empty-404-actions">
            <?php if ( '404' === $context ) : ?>
                <a class="news-empty-404-btn" href="<?php echo esc_url( $news_page_url ); ?>"><?php esc_html_e( 'Back To All News', 'jobscout' ); ?></a>
                <a class="news-empty-404-btn news-empty-404-btn--outline" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $btn ); ?></a>
            <?php else : ?>
                <a class="news-empty-404-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $btn ); ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
