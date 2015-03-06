<?php 

$config_server_timezone = "";

$config_database_server = "";
$config_database_username = "";
$config_database_password = "";

$config_checker_path = ".".DIRECTORY_SEPARATOR."visa_checker".DIRECTORY_SEPARATOR."visa_checker";
$config_checker_options = "";

// email related
$config_email_username = "issuemyvisatoday@gmail.com";
$config_email_password = ""; 
$config_email_fromname = "issuemyvisa.today"; // readable name
$config_email_subject = "Your visa status is updated!";
$config_email_body    = "Hi,\n There is a update on your case %s, go check it out!\n Status has been changed from "
        . "%s to %s."; 
$config_email_error_body = "Hi,\n The DS-160 Case ID you entered in issuemyvisa.today may be incorrect. You can modify"
                    . " your case information at http://issuemyvisa.today/nocheck/index.php?do=case&ac=update&id=%d";

