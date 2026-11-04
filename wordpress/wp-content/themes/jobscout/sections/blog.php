<?php
/**
 * Blog Section - NEWEST BLOG ENTRIES
 * Matching News page style & layout per user request.
 *
 * @package JobScout
 */

$blog_heading   = get_theme_mod( 'blog_section_title', __( 'NEWEST BLOG ENTRIES', 'jobscout' ) );
$ed_blog        = get_theme_mod( 'ed_blog', true );
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

if( $ed_blog ){ ?>
<section id="blog-section" class="news-main-section home-news-section">
    <div class="container">
        <div class="news-header">
            <h2 class="news-section-title"><?php echo esc_html( $blog_heading ); ?></h2>
            <span class="news-grid-shortcut" aria-hidden="true"></span>
        </div>

        <div class="news-grid">
            <?php
            $news_query = new WP_Query( array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 4,
                'orderby'             => $orderby,
                'order'               => $order,
                'ignore_sticky_posts' => 1,
            ) );

            if ( $news_query->have_posts() ) :
                while ( $news_query->have_posts() ) : $news_query->the_post();
                    $pid       = get_the_ID();
                    $thumb_url = get_the_post_thumbnail_url( $pid, 'jobscout-blog' );
                    if ( ! $thumb_url ) {
                        $thumb_url = get_the_post_thumbnail_url( $pid, 'full' );
                    }
                    if ( ! $thumb_url && function_exists( 'jobscout_get_news_post_thumb_url' ) ) {
                        $thumb_url = jobscout_get_news_post_thumb_url( $pid );
                    }
                    if ( ! $thumb_url ) {
                        $thumb_url = get_template_directory_uri() . '/images/banner-image.jpg';
                    }
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
    </div>
</section>
<?php 
}
