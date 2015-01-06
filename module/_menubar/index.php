<?php
/*入参：$menubar_para["now_on"] 当前菜单位置
 *     $menubar_para["is_admin"] 当前用户类型， 是否为管理员 admin/user
 */

?>
<div id="menubar">
    <ul>
        <?php
            if (!$menubar_para["is_admin"])
            { echo '<li><a href="index.php">Homepage</a></li>
                    <li><a href="index.php?do=case&ac=add">Add Your Case</a></li>';
            }
        ?>
    </ul>
</div>