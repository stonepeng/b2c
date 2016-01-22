<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';

global $db;
$data = $db->all("SELECT `user_name` FROM `[users]` WHERE user_id>740 LIMIT 3");

debug($data);