<?php 

require(__DIR__.DIRECTORY_SEPARATOR."../../common/protect.php");

// 建立一个传参的数组
$menubar_para = array();
$menubar_para["is_admin"] = FALSE;
$menubar_para["now_on"] = "user";

// browsing the whole data or browse by month
$acs = array("ret_pwd");
$ac = _SAFEGET("ac");
if(empty($ac) || !in_array($ac, $acs)) {
	$ac = $acs[0];
}

$pageno = _SAFEGET("pageno");
if (empty($pageno)) {
    $page_now = 0;
} else {
    $page_now = $pageno;
}

if ($ac == "ret_pwd"){
    if (isset($_POST["submit"])) {
        
    }
}



?>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <link rel="stylesheet" type="text/css" href="module/common/style/style.css" />
    <link rel="stylesheet" type="text/css" href="module/case/style.css" />
    <script type="text/javascript" src="lib/js/jquery-2.1.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/js/datepicker/jquery.datepick.css">
    <script type="text/javascript" src="lib/js/datepicker/jquery.plugin.js"></script>
    <script type="text/javascript" src="lib/js/datepicker/jquery.datepick.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            // initialize the date picker
            jQuery("#dp_applydate").datepick({dateFormat: 'yyyy-mm-dd'});
            jQuery("#dp_cleardate").datepick({dateFormat: 'yyyy-mm-dd'});
            // restore the last state (the last user submit information)
            var num_visatype = jQuery("#last_visatype").attr("value");
            if (num_visatype) jQuery(jQuery("#sel_visatype option")[num_visatype]).attr("selected","selected");
            var num_visaentry = jQuery("#last_visaentry").attr("value");
            if (num_visaentry) jQuery(jQuery("#sel_visaentry option")[num_visaentry]).attr("selected","selected");
            var num_consulate = jQuery("#last_consulate").attr("value");
            if (num_consulate) jQuery(jQuery("#sel_consulate option")[num_consulate]).attr("selected","selected");
            var num_degree = jQuery("#last_degree").attr("value");
            if (num_degree) jQuery(jQuery("#sel_degree option")[num_degree]).attr("selected","selected");
        });
    </script>
</head>
<body><div id="container_all">
	<?php include(FROOT."module/_header/index.php");?>
	<?php include(FROOT."module/_menubar/index.php");?>
	<div id="right_div">
            <div id="right_tab">
                
                <div id="tab_view" class="tab_title <?php if($ac=="ret_pwd"){echo "tab_highlight";} ?>">
                    <a>Retrieve Password</a>
                    <!--<form action="index.php?do=case&ac=view&id=" method="post">
                        <div><input type="submit" name="submit_type" value="Go" <?php ?> /></div>
                    </form>-->
                </div>
                <div class="clear_all"></div>
                
            </div>
            <div id="main_content">
		<?php
                // two types of layout, one is for viewall, others use a layout
                // contain some statistical data
                if ($ac == "ret_pwd"){
                    include_once(FROOT."module/user/_ret_pwd.php");
                }
                ?>
            </div>
	</div>
	<?php include(FROOT."module/_footer/index.php"); ?>
</div></body>
</html>
