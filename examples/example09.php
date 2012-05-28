<?php

require_once '../lib/Elegance/Db.php';

$db = new Elegance_Db(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

$data = array(
    'status' => 'destroyed',
    'intendedFor' => 'People you don\'t wanna mess with'
);
$db->update('music', $data, array('`genre` = ?' => 'Hardcore rap'), 3);;