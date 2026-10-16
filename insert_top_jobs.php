<?php
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wordpress/wp-load.php';

$jobs = [
    [
        'title' => 'HOTEL MANAGER',
        'slug' => 'hotel-manager',
        'company' => 'The Sodoh',
        'logo' => get_template_directory_uri() . '/images/job-logos/logo-1.png',
        'date' => '2023-10-20 09:00:00',
        'location' => 'Ho Chi Minh City',
        'type' => 'Full time',
        'category' => 'Category Name',
        'bullets' => [
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary, bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)'
        ]
    ],
    [
        'title' => 'GENERAL MANAGER - LEADING HOTEL CHAIN',
        'slug' => 'general-manager-leading-hotel-chain',
        'company' => 'Plan Do See',
        'logo' => get_template_directory_uri() . '/images/job-logos/logo-2.png',
        'date' => '2023-10-20 09:10:00',
        'location' => 'Ho Chi Minh City',
        'type' => 'Full time',
        'category' => 'Category Name',
        'bullets' => [
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary, bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)'
        ]
    ],
    [
        'title' => 'BANQUET MANAGER',
        'slug' => 'banquet-manager',
        'company' => 'The Sodoh',
        'logo' => get_template_directory_uri() . '/images/job-logos/logo-3.png',
        'date' => '2023-10-20 09:20:00',
        'location' => 'Ho Chi Minh City',
        'type' => 'Full time',
        'category' => 'Category Name',
        'bullets' => [
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary, bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)'
        ]
    ],
    [
        'title' => 'BELLMAN',
        'slug' => 'bellman',
        'company' => 'Taksimhouse',
        'logo' => get_template_directory_uri() . '/images/job-logos/logo-4.png',
        'date' => '2023-10-20 09:30:00',
        'location' => 'Ho Chi Minh City',
        'type' => 'Full time',
        'category' => 'Category Name',
        'bullets' => [
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary, bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)'
        ]
    ],
    [
        'title' => 'CHIEF OPERATING OFFICER HOTEL/ RESORT CHAIN',
        'slug' => 'chief-operating-officer-hotel-resort-chain',
        'company' => 'Phở Thìn',
        'logo' => get_template_directory_uri() . '/images/job-logos/logo-5.png',
        'date' => '2023-10-20 09:40:00',
        'location' => 'Ho Chi Minh City',
        'type' => 'Full time',
        'category' => 'Category Name',
        'bullets' => [
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary, bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)'
        ]
    ],
    [
        'title' => 'LOSS PREVENTION OFFICER',
        'slug' => 'loss-prevention-officer',
        'company' => 'Plan Do See',
        'logo' => get_template_directory_uri() . '/images/job-logos/logo-6.png',
        'date' => '2023-10-20 09:50:00',
        'location' => 'Ho Chi Minh City',
        'type' => 'Full time',
        'category' => 'Category Name',
        'bullets' => [
            'Be responsible for the effective operational management of the hotel',
            'Excellent salary, bonuses & recognition activities',
            'Foreign language allowance (up to 500USD/month)'
        ]
    ]
];

foreach ($jobs as $item) {
    $existing = get_page_by_path($item['slug'], OBJECT, 'job_listing');
    $content = "<ul>\n";
    foreach ($item['bullets'] as $b) {
        $content .= "<li>" . esc_html($b) . "</li>\n";
    }
    $content .= "</ul>";

    $post_data = [
        'post_title'   => $item['title'],
        'post_name'    => $item['slug'],
        'post_type'    => 'job_listing',
        'post_status'  => 'publish',
        'post_date'    => $item['date'],
        'post_content' => $content,
        'post_excerpt' => implode(". ", $item['bullets']),
    ];

    if ($existing) {
        $post_data['ID'] = $existing->ID;
        $job_id = wp_update_post($post_data);
        echo "Updated job {$item['title']} (ID: $job_id)\n";
    } else {
        $job_id = wp_insert_post($post_data);
        echo "Inserted job {$item['title']} (ID: $job_id)\n";
    }

    if ($job_id) {
        update_post_meta($job_id, '_job_location', $item['location']);
        update_post_meta($job_id, '_company_name', $item['company']);
        update_post_meta($job_id, '_company_logo', $item['logo']);
        update_post_meta($job_id, '_job_expires', '2035-12-31');
        update_post_meta($job_id, '_filled', 0);
        update_post_meta($job_id, '_featured', 1);
        update_post_meta($job_id, '_job_salary', 'Competitive');
        update_post_meta($job_id, '_job_type_label', $item['type']);
        update_post_meta($job_id, '_job_category_label', $item['category']);
        update_post_meta($job_id, '_job_bullets', $item['bullets']);
        
        // Also assign full-time term
        wp_set_object_terms($job_id, 2, 'job_listing_type');
    }
}
echo "Done setting up jobs.\n";
