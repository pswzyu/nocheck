<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require(__DIR__.DIRECTORY_SEPARATOR."../../common/protect.php");

include_once(FROOT."classes/CaseOperation.class.php");

$notify = "";
$case_operator = new CaseOperation($udb);

# check if the user has entered the information
if (isset($_POST["submit"]))
{
    $info["id"] = $caseid;
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
    $info["degree"] = isset($_POST["degree"])?$_POST["degree"]:"";
    $info["major"] = isset($_POST["major"])?$_POST["major"]:"";
    $info["employer"] = isset($_POST["employer"])?$_POST["employer"]:"";
    $info["jobtitle"] = isset($_POST["jobtitle"])?$_POST["jobtitle"]:"";
    $info["applydate"] = isset($_POST["applydate"])?$_POST["applydate"]:"";
    $info["cleardate"] = isset($_POST["cleardate"])?$_POST["cleardate"]:"";
    $info["note"] = isset($_POST["note"])?$_POST["note"]:"";
    
    $ret = $case_operator->updateCase($info);
    
    if ($ret == -1)
    {
        $error_msg = $case_operator->getErrorMessage();
        foreach( $error_msg as $fieldname => $content )
        {
            $notify .= "<div class='error_msg'>{$content}</div>";
        }
    }else{
        $notify = "<div class='info_msg'>Updated Successfully!</div>";
    }
    // since the data comes from html directly, we need to strip the slashes added in
    // order to display normally to the user
    $info = sstripslashes($info);
    
}else{ // otherwise, the user just arrived this page, get the detail of the case and list them
    // inside each input box
    $info = $case_operator->getCase($caseid);
    // if this case is from checkee, then provide the notice and redirect link
    if ($info["checkee_id"]){
        $notify = "This case is from checkee.info, please update the case at "
                . "<a href='http://www.checkee.info/update.php?casenum={$info["checkee_id"]}'>here</a>";
    }
    // also disable the submit button
    
    
}

?>

<div id="main_table" >
    <div id="notify_back" class=""><?php echo $notify; ?></div>
    <form action="index.php?do=case&ac=update&id=<?php echo $caseid; ?>" method="post">
        <table border="1">
            <tr><td>DS-160 Case ID</td><td><input type="text" name="dos_id"
                    value="NoChangeHere" title="<?php echo CaseOperation::getMaskedDS160ID($info["dos_id"]); ?>"/><span class="form_req">*</span></td></tr>
            
            <tr><td>Email</td><td><input type="text" name="email" value="NoChange@Here.com"
                    title="<?php echo CaseOperation::getMaskedEmailAddress($info["email"]); ?>"/><span class="form_req">*</span></td></tr>
            
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
            
            <tr><td>Application Type</td><td><select id="sel_visaentry" type="text" name="visaentry">
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
            
            <tr id="field_university" class="conditional_field"><td>University(College)</td><td><input type="text" name="university"
                    value="<?php echo $info["university"]; ?>"><span class="form_req">*</span></td></tr>
            
            <tr id="field_degree" class="conditional_field"><td>Degree</td><td><select id="sel_degree" name="degree">
                        <option value="0" disabled selected >-- Please Select --</option>
                        <option value="1">N/A</option><option value="2">BS</option>
                        <option value="3">MS</option><option value="4">Ph.D</option>
                        <option value="5">Others</option>
                    </select><span class="form_req">*</span></td></tr>
            
            <tr id="field_major" class="conditional_field"><td>Major</td><td><input id="ac_major" type="text" name="major"
                    title="Enter &quot;Other&quot; if you can't find your major"
                    value="<?php echo $info["major"]; ?>"><span class="form_req">*</span></td></tr>
            
            <tr id="field_employer" class="conditional_field"><td>Employer/Company</td><td><input type="text" name="employer"
                    value="<?php echo $info["employer"]; ?>"><span class="form_req">*</span></td></tr>
            
            <tr id="field_jobtitle" class="conditional_field"><td>Job Title</td><td><input id="ac_jobtitle" type="text" name="jobtitle"
                    title="Enter &quot;Other&quot; if you can't find your job title"
                    value="<?php echo $info["jobtitle"]; ?>"><span class="form_req">*</span></td></tr>
            
            <tr><td>Apply Date</td><td><input id="dp_applydate" type="text" name="applydate"
                                              value="<?php echo $info["applydate"]; ?>"></td></tr>
            <tr><td>Clear Date</td><td><input id="dp_cleardate" type="text" name="cleardate"
                                              value="<?php echo $info["cleardate"]; ?>"></td></tr>
            
            <tr><td>Note</td><td>
                <textarea cols="50" rows="10" name="note"><?php echo $info["note"]; ?></textarea>
                </td></tr>
            
        </table>
        <input type="hidden" id="last_visatype" value="<?php echo $info["visatype"]; ?>"/>
        <input type="hidden" id="last_visaentry" value="<?php echo $info["visaentry"]; ?>"/>
        <input type="hidden" id="last_consulate" value="<?php echo $info["consulate"]; ?>"/>
        <input type="hidden" id="last_degree" value="<?php echo $info["degree"]; ?>"/>
        <label>Password</label><input type="password" name="password"/>
        <input type="submit" name="submit"<?php if(empty($info["dos_id"]))
            echo "disabled='disabled'"; ?> value="Update" />
    </form>
    
</div>

