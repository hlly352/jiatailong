<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	$material_number = trim($_GET['material_number']);
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['specification']);
	$complete_status = $_GET['complete_status'];
	if($complete_status != NULL){
		$sql_complete_status = " AND `db_mould_material`.`complete_status` = '$complete_status'";
	}
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_number` LIKE '%$material_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' $sql_complete_status";
}
$sql = "SELECT `db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_material`.`complete_status`,`db_mould`.`mould_number`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code` FROM `db_mould_material` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` WHERE `db_mould_material`.`type` != 'Z' $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['mould_material_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_material`.`material_date` DESC,`db_mould_material`.`materialid` DESC" . $pages->limitsql;
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
<title>模具物料-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具物料</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料编号：</th>
        <td><input type="text" name="material_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>完整：</th>
        <td><select name="complete_status">
        <option value="">所有</option>
        <?php
        foreach($array_is_status as $is_status_key=>$is_status_value){
			echo "<option value=\"".$is_status_key."\">".$is_status_value."</option>";
		}
		?>
        </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
        <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_mould_material_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_materialid .= $row_id['materialid'].',';
	  }
	  $array_materialid = rtrim($array_materialid,',');
	  $sql_order = "SELECT `materialid` FROM `db_material_order_list` WHERE `materialid` IN ($array_materialid) GROUP BY `materialid`";
	  $result_order = $db->query($sql_order);
	  if($result_order->num_rows){
		  while($row_order = $result_order->fetch_assoc()){
			  $array_order[] = $row_order['materialid'];
		  }
	  }else{
		  $array_order = array();
	  }
  ?>
  <form action="mould_materialdo.php" name="mould_material_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">模具编号</th>
        <th width="8%">料单编号</th>
        <th width="4%">料单序号</th>
        <th width="10%">物料编码</th>
        <th width="10%">物料名称</th>
        <th width="12%">规格</th>
        <th width="4%">数量</th>
        <th width="6%">材质</th>
        <th width="10%">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $specification_bg = '';
		  $material_name_bg = '';
		  $materialid = $row['materialid'];
		  $material_number_code = $row['material_number_code'];
		  $specification = $row['specification'];
		  if(in_array($material_number_code,array(1,2,3,4,5))){
			  $tag_a = substr_count($specification,'*');
			  $tag_b = substr_count($specification,'#');
			  $specification_bg = ($tag_a != 2 || $tag_b != 1)?" style=\"background:orange\"":'';
		  }
		  $material_name_bg = $row['complete_status']?'':" style=\"background:yellow\"";
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $materialid; ?>"<?php if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_list_number']; ?></td>
        <td><?php echo $row['material_list_sn']; ?></td>
        <td><?php echo $row['material_number']; ?></td>
        <td<?php echo $material_name_bg; ?>><?php echo $row['material_name']; ?></td>
        <td<?php echo $specification_bg; ?>><?php echo $specification; ?></td>
        <td><?php echo $row['material_quantity']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['remark']; ?></td>
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
	  echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>