<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * this script is used to crawl the check.info website to get the records, if the records are updated
 * from check.info, this script will also update the record in our database
 * 
 * also, this script will find out the records in our database which are still not finished, use
 * their ds160 id to check the status of this application on ceac website.
 * this script will also call the email sending utility to send email to the user about this update
 * 
 * the parameter of the command is the action, the action can be "checkee" or "ceac"
 */

include_once("./common/types.php");
include_once("./common/functions.php");
include_once("./common/utils/log.php");
include_once("./lib/db/mysql.inc.php");
include_once("./common.php");

$start_y = intval("2012");
$start_m = intval("08");

$end_y = intval(date("Y"));
$end_m = intval(date("m"));

if (!isset($argv[1])){
    die("Error: No action given!\n");
}
$ac = $argv[1];
$acs = array("checkee", "ceac");
if(empty($ac) || !in_array($ac, $acs)) {
    die("Error: Invalid action!\n");
}


if ($ac == "checkee")
{
    
/*
 * first get the info from check.info
 */
while (true)
{
    echo "".$end_y."-".$end_m."\n";
    $s_end_m = $end_m < 10? "0$end_m":"$end_m";
    $page_content = file_get_contents("http://www.checkee.info/main.php?dispdate={$end_y}-{$s_end_m}");
    if (!$page_content)
    {
        die("network fail");
    }
    preg_match_all('/.\/update.php\?casenum=\d+/', $page_content, $matches);
    foreach ($matches[0] as $key => $value) {
        
        $address = explode("=", $value);
        $casenum = $address[1]; // we need the number behind =
        
        $no_error = TRUE;
        $error_msg = "Error:{$casenum}:";
        
        $test = file_get_contents("http://www.checkee.info/update.php?casenum={$casenum}");
        
        $info = array();
        ////////////////////////
        preg_match('/name="casenum" value="(\d+)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["Checkee_CaseId"] = $matches[1];
        }else{
            $info["Checkee_CaseId"] = "";
            $no_error = FALSE;
            $error_msg .= "Checkee_CaseId,";
        }
        ////////////////////////
        preg_match('/name="id" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["Nickname"] = $matches[1];
        }else{
            $info["Nickname"] = "";
            $no_error = FALSE;
            $error_msg .= "Nickname,";
        }
        ////////////////////////
        preg_match('/name="email" value="([\w.\@ -]*?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["Email"] = $matches[1];
        }else{
            $info["Email"] = "";
            $no_error = FALSE;
            $error_msg .= "Email,";
        }
        ///////////////////////
        preg_match('/NAME="check_date" VALUE="([\w-]+)"/', $test, $matches);
        if(isset($matches[1])){
            $info["ApplicationDate"] = $matches[1];
        }else{
            $info["ApplicationDate"] = "";
            $no_error = FALSE;
            $error_msg .= "ApplicationDate,";
        }
        //////////////////////
        // type entry consulate status
        preg_match_all('/option value="([\w.\/ ]*?)" SELECTED/', $test, $matches);
        if(isset($matches[1][1])){
            $info["VisaType"] = $matches[1][1];
        }else{
            $info["VisaType"] = "";
            $no_error = FALSE;
            $error_msg .= "VisaType,";
        }
        if(isset($matches[1][2])){
            $info["VisaEntry"] = $matches[1][2];
        }else{
            $info["VisaEntry"] = "";
            $no_error = FALSE;
            $error_msg .= "VisaEntry,";
        }
        if(isset($matches[1][3])){
            $info["Consulate"] = $matches[1][3];
        }else{
            $info["Consulate"] = "";
            $no_error = FALSE;
            $error_msg .= "Consulate,";
        }
        if(isset($matches[1][4])){
            $info["ApplicationStatus"] = $matches[1][4];
        }else{
            $info["ApplicationStatus"] = "";
            $no_error = FALSE;
            $error_msg .= "ApplicationStatus,";
        }
        if(isset($matches[1][5])){
            $info["Degree"] = $matches[1][5];
        }else{
            $info["Degree"] = "";
            $no_error = FALSE;
            $error_msg .= "Degree,";
        }
        //////////////////////
        preg_match('/name="major" value="(\X*?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["Major"] = $matches[1];
        }else{
            $info["Major"] = "";
            $no_error = FALSE;
            $error_msg .= "Major,";
        }
        //////////////////////
        preg_match('/NAME="clear_date" VALUE="([\w-]+)"/', $test, $matches);
        if(isset($matches[1])){
            $info["ClearanceDate"] = $matches[1];
        }else{
            $info["ClearanceDate"] = "";
            $no_error = FALSE;
            $error_msg .= "ClearanceDate,";
        }
        ///////////////////////
        preg_match('/name="note">(\X*?)<\/textarea>/', $test, $matches);
        if(isset($matches[1])){
            $info["Note"] = $matches[1];
        }else{
            $info["Note"] = "";
            $no_error = FALSE;
            $error_msg .= "Note,";
        }
        ///////////////////////
        preg_match('/name="lastname" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["LastName"] = $matches[1];
        }else{
            $info["LastName"] = "";
            $no_error = FALSE;
            $error_msg .= "LastName,";
        }
        ///////////////////////
        preg_match('/name="firstname" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["FirstName"] = $matches[1];
        }else{
            $info["FirstName"] = "";
            $no_error = FALSE;
            $error_msg .= "FirstName,";
        }
        /////////////////////////
        preg_match('/name="univ_college" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["University"] = $matches[1];
        }else{
            $info["University"] = "";
            $no_error = FALSE;
            $error_msg .= "University,";
        }
        //////////////////////////
        preg_match('/name="employer" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["Employer"] = $matches[1];
        }else{
            $info["Employer"] = "";
            $no_error = FALSE;
            $error_msg .= "Employer,";
        }
        //////////////////////////
        preg_match('/name="job_title" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["JobTitle"] = $matches[1];
        }else{
            $info["JobTitle"] = "";
            $no_error = FALSE;
            $error_msg .= "JobTitle,";
        }
        //////////////////////////
        preg_match('/name="years_in_usa" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["YearsInUSA"] = $matches[1];
        }else{
            $info["YearsInUSA"] = "";
            $no_error = FALSE;
            $error_msg .= "YearsInUSA,";
        }
        //////////////////////////
        preg_match('/name="country" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["Citizenship"] = $matches[1];
        }else{
            $info["Citizenship"] = "";
            $no_error = FALSE;
            $error_msg .= "Citizenship,";
        }
        ///////////////////////
        
        $info = saddslashes($info);
        
        if (!$no_error)
        {
            echo $error_msg."\n";
        }
        
        // first check if this case is already in the databse
        $query_handle = $udb->query("SELECT * FROM `nocheck_cases` WHERE `Checkee_CaseId`={$info["Checkee_CaseId"]}");
        if ($udb->get_error_no()){
            die("Error:{$info["Checkee_CaseId"]}");
        }
        // if this record is already in the database
        if ($udb->fetch_assoc($query_handle)){
            // update the record in the database
            $sql = "UPDATE `nocheck_cases` SET
                    `Nickname`='{$info["Nickname"]}',
                    `Email`='{$info["Email"]}',
                    `ApplicationDate`='{$info["ApplicationDate"]}',
                    `ClearanceDate`='{$info["ClearanceDate"]}',
                    `VisaType`='".array_search($info["VisaType"], $enum_visatype)."',
                    `VisaEntry`='".array_search($info["VisaEntry"], $enum_visaentry)."',
                    `Consulate`='".array_search(trim($info["Consulate"]), $enum_consulate)."',
                    `Major_old`='{$info["Major"]}',
                    `ApplicationStatus`='".array_search($info["ApplicationStatus"], $enum_status)."',
                    `Note`='{$info["Note"]}',
                    `LastName`='{$info["LastName"]}',
                    `FirstName`='{$info["FirstName"]}',
                    `University`='{$info["University"]}',
                    `Degree`='".array_search($info["Degree"], $enum_degree)."',
                    `Employer`='{$info["Employer"]}',
                    `JobTitle`='{$info["JobTitle"]}',
                    `YearsInUSA`='{$info["YearsInUSA"]}',
                    `Citizenship`='{$info["Citizenship"]}'
                WHERE
                    `Checkee_CaseId`='{$info["Checkee_CaseId"]}'";
        }else{
            $sql = "INSERT INTO `nocheck_cases`
                (`id`, `Checkee_CaseId`, `Nickname`, `DOS_CaseId`,
                 `Email`, `ApplicationDate`, `ClearanceDate`,
                 `VisaType`, `VisaEntry`, `Consulate`, `Major_old`,
                 `ApplicationStatus`, `Note`, `LastName`,
                 `FirstName`, `University`, `Degree`, `Employer`,
                 `JobTitle`, `YearsInUSA`, `Citizenship`)
                VALUES
                (
                    NULL, '{$info["Checkee_CaseId"]}','{$info["Nickname"]}',
                    NULL, '{$info["Email"]}', '{$info["ApplicationDate"]}',
                    '{$info["ClearanceDate"]}','"
                    .array_search($info["VisaType"], $enum_visatype)."','"
                    .array_search($info["VisaEntry"], $enum_visaentry)."','"
                    .array_search($info["Consulate"], $enum_consulate)."','{$info["Major"]}','"
                    .array_search($info["ApplicationStatus"], $enum_status)."',
                    '{$info["Note"]}','{$info["LastName"]}','{$info["FirstName"]}',
                    '{$info["University"]}','".array_search($info["Degree"], $enum_degree)."',
                    '{$info["Employer"]}','{$info["JobTitle"]}',
                    '{$info["YearsInUSA"]}','{$info["Citizenship"]}'
                )";
        }
        $udb->query($sql);
        if ( $udb->get_error_no() )
        {
            die("Error:".$sql);
        }
    }
    
    
    // decrease one month
    $end_m --;
    if ($end_m < 1)
    {
        $end_y --;
        $end_m = 12;
    }
    if ($end_y == $start_y && $end_m < $start_m)
    {
        break;
    }
}

}// disable this block for now

/* then find the cases that are still open
 * 
 * 
 */
if ($ac == "ceac"){


include_once __DIR__.DIRECTORY_SEPARATOR.'./classes/CaseOperation.class.php';

$sql = "SELECT * FROM `nocheck_cases` WHERE `ApplicationStatus`=2 AND (`DOS_CaseId` IS NOT NULL)";
$query_handle = $udb->query($sql);

$co = new CaseOperation($udb);

// init the email backend
$mail = new PHPMailer();

// ---------- adjust these lines ---------------------------------------
$mail->Username = $config_email_username; // your GMail user name
$mail->Password = $config_email_password;
$mail->FromName = $config_email_fromname; // readable name
//-----------------------------------------------------------------------

$mail->Host = "ssl://smtp.gmail.com"; // GMail
$mail->Port = 465;
$mail->IsSMTP(); // use SMTP
$mail->SMTPAuth = true; // turn on SMTP authentication
$mail->From = $mail->Username;
// finished email config

while ($open_case = $udb -> fetch_assoc($query_handle))
{
    // get the status string
    $check_result = $co->getCaseDOSStatus($open_case["DOS_CaseId"], $config_checker_path, $config_checker_options);
    
    $result_parts = explode(",", $check_result);
    
    echo "On Case ".$open_case["DOS_CaseId"].": ".$check_result.": ";
    
    if ($result_parts[0] == "failed")
    {
        // check the reason
        if ($result_parts[1] == "Invalid caseid!") {
            // tell the user if we haven't
            if ($open_case["InfoStatus"] == 0) {
                echo "Email to {$open_case["Email"]}, case ID {$open_case["DOS_CaseId"]} is incorrect!\n";
                
                $mail->Subject = "ALERT: Invalid caseid!";
                $mail->ClearAddresses();
                $mail->AddAddress($open_case["Email"]); // recipients email
                $mail->Body    = sprintf($config_email_error_body, $open_case["id"]);

                $send_result = $config_email_send ? $mail->Send() : FALSE;
                
                if($send_result)
                    echo "Message has been sent!\n";
                else
                    echo "Mailer Error: " . $mail->ErrorInfo."\n";
                
                // record in the database that this record is not correct
                $sql = "UPDATE `nocheck_cases` SET
                            `InfoStatus`='1'
                        WHERE
                            `id`={$open_case["id"]}";
                    $udb->query($sql);
            }
        }elseif ($result_parts[1] == "Time limit exceeded!") {
            // tell the admin
            $mail->Subject = "ALERT: visa checking error!";
            $mail->ClearAddresses();
            $mail->AddAddress($config_email_username); // recipients email
            $mail->Body    = "ALERT:visa checking took too much time:".$open_case["DOS_CaseId"].
                    ", ".$check_result;

            $send_result = $config_email_send ? $mail->Send() : FALSE;
                
            if($send_result)
                echo "Message has been sent!\n";
            else
                echo "Mailer Error: " . $mail->ErrorInfo."\n";
        }elseif ($result_parts[1] == "No data!") {
            // don't need to do anything
            echo "Case has no data now!\n";
        }
    }elseif($result_parts[0] == "success"){
        // only email the user when status changes
        $new_status = $co->convertStatusNameToCode($result_parts[1]);
        // if the new status is an unknown status, record this exception in the database
        if ($new_status == 0) {
            log_unexpected_event($udb, "unknown visa status", $open_case["DOS_CaseId"].":".$check_result);
            // notify the admin
            $mail->Subject = "ALERT: visa checking error!";
            $mail->ClearAddresses();
            $mail->AddAddress($config_email_username); // recipients email
            $mail->Body    = "ALERT:unknown visa status:".$open_case["DOS_CaseId"].", ".$check_result;

            $send_result = $config_email_send ? $mail->Send() : FALSE;
                
            if($send_result)
                echo "Message has been sent!\n";
            else
                echo "Mailer Error: " . $mail->ErrorInfo."\n";
            
        }else{
            // get the last update time
            $query_handle_last_update = $udb->query("SELECT DATE(`update_time`) FROM `nocheck_case_update`
                WHERE `status_code`<100 and `case_id`={$open_case["id"]} ORDER BY `update_time` DESC;");
                
            $last_update_date = $udb->fetch_assoc($query_handle_last_update);
            $udb->free_result($query_handle_last_update);
            if ($last_update_date) {
                $last_update_date = $last_update_date["DATE(`update_time`)"];
            }
            
            $case_start_date = date_format(date_create_from_format("d-M-Y", $result_parts[2]), "Y-m-d");
            $case_update_date = date_format(date_create_from_format("d-M-Y", $result_parts[3]), "Y-m-d");
            // if the update date changeg or the status changed
            
            if ( ($last_update_date && $case_update_date != $last_update_date) ||
                    ($new_status != intval($open_case["ApplicationStatus"])) )
            {
                echo "Email to: {$open_case["Email"]}, {$open_case["DOS_CaseId"]}"
                    . " from ".Enums::$enum_status_name[intval($open_case["ApplicationStatus"])]." to "
                    .Enums::$enum_status_name[$new_status].".\n";
                // need to update the database first
                // if the visa has been issued/rejected, also update the summary table
                if ($new_status == 1 || $new_status == 3) {
                    $sql = "UPDATE `nocheck_cases` SET
                            `ApplicationStatus`='{$new_status}',
                            `ApplicationDate`='{$case_start_date}',
                            `ClearanceDate`='{$case_update_date}'
                        WHERE
                            `id`={$open_case["id"]}";
                    $udb->query($sql);
                }
                // the suspected reject case send email to admin
                /*if ( $new_status == 2 && $new_status == intval($open_case["ApplicationStatus"]) ) {
                    echo "Suspected rejected case!\n";
                    log_unexpected_event($udb, "rejected", "dosid:".$open_case["DOS_CaseId"].",case_id:".$open_case["id"]);
                    $mail->ClearAddresses();
                    $mail->AddAddress($config_email_username); // recipients email
                    $mail->Body    = sprintf("ALERT:Suspected rejected case!"."dosid:".$open_case["DOS_CaseId"].
                            ",case_id:".$open_case["id"]);

                    $send_result = $config_email_send ? $mail->Send() : FALSE;
                
                    if($send_result)
                        echo "Message has been sent!\n";
                    else
                        echo "Mailer Error: " . $mail->ErrorInfo."\n";
                }*/
                
                // all the update events need to be recorded to case_update table
                $sql = "INSERT INTO `nocheck_case_update`
                        ( `id`, `case_id`, `dos_id`, `status_code`, `update_time`)
                        VALUES (NULL, '{$open_case["id"]}', '{$open_case["DOS_CaseId"]}', '{$new_status}',
                            '{$case_update_date}');";
                $udb->query($sql);

                // and then notify the user using email
                $mail->Subject = $config_email_subject;
                $mail->ClearAddresses();
                $mail->AddAddress($open_case["Email"]); // recipients email
                $mail->Body    = sprintf($config_email_body, $open_case["DOS_CaseId"],
                        Enums::$enum_status_name[intval($open_case["ApplicationStatus"])],
                        Enums::$enum_status_name[$new_status], $case_update_date );

                $send_result = $config_email_send ? $mail->Send() : FALSE;
                
                if($send_result)
                    echo "Message has been sent!\n";
                else
                    echo "Mailer Error: " . $mail->ErrorInfo."\n";
            }else{
                echo "Case ".$open_case["DOS_CaseId"].": No change.\n";
            }
        }
    }else{
        // TODO: deal with unknown result
    }
}

}







