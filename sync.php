<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
        preg_match('/name="id" value="(\w+)"\//', $test, $matches);
        $info["Nickname"] = $matches[1];
        preg_match('/name="email" value="([\w.\@]+)"\//', $test, $matches);
        $info["Email"] = $matches[1];
        preg_match('/NAME="check_date" VALUE="([\w-]+)"/', $test, $matches);
        $info["ApplicationDate"] = $matches[1];
        // type entry consulate status
        preg_match_all('/option value="([\w.\/]+)" SELECTED/', $test, $matches);
        $info["VisaType"] = $matches[1][0];
        $info["VisaEntry"] = $matches[1][1];
        $info["Consulate"] = $matches[1][2];
        $info["ApplicationStatus"] = $matches[1][3];
        $info["Degree"] = $matches[1][4];
        preg_match('/name="major" value="(\X+?)"\//u', $test, $matches);
        $info["Major"] = $matches[1];
        preg_match('/NAME="clear_date" VALUE="([\w-]+)"/', $test, $matches);
        $info["ClearanceDate"] = $matches[1];
        preg_match('/name="note">(\X+?)<\/textarea>/u', $test, $matches);
        $info["Note"] = $matches[1];
        preg_match('/name="lastname" value="(\X+?)"\//u', $test, $matches);
        $info["LastName"] = $matches[1];
        preg_match('/name="firstname" value="(\X+?)"\//u', $test, $matches);
        $info["FirstName"] = $matches[1];
        preg_match('/name="univ_college" value="(\X+?)"\//u', $test, $matches);
        $info["University"] = $matches[1];
        preg_match('/name="employer" value="(\X+?)"\//u', $test, $matches);
        $info["Employer"] = $matches[1];
        preg_match('/name="job_title" value="(\X+?)"\//u', $test, $matches);
        $info["JobTitle"] = $matches[1];
        preg_match('/name="years_in_usa" value="(\X+?)"\//u', $test, $matches);
        $info["YearsInUSA"] = $matches[1];
        preg_match('/name="country" value="(\X+?)"\//u', $test, $matches);
        $info["Citizenship"] = $matches[1];
        
        $info = saddslashes($info);
        
        $sql = "
            INSERT INTO `nocheck_cases`
            (`id`, `Checkee_CaseId`, `Nickname`, `CaseNumber`,
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
            )
            ON DUPLICATE KEY UPDATE
                `Checkee_CaseId`='{$info["Checkee_CaseId"]}',
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
        ";
        $udb->query($sql);
        if ( $udb->get_error_no() )
        {
            die($sql);
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