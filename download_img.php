<?php
$ids = [
    'photo-1493976040374-85c8e12f0c0e', 
    'photo-1503899036084-c55cdd92da26', 
    'photo-1524413840807-0c3cb6fa808d', 
    'photo-1578637387939-43c525550085', 
    'photo-1509099836639-18ba1795216d',
    'photo-1536098561742-ca998e48cbcc', // Japan temple pagoda
    'photo-1528360983277-13d401cdc186', // Japan street / lanterns
    'photo-1503899036084-c55cdd92da26'
];

foreach ($ids as $id) {
    $url = "https://images.unsplash.com/{$id}?auto=format&fit=crop&w=1920&q=80";
    $ctx = stream_context_create(['http' => ['timeout' => 5, 'header' => "User-Agent: Mozilla/5.0\r\n"]]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data && strlen($data) > 1000) {
        $dest = "c:/wamp64/www/CMS_project_ck_nhomH/wordpress/wp-content/themes/jobscout/images/{$id}.jpg";
        file_put_contents($dest, $data);
        echo "{$id}: " . strlen($data) . " bytes saved.\n";
    } else {
        echo "{$id}: failed\n";
    }
}
