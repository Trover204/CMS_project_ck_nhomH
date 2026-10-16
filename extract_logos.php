<?php
$src = imagecreatefromjpeg('C:/Users/DAT09/.gemini/antigravity/brain/a693a3d4-9cbd-4099-9f4e-42b0a1c4ee18/.user_uploaded/media_1792118501980_7f5e2429.jpg');
$out_dir = 'C:/wamp64/www/CMS_project_ck_nhomH/wordpress/wp-content/themes/jobscout/images/cropped';

// In top_jobs_section (which starts at x=50, y=190 in the original):
// Card 1 box: in top_jobs_section, the square logo is at roughly x=20, y=36, size 46x46
// In original image:
// Logo 1: x: 70, y: 226, w: 46, h: 46
// Logo 2: x: 231, y: 226, w: 46, h: 46
// Logo 3: x: 70, y: 310, w: 46, h: 46
// Logo 4: x: 231, y: 310, w: 46, h: 46
// Logo 5: x: 70, y: 394, w: 46, h: 46
// Logo 6: x: 231, y: 394, w: 46, h: 46

$logos = [
    'logo_1' => ['x' => 70, 'y' => 226, 'w' => 46, 'h' => 46],
    'logo_2' => ['x' => 231, 'y' => 226, 'w' => 46, 'h' => 46],
    'logo_3' => ['x' => 70, 'y' => 310, 'w' => 46, 'h' => 46],
    'logo_4' => ['x' => 231, 'y' => 310, 'w' => 46, 'h' => 46],
    'logo_5' => ['x' => 70, 'y' => 394, 'w' => 46, 'h' => 46],
    'logo_6' => ['x' => 231, 'y' => 394, 'w' => 46, 'h' => 46],
];

foreach ($logos as $name => $rect) {
    $cropped = imagecrop($src, ['x' => $rect['x'], 'y' => $rect['y'], 'width' => $rect['w'], 'height' => $rect['h']]);
    // Upscale 2x for sharper retina rendering
    $up = imagecreatetruecolor(120, 120);
    imagecopyresampled($up, $cropped, 0, 0, 0, 0, 120, 120, $rect['w'], $rect['h']);
    imagepng($up, "{$out_dir}/{$name}_hd.png");
    imagepng($cropped, "{$out_dir}/{$name}.png");
}

echo "Extracted 6 logos.\n";
