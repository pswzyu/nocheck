<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
protect();

$pagesize = 5;

$pageinfo = get_yearmonth_page_d("2012-08", date("Y-m"), $page_now, $pagesize);

$table = [];

foreach ( $pageinfo["pagename_list"] as $key=>$value )
{
    $ym = explode("-", $value);
    $sql = "SELECT *,DATEDIFF(`ClearanceDate`,`ApplicationDate`) AS `wait`
                FROM `nocheck_cases`
                WHERE YEAR(`ApplicationDate`) = '{$ym[0]}' 
                AND MONTH(`ApplicationDate`) = '{$ym[1]}' ;";
    
    $type_count = array_fill(0, count($enum_status), 0);
    $total_wait = 0;
    $udb->query($sql);
    while ($one_data = $udb -> fetch_assoc())
    {
        $type_count[intval($one_data["ApplicationStatus"])] += 1;
        $wait_time = empty($one_data["wait"])?0:intval($one_data["wait"]);
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
    $table[] = [ $value, $type_count[1], $type_count[2], $type_count[3],
        $total_people, $avg_wait ];
    
    $udb -> free_result();
}

?>

<div id="main_table" >
    <table border="1">
        <tr><th>Track</th><th>Month</th><th>Clear</th><th>Pending</th>
            <th>Rejected</th><th>Total</th><th>Avg. Wait</th>
        </tr>
        <?php
        for ($step = 0; $step != count($table); $step++)
        {
            echo "<tr>
                <td><a href='index.php?do=index&ac=month&month={$table[$step][0]}'>Track</a></td>
                <td>{$table[$step][0]}</td><td>{$table[$step][1]}</td>
                <td>{$table[$step][2]}</td><td>{$table[$step][3]}</td>
                <td>{$table[$step][4]}</td><td>{$table[$step][5]}</td>
            </tr>";
        }
        ?>
    </table>
</div>
<div id="paging">
    <?php
    for ($step = 0; $step != $pageinfo["total_page"]; $step++)
    {
        $page = $step + 1;
        $highlight = $step==$pageno?"class='page_highlight'":"";
        echo "<div><a {$highlight} href='index.php?do=index&ac=viewall&pageno={$step}'>{$page}</a></div>";
        
    }
    ?>
</div>

