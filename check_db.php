<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'wordpress');

echo "=== ALL JOB LISTINGS ===\n";
$res = $mysqli->query("SELECT ID, post_title, post_status, post_content, post_excerpt FROM wp_posts WHERE post_type='job_listing'");
while ($row = $res->fetch_assoc()) {
    echo $row['ID'] . " | " . $row['post_title'] . " | " . $row['post_status'] . "\n";
}

echo "\n=== ALL ATTACHMENTS ===\n";
$res = $mysqli->query("SELECT ID, post_title, guid FROM wp_posts WHERE post_type='attachment'");
while ($row = $res->fetch_assoc()) {
    echo $row['ID'] . " | " . $row['post_title'] . " | " . $row['guid'] . "\n";
}
