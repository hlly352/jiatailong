<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$unit_name = trim($_GET['unit_name']);
	$sqlwhere = " WHERE `unit_name` LIKE '%$unit_name%'";
}
$sql = "SELECT * FROM `db_unit` $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `unitid` DESC" . $pages->limitsql;
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
  <h4>计量单位</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>计量单位：</th>
        <td><input type="text" name="unit_name" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='unitae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	   while($row_id = $result_id->fetch_assoc()){
		  $array_unitid .= $row_id['unitid'].',';
	  }
	  $array_unitid = rtrim($array_unitid,',');
	  $sql_material_inout = "SELECT `unitid` FROM `db_material_order_list` WHERE `unitid` IN ($array_unitid) GROUP BY `unitid`";
	  $result_material_inout = $db->query($sql_material_inout);
	  if($result_material_inout->num_rows){
		  while($row_material_inout = $result_material_inout->fetch_assoc()){
			  $array_material_inout[] .= $row_material_inout['unitid'];
		  }
	  }else{
		  $array_material_inout = array();
	  }
  ?>
  <form action="unitdo.php" name="unit_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th>计量单位</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $unitid = $row['unitid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $unitid; ?>"<?php if(in_array($unitid,$array_material_inout)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['unit_name']; ?></td>
        <td width="4%"><a href="unitae.php?id=<?php echo $unitid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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