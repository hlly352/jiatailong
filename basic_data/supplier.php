<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
//读取供应商类型
$sql_supplier_type = "SELECT * FROM `db_supplier_type` ORDER BY `supplier_typeid`";
$result_supplier_type = $db->query($sql_supplier_type);
//读取供应商业务类型
$sql_supplier_business_type = "SELECT * FROM `db_supplier_business_type` ORDER BY `business_typeid` ASC";
$result_supplier_business_type = $db->query($sql_supplier_business_type);
if($_GET['submit']){
	$supplier_name = trim($_GET['supplier_name']);
	$supplier_typeid = $_GET['supplier_typeid'];
	if($supplier_typeid){
		$sql_suppliertype = " AND FIND_IN_SET($supplier_typeid,`supplier_typeid`) > 0";
	}
	$business_typeid = $_GET['business_typeid'];
	if($business_typeid){
		$sql_businesstype = " AND FIND_IN_SET($business_typeid,`business_typeid`) > 0";
	}
	$sqlwhere = " WHERE (`supplier_cname` LIKE '%$supplier_name%' OR `supplier_ename` LIKE '%$supplier_name%') $sql_suppliertype $sql_businesstype";
}
$sql = "SELECT * FROM `db_supplier` $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `supplier_ename` ASC,`supplierid` ASC" . $pages->limitsql;
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
  <h4>供应商</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>供应商：</th>
        <td><input type="text" name="supplier_name" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="supplier_typeid">
            <option value="">所有</option>
            <?php
			if($result_supplier_type->num_rows){
				while($row_supplier_type = $result_supplier_type->fetch_assoc()){
					echo "<option value=\"".$row_supplier_type['supplier_typeid']."\">".$row_supplier_type['supplier_typename']."</option>";
				}
			}
			?>
          </select></td>
        <th>业务类型：</th>
        <td><select name="business_typeid">
            <option value="">所有</option>
            <?php
			if($result_supplier_business_type->num_rows){
				while($row_supplier_business_type = $result_supplier_business_type->fetch_assoc()){
					echo "<option value=\"".$row_supplier_business_type['business_typeid']."\">".$row_supplier_business_type['business_typename']."</option>";
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='supplierae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_supplierid .= $row_id['supplierid'].',';
	  }
	  $array_supplierid = rtrim($array_supplierid,',');
	  $sql_mould_outward = "SELECT `supplierid` FROM `db_mould_outward` WHERE `supplierid` IN ($array_supplierid) GROUP BY `supplierid`";
	  $result_mould_outward = $db->query($sql_mould_outward);
	  if($result_mould_outward->num_rows){
		  while($row_mould_outward = $result_mould_outward->fetch_assoc()){
			  $array_mould_outward[] = $row_mould_outward['supplierid'];
		  }
	  }else{
		  $array_mould_outward = array();
	  }
	  //print_r($array_mould_outward);
	  $sql_mould_weld = "SELECT `supplierid` FROM `db_mould_weld` WHERE `supplierid` IN ($array_supplierid) GROUP BY `supplierid`";
	  $result_mould_weld = $db->query($sql_mould_weld);
	  if($result_mould_weld->num_rows){
		  while($row_mould_weld = $result_mould_weld->fetch_assoc()){
			  $array_mould_weld[] = $row_mould_weld['supplierid'];
		  }
	  }else{
		  $array_mould_weld = array();
	  }
	  //print_r($array_mould_weld);
	  $sql_mould_try = "SELECT `supplierid` FROM `db_mould_try` WHERE `supplierid` IN ($array_supplierid) GROUP BY `supplierid`";
	  $result_mould_try = $db->query($sql_mould_try);
	  if($result_mould_try->num_rows){
		  while($row_mould_try = $result_mould_try->fetch_assoc()){
			  $array_mould_try[] = $row_mould_try['supplierid'];
		  }
	  }else{
		  $array_mould_try = array();
	  }
	  //print_r($array_mould_try);
	  $sql_material_order = "SELECT `supplierid` FROM `db_material_order` WHERE `supplierid` IN ($array_supplierid) GROUP BY `supplierid`";
	  $result_material_order = $db->query($sql_material_order);
	  if($result_material_order->num_rows){
		  while($row_material_order = $result_material_order->fetch_assoc()){
			  $array_material_order[] = $row_material_order['supplierid'];
		  }
	  }else{
		  $array_material_order = array();
	  }
	  //print_r($array_material_order);
	  $array_supplier = array_unique(array_merge($array_mould_outward,$array_mould_weld,$array_mould_try,$array_material_order));
	  //print_r($array_supplier);
  ?>
  <form action="supplierdo.php" name="supplier_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">中文名</th>
        <th width="8%">英文名</th>
        <th width="20%">全称</th>
        <th width="10%">类型</th>
        <th width="15%">业务类型</th>
        <th width="25%">地址</th>
        <th width="4%">状态</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $supplierid = $row['supplierid'];
		  $supplier_typeid = $row['supplier_typeid'];
		  $business_typeid = $row['business_typeid'];
		  //类型
		  $sql_supplier_typename = "SELECT `supplier_typename` FROM `db_supplier_type` WHERE `supplier_typeid` IN ($supplier_typeid)";
		  $result_supplier_typename = $db->query($sql_supplier_typename);
		  $supplier_typename = '';
		  if($result_supplier_typename->num_rows){
			  while($row_supplier_typename = $result_supplier_typename->fetch_assoc()){
				  $supplier_typename .= $row_supplier_typename['supplier_typename'].',';
			  }
			  $supplier_typename = rtrim($supplier_typename,',');
		  }
		  //业务类型
		  $sql_business_typename = "SELECT `business_typename` FROM `db_supplier_business_type` WHERE `business_typeid` IN ($business_typeid)";
		  $result_business_typename = $db->query($sql_business_typename);
		   $business_typename = '';
		  if($result_business_typename->num_rows){
			  $business_typename = '';
			  while($row_business_typename = $result_business_typename->fetch_assoc()){
				  $business_typename .= $row_business_typename['business_typename'].',';
			  }
			  $business_typename = rtrim($business_typename,',');
		  }
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $supplierid; ?>"<?php if(in_array($supplierid,$array_supplier)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['supplier_code'].'-'.$row['supplier_cname']; ?></td>
        <td><?php echo $row['supplier_ename']; ?></td>
        <td><?php echo $row['supplier_name']; ?></td>
        <td><?php echo $supplier_typename; ?></td>
        <td><?php echo $business_typename; ?></td>
        <td><?php echo $row['supplier_address']; ?></td>
        <td><?php echo $array_status[$row['supplier_status']]; ?></td>
        <td width="4%"><a href="supplierae.php?id=<?php echo $supplierid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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