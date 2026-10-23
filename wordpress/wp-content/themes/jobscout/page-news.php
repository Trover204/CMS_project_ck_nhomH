<?php
/**
 * Template Name: News Page
 *
 * Template for displaying the News page with customizable banner, post grid, and newsletter section.
 * Supports WordPress Customizer selective refresh (blue pencil edit icons) and WP Admin Dashboard editing.
 *
 * @package JobScout
 */

get_header();

$banner_bg_url  = function_exists( 'jobscout_get_news_banner_bg_url' ) ? jobscout_get_news_banner_bg_url() : get_template_directory_uri() . '/images/view-tokyo-cityscape-night-japan.jpg';
$banner_title   = get_theme_mod( 'news_banner_title', __( 'PDS NEWS', 'jobscout' ) );
$section_title  = get_theme_mod( 'news_section_heading', __( 'NEWEST BLOG ENTRIES', 'jobscout' ) );
$posts_per_page = absint( get_theme_mod( 'news_posts_per_page', 8 ) );
if ( $posts_per_page < 1 ) {
    $posts_per_page = 8;
}
$sort_mode      = get_theme_mod( 'news_posts_orderby', 'date-desc' );
$readmore_label = get_theme_mod( 'news_readmore_text', __( 'Read More', 'jobscout' ) );

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
?>

<!-- News Page Hero Banner -->
<section class="news-banner-section" style="background-image: url('<?php echo esc_url( $banner_bg_url ); ?>');">
    <div class="news-banner-overlay"></div>
    <span class="news-banner-bg-shortcut" aria-hidden="true"></span>
    <div class="container news-banner-container">
        <h1 class="news-banner-title"><?php echo esc_html( $banner_title ); ?></h1>
    </div>
</section>

<!-- News Content Grid -->
<section class="news-main-section">
    <div class="container">
        <div class="news-header">
            <h2 class="news-section-title"><?php echo esc_html( $section_title ); ?></h2>
            <span class="news-grid-shortcut" aria-hidden="true"></span>
        </div>

        <div class="news-grid">
            <?php
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
            $news_query = new WP_Query( array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => $posts_per_page,
                'paged'               => $paged,
                'orderby'             => $orderby,
                'order'               => $order,
                'ignore_sticky_posts' => 1,
            ) );

            if ( $news_query->have_posts() ) :
                while ( $news_query->have_posts() ) : $news_query->the_post();
                    $pid          = get_the_ID();
                    $thumb_url    = function_exists( 'jobscout_get_news_post_thumb_url' ) ? jobscout_get_news_post_thumb_url( $pid ) : get_the_post_thumbnail_url( $pid, 'jobscout-blog' );
                    $card_title   = function_exists( 'jobscout_get_news_post_title' ) ? jobscout_get_news_post_title( $pid ) : get_the_title();
                    $card_excerpt = function_exists( 'jobscout_get_news_post_excerpt' ) ? jobscout_get_news_post_excerpt( $pid ) : wp_trim_words( get_the_excerpt(), 18, '...' );
                    ?>
                    <article id="post-<?php echo esc_attr( $pid ); ?>" <?php post_class( 'news-card' ); ?> onclick="if(event.target.closest('.customize-partial-edit-shortcut'))return;window.location.href='<?php echo esc_url( get_permalink() ); ?>';" style="cursor: pointer;">
                        <div class="news-card-thumb">
                            <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( $card_title ); ?>">
                                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $card_title ); ?>" loading="lazy" />
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
            else :
                ?>
                <p class="news-no-posts"><?php esc_html_e( 'No posts found.', 'jobscout' ); ?></p>
                <?php
            endif;
            ?>
        </div>

        <?php if ( isset( $news_query ) && $news_query->max_num_pages > 1 ) : ?>
            <div class="news-pagination">
                <?php
                echo paginate_links( array(
                    'total'     => $news_query->max_num_pages,
                    'current'   => $paged,
                    'prev_text' => '&laquo; Prev',
                    'next_text' => 'Next &raquo;',
                ) );
                ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php // Orange Newsletter Section (Contact Form 7 / Built-in Fallback) ?>
<?php get_template_part( 'template-parts/newsletter-cf7' ); ?>

<?php
get_footer();
