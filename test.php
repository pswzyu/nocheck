<?php


$test = '
<p><h1>Update your case </h1></p>
<b>CaseNum is: 248177</b><br>
<b>ID is: jyun0207</b>
<form name="update" action="./update_process.php" method="post">
	
	<table width="100%" border="1" align="center" cellspacing="0">
	<tr><td><h3>Visa information</h3></td></tr>
	<tr><td>
	
<input type="hidden" name="casenum" value="248177"/> 
<input type="hidden" name="id" value="jyun0207"/> 

Password: <input type="password" size="15" name="password" /><br><br>

Email: (weekly email notification)<input type="text" size="35" name="email" value="yun.jiang@gmail.com"/> <br><br>

Check Date:(YYYY-MM-DD)

<INPUT TYPE="text" NAME="check_date" VALUE="2013-12-06" SIZE=20 >
<A HREF="#"
   onClick="cal.select(document.forms[\'update\'].check_date,\'anchor1\',\'yyyy-MM-dd\'); return false;"
   NAME="anchor1" ID="anchor1"><img border="0" src="./b_calendar.png"></A>



<br><br>

Visa Type: <select name="visa_type" >
<option value="F1" SELECTED>F1</option> 
<option value="F1">F1</option>
<option value="F2">F2</option>
<option value="H1">H1</option>
<option value="H4">H4</option>
<option value="J1">J1</option>
<option value="J2">J2</option>
<option value="B1">B1</option>
<option value="B2">B2</option>
<option value="L1">L1</option>
<option value="L2">L2</option>
</select>

Visa Entry: <select name="visa_entry">
<option value="Renewal" SELECTED>Renewal</option> 
<option value="New">New</option>
<option value="Renewal">Renewal</option></select><br><br>

US Consulate: <select name="city" >
<option value="ShangHai" SELECTED>ShangHai</option> 
<option value="BeiJing">BeiJing</option>
<option value="ChengDu">ChengDu</option>
<option value="Chennai">Chennai</option>
<option value="GuangZhou">GuangZhou</option>
<option value="Kolkata">Kolkata</option>
<option value="MexicoCity">MexicoCity</option>
<option value="Montreal ">Montreal </option>
<option value="Mumbai">Mumbai</option>
<option value="NewDelhi">NewDelhi</option>
<option value="Ottawa ">Ottawa </option>
<option value="Quebec">Quebec</option>
<option value="ShangHai">ShangHai</option>
<option value="ShenYang">ShenYang</option>
<option value="Tijuana ">Tijuana </option>
<option value="Toronto ">Toronto </option>
<option value="Vancouver ">Vancouver </option>
<option value="Others">Others</option>
</select><br><br>

Major: <input type="text" size="25" name="major" value="BME草拟吗"/>
<br><br>

Status: <select name="status">
<option value="Clear" SELECTED>Clear</option> 
<option value="Pending">Pending</option>
<option value="Clear">Clear</option>
<option value="Reject">Reject</option>
</select><br><br>

Complete Date:(YYYY-MM-DD)
<INPUT TYPE="text" NAME="clear_date" VALUE="2013-12-23" SIZE=20 >
<A HREF="#"
   onClick="cal.select(document.forms[\'update\'].clear_date,\'anchor2\',\'yyyy-MM-dd\'); return false;"
   NAME="anchor2" ID="anchor2"><img border="0" src="./b_calendar.png"></A>


<br><br>

Note:<br>
<textarea rows="10" cols="100" name="note">cc
</textarea> <br><br>
</td></tr>

<tr><td><h3>Personal information</h3></td></tr>
<tr><td>
	
Last Name: <input type="text" size="20" name="lastname" value="N/A"/><br><br>

First Name: <input type="text" size="20" name="firstname" value="N/A"/><br><br>

University(College): <input type="text" size="60" name="univ_college" value="N/A"/><br><br>

Degree: <select name="degree">
<option value="Ph.D" SELECTED>Ph.D</option> 
<option value="BS">BS</option>
<option value="MS">MS</option>
<option value="Ph.D">Ph.D</option>
<option value="Others">Others</option>
</select><br><br>

Employer: <input type="text" size="60" name="employer" value="N/A"/><br><br>

Job Title: <input type="text" size="60" name="job_title" value="N/A"/><br><br>

Years in Usa: <input type="text" size="5" name="years_in_usa" value="3"/><br><br>

Country: <input type="text" size="20" name="country" value="N/A"/><br>
	
</td></tr>

<tr><td>
* required<br>
<input type="submit" value="Update"/>
<a href="./email_verify.php?casenum=248177">Forgot your password?</a>
</td></tr></table>

</form>
';

preg_match('/name="note">(\X+?)<\/textarea>/u', $test, $matches);
$info["Note"] = $matches;

/*
Email:
Check Date:(YYYY-MM-DD
 * 
Visa Type
Visa Entry
US Consulate   
Major
Status
Complete Date
Note
Last Name
First Name
University(Colle
Degree
Employer
Job Title;
Years in Usa
Country
*/
print_r($info);

?>
ji0ng.bi0n@gmail.com
jinglei.ren@gmail.com 
