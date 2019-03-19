<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
//查询程序
$sql_system = "SELECT `systemid`,`system_name` FROM `db_system` WHERE `system_status` = 1 ORDER BY `system_type` ASC,`system_order` ASC,`systemid` ASC";
$result_system = $db->query($sql_system);
if($_GET['submit']){
	$help_title = trim($_GET['help_title']);
	$systemid = $_GET['systemid'];
	if($systemid){
		$sql_systemid = " AND `db_system_help`.`systemid` = '$systemid'";
	}
	$sqlwhere = " WHERE `db_system_help`.`help_title` LIKE '%$help_title%' $sql_systemid";
}
$sql = "SELECT `db_system_help`.`helpid`,`db_system_help`.`help_title`,`db_system_help`.`help_status`,`db_system_help`.`dotime`,`db_system`.`system_name`,`db_employee`.`employee_name` FROM `db_system_help` INNER JOIN `db_system` ON `db_system`.`systemid` = `db_system_help`.`systemid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_system_help`.`employeeid` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,12);
$sqllist = $sql . " ORDER BY `db_system_help`.`helpid` DESC" . $pages->limitsql;
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
  <h4>系统帮助</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>标题：</th>
        <td><input type="text" name="help_title" class="input_txt" /></td>
        <th>系统程序：</th>
        <td><select name="systemid">
            <option value="">所有</option>
            <?php
			if($result_system->num_rows){
				while($row_system = $result_system->fetch_assoc()){
					echo "<option value=\"".$row_system['systemid']."\">".$row_system['system_name']."</option>";
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='system_helpae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="system_helpdo.php" name="system_help_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th>标题</th>
        <th width="15%">系统程序</th>
        <th width="10%">操作人</th>
        <th width="10%">时间</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $helpid = $row['helpid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $helpid; ?>" /></td>
        <td><?php echo $row['help_title']; ?></td>
        <td><?php echo $row['system_name']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $array_status[$row['help_status']]; ?></td>
        <td><a href="system_helpae.php?id=<?php echo $helpid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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