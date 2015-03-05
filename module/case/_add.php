<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require(__DIR__.DIRECTORY_SEPARATOR."../../common/protect.php");

include_once(FROOT."classes/CaseOperation.class.php");

include(FROOT."lib/php/simple-php-captcha/simple-php-captcha.php");

session_start();

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

$info["dos_id"] = isset($_POST["dos_id"])?$_POST["dos_id"]:"";
$info["email"] = isset($_POST["email"])?$_POST["email"]:"";
$info["password"] = isset($_POST["password"])?$_POST["password"]:"";
$info["firstname"] = isset($_POST["firstname"])?$_POST["firstname"]:"";
$info["lastname"] = isset($_POST["lastname"])?$_POST["lastname"]:"";
$info["visatype"] = isset($_POST["visatype"])?$_POST["visatype"]:"";
$info["visaentry"] = isset($_POST["visaentry"])?$_POST["visaentry"]:"";
$info["consulate"] = isset($_POST["consulate"])?$_POST["consulate"]:"";
$info["yearsinusa"] = isset($_POST["yearsinusa"])?$_POST["yearsinusa"]:"";
$info["citizenship"] = isset($_POST["citizenship"])?$_POST["citizenship"]:"";
$info["university"] = isset($_POST["university"])?$_POST["university"]:"";
$info["degree"] = isset($_POST["university"])?$_POST["degree"]:"";
$info["major"] = isset($_POST["major"])?$_POST["major"]:"";
$info["employer"] = isset($_POST["employer"])?$_POST["employer"]:"";
$info["jobtitle"] = isset($_POST["jobtitle"])?$_POST["jobtitle"]:"";
$info["applydate"] = isset($_POST["applydate"])?$_POST["applydate"]:date("Y-m-d");
$info["note"] = isset($_POST["note"])?$_POST["note"]:"";

$info["captcha"] = _SAFEPOST("captcha");

$notify = "";
$case_operator = new CaseOperation($udb);

# check if the user has entered the information
if (isset($_POST["submit"]))
{    
    // first chech the captcha
    if (strtolower($info["captcha"]) != strtolower($_SESSION['captcha']['code']) ) {
        $notify = "<div class='error_msg'>Captcha check failed!</div>";
    } else {
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
    
    // since the data comes from html directly, we need to strip the slashes added in
    // order to display normally to the user
    $info = sstripslashes($info);
    
}

$_SESSION['captcha'] = simple_php_captcha();

?>

<div id="main_table" >
    <div id="notify_back" class=""><?php echo $notify; ?></div>
    <form action="index.php?do=case&ac=add" method="post">
        <table border="1">
            <tr><td>DS-160 Case ID</td><td><input type="text" name="dos_id"
                    value="<?php echo $info["dos_id"]; ?>"/><span class="form_req">*</span></td></tr>
            <tr><td>Email</td><td><input type="text" name="email"
                    value="<?php echo $info["email"]; ?>"/><span class="form_req">*</span></td></tr>
            <tr><td>Password</td><td><input type="password" name="password"
                    value="<?php echo $info["password"]; ?>"/><span class="form_req">*</span></td></tr>
            <tr><td>First Name</td><td><input type="text" name="firstname"
                                              value="<?php echo $info["firstname"]; ?>"/></td></tr>
            <tr><td>Last Name</td><td><input type="text" name="lastname"
                                             value="<?php echo $info["lastname"]; ?>"/></td></tr>
            
            <tr><td>Visa Type</td><td><select id="sel_visatype" name="visatype">
                        <option value="0" disabled selected >-- Please Select --</option>
                        <option value="1">F1</option><option value="2">F2</option>
                        <option value="3">H1</option><option value="4">H4</option>
                        <option value="5">J1</option><option value="6">J2</option>
                        <option value="7">B1</option><option value="8">B2</option>
                        <option value="9">L1</option><option value="10">L2</option>
                    </select><span class="form_req">*</span></td></tr>
            <tr><td>Visa Entry</td><td><select id="sel_visaentry" type="text" name="visaentry">
                        <option value="0" disabled selected >-- Please Select --</option>
                        <option value="1">New</option><option value="2">Renewal</option>
                    </select><span class="form_req">*</span></td></tr>
            <tr><td>US Consulate</td><td><select id="sel_consulate" name="consulate">
                        <option value="0" disabled selected >-- Please Select --</option>
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
                    </select><span class="form_req">*</span></td></tr>
            <tr><td>Years In USA</td><td><input type="text" name="yearsinusa"
                                                value="<?php echo $info["yearsinusa"]; ?>"></td></tr>
            <tr><td>Citizenship</td><td><input type="text" name="citizenship"
                                               value="<?php echo $info["citizenship"]; ?>"></td></tr>
            
            <tr><td>University(College)</td><td><input type="text" name="university"
                                                       value="<?php echo $info["university"]; ?>"></td></tr>
            <tr><td>Degree</td><td><select id="sel_degree" name="degree">
                        <option value="0" disabled selected >-- Please Select --</option>
                        <option value="1">N/A</option><option value="2">BS</option>
                        <option value="3">MS</option><option value="4">Ph.D</option>
                        <option value="5">Others</option>
                    </select><span class="form_req">*</span></td></tr>
            <tr><td>Major</td><td><input type="text" name="major"
                                         value="<?php echo $info["major"]; ?>"></td></tr>
            <tr><td>Employer</td><td><input type="text" name="employer"
                                            value="<?php echo $info["employer"]; ?>"></td></tr>
            <tr><td>Job Title</td><td><input type="text" name="jobtitle"
                                             value="<?php echo $info["jobtitle"]; ?>"></td></tr>
            
            <tr><td>Apply Date</td><td><input id="dp_applydate" type="text" name="applydate"
                                              value="<?php echo $info["applydate"]; ?>"></td></tr>
            
            <tr><td>Note</td><td>
                <textarea cols="50" rows="10" name="note"><?php echo $info["note"]; ?></textarea>
                </td></tr>
            
            <tr><td>CAPTCHA</td><td><img src="<?php echo $_SESSION['captcha']['image_src']; ?>" /><br/>
                <input name="captcha" /><span class="form_req">*</span></td></tr>
            
        </table>
        <input type="hidden" id="last_visatype" value="<?php echo $info["visatype"]; ?>"/>
        <input type="hidden" id="last_visaentry" value="<?php echo $info["visaentry"]; ?>"/>
        <input type="hidden" id="last_consulate" value="<?php echo $info["consulate"]; ?>"/>
        <input type="hidden" id="last_degree" value="<?php echo $info["degree"]; ?>"/>
        <input type="submit" name="submit" value="Submit" />
    </form>
    
</div>

