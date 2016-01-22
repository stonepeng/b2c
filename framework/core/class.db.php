<?php

/**
 * db.mysqli。
 * @author Stone
 */
class DB extends mysqli
{
	/**
	 * sql语句。
	 * @var string
	 */
	public $sql;
	
	/**
	 * 表前缀。
	 * @var string
	 */
	public $prefix;
	
	/**
	 * mysqli实例对象。
	 * @var object
	 */
	protected $mysqli;
	
	/**
	 * 结果集。
	 * @var resource
	 */
	protected $rs;
	
	/**
	 * 执行次数。
	 * @var integer
	 */
	protected $query_num	= 0;
	
	/**
	 * 缓存类对象。
	 * @var object
	 */
	protected $cache;
	
	/**
	 * 重载。
	 * @var bool
	 */
	protected $reload     = false;
	
	/**
	 * 缓存标记。
	 * @var bool
	 */
	protected $cache_mark = true;
	protected $fetch_mode	= MYSQLI_ASSOC;
	
	/**
	 * db.mysqli。
	 * @param string $dbhost
	 * @param string $dbuser
	 * @param string $dbpass
	 * @param string $dbname
	 * @param string $prefix
	 * @param int $dbport
	 * @return void
	 */
	public function  __construct($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dbport = 3306)
	{
		$this->prefix = $prefix;
		@parent::__construct($dbhost, $dbuser, $dbpass, $dbname, $dbport);
		
		if($this->connect_errno)
		{
			die('<h2>'. $this->connect_error .'</h2>');
		}
		else
		{
			parent::set_charset("utf8");
		}
	}

	/**
	 * 缓存类对象。
	 * @param mixed $cache
	 * @example 文件缓存、memcache键值对缓存
	 * @return void
	 */
	public function cache_obj($cache)
	{
		$this->cache	= $cache;
	}

	/**
	 * 释放结果集和关闭数据库连接。
	 * @return void
	 */
	public function  __destruct()
	{
		$this->free();
		$this->close();
	}

	/**
	 * 释放结果集所占资源
	 * @return void 
	 */
	protected function free()
	{
		@$this->rs->free();
	}

	/**
	 * 获取结果集
	 * @return array
	 */
	protected function fetch()
	{
		return $this->rs->fetch_array($this->fetch_mode);
	}

	/**
	 * 获取查询的sql语句。
	 * @param string $sql
	 * @param string $limit
	 * @return string
	 */
	protected function get_query_sql($sql, $limit = null)
	{
		$sql = $this->sql($sql);
		if (@preg_match("/[0-9]+(,[ ]?[0-9]+)?/is", $limit) && !preg_match("/ LIMIT [0-9]+(,[ ]?[0-9]+)?$/is", $sql))
		{
			$sql .= " LIMIT " . $limit;
		}
		return $sql;
	}

	/**
	 * 缓存中获取数据。
	 * @param string $sql
	 * @param string $method
	 * @return resource
	 */
	protected function get_cache($sql, $method)
	{
		$cache_file = md5($sql . $method);
		$res = $this->cache->get($cache_file);
		if(!$res)
		{
			//如果缓存文件过期或不存在的话，返回false；如果缓存文件存在且未过期的话，则返回缓存数据
			$res = $this->$method($sql);	//先从缓存中取数据，如果缓存中没数据，则从数据库中取数据
			if($res && $this->cache_mark && !$this->reload)
			{
				$this->cache->set($cache_file, $res); //如果缓存文件过期或不存在的话，将重新将从数据库中查询的数据放入缓存文件
			}
		}
		return $res;
	}

	/**
	 * 获取查询次数。
	 * @return int
	 */
	public function query_num()
	{
		return $this->query_num;
	}
	
	/**
	 * 执行sql语句查询。
	 * @param string $sql
	 * @param $string $limit 
	 * @return resource
	 */
	public function query($sql, $limit = null)
	{
		$sql = $this->get_query_sql($sql, $limit);
		$this->sql[] = $sql;
		$this->rs = parent::query($sql);
		if (!$this->rs)
		{
			echo "<p>error: ". $this->error ."</p>"; 
			die("<p>sql: ".$sql."</p>");
		}
		else
		{
			$this->query_num++;
			return $this->rs;
		}
	}
	
	/**
	 * 获取单条记录的单个字段值。
	 * @param string $sql
	 * @return string
	 */
	public function one($sql)
	{
		$this->query($sql, 1);
		$this->fetch_mode = MYSQLI_NUM;
		$row = $this->fetch();
		$this->free();
		return $row[0];
	}

	/**
	 * 缓存单个字段。
	 * @param string $sql
	 * @param bool $reload
	 * @return string
	 */
	public function cache_one($sql, $reload = false)
	{
		$this->reload	= $reload;
		$sql    = $this->get_query_sql($sql, 1);
		return $this->get_cache($sql, 'one');
	}
	
	/**
	 * 获取行记录。
	 * @param string $sql
	 * @param string $fetch_mode
	 * @return array
	 */
	public function row($sql, $fetch_mode = MYSQLI_ASSOC)
	{
		$this->query($sql, 1);
		$this->fetch_mode    = $fetch_mode;
		$row = $this->fetch();
		$this->free();
		return $row;
	}
	
	/**
	 * 缓存行。
	 * @param string $sql
	 * @param bool $reload
	 * @return array
	 */
	public function cache_row($sql, $reload = false)
	{
		$this->reload	= $reload;
		$sql    = $this->get_query_sql($sql, 1);
		return $this->get_cache($sql, 'row');
	}
	
	/**
	 * 返回所有的结果集。
	 * @param string $sql
	 * @param mixed $limit
	 * @param string $fetch_mode
	 * @return array
	 */
	public function all($sql, $limit = null, $fetch_mode = MYSQLI_ASSOC)
	{
		$rows = array();
		$this->query($sql, $limit);
		$this->fetch_mode = $fetch_mode;
		
		while($row = $this->fetch())
		{
			$rows[] = $row;
		}

		$this->free();
		return $rows;
	}
	
	/**
	 * 缓存所有行。
	 * @param string $sql
	 * @param string $reload
	 * @param mixed $limit
	 * @return array
	 */
	public function cache_all($sql, $reload = false, $limit = null)
	{
		$this->reload	= $reload;
		$sql = $this->get_query_sql($sql, $limit);
		return $this->get_cache($sql, 'all');
	}

	/**
	 * 返回前一次mysql操作所影响的记录行数。
	 * @return int
	 */
	public function affected_rows()
	{
		return $this->affected_rows;
	}
	
	 /**
	 * 获取插入语句。
     * @param string $tbl_name 表名
     * @param array $info 数据
     * @return string
     */
    public function get_insert_db_sql($tbl_name, $info)
	{
		//首先判断是否为数组，再判断数组是否为空
        if(is_array($info)&&!empty($info))
        {
            $i = 0;
            foreach($info as $key=>$val)
            {
                $fields[$i] = $key;	//将所有的键名放到一个$fields[]数组中
                $values[$i] = $val;	//将所有的值放到一个$values[]数组中
                $i++;
            }
            $s_fields = "(".implode(",",$fields).")";
            $s_values  = "('".implode("','",$values)."')";
            $sql = "INSERT INTO $tbl_name $s_fields VALUES $s_values";
            return $sql;
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取替换语句:replace into是insert into的增强版
     * 区别：replace into跟insert功能类似，不同点在于：replace into 首先尝试插入数据到表中，如果发现表中
			 已经有此行数据(根据主键或唯一索引判断)，则先删除此行数据，然后插入新的数据，否则直接插入新数据
     * @param    string     $tbl_name   表名
     * @param    array      $info       数据
     */
    public function get_replace_db_sql($tbl_name,$info)
    {
        if(is_array($info)&&!empty($info))
        {
            $i = 0;
            foreach($info as $key=>$val)
            {
                $fields[$i] = $key;
                $values[$i] = $val;
                $i++;
            }
            $s_fields = "(".implode(",",$fields).")";
            $s_values  = "('".implode("','",$values)."')";
            $sql = "REPLACE INTO
                        $tbl_name
                        $s_fields
                    VALUES
                        $s_values";
            Return $sql;
        }
        else
        {
            Return false;
        }
    }
    
  /**
     * 获取更新SQL语句
     *
     * @param    string     $tbl_name   表名
     * @param    array      $info       数据
     * @param    array      $condition  条件
     */
    public function get_update_db_sql($tbl_name,$info,$condition)
    {
        $i = 0;
        $data = '';
        if(is_array($info)&&!empty($info))
        {
            foreach( $info as $key=>$val )
            {
                if(isset($val))
                {
                    if($i==0&&$val!==null)
                    {
                        $data = $key."='".$val."'";	//第一次：如，update 表名 set name='admin'
                    }
                    else
                    {
                        $data .= ",".$key." = '".$val."'";//非第一次：如， ，p='123'
                    }
                    $i++;
                }
            }	
            $sql = "UPDATE $tbl_name SET $data WHERE ".$condition;
            return $sql;
        }
        else
        {
            Return false;
        }
    }
    
    /**
     * 取得数据库最后一个插入ID。
     * @return int
     */
    public function last_id()
    {
        return $this->insert_id;
    }

	/**
	 * 预处理sql。
	 * @param string $sql
	 * @example [users] 转义成带前缀表名 prefix.users
	 * @return string
	 */    
    public function sql($sql)
    {
    	if (isset($this->prefix) && !empty($this->prefix))
    	{
	    	$sql = preg_replace('/\[/', $this->prefix, $sql);
	    	$sql = preg_replace('/\]/', '', $sql);
    	}
    	return self::escape_string($sql);
    }
    
    /**
     * 转义字符串。
     * @param string $var 源字串。
     * @return string
     */
    public static function escape($var)
    {
    	if ($var instanceof self)
    	{
    		return $var;
    	}
    	if (is_array($var))
    	{
    		foreach($var as &$s)
    		{
    			$s = self::escape($s);
    		}
    		return $var;
    	}
    
    	if (!is_string($var) || strlen($var) == 0)
    	{
    		return $var;
    	}
    
    	/* if (function_exists('mysql_real_escape_string'))
    	 {
    	 return mysql_real_escape_string($var);
    	 }
    	 else if (function_exists('mysql_escape_string'))
    	 {
    	 return mysql_escape_string( $var );
    	 } */
    
    	/*
    	 \v		垂直 tab 符, ASCII 11(0x0B)。// PHP but MySQL
    	 \f		ASCII 12(0x0C)。 // PHP but MySQL
    
    	 \0		ASCII 0(NUL)字符。
    	 \'		单引号(‘'’)。
    	 \"		双引号(‘"’)。
    
    	 \b		回退符, ASCII 8(0x08)。
    	 \n		换行符, ASCII 10(0x0A)。
    	 \r		回车符, ASCII 13(0x0D)。
    	 \t		tab字符, ASCII 9(0x09)。
    	 \Z		ASCII 26(0x1A)。 // MySQL, (控制（Ctrl）-Z)。
    	 该字符可以编码为‘\Z’，以允许你解决在Windows中ASCII 26代表文件结尾这一问题。
    	 (如果你试图使用mysql db_name < file_name，ASCII 26会带来问题）。
    	 \\		反斜线(‘\’)字符。
    	 \%		‘%’字符。仅在 MySQL 的 LIKE 语句中使用。
    	 \_		‘_’字符。仅在 MySQL 的 LIKE 语句中使用。
    	 */
    
    	// WARNING: \ 斜线的替换要放在最前面。
    	$s = array("\\", "\0", "'", '"', "\x08", "\n", "\r", "\t", "\x1A");
    	$c = array("\\\\", "\\0", "\\'", '\\"', "\\b", "\\n", "\\r", "\\t", "\\Z");
    
    	return str_replace($s, $c, $var);
    }
}