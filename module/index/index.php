<?php 

require(__DIR__.DIRECTORY_SEPARATOR."../../common/protect.php");

// 建立一个传参的数组
$menubar_para = array();
// 如果当前登录ecms的用户id和设定中的管理员id相同， 则为管理员， 否则为用户
$menubar_para["is_admin"] = FALSE;
$menubar_para["now_on"] = "index";

// browsing the whole data or browse by month
$acs = array("viewall", "month", "type", "email");
$ac = _SAFEGET("ac");
if(empty($ac) || !in_array($ac, $acs)) {
	$ac = "viewall";
}

// if the page contain more than one page, we should get the page number
$pageno = _SAFEGET("pageno");
if (empty($pageno)) {
    $page_now = 0;
} else {
    $page_now = $pageno;
}

// for different view modes, get the parameters that used in different modes
if ($ac == "viewall")
{
    
} elseif ($ac == "month") {
    // default value of year month is current year and month
    if (isset($_GET["month"]) && is_numeric($_GET["month"])){
        $month = intval($_GET["month"]);
    }else{
        $month = intval(date("m"));
    }
    
    if (isset($_GET["year"]) && is_numeric($_GET["year"])){
        $year = intval($_GET["year"]);
    }else{
        $year = intval(date("Y"));
    }
} elseif ($ac == "type") {
    // for this view mode, get the type and the recent days uploaded by user
    // the status code is according to the types.php
    if (isset($_GET["status"]) && is_numeric($_GET["status"])){
        $pg_status = intval($_GET["status"]);
    }else{
        $pg_status = 1;
    }
    
    if (isset($_GET["how_recent"]) && is_numeric($_GET["how_recent"])){
        $pg_recent = intval($_GET["how_recent"]);
    }else{
        $pg_recent = 1;
    }

}elseif ($ac == "email") // search by email mode
{
    // need to extract the email address used typed in
    if (isset($_GET["email"]) ){
        $pg_email = $_GET["email"];
    }else{
        $pg_email = "";
    }
}

?>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<link rel="stylesheet" type="text/css" href="module/common/style/style.css" />
	<link rel="stylesheet" type="text/css" href="module/index/style.css" />
</head>
<body><div id="container_all">
	<?php include(FROOT."module/_header/index.php");?>
	<?php include(FROOT."module/_menubar/index.php");?>
	<div id="right_div">
            <div id="right_tab">
                <div id="tab_viewall" class="tab_title <?php if($ac=="viewall"){echo "tab_highlight";} ?>">
                    <a>View All Records</a>
                    <form action="index.php?do=index&ac=viewall" method="get">
                        <div><input type="submit" name="submit_month" value="Go" /></div>
                    </form>
                </div>
                <div id="tab_month" class="tab_title <?php if($ac=="month"){echo "tab_highlight";} ?>">
                    <a>View by Month</a>
                    <form action="index.php" method="get">
                        <input type="hidden" name="do" value="index"/>
                        <input type="hidden" name="ac" value="month"/>
                        <div><select name="year">
                            <?php
                            for ($step = intval(date("Y")); $step != 2012 -1; $step --)
                            {
                                $sel = isset($year)&&$year==$step?"selected='selected'":"";
                                echo "<option $sel value='{$step}'>{$step}</option>";
                            }
                            ?>
                        </select>
                        <select name="month">
                            <?php
                            for ($step = 1; $step != 13; $step ++)
                            {
                                $sel = isset($month)&&$month==$step?"selected='selected'":"";
                                echo "<option $sel value='{$step}'>{$step}</option>";
                            }
                            ?>
                        </select></div>
                        <div><input type="submit" name="submit_month" value="Go" /></div>
                    </form>
                </div>
                <div id="tab_type" class="tab_title <?php if($ac=="type"){echo "tab_highlight";} ?>">
                    <a>Recent Cases</a>
                    <form action="index.php" method="get">
                        <input type="hidden" name="do" value="index"/>
                        <input type="hidden" name="ac" value="type"/>
                        <div><select name="status">
                                <option <?php if(isset($pg_status)&&$pg_status==1){ echo "selected='selected'";}?> value="1">Clear</option>
                                <!--<option value="2">Pending</option>-->
                                <option <?php if(isset($pg_status)&&$pg_status==3){ echo "selected='selected'";}?> value="3">Reject</option>
                            </select>
                            <a>Cases</a><br/>
                            <a>in Last</a>
                            <select name="how_recent">
                                <option <?php if(isset($pg_recent)&&$pg_recent==1){ echo "selected='selected'";}?> value="1">7</option>
                                <option <?php if(isset($pg_recent)&&$pg_recent==2){ echo "selected='selected'";}?> value="2">15</option>
                                <option <?php if(isset($pg_recent)&&$pg_recent==3){ echo "selected='selected'";}?> value="3">30</option>
                                <option <?php if(isset($pg_recent)&&$pg_recent==4){ echo "selected='selected'";}?> value="4">90</option>
                            </select>
                            <a>Days</a>
                        </div>
                        <div><input type="submit" name="submit_type" value="Go" /></div>
                    </form>
                </div>
                <div id="tab_email" class="tab_title <?php if($ac=="email"){echo "tab_highlight";} ?>">
                    <a>Search by Email</a>
                    <form action="index.php" method="get">
                        <input type="hidden" name="do" value="index"/>
                        <input type="hidden" name="ac" value="email"/>
                        <div><input type="text" name="email" <?php if(isset($pg_email)){
                            echo "value=\"".sstripslashes($pg_email)."\""; } ?> /></div>
                        <div><input type="submit" name="submit_type" value="Go" /></div>
                    </form>
                </div>
                <div class="clear_all"></div>
            </div>
            <div id="main_content">
		<?php
                // two types of layout, one is for viewall, others use a layout
                // contain some statistical data
                if ($ac == "viewall")
                {
                    include(FROOT."common/utils/yearmonth.php");
                    include(FROOT."module/index/_viewall.php");
                    
                } elseif ($ac == "month") { // layout for others
                    include(FROOT."module/index/_viewmonth.php");
                } elseif ($ac == "type") {
                    include(FROOT."module/index/_viewtype.php");
                } elseif ($ac == "email") {
                    include(FROOT."module/index/_viewemail.php");
                }
                ?>
            </div>
	</div>
	<?php include(FROOT."module/_footer/index.php"); ?>
</div></body>
</html>
