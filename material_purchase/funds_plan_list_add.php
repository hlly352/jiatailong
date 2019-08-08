<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$planid = $_GET['id'];
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

	//失去焦点
	$("input[name^=plan_amount]").blur(function(){
		
		var plan_amount = $(this).val();
		if(!rf_a.test(plan_amount) && $.trim(plan_amount)){
			alert('请输入数字');
			$(this).val(this.defaultValue);
		}else{
			if($.trim(plan_amount)){
				$(this).val(parseFloat($(this).val()).toFixed(2));
				var id = $(this).attr('id');
				var accountid = id.substr(id.indexOf('-')+1);
				// var array_accountid = $(this).attr('id').split('-');
				// var accountid = array_accountid[1];
				//获取对应的对账金额
				// var amount = $("#amount-"+accountid).html();
				// if(amount < $.trim(plan_amount)){
				// 	alert('计划金额不能大于对账金额');
				// 	return false;
				// }
				
			}
		}
	})
	$("input[name^=plan_amount]").one('blur',function(){
		var plan_amount = $(this).val();
		if(!$.trim(plan_amount)){
			var id = $(this).attr('id');
		 	var accountid = id.substr(id.indexOf('-')+1);
		 	$(this).val($.trim($('#amount-'+accountid).html()));
		}
	})
		//点击添加按钮
	$('input[name=submit]').live('click',function(){
		var num = $('input[name ^= plan_amount]').size();
		for(var i=0;i<num;i++){
			var plan_amount = $.trim($('input[name ^= plan_amount]').eq(i).val());
			var amount = parseFloat($('.amount').eq(i).html());
			if(plan_amount>amount){
				alert('计划金额不能大于对账金额');
				return false;
			}
		}
	})
})
</script>
<title>采购管理-嘉泰隆</title>
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
      <th width="10%">计划日期：</th>
      <td width="15%"><input type="text" value="<?php echo $array_plan['plan_date']; ?>" name="plan_date" form="account" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})"></td>
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
<div id="table_list">
<h4 style="height: 30px;line-height: 30px;margin-bottom: 5px;font-size: 14px;
    padding-left: 32px;background:#ddd">计划详情</h4>
<?php
	//查找当前计划单下面的所有计划内容
	$plan_list_sql = "SELECT * FROM `db_funds_plan_list` WHERE `planid` = '$planid'";
	$result_list = $db->query($plan_list_sql);
	if($result_list->num_rows){
		?>
  	<table>
  		<tr>
  			<th>ID</th>
  			<th>对账时间</th>
  			<th>发票时间</th>
  			<th>供应商名称</th>
  			<th>对账金额</th>
  			<th>计划金额</th>
  			<th>操作</th>
  		</tr>
	<?php
		while($row_list = $result_list->fetch_assoc()){
			//判断是对账款还是预付款
			if($row_list['accountid']){
				$info_sql = "SELECT * FROM `db_material_account` INNER JOIN `db_material_invoice_list` ON `db_material_account`.`accountid` = `db_material_invoice_list`.`accountid` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_material_account`.`accountid` =".$row_list['accountid'];
			} else{
				$info_sql = "SELECT * FROM `db_funds_prepayment` INNER JOIN `db_supplier` ON `db_funds_prepayment`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_funds_prepayment`.`prepayid` =".$row_list['preid'];
			}
			$result_info = $db->query($info_sql);
			if($result_info->num_rows){
				$row_info = $result_info->fetch_assoc();
					
		?>
		<tr>
      		<td>
      			<input type="checkbox" value="<?php echo $row_list['listid'] ?>">
      		</td>
      		<td><?php echo $row_info['account_time'] ?></td>
      		<td><?php echo $row_info['date'] ?></td>
      		<td><?php echo $row_info['supplier_cname']?></td>
      		<td>
      			<?php 
      				$account_amount =  $row_info['tot_amount'] + $row_info['tot_process_cost'] - $row_info['tot_cancel_amount'] - $row_info['tot_cut_payment'] - $row_info['tot_prepayment'];
      				echo number_format($account_amount,2,'.','');
      			?>		
      		</td>
      		<td>
      			<?php 
      				$plan_amount = $row_info['prepayment']?$row_info['prepayment']:$row_list['plan_amount'];
      				echo number_format($plan_amount,2,'.','');
      			?>
      		</td>
      		<td>
      			<a href="funds_plando.php?action=del&id=<?php echo $row_list['listid'] ?>">删除</a>
      		</td>
    	</tr>	
			
	
<?php
		}
	}
?>
	</table>

<?php
 }else{
	  echo "<p class=\"tag\">系统提示：暂无付款计划！</p></div>";
  }

?>
</div>
<?php
$data_source = $_GET['data_source']?trim($_GET['data_source']):'B';
// if($_GET['submit']){
// 	$mould_number = trim($_GET['mould_number']);
// 	$material_name = trim($_GET['material_name']);
// 	$specification = trim($_GET['specification']);
// 	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
// }
// if($data_source == 'A'){
// //	$sql = "SELECT `db_material_account`.`apply_amount`,`db_material_invoice_list`.`date`,`db_material_account`.`accountid`,`db_material_account`.`account_time`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_prepayment`) AS `amount`,`db_supplier`.`supplier_cname` FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` INNER JOIN `db_material_inout` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` INNER JOIN `db_material_invoice_list` ON `db_material_invoice_list`.`accountid` = `db_material_account`.`accountid` WHERE `db_material_inout`.`account_status` = 'M' AND `db_material_account`.`status` !='C' AND `db_material_account`.`employeeid` = '$employeeid' AND (`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_prepayment` - `db_material_account`.`apply_amount`)>0 AND (`db_material_account`.`account_time` BETWEEN '$sdate' AND '$edate')".$sqlwhere."GROUP BY `db_material_account`.`accountid`";
// 	$sql = "SELECT `db_material_account`.`accountid`,`db_material_account`.`account_time`,`db_material_invoice_list`.`date`,`db_supplier`.`supplier_cname`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_prepayment`) AS `amount`,`db_material_account`.`apply_amount` FROM `db_material_account` INNER JOIN `db_material_invoice_list` ON `db_material_account`.`accountid` = `db_material_invoice_list`.`accountid` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` WHERE (`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_prepayment`-`db_material_account`.`apply_amount`)>0 AND `db_material_account`.`status` = 'F' AND `db_material_account`.`employeeid` = '$employeeid'";
// }else

if($data_source == 'B'){
	$sql = "SELECT `db_material_account`.`accountid`,`db_material_account`.`account_time`,`db_material_invoice_list`.`date`,`db_supplier`.`supplier_cname`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_prepayment`) AS `amount`,`db_material_account`.`apply_amount` FROM `db_material_account` INNER JOIN `db_material_invoice_list` ON `db_material_account`.`accountid` = `db_material_invoice_list`.`accountid` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` WHERE (`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_prepayment`-`db_material_account`.`apply_amount`)>0 AND `db_material_account`.`status` = 'F'";
}elseif($data_source == 'C'){
		$sql = "SELECT * FROM `db_funds_prepayment` INNER JOIN `db_supplier` ON `db_funds_prepayment`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_employee` ON `db_funds_prepayment`.`employeeid` = `db_employee`.`employeeid` WHERE `db_funds_prepayment`.`status` = '0'";
}

$result = $db->query($sql);
$pages = new page($result->num_rows,10);
if($data_source == 'B'){
$sqllist = $sql . " ORDER BY `db_material_account`.`account_time` DESC" . $pages->limitsql;
}elseif($data_source == 'C'){
	$sqllist = $sql."ORDER BY `db_funds_prepayment`.`dotime` DESC".$pages->limitsql;
}
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>应付账款</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
       <!--  <th>供应商名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>对账日期：</th>
        <td><input type="text" name="specification" class="input_txt" /></td> -->
        <th>应付款来源：</th>
        <td><select name="data_source" id="data_source">
            <!-- <option value="A"<?php if($data_source == 'A') echo " selected=\"selected\""; ?>>我的应付账款</option> -->
            <option value="B"<?php if($data_source == 'B') echo " selected=\"selected\""; ?>>对账应付账款</option>
            <option value="C"<?php if($data_source == 'C') echo " selected=\"selected\""; ?>>预付款</option>
          </select></td>
        <td><input type="submit" name="submit" id="submit" value="查询" class="button" />
          <input type="hidden" name="id" value="<?php echo $planid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="funds_plando.php" name="material_list" id="account" method="post">
    <table>
    <?php
    	if($data_source == 'B'){
    ?>
      <tr>
      	<th>ID</th>
        <th>对账时间</th>
        <th>发票时间</th>
        <th>供应商名称</th>
        <th>对账金额</th>
        <th>剩余金额</th>
        <th width="17%">计划金额</th>
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
        	<?php echo $row['accountid'] ?>
        	<input type="hidden" name="accountid[]" value="<?php echo $row['accountid'] ?>">
        </td>
        <td><?php echo $row['account_time'] ?></td>
        <td><?php echo $row['date'] ?></td>
        <td><?php echo $row['supplier_cname'] ?></td>
        <td >
        	<?php 
        		echo  number_format($row['amount'],2,'.','') 
        	?>	
        </td>
        <td class="amount" id="amount-<?php echo $row['accountid'] ?>">
        	<?php
        		$surplus_amount = $row['amount'] - $row['apply_amount'];
        		echo number_format($surplus_amount,2,'.','');
        	?>
        </td>
        <td>
        	<input type="text" name="plan_amount[]" id="plan_amount-<?php echo $row['accountid'] ?>" class="input_txt">
        </td>
        <td>
        	<?php
        		if($result_invoice->num_rows){
        			while($row_invoice = $result_invoice->fetch_assoc()){
        				echo ' '.$row_invoice['invoice_no'];
        			}
        		}
        	?>
        </td>
      </tr>
      <?php } }elseif($data_source == 'C'){ ?>
     <tr>
      <th width="">ID</th>
      <th width="">添加时间</th>
      <th width="">供应商</th>
      <th width="">合同号</th>
      <th width="">预付金额</th>
      <th width="">操作人</th>
    </tr>
    <?php
  while($row = $result->fetch_assoc()){
  ?>
  <form action="material_balance_account_do.php" id="account" method="post">
    <tr>
      <td>
        <input type="checkbox" name="id[]" value="<?php echo $row['prepayid']?>">
      </td>
      <td><?php echo $row['dotime']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['prepayment']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
    </tr>

      <?php } } ?>
      <tr>
        <td colspan="15"><input type="submit" name="submit" value="添加" class="button" />
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="data_source" value="<?php echo $data_source ?>">
          <input type="hidden" value="<?php echo $array_plan['planid'] ?>" name="planid"/>
          <input type="button" name="button" value="返回" class="button" onclick="window.location.href = 'material_funds_plan.php'" />

        </td>
      </tr>
    </table>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
	  echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'material_funds_plan.php\'" /></p>';
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>