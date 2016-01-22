<?php

/**
 * 根目录。
 * @var string
 */
define('ROOT_PATH', dirname(__FILE__));

/**
 * 框架目录路径，不以反斜杠结束。
 * @var string
 */
define('FRAMEWORK_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'framework');

/**
 * 配置目录路径，不以反斜杠结束。
 * @var string
 */
define('CONFIG_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'config');

/**
 * 初始化。
 */
require_once(FRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'function.php');
require_once(FRAMEWORK_PATH . DIRECTORY_SEPARATOR . 'constant.php');
require_once(CONFIG_PATH . DIRECTORY_SEPARATOR . 'config.inc.php');

/**
 * 目录分隔符 DIRECTORY_SEPARATOR 的别名。
 * @var string
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once(FRAMEWORK_PATH . DS . 'core' .DS. 'class.db.php');

$db = new DB(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_, _DB_PREFIX_);
