<?php 

// ecms中作为图书管理员的id
$admin_id = 1;

// mediawiki api的位置
$config_api_url= "http://localhost/wiki/api.php";

// 超级密码 可以跳过借书本书和超期限制
$config_super_code = "123123";
$config_database_server = "issuemyvisa.chqisrnskoqf.us-west-2.rds.amazonaws.com";
$config_database_username = "issuemyvisa";
$config_database_password = "issuemyvisa00";


// 分页条数
$config_page_size = 50;


// 角色持书数量， 单位为本
$config_teacher_book_count = 10;
$config_master_book_count = 10;
$config_ug_book_count = 10;

// 角色持书时间， 单位为秒
$config_teacher_book_keep = 10;
$config_master_book_keep = 10;
$config_ug_book_keep = 10;
?>
