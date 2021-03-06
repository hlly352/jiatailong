<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$materialid = $_GET['id'];
if($_GET['submit']){
	$material_typename = trim($_GET['material_typename']);
	$sqlwhere = " WHERE `material_name` LIKE '%$material_typename%'";
}
$sql = "SELECT * FROM `db_other_material_specification` INNER JOIN `db_other_material_data` ON `db_other_material_data`.`dataid` = `db_other_material_specification`.`materialid` WHERE `db_other_material_specification`.`materialid` = '$materialid'";

$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `dataid` ASC" . $pages->limitsql;
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
  <h4>期间物料规格</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
          <input type="button" name="button" value="添加" class="button" onclick="location.href='other_material_typeae.php?action=specification&id=<?php echo $materialid ?>'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="other_material_typedo.php" name="material_type_list" method="post">
    <table>
      <tr>
        <th width="10%">ID</th>
        <th width="25%">物料名称</th>
        <th width="20%">物料规格</th>
        <th>标准库存</th>
        <th width="10%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $specificationid = $row['specificationid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $specificationid; ?>" /></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification_name']; ?></td>
        <td><?php echo $row['standard_stock']; ?></td>
        <td width="4%"><a href="other_material_typeae.php?id=<?php echo $specificationid; ?>&action=edit_specification"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del_specification" />
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