<?php
$src = imagecreatefromjpeg('C:/Users/DAT09/.gemini/antigravity/brain/a693a3d4-9cbd-4099-9f4e-42b0a1c4ee18/.user_uploaded/media_1792118501980_7f5e2429.jpg');
$w = imagesx($src);
$h = imagesy($src);
echo "Image dimensions: {$w}x{$h}\n";

// Let's create an output dir
$out_dir = 'C:/wamp64/www/CMS_project_ck_nhomH/wordpress/wp-content/themes/jobscout/images/cropped';
if (!file_exists($out_dir)) {
    mkdir($out_dir, 0777, true);
}

// In a 424x1024 image:
// TOP JOBS section is roughly y: 200 to 480
// Col 1 is roughly x: 55 to 210
// Col 2 is roughly x: 215 to 370
// Row 1: y: 220 to 290
// Row 2: y: 300 to 370
// Row 3: y: 380 to 450

// Let's crop the whole Top Jobs area first to inspect:
$top_jobs = imagecrop($src, ['x' => 50, 'y' => 190, 'width' => 324, 'height' => 280]);
imagejpeg($top_jobs, $out_dir . '/top_jobs_section.jpg', 95);

// Crop each logo specifically:
// Card 1 logo:
$logo1 = imagecrop($src, ['x' => 64, 'y' => 228, 'width' => 32, 'height' => 32]);
imagepng($logo1, $out_dir . '/logo_1.png');

// Card 2 logo:
$logo2 = imagecrop($src, ['x' => 224, 'y' => 228, 'width' => 32, 'height' => 32]);
imagepng($logo2, $out_dir . '/logo_2.png');

// Card 3 logo:
$logo3 = imagecrop($src, ['x' => 64, 'y' => 308, 'width' => 32, 'height' => 32]);
imagepng($logo3, $out_dir . '/logo_3.png');

// Card 4 logo:
$logo4 = imagecrop($src, ['x' => 224, 'y' => 308, 'width' => 32, 'height' => 32]);
imagepng($logo4, $out_dir . '/logo_4.png');

// Card 5 logo:
$logo5 = imagecrop($src, ['x' => 64, 'y' => 388, 'width' => 32, 'height' => 32]);
imagepng($logo5, $out_dir . '/logo_5.png');

// Card 6 logo:
$logo6 = imagecrop($src, ['x' => 224, 'y' => 388, 'width' => 32, 'height' => 32]);
imagepng($logo6, $out_dir . '/logo_6.png');

// Career With Us banner background:
// roughly y: 490 to 640, x: 0 to 424
$career_bg = imagecrop($src, ['x' => 0, 'y' => 495, 'width' => 424, 'height' => 145]);
imagejpeg($career_bg, $out_dir . '/career_bg.jpg', 95);

// Banner hero background:
$hero_bg = imagecrop($src, ['x' => 0, 'y' => 20, 'width' => 424, 'height' => 165]);
imagejpeg($hero_bg, $out_dir . '/hero_bg.jpg', 95);

echo "Cropped files created successfully.\n";
