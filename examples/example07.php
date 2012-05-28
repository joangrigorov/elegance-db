<?php

require_once '../lib/Elegance/Db.php';

$db = new Elegance_Db(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

// Let's assume that there is a UNIQUE index on name and author
$data = array(
        'name' => 'Dead Serious',
        'author' => 'Das EFX',
        'year' => 1992
);
$db->insert('records', $data);
// If we try to run the same again, it won't work,
// but any errors from duplicate key will be ignored
$db->insert('records', $data, true);