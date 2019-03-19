<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
if($_GET['submit']){
	$specification = trim($_GET['specification']);
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
	}
	$sqlwhere = " WHERE `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid";
}
$sql = "SELECT `db_cutter_specification`.`specificationid`,`db_cutter_specification`.`specification`,`db_cutter_type`.`type` FROM `db_cutter_specification` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_specification`.`typeid` ASC,`db_cutter_specification`.`specificationid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sqllist);
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
  <h4>刀具规格</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="typeid">
            <option value="">所有</option>
            <?php
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_type['typeid']; ?>"<?php if($row_cutter_type['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type['type']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='cutter_specificationae.php?action=add'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_specificationid .= $row_id['specificationid'].',';
	  }
	  $array_specificationid = rtrim($array_specificationid,',');
	  $sql_group = "SELECT `specificationid` FROM `db_mould_cutter` WHERE `specificationid` IN ($array_specificationid) GROUP BY `specificationid`";
	  $result_group = $db->query($sql_group);
	  if($result_group->num_rows){
		  while($row_group = $result_group->fetch_assoc()){
			  $array_group[] = $row_group['specificationid'];
		  }
	  }else{
		  $array_group = array();
	  }
	  //print_r($array_group);
  ?>
  <form action="cutter_specificationdo.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th>规格</th>
        <th width="10%">类型</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $specificationid = $row['specificationid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $specificationid; ?>"<?php if(in_array($specificationid,$array_group)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $row['type']; ?></td>
        <td><a href="cutter_specificationae.php?id=<?php echo $specificationid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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