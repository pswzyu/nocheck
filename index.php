<?php

include_once("./common.php");


$dos = array("index", "case", "user");

$do = _SAFEGET("do");

//echo $do;

if(empty($do) || !in_array($do, $dos)) {
	$do = "index";
}

include(FROOT."module/".$do."/index.php");


?>