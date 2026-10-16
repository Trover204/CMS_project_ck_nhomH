<?php
$hero_src = 'C:/Users/DAT09/.gemini/antigravity/brain/a693a3d4-9cbd-4099-9f4e-42b0a1c4ee18/hero_pagoda_fuji_1792120951560.jpg';
$career_src = 'C:/Users/DAT09/.gemini/antigravity/brain/a693a3d4-9cbd-4099-9f4e-42b0a1c4ee18/career_sakura_bg_1792120933492.jpg';
$target_dir = 'c:/wamp64/www/CMS_project_ck_nhomH/wordpress/wp-content/themes/jobscout/images';

// 1. Flip hero horizontally so Pagoda is on the right and Mt Fuji is on the left
$im = imagecreatefromjpeg($hero_src);
imageflip($im, IMG_FLIP_HORIZONTAL);
imagejpeg($im, "$target_dir/home-hero-bg.jpg", 92);
imagejpeg($im, "$target_dir/banner-image.jpg", 92);
imagedestroy($im);
echo "Hero image saved (flipped).\n";

// 2. Career image
copy($career_src, "$target_dir/home-career-bg.jpg");
echo "Career image saved.\n";

// 3. Logo images
$logo_dir = "$target_dir/job-logos";
if (!file_exists($logo_dir)) {
    mkdir($logo_dir, 0777, true);
}

// In the original screenshot:
// The logos in the cards are:
// Card 1: x: 62, y: 226, w: 37, h: 37
// Card 2: x: 223, y: 226, w: 37, h: 37
// Card 3: x: 62, y: 309, w: 37, h: 37
// Card 4: x: 223, y: 309, w: 37, h: 37
// Card 5: x: 62, y: 392, w: 37, h: 37
// Card 6: x: 223, y: 392, w: 37, h: 37
$screen = imagecreatefromjpeg('C:/Users/DAT09/.gemini/antigravity/brain/a693a3d4-9cbd-4099-9f4e-42b0a1c4ee18/.user_uploaded/media_1792118501980_7f5e2429.jpg');

$coords = [
    1 => [63, 227],
    2 => [224, 227],
    3 => [63, 310],
    4 => [224, 310],
    5 => [63, 393],
    6 => [224, 393],
];

foreach ($coords as $idx => $xy) {
    // Crop inside the box (w: 35, h: 35) to avoid outer grey border
    $crop = imagecrop($screen, ['x' => $xy[0] + 1, 'y' => $xy[1] + 1, 'width' => 34, 'height' => 34]);
    
    // Scale up nicely with white background
    $dest = imagecreatetruecolor(100, 100);
    $white = imagecolorallocate($dest, 255, 255, 255);
    imagefill($dest, 0, 0, $white);
    imagecopyresampled($dest, $crop, 6, 6, 0, 0, 88, 88, 34, 34);
    
    imagepng($dest, "$logo_dir/logo-$idx.png");
    imagedestroy($crop);
    imagedestroy($dest);
}
echo "Logos created in job-logos folder.\n";
