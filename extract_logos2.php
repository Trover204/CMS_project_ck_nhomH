<?php
$src = imagecreatefromjpeg('C:/Users/DAT09/.gemini/antigravity/brain/a693a3d4-9cbd-4099-9f4e-42b0a1c4ee18/.user_uploaded/media_1792118501980_7f5e2429.jpg');
$out_dir = 'C:/wamp64/www/CMS_project_ck_nhomH/wordpress/wp-content/themes/jobscout/images/cropped';

// Let's crop cards exactly:
// Card 1 logo box: x=61, y=225, w=38, h=38
// Card 2 logo box: x=227, y=225, w=38, h=38
// Card 3 logo box: x=61, y=309, w=38, h=38
// Card 4 logo box: x=227, y=309, w=38, h=38
// Card 5 logo box: x=61, y=392, w=38, h=38
// Card 6 logo box: x=227, y=392, w=38, h=38

$boxes = [
    1 => [62, 226],
    2 => [228, 226],
    3 => [62, 309],
    4 => [228, 309],
    5 => [62, 392],
    6 => [228, 392],
];

foreach ($boxes as $i => $pos) {
    $c = imagecrop($src, ['x' => $pos[0], 'y' => $pos[1], 'width' => 38, 'height' => 38]);
    imagepng($c, "$out_dir/logo_box_$i.png");
}

echo "Extracted logo boxes.\n";
