<?php

/**
 * 标志设置测试。
 * @param int $flags 标志集。 
 * @param int $flag 某一个或几个标志。
 * @return boolean 已设置返回 true，否则为 false。
 */
function flag($flags, $flag)
{
	return ($flags & $flag) === $flag;
}

/**
 * 组装路径。整理路径包括：
 * <ol>
 * <li>整理目录分隔符为标准形式。</li>
 * <li>连接多段路径，清理多余的目录分隔符。</li>
 * <li>空的或无效的路径段将被忽略。</li>
 * </ol>
 * @param string $_ 可变参数，要组装的路径段。
 * @return string 返回组装后的路径，空路径返回 null。
 * @example path('/www', 'sites', 'www.example.com', 'index.php'); // 结果：/www/sites/www.example.com/index.php
 */
function path($_)
{
	$ps = array();
	$n = func_num_args();
	for($i=0; $i<$n; $i++)
	{
		$p = func_get_arg($i);
		if (is_object($p) && method_exists($p, '__toString'))
		{
			$p = "$p";
		}
		if (!is_scalar($p))
		{
			continue;
		}
		
		$p = str_replace(DSC, DS, $p);
		
		if ($n>1)
		{
			if ($i>0 && $i+1<$n)
			{
				$p = trim($p, DS);
			}
			else if ($i===0)
			{
				$p = rtrim($p, DS);
			}
			else
			{
				$p = ltrim($p, DS);
			}
		}
		
		if (strlen($p)<1)
		{
			continue;
		}
		$ps[] = $p;
	}
	return implode(DS, $ps);
}

/**
 * 测试字符串是否含有指定前缀。
 * @param string $str 源字符串。
 * @param string $prefix 前缀。
 * @param bool $ignoreCase 可选，是否忽略大小写。默认忽略。
 * @return bool
 */
function startswith($str, $prefix, $ignoreCase = true)
{
	if (!is_string($str) || !is_string($prefix))
	{
		return false;
	}
	$sl = strlen($str);
	$pl = strlen($prefix);
	if ($sl<$pl)
	{
		return false;
	}
	return substr_compare($str, $prefix, 0, $pl, $ignoreCase) === 0;
}

/**
 * 测试字符串是否含有指定后缀。
 * @param string $str 源字符串。
 * @param string $suffix 后缀。
 * @param bool $ignoreCase 可选，是否忽略大小写。默认忽略。
 * @return bool
 */
function endswith($str, $suffix, $ignoreCase = true)
{
	if (!is_string($str) || !is_string($suffix))
	{
		return false;
	}
	$sl = strlen($str);
	$pl = strlen($suffix);
	if ($sl<$pl)
	{
		return false;
	}
	$ss = substr($str, 0-$pl);
	return $ignoreCase ? strcasecmp($ss, $suffix) === 0 : strcmp($ss, $suffix) === 0;
}

/**
 * 提取子字符串。
 * @param string $pattern 正则表达式。
 * @param string|integer|array $group 正则表达式模式组：组的序号或名字。如果是数组，则返回指定的模式组的捕获结果。
 * @param string $subject 源字符串。
 * @param int $flags 可选，正则选项。
 * @param int $offset 可选，开始的偏移位置。
 * @return string|array
 */
function preg_fetch($pattern, $group, $subject, $flags = null, $offset = null)
{
	$result = null;
	if (preg_match($pattern, $subject, $matches, $flags, $offset))
	{
		if (is_scalar($group) && isset($matches[$group]))
		{
			$result = $matches[$group];
		}
		else if (is_array($group))
		{
			foreach ($group as $g)
			{
				if (is_scalar($g) && isset($matches[$g]))
				{
					$result[$g] = $matches[$g];
				}
			}
		}
	}
	return $result;
}


/**
 * 从数组中移除指定的值。
 * @param array $array 源数组。
 * @param mixed $value 要移除的值。
 * @param bool $strict 可选，是否严格匹配，默认为普通匹配，忽略类型。
 * @return array 返回新数组。
 */
function array_remove(array $array, $value, $strict = false)
{
	return array_filter($array, function ($var) use($value, $strict)
	{
		return !($strict ? $var === $value : $var == $value);
	});
}

/**
 * 获取第一个非空（empty 测试）的数据。
 * @param mixed $_ 可变参数，数据表。
 * @return mixed
 */
function not_empty($_)
{
	for ($i=0; $i<func_num_args(); $i++)
	{
		$var = func_get_arg($i);
		if (!empty($var))
		{
			return $var;
		}
	}
	
	return null;
}

/**
 * 获取第一个非 null (isset 测试)的数据。
 * @param mixed $_ 可变参数，数据表。
 * @return mixed
 */
function not_null($_)
{
	for ($i=0; $i<func_num_args(); $i++)
	{
		$var = func_get_arg($i);
		if (isset($var))
		{
			return $var;
		}
	}

	return null;
}

/**
 * 获取对象的所有类名。
 * @param string|object $obj 对象或类名。stdClass 类或对象忽略。
 * @param bool $autoload 可选，是否自动加载。默认为 true。
 * @return array 按继承关系倒序（先子类，后父类）排列。
 */
function classes($obj, $autoload = true)
{
	$cs = array();
	
	if (is_object($obj))
	{
		if (!$obj instanceof stdClass)
		{
			$cs = class_parents($obj, $autoload);
			if (!$cs) $cs = array();
			array_unshift($cs, get_class($obj));
		}
	}
	else if (is_string($obj))
	{
		if ($obj != 'stdClass' && class_exists($obj, $autoload))
		{
			$cs = class_parents($obj, $autoload);
			if (!$cs) $cs = array();
			array_unshift($cs, $obj);
		}
	}
	
	if (!empty($cs))
	{
		$cs = array_values($cs);
		return $cs;
	}
	return null;
}

/**
 * 多用途工厂方法。
 * <ol>
 * <li>constuct(string classname, array(arg1, arg2, etc)) 标准用法。</li>
 * <li>constuct(string classname, arg1, arg2, etc) 多参数（注意：参数数量仅为2时第二个参数不可为数组，否则视为第一种用法），或者参数数量超过2个，自动将首个元素作为类名，后续元素作为构造器参数。</li>
 * <li>constuct(array(string classname, arg1, arg2, etc)) 单数组参数，自动将数组的首个元素作为类名，后续元素作为构造器参数。注意，仅支持一个数组参数。</li>
 * </ol>
 * @param string $class 类名。
 * @param array $args 可选，传递给构造器的参数表。
 * @return object 成功实例化返回对象，否则返回 null。
 * @example constuct('classname', array($arg1, $arg2, ...)) 工厂方法。
 * @example constuct('classname', $arg1, $arg2, ...)) 工厂方法。
 * @example constuct(array('classname', $arg1, $arg2, ...)) 工厂方法。
 */
function constuct($class, $args=null)
{
	if (is_array($class))
	{
		// constuct(array(string classname, arg1, arg2, etc))
		$args = $class;
		$class = array_shift($args);
	}
	else if (func_num_args()>2 || (isset($args) && !is_array($args)))
	{
		// constuct(string classname, arg1, arg2, etc)
		$args = func_get_args();
		array_shift($args);
	}
	
	if (!is_string($class) && !class_exists($class, true))
	{
		return null;
	}
	
	if (isset($args))
	{
		$args = array_values($args);
		$c = count($args);
	}
	else
	{
		$c = 0;
	}
	
	switch($c)
	{
		case 0:
			$r = new $class();
			break;
		case 1:
			$r = new $class($args[0]);
			break;
		case 2:
			$r = new $class($args[0], $args[1]);
			break;
		case 3:
			$r = new $class($args[0], $args[1], $args[2]);
			break;
		case 4:
			$r = new $class($args[0], $args[1], $args[2], $args[3]);
			break;
		case 5:
			$r = new $class($args[0], $args[1], $args[2], $args[3], $args[4]);
			break;
		case 6:
			$r = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
			break;
		case 7:
			$r = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
			break;
		case 8:
			$r = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
			break;
		case 9:
			$r = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]);
			break;
		case 10:
			$r = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]);
			break;
		default:
			$r = new ReflectionClass($class);
			$r = $r->newInstanceArgs($args);
			break;
	}
	
	return $r;
}

/**
 * 获取第一个符合回调的数据。
 * @param callable $callback 回调，签名为 bool function (mixed $var) 测试 $var返回  true 或 false。
 * @param mixed $_ 可变参数，数据表。
 * @return mixed
 */
function fit($callback, $_)
{
	if (!is_callable($callback) || func_num_args()<2)
	{
		return null;
	}
	
	for ($i=1; $i<func_num_args(); $i++)
	{
		$var = func_get_arg($i);
		
		if (call_user_func($callback, $var))
		{
			return $var;
		}
	}
	
	return null;
}

/**
 * 转换为一维数据。
 * @param mixed $_ 可变参数，要转换的数据。
 * @return array
 */
function d1($_ = null)
{
	$result = array();
	for($i=0; $i<func_num_args();$i++)
	{
		$v = func_get_arg($i);
		if (is_array($v))
		{
			$result = array_merge($result, d1($v));
		}
		else
		{
			$result[] = $v;
		}
	}
	return $result;
}

/**
 * 分隔符分隔的字符串转换为数组。
 * @param string $list 分隔符分隔的字符串。
 * @param string $delimiter 可选，分隔符。默认为“,”。
 * @return array
 */
function stringlist($list, $delimiter = ',')
{
	if (is_string($list))
	{
		$list = explode($delimiter, $list);
		return array_map('trim', $list);
	}
	return null;
}

/**
 * 随机字符串。
 * @param int $length 字符串长度。
 * @param string $chars 可选，字符表，默认为 a-zA-Z0-9 区分大小写的集合。
 * @return string
 */
function randstr($length=16, $chars = 'sYBv4jefgSTUDVw9zAXCoR8c5NIJuklOEabxy7dWZ01mqn2K3FMtPLGhiQprH6')
{
	$str = '';
	for($i = 0; $i < $length; $i++)
	{
		$str .= $chars[mt_rand(0, strlen($chars) - 1)];
	}
	return $str;
}

/**
 * 组合链接。
 * @param string $url 基链接。
 * @param string|array $paramters 可选，参数，可以是数组或字符串（字符串必须已经编码过）。
 * @param string $fragment 可选，片段。
 * @return string 返回组合后的链接
 */
function combo($url, $paramters = null, $fragment = null)
{
	if (is_object($paramters))
	{
		$params = get_object_vars($paramters);
	}
	if (is_array($paramters))
	{
		$paramters = http_build_query($paramters);
	}
	if (!is_string($paramters))
	{
		$paramters = null;
	}
	
	if (!empty($paramters))
	{
		if (!empty($url))
		{
			$url .= strpos($url, '?') !== false ? '&' : '?';
			$url .= $paramters;
		}
		else
		{
			$url = '?' . $paramters;
		}
	}
	if (!empty($fragment))
	{
		if (strpos($url, '#')!==false)
		{
			$url = preg_replace('/#.*$/', '#' . $fragment, $url);
		}
		else
		{
			$url .= '#' . $fragment;
		}
	}
	
	return $url;
}

/**
 * 获取 $_GET 参数。
 * @param string $key 键名。
 * @param string $fallback 备用值，指定键不存在时返回。
 * @return string
 */
function _GET($key, $fallback=null)
{
	return ifexists($_GET, $key, $fallback);
}

/**
 * 获取 $_POST 参数。
 * @param string $key 键名。
 * @param string $fallback 备用值，指定键不存在时返回。
 * @return string
 */
function _POST($key, $fallback=null)
{
	return ifexists($_POST, $key, $fallback);
}

/**
 * 获取 $_COOKIE 参数。
 * @param string $key 键名。
 * @param string $fallback 备用值，指定键不存在时返回。
 * @return string
 */
function _COOKIE($key, $fallback=null)
{
	return ifexists($_COOKIE, $key, $fallback);
}

/**
 * 获取 $_SESSION 参数。
 * @param string $key 键名。
 * @param string $fallback 备用值，指定键不存在时返回。
 * @return string
 */
function _SESSION($key, $fallback=null)
{
	return ifexists($_SESSION, $key, $fallback);
}

/**
 * 获取 $_SERVER 参数。
 * @param string $key 键名。
 * @param string $fallback 备用值，指定键不存在时返回。
 * @return string
 */
function _SERVER($key, $fallback=null)
{
	return ifexists($_SERVER, $key, $fallback);
}

/**
 * 获取 $_REQUEST 参数。
 * @param string $key 键名。
 * @param string $fallback 备用值，指定键不存在时返回。
 * @return string
 */
function _REQUEST($key, $fallback=null)
{
	return ifexists($_REQUEST, $key, $fallback);
}

/**
 * 获取指定数组元素的值。
 * @param array $data 引用，源数组。
 * @param string $key 键名。
 * @param string $fallback 可选，备用值，指定键不存在时返回。
 * @return string
 */
function ifexists(&$data, $key, $fallback = null)
{
	return iif($data, $key, $fallback);
}

/**
 * 获取指定数组元素的值。
 * @param array $data 引用，源数组。
 * @param string $key 键名。
 * @param string $fallback 可选，备用值，指定键不存在时返回。
 * @return string
 */
function ifset(&$data, $key, $fallback = null)
{
	return iif($data, $key, $fallback);
}

/**
 * 获取指定数组元素或对象属性的值。如果是实现了 ArrayAccess 接口的对象，会尝试使用数组形式访问（优先访问属性）。
 * @param array|object $data 引用，源数组或对象。
 * @param string $key 键名。
 * @param string $fallback 可选，备用值，指定键不存在时返回。
 * @return string
 */
function iif(&$data, $key, $fallback = null)
{
	if (is_object($data))
	{
		if (is_scalar($key) && isset($data->{$key}))
		{
			return $data->{$key};
		}
	}
	if (is_array($data) || $data instanceof ArrayAccess)
	{
		if (is_scalar($key) && isset($data[$key]))
		{
			return $data[$key];
		}
	}
	return $fallback;
}

/**
 * 检测 null 值。
 * @param mixed $var 引用，变量。
 * @param mixed $fallback 可选，备选值，当 $var 为 null 时返回此值，默认值为 null，需要返回非 null 值时此参数必须设置。
 * @return mixed 变量 $var 不为 null 时返回其本身，否则返回 $fallback。
 */
function ifnull(&$var, $fallback=null)
{
	if (isset($var))
	{
		return $var;
	}
	return $fallback;
}

/**
 * 检测空值。
 * @param mixed $var 引用，变量。
 * @param mixed $fallback 可选，备选值，当 $var 为空时（empty 检测）返回此值，默认值为 null，需要返回非 null 值时此参数必须设置。
 * @return mixed 变量 $var 不为空时（empty 检测）返回其本身，否则返回 $fallback。
 */
function ifempty(&$var, $fallback=null)
{
	if (!empty($var))
	{
		return $var;
	}
	return $fallback;
}

/**
 * 简单输出变量原始值，便于调试，输出后中断流程。
 * @param mixed $_ 可变参数，要输出的变量。
 * @return void
 */
function print_var($_)
{
	if (!headers_sent())
	{
		header('Content-Type: text/html;charset: utf-8');
	}
	if (!IS_CLI)	echo '<pre>';
	for($i=0;$i<func_num_args();$i++)
	{
		print_r(func_get_arg($i));
		echo PHP_EOL;
	}
	if (!IS_CLI)	echo '</pre>';
	die;
}

/**
 * 输出调试信息。
 * @param mixed $_ 可变参数，要打印输出的变量。
 * @return void
 */
function debug($_ = null)
{
	$tz = @date_default_timezone_get();
	// date_default_timezone_set('Asia/Shanghai');
	
	echo '<h1 style="font-size: 1.5em; padding: 0; margin:0; line-height: 1.5em;">DEBUG <span style="font-size:9pt;font-weight:normal;">php v',
	PHP_VERSION,
	', server time: ', date('Y-m-d H:i:s'), ' in ', $tz,
	', session id: ', session_status() === PHP_SESSION_ACTIVE ? ('<strong>' . session_id() . '</strong>') : ('<span style="color:red;font-weight:bold;">disabled</span>'), '</strong>',
	'</span></h1>',
	'<div style="font-size:9pt;color:#333;"><pre style="color:#C33; margin:5px; padding: 5px;overflow:auto;">';
	
	if (version_compare(PHP_VERSION, '5.3.2', '>='))
	{
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	}
	else
	{
		debug_print_backtrace();
	}
	echo '</pre><ol style="  list-style-type: decimal;border:1px solid #333;background-color:#CCC;display:block;">';
	for ($i=0;$i<func_num_args();$i++)
	{
		echo '<li style="margin: 5px; border:1px solid #999;background-color:#F0F0F0;padding: 2px;"><div style="overflow:auto;background-color:white;margin:0;padding:3px;max-height: 400px;">';
		highlight_string(print_r(func_get_arg($i), true));
		echo '</div></li>';
	}
	
	$gvars = array('_GET', '_POST', '_COOKIE', '_REQUEST', '_FILES', '_SERVER', '_SESSION', '_ENV');
	foreach ($gvars as $var)
	{
		if (isset($GLOBALS[$var]))
		{
			echo '<li style="margin: 5px; border:1px solid #999;background-color:#F0F0F0;padding: 2px;">',
			'<h2 style="font-size:9pt;line-height:12pt;margin:5px;padding:0;#C33;cursor:pointer;" onclick="var p=document.getElementById(\'debug_', $var,'_content\');p.style.display=p.style.display==\'none\'?\'block\':\'none\';">$', $var,'</h2>',
			'<div style="overflow:auto;background-color:white;margin:0;padding:3px;display:none;max-height:400px;" id="debug_', $var,'_content">';
			highlight_string(print_r($GLOBALS[$var], true));
			echo '</div></li>';
		}
	}
	
	echo '<li style="margin: 5px; border:1px solid #999;background-color:#F0F0F0;padding: 2px;">',
	'<h2 style="font-size:9pt;line-height:12pt;margin:5px;padding:0;#C33;cursor:pointer;" onclick="var p=document.getElementById(\'debug_globals_content\');p.style.display=p.style.display==\'none\'?\'block\':\'none\';">$GLOBALS</h2>',
	'<div style="overflow:auto;background-color:white;margin:0;padding:3px;display:none;" id="debug_globals_content"><table>';
	
	foreach ($GLOBALS as $k=>$v)
	{
		if ($k == 'GLOBALS' || in_array($k, $gvars))
		{
			continue;
		}
		
		if (is_scalar($v))
		{
			$p = is_bool($v) ? ($v?'true':'false') : (!is_string($v)?$v:(htmlspecialchars("\"$v\"")));
		}
		else
		{
			$p = highlight_string(print_r($v, true), true);
		}
		
		echo '<tr><td style="text-align:right;font-size:9pt;"><strong>', $k, '</strong></td><td style="font-size:9pt; color:red;">=</td><td style="font-size:9pt;max-height:300px;overflow:auto;">', $p, '</td></tr>';
	}
	echo '</table></div></li>';
	
	$_ = error_get_last();
	if ($_)
	{
		echo '<li style="margin: 5px; border:1px solid #999;background-color:#F0F0F0;padding: 2px;">',
		'<h2 style="font-size:9pt;line-height:12pt;margin:5px;padding:0;#C33;cursor:pointer;" onclick="var p=document.getElementById(\'debug_last_error\');p.style.display=p.style.display==\'none\'?\'block\':\'none\';">Last Error</h2>',
		'<div style="overflow:auto;background-color:white;margin:0;padding:3px;display:none;" id="debug_last_error">';
		echo 'Error "<span style="color:#C33;">', htmlspecialchars($_['message']), '</span>" found in "<span style="color:green;">', $_['file'], '</span>" line ', $_['line']. '.';
		echo '</div></li>';
	}
	
	$_ = get_loaded_extensions();
	if (count($_)>0)
	{
		echo '<li style="margin: 5px; border:1px solid #999;background-color:#F0F0F0;padding: 2px;">',
		'<h2 style="font-size:9pt;line-height:12pt;margin:5px;padding:0;#C33;cursor:pointer;" onclick="var p=document.getElementById(\'debug_loaded_extensions\');p.style.display=p.style.display==\'none\'?\'block\':\'none\';">PHP Extensions</h2>',
		'<div style="overflow:auto;background-color:white;margin:0;padding:3px;display:none;" id="debug_loaded_extensions">';
		echo '<span>', implode('</span>, <span>', $_), '</span>';
		echo '</div></li>';
	}
	
	$_ = get_included_files();
	if (count($_)>0)
	{
		echo '<li style="margin: 5px; border:1px solid #999;background-color:#F0F0F0;padding: 2px;">',
		'<h2 style="font-size:9pt;line-height:12pt;margin:5px;padding:0;#C33;cursor:pointer;" onclick="var p=document.getElementById(\'debug_included_files_content\');p.style.display=p.style.display==\'none\'?\'block\':\'none\';">Included Files</h2>',
		'<div style="overflow:auto;background-color:white;margin:0;padding:3px;display:none;" id="debug_included_files_content">';
		echo '<ol><li>', implode('</li><li>', $_), '</li></ol>';
		echo '</div></li>';
	}
	
	$_ = get_defined_constants(true);
	$_ = $_['user'];
	if (count($_)>0)
	{
		echo '<li style="margin: 5px; border:1px solid #999;background-color:#F0F0F0;padding: 2px;">',
		'<h2 style="font-size:9pt;line-height:12pt;margin:5px;padding:0;#C33;cursor:pointer;" onclick="var p=document.getElementById(\'debug_defined_constants\');p.style.display=p.style.display==\'none\'?\'block\':\'none\';">Defined Constants</h2>',
		'<div style="overflow:auto;background-color:white;margin:0;padding:3px;display:none;" id="debug_defined_constants">';
		echo '<table>';
		foreach($_ as $k=>$v)
		{
			$p = is_bool($v) ? ($v?'true':'false') : (!is_string($v)?$v:(htmlspecialchars("\"$v\"")));
			echo '<tr><td style="text-align:right;font-size:9pt;"><strong>', $k, '</strong></td><td style="font-size:9pt; color:red;">=</td><td style="font-size:9pt;">', $p, '</td></tr>';
		}
		echo '</table>';
		echo '</div></li>';
	}
	
	echo '</ol><div>php.ini: <span style="color:green;">"',php_ini_loaded_file(), '"</span>';
	if (isset($_SERVER['REQUEST_TIME_FLOAT']))
	{
		echo ', runtime: ', microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 'ms';
	}
	die('</div></div>');
}

/**
 * 写日志到文件中。每次写入并入一行，并添加时间戳。
 * @param string $_ 可变参数。
 * @return void
 */
function trace_log()
{
	if (func_num_args()<1)
	{
		return;
	}
	$web = SAPI === SAPI_WEB;
	if ($web)
	{
		$browser = web::browser();
		$ip = $browser->ip;
		$ua = $browser->useragent;
		$m = $browser->method;
		$url = $browser->request;
	}
	
	$ds = DIRECTORY_SEPARATOR;
	$dir = BASE . DS . 'logs'. DS . date("Y{$ds}m");
	if (!is_dir($dir))
	{
		@mkdir($dir, 0777, true);
		if (!is_dir($dir))
		{
			error_log("Cannot create directory \"$dir\".");
			return;
		}
	}
	$file = $dir . DS . date('d') . '.log';
	$h=fopen($file, 'a');
	if ($h)
	{
		$time = date('Y-m-d H:i:s');
		$time = "[$time] " . ($web ? "[$ip] $m $url\n" : '');
		if (flock($h, LOCK_EX))
		{
			fwrite($h, $time);
			for ($i=0; $i<func_num_args(); $i++)
			{
				$var = func_get_arg($i);
				if (!is_scalar($var))
				{
					$var = PHP_EOL . print_r($var, true) . PHP_EOL;
				}
				else if (is_resource($var))
				{
					$var = '[RESOURCE]';
				}
				
				fwrite($h, ($i>0 ? "\t" : '') . $var . '');
			}
			fwrite($h, PHP_EOL);
			flock($h, LOCK_UN);
		}
		fclose($h);
	}
	else
	{
		error_log("Cannot write file \"$file\".");
	}
}

/**
 * 写日志到文件中。每次写入并入一行，并添加时间戳。
 * @param string $name 日志名。
 * @param string $_ 可变参数。要记录的数据。
 * @return void
 */
function write_log($name)
{
	if (func_num_args()<2)
	{
		return;
	}
	
	$web = SAPI === SAPI_WEB;
	if ($web)
	{
		$browser = web::browser();
		$ip = $browser->ip;
		$ua = $browser->useragent;
		$m = $browser->method;
		$url = $browser->request;
	}
	
	$ds = DIRECTORY_SEPARATOR;
	$dir = BASE . DS . 'logs'. DS . date("Y{$ds}m");
	if (!is_dir($dir))
	{
		@mkdir($dir, 0777, true);
		if (!is_dir($dir))
		{
			error_log("Cannot create directory \"$dir\".");
			return;
		}
	}
	$file = $dir . DS . $name . '-' . date('d') . '.log';
	$h=fopen($file, 'a');
	if ($h)
	{
		$time = date('Y-m-d H:i:s');
		$time = "[$time] " . ($web ? "[$ip] $m $url\n" : '');
		if (flock($h, LOCK_EX))
		{
			fwrite($h, $time);
			for ($i=1; $i<func_num_args(); $i++)
			{
				$var = func_get_arg($i);
				if (!is_scalar($var))
				{
					$var = PHP_EOL . print_r($var, true) . PHP_EOL;
				}
				else if (is_resource($var))
				{
					$var = '[RESOURCE]';
				}
				
				fwrite($h, ($i>0 ? "\t" : '') . $var . '');
			}
			fwrite($h, PHP_EOL);
			flock($h, LOCK_UN);
		}
		fclose($h);
	}
	else
	{
		error_log("Cannot write file \"$file\".");
	}
}

if (!function_exists('fastcgi_finish_request'))
{
	/**
	 * 此函数在 PHP FPM 运行模式下冲刷(flush)所有响应的数据给客户端并结束请求。 这使得客户端结束连接后，需要大量时间运行的任务能够继续运行。 
	 */
	function fastcgi_finish_request()
	{
		flush();
		set_time_limit(0);
		ignore_user_abort(true);
	}
}

/**
 * web get.
 * @param string $url 基链接。
 * @param string|array $params 可选，POST 数据表，表示使用 POST 提交。
 * 上传文件则在文件名前加 @ 前缀。示例 array('file'=>'@/path/')。也可以使用 CURLFile 实例指定上传文件项。
 * @param array $settings 可选，设置项，包括：
 * <ol>
 * <li>headers array 请求头。示例：array('User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:39.0) Gecko/20100101 Firefox/39.0', 'Accept-Encoding: gzip, deflate')</li>
 * <li>timeout int 超时时间，单位：秒。默认不超时。</li>
 * <li>nobody bool 不包含正文，仅返回响应头信息。默认为 false。</li>
 * <li>onlybody bool 仅返回正文，不包含头和状态信息。默认为 true。如果此选项设置，则 nobody 将被忽略。</li>
 * <li>charset string 字符集，如果返回数据指定的字符集与此处的不兼容则自动转换，默认为 utf-8。</li>
 * </ol>
 * @return string|array 返回数组包括：status HTTP 状态，info 请求信息，headers 响应头信息，content 正文流数据，
 * error array 错误信息（如果有错误则包含 code 和 message）。如果指定 onlybody 选项为 true 则直接返回正文流。当超时或出错时返回 false。
 */
function wget($url, $params = null, array $settings = null)
{
	$conn = curl_init();
	
	curl_setopt($conn, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($conn, CURLOPT_MAXREDIRS, 10);
	
	$timeout = iif($settings, 'timeout');
	$timeout = intval($timeout);
	if ($timeout>0)
	{
		curl_setopt($conn, CURLOPT_TIMEOUT, $timeout);
	}
	
	$headers = iif($settings, 'headers');
	if (is_array($headers))
	{
		curl_setopt($conn, CURLOPT_HTTPHEADER, $headers);
	}
	
	$nobody = iif($settings, 'nobody');
	$nobody = boolval($nobody);
	
	$onlybody = iif($settings, 'onlybody');
	$onlybody = boolval($onlybody);
	
	if ($onlybody)
	{
		$nobody = false;
	}
	
	if ($nobody)
	{
		curl_setopt($conn, CURLOPT_NOBODY, $nobody);
	}
	
	if(stripos($url, 'https://') !== false)
	{
		curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($conn, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
	}
	
	curl_setopt($conn, CURLOPT_URL, $url);
	curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
	
	if (is_object($params))
	{
		$params = get_object_vars($params);
	}
	if (!is_string($params) && !is_array($params))
	{
		$params = null;
	}
	
	if (class_exists('\CURLFile'))
	{
		curl_setopt($conn, CURLOPT_SAFE_UPLOAD, true);
	}
	else if (defined('CURLOPT_SAFE_UPLOAD'))
	{
		curl_setopt($conn, CURLOPT_SAFE_UPLOAD, false);
	}
	
	if (isset($params))
	{
		curl_setopt($conn, CURLOPT_POST, true);
		if (is_array($params) and class_exists('\CURLFile'))
		{
			foreach ($params as $k=>&$v)
			{
				if (is_string($v) && !empty($v) && $v[0]==='@')
				{
					$v = substr($v, 1);
					$v = new \CURLFile($v);
				}
			}
		}
		curl_setopt($conn, CURLOPT_POSTFIELDS, $params);
	}
	
	$headers = null;
	curl_setopt($conn, CURLOPT_HEADERFUNCTION, function($conn, $header) use(&$headers)
	{
		$len = strlen($header);
		if (preg_match('/^HTTP\/(?<version>1\.[01])\s(?<status>\d{3})\s(?<text>.*)$/', $header, $matches))
		{
			$headers['HTTP'] = array('version'=>$matches['version'], 'status'=>$matches['status'], 'text'=>$matches['text']);
			return $len;
		}
		$header = trim($header);
		if (!empty($header))
		{
			$hs = array_map('trim', explode(':', $header, 2));
			if (count($hs)<2)
			{
				$hs[] = null;
			}
			list($k, $v) = $hs;
			
			if (array_key_exists($k, $headers))
			{
				// 多个响应头
				if (!is_array($headers[$k]))
				{
					$headers[$k] = array($headers[$k]);
				}
				$headers[$k][] = $v;
			}
			else
			{
				$headers[$k] = $v;
			}
		}
		return $len;
	});
	
	$content = curl_exec($conn);
	$error = null;
	$e = curl_errno($conn);
	if ($e !== 0)
	{
		$error = array('code'=>$e, 'message'=>curl_error($conn));
	}
	
	$ct = iif($headers, 'Content-Type');
	if (!empty($ct))
	{
		// Content-Type: application/json;charset=utf-8;
		$charset = iif($settings, 'charset');
		if (!empty($charset))
		{
			$charset = trim($charset);
		}
		else
		{
			$charset = 'utf-8';
		}
		
		$response_charset = preg_fetch('{\bcharset\s*=\s*(.*)$}i', 1, $ct);
		if (!empty($response_charset) && strcasecmp(trim($response_charset), $charset)!==0)
		{
			// 编码转换
			$content = iconv($response_charset, $charset, $content);
		}
	}
	
	$info = curl_getinfo($conn);
	curl_close($conn);
	$status = intval($info['http_code']);
	
	if ($onlybody)
	{
		// 出错时返回 false，否则返回正文文本。
		return $content;
	}
	
	if ($nobody)
	{
		$content = null;
	}
	
	return array('status'=>$status, 'info'=>$info, 'headers'=>$headers, 'content'=>$content, 'error'=>$error);
}
