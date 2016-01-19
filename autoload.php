<?php

define('ROOT_PATH', dirname(__FILE__));
define('FRAMEWORK_DIR_NAME', 'framework');
define('FRAMEWORK_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . FRAMEWORK_DIR_NAME);

require_once(FRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'constant.php');
require_once(FRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'config.inc.php');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once(FRAMEWORK_PATH . DS . 'db.php');

$DB = new DB(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);