<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$planid = fun_check_int($_GET['id']);
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
	/*
	$("input[name^=order_quantity]").blur(function(){
		var order_quantity = $(this).val();
		if(!rf_b.test(order_quantity)){
			alert('请输入大于零的数字');
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2));
			var array_id = $(this).attr('id').split('-');
			var materialid = array_id[1];
			$.post('../ajax_function/material_order_quantity.php',{
			materialid:materialid
			},function(data,textstatus){
				var unit_price = $("#unit_price-"+materialid).val();
				var actual_quantity = order_quantity*data;
				$("#actual_quantity-"+materialid).val(parseFloat(actual_quantity).toFixed(2));
				var amount = actual_quantity*unit_price;
				$("#amount-"+materialid).val(amount.toFixed(2));
			})
		}
	})
	*/
	//失去焦点
	$("input[name^=actual_quantity]").blur(function(){
		var actual_quantity = $(this).val();
		if(!rf_a.test(actual_quantity) && $.trim(actual_quantity)){
			alert('请输入数字');
			$(this).val(this.defaultValue);
		}else{
			if($.trim(actual_quantity)){
				$(this).val(parseFloat($(this).val()).toFixed(2));
				var array_id = $(this).attr('id').split('-');
				var materialid = array_id[1];
				var unit_price = $("#unit_price-"+materialid).val();
				var amount = actual_quantity*unit_price;
				$("#amount-"+materialid).val(amount.toFixed(2));
			}
		}
	})
	//获取焦点
	$("input[name^=actual_quantity]").focus(function(){
		var array_id = $(this).attr('id').split('-');
		var materialid = array_id[1];
		$.post('../ajax_function/material_order_quantity.php',{
			materialid:materialid
		},function(data,textstatus){
			var array_data = data.split('#');
			var actual_quantity = array_data[0];	
			var unitid = array_data[1];
			$("#actual_quantity-"+materialid).val(actual_quantity);
			$("#actual_unitid-"+materialid).find("option[value="+unitid+"]").attr("selected",true);
			var unit_price = $("#unit_price-"+materialid).val();
			var amount = actual_quantity*unit_price;
			$("#amount-"+materialid).val(amount.toFixed(2));
		})
	})								 
	$("input[name^=unit_price]").blur(function(){
		var unit_price = $(this).val();
		if($.trim(unit_price) && !rf_b.test(unit_price)){
			alert('请输入大于零的数字')
			$(this).val(this.defaultValue);
		}else{
			if($.trim(unit_price)){
				$(this).val(parseFloat($(this).val()).toFixed(2));
				var array_id = $(this).attr('id').split('-');
				var materialid = array_id[1];
				var actual_quantity = $("#actual_quantity-"+materialid).val();
				var amount = actual_quantity*unit_price;
				$("#amount-"+materialid).val(amount.toFixed(2));
			}
		}
	})
	$("input[name^=process_cost]").blur(function(){
		var process_cost = $(this).val();
		if($.trim(process_cost) && !rf_a.test(process_cost)){
			alert('请输入数字')
			$(this).val(this.defaultValue);
		}else{
			if($.trim(process_cost)){
				$(this).val(parseFloat($(this).val()).toFixed(2))
			}
		}
	})
	/*
	$("input[name^=amount]").blur(function(){
		var amount = $(this).val();
		if(!rf_a.test(amount)){
			alert('请输入数字')
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2))
		}
	})
	*/
	$("#data_source").change(function(){
		$("#submit").click();
	})
})
</script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  $sql_plan = "SELECT * FROM `db_material_funds_plan` INNER JOIN `db_employee` ON `db_material_funds_plan`.`employeeid` = `db_employee`.`employeeid` WHERE `planid`= $planid";
  $result_plan = $db->query($sql_plan);
  if($result_plan->num_rows){
	  $array_plan = $result_plan->fetch_assoc();
	  $plan_date = $array_plan['plan_date'];
  ?>
  <h4>付款计划</h4>
  <table>
    <tr>
      <th width="10%">付款单号：</th>
      <td width="15%"><?php echo $array_plan['plan_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo $array_plan['plan_date']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_plan['employee_name']; ?></td>
    </tr>
  </table>
  <?php
  }else{
	  die("<p class=\"tag\">系统提示：暂无付款计划！</p></div>");
  }
  ?>
</div>
<?php
$data_source = $_GET['data_source']?trim($_GET['data_source']):'A';
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['specification']);
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
}
if($data_source == 'A'){
	$sql = "SELECT `db_material_account`.`apply_amount`,`db_material_invoice_list`.`date`,`db_material_account`.`accountid`,`db_material_account`.`account_time`,`db_material_account`.`amount`,`db_supplier`.`supplier_cname` FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` INNER JOIN `db_material_inout` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` INNER JOIN `db_material_invoice_list` ON `db_material_invoice_list`.`accountid` = `db_material_account`.`accountid` WHERE `db_material_account`.`employeeid`='$employeeid' AND `db_material_inout`.`account_status` = 'M' AND (`db_material_account`.`account_time` BETWEEN '$sdate' AND '$edate')".$sqlwhere."GROUP BY `db_material_account`.`accountid`";
}elseif($data_source == 'B'){
		$sql = "SELECT `db_material_account`.`apply_amount`,`db_material_invoice_list`.`date`,`db_material_account`.`accountid`,`db_material_account`.`account_time`,`db_material_account`.`amount`,`db_supplier`.`supplier_cname` FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` INNER JOIN `db_material_inout` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` INNER JOIN `db_material_invoice_list` ON `db_material_invoice_list`.`accountid` = `db_material_account`.`accountid` WHERE `db_material_inout`.`account_status` = 'M' AND (`db_material_account`.`account_time` BETWEEN '$sdate' AND '$edate')".$sqlwhere."GROUP BY `db_material_account`.`accountid`";
}elseif($data_source == 'C'){
		$sql = "SELECT `db_material_account`.`apply_amount`,`db_material_invoice_list`.`date`,`db_material_account`.`accountid`,`db_material_account`.`account_time`,`db_material_account`.`amount`,`db_supplier`.`supplier_cname` FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` INNER JOIN `db_material_inout` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` INNER JOIN `db_material_invoice_list` ON `db_material_invoice_list`.`accountid` = `db_material_account`.`accountid` WHERE `db_material_inout`.`account_status` = 'M' AND (`db_material_account`.`account_time` BETWEEN '$sdate' AND '$edate')".$sqlwhere."GROUP BY `db_material_account`.`accountid`";
}
$result = $db->query($sql);
$pages = new page($result->num_rows,10);
$sqllist = $sql . " ORDER BY `db_material_account`.`account_time` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>可下订单物料</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>物料来源：</th>
        <td><select name="data_source" id="data_source">
            <option value="A"<?php if($data_source == 'A') echo " selected=\"selected\""; ?>>我的应付账款</option>
            <option value="B"<?php if($data_source == 'B') echo " selected=\"selected\""; ?>>所有应付账款</option>
            <option value="C"<?php if($data_source == 'C') echo " selected=\"selected\""; ?>>预付款</option>
          </select></td>
        <td><input type="submit" name="submit" id="submit" value="查询" class="button" />
          <input type="button" name="button" value="明细" class="button" onclick="location.href='material_order_list.php?id=<?php echo $orderid; ?>'" />
          <input type="hidden" name="id" value="<?php echo $orderid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="material_order_list_adddo.php" name="material_list" method="post">
    <table>
      <tr>
      	<th>ID</th>
        <th>对账时间</th>
        <th>发票时间</th>
        <th>供应商名称</th>
        <th>总金额</th>
        <th width="20%">发票号</th>
      </tr>

      <?php
      while($row = $result->fetch_assoc()){
      	//查询对应的发票号
      	$invoice_sql = "SELECT `invoice_no` FROM `db_material_invoice_list` WHERE `accountid`=".$row['accountid'];
      	$result_invoice = $db->query($invoice_sql);
	  ?>
      <tr>
        <td>
        	<input type="checkbox" name="accountid" value="<?php echo $row['accountid'] ?>">
        </td>
        <td><?php echo $row['account_time'] ?></td>
        <td><?php echo $row['date'] ?></td>
        <td><?php echo $row['supplier_cname'] ?></td>
        <td><?php echo $row['amount'] ?></td>
        <td>
        	<?php
        		if($result_invoice->num_rows){
        			while($row_invoice = $result_invoice->fetch_assoc()){
        				echo ' PO:'.$row_invoice['invoice_no'];
        			}
        		}
        	?>
        </td>
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
	  echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>