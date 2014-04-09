<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
protect();

$pagesize = 10;


$sql = "SELECT *,DATEDIFF(`ClearanceDate`,`ApplicationDate`) AS `wait`
            FROM `nocheck_cases`
            WHERE YEAR(`ApplicationDate`) = '{$year}' 
            AND MONTH(`ApplicationDate`) = '{$month}' ";

$type_count = array_fill(0, count($enum_status), 0);
$total_wait = 0;
$udb->query($sql);

$table = [];
$all_data = [];
$page_start = $pageno * $pagesize;
$nof_record = 0;
while ($one_data = $udb -> fetch_assoc())
{
    $nof_record ++;
    if ($nof_record > $page_start && $nof_record <= $page_start+$pagesize)
    {
        $all_data[] = $one_data;
    }
    $type_count[intval($one_data["ApplicationStatus"])] += 1;
    $wait_time = empty($one_data["wait"])?0:intval($one_data["wait"]);
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
$table = [ $type_count[1], $type_count[2], $type_count[3],
    $total_people, $avg_wait ];

$udb -> free_result();

?>

<div id="main_table" >
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
            echo "<tr><td><a href='index.php?do=casedetail&ac=update&id={$all_data[$step]["id"]}'>Update</a></td>"
            . "<td>{$all_data[$step]["Checkee_CaseId"]}</td>"
            . "<td>{$enum_visatype[$all_data[$step]["VisaType"]]}</td>"
            . "<td>{$enum_consulate[$all_data[$step]["Consulate"]]}</td>"
            . "<td>{$all_data[$step]["Major_old"]}</td><td>{$enum_status[$all_data[$step]["ApplicationStatus"]]}</td>"
            . "<td>{$all_data[$step]["ApplicationDate"]}</td><td>{$all_data[$step]["ClearanceDate"]}</td>"
            . "<td>{$all_data[$step]["wait"]}</td>"
            . "<td><a href='index.php?do=casedetail&ac=view&id={$all_data[$step]["id"]}'>Detail</a></td></tr>";
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
        echo "<div><a {$highlight} href='index.php?do=index&ac=month&"
            . "month={$month_get}&pageno={$step}'>{$page}</a></div>";
        
    }
    ?>
</div>
