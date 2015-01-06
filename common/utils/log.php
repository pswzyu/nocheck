<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * this file contains the log utilities, for saving the data in user query and the result of that query
 * for every exposed page, it should call log_start at the begining of the code and call log_end at the
 * end of the execution
 */

/*
 * start a log session, this function will return the log record id in the database, later on, 
 * the log_end function can use this id to change the status of this query
 */
function log_start($page_name, $udb, $url, $http_get, $http_post)
{
    
}

function log_end($log_id)
{
    
}