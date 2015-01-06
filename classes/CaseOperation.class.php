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
require_once(FROOT.'lib/php/formvalidator.php');

class CaseOperation {
    //put your code here
    private $udb;
    private $error;
    function __construct($db_con)
    {
        $this->udb = $db_con;
    }
    
    public function getErrorMessage()
    {
        return $this->error;
    }
    
    /*
     * function to add a case to database
     * the param in is the information extracted from form submitted.
     * This function return the db id of the new case if succeeded.
     * return -1 if failed
     */
    public function addCase($info)
    {
        $validator = new FormValidator();
        $validator->addValidation("dos_id","req","Please fill in Case ID!");
        $validator->addValidation("email","email","Please use a valid email address!");
        $validator->addValidation("email","req","Please fill in Email");
        $validator->addValidation("visatype","req","Please fill in the type of visa!");
        $validator->addValidation("visatype", "num", "Please fill in the type of visa!");
        $validator->addValidation("visaentry","req","Please fill in visa application type!");
        $validator->addValidation("visaentry", "num", "Please fill in visa application type!");
        $validator->addValidation("consulate","req","Please fill in the consulate!");
        $validator->addValidation("consulate", "num", "Please fill in the consulate!");
        $validator->addValidation("applydate", "req", "Please fill in the date of interview!");
        if($validator->ValidateForm($info))
        {
            // first check if this dos_caseid is added before
            $this->udb->query("SELECT * FROM `nocheck_cases` WHERE `DOS_CaseId`='{$info["dos_id"]}';");
            if ($this->udb->get_error_no())
            {
                $this->error["dos_id"] = "DOS Case ID you gave is invalid";
                return -1;
            }elseif ($this->udb->fetch_assoc()){
                $this->error["dos_id"] = "DOS Case ID is already exist in our database! You can update ".
                        "it after login!";
                return -1;
            }else{
                $this->udb->query("INSERT INTO `nocheck`.`nocheck_cases`
                    (`id`, `Checkee_CaseId`, `Nickname`, `DOS_CaseId`, `Email`,
                    `ApplicationDate`, `ClearanceDate`, `VisaType`, `VisaEntry`,
                    `Consulate`, `Major_old`, `ApplicationStatus`, `Note`, `LastName`,
                    `FirstName`, `University`, `Degree`, `Employer`, `JobTitle`,
                    `YearsInUSA`, `Citizenship`) VALUES (NULL, NULL, NULL,
                    '{$info["dos_id"]}', '{$info["email"]}', '{$info["applydate"]}', NULL,'{$info["visatype"]}',
                    '{$info["visaentry"]}', '{$info["consulate"]}', '{$info["major"]}', 2, '{$info["note"]}',
                    '{$info["lastname"]}', '{$info["firstname"]}', '{$info["university"]}','{$info["degree"]}',
                    '{$info["employer"]}', '{$info["jobtitle"]}', '{$info["yearsinusa"]}', '{$info["citizenship"]}');");
                if ($this->udb->get_error_no())
                {
                    $this->udb->error["Unknown"] = "Unknown error";
                    return -1;
                }else{
                    return $this->udb->inserted_id();
                }
            }
        }else{
            // here is for validation falure, write the error and return -1
            $this->error = $validator->GetErrors();
            return -1;
        }
    }
    
    /*
     * this function is used to get the information of one case from the database
     * the return value is a array which has the key and value same as the fetch_assoc
     * function would return
     */
    public function getCase($id)
    {
        // TODO: finish this function if needed
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
    public function getCaseDOSStatus($ds160_id, $config_checker_path)
    {
        if (substr(php_uname(), 0, 7) == "Windows"){
            $phantom_exe = $config_checker_path.DIRECTORY_SEPARATOR."phantomjs_win".DIRECTORY_SEPARATOR."phantomjs.exe";
        }else{
            $phantom_exe = $config_checker_path.DIRECTORY_SEPARATOR."phantomjs_linux".DIRECTORY_SEPARATOR."phantomjs";
        }
        
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
    public function convertStatusNameToCode($status_name)
    {
        if ($status_name == "Ready"){
            return 2;
        }elseif ($status_name == "Administrative Processing"){
            return 2;
        }elseif ($status_name == "Issued"){
            return 1;
        }else{
            return 0;
        }
    }
}
