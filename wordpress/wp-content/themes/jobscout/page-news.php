<?php
/**
 * Template Name: News Page
 *
 * Template for displaying the News page with banner, post grid, and newsletter section.
 *
 * @package JobScout
 */

get_header();
?>

<!-- News Page Hero Banner -->
<section class="news-banner-section">
    <div class="news-banner-overlay"></div>
    <div class="container news-banner-container">
        <h1 class="news-banner-title"><?php echo esc_html( get_theme_mod( 'news_banner_title', 'PDS NEWS' ) ); ?></h1>
    </div>
</section>

<!-- News Content Grid -->
<section class="news-main-section">
    <div class="container">
        <div class="news-header">
            <h2 class="news-section-title"><?php echo esc_html( get_theme_mod( 'news_section_heading', 'NEWEST BLOG ENTRIES' ) ); ?></h2>
        </div>

        <div class="news-grid">
            <?php
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
            $news_query = new WP_Query( array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 8,
                'paged'               => $paged,
                'ignore_sticky_posts' => 1,
            ) );

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

<?php // Orange Newsletter Section (Contact Form 7) ?>
<?php get_template_part( 'template-parts/newsletter-cf7' ); ?>

<?php
get_footer();
