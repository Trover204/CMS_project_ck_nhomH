/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.site-title, .site-description' ).css( {
					'clip': 'auto',
					'position': 'relative'
				} );
				$( '.site-title a, .site-description' ).css( {
					'color': to
				} );
			}
		} );
	} );

	// News Page Live Preview Bindings
	wp.customize( 'news_banner_title', function( value ) {
		value.bind( function( to ) {
			$( '.news-banner-title' ).text( to );
		} );
	} );

	wp.customize( 'news_section_heading', function( value ) {
		value.bind( function( to ) {
			$( '.news-section-title' ).text( to );
		} );
	} );

	wp.customize( 'news_readmore_text', function( value ) {
		value.bind( function( to ) {
			$( '.news-card-readmore' ).text( to );
		} );
	} );

	wp.customize( 'news_newsletter_line1', function( value ) {
		value.bind( function( to ) {
			$( '.news-newsletter-line1' ).text( to );
		} );
	} );

	wp.customize( 'news_newsletter_line2', function( value ) {
		value.bind( function( to ) {
			$( '.news-newsletter-line2' ).text( to );
		} );
	} );

	wp.customize( 'news_newsletter_placeholder', function( value ) {
		value.bind( function( to ) {
			$( '.news-newsletter-email-input, .news-newsletter-section input[type="email"]' ).attr( 'placeholder', to );
		} );
	} );

	wp.customize( 'news_newsletter_btn_text', function( value ) {
		value.bind( function( to ) {
			$( '.news-newsletter-btn, .news-newsletter-section input[type="submit"], .news-newsletter-section button[type="submit"]' ).val( to ).text( to );
		} );
	} );

	wp.customize( 'news_breadcrumb_home', function( value ) {
		value.bind( function( to ) {
			$( '.breadcrumb-home-link' ).text( to );
		} );
	} );

	wp.customize( 'news_breadcrumb_all', function( value ) {
		value.bind( function( to ) {
			$( '.breadcrumb-all-link' ).text( to );
		} );
	} );

	wp.customize( 'news_breadcrumb_detail', function( value ) {
		value.bind( function( to ) {
			$( '.breadcrumb-detail-text' ).text( to );
		} );
	} );

	wp.customize( 'news_detail_share_label', function( value ) {
		value.bind( function( to ) {
			$( '.news-detail-share-btn' ).text( to );
		} );
	} );


	// Jobs Page Live Preview Bindings
	wp.customize( 'jobs_banner_title', function( value ) {
		value.bind( function( to ) {
			$( '.jobs-banner-title' ).text( to );
		} );
	} );

	wp.customize( 'jobs_section_heading', function( value ) {
		value.bind( function( to ) {
			$( '.jobs-section-title' ).text( to );
		} );
	} );

} )( jQuery );
