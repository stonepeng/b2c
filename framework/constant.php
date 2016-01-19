<?php

/**
 * 目录分隔符 DIRECTORY_SEPARATOR 的别名。
 * @var string
 */
define('DS', DIRECTORY_SEPARATOR);
/**
 * 路径分隔符 PATH_SEPARATOR 的别名。
 * @var string
 */
define('PS', PATH_SEPARATOR);

/**
 * 要校正的目录分隔符。
 * @var string
 */
define('DIRECTORY_SEPARATOR_CORRECTION', DIRECTORY_SEPARATOR === '/' ? '\\' : '/');
/**
 * 要校正的目录分隔符，DIRECTORY_SEPARATOR_CORRECTION 别名。
 * @var string
 */
define('DSC', DIRECTORY_SEPARATOR_CORRECTION);
/**
 * 正则表达式模式，用来检测字符串是否是一个有效的正则表达式。
 * @var string
 */
define('PREG_PATTERN', '{^(?:([^\s\w\\\\[:cntrl:]]).*\1|\{.*\})[imsxuADSUXJ]*$}');

/**
 * 是否运行在 CLI 环境。
 * @var boolean
 */
define('IS_CLI', substr(PHP_SAPI, 0, 3)=='cli' && stripos(PHP_SAPI, 'server') === false, true);
/**
 * 是否运行在 windows 环境。
 * @var boolean
*/
define('IS_WIN', strstr(PHP_OS, 'WIN') ? true : false, true);
/**
 * 是否运行在 PHP-fpm 模式。
 * @var boolean
*/
define('IS_FPM', substr_compare(PHP_SAPI, 'fpm', 0, 3, true)===0, true);
/**
 * 是否运行在 CGI 模式。
 * @var boolean
*/
define('IS_CGI', substr(PHP_SAPI, 0, 3)=='cgi', true);
/**
 * 是否运行在 Fast CGI 模式。
 * @var boolean
*/
define('IS_FAST_CGI', stripos(PHP_SAPI, 'fcgi') !== false, true);
/**
 * 是否运行在 CLI Server 模式下。
 * @var boolean
*/
define('IS_CLI_SERVER', strcasecmp(PHP_SAPI, 'cli-server') === 0);



/**
 * (BOM: Byte Order Mark) 字符串：由 ASCII 码为 239，187 和 191 的三个字节构成。
 * @var string
 */
define('BOM', "\xEF\xBB\xBF");


/**
 * 应用程序接口类型：任意类型。
 * @var int
*/
define('SAPI_ANY', 0xFF);
/**
 * 应用程序接口类型：自动识别。
 * @var int
*/
define('SAPI_AUTO', 0);
/**
 * 应用程序接口类型：CLI 命令行，外壳交互环境或其它非 WEB 环境。
 * @var int
*/
define('SAPI_CLI', 1);
/**
 * 应用程序接口类型：Web 服务。
 * @var int
*/
define('SAPI_WEB', 2);
/**
 * 应用程序接口类型：Web 服务之 CGI。
 * @var int
*/
define('SAPI_CGI', 4);

/**
 * 当前的 SAPI 环境：CGI 或 CLI 或 WEB。
 * @var int
*/
define('SAPI',  IS_CLI ? SAPI_CLI : SAPI_WEB, true);


/**
 * 1 秒的微秒数。即 1 秒 = ? 微秒数。
 * @var int
*/
define('SECOND', 1000000, true);

/**
 * 1 分钟的秒数：60 秒。
 * @var int
*/
define('MINUTE', 60, true);

/**
 * 1 小时的秒数：3600 秒。
 * @var int
*/
define('HOUR', 3600, true);
/**
 * 1 天的秒数：86400 秒。
 * @var int
*/
define('DAY', 86400, true);

/**
 * 容量单位：1 KB 的字节数。
 * @var int
*/
define('KB', 1024, true);

/**
 * 容量单位：1 MB 的字节数。
 * @var int
*/
define('MB', 1048576, true);

/**
 * 容量单位：1 GB 的字节数。
 * @var int
*/
define('GB', 1073741824, true);

/**
 * 容量单位：1 TB 的字节数。
 * @var int
*/
define('TB', 1099511627776, true);



/**
 * 容量单位：1 KiB 的字节数。
 * @var int
*/
define('KiB', 1000, true);

/**
 * 容量单位：1 MiB 的字节数。
 * @var int
*/
define('MiB', 1000000, true);

/**
 * 容量单位：1 GiB 的字节数。
 * @var int
*/
define('GiB', 1000000000, true);

/**
 * 容量单位：1 TiB 的字节数。
 * @var int
*/
define('TiB', 1000000000000, true);



/**
 * PHP_EOL 别名，行结束符，依赖于平台的不同自动切换合适的行结束符。
 * @var string
*/
define('EOL', PHP_EOL, true);

/**
 * Windows 平台换行符：\r\n。
 * @var string
*/
define('EOL_WIN', "\r\n", true);
/**
 * 类 UNIX 平台换行符：\n。
 * @var string
*/
define('EOL_UNIX', "\n", true);
/**
 * Mac 平台换行符：\r。
 * @var string
*/
define('EOL_MAC', "\r", true);
/**
 * 自动根据平台选择换行符：\r\n。
 * @var string
*/
define('EOL_AUTO', PHP_EOL, true);
/**
 * TAB 符。
 * @var string
*/
define('TAB', "\t", true);

/**
 * 类型：字符串。
 * @var int
 */
const TYPE_STR = 0;
/**
 * 类型：字符串。
 * @var int
 */
const TYPE_STRING = 0;

/**
 * 类型：整型，表示任意整数。
 * @var int
 */
const TYPE_INT=1;
/**
 * 类型：整型，表示任意整数。
 * @var int
 */
const TYPE_INTEGER=1;

/**
 * 类型：正整型，表示任意非负整数。
 * @var int
 */
const TYPE_UNSIGNED=2;

/**
 * 类型：浮点型，表示任意浮点数。
 * @var int
 */
const TYPE_FLOAT=3;
/**
 * 类型：浮点型，表示任意浮点数。
 * @var int
 */
const TYPE_REAL=3;
/**
 * 类型：浮点型，表示任意浮点数。
 * @var int
 */
const TYPE_DOUBLE=3;
/**
 * 类型：浮点型，表示任意浮点数。
 * @var int
 */
const TYPE_DECIMAL=3;

/**
 * 类型：货币型，用于货币表示的浮点数。
 * @var int
 */
const TYPE_CURRENCY=4;

/**
 * 类型：布尔型，表示任意布尔值：true 或 false。
 * @var int
 */
const TYPE_BOOL =5;
/**
 * 类型：布尔型，表示任意布尔值：true 或 false。
 * @var int
 */
const TYPE_BOOLEAN =5;


/**
 * 类型：日期，不含时间值。
 * @var int
 */
const TYPE_DATE = 10;
/**
 * 类型：时间，不含日期值。
 * @var int
 */
const TYPE_TIME = 11;
/**
 * 类型：日期时间，包括日期和时间值。
 * @var int
 */
const TYPE_DATETIME = 12;
/**
 * 类型：UNIX 时间戳，表示日期和时间值的整数。
 * @var int
 */
const TYPE_TIMESTAMP = 13;

/**
 * 类型：数组。
 * @var int
 */
const TYPE_ARRAY = 21;

/**
 * 类型：二进制数据块。
 * @var int
 */
const TYPE_BINARY = 30;
/**
 * 类型：二进制数据块。
 * @var int
 */
const TYPE_BLOB = 31;
/**
 * 类型：图像数据块。
 * @var int
 */
const TYPE_IMAGE = 32;

/**
 * 类型：对象。
 * @var int
 */
const TYPE_OBJECT = 40;
