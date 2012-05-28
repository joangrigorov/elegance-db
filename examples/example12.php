<?php

require_once '../lib/Elegance/Db.php';

$db = new Elegance_Db(new PDO('mysql:host=localhost;dbname=test', 'root', ''));

$db->delete('music', array('`genre` = ?' => 'hiphop', '`topic` = ?' => 'Money, b*tches, cars'), 100);