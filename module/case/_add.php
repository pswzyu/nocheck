<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

protect();

include_once(FROOT."classes/CaseOperation.class.php");

// get the case information by caseid and then display it
//$udb->query("SELECT * FROM `nocheck_cases` WHERE id=".$caseid);

//$table = $udb->fetch_assoc();

/*
$VisaType = Enums::$enum_visatype[$table["VisaType"]];
$VisaEntry = Enums::$enum_visaentry[$table["VisaEntry"]];
$Consulate = Enums::$enum_consulate[$table["Consulate"]];
$Degree = Enums::$enum_degree[$table["Degree"]];
$ApplicationStatus = Enums::$enum_status[$table["ApplicationStatus"]];
*/

$dos_id = "";
$email = "";
$firstname = "";
$lastname = "";
$visatype = "";
$visaentry = "";
$consulate = "";
$yearsinusa = "";
$citizenship = "";
$university = "";
$degree = "";
$major = "";
$employer = "";
$jobtitle = "";
$applydate = "";
$note = "";

$notify = "";

# check if the user has entered the information
if (@$_POST["submit"])
{
    $dos_id = $_POST["dos_id"];
    $email = $_POST["email"];
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $visatype = $_POST["visatype"];
    $visaentry = $_POST["visaentry"];
    $consulate = $_POST["consulate"];
    $yearsinusa = $_POST["yearsinusa"];
    $citizenship = $_POST["citizenship"];
    $university = $_POST["university"];
    $degree = $_POST["degree"];
    $major = $_POST["major"];
    $employer = $_POST["employer"];
    $jobtitle = $_POST["jobtitle"];
    $applydate = $_POST["applydate"];
    $note = $_POST["note"];
    
    $info = $_POST;
    
    $case_operator = new CaseOperation($udb);
    $new_case_dbid = $case_operator->addCase($info);
    
    if ($new_case_dbid == -1)
    {
        $error_msg = $case_operator->getErrorMessage();
        foreach( $error_msg as $fieldname => $content )
        {
            $notify .= "<div class='error_msg'>{$content}</div>";
        }
    }else{
        $notify = "<div class='info_msg'>Added Successfully!</div>";
    }
}

?>

<div id="main_table" >
    <div id="notify_back" class=""><?php echo $notify; ?></div>
    <form action="index.php?do=case&ac=add" method="post">
        <table border="1">
            <tr><td>DS-160 Case ID</td><td><input type="text" name="dos_id"
                                               value="<?php echo $dos_id; ?>"/></td></tr>
            <tr><td>Email</td><td><input type="text" name="email"
                                         value="<?php echo $email; ?>"/></td></tr>
            <tr><td>First Name</td><td><input type="text" name="firstname"
                                              value="<?php echo $firstname; ?>"/></td></tr>
            <tr><td>Last Name</td><td><input type="text" name="lastname"
                                             value="<?php echo $lastname; ?>"/></td></tr>
            
            <tr><td>Visa Type</td><td><select id="sel_visatype" name="visatype">
                        <option value="1">F1</option><option value="2">F2</option>
                        <option value="3">H1</option><option value="4">H4</option>
                        <option value="5">J1</option><option value="6">J2</option>
                        <option value="7">B1</option><option value="8">B2</option>
                        <option value="9">L1</option><option value="10">L2</option>
                    </select></td></tr>
            <tr><td>Visa Entry</td><td><select id="sel_visaentry" type="text" name="visaentry">
                        <option value="1">New</option><option value="2">Renewal</option>
                    </select></td></tr>
            <tr><td>US Consulate</td><td><select id="sel_consulate" name="consulate">
                        <option value="1">BeiJing</option><option value="2">ChengDu</option>
                        <option value="3">Chennai</option><option value="4">Europe</option>
                        <option value="5">GuangZhou</option><option value="6">HongKong</option>
                        <option value="7">Kolkata</option><option value="8">MexicoCity</option>
                        <option value="9">Montreal</option><option value="10">Mumbai</option>
                        <option value="11">NewDelhi</option><option value="12">Ottawa</option>
                        <option value="13">Quebec</option><option value="14">ShangHai</option>
                        <option value="15">ShenYang</option><option value="16">Tijuana</option>
                        <option value="17">Toronto</option><option value="18">Vancouver</option>
                        <option value="19">Others</option>
                    </select></td></tr>
            <tr><td>Years In USA</td><td><input type="text" name="yearsinusa"
                                                value="<?php echo $yearsinusa; ?>"></td></tr>
            <tr><td>Citizenship</td><td><input type="text" name="citizenship"
                                               value="<?php echo $citizenship; ?>"></td></tr>
            
            <tr><td>University(College)</td><td><input type="text" name="university"
                                                       value="<?php echo $university; ?>"></td></tr>
            <tr><td>Degree</td><td><select id="sel_degree" name="degree">
                        <option value="1">N/A</option><option value="2">BS</option>
                        <option value="3">MS</option><option value="4">Ph.D</option>
                        <option value="5">Others</option>
                    </select></td></tr>
            <tr><td>Major</td><td><input type="text" name="major"
                                         value="<?php echo $major; ?>"></td></tr>
            <tr><td>Employer</td><td><input type="text" name="employer"
                                            value="<?php echo $employer; ?>"></td></tr>
            <tr><td>Job Title</td><td><input type="text" name="jobtitle"
                                             value="<?php echo $jobtitle; ?>"></td></tr>
            
            <tr><td>Apply Date</td><td><input id="dp_applydate" type="text" name="applydate"
                                              value="<?php echo $applydate; ?>"></td></tr>
            
            <tr><td>Note</td><td>
                <textarea cols="50" rows="10" name="note"><?php echo $note; ?></textarea>
                </td></tr>
            
        </table>
        <input type="hidden" id="last_visatype" value="<?php echo $visatype; ?>"/>
        <input type="hidden" id="last_visaentry" value="<?php echo $visaentry; ?>"/>
        <input type="hidden" id="last_consulate" value="<?php echo $consulate; ?>"/>
        <input type="hidden" id="last_degree" value="<?php echo $degree; ?>"/>
        <input type="submit" name="submit" value="Submit" />
    </form>
    
</div>

