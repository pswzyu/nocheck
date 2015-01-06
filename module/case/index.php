<?php 

protect();

// 建立一个传参的数组
$menubar_para = array();
$menubar_para["is_admin"] = FALSE;
$menubar_para["now_on"] = "index";

// browsing the whole data or browse by month
$acs = array("add", "update", "view");
$ac = @$_GET["ac"];
if(empty($ac) || !in_array($ac, $acs)) {
	$ac = "view";
}

$pageno = @$_GET["pageno"];
if (empty($pageno)) {
    $page_now = 0;
} else {
    $page_now = $pageno;
}
$caseid = @$_GET["id"]; // caseid

?>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <link rel="stylesheet" type="text/css" href="common/style/style.css" />
    <link rel="stylesheet" type="text/css" href="module/case/style.css" />
    <script type="text/javascript" src="lib/js/jquery-2.1.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="lib/js/datepicker/jquery.datepick.css">
    <script type="text/javascript" src="lib/js/datepicker/jquery.plugin.js"></script>
    <script type="text/javascript" src="lib/js/datepicker/jquery.datepick.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            // initialize the date picker
            jQuery("#dp_applydate").datepick({dateFormat: 'yyyy-mm-dd'});
            // restore the last state (the last user submit information)
            var num_visatype = jQuery("#last_visatype").attr("value");
            if (num_visatype) jQuery(jQuery("#sel_visatype option")[num_visatype-1]).attr("selected","selected");
            var num_visaentry = jQuery("#last_visaentry").attr("value");
            if (num_visaentry) jQuery(jQuery("#sel_visaentry option")[num_visaentry-1]).attr("selected","selected");
            var num_consulate = jQuery("#last_consulate").attr("value");
            if (num_consulate) jQuery(jQuery("#sel_consulate option")[num_consulate-1]).attr("selected","selected");
            var num_degree = jQuery("#last_degree").attr("value");
            if (num_degree) jQuery(jQuery("#sel_degree option")[num_degree-1]).attr("selected","selected");
        });
    </script>
</head>
<body><div id="container_all">
	<?php include(FROOT."module/_header/index.php");?>
	<?php include(FROOT."module/_menubar/index.php");?>
	<div id="right_div">
            <div id="right_tab">
                
                
                <div id="tab_view" class="tab_title <?php if($ac=="view"){echo "tab_highlight";} ?>">
                    <a>View Case Detail</a>
                    <form action="index.php?do=case&ac=view&id=<?php echo $caseid; ?>" method="post">
                        <div><input type="submit" name="submit_type" value="Go" /></div>
                    </form>
                </div>
                <div id="tab_update" class="tab_title <?php if($ac=="type"){echo "tab_highlight";} ?>">
                    <a>Update this Case</a>
                    <form action="index.php?do=index&ac=type" method="post">
                        <div><label>username:</label><input type="text" name="username" /></div>
                        <div><label>password:</label><input type="password" name="password" /></div>
                        <div><input type="submit" name="submit_type" value="Go" /></div>
                    </form>
                </div>
                <div id="tab_add" class="tab_title <?php if($ac=="add"){echo "tab_highlight";} ?>">
                    <a>Add yours</a>
                    <form action="index.php?do=case&ac=add" method="post">
                        <div><input type="submit" name="submit_type" value="Go" /></div>
                    </form>
                </div>
            </div>
            <div id="main_content">
		<?php
                // two types of layout, one is for viewall, others use a layout
                // contain some statistical data
                if ($ac == "view")
                {
                    include_once(FROOT."module/case/_view.php");
                }
                elseif ($ac == "add")
                {
                    include_once(FROOT."module/case/_add.php");
                } else { // layout for others
                    
                }
                ?>
            </div>
	</div>
	<?php include(FROOT."module/_footer/index.php"); ?>
</div></body>
</html>
