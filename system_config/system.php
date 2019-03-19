<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$system_name = trim($_GET['system_name']);
	$system_type = $_GET['system_type'];
	if($system_type){
		$sql_system_type = " AND `system_type` = '$system_type'";
	}
	$sqlwhere = " WHERE `system_name` LIKE '%$system_name%' $sql_system_type";
}
$sql = "SELECT * FROM `db_system` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,12);
$sqllist = $sql . " ORDER BY `system_type` ASC,`system_order` ASC,`systemid` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>系统设置-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>系统程序</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>名称：</th>
        <td><input type="text" name="system_name" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="system_type">
            <option value="">所有</option>
            <?php
			foreach($array_system_type as $system_type_key=>$array_system_value){
				echo "<option value=\"".$system_type_key."\">".$array_system_value."</option>";
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='systemae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="systemdo.php" name="system_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="24%">名称</th>
        <th width="24%">目录</th>
        <th width="24%">类型</th>
        <th width="4%">排序</th>
        <th width="4%">状态</th>
        <th width="4%">成员</th>
        <th width="4%">确认人</th>
        <th width="4%">管理员</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $systemid = $row['systemid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $systemid; ?>" /></td>
        <td><?php echo $row['system_name']; ?></td>
        <td><?php echo $row['system_dir']; ?></td>
        <td><?php echo $array_system_type[$row['system_type']]; ?></td>
        <td><?php echo $row['system_order']; ?></td>
        <td><?php echo $array_status[$row['system_status']]; ?></td>
        <td><a href="system_employee.php?id=<?php echo $systemid; ?>"><img src="../images/system_ico/employee_10_10.png" width="10" height="10" /></a></td>
        <td><a href="system_confirm.php?id=<?php echo $systemid; ?>"><img src="../images/system_ico/employee_confirmer_10_10.png" width="10" height="10" /></a></td>
        <td><a href="system_admin.php?id=<?php echo $systemid; ?>"><img src="../images/system_ico/employee_admin_10_10.png" width="10" height="10" /></a></td>
        <td><a href="systemae.php?id=<?php echo $systemid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>