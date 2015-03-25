<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require(__DIR__.DIRECTORY_SEPARATOR."../../common/protect.php");

//include_once(FROOT."classes/CaseOperation.class.php");
include_once(FROOT."classes/UserInfo.class.php");

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
$info["captcha"] = _SAFEPOST("captcha");

$notify = "";
$user_class = new UserInfo($udb);

# check if the user has entered the information
if (isset($_POST["submit"]))
{
    // first chech the captcha
    if (strtolower($info["captcha"]) != strtolower($_SESSION['captcha']['code']) ) {
        $notify = "<div class='error_msg'>Captcha check failed!</div>";
    } else {
        // then get the user password
        $user_pwd = $user_class->getUserPassword($info["email"], $info["dos_id"]);

        if ($user_pwd == NULL)
        {
            $notify = "<div class='error_msg'>No such Email and DS160 combination!</div>";
        }else{

            // init the email backend
            $mail = new PHPMailer();

            // ---------- adjust these lines ---------------------------------------
            $mail->Username = $config_email_username; // your GMail user name
            $mail->Password = $config_email_password;
            $mail->FromName = $config_email_fromname; // readable name

            $mail->Subject = $config_email_subject;
            //-----------------------------------------------------------------------

            $mail->Host = "ssl://smtp.gmail.com"; // GMail
            $mail->Port = 465;
            $mail->IsSMTP(); // use SMTP
            $mail->SMTPAuth = true; // turn on SMTP authentication
            $mail->From = $mail->Username;
            // finished email config

            // tell the user
            $mail->ClearAddresses();
            $mail->AddAddress($info["email"]); // recipients email
            $mail->Body    = sprintf("Hi,\n The password for case %s is %s", $info["dos_id"], $user_pwd);

            if(!$mail->Send())
                $notify = "<div class='error_msg'>Mailer Error: " . $mail->ErrorInfo."</div>";
            else
                $notify = "<div class='info_msg'>Password has been sent to your email.</div>";
        }
    }
    
    // since the data comes from html directly, we need to strip the slashes added in
    // order to display normally to the user
    $info = sstripslashes($info);
    
    //include(FROOT."module/user/_ret_pwd_2.php");
    
} else {
    
    //include(FROOT."module/user/_ret_pwd_1.php");
}

$_SESSION['captcha'] = simple_php_captcha();


?>

<div id="main_table" >
    <div id="notify_back" class=""><?php echo $notify; ?></div>
    <form action="index.php?do=user&ac=ret_pwd" method="post">
        <table border="1">
            <tr><td>DS-160 Case ID</td><td><input type="text" name="dos_id"
                    value="<?php echo $info["dos_id"]; ?>"/><span class="form_req">*</span></td></tr>
            <tr><td>Email</td><td><input type="text" name="email"
                    value="<?php echo $info["email"]; ?>"/><span class="form_req">*</span></td></tr>
            <tr><td>CAPTCHA</td><td><img src="<?php echo $_SESSION['captcha']['image_src']; ?>" /><br/>
                <input name="captcha" /><span class="form_req">*</span></td></tr>

        </table>

        <input type="submit" name="submit" value="Submit" />
    </form>

</div>

