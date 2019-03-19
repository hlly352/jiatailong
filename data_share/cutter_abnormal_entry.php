<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
	$order_number = trim($_GET['order_number']);
	$specification = trim($_GET['specification']);
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
	}
	$supplierid = $_GET['supplierid'];
	if($supplierid){
		$sql_supplierid = " AND `db_cutter_order`.`supplierid` = '$supplierid'";
	}
	$sqlwhere = " AND `db_cutter_order`.`order_number` LIKE '%$order_number%' AND `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid $sql_supplierid";
}
$sql = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`form_number`,`db_cutter_order_list`.`unit_price`,`db_cutter_order_list`.`plan_date`,`db_cutter_order`.`order_number`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,(`db_cutter_inout`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount`,DATEDIFF(`db_cutter_order_list`.`plan_date`,`db_cutter_inout`.`dodate`) AS `diff_date` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` WHERE `db_cutter_inout`.`dotype` = 'I' AND (`db_cutter_inout`.`dodate` BETWEEN '$sdate' AND '$edate') AND DATEDIFF(`db_cutter_order_list`.`plan_date`,`db_cutter_inout`.`dodate`) < 0 $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_inout`.`inoutid` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>物控管理-苏州希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>刀具异常入库</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" /></td>
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
        <th>订单日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
			?>
            <option value="<?php echo $row_supplier['supplierid']; ?>"<?php if($row_supplier['supplierid'] == $supplierid) echo " selected=\"selected\""; ?>><?php echo $row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">合同号</th>
      <th width="6%">类型</th>
      <th width="10%">规格</th>
      <th width="6%">材质</th>
      <th width="8%">硬度</th>
      <th width="6%">品牌</th>
      <th width="8%">表单号</th>
      <th width="4%">入库<br />
        数量</th>
      <th width="4%">单位</th>
      <th width="6%">单价<br />
        (含税)</th>
      <th width="6%">金额<br />
        (含税)</th>
      <th width="8%">供应商</th>
      <th width="6%">入库日期</th>
      <th width="6%">计划回厂日期</th>
      <th width="4%">差异</th>
    </tr>
    <?php while($row = $result->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row['inoutid']; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['type']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
      <td><?php echo $row['hardness']; ?></td>
      <td><?php echo $row['brand']; ?></td>
      <td><?php echo $row['form_number']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td>件</td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['dodate']; ?></td>
      <td><?php echo $row['plan_date']; ?></td>
      <td><?php echo $row['diff_date']; ?></td>
    </tr>
    <?php } ?>
  </table>
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