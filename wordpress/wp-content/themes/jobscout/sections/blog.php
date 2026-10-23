<?php
/**
 * Blog Section - NEWEST BLOG ENTRIES
 * Matching News page style & layout per user request.
 *
 * @package JobScout
 */

$blog_heading   = get_theme_mod( 'blog_section_title', __( 'NEWEST BLOG ENTRIES', 'jobscout' ) );
$ed_blog        = get_theme_mod( 'ed_blog', true );
$readmore_label = get_theme_mod( 'news_readmore_text', __( 'Read More', 'jobscout' ) );

if( $ed_blog ){ ?>
<section id="blog-section" class="news-main-section home-news-section">
    <div class="container">
        <div class="news-header">
            <h2 class="news-section-title"><?php echo esc_html( $blog_heading ); ?></h2>
        </div>

        <div class="news-grid">
            <?php
            $target_ids = array( 977, 979, 981, 982 );
            $args = array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 4,
                'ignore_sticky_posts' => 1,
            );
            $check_targets = get_posts( array( 'post__in' => $target_ids, 'numberposts' => 4, 'post_type' => 'post' ) );
            if ( count( $check_targets ) === 4 ) {
                $args['post__in'] = $target_ids;
                $args['orderby']  = 'post__in';
            }

            $news_query = new WP_Query( $args );

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
            endif;
            ?>
        </div>
    </div>
</section>
<?php 
}
