<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['orderid']);
$employeeid = $_SESSION['employee_info']['employeeid'];
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
<script language="javascript" type="text/javascript">
$(function(){							 
	$("input[name^=unit_price]").blur(function(){
		var unit_price = $(this).val();
		var array_id = $(this).attr('id').split('-');
		var purchase_listid = array_id[1];
		if($.trim(unit_price) && !rf_b.test(unit_price)){
			alert('请输入大于零的数字')
			$(this).val('');
			$("#amount-"+purchase_listid).val('');
		}else{
			if($.trim(unit_price)){
				$(this).val(parseFloat($(this).val()).toFixed(2));
				var quantity = $("#quantity-"+purchase_listid).val();
				var amount = quantity*unit_price;
				$("#amount-"+purchase_listid).val(amount.toFixed(2));
			}
		}
	})
	$("#data_source").change(function(){
		$("#submit").click();
	})
})
</script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
$sql_order = "SELECT `db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,DATE_ADD(`db_cutter_order`.`order_date`,interval +`db_cutter_order`.`delivery_cycle` day) AS `plan_date`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_cutter_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_order`.`employeeid` WHERE `db_cutter_order`.`orderid` = '$orderid' AND `db_cutter_order`.`employeeid` = '$employeeid'";
$result_order = $db->query($sql_order);
if($result_order->num_rows){
	$array_order = $result_order->fetch_assoc();
	$plan_date = $array_order['plan_date'];
	$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
	$result_cutter_type = $db->query($sql_cutter_type);
?>
<div id="table_sheet">
  <h4>刀具订单</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="15%"><?php echo $array_order['order_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo $array_order['order_date']; ?></td>
      <th width="10%">供应商：</th>
      <td width="15%"><?php echo $array_order['supplier_cname']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_order['employee_name']; ?></td>
    </tr>
  </table>
</div>
<?php
$data_source = $_GET['data_source']?trim($_GET['data_source']):'A';
if($_GET['submit']){
	$purchase_number = trim($_GET['purchase_number']);
	$specification = trim($_GET['specification']);
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
	}
	$sqlwhere = " AND `db_cutter_purchase`.`purchase_number` LIKE '%$purchase_number%' AND `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid";
}
if($data_source == 'A'){
	$sql = "SELECT `db_cutter_purchase_list`.`purchase_listid`,`db_cutter_purchase_list`.`quantity`,`db_cutter_purchase_list`.`plan_date`,`db_cutter_purchase_list`.`remark`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_cutter_purchase`.`purchase_number` FROM `db_cutter_inquiry` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_inquiry`.`purchase_listid` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` WHERE `db_cutter_purchase_list`.`purchase_listid` NOT IN (SELECT `purchase_listid` FROM `db_cutter_order_list` GROUP BY `purchase_listid`) AND `db_cutter_inquiry`.`employeeid` = '$employeeid' $sqlwhere";
}elseif($data_source == 'B'){
	$sql = "SELECT `db_cutter_purchase_list`.`purchase_listid`,`db_cutter_purchase_list`.`quantity`,`db_cutter_purchase_list`.`plan_date`,`db_cutter_purchase_list`.`remark`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_cutter_purchase`.`purchase_number` FROM `db_cutter_inquiry` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_inquiry`.`purchase_listid` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` WHERE `db_cutter_purchase_list`.`purchase_listid` NOT IN (SELECT `purchase_listid` FROM `db_cutter_order_list` GROUP BY `purchase_listid`) $sqlwhere";
}elseif($data_source == 'C'){
	$sql = "SELECT `db_cutter_purchase_list`.`purchase_listid`,`db_cutter_purchase_list`.`quantity`,`db_cutter_purchase_list`.`plan_date`,`db_cutter_purchase_list`.`remark`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_cutter_purchase`.`purchase_number` FROM `db_cutter_purchase_list` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` WHERE `db_cutter_purchase_list`.`purchase_listid` NOT IN (SELECT `purchase_listid` FROM `db_cutter_order_list` GROUP BY `purchase_listid`) $sqlwhere";
}
$result = $db->query($sql);
$pages = new page($result->num_rows,10);
$sqllist = $sql . " ORDER BY `db_cutter_purchase`.`purchaseid` DESC,`db_cutter_purchase_list`.`purchase_listid` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>可下订单刀具</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>申购单号：</th>
        <td><input type="text" name="purchase_number" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="type" class="input_txt" /></td>
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
        <th>刀具来源：</th>
        <td><select name="data_source" id="data_source">
            <option value="A"<?php if($data_source == 'A') echo " selected=\"selected\""; ?>>我的询价单</option>
            <option value="B"<?php if($data_source == 'B') echo " selected=\"selected\""; ?>>所有询价单</option>
            <option value="C"<?php if($data_source == 'C') echo " selected=\"selected\""; ?>>未下订单</option>
          </select></td>
        <td><input type="submit" name="submit" id="submit" value="查询" class="button" />
          <input type="button" name="button" value="明细" class="button" onclick="location.href='cutter_order_list.php?orderid=<?php echo $orderid; ?>'" />
          <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="cutter_order_list_adddo.php" name="cutter_order_list_add" method="post">
    <table>
      <tr>
        <th width="7%">申购单号</th>
        <th width="6%">类型</th>
        <th width="10%">规格</th>
        <th width="6%">材质</th>
        <th width="8%">硬度</th>
        <th width="6%">品牌</th>
        <th width="6%">数量</th>
        <th width="4%">单位</th>
        <th width="6%">要求回厂日期</th>
        <th width="6%">单价(含税)</th>
        <th width="5%">税率</th>
        <th width="8%">金额(含税)</th>
        <th width="4%">现金</th>
        <th width="8%">计划回厂日期</th>
        <th width="10%">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $purchase_listid = $row['purchase_listid'];
	  ?>
      <tr>
        <td><?php echo $row['purchase_number']; ?></td>
        <td><?php echo $row['type']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
        <td><?php echo $row['hardness']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><input type="text" name="quantity[]" id="quantity-<?php echo $purchase_listid; ?>" value="<?php echo $row['quantity']; ?>" class="input_txt" size="6" readonly="readonly" /></td>
        <td>件</td>
        <td><?php echo $row['plan_date']; ?></td>
        <td><input type="text" name="unit_price[]" id="unit_price-<?php echo $purchase_listid; ?>" class="input_txt" size="6" /></td>
        <td><select name="tax_rate[]" id="tax_rate-<?php echo $purchase_listid; ?>">
            <?php
			foreach($array_tax_rate as $tax_rate){
				echo "<option value=\"".$tax_rate."\">".($tax_rate*100)."%</option>";
			}
			?>
          </select></td>
        <td><input type="text" name="amount[]" id="amount-<?php echo $purchase_listid; ?>" class="input_txt" size="10" /></td>
        <td><select name="iscash[]">
            <?php foreach($array_is_status as $is_status_key=>$is_status_value){ ?>
            <option value="<?php echo $is_status_key; ?>"<?php if($is_status_key == 0) echo " selected=\"selected\""; ?>><?php echo $is_status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="text" name="plan_date[]" value="<?php echo $plan_date; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" /></td>
        <td><input type="text" name="remark[]" value="<?php echo $row['remark']; ?>" class="input_txt" size="16" />
          <input type="hidden" name="purchase_listid[]" value="<?php echo $purchase_listid; ?>" /></td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="15"><input type="submit" name="submit" value="添加" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" /></td>
      </tr>
    </table>
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
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>