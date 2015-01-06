<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'api.abs.class.php';
include_once(FROOT."classes/ViewStatistics.class.php");
include_once(FROOT."common/utils/yearmonth.php");

include_once(FROOT."classes/CaseOperation.class.php");

class MyAPI extends API
{
    //protected $User;
    protected $udb;
    
    public function __construct($request, $origin) {
        parent::__construct($request);

        // TODO: do authentication here
        /*
        // Abstracted out for example
        $APIKey = new Models\APIKey();
        $User = new Models\User();

        if (!array_key_exists('apiKey', $this->request)) {
            throw new Exception('No API Key provided');
        } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
            throw new Exception('Invalid API Key');
        } else if (array_key_exists('token', $this->request) &&
             !$User->get('token', $this->request['token'])) {

            throw new Exception('Invalid User Token');
        }

        $this->User = $User;
        
        */
    }

    /**
     * Example of an Endpoint
     */
    protected function example() {
        if ($this->method == 'GET') {
            return "Your name is " . $this->User->name;
        } else {
            return "Only accepts GET requests";
        }
    }
    
    public function setDBConnection($db)
    {
        $this->udb = $db;
    }
    
    // endpoint functions start from here
    
    public function cases()
    {
        if ($this->verb == "add")
        {
            $case_operator = new CaseOperation($this->udb);
            $caseid = $case_operator->addCase($_POST);
            if ($caseid == -1)
            {
                return array("id"=>"-1", "error"=>$case_operator->getErrorMessage());
            }else
            {
                return array("id"=>$caseid);
            }
        }
        return array("args"=>$this->args, "verbs"=>$this->verb);
    }
    public function view()
    {
        if ($this->verb == "all")
        {
            $pageinfo = get_yearmonth_page_d("2012-08", date("Y-m"), $this->args[1], $this->args[0]);
            //print_r($pageinfo);
            $vs = new ViewStatistics($this->udb);
            $table = $vs->viewAll($pageinfo["pagename_list"]);
            
            return array("total_pages"=>$pageinfo["total_page"], "data"=>$table);
        }
        return array("args"=>$this->args, "verbs"=>$this->verb);
    }
 }


?>