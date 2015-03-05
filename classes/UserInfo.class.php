<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserInfo
 *
 * @author PSWZYU
 */
class UserInfo {
    //put your code here
    
    var $udb;
    
    function __construct($db_con) {
        $this->udb = $db_con;
    }
    
    
    function getUserPassword($email, $ds160) {
        
        $query_handle = $this->udb->query("SELECT * FROM `nocheck_cases` WHERE `Email`='{$email}' AND `DOS_CaseId`='{$ds160}';");
        $table = $this->udb->fetch_assoc($query_handle);
        
        $this->udb->free_result($query_handle);
        
        if ($table) {
            return $table["Password"];
        } else {
            return NULL;
        }

    }
    
}
