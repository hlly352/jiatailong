<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$material_typename = trim($_GET['material_typename']);
	$sqlwhere = " WHERE `material_typename` LIKE '%$material_typename%'";
}
//获取父级类型的id
$sqlwhere = '';
if($_GET['id']){
	$sql_pid = $_GET['id'];
	$sqlwhere = "WHERE `db_mould_check_type`.`pid` = '$sql_pid'";
}else{
	$sqlwhere = "WHERE `db_mould_check_type`.`pid` = '0'";
}
if($_GET['pid']){
	$pid = $_GET['pid'];
	$sqlwhere = "WHERE `db_mould_check_type`.`id` = '$pid'";
}
$sql = "SELECT `db_mould_check_type`.`pid`,`db_mould_check_type`.`id`,`db_mould_check_type`.`typename`,`db_parent_type`.`typename` AS `pname` FROM `db_mould_check_type` LEFT JOIN `db_mould_check_type` AS `db_parent_type` ON `db_mould_check_type`.`pid` = `db_parent_type`.`id` $sqlwhere ";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `id` DESC" . $pages->limitsql;
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
<title>基础数据-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具图纸检查表项目类型</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>类型名称：</th>
        <td><input type="text" name="material_typename" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='mould_check_typeae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="mould_check_typedo.php" name="material_type_list" method="post">
    <table>
      <tr>
        <th>ID</th>
        <th>父级类型</th>
        <th>类型名称</th>
        <th>父类</th>
        <th>子类</th>
        <th>Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $typeid = $row['id'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $typeid; ?>" /></td>
        <td><?php echo $row['pname']; ?></td>
        <td><?php echo $row['typename']; ?></td>
        <td>
        	<a href="mould_check_type.php?pid=<?php echo $row['pid']; ?>">
        		<img src="../images/system_ico/info_8_10.png" width="15">
        	</a>
        </td>
        <td>
			<a href="mould_check_type.php?id=<?php echo $typeid; ?>">
				<img src="../images/system_ico/info_8_10.png" width="15">
			</a>
        </td>
        <td width="4%"><a href="moul   d_check_typeae.php?id=<?php echo $typeid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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