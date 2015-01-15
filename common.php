<?php

@define('NOCHECK', TRUE);
//set_magic_quotes_runtime(0);
define('FROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
include_once(FROOT."common/functions.php");
include_once(FROOT."common/types.php");
include_once(FROOT."lib/db/mysql.inc.php");
include_once(FROOT."config/config.php");
@include_once(FROOT."config/config.private.php");
include_once(FROOT."config/lang.php");
//include_once(FROOT."../member/config.php");

//$ecms_userid = $cfg_ml->M_ID;
//echo "--".$ecms_userid;

date_default_timezone_set($config_server_timezone);

// define the autoloader function
spl_autoload_register(function ($class_name){
    // first split the $class_name to find the top namespace
    $ns_path = explode("\\", $class_name);
    // for Respect, the validator, map the class name to the dir name
    if ($ns_path[0] == "Respect") {
        // connect the path from the third one with dir separater
        $filepath = "";
        for ($step = 2; $step != count($ns_path); $step++) {
            $filepath .= $ns_path[$step];
            if ($step != count($ns_path)-1){
                $filepath .= "/";
            }
        }
        $filepath .= ".php";
        include (FROOT.'lib/php/Validation/library/'.$filepath);
    }
});

$udb = new UDB();
$udb -> connect($config_database_server, $config_database_username, $config_database_password);
$udb -> query("USE `nocheck`");
$udb -> query("SET NAMES UTF8");

// set the error handle function
set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext){
    global $udb;
    // convert the information
    $errstr = saddslashes($errstr);
    $errfile = saddslashes($errfile);
    $errcontext = saddslashes(serialize($errcontext));
    
    $udb->query("INSERT INTO `nocheck_errlog` (`id`, `errno`, `errstr`, `errfile`, `errline`, `errcontext`)
            VALUES( NULL, {$errno}, '{$errstr}', '{$errfile}', {$errline}, '{$errcontext}' )");    
    
});


$magic_quote = get_magic_quotes_gpc();
if(empty($magic_quote)) {
	$_GET = saddslashes(shtmlspecialchars($_GET));
	$_POST = saddslashes(shtmlspecialchars($_POST));
}


?>