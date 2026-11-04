<?php
/**
 * The template for displaying 404 pages (not found / non-existent News posts)
 *
 * Uses the unified PDS News 404 layout with Breadcrumb, 404 Empty State Card,
 * Newest Blog Entries, and Orange Newsletter section.
 *
 * @package JobScout
 */

get_header();

// Find the News page URL for the "All News" breadcrumb link
$news_page = get_page_by_path( 'news' );
if ( ! $news_page && function_exists( 'jobscout_get_page_id_by_template' ) ) {
    $news_pages = jobscout_get_page_id_by_template( 'page-news.php' );
    if ( ! empty( $news_pages ) ) {
        $news_page = get_post( $news_pages[0] );
    }
}
$news_page_url = $news_page ? get_permalink( $news_page->ID ) : home_url( '/index.php/news/' );

$breadcrumb_home   = get_theme_mod( 'news_breadcrumb_home', 'Home' );
$breadcrumb_all    = get_theme_mod( 'news_breadcrumb_all', 'All News' );
$breadcrumb_detail = get_theme_mod( 'news_breadcrumb_detail', 'News Detail' );
$readmore_label    = get_theme_mod( 'news_readmore_text', 'Read More' );
$sort_mode         = get_theme_mod( 'news_posts_orderby', 'date-desc' );

if ( 'Trang Chủ' === trim( (string) $breadcrumb_home ) ) {
    $breadcrumb_home = 'Home';
}

$orderby = 'date';
$order   = 'DESC';
if ( 'date-asc' === $sort_mode ) {
    $orderby = 'date';
    $order   = 'ASC';
} elseif ( 'title-asc' === $sort_mode ) {
    $orderby = 'title';
    $order   = 'ASC';
} elseif ( 'title-desc' === $sort_mode ) {
    $orderby = 'title';
    $order   = 'DESC';
}

$newest_count = absint( get_theme_mod( 'news_detail_newest_count', 6 ) );
if ( $newest_count < 1 ) {
    $newest_count = 6;
}
?>

<section class="news-detail-section news-404-section">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="news-detail-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'jobscout' ); ?>">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumb-link breadcrumb-home-link"><?php echo esc_html( $breadcrumb_home ); ?></a>
            <span class="breadcrumb-sep">/</span>
            <a href="<?php echo esc_url( $news_page_url ); ?>" class="breadcrumb-link breadcrumb-all-link"><?php echo esc_html( $breadcrumb_all ); ?></a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current breadcrumb-detail-text"><?php echo esc_html( $breadcrumb_detail ); ?></span>
        </nav>

        <!-- 404 Not Found Card -->
        <?php get_template_part( 'template-parts/news-empty-404', null, array( 'context' => '404' ) ); ?>

        <!-- Newest Blog Entries Section -->
        <?php
        $newest_query = new WP_Query( array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => $newest_count,
            'orderby'             => $orderby,
            'order'               => $order,
            'ignore_sticky_posts' => 1,
        ) );

        if ( $newest_query->have_posts() ) :
            ?>
            <div class="news-detail-newest-section">
                <div class="news-header">
                    <h2 class="news-section-title"><?php echo esc_html( get_theme_mod( 'news_section_heading', 'NEWEST BLOG ENTRIES' ) ); ?></h2>
                    <span class="news-detail-newest-grid-shortcut" aria-hidden="true"></span>
                </div>

                <div class="news-grid">
                    <?php
                    while ( $newest_query->have_posts() ) : $newest_query->the_post();
                        $card_pid       = get_the_ID();
                        $card_thumb_url = get_the_post_thumbnail_url( $card_pid, 'jobscout-blog' );
                        if ( ! $card_thumb_url ) {
                            $card_thumb_url = get_the_post_thumbnail_url( $card_pid, 'full' );
                        }
                        if ( ! $card_thumb_url && function_exists( 'jobscout_get_news_post_thumb_url' ) ) {
                            $card_thumb_url = jobscout_get_news_post_thumb_url( $card_pid );
                        }
                        if ( ! $card_thumb_url ) {
                            $card_thumb_url = get_template_directory_uri() . '/images/banner-image.jpg';
                        }
                        $card_title   = function_exists( 'jobscout_get_news_post_title' ) ? jobscout_get_news_post_title( $card_pid ) : get_the_title();
                        $card_excerpt = function_exists( 'jobscout_get_news_post_excerpt' ) ? jobscout_get_news_post_excerpt( $card_pid ) : wp_trim_words( get_the_excerpt(), 18, '...' );
                        ?>
                        <article id="post-<?php echo esc_attr( $card_pid ); ?>" <?php post_class( 'news-card' ); ?> onclick="if(event.target.closest('.customize-partial-edit-shortcut'))return;window.location.href='<?php echo esc_url( get_permalink() ); ?>';" style="cursor: pointer;">
                            <div class="news-card-thumb">
                                <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( $card_title ); ?>">
                                    <img src="<?php echo esc_url( $card_thumb_url ); ?>" alt="<?php echo esc_attr( $card_title ); ?>" loading="lazy" />
                                </a>
                            </div>
                            <div class="news-card-body">
                                <h3 class="news-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php echo esc_html( $card_title ); ?></a>
                                </h3>
                                <div class="news-card-excerpt">
                                    <p>
                                        <a href="<?php the_permalink(); ?>" class="news-card-excerpt-link">
                                            <?php echo esc_html( $card_excerpt ); ?>
                                        </a>
                                    </p>
                                </div>
                                <div class="news-card-action">
                                    <a href="<?php the_permalink(); ?>" class="news-card-readmore"><?php echo esc_html( $readmore_label ); ?></a>
                                </div>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php // Orange Newsletter Section (Contact Form 7) ?>
<?php get_template_part( 'template-parts/newsletter-cf7' ); ?>

<?php
get_footer();
