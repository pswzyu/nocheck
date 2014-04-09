<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>


<?php
// 引用解析html的库
include("./simplehtmldom/simple_html_dom.php");

// 存放cookie的文件，每次运行使用一个不重复的文件进行保存，在最后删掉
$cookie_save = "cook_sa.txt";

// 接收命令的输出
$output_array = array();

// 运行抓取第一步，先访问网址，获取cookie
exec("curl -c {$cookie_save} https://ceac.state.gov/CEACStatTracker/Status.aspx?App=NIV", $output_array );
$output = str_join($output_array);

// 解析获取302跳转地址
$html = str_get_html($output);
$forward_add = urldecode($html->find("a", 0)->href);
$new_url = "https://ceac.state.gov".$forward_add;

// 执行抓取第二步，访问跳转后的地址
unset($output_array);
exec("curl -b {$cookie_save} -c {$cookie_save} {$new_url}", $output_array );
$output = str_join($output_array);

//解析第二步获取的信息，找到post时需要的信息
$html->clear();
$html = str_get_html($output);
$input_elements = $html->find("input");
$script_elements = $html->find("script");
foreach ($input_elements as $val)
{
	$input_field_name = trim(urldecode($val->name));
	if ($input_field_name == "__EVENTVALIDATION")
	{
		$EVENTVALIDATION = trim($val->value);
	}elseif ($input_field_name == "__VIEWSTATE")
	{
		$VIEWSTATE = trim($val->value);
	}else
	{
	}
}
foreach ($script_elements as $val)
{
	$script_field_name = trim(htmlspecialchars_decode(urldecode($val->src)));
	if (strpos($script_field_name, "AjaxControlToolkit") != false)
	{
		$ctl00_ToolkitScript = $script_field_name;
		$dispose = mb_strlen("/CEACStatTracker/Status.aspx?_TSM_HiddenField_=ctl00_ToolkitScriptManager1_HiddenField&_TSM_CombinedScripts_=");
		$ctl00_ToolkitScript = substr($ctl00_ToolkitScript, $dispose, mb_strlen($ctl00_ToolkitScript) - $dispose);
		exec("curl -b {$cookie_save} -c {$cookie_save} -L https://ceac.state.gov".$script_field_name);
	}
	
	
}

// 执行第三步，将所有信息post出去获取签证状态
$case_no = "AA003DOTHO";
unset($output_array);
$cmd_line = "curl -b {$cookie_save} -c {$cookie_save} --referer {$new_url} -d \"__ASYNCPOST=true&__EVENTARGUMENT=&__EVENTTARGET=&__EVENTVALIDATION=".rawurlencode($EVENTVALIDATION)."&__LASTFOCUS=&__VIEWSTATE=".rawurlencode($VIEWSTATE)."&ctl00%24ContentPlaceHolder1%24btnSubmit.x=90&ctl00%24ContentPlaceHolder1%24btnSubmit.y=20&ctl00%24ContentPlaceHolder1%24ddlApplications=NIV&ctl00%24ContentPlaceHolder1%24ddlLocation=&ctl00%24ContentPlaceHolder1%24txbCase={$case_no}&ctl00%24ToolkitScriptManager1=ctl00%24ContentPlaceHolder1%24UpdatePanel1%7Cctl00%24ContentPlaceHolder1%24btnSubmit&ctl00_ToolkitScriptManager1_HiddenField=".rawurlencode($ctl00_ToolkitScript)."\" https://ceac.state.gov/CEACStatTracker/Status.aspx?App=NIV";
exec($cmd_line, $output_array);
$output = str_join($output_array);


echo $EVENTVALIDATION."<br/><br/>".$VIEWSTATE."<br/><br/>".$ctl00_ToolkitScript;

echo "<br/><br/>";
echo $output;
echo "<br/><br/>";
echo $cmd_line;


function str_join($str_array)
{
	$ret = "";
	foreach( $str_array as $index=>$line )
	{
		$ret = $ret.$line;
	}
	return $ret;
}
?>

















0316-5555178/2204933
http://www.taobao.com/webww/   qiudaxin 
http://item.taobao.com/item.htm?spm=a1z10.3.w4002-1043448152.9.uhFbZc&id=13193189742
</head>
</html>


