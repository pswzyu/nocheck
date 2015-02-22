<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require(__DIR__.DIRECTORY_SEPARATOR."../../common/protect.php");

$pagesize = 10;

$pageinfo = get_yearmonth_page_d("2012-08", date("Y-m"), $page_now, $pagesize);

include_once(FROOT."classes/ViewStatistics.class.php");

$vs = new ViewStatistics($udb);

$table = $vs->viewAll($pageinfo["pagename_list"]);

?>

<div id="main_table" >
    <table border="1">
        <tr><th>Track</th><th>Month</th><th>Clear</th><th>Pending</th>
            <th>Rejected</th><th>Total</th><th>Avg. Wait</th>
        </tr>
        <?php
        for ($step = 0; $step != count($table); $step++)
        {
            $year_month = explode("-",$table[$step]["yearmonth"]);
            echo "<tr>
                <td><a href='index.php?do=index&ac=month&year={$year_month[0]}&month={$year_month[1]}'>Track</a></td>
                <td>{$table[$step]["yearmonth"]}</td><td>{$table[$step]["clear"]}</td>
                <td>{$table[$step]["pending"]}</td><td>{$table[$step]["rejected"]}</td>
                <td>{$table[$step]["total"]}</td><td>{$table[$step]["avg_wait"]}</td>
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

