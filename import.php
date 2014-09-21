<?php

function saddslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = saddslashes($val);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}


$mysql = mysql_connect("localhost", "root", "");
mysql_query("USE `nocheck`");
mysql_query("SET NAMES UTF8");

setlocale(LC_ALL, "zh_CN.UTF-8");
$file = fopen('D:\workspace\nocheck\doc\checkcheck.csv','r'); 
$data = fgetcsv($file);
//Checkee_CaseId, VisaType, ApplicationDate, ClearanceDate, Consulate, Major, ApplicationStatus

$consult = array( "", "BeiJing", "ChengDu", "Chennai", "Europe",
        "GuangZhou", "HongKong", "Kolkata", "MexicoCity", "Montreal",
        "Mumbai", "NewDelhi", "Ottawa", "Quebec", "ShangHai", "ShenYang",
        "Tijuana", "Toronto", "Vancouver", "Others" );

$visatype = array( "", "F1", "F2", "H1", "H4", "J1", "J2", "B1", "B2", "L1", "L2" );

$status_type = array( "", "Clear", "Pending", "Reject" );

while ($data = fgetcsv($file)) {    //每次读取CSV里面的一行内容   
	//print_r($data); //此为一个数组，要获得每一个数据，访问数组下标即可 
	$data = saddslashes($data);
        
        if (empty($data[1])) {
            $visatype_id = 0;
        } else {
            $visatype_id = array_search($data[1], $visatype);
        }
        if ($visatype_id === FALSE) {
            die("visatype not found");
        }
        
        if ( empty($data[4]) ) {
            $consult_id = 0;
        } else {
            $consult_id = array_search($data[4], $consult);
        }
        if ($consult_id === FALSE) {
            die("consult not found");
        }
        
        if (empty($data[6])) {
            $status_id = 0;
        } else {
            $status_id = array_search($data[6], $status_type);
        }
        if ($status_id === FALSE) {
            die("status_type not found");
        }
        
        // check weather this record has already been imported
        $sql = "SELECT * FROM `nocheck`.`nocheck_cases` WHERE `Checkee_CaseId`={$data[0]};";
        $result = mysql_query($sql);
        $sqldata = mysql_fetch_assoc($result);
        if ($sqldata) {
            continue;
        }
        
	$sql1 = "INSERT INTO `nocheck`.`nocheck_cases` (`id` ,`caseid` ,`Checkee_CaseId` ,`VisaType` ,`ApplicationDate` ,`ClearanceDate` ,
		`Consulate` ,`Major_old` ,`ApplicationStatus` )
                VALUES (NULL , NULL, '{$data[0]}', '{$visatype_id}', '{$data[2]}', '{$data[3]}',
		'{$consult_id}', '{$data[5]}', '{$status_id}');";
	mysql_query($sql1);
	//echo $sql;
        $err = mysql_errno();
	if ( $err )
	{
		echo mysql_error();
		break;
	}
}   
fclose($file);   



?>

