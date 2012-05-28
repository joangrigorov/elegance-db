<?php

require_once '../lib/Elegance/Db.php';

$db = new Elegance_Db(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

$data = array(
    'status' => 'destroyed',
    'intendedFor' => 'Inteligent people'
);
$db->update('music', $data, '`genre` = \'Real hip hop\'');