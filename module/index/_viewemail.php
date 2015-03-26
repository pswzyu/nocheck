<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require(__DIR__.DIRECTORY_SEPARATOR."../../common/protect.php");

include(FROOT."classes/CaseOperation.class.php");

$pagesize = 10;

$table = array( NULL, NULL, NULL, NULL, NULL);
$pageinfo["total_page"] = 0;
$all_data = array();
$notify = "";

if ($pg_email == NULL || $pg_email == "")
{
    $notify = "<div class='error_msg'>Please give search content!</div>";
}else{

    $sql = "SELECT *,DATEDIFF(`ClearanceDate`,`ApplicationDate`) AS `wait`
                FROM `nocheck_cases`
                WHERE `Email` LIKE '%{$pg_email}%' ";

    $type_count = array_fill(0, count($enum_status), 0);
    $total_wait = 0;
    $query_handle = $udb->query($sql);

    $page_start = $pageno * $pagesize;
    $nof_record = 0;
    while ($one_data = $udb -> fetch_assoc($query_handle))
    {
        $nof_record ++;
        if ($nof_record > $page_start && $nof_record <= $page_start+$pagesize)
        {
            $ap_date = explode(" ", $one_data["ApplicationDate"]);
            $cl_date = explode(" ", $one_data["ClearanceDate"]);
            $one_data["ApplicationDate"] = $ap_date[0];
            $one_data["ClearanceDate"] = $cl_date[0];
            if ($one_data["Nickname"] == "") {
                $one_data["Nickname"] = CaseOperation::getMaskedDS160ID($one_data["DOS_CaseId"]);
            }
            $all_data[] = $one_data;
        }
        $type_count[intval($one_data["ApplicationStatus"])] += 1;
        $wait_time = empty($one_data["wait"])?0:intval($one_data["wait"]);
        if ($wait_time < 0) $wait_time = 0;
        // only consider the cleared case
        if ( intval($one_data["ApplicationStatus"]) == 1 )
        {
            $total_wait += $wait_time;
        }
    }

    $pageinfo["total_page"] = ceil($nof_record / $pagesize);

    $total_people = 0;
    for ($step = 0; $step != count($type_count); $step ++)
    {
        $total_people += $type_count[$step];
    }
    // only consider the cleared people
    $avg_wait = round( $total_wait / ($type_count[1]==0?1:$type_count[1]) );
    // month, pending, clear, reject, total, avgwait
    $table = array( $type_count[1], $type_count[2], $type_count[3],
        $total_people, $avg_wait );

    $udb -> free_result($query_handle);
}

?>

<div id="main_table" >
    <div id="notify_back" class=""><?php echo $notify; ?></div>
    <table border="1">
        <tr><th>Clear</th><th>Pending</th>
            <th>Rejected</th><th>Total</th><th>Avg. Wait</th>
        </tr>
        <?php
        echo "<tr><td>{$table[0]}</td><td>{$table[1]}</td><td>{$table[2]}</td>"
            . "<td>{$table[3]}</td><td>{$table[4]}</td></tr>";
        ?>
    </table>
    <table border="1">
        <tr><th>Update</th><th>NickName</th><th>Visa Type</th><th>Consulate</th>
            <th>Major</th><th>Status</th><th>Add Date</th><th>Clear Date</th>
            <th>Waiting Days</th><th>Details</th>
        </tr>
        <?php
        for ( $step = 0; $step != count($all_data); $step ++ )
        {
            $status_class = "";
            if ($all_data[$step]["ApplicationStatus"] == 1){
                $status_class = "app_status_issued";
            }
            echo "<tr class={$status_class}><td><a href='index.php?do=case&ac=update&id={$all_data[$step]["id"]}'>Update</a></td>"
            . "<td>{$all_data[$step]["Nickname"]}</td>"
            . "<td>{$enum_visatype[$all_data[$step]["VisaType"]]}</td>"
            . "<td>{$enum_consulate[$all_data[$step]["Consulate"]]}</td>"
            . "<td>{$all_data[$step]["Major_old"]}</td><td>{$enum_status[$all_data[$step]["ApplicationStatus"]]}</td>"
            . "<td>{$all_data[$step]["ApplicationDate"]}</td><td>{$all_data[$step]["ClearanceDate"]}</td>"
            . "<td>{$all_data[$step]["wait"]}</td>"
            . "<td><a href='index.php?do=case&ac=view&id={$all_data[$step]["id"]}'>Detail</a></td></tr>";
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
        echo "<div><a {$highlight} href='index.php?do=index&ac=email&email={$pg_email}"
            ."&pageno={$step}'>{$page}</a></div>";
        
    }
    ?>
</div>

