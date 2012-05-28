<?php

require_once '../lib/Elegance/Db.php';

$db = new Elegance_Db(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

$rows = $db->fetchAll('SELECT * FROM my_fav_albums WHERE `year` < ?', array(1990));

foreach ($rows as $row) {
    echo 'Album: ' . $row->name . '; Year: ' . $row->year . '; By: ' . $row->author . PHP_EOL;
}