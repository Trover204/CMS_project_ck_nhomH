<?php
/**
 * Blog Section - NEWEST BLOG ENTRIES
 * Matching News page style & layout per user request.
 *
 * @package JobScout
 */

$blog_heading = get_theme_mod( 'blog_section_title', __( 'NEWEST BLOG ENTRIES', 'jobscout' ) );
$ed_blog      = get_theme_mod( 'ed_blog', true );

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
                $theme_img_dir = get_template_directory_uri() . '/images/';
                $custom_post_images = array(
                    977 => $theme_img_dir . 'photo-1528360983277-13d401cdc186.jpg',
                    979 => $theme_img_dir . 'photo-1493976040374-85c8e12f0c0e.jpg',
                    981 => $theme_img_dir . 'photo-1524413840807-0c3cb6fa808d.jpg',
                    982 => $theme_img_dir . 'photo-1503899036084-c55cdd92da26.jpg',
                );

                while ( $news_query->have_posts() ) : $news_query->the_post();
                    $pid = get_the_ID();
                    $thumb_url = isset( $custom_post_images[ $pid ] ) ? $custom_post_images[ $pid ] : get_the_post_thumbnail_url( $pid, 'jobscout-blog' );
                    if ( ! $thumb_url ) {
                        $thumb_url = $theme_img_dir . 'banner-image.jpg';
                    }
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'news-card' ); ?> onclick="window.location.href='<?php echo esc_url( get_permalink() ); ?>';" style="cursor: pointer;">
                        <div class="news-card-thumb">
                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
                            </a>
                        </div>
                        <div class="news-card-body">
                            <h3 class="news-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="news-card-excerpt">
                                <p>
                                    <a href="<?php the_permalink(); ?>" class="news-card-excerpt-link">
                                        <?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '...' ) ); ?>
                                    </a>
                                </p>
                            </div>
                            <div class="news-card-action">
                                <a href="<?php the_permalink(); ?>" class="news-card-readmore"><?php esc_html_e( 'Read More', 'jobscout' ); ?></a>
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