<?php
/**
 * JobScout Template Functions which enhance the theme by hooking into WordPress
 *
 * @package JobScout
 */

if( ! function_exists( 'jobscout_doctype' ) ) :
/**
 * Doctype Declaration
*/
function jobscout_doctype(){ ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <?php
}
endif;
add_action( 'jobscout_doctype', 'jobscout_doctype' );

if( ! function_exists( 'jobscout_head' ) ) :
/**
 * Before wp_head 
*/
function jobscout_head(){ ?>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php
}
endif;
add_action( 'jobscout_before_wp_head', 'jobscout_head' );

if( ! function_exists( 'jobscout_responsive_header' ) ) :
/**
 * Responsive Header
*/
function jobscout_responsive_header(){ 
    $post_job_label  = get_theme_mod( 'post_job_label', __( 'Post Jobs', 'jobscout' ) );
    $post_job_url    = get_theme_mod( 'post_job_url', '#' );
    ?>
    <div class="responsive-nav">
        <div class="nav-top">
            <?php jobscout_site_branding( true ); ?>
        </div>


        <nav id="mobile-site-navigation" class="main-navigation mobile-navigation">        
            <div class="primary-menu-list main-menu-modal cover-modal" data-modal-target-string=".main-menu-modal">
                <button class="close close-main-nav-toggle" data-toggle-target=".main-menu-modal" data-toggle-body-class="showing-main-menu-modal" aria-expanded="false" data-set-focus=".main-menu-modal"></button>
                <div class="mobile-menu" aria-label="<?php esc_attr_e( 'Mobile', 'jobscout' ); ?>">
                    <?php
                        wp_nav_menu( array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'mobile-primary-menu',
                            'menu_class'     => 'nav-menu main-menu-modal',
                            'container'      => false,
                            'fallback_cb'    => 'jobscout_primary_menu_fallback',
                        ) );
                    ?>
                
                    <?php if( $post_job_label || $post_job_url ){ ?>
                        <div class="btn-wrap">
                            <a class="btn" href="<?php echo esc_url( $post_job_url ) ?>"><?php echo esc_html( $post_job_label ) ?></a>
                        </div>
                    <?php } ?>
               </div>
            </div>
        </nav><!-- #mobile-site-navigation -->
    </div> <!-- .responsive-nav -->
    <?php
}
endif;
add_action( 'jobscout_before_header', 'jobscout_responsive_header', 15 );

if( ! function_exists( 'jobscout_page_start' ) ) :
/**
 * Page Start
*/
function jobscout_page_start(){ ?>
    <div id="page" class="site">
        <a class="skip-link screen-reader-text" href="#acc-content"><?php esc_html_e( 'Skip to content (Press Enter)', 'jobscout' ); ?></a>
    <?php
}
endif;
add_action( 'jobscout_before_header', 'jobscout_page_start', 20 );

if( ! function_exists( 'jobscout_header' ) ) :
/**
 * Header Start
*/
function jobscout_header(){ 
    ?>
    <header id="masthead" class="site-header header-one" itemscope itemtype="https://schema.org/WPHeader">
        <div class="header-main">
            <div class="container">
                <?php 
                    jobscout_site_branding( false );
                    echo '<div class="menu-wrap">';
                    jobscout_primary_nagivation();
                    echo '</div><!-- .menu-wrap -->';
                ?>
            </div>
        </div> <!-- .header-main -->
    </header> <!-- .site-header -->
    <?php
}
endif;
add_action( 'jobscout_header', 'jobscout_header', 20 );

if( ! function_exists( 'jobscout_breadcrumbs_bar' ) ) :
    /**
     * Breadcrumbs
    */
    function jobscout_breadcrumbs_bar(){
        $ed_breadcrumbs = get_theme_mod( 'ed_breadcrumbs', false );

        if( $ed_breadcrumbs && ! is_front_page() && ! is_404() && ! is_page_template( 'page-news.php' ) && ! is_page( 'news' ) && ! is_page_template( 'page-contact.php' ) && ! is_page( 'contact' ) && ! is_page_template( 'page-jobs.php' ) && ! is_page( 'jobs' ) && ! is_page( 'jobs-2' ) && ! is_singular( 'post' ) && ! is_singular( 'job_listing' ) && ! is_page_template( 'page-job-detail.php' ) && ! is_page( 'job-detail' ) ){ ?>
            <section class="breadcrumb-wrap">
                <div class="container">
                    <?php jobscout_breadcrumbs_cb(); //Breadcrumb ?>
                </div>
            </section>   
            <?php 
        }    
    }
endif;
add_action( 'jobscout_after_header', 'jobscout_breadcrumbs_bar', 30 );

if( ! function_exists( 'jobscout_content_start' ) ) :
/**
 * Content Start
 *  
 */
function jobscout_content_start(){       
    echo '<div id="acc-content"><!-- .site-header -->';
    $home_sections = jobscout_get_home_sections(); 
    if( ! ( is_front_page() && ! is_home() && $home_sections ) && ! is_page_template( 'page-news.php' ) && ! is_page( 'news' ) && ! is_page_template( 'page-contact.php' ) && ! is_page( 'contact' ) && ! is_page_template( 'page-jobs.php' ) && ! is_page( 'jobs' ) && ! is_page( 'jobs-2' ) && ! is_singular( 'post' ) && ! is_singular( 'job_listing' ) && ! is_page_template( 'page-job-detail.php' ) && ! is_page( 'job-detail' ) ){ //Make necessary adjust for pg template.
        echo is_404() ? '<div class="error-holder">' : '<div id="content" class="site-content">'; 

        if( is_archive() || is_search() || is_page_template( 'templates/portfolio.php' ) ) : ?>
            <header class="page-header">
                <?php
                    if( is_archive() ){ 
                        if( is_author() ) { 
                            $author_title = get_the_author(); ?>
                            <div class="author-bio">
                                <figure class="author-img"><?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?></figure>
                                <div class="author-content">
                                    <?php 
                                        echo '<span class="sub-title">' . esc_html__( 'All Posts by', 'jobscout' ) . '</span>';
                                        if( $author_title ) echo '<h1 class="author-title">' . esc_html( $author_title ) . '</h3>';
                                    ?>      
                                </div>
                            </div>
                        <?php }else{
                            the_archive_title( '<h1 class="page-title">', '</h1>' );
                            the_archive_description( '<div class="archive-description">', '</div>' );             
                        }
                    }
                    
                    if( is_search() ){ 
                        echo '<div class="container">';
                            echo '<h1 class="page-title">' . esc_html__( 'Search', 'jobscout' ) . '</h1>';
                            get_search_form();
                        echo '</div><!-- .container -->';
                    }

                    if( ! is_author() && ! is_search() ){
                        jobscout_posts_per_page_count();
                    }

                    if( is_page_template( 'templates/portfolio.php' ) ){
                        global $post;
                        echo '<div class="container">';
                            echo '<h1 class="page-title">' . esc_html( get_the_title( $post->ID ), 'jobscout' ) . '</h1>';
                            if( $post->post_content ) echo wpautop( wp_kses_post( $post->post_content ) );
                        echo '</div><!-- .container -->';
                    }
                ?>
            </header>
        <?php endif; 
            if( is_singular( 'job_listing' ) ){
                global $post;
                $banner_image   = get_header_image();
                $show_banner    = get_theme_mod( 'ed_job_banner', true );

                if( $banner_image && $show_banner ){
                    $banner_style = 'background-image: url(' . esc_url( $banner_image ) . '); background-size: cover;';
                    echo '<header class="entry-header" style="'. esc_attr( $banner_style ) .'"></header>';
                }
            } 
        ?>
        <div class="container">
        <?php 
    }
}
endif;
add_action( 'jobscout_content', 'jobscout_content_start' );

if ( ! function_exists( 'jobscout_post_thumbnail' ) ) :
/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 */
function jobscout_post_thumbnail() {
    $image_size  = 'thumbnail';
    $ed_featured = get_theme_mod( 'ed_featured_image', true );
    $sidebar     = jobscout_sidebar_layout();
    
    if( is_home() || is_archive() || is_search() ){        
        $image_size = 'jobscout-blog';    
        if( has_post_thumbnail() ){                        
            echo '<figure class="post-thumbnail"><a href="' . esc_url( get_permalink() ) . '">';
                the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );    
            echo '</a></figure>';
        }else{
            echo '<figure class="post-thumbnail">';
                jobscout_fallback_svg_image( $image_size );  
            echo '</figure>';  
        }        
    }elseif( is_singular() ){
        $image_size = ( $sidebar ) ? 'jobscout-single' : 'jobscout-single-fullwidth';
        if( is_single() ){
            if( $ed_featured && has_post_thumbnail() ){
                echo '<figure class="post-thumbnail">';
                the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );
                echo '</figure>';
            }
        }else{
            echo '<figure class="post-thumbnail">';
            the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );
            echo '</figure>';
        }
    }
}
endif;
add_action( 'jobscout_before_post_entry_content', 'jobscout_post_thumbnail', 15 );
add_action( 'jobscout_before_page_entry_content', 'jobscout_post_thumbnail', 15 );
add_action( 'jobscout_before_single_post_entry_content', 'jobscout_post_thumbnail', 15 );

if( ! function_exists( 'jobscout_entry_header' ) ) :
/**
 * Entry Header
*/
function jobscout_entry_header(){ ?>
    <header class="entry-header">
        <?php 
            $ed_cat_single = get_theme_mod( 'ed_category', false );
            $hide_author   = get_theme_mod( 'ed_post_author', false );
            $hide_date     = get_theme_mod( 'ed_post_date', false );

            if( is_single() ){
                if( ! $ed_cat_single ) jobscout_category();
            }else{
                if( 'post' === get_post_type() ){
                    echo '<div class="entry-meta">';
                    if( ! $hide_author ) jobscout_posted_by();
                    if( ! $hide_date ) jobscout_posted_on();
                    echo '</div>';
                }
            }

            if ( is_singular() ) :
                the_title( '<h1 class="entry-title">', '</h1>' );
            else :
                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
            endif;        
        ?>
    </header>         
    <?php    
}
endif;
add_action( 'jobscout_post_entry_content', 'jobscout_entry_header', 10 );
add_action( 'jobscout_before_page_entry_content', 'jobscout_entry_header', 10 );
add_action( 'jobscout_before_single_post_entry_content', 'jobscout_entry_header', 10 );

if( ! function_exists( 'jobscout_entry_content' ) ) :
/**
 * Entry Content
*/
function jobscout_entry_content(){ 
    $ed_excerpt = get_theme_mod( 'ed_excerpt', true ); ?>
    <div class="entry-content" itemprop="text">
		<?php
			if( is_singular() || ! $ed_excerpt || ( get_post_format() != false ) ){
                the_content();    
    			wp_link_pages( array(
    				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'jobscout' ),
    				'after'  => '</div>',
    			) );
            }else{
                the_excerpt();
            }
		?>
	</div><!-- .entry-content -->
    <?php
}
endif;
add_action( 'jobscout_post_entry_content', 'jobscout_entry_content', 15 );
add_action( 'jobscout_page_entry_content', 'jobscout_entry_content', 15 );
add_action( 'jobscout_single_post_entry_content', 'jobscout_entry_content', 15 );
add_action( 'jobscout_single_post_entry_content', 'jobscout_entry_content', 15 );
add_action( 'jobscout_before_single_job_content', 'jobscout_entry_content', 15 );

if( ! function_exists( 'jobscout_entry_footer' ) ) :
/**
 * Entry Footer
*/
function jobscout_entry_footer(){ 
    $readmore = get_theme_mod( 'read_more_text', __( 'Read More', 'jobscout' ) );
    $ed_post_date   = get_theme_mod( 'ed_post_date', false ); ?>
	<footer class="entry-footer">
		<?php
			if( is_single() ){
			    jobscout_tag();
			}
            
            if( is_front_page() || is_home() || is_search() || is_archive() ){
                echo '<a href="' . esc_url( get_the_permalink() ) . '" class="readmore-link"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16.207 8.58"><defs><style>.c{fill:none;stroke:#2ace5e;}</style></defs><g transform="translate(-701.5 -958.173)"><path class="c" d="M-9326.909-9204.917l-3.937,3.937,3.937,3.937" transform="translate(-8613.846 -8238.518) rotate(180)"/><line class="c" x2="15.154" transform="translate(701.5 962.426)"/></g></svg>' . esc_html( $readmore ) . '</a>';    
            }

            if( is_single() ) echo '<div class="entry-footer-right">';
            if( 'post' === get_post_type() && is_single() ){
                if( ! $ed_post_date ) jobscout_posted_on( true );
                jobscout_comment_count();
            }
            
            if( get_edit_post_link() ){
                edit_post_link(
                    sprintf(
                        wp_kses(
                            /* translators: %s: Name of current post. Only visible to screen readers */
                            __( 'Edit <span class="screen-reader-text">%s</span>', 'jobscout' ),
                            array(
                                'span' => array(
                                    'class' => array(),
                                ),
                            )
                        ),
                        get_the_title()
                    ),
                    '<span class="edit-link">',
                    '</span>'
                );
            }
            if( is_single() ) echo '</div>';
		?>
	</footer><!-- .entry-footer -->
	<?php 
}
endif;
add_action( 'jobscout_post_entry_content', 'jobscout_entry_footer', 20 );
add_action( 'jobscout_page_entry_content', 'jobscout_entry_footer', 20 );
add_action( 'jobscout_single_post_entry_content', 'jobscout_entry_footer', 20 );

if( ! function_exists( 'jobscout_get_single_job_title' ) ) :
/**
 * Before wp_head 
*/
function jobscout_get_single_job_title(){ 
    ?>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php
            if ( get_option( 'job_manager_enable_types' ) ) { 
                echo '<div class="job-type">';
                    $types = wpjm_get_the_job_types(); 
                    if ( ! empty( $types ) ) : foreach ( $types as $type ) : ?>
                        <span class="btn <?php echo esc_attr( sanitize_title( $type->slug ) ); ?>"><?php echo esc_html( $type->name ); ?></span>
                    <?php endforeach; endif;
                echo '</div>';
            } 
        ?>
    </header>
    <?php
}
endif;
add_action( 'jobscout_before_single_job_content', 'jobscout_get_single_job_title' );

if( ! function_exists( 'jobscout_navigation' ) ) :
/**
 * Navigation
*/
function jobscout_navigation(){
    if( is_single() ){
        $previous = get_previous_post_link(
    		'<div class="nav-previous nav-holder">%link</div>',
    		'<span class="meta-nav">' . esc_html__( 'Previous Article', 'jobscout' ) . '</span><span class="post-title">%title</span>',
    		false,
    		'',
    		'category'
    	);
    
    	$next = get_next_post_link(
    		'<div class="nav-next nav-holder">%link</div>',
    		'<span class="meta-nav">' . esc_html__( 'Next Article', 'jobscout' ) . '</span><span class="post-title">%title</span>',
    		false,
    		'',
    		'category'
    	); 
        
        if( $previous || $next ){?>            
            <nav class="navigation post-navigation" role="navigation">
    			<h2 class="screen-reader-text"><?php esc_html_e( 'Post Navigation', 'jobscout' ); ?></h2>
    			<div class="nav-links">
    				<?php
                        if( $previous ) echo $previous;
                        if( $next ) echo $next;
                    ?>
    			</div>
    		</nav>        
            <?php
        }
    }else{
        the_posts_navigation();
    }
}
endif;
add_action( 'jobscout_after_post_content', 'jobscout_navigation', 10 );
add_action( 'jobscout_after_posts_content', 'jobscout_navigation' );

if( ! function_exists( 'jobscout_author' ) ) :
/**
 * Author Section
*/
function jobscout_author(){ 
    $ed_author    = get_theme_mod( 'ed_author', false );
    $author_title = get_theme_mod( 'author_title', __( 'About Author', 'jobscout' ) );
    if( ! $ed_author && get_the_author_meta( 'description' ) ){ ?>
    <div class="author-bio">
        <?php if( $author_title ) echo '<h3 class="title">' . esc_html( $author_title ) . '</h3>'; ?>
        <div class="author-bio-inner">
            <figure class="author-img"><?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?></figure>
            <div class="author-content">
                <?php echo '<div class="author-info">' . wpautop( wp_kses_post( get_the_author_meta( 'description' ) ) ) . '</div>';
                ?>		
            </div>
		</div>
	</div>
    <?php
    }
}
endif;
add_action( 'jobscout_after_post_content', 'jobscout_author', 20 );

if( ! function_exists( 'jobscout_comment' ) ) :
/**
 * Comments Template 
*/
function jobscout_comment(){
    // If comments are open or we have at least one comment, load up the comment template.
	if( get_theme_mod( 'ed_comments', true ) && ( comments_open() || get_comments_number() ) ) :
		comments_template();
	endif;
}
endif;
add_action( 'jobscout_after_post_content', 'jobscout_comment', 30 );
add_action( 'jobscout_after_page_content', 'jobscout_comment' );

if( ! function_exists( 'jobscout_content_end' ) ) :
/**
 * Content End
*/
function jobscout_content_end(){ 
    $home_sections = jobscout_get_home_sections(); 
    if( ! ( is_front_page() && ! is_home() && $home_sections ) && ! is_page_template( 'page-news.php' ) && ! is_page( 'news' ) && ! is_page_template( 'page-contact.php' ) && ! is_page( 'contact' ) && ! is_page_template( 'page-jobs.php' ) && ! is_page( 'jobs' ) && ! is_page( 'jobs-2' ) && ! is_singular( 'post' ) && ! is_singular( 'job_listing' ) && ! is_page_template( 'page-job-detail.php' ) && ! is_page( 'job-detail' ) ){ ?>            
        </div><!-- .container/ -->        
    </div><!-- .error-holder/site-content -->
    <?php
    }
}
endif;
add_action( 'jobscout_before_footer', 'jobscout_content_end', 20 );

if( ! function_exists( 'jobscout_footer_start' ) ) :
/**
 * Footer Start
*/
function jobscout_footer_start(){
    ?>
    <footer id="colophon" class="site-footer" itemscope itemtype="https://schema.org/WPFooter">
    <?php
}
endif;
add_action( 'jobscout_footer', 'jobscout_footer_start', 20 );

if( ! function_exists( 'jobscout_footer_top' ) ) :
/**
 * Footer Top - Synchronized PDS Footer across Home, News, and News Detail pages
*/
function jobscout_footer_top(){    
    $news_page     = get_page_by_path( 'news' );
    $news_page_url = $news_page ? get_permalink( $news_page->ID ) : home_url( '/index.php/news/' );
    $jobs_page     = get_page_by_path( 'jobs-2' );
    $jobs_page_url = $jobs_page ? get_permalink( $jobs_page->ID ) : home_url( '/index.php/jobs-2/' );
    $about_page    = get_page_by_path( 'about' );
    $about_url     = $about_page ? get_permalink( $about_page->ID ) : home_url( '/index.php/about/' );
    $contact_page  = get_page_by_path( 'contact' );
    $contact_url   = $contact_page ? get_permalink( $contact_page->ID ) : home_url( '/index.php/contact/' );

    // Display the Orange Newsletter Section on Home, Contact, and other pages (News, News Detail, Jobs, and Job Detail already render it before get_footer)
    if ( ! is_page_template( 'page-news.php' ) && ! is_page( 'news' ) && ! is_page_template( 'page-jobs.php' ) && ! is_page( 'jobs' ) && ! is_page( 'jobs-2' ) && ! is_page_template( 'page-about.php' ) && ! is_page( 'about' ) && ! is_page_template( 'page-contact.php' ) && ! is_page( 'contact' ) && ! is_singular( 'post' ) && ! is_singular( 'job_listing' ) && ! is_page_template( 'page-job-detail.php' ) && ! is_page( 'job-detail' ) ) :
        ?>
        <section class="news-newsletter-section">
            <div class="container">
                <div class="news-newsletter-inner">
                    <div class="news-newsletter-title">
                        <span class="news-newsletter-line1"><?php echo esc_html( get_theme_mod( 'news_newsletter_line1', 'Subscribe To' ) ); ?></span>
                        <span class="news-newsletter-line2"><?php echo esc_html( get_theme_mod( 'news_newsletter_line2', 'Our Newsletter' ) ); ?></span>
                    </div>
                    <div class="news-newsletter-form-wrap">
                        <form class="news-newsletter-form" onsubmit="event.preventDefault(); alert('Thank you for subscribing to our newsletter!');">
                            <div class="news-input-group">
                                <span class="news-input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ea751e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </span>
                                <input type="email" class="news-newsletter-email-input" placeholder="<?php echo esc_attr( get_theme_mod( 'news_newsletter_placeholder', __( 'Input your email address', 'jobscout' ) ) ); ?>" required />
                            </div>
                            <button type="submit" class="news-newsletter-btn"><?php echo esc_html( get_theme_mod( 'news_newsletter_btn_text', __( 'SUBSCRIBE', 'jobscout' ) ) ); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <?php
    endif;
    ?>
    <div class="pds-footer-top">
        <div class="container">
            <div class="pds-footer-branding">
                <?php jobscout_site_branding( true ); ?>
            </div>
            <ul class="pds-footer-menu">
                <li><a href="<?php echo esc_url( $jobs_page_url ); ?>"><?php esc_html_e( 'JOBS', 'jobscout' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'COMPANIES', 'jobscout' ); ?></a></li>
                <li><a href="<?php echo esc_url( $news_page_url ); ?>"><?php esc_html_e( 'BLOG', 'jobscout' ); ?></a></li>
                <li><a href="<?php echo esc_url( $about_url ); ?>"><?php esc_html_e( 'ABOUT', 'jobscout' ); ?></a></li>
                <li><a href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'CONTACT', 'jobscout' ); ?></a></li>
            </ul>
            <div class="pds-footer-socials">
                <a href="<?php echo esc_url( get_theme_mod( 'pds_social_fb', 'https://facebook.com' ) ); ?>" target="_blank" rel="noopener noreferrer" class="pds-social-btn pds-social-fb" aria-label="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 320 512" fill="#ffffff"><path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>
                </a>
                <a href="<?php echo esc_url( get_theme_mod( 'pds_social_gg', 'https://google.com' ) ); ?>" target="_blank" rel="noopener noreferrer" class="pds-social-btn pds-social-gg" aria-label="Google">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 48 48">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                </a>
                <a href="<?php echo esc_url( get_theme_mod( 'pds_social_line', 'https://line.me' ) ); ?>" target="_blank" rel="noopener noreferrer" class="pds-social-btn pds-social-line" aria-label="Line">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#ffffff"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>
                </a>
                <a href="<?php echo esc_url( get_theme_mod( 'pds_social_tw', 'https://twitter.com' ) ); ?>" target="_blank" rel="noopener noreferrer" class="pds-social-btn pds-social-tw" aria-label="Twitter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512" fill="#ffffff"><path d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"/></svg>
                </a>
            </div>
        </div>
    </div>
    <?php
}
endif;
add_action( 'jobscout_footer', 'jobscout_footer_top', 30 );

if( ! function_exists( 'jobscout_news_menu_active_class' ) ) :
/**
 * Add current-menu-item class to menu items (NEWS when on post, JOBS when on jobs page/job listing)
 */
function jobscout_news_menu_active_class( $classes, $item ) {
    $title = strtolower( trim( strip_tags( $item->title ) ) );
    $url   = strtolower( $item->url );

    if ( is_singular( 'post' ) ) {
        if ( 'news' === $title || 'blog' === $title || strpos( $url, '/news' ) !== false ) {
            $classes[] = 'current-menu-item';
        }
    }

    if ( is_page_template( 'page-jobs.php' ) || is_page( 'jobs' ) || is_page( 'jobs-2' ) || is_singular( 'job_listing' ) ) {
        if ( 'jobs' === $title || 'job' === $title || strpos( $url, '/jobs' ) !== false ) {
            $classes[] = 'current-menu-item';
        }
    }

    return array_unique( $classes );
}
endif;
add_filter( 'nav_menu_css_class', 'jobscout_news_menu_active_class', 10, 2 );

if( ! function_exists( 'jobscout_footer_bottom' ) ) :
/**
 * Footer Bottom
*/
function jobscout_footer_bottom(){ ?>
    <div class="footer-b">
		<div class="container">
            <?php 
                if ( function_exists( 'the_privacy_policy_link' )  )  the_privacy_policy_link( '<div class="privacy-block">', '</div>' );
            ?>
			<div class="copyright">            
            <?php
                jobscout_get_footer_copyright();
                jobscout_ed_author_link();
                jobscout_ed_wp_link();
            ?>               
            </div>
		</div>
	</div>
    <?php
}
endif;
add_action( 'jobscout_footer', 'jobscout_footer_bottom', 40 );

if( ! function_exists( 'jobscout_footer_end' ) ) :
/**
 * Footer End 
*/
function jobscout_footer_end(){ ?>
    </footer><!-- #colophon -->
    <?php
}
endif;
add_action( 'jobscout_footer', 'jobscout_footer_end', 50 );

if( ! function_exists( 'jobscout_page_end' ) ) :
/**
 * Page End
*/
function jobscout_page_end(){ ?>
    </div><!-- #acc-content -->
    </div><!-- #page -->
    <?php
}
endif;
add_action( 'jobscout_after_footer', 'jobscout_page_end', 20 );