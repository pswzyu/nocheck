<?php

include("common.php");
$query_result = $udb->query("SELECT * FROM `nocheck_cases` WHERE id=-1");
$error_result = $udb->get_error_no();
$data_result = $udb->fetch_assoc();

print_r($query_result); // the return value of the query is a mysqli_result object or true
// return false on failure
echo "<br/>";
print_r($error_result);
echo "<br/>";
print_r($data_result);
echo "<br/>";

$casenum = "497187";

$test = file_get_contents("http://www.checkee.info/update.php?casenum={$casenum}");
        
        $info = array();
        preg_match('/name="casenum" value="(\d+)"\//', $test, $matches);
        $info["Checkee_CaseId"] = $matches[1];
        preg_match('/name="id" value="(\X+?)"\//', $test, $matches);
        $info["Nickname"] = $matches[1];
        preg_match('/name="email" value="([\w.\@]*?)"\//', $test, $matches);
        $info["Email"] = $matches[1];
        preg_match('/NAME="check_date" VALUE="([\w-]+)"/', $test, $matches);
        $info["ApplicationDate"] = $matches[1];
        // type entry consulate status
        preg_match_all('/option value="([.\/\w]*?)" SELECTED/', $test, $matches);
        $info["VisaType"] = $matches[1][0];
        $info["VisaEntry"] = $matches[1][1];
        $info["Consulate"] = $matches[1][2];
        $info["ApplicationStatus"] = $matches[1][3];
        $info["Degree"] = $matches[1][4];
        preg_match('/name="major" value="(\X*?)"\//u', $test, $matches);
        $info["Major"] = $matches[1];
        preg_match('/NAME="clear_date" VALUE="([\w-]+)"/', $test, $matches);
        $info["ClearanceDate"] = $matches[1];
        preg_match('/name="note">(\X*?)<\/textarea>/u', $test, $matches);
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
        
        print_r($info);

?>