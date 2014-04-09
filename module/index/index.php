<?php 

if (!defined("NOCHECK"))
{
    die("Internal Error!");
}
// 建立一个传参的数组
$menubar_para = array();
// 如果当前登录ecms的用户id和设定中的管理员id相同， 则为管理员， 否则为用户
$menubar_para["is_admin"] = FALSE;
$menubar_para["now_on"] = "index";

// browsing the whole data or browse by month
$acs = array("viewall", "month", "type", "email");
$ac = @$_GET["ac"];
if(empty($ac) || !in_array($ac, $acs)) {
	$ac = "viewall";
}

print_r($_POST);
$pageno = @$_GET["pageno"];
if (empty($pageno)) {
    $page_now = 0;
} else {
    $page_now = $pageno;
}
// if now we are in view all mode, we should get the page number
if ($ac == "viewall")
{
    
} else {
    $month_get = @$_GET["month"];
    $month = 0;
    $year = 0;
    if (!empty($month_get)) // month data can come from get
    {
        $tmpym = explode("-", $month_get);
        $year = intval($tmpym[0]);
        $month = intval($tmpym[1]);
    } else { // can also from post
        $month = @$_POST["month"];
        $year = @$_POST["year"];
        $month_get = "".$year."-".$month;
    }
    if (!$month || !$year)
    {
        die("Invalid year or month!");
    }
}

?>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<link rel="stylesheet" type="text/css" href="common/style/style.css" />
	<link rel="stylesheet" type="text/css" href="module/index/style.css" />
</head>
<body><div id="container_all">
	<?php include(FROOT."module/_header/index.php");?>
	<?php include(FROOT."module/_menubar/index.php");?>
	<div id="right_div">
            <div id="right_tab">
                <div id="tab_viewall" class="tab_title <?php if($ac=="viewall"){echo "tab_highlight";} ?>">
                    <a>View All Records</a>
                    <form action="index.php?do=index&ac=viewall" method="post">
                        <div><input type="submit" name="submit_month" value="Go" /></div>
                    </form>
                </div>
                <div id="tab_month" class="tab_title <?php if($ac=="month"){echo "tab_highlight";} ?>">
                    <a>View by Month</a>
                    <form action="index.php?do=index&ac=month" method="post">
                        <div><select name="year">
                            <?php
                            for ($step = intval(date("Y")); $step != 2012 -1; $step --)
                            {
                                $sel = $year==$step?"selected='selected'":"";
                                echo "<option $sel value='{$step}'>{$step}</option>";
                            }
                            ?>
                        </select>
                        <select name="month">
                            <?php
                            for ($step = 1; $step != 13; $step ++)
                            {
                                $sel = $month==$step?"selected='selected'":"";
                                echo "<option $sel value='{$step}'>{$step}</option>";
                            }
                            ?>
                        </select></div>
                        <div><input type="submit" name="submit_month" value="Go" /></div>
                    </form>
                </div>
                <div id="tab_type" class="tab_title <?php if($ac=="type"){echo "tab_highlight";} ?>">
                    <a>View by Case Status</a>
                    <form action="index.php?do=index&ac=type" method="post">
                        <div><select name="status">
                                <option value="1">Clear</option>
                                <option value="2">Pending</option>
                                <option value="3">Reject</option>
                        </select></div>
                        <div><input type="submit" name="submit_type" value="Go" /></div>
                    </form>
                </div>
                <div id="tab_email" class="tab_title <?php if($ac=="email"){echo "tab_highlight";} ?>">
                    <a>Search by Email</a>
                    <form action="index.php?do=index&ac=email" method="post">
                        <div><input type="text" name="email"  /></div>
                        <div><input type="submit" name="submit_type" value="Go" /></div>
                    </form>
                </div>
            </div>
            <div id="main_content">
		<?php
                // two types of layout, one is for viewall, others use a layout
                // contain some statistical data
                if ($ac == "viewall")
                {
                    include_once(FROOT."common/utils/yearmonth.php");
                    include_once(FROOT."module/index/_viewall.php");
                    
                } else { // layout for others
                    include_once(FROOT."module/index/_viewmonth.php");
                }
                ?>
            </div>
	</div>
	<?php include(FROOT."module/_footer/index.php"); ?>
</div></body>
</html>
