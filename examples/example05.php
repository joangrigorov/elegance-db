<?php

require_once '../lib/Elegance/Db.php';

$db = new Elegance_Db(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

$pairs = $db->fetchPair('SELECT `author`, `name` FROM `my_fav_albums`');
var_dump($pairs);