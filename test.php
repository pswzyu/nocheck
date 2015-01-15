<?php

include("common.php");
//$query_result = $udb->query("SELECT * FROM `nocheck_cases` WHERE id=-1");
//$error_result = $udb->get_error_no();
//$data_result = $udb->fetch_assoc($query_result);
//
//print_r($query_result); // the return value of the query is a mysqli_result object or true
//// return false on failure
//echo "<br/>";
//print_r($error_result);
//echo "<br/>";
//print_r($data_result);
//echo "<br/>";

//437788 439986 453319 449917 404103

$casenum = "496536";

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
            $no_error = FALSE;
            $error_msg .= "Email,";
        }
        ///////////////////////
        preg_match('/NAME="check_date" VALUE="([\w-]+)"/', $test, $matches);
        if(isset($matches[1])){
            $info["ApplicationDate"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "ApplicationDate,";
        }
        //////////////////////
        // type entry consulate status
        preg_match_all('/option value="([\w.\\/]*?)" SELECTED/X', $test, $matches);
        print_r($matches);
        die();
        if(isset($matches[1][1])){
            $info["VisaType"] = $matches[1][1];
        }else{
            $no_error = FALSE;
            $error_msg .= "VisaType,";
        }
        if(isset($matches[1][2])){
            $info["VisaEntry"] = $matches[1][2];
        }else{
            $no_error = FALSE;
            $error_msg .= "VisaEntry,";
        }
        if(isset($matches[1][3])){
            $info["Consulate"] = $matches[1][3];
        }else{
            $no_error = FALSE;
            $error_msg .= "Consulate,";
        }
        if(isset($matches[1][4])){
            $info["ApplicationStatus"] = $matches[1][4];
        }else{
            $no_error = FALSE;
            $error_msg .= "ApplicationStatus,";
        }
        if(isset($matches[1][5])){
            $info["Degree"] = $matches[1][5];
        }else{
            $no_error = FALSE;
            $error_msg .= "Degree,";
        }
        //////////////////////
        preg_match('/name="major" value="(\X*?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["Major"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "Major,";
        }
        //////////////////////
        preg_match('/NAME="clear_date" VALUE="([\w-]+)"/', $test, $matches);
        if(isset($matches[1])){
            $info["ClearanceDate"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "ClearanceDate,";
        }
        ///////////////////////
        preg_match('/name="note">(\X*?)<\/textarea>/', $test, $matches);
        if(isset($matches[1])){
            $info["Note"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "Note,";
        }
        ///////////////////////
        preg_match('/name="lastname" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["LastName"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "LastName,";
        }
        ///////////////////////
        preg_match('/name="firstname" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["FirstName"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "FirstName,";
        }
        /////////////////////////
        preg_match('/name="univ_college" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["University"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "University,";
        }
        //////////////////////////
        preg_match('/name="employer" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["Employer"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "Employer,";
        }
        //////////////////////////
        preg_match('/name="job_title" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["JobTitle"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "JobTitle,";
        }
        //////////////////////////
        preg_match('/name="years_in_usa" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["YearsInUSA"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "YearsInUSA,";
        }
        //////////////////////////
        preg_match('/name="country" value="(\X+?)"\//', $test, $matches);
        if(isset($matches[1])){
            $info["Citizenship"] = $matches[1];
        }else{
            $no_error = FALSE;
            $error_msg .= "Citizenship,";
        }
        ///////////////////////
        
        $info = saddslashes($info);
        
        if (!$no_error)
        {
            echo $error_msg."\n";
        }
        
        print_r($info);

?>