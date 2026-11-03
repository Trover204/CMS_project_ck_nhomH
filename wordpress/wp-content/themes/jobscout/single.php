<?php
/**
 * The template for displaying all single posts (News Detail Page)
 *
 * Supports full customization via WordPress Customizer (with blue pencil icons)
 * and two-way synchronization with WP Admin -> Posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package JobScout
 */

get_header();

// Find the News page URL for the "All News" breadcrumb link
$news_page = get_page_by_path( 'news' );
if ( ! $news_page ) {
    $news_pages = jobscout_get_page_id_by_template( 'page-news.php' );
    if ( ! empty( $news_pages ) ) {
        $news_page = $news_pages[0];
    }
}
$news_page_url = $news_page ? get_permalink( $news_page->ID ) : home_url( '/index.php/news/' );

$breadcrumb_home   = get_theme_mod( 'news_breadcrumb_home', __( 'Home', 'jobscout' ) );
$breadcrumb_all    = get_theme_mod( 'news_breadcrumb_all', __( 'All News', 'jobscout' ) );
$breadcrumb_detail = get_theme_mod( 'news_breadcrumb_detail', __( 'News Detail', 'jobscout' ) );
$share_label       = get_theme_mod( 'news_detail_share_label', __( 'SHARE', 'jobscout' ) );
$readmore_label    = get_theme_mod( 'news_readmore_text', __( 'Read More', 'jobscout' ) );
$newest_count      = absint( get_theme_mod( 'news_detail_newest_count', 6 ) );
if ( $newest_count < 1 ) {
    $newest_count = 6;
}
?>

<section class="news-detail-section">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
            <?php
            $current_post_id = get_the_ID();
            $thumb_url       = get_the_post_thumbnail_url( $current_post_id, 'jobscout-blog' );
            if ( ! $thumb_url ) {
                $thumb_url = get_the_post_thumbnail_url( $current_post_id, 'full' );
            }
            if ( ! $thumb_url && function_exists( 'jobscout_get_news_post_thumb_url' ) ) {
                $thumb_url = jobscout_get_news_post_thumb_url( $current_post_id );
            }
            if ( ! $thumb_url ) {
                $thumb_url = get_template_directory_uri() . '/images/banner-image.jpg';
            }
            $display_title   = function_exists( 'jobscout_get_news_post_title' ) ? jobscout_get_news_post_title( $current_post_id ) : get_the_title();
            $category_label  = function_exists( 'jobscout_get_news_post_category' ) ? jobscout_get_news_post_category( $current_post_id ) : 'Category Name';
            $location_label  = function_exists( 'jobscout_get_news_post_location' ) ? jobscout_get_news_post_location( $current_post_id ) : 'Ho Chi Minh City';
            $date_display    = function_exists( 'jobscout_get_news_post_date_display' ) ? jobscout_get_news_post_date_display( $current_post_id ) : ( 'Posted: ' . get_the_date( 'M d, Y' ) );
            ?>

            <!-- Breadcrumb -->
            <nav class="news-detail-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'jobscout' ); ?>">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumb-link breadcrumb-home-link"><?php echo esc_html( $breadcrumb_home ); ?></a>
                <span class="breadcrumb-sep">/</span>
                <a href="<?php echo esc_url( $news_page_url ); ?>" class="breadcrumb-link breadcrumb-all-link"><?php echo esc_html( $breadcrumb_all ); ?></a>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current breadcrumb-detail-text"><?php echo esc_html( $breadcrumb_detail ); ?></span>
            </nav>

            <!-- Top Post Header Card -->
            <div class="news-detail-header-card">
                <div class="news-detail-header-left">
                    <div class="news-detail-header-thumb post-id-<?php echo esc_attr( $current_post_id ); ?>-detail-thumb">
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $display_title ); ?>" />
                    </div>
                    <div class="news-detail-header-info">
                        <h1 class="news-detail-title post-id-<?php echo esc_attr( $current_post_id ); ?>-detail-title"><?php echo esc_html( $display_title ); ?></h1>
                        <div class="news-detail-date post-id-<?php echo esc_attr( $current_post_id ); ?>-detail-date">
                            <?php echo esc_html( $date_display ); ?>
                        </div>
                        <div class="news-detail-badges">
                            <span class="news-detail-badge post-id-<?php echo esc_attr( $current_post_id ); ?>-category-badge"><?php echo esc_html( $category_label ); ?></span>
                            <span class="news-detail-badge post-id-<?php echo esc_attr( $current_post_id ); ?>-location-badge"><?php echo esc_html( $location_label ); ?></span>
                        </div>
                    </div>
                </div>
                <div class="news-detail-header-right news-detail-share-wrap">
                    <button type="button" class="news-detail-share-btn" onclick="if(event.target.closest('.customize-partial-edit-shortcut'))return;if(window.jobscoutOpenNewsShareModal){window.jobscoutOpenNewsShareModal();}">
                        <span class="news-detail-share-text"><?php echo esc_html( $share_label ); ?></span>
                    </button>
                </div>
            </div>

            <!-- Main Article Content Card (Left 2/3 Column) -->
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'news-detail-body-card' ); ?>>
                <div class="news-detail-entry-content post-id-<?php echo esc_attr( $current_post_id ); ?>-detail-content">
                    <?php
                    if ( function_exists( 'jobscout_get_news_post_content' ) ) {
                        echo jobscout_get_news_post_content( $current_post_id );
                    } else {
                        the_content();
                    }

                    wp_link_pages( array(
                        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'jobscout' ),
                        'after'  => '</div>',
                    ) );
                    ?>
                </div>
            </article>

        <?php endwhile; ?>

        <!-- Newest Blog Entries Section -->
        <div class="news-detail-newest-section">
            <div class="news-header">
                <h2 class="news-section-title"><?php echo esc_html( get_theme_mod( 'news_section_heading', 'NEWEST BLOG ENTRIES' ) ); ?></h2>
                <span class="news-detail-newest-grid-shortcut" aria-hidden="true"></span>
            </div>

            <div class="news-grid">
                <?php
                $newest_query = new WP_Query( array(
                    'post_type'           => 'post',
                    'post_status'         => 'publish',
                    'posts_per_page'      => $newest_count,
                    'ignore_sticky_posts' => 1,
                ) );

                if ( $newest_query->have_posts() ) :
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
                endif;
                ?>
            </div>
        </div>
    </div>
</section>

<?php // Share News Modal (Facebook, X, Pinterest) ?>
<?php get_template_part( 'template-parts/share-news-modal' ); ?>

<?php // Orange Newsletter Section (Contact Form 7) ?>
<?php get_template_part( 'template-parts/newsletter-cf7' ); ?>

<?php
get_footer();
