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
                while ( $news_query->have_posts() ) : $news_query->the_post();
                    $thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'jobscout-blog' );
                    if ( ! $thumb_url ) {
                        $thumb_url = get_template_directory_uri() . '/images/banner-image.jpg';
                    }
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'news-card' ); ?>>
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
                                <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '...' ) ); ?></p>
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

<!-- Orange Newsletter Section -->
<section class="news-newsletter-section">
    <div class="container">
        <div class="news-newsletter-inner">
            <div class="news-newsletter-title">
                <span><?php echo esc_html( get_theme_mod( 'news_newsletter_line1', 'Subscribe To' ) ); ?></span>
                <span><?php echo esc_html( get_theme_mod( 'news_newsletter_line2', 'Our Newsletter' ) ); ?></span>
            </div>
            <div class="news-newsletter-form-wrap">
                <form class="news-newsletter-form" onsubmit="event.preventDefault(); alert('Thank you for subscribing to our newsletter!');">
                    <div class="news-input-group">
                        <span class="news-input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </span>
                        <input type="email" placeholder="<?php esc_attr_e( 'Input your email address', 'jobscout' ); ?>" required />
                    </div>
                    <button type="submit" class="news-newsletter-btn"><?php esc_html_e( 'SUBSCRIBE', 'jobscout' ); ?></button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
