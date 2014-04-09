<?php
/*入参：$menubar_para["now_on"] 当前菜单位置
 *     $menubar_para["is_admin"] 当前用户类型， 是否为管理员 admin/user
 */

?>
<div id="menubar">
	<ul>
		<?php
			if (!$menubar_para["is_admin"])
			{ echo '<li><a href="index.php?do=bookman">图书搜索</a></li>
					<li><a href="index.php?do=cdman">光盘下载</a></li>';
			}
		?>
		 <li><a href="index.php?do=history">借阅记录</a></li>
		 <?php 
		 // 如果是管理员， 则多出这么多的功能
		 if ($menubar_para["is_admin"])
		 {
			echo '<li><a href="index.php?do=borrow">借书</a></li>
				  <li><a href="index.php?do=return">还书</a></li>
				  <li><a>特殊书目列表</a>
				      <ul>
				      	<li><a href="index.php?do=speciallist&ac=expired">超期书列表</a></li>
				      	<li><a href="index.php?do=speciallist&ac=deleted">已删除书列表</a></li>
				      	<li><a href="index.php?do=speciallist&ac=lost">已丢失书列表</a></li>
				      </ul>
				  </li>
				  <li><a href="index.php?do=userman">人员管理</a></li>
				  <li><a href="index.php?do=bookman">图书管理</a></li>
				  <li><a href="index.php?do=cdman">光盘管理</a></li>
			';
		 }
		 ?>
	</ul>
</div>