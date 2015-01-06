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
 */

include_once("./common/types.php");
include_once("./common/functions.php");
include_once("./lib/db/mysql.inc.php");
include_once("./config/config.php");
include_once("./common.php");

$start_y = intval("2012");
$start_m = intval("08");

$end_y = intval(date("Y"));
$end_m = intval(date("m"));

if (0)
{

/*
 * first get the info from check.info
 */
while (true)
{
    echo "".$end_y."-".$end_m."<br/>";
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
        echo "casenum={$casenum}\n";
        $test = file_get_contents("http://www.checkee.info/update.php?casenum={$casenum}");
        
        $info = array();
        preg_match('/name="casenum" value="(\d+)"\//', $test, $matches);
        $info["Checkee_CaseId"] = $matches[1];
        preg_match('/name="id" value="(\X+?)"\//', $test, $matches);
        $info["Nickname"] = $matches[1];
        preg_match('/name="email" value="([\w.\@ -]*?)"\//', $test, $matches);
        $info["Email"] = $matches[1];
        preg_match('/NAME="check_date" VALUE="([\w-]+)"/', $test, $matches);
        $info["ApplicationDate"] = $matches[1];
        // type entry consulate status
        preg_match_all('/option value="([\w.\\/]*?)" SELECTED/', $test, $matches);
        $info["VisaType"] = $matches[1][0];
        $info["VisaEntry"] = $matches[1][1];
        $info["Consulate"] = $matches[1][2];
        $info["ApplicationStatus"] = $matches[1][3];
        $info["Degree"] = $matches[1][4];
        preg_match('/name="major" value="(\X*?)"\//', $test, $matches);
        $info["Major"] = $matches[1];
        preg_match('/NAME="clear_date" VALUE="([\w-]+)"/', $test, $matches);
        $info["ClearanceDate"] = $matches[1];
        preg_match('/name="note">(\X*?)<\/textarea>/', $test, $matches);
        $info["Note"] = $matches[1];
        preg_match('/name="lastname" value="(\X+?)"\//', $test, $matches);
        $info["LastName"] = $matches[1];
        preg_match('/name="firstname" value="(\X+?)"\//', $test, $matches);
        $info["FirstName"] = $matches[1];
        preg_match('/name="univ_college" value="(\X+?)"\//', $test, $matches);
        $info["University"] = $matches[1];
        preg_match('/name="employer" value="(\X+?)"\//', $test, $matches);
        $info["Employer"] = $matches[1];
        preg_match('/name="job_title" value="(\X+?)"\//', $test, $matches);
        $info["JobTitle"] = $matches[1];
        preg_match('/name="years_in_usa" value="(\X+?)"\//', $test, $matches);
        $info["YearsInUSA"] = $matches[1];
        preg_match('/name="country" value="(\X+?)"\//', $test, $matches);
        $info["Citizenship"] = $matches[1];
        
        $info = saddslashes($info);
        
        // first check if this case is already in the databse
        $udb->query("SELECT * FROM `nocheck_cases` WHERE `Checkee_CaseId`={$info["Checkee_CaseId"]}");
        if ($udb->get_error_no()){
            die("Error:{$info["Checkee_CaseId"]}");
        }
        // if this record is already in the database
        if ($udb->fetch_assoc()){
            // update the record in the database
            $sql = "UPDATE `nocheck_cases` SET
                    `Nickname`='{$info["Nickname"]}',
                    `Email`='{$info["Email"]}',
                    `ApplicationDate`='{$info["ApplicationDate"]}',
                    `ClearanceDate`='{$info["ClearanceDate"]}',
                    `VisaType`='".array_search($info["VisaType"], $enum_visatype)."',
                    `VisaEntry`='".array_search($info["VisaEntry"], $enum_visaentry)."',
                    `Consulate`='".array_search($info["Consulate"], $enum_consulate)."',
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
    
    
    // decrement one month
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
if (1){


include_once __DIR__.'./classes/CaseOperation.class.php';
include __DIR__."./lib/php/PHPMailer-master/PHPMailerAutoload.php";

$sql = "SELECT * FROM `nocheck_cases` WHERE `ApplicationStatus`=2 AND (`DOS_CaseId` IS NOT NULL)";
$query_handle = $udb->query($sql);

$co = new CaseOperation($udb);

// init the email backend
$mail = new PHPMailer();

// ---------- adjust these lines ---------------------------------------
$mail->Username = $config_email_username; // your GMail user name
$mail->Password = $config_email_password;
$mail->FromName = $config_email_fromname; // readable name

$mail->Subject = $config_email_subject;
$mail->Body    = $config_email_body;
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
    $check_result = $co->getCaseDOSStatus($open_case["DOS_CaseId"], $config_checker_path);
    
    $result_parts = explode(",", $check_result);
    
    if ($result_parts[0] == "failed")
    {
        // TODO: deal with failed check
    }elseif($result_parts[0] == "success"){
        // only email the user when status changes
        $new_status = $co->convertStatusNameToCode($result_parts[1]);
        $case_start_date = $udb->timestamp_php_to_db($result_parts[2], "d-M-Y");
        $case_end_date = $udb->timestamp_php_to_db($result_parts[3], "d-M-Y");
        if ($new_status != intval($open_case["ApplicationStatus"]))
        {
            // need to update the database first
            $sql = "UPDATE `nocheck_cases` SET
                    `ApplicationStatus`='{$new_status}',
                    `ApplicationDate`='{$case_start_date}',
                    `ClearanceDate`='{$case_end_date}'
                WHERE
                    `id`={$open_case["id"]}";
            $udb->query($sql);
            
            // and then notify the user using email
            $mail->ClearAddresses();
            $mail->AddAddress($open_case["Email"]); // recipients email

            if(!$mail->Send())
                echo "Mailer Error: " . $mail->ErrorInfo;
            else
                echo "Message has been sent";
        }
    }else{
        // TODO: deal with unknown result
    }
}

}







