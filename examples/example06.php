<?php

require_once '../lib/Elegance/Db.php';

$db = new Elegance_Db(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

$data = array(
        'name' => 'All we got is uz',
        'author' => 'Onyx',
        'year' => 1995
);
$id = $db->insert('records', $data);