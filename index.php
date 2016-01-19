<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoload.php';

global $DB;
$data = $DB->get_all("SELECT `id` FROM `taiyue`.`ty_info`");
print_r($data);