<?php

@define('NOCHECK', TRUE);
//set_magic_quotes_runtime(0);
define('FROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
include_once("./common/functions.php");
include_once("./common/types.php");
include_once(FROOT."lib/db/mysql.inc.php");
include_once(FROOT."config/config.php");
include_once(FROOT."config/lang.php");
//include_once(FROOT."../member/config.php");

//$ecms_userid = $cfg_ml->M_ID;
//echo "--".$ecms_userid;

// 获取数据库连接
$udb = new UDB(); // 创建数据库工具类对象
// 连接数据库
$udb -> connect($config_database_server, $config_database_username, $config_database_password);	
$udb -> query("USE `nocheck`"); // 切换到chisha数据库
$udb -> query("SET NAMES UTF8"); // 设定传输编码


$magic_quote = get_magic_quotes_gpc();
if(empty($magic_quote)) {
	$_GET = saddslashes($_GET);
	$_POST = saddslashes($_POST);
}


?>
