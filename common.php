<?php

define('NOCHECK', TRUE);
//set_magic_quotes_runtime(0);
define('FROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
include_once(FROOT."common/functions.php");
include_once(FROOT."common/types.php");
include_once(FROOT."lib/db/mysql.inc.php");
include_once(FROOT."config/config.php");
include_once(FROOT."config/config.private.php");
include_once(FROOT."config/lang.php");

include __DIR__.DIRECTORY_SEPARATOR."./lib/php/PHPMailer/PHPMailerAutoload.php";

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

function format_error( $errno, $errstr, $errfile, $errline ) {
    $trace = print_r( debug_backtrace( false ), true );

    $content  = "<table><thead bgcolor='#c8c8c8'><th>Item</th><th>Description</th></thead><tbody>";
    $content .= "<tr valign='top'><td><b>Error</b></td><td><pre>$errstr</pre></td></tr>";
    $content .= "<tr valign='top'><td><b>Errno</b></td><td><pre>$errno</pre></td></tr>";
    $content .= "<tr valign='top'><td><b>File</b></td><td>$errfile</td></tr>";
    $content .= "<tr valign='top'><td><b>Line</b></td><td>$errline</td></tr>";
    $content .= "<tr valign='top'><td><b>Trace</b></td><td><pre>$trace</pre></td></tr>";
    $content .= '</tbody></table>';

    return $content;
}

register_shutdown_function(function (){
    
    global $config_email_username, $config_email_password, $config_email_fromname;
    // init the email backend
    $mail = new PHPMailer();

    // ---------- adjust these lines ---------------------------------------
    $mail->Username = $config_email_username; // your GMail user name
    $mail->Password = $config_email_password;
    $mail->FromName = $config_email_fromname; // readable name

    $mail->Subject = "ALERT:Fatal Error!";
    //-----------------------------------------------------------------------

    $mail->Host = "ssl://smtp.gmail.com"; // GMail
    $mail->Port = 465;
    $mail->IsSMTP(); // use SMTP
    $mail->SMTPAuth = true; // turn on SMTP authentication
    $mail->From = $mail->Username;
    
    $mail->ClearAddresses();
    $mail->AddAddress($config_email_username); // recipients email
    
    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if( $error !== NULL) {
        $errno   = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];
        
        $mail->Body    = format_error( $errno, $errstr, $errfile, $errline);

        if(!$mail->Send())
            echo "Mailer Error: " . $mail->ErrorInfo."\n";
        else
            echo "Message has been sent!\n";
    }
    
});


$magic_quote = get_magic_quotes_gpc();
if(empty($magic_quote)) {
	$_GET = saddslashes(shtmlspecialchars($_GET));
	$_POST = saddslashes(shtmlspecialchars($_POST));
}


?>