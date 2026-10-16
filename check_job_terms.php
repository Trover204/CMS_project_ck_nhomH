<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'wordpress');
if ($mysqli->connect_error) {
    die("Connect error: " . $mysqli->connect_error);
}

$res = $mysqli->query("SELECT t.term_id, t.name, t.slug, tt.taxonomy, tt.term_taxonomy_id FROM wp_terms t JOIN wp_term_taxonomy tt ON t.term_id=tt.term_id WHERE tt.taxonomy LIKE '%job%'");
while ($row = $res->fetch_assoc()) {
    echo "{$row['taxonomy']} | ID: {$row['term_id']} | TT_ID: {$row['term_taxonomy_id']} | {$row['name']} | {$row['slug']}\n";
}
