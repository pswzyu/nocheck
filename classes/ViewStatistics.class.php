<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ViewStatistics
 *
 * @author pswzyu
 */
class ViewStatistics {
    // db connection
    public $udb;
    
    public function __construct($db_connection)
    {
        $this->udb = $db_connection;
    }
    
    public function viewAll($yearmonth_list)
    {
        $table = array();

        foreach ( $yearmonth_list as $key=>$value )
        {
            $ym = explode("-", $value);
            $sql = "SELECT *,DATEDIFF(`ClearanceDate`,`ApplicationDate`) AS `wait`
                        FROM `nocheck_cases`
                        WHERE YEAR(`ApplicationDate`) = '{$ym[0]}' 
                        AND MONTH(`ApplicationDate`) = '{$ym[1]}' ;";

            $type_count = array_fill(0, count(Enums::$enum_status), 0);
            $total_wait = 0;
            $query_handle = $this->udb->query($sql);
            while ($one_data = $this->udb -> fetch_assoc($query_handle))
            {
                $type_count[intval($one_data["ApplicationStatus"])] += 1;
                $wait_time = empty($one_data["wait"])?0:intval($one_data["wait"]);
                if ($wait_time < 0) $wait_time = 0;
                // only consider the cleared case
                if ( intval($one_data["ApplicationStatus"]) == 1 )
                {
                    $total_wait += $wait_time;
                }
            }

            $total_people = 0;
            for ($step = 0; $step != count($type_count); $step ++)
            {
                $total_people += $type_count[$step];
            }
            // only consider the cleared people
            $avg_wait = round( $total_wait / ($type_count[1]==0?1:$type_count[1]) );
            // month, pending, clear, reject, total, avgwait
            $table[] = array( "yearmonth"=>$value, "clear"=>$type_count[1], "pending"=>$type_count[2],
                "rejected"=>$type_count[3], "total"=>$total_people, "avg_wait"=>$avg_wait );

            $this->udb -> free_result($query_handle);
        }
        
        return $table;
    }
    
}

?>