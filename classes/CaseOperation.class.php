<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CaseOperation
 *
 * @author pswzyu
 */
//require_once(FROOT.'lib/php/formvalidator.php');

use Respect\Validation\Validator as v;

class CaseOperation {
    //put your code here
    
    private $udb;
    private $error;
    
    var $majors_array;
    var $careers_array;
    
    function __construct($db_con)
    {
        $this->udb = $db_con;
        include(FROOT.'lib/php/majors_and_careers.php');
        $this->majors_array = unserialize($majors_string);
        $this->careers_array = unserialize($careers_string);
    }
    
    public function getErrorMessage()
    {
        return $this->error;
    }
    
    /*
     * this function use the Respect Validator to valid the form uploaded by the
     * user to see if the form contents are valid.
     * return the errors, each content in the errors array is an error
     */
    private function validateForm($info)
    {
        $errors = array();
        // validate dos_id
        if (! v::alnum()->notEmpty()->validate($info["dos_id"]) ){
            $errors["dos_id"] = "Please fill in DS160 ID!";
        }
        // validate email
        if (! v::Email()->notEmpty()->validate($info["email"]) ){
            $errors["email"] = "Please fill in a valid Email address!";
        }
        // validate password
        if (! v::notEmpty()->validate($info["password"]) ){
            $errors["password"] = "Please fill in a valid password!";
        }
        // visa type
        if (! v::int()->between(1,10,TRUE)->notEmpty()->validate($info["visatype"]) ){
            $errors["visatype"] = "Invalid visa type!";
        }else{
            // validate the major/career
            $sel_value = intval($info["visatype"]);
            if ($sel_value == 1 || $sel_value == 2) { // f
                $errors = array_merge($errors, $this->validateFormFAndJ($info));
            }else if ($sel_value == 3 || $sel_value == 4) { // h
                $errors = array_merge($errors, $this->validateFormHAndL($info));
            }else if ($sel_value == 5 || $sel_value == 6) { // j
                $errors = array_merge($errors, $this->validateFormFAndJ($info));
            }else if ($sel_value == 7 || $sel_value == 8) { // b

            }else if ($sel_value == 9 || $sel_value == 10) { // l
                $errors = array_merge($errors, $this->validateFormHAndL($info));
            }else{ // value is 0 and other

            }
        }
        // visa entry
        if (! v::int()->between(1,2,TRUE)->notEmpty()->validate($info["visaentry"]) ){
            $errors["visaentry"] = "Invalid visa entry!";
        }
        // consulate
        if (! v::int()->between(1,19,TRUE)->notEmpty()->validate($info["consulate"]) ){
            $errors["consulate"] = "Invalid consulate!";
        }
        // applied date
        if (! v::date()->notEmpty()->validate($info["applydate"]) ){
            $errors["applydate"] = "Invalid application filed date!";
        }
        
        return $errors;
    }
    private function validateFormFAndJ($info) {
        $errors = array();
        // university
        if (! v::notEmpty()->validate($info["university"]) ){
            $errors["university"] = "Invalid university name!";
        }
        // degree
        if (! v::int()->between(1,5,TRUE)->notEmpty()->validate($info["degree"]) ){
            $errors["degree"] = "Invalid degree!";
        }
        // major
        if (! v::notEmpty()->validate($info["major"]) ||
                ! in_array($info["major"], $this->majors_array)) {
            $errors["major"] = "Invalid major!";
        }
        return $errors;
    }
    private function validateFormHAndL($info) {
        $errors = array();
        // employer
        if (! v::notEmpty()->validate($info["employer"]) ){
            $errors["employer"] = "Invalid employer!";
        }
        // jobtitle
        if ( (!v::notEmpty()->validate($info["jobtitle"])) ||
                (!in_array($info["jobtitle"], $this->careers_array)) ) {
            $errors["jobtitle"] = "Invalid jobtitle!";
        }
        return $errors;
    }
    
    /*
     * function to add a case to database
     * the param in is the information extracted from form submitted.
     * This function return the db id of the new case if succeeded.
     * return -1 if failed
     */
    public function addCase($info)
    {
        $this->error = $this->validateForm($info);
        
        if (!empty($this->error)) {
            return -1;
        }
        // first check if this dos_caseid is added before
        $query_handle = $this->udb->query("SELECT * FROM `nocheck_cases` WHERE `DOS_CaseId`='{$info["dos_id"]}';");
        if ($this->udb->get_error_no()) {
            $this->error["dos_id"] = "DS-160 Case ID you gave is invalid";
            return -1;
        }
        $exist = $this->udb->fetch_assoc($query_handle);
        $this->udb->free_result($query_handle);
        if ($exist) {
            $this->error["dos_id"] = "DS-160 Case ID is already exist in our database! You can update ".
                    "it <a href='index.php?do=case&ac=update&id={$exist["id"]}'>here</a>!";
            return -1;
        }
        // insert the detail of the case into case table
        $query_handle = $this->udb->query("INSERT INTO `nocheck`.`nocheck_cases`
            (`id`, `RecordStatus`, `InfoStatus`, `Checkee_CaseId`, `Nickname`, `DOS_CaseId`, `Email`, `Password`,
            `ApplicationDate`, `ClearanceDate`, `VisaType`, `VisaEntry`,
            `Consulate`, `Major_old`, `ApplicationStatus`, `Note`, `LastName`,
            `FirstName`, `University`, `Degree`, `Employer`, `JobTitle`,
            `YearsInUSA`, `Citizenship`) VALUES (NULL, 0, 0, NULL, NULL,
            '{$info["dos_id"]}', '{$info["email"]}', '{$info["password"]}', '{$info["applydate"]}', NULL,'{$info["visatype"]}',
            '{$info["visaentry"]}', '{$info["consulate"]}', '{$info["major"]}', 2, '{$info["note"]}',
            '{$info["lastname"]}', '{$info["firstname"]}', '{$info["university"]}','{$info["degree"]}',
            '{$info["employer"]}', '{$info["jobtitle"]}', '{$info["yearsinusa"]}', '{$info["citizenship"]}');");
        if ($this->udb->get_error_no())
        {
            $this->udb->error["Unknown"] = "Fatal error: error when recording case detail!";
            return -1;
        }
        
        // get the id we just inserted
        $last_id = $this->udb->inserted_id();
        
        // record the time that user add this case
        $this->udb->query("INSERT INTO `nocheck_case_update`
            (`id`, `case_id`, `dos_id`, `status_code`, `update_time`) VALUES
            (NULL, {$last_id}, '{$info["dos_id"]}', 101, NOW() )");
        if ($this->udb->get_error_no())
        {
            $this->udb->error["Unknown"] = "Fatal error: error when recording case detail!";
            return -1;
        }

        return $last_id;
    }
    
    /*
     * function to update a case to database
     * the param in is the information extracted from form submitted.
     * This function return 1 if succeeded.
     * return -1 if failed
     */
    public function updateCase(&$info)
    {
        $this->error = $this->validateForm($info);
        
        if (!empty($this->error)){
            return -1;
        }
        // first check the password
        $query_handle = $this->udb->query("SELECT * FROM `nocheck_cases` WHERE `id`='{$info["id"]}';");
        if ($this->udb->get_error_no()) {
            $this->error["id"] = "Invalid ID";
            return -1;
        }
        $exist = $this->udb->fetch_assoc($query_handle);
        $this->udb->free_result($query_handle);
        if (!$exist) {
            $this->error["id"] = "ID does not exist";
            return -1;
        }
        if ($exist["Password"] != $info["password"]) {
            // TODO: retrieve password page link
            $this->error["password"] = "Password is wrong, you can retrieve your password from <a href='index.php?do=user&ac=ret_pwd'>here</a>";
            return -1;
        }       
        // recover the original email and ds160 number
        if ($info["dos_id"] == "NoChangeHere") {
            $info["dos_id"] = $exist["DOS_CaseId"];
        }
        if ($info["email"] == "NoChange@Here.com") {
            $info["email"] = $exist["Email"];
        }

        // insert the detail of the case into case table
        $this->udb->query("UPDATE `nocheck`.`nocheck_cases` SET
            `InfoStatus`='0',
            `DOS_CaseId`='{$info["dos_id"]}', `Email`='{$info["email"]}', `ApplicationDate`='{$info["applydate"]}',
            `ClearanceDate`='{$info["cleardate"]}', `VisaType`='{$info["visatype"]}', `VisaEntry`='{$info["visaentry"]}',
            `Consulate`='{$info["consulate"]}', `Major_old`='{$info["major"]}', `Note`='{$info["note"]}',
            `LastName`='{$info["lastname"]}', `FirstName`='{$info["firstname"]}', `University`='{$info["university"]}',
            `Degree`='{$info["degree"]}', `Employer`='{$info["employer"]}', `JobTitle`='{$info["jobtitle"]}',
            `YearsInUSA`='{$info["yearsinusa"]}', `Citizenship`='{$info["citizenship"]}'
            WHERE `id`={$info["id"]}");
        if ($this->udb->get_error_no())
        {
            $this->udb->error["Unknown"] = "Fatal error: error when updating case detail!";
            return -1;
        }
        
        // record the time that user update this case
        $this->udb->query("INSERT INTO `nocheck_case_update`
            (`id`, `case_id`, `dos_id`, `status_code`, `update_time`) VALUES
            (NULL, {$info["id"]}, '{$info["dos_id"]}', 102, NOW() )");
        if ($this->udb->get_error_no())
        {
            $this->udb->error["Unknown"] = "Fatal error: error when recording case detail!";
            return -1;
        }

        return 1;
    }
    
    
    
    
    /*
     * this function is used to get the information of one case from the database
     * the return value is a array which has the key and value same as the fetch_assoc
     * function would return
     */
    public function getCase($id)
    {
        $query_handle = $this->udb->query("SELECT * FROM `nocheck_cases` WHERE `id`={$id}");
        $db_result = $this->udb->fetch_assoc($query_handle);
        $this->udb->free_result($query_handle);
        
        $info["id"] = $id;
        $info["checkee_id"] = $db_result["Checkee_CaseId"];
        $info["dos_id"] = $db_result["DOS_CaseId"];
        $info["email"] = $db_result["Email"];
        $info["password"] = $db_result["Password"];
        $info["firstname"] = $db_result["FirstName"];
        $info["lastname"] = $db_result["LastName"];
        $info["visatype"] = $db_result["VisaType"];
        $info["visaentry"] = $db_result["VisaEntry"];
        $info["consulate"] = $db_result["Consulate"];
        $info["yearsinusa"] = $db_result["YearsInUSA"];
        $info["citizenship"] = $db_result["Citizenship"];
        $info["university"] = $db_result["University"];
        $info["degree"] = $db_result["Degree"];
        $info["major"] = $db_result["Major_old"];
        $info["employer"] = $db_result["Employer"];
        $info["jobtitle"] = $db_result["JobTitle"];
        $info["applydate"] = $db_result["ApplicationDate"];
        $info["cleardate"] = $db_result["ClearanceDate"];
        $info["note"] = $db_result["Note"];
        
        return $info;
    }
    
    /*
     * this function query the status of a case from database
     * the return value is the status code
     */
    public function getCaseDBStatus($db_id)
    {
        // TODO: finish this function if needed
    }
    
    /*
     * this function get the status of a case from DOS website
     * by crawling their webpage.
     * return value is the status code
     */
    public function getCaseDOSStatus($ds160_id, $config_checker_path, $config_checker_options)
    {
        if (substr(php_uname(), 0, 7) == "Windows"){
            $phantom_exe = $config_checker_path.DIRECTORY_SEPARATOR."phantomjs_win".DIRECTORY_SEPARATOR."phantomjs.exe ";
        }else{
            $phantom_exe = $config_checker_path.DIRECTORY_SEPARATOR."phantomjs_linux".DIRECTORY_SEPARATOR."bin".DIRECTORY_SEPARATOR."phantomjs ";
        }
        
        $phantom_exe = $phantom_exe." ".$config_checker_options." ";
        
        // run the phantomjs exe to get the status of this case
        exec($phantom_exe." ".$config_checker_path.DIRECTORY_SEPARATOR."check.js ".$ds160_id, $check_output);
        //echo $phantom_exe." ".$config_checker_path.DIRECTORY_SEPARATOR."check.js ".$open_case["DOS_CaseId"];
        $check_result = $check_output[0];

        return $check_result;
    }
    
    /*
     * this function converts the status name from the dos website to the code that represent it
     * return value: 0 for unknown status, 1 for clear, 2 for pending, 3 for rejected
     * TODO: what's the status name for reject???
     */
    public static function convertStatusNameToCode($status_name)
    {
        if ($status_name == "No Status") { // just created the ds160 form
            return 5;
        }elseif ($status_name == "Ready"){ // ?
            return 4;
        }elseif ($status_name == "Refused"){
            return 3;
        }elseif ($status_name == "Administrative Processing"){
            return 2;
        }elseif ($status_name == "Issued"){
            return 1;
        }else{
            return 0;
        }
    }
    
    /*
     * this function converts the case status code to readable names
     */
    public static function convertStatusCodeToName($status_code)
    {
        switch ($status_code){
            case 1:
                return "Issued";
            case 2:
                return "Administrative Processing";
            case 3:
                return "Refused";
            case 4:
                return "Ready";
            case 5:
                return "No Status";
            case 101:
                return "User Added Case";
            case 102:
                return "User Modified Case";
            default:
                return NULL;
        }
    }
    
    /*
     * get the masked email address, the masked email address will only show the first three chars
     * of the username of the email address.
     * the masked email address is used for showing to others in the case detail or page for updating case
     * input: the original email address
     * output: the masked(using *) address
     */
    public static function getMaskedEmailAddress($email)
    {
        if (!strpos($email, "@")) {
            return $email;
        }
        $cut = 3;
        $result_parts = explode("@", $email);
        if (strlen($result_parts[0]) > $cut) {
            $first = substr($result_parts[0], 0, $cut);
            return $first.str_repeat("*", strlen($result_parts[0])-$cut)."@".$result_parts[1];
        }else{
            return $result_parts[0].str_repeat("*", $cut)."@".$result_parts[1];
        }
    }
    
    /*
     * get the masked ds160 id, the masked ds160 id will only show the leading two and the tail three chars.
     * the masked ds160 id is used for showing to others in the case detail or page for updating case
     * input: the original ds160 id
     * output: the masked(using *) ds160 id
     */
    public static function getMaskedDS160ID($dsid)
    {
        $cut1 = 2;
        $cut2 = 3;
        
        if (strlen($dsid)-$cut1-$cut2 < 1) {
            return str_repeat("*", $cut1+$cut2);
        }
        
        $first = substr($dsid, 0, $cut1);
        $second = str_repeat("*", strlen($dsid)-$cut1-$cut2);
        $third = substr($dsid, strlen($dsid)-$cut2, $cut2);
        return $first.$second.$third;
    }
}
