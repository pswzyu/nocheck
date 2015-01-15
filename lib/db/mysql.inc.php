<?php
/**
 * @Author : PSWZ-ZhangY <pswzyu@gmail.com>
 * @License : GNU GPLv3
 */
?>
<?php

/*
 * 操作数据库的类， 需要先进行实例化然后使用
 */
class UDB
{
	private $connection;
	function UDB()
	{
	}
	function connect($server, $username, $password)
	{
		$this -> connection = mysqli_connect($server, $username, $password);
		return $this -> connection;
	}
	function query($sql)
	{
		$query_result = mysqli_query($this -> connection, $sql);
		if ($this->get_error_no() != 0)
		{
			echo "SQL Query Error:".$sql;
		}
		return $query_result;
	}
        // execute a sql statement, and return if the result is empty or not
        function is_empty($sql)
        {
                $query_handle = $this -> query($sql);
		$tmp_result = $this -> fetch_assoc($query_handle);
		$this -> free_result($query_handle);
		if ($tmp_result)
                { return TRUE; }
                else
                { return FALSE; }
        }
	function fetch_assoc($query_handle)
	{
            if ($query_handle){
                return mysqli_fetch_assoc($query_handle);
            }else{
                return false;
            }
	}
	function get_error_no()
	{
		return mysqli_errno($this -> connection);
	}
	function free_result($query_handle)
	{
            if ($query_handle)
            {
                mysqli_free_result($query_handle);
            }
	}
	function inserted_id()
	{
		/*$this->query_result = mysqli_query("SELECT LAST_INSERT_ID();", $this -> connection);
                $result = $this->fetch_assoc();
		$this->free_result();
		return $result["LAST_INSERT_ID()"];*/
                return mysqli_insert_id($this->connection);
	}
	function close()
	{
		mysqli_close($this -> connection);
		$this -> connection = null;
		return $this -> connection;
	}
	/**
	 * 将数据库时间戳格式转换为php时间戳格式， 静态方法
	 * @param string $mysql_timestamp
	 * @return int 整形的php时间戳
	 */
	static function timestamp_db_to_php($mysql_timestamp)
	{
		return strtotime($mysql_timestamp);
	}
	/**
	 * 将数据库时间戳格式转换为php时间戳格式， 静态方法
	 * @param string $mysql_timestamp
	 * @return int 整形的php时间戳
	 */
	static function timestamp_php_to_db($php_timestamp)
	{
		return date("Y-m-d H:i:s", $php_timestamp);
	}
}



?>
