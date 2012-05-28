<?php

require_once '../lib/Elegance/Db.php';

$db = new Elegance_Db(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

$name = $db->fetchOne('SELECT author FROM `my_fav_albums` WHERE `id` = ?', array(4));
echo 'My favorite thrash band is ' . $name;