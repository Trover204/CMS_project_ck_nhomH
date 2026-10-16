<?php
/**
 * Custom job search form for homepage hero banner
 * Matching PDS Jobs design mockup.
 *
 * @package JobScout
 */
$find_a_job_link = get_option( 'job_manager_jobs_page_id', 0 );
$post_slug       = get_post_field( 'post_name', $find_a_job_link );

if( $post_slug ){
    $action_page = home_url( '/'. $post_slug );
}else {
    $action_page = home_url( '/jobs-2/' );
}
?>

<div class="job_listings pds-hero-job-listings">

  <form class="jobscout_job_filters pds-hero-filters" method="GET" action="<?php echo esc_url( $action_page ) ?>">
    <div class="search_jobs pds-hero-search-jobs">

      <div class="search_keywords pds-hero-field pds-hero-keywords">
        <label for="search_keywords"><?php esc_html_e( 'Keywords', 'jobscout' ); ?></label>
        <span class="pds-input-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ea751e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </span>
        <input type="text" id="search_keywords" name="search_keywords" placeholder="<?php esc_attr_e( 'Search for jobs, companies, skills', 'jobscout' ); ?>">
      </div>

      <div class="search_location pds-hero-field pds-hero-location">											
        <?php											
        global $wpdb;											
        $table = $wpdb->prefix . 'postmeta';											
        $sql   = "SELECT DISTINCT SUBSTRING_INDEX(`meta_value`,',',-1) as location FROM `{$table}` WHERE `meta_key` like '%location%' ORDER BY location";											
        $data  = $wpdb->get_results( $sql );											
        ?>											
        <span class="pds-input-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ea751e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
        </span>
        <select id="search_location" name="search_location">											
            <option value="Tokyo"><?php esc_html_e( 'Tokyo', 'jobscout' ); ?></option>											
            <?php 
            if ( ! empty( $data ) ) :
                foreach ( $data as $value ) : 
                    $loc = trim( $value->location );
                    if ( ! empty( $loc ) && strcasecmp( $loc, 'Tokyo' ) !== 0 ) : ?>											
                        <option value="<?php echo esc_attr( $loc ); ?>"><?php echo esc_html( $loc ); ?></option>											
                    <?php 
                    endif;
                endforeach; 
            endif;
            ?>											
        </select>											
      </div>
      
      <div class="search_submit pds-hero-submit">
        <input type="submit" value="<?php esc_attr_e( 'SEARCH JOB', 'jobscout' ); ?>" />
      </div>

    </div>
  </form>

</div>