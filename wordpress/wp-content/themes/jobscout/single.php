<?php
/**
 * The template for displaying all single posts (News Detail Page)
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

$upload_dir       = wp_get_upload_dir();
$content_img_1    = $upload_dir['baseurl'] . '/2026/10/project-development.jpg';
$content_img_2    = $upload_dir['baseurl'] . '/2026/10/restaurant-hotel-management-and-operations.jpg';
$fallback_img_1   = get_template_directory_uri() . '/images/view-tokyo-cityscape-night-japan.jpg';
$fallback_img_2   = get_template_directory_uri() . '/images/tokyonight.jpg';

$breadcrumb_home   = get_theme_mod( 'news_breadcrumb_home', __( 'Home', 'jobscout' ) );
$breadcrumb_all    = get_theme_mod( 'news_breadcrumb_all', __( 'All News', 'jobscout' ) );
$breadcrumb_detail = get_theme_mod( 'news_breadcrumb_detail', __( 'News Detail', 'jobscout' ) );
$share_label       = get_theme_mod( 'news_detail_share_label', __( 'SHARE', 'jobscout' ) );
$readmore_label    = get_theme_mod( 'news_readmore_text', __( 'Read More', 'jobscout' ) );
?>

<section class="news-detail-section">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
            <?php
            $current_post_id = get_the_ID();
            $thumb_url       = function_exists( 'jobscout_get_news_post_thumb_url' ) ? jobscout_get_news_post_thumb_url( $current_post_id ) : get_the_post_thumbnail_url( $current_post_id, 'jobscout-blog' );
            if ( ! $thumb_url ) {
                $thumb_url = get_template_directory_uri() . '/images/banner-image.jpg';
            }
            $display_title   = function_exists( 'jobscout_get_news_post_title' ) ? jobscout_get_news_post_title( $current_post_id ) : get_the_title();

            // Category badge
            $categories     = get_the_category( $current_post_id );
            $category_label = 'Category Name';
            if ( ! empty( $categories ) ) {
                foreach ( $categories as $cat ) {
                    if ( ! in_array( $cat->slug, array( 'uncategorized', 'chua-phan-loai' ), true ) ) {
                        $category_label = $cat->name;
                        break;
                    }
                }
            }

            // Location badge
            $location_label = get_theme_mod( 'news_post_location_' . $current_post_id, get_post_meta( $current_post_id, 'location', true ) );
            if ( empty( $location_label ) ) {
                $location_label = 'Ho Chi Minh City';
            }
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
                        <div class="news-detail-date">
                            <?php echo esc_html( 'Posted: ' . get_the_date( 'M d, Y' ) ); ?>
                        </div>
                        <div class="news-detail-badges">
                            <span class="news-detail-badge"><?php echo esc_html( $category_label ); ?></span>
                            <span class="news-detail-badge post-id-<?php echo esc_attr( $current_post_id ); ?>-location-badge"><?php echo esc_html( $location_label ); ?></span>
                        </div>
                    </div>
                </div>
                <div class="news-detail-header-right">
                    <button type="button" class="news-detail-share-btn" onclick="if(event.target.closest('.customize-partial-edit-shortcut'))return;if(navigator.share){navigator.share({title:document.title,url:window.location.href});}else if(navigator.clipboard){navigator.clipboard.writeText(window.location.href);alert('Link copied to clipboard!');}">
                        <?php echo esc_html( $share_label ); ?>
                    </button>
                </div>
            </div>

            <!-- Main Article Content Card (Left 2/3 Column) -->
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'news-detail-body-card' ); ?>>
                <div class="news-detail-entry-content">
                    <?php
                    $raw_content = get_the_content();
                    if ( strpos( $raw_content, '<img' ) !== false ) :
                        the_content();
                    else :
                        ?>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, et. Vivamus volutpat in nunc, porttitor dui. Ut placerat aenean accumsan a, aenean leo, a eu. Aliquet urna, habitasse elit lorem id aenean quam. Eu varius nulla auctor dignissim massa tempus, massa tortor. Eget auctor nulla maecenas ac tortor.</p>
                        <p>Ornare faucibus sed vitae dolor eu, eu faucibus in enim. Tinciduntoulque sed netus nibh pharetra. Gravida venenatis, lobortis id mi. Metus, ultrices duis pellentesque aliquet amet cras blandit. Aliquet purus quam laoreet ipsum penibus. Odio cursus, nunc, diam habitant nunc massa sed. Placerat arcu quis est, magna facilisis amet. Dignissim vel adipiscing, elit, facilisi praesent platea. Nulla posuere feugiat turpis magna etiam. Non cras felis eu praesent cras.</p>

                        <figure class="news-detail-inline-figure">
                            <img src="<?php echo esc_url( $content_img_1 ); ?>" onerror="this.onerror=null;this.src='<?php echo esc_url( $fallback_img_1 ); ?>';" alt="<?php echo esc_attr( $display_title ); ?>" />
                        </figure>

                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, et. Vivamus volutpat in nunc, porttitor dui. Ut placerat aenean accumsan a, aenean leo, a eu. Aliquet urna, habitasse elit lorem id aenean quam. Eu varius nulla auctor dignissim massa tempus, massa tortor. Eget auctor nulla maecenas ac tortor.</p>
                        <p>Ornare faucibus sed vitae dolor eu, eu faucibus in enim. Tinciduntoulque sed netus nibh pharetra. Gravida venenatis, lobortis id mi. Metus, ultrices duis pellentesque aliquet amet cras blandit. Aliquet purus quam laoreet ipsum penibus. Odio cursus, nunc, diam habitant nunc massa sed. Placerat arcu quis est, magna facilisis amet. Dignissim vel adipiscing, elit, facilisi praesent platea. Nulla posuere feugiat turpis magna etiam. Non cras felis eu praesent cras.</p>

                        <figure class="news-detail-inline-figure">
                            <img src="<?php echo esc_url( $content_img_2 ); ?>" onerror="this.onerror=null;this.src='<?php echo esc_url( $fallback_img_2 ); ?>';" alt="<?php echo esc_attr( $display_title ); ?>" />
                        </figure>

                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, et. Vivamus volutpat in nunc, porttitor dui. Ut placerat aenean accumsan a, aenean leo, a eu. Aliquet urna, habitasse elit lorem id aenean quam. Eu varius nulla auctor dignissim massa tempus, massa tortor. Eget auctor nulla maecenas ac tortor.</p>
                    <?php endif; ?>
                </div>
            </article>

        <?php endwhile; ?>

        <!-- Newest Blog Entries Section -->
        <div class="news-detail-newest-section">
            <div class="news-header">
                <h2 class="news-section-title"><?php echo esc_html( get_theme_mod( 'news_section_heading', 'NEWEST BLOG ENTRIES' ) ); ?></h2>
            </div>

            <div class="news-grid">
                <?php
                $newest_query = new WP_Query( array(
                    'post_type'           => 'post',
                    'post_status'         => 'publish',
                    'posts_per_page'      => 6,
                    'ignore_sticky_posts' => 1,
                ) );

                if ( $newest_query->have_posts() ) :
                    while ( $newest_query->have_posts() ) : $newest_query->the_post();
                        $card_pid       = get_the_ID();
                        $card_thumb_url = function_exists( 'jobscout_get_news_post_thumb_url' ) ? jobscout_get_news_post_thumb_url( $card_pid ) : get_the_post_thumbnail_url( $card_pid, 'jobscout-blog' );
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

<?php // Orange Newsletter Section (Contact Form 7) ?>
<?php get_template_part( 'template-parts/newsletter-cf7' ); ?>

<?php
get_footer();
