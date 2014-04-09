<?php

include_once("./common.php");


$dos = array("index", "addcase", "case_detail");

$do = @$_GET["do"];

//echo $do;

if(empty($do) || !in_array($do, $dos)) {
	$do = "index";
}

include(FROOT."module/".$do."/index.php");

?>