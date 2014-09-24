<?php
header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

echo json_encode(array("name"=>"zhangyu"));

?>