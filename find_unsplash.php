<?php
$query = 'chureito-pagoda-mount-fuji';
$ctx = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\nAccept: application/json\r\n"
    ]
]);
$url = "https://unsplash.com/napi/search/photos?query=chureito+pagoda+fuji&per_page=5";
$json = @file_get_contents($url, false, $ctx);
if ($json) {
    $data = json_decode($json, true);
    if (!empty($data['results'])) {
        foreach ($data['results'] as $item) {
            echo $item['id'] . " | " . $item['alt_description'] . "\n" . $item['urls']['full'] . "\n\n";
        }
    } else {
        echo "No results in json\n";
    }
} else {
    echo "Failed to fetch json\n";
}
