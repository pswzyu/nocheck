<?php

include("common.php");
//$query_result = $udb->query("SELECT * FROM `nocheck_cases` WHERE id=-1");
//$error_result = $udb->get_error_no();
//$data_result = $udb->fetch_assoc($query_result);
//
//print_r($query_result); // the return value of the query is a mysqli_result object or true
//// return false on failure
//echo "<br/>";
//print_r($error_result);
//echo "<br/>";
//print_r($data_result);
//echo "<br/>";

//437788 439986 453319 449917 404103

include ("classes/CaseOperation.class.php");

$co = new CaseOperation();

print_r($co->getMaskedDS160ID("AA004ORPIS"));
echo "<br/>";
print_r($co->getMaskedEmailAddress("a@gmail.com"));echo "<br/>";
print_r($co->getMaskedEmailAddress("a123456@gmail.com"));echo "<br/>";



/*
 * 1. add the validation of the ds160 id
 * 2. show masked email and ds160 in the detail page and update page
 */


?>