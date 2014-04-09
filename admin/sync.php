<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$start_y = intval("2012");
$start_m = intval("08");

$end_y = intval(date("Y"));
$end_m = intval(date("m"));

while (true)
{
    echo "".$end_y."-".$end_m."<br/>";
    $s_end_m = $end_m < 10? "0$end_m":"$end_m";
    $page_content = file_get_contents("http://www.checkee.info/main.php?dispdate={$end_y}-{$s_end_m}");
    if (!$page_content)
    {
        die("network fail");
    }
    preg_match_all('/.\/update.php\?casenum=\d+/', $page_content, $matches);
    foreach ($matches[0] as $key => $value) {
        $casenum = explode("=", $value)[1]; // we need the number behind =
        
    }
    
    break;
    // decrement one month
    $end_m --;
    if ($end_m < 1)
    {
        $end_y --;
        $end_m = 12;
    }
    if ($end_y == $start_y && $end_m < $start_m)
    {
        break;
    }
}