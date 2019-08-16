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
	//查找所有的小计单元格
	var num = $('.count').size();
	var arr = new Array();
	for(var i=0;i<num;i++){
		var name = $('.count').eq(i).attr('class');
		arr[i] = name;
	
	}
	 
        var obj = {}; 
        for(var i= 0, l = arr.length; i< l; i++){ 
            var item = arr[i]; 
            obj[item] = (obj[item] +1 ) || 1;

        }
        console.log(obj);
  	for(var j=0;j<obj.length;j++){
  		console.log(obj[j]);
  	}
    
	$('#plan_date').val();
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
<div id="table_search">
  <h4>付款计划</h4>
  <form action=""  method="get">
   <?php
  $sql_plan = "SELECT * FROM `db_material_funds_plan` INNER JOIN `db_employee` ON `db_material_funds_plan`.`employeeid` = `db_employee`.`employeeid` WHERE `planid`= $planid";
  $result_plan = $db->query($sql_plan);
  if($result_plan->num_rows){
	  $array_plan = $result_plan->fetch_assoc();
	  $plan_date = $array_plan['plan_date'];
  ?>
    <table>
       <tr>
      <th width="10%">付款单号：</th>
      <td width="15%"><?php echo $array_plan['plan_number']; ?></td>
      <th width="10%">计划日期：</th>
      <td width="15%"><?php echo $array_plan['plan_date']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_plan['employee_name']; ?></td>
    </tr>
    </table>
   <?php }else{
	  die("<p class=\"tag\">系统提示：暂无付款计划！</p></div>"); ?>
	  <?php } ?>
  </form>
</div>


<div id="table_list">
<?php
	//查找当前计划单下面的所有计划内容
	//$plan_list_sql = "SELECT `db_material_order`.`supplierid`,`db_funds_plan_list`.`listid`,`db_funds_plan_list`.`order_amount`,`db_funds_plan_list`.`process_cost`,`db_funds_plan_list`.`accountid`,`db_funds_plan_list`.`cancel_amount`,`db_funds_plan_list`.`cut_payment`,`db_funds_plan_list`.`plan_amount`,`db_supplier`.`supplier_cname`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_material_order`.`supplierid` FROM `db_funds_plan_list` INNER JOIN `db_material_order` ON `db_funds_plan_list`.`orderid` = `db_material_order`.`orderid` INNER JOIN `db_supplier` ON `db_material_order`.`supplierid` = `db_supplier`.`supplierid` WHERE `planid` = '$planid'  ORDER BY `db_material_order`.`supplierid`";
	$plan_list_sql = "SELECT `db_account_order_list`.`accountid`,`db_material_account`.`account_number`,`db_supplier`.`supplier_cname`,`db_material_account`.`account_time`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_cut_payment`) AS `total_amount`,`db_material_account`.`tot_plan_amount` FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid` INNER JOIN `db_material_account` ON `db_account_order_list`.`accountid` = `db_material_account`.`accountid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_account`.`supplierid` WHERE `db_funds_plan_list`.`planid` = '$planid' GROUP BY `db_material_account`.`accountid`";
	$result_list = $db->query($plan_list_sql);
	if($result_list->num_rows){
		?>
  	<table>
  	   	<tr>
           <th>ID</th>
           <th>供应商</th>
           <th>对账单号</th>
           <th>对账时间</th>
           <th>发票号</th>
           <th>发票时间</th>
           <th>总金额</th>
           <th>计划金额</th>
           <th>操作</th>
      	</tr>
		<?php
			while($row_list = $result_list->fetch_assoc()){
				//计算总计
				$total_tot_amount += $row_list['total_amount'];
				$total_plan_amount += $row_list['tot_plan_amount'];
				//查找发票信息
				$invoice_sql = "SELECT `invoice_no`,`date` FROM `db_material_invoice_list` WHERE `accountid` =".$row_list['accountid'];
				$result_invoice = $db->query($invoice_sql);
		?>
		<tr>
			<td><input type="checkbox" value="<?php echo $row_list['accountid'] ?>" /></td>
			<td><?php echo $row_list['supplier_cname'] ?></td>
			<td><?php echo $row_list['account_number'] ?></td>
			<td><?php echo $row_list['account_time'] ?></td>
			<td>
				<?php if($result_invoice->num_rows){
					while($row_invoice = $result_invoice->fetch_assoc()){
						echo $row_invoice['invoice_no'].'<br>';
				 	}
				 } ?>
			</td>
			<td>
				<?php
					$result_invoice = $db->query($invoice_sql);
					if($result_invoice->num_rows){
					while($row_invoice = $result_invoice->fetch_assoc()){
						echo $row_invoice['date'].'<br>';
				 	}
				 } ?>
			</td>
			<td><?php echo $row_list['total_amount'] ?></td>
			<td><?php echo $row_list['tot_plan_amount'] ?></td>
      		<td>
      			<a href="funds_plan_order_info.php?action=del&planid=<?php echo $planid ?>&accountid=<?php echo $row_list['accountid'] ?>">详情</a>
      		</td>
    	</tr>	
			
	
<?php
		}
	
?>	
	<tr>
		<td colspan="6">总计</td>
		<td><?php echo number_format($total_tot_amount,2,'.','') ?></td>
		<td><?php echo number_format($total_plan_amount,2,'.','') ?></td>
		
		<td></td>
	</tr>
	<tr>
		<td colspan="12">
			<input type="button" class="button" value="确认" onclick="window.location.href='material_funds_plan.php'" />
		</td>
	</tr>
	</table>

<?php
 }else{
	  echo "<p class=\"tag\">系统提示：暂无付款计划！</p></div>";
  }

?>
</div>
 <div id="table_search">
  <h4>应付账款</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
       <!--  <th>供应商名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>对账日期：</th>
        <td><input type="text" name="specification" class="input_txt" /></td> -->
        <th>供应商：</th>
        <td><select name="data_source" id="data_source" class="input_txt txt">
            <!-- <option value="A"<?php if($data_source == 'A') echo " selected=\"selected\""; ?>>我的应付账款</option> -->
            <option value="B"<?php if($data_source == 'B') echo " selected=\"selected\""; ?>>应付账款</option>
            <option value="C"<?php if($data_source == 'C') echo " selected=\"selected\""; ?>>预付帐款</option>
          </select></td>
        <td><input type="submit" name="submit" id="submit" value="查询" class="button" />
          <input type="hidden" name="id" value="<?php echo $planid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
//$data_source = $_GET['data_source']?trim($_GET['data_source']):'B';


  $order_list_sql = "SELECT `db_material_account`.`tot_plan_amount`,`db_material_account`.`account_type`,`db_material_account`.`supplierid`,`db_material_account`.`orderidlist`,`db_material_account`.`accountid`,`db_material_account`.`account_number`,`db_material_account`.`orderidlist`,`db_material_account`.`account_time`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_cancel_amount`) AS `total_amount`  FROM `db_material_account`INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_account`.`employeeid` WHERE `db_material_account`.`status` = 'P' AND `db_material_account`.`tot_plan_amount` < (`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_cut_payment`) GROUP BY `db_material_account`.`accountid`";

  $result = $db->query($order_list_sql);
  $pages = new page($result->num_rows,10);
  $sqllist = $order_list_sql . " ORDER BY `db_material_account`.`accountid` DESC" . $pages->limitsql;
  $result_order_list = $db->query($sqllist);
  ?>
    <div id="table_list">
   	  <form action="funds_plando.php" method="post">
   <?php 
  if($result_order_list->num_rows){ ?>
        <table>
          <tr>
            <th>ID</th>
            <th>供应商</th>
            <th>对账单号</th>
            <th>对账时间</th>
            <th>发票号</th>
            <th>发票时间</th>
            <th>总金额</th>
            <th>计划金额</th>
            <th>应付余额</th>
            <th>操作</th>
          </tr>
  <?php
    while($row_order_list = $result_order_list->fetch_assoc()){
      $accountid = $row_order_list['accountid'];
      //查找发票号
      $invoice_sql = "SELECT `invoice_no`,`date` FROM `db_material_invoice_list` WHERE `accountid` = '$accountid'";
      $result_invoice = $db->query($invoice_sql);
  ?>
      <tr>
        <td>
          <input type="checkbox" value="<?php echo $row_order_list['accountid'] ?>" name="accountid[]" />
        </td>
        <td>
          <?php echo $row_order_list['supplier_cname'] ?>
        </td>
        <td>
          <?php echo $row_order_list['account_number'] ?>
            
        </td>
        <td>
          <?php echo $row_order_list['account_time'] ?>
            
        </td>
        <td>
         <?php 
            if($result_invoice->num_rows){
              while($row_invoice = $result_invoice->fetch_assoc()){
                echo $row_invoice['invoice_no'].'<br>';
            }
          }
          ?>
        </td>
        <td>
          <?php
            $result_invoice = $db->query($invoice_sql);
            if($result_invoice->num_rows){
              while($row_invoice = $result_invoice->fetch_assoc()){
                echo $row_invoice['date'].'<br>';
              }
            }
          ?>
        </td>
        <td>
        	<?php echo $row_order_list['total_amount'] ?>
        </td>
        <td>
        	<?php echo $row_order_list['tot_plan_amount'] ?>
        </td>
        <td>
        	<?php echo $row_order_list['total_amount'] - $row_order_list['tot_plan_amount'] ?>
        </td>
        <td>
        	<a href="funds_plan_order_info.php?action=add&planid=<?php echo $planid ?>&accountid=<?php echo $row_order_list['accountid'] ?>">排款</a>
        </td>
      </tr>
      <?php } ?>
       <!--  <tr>
        <td colspan="15">
          <input type="submit" name="submit" value="添加" class="button" />
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="data_source" value="B">
          <input type="hidden" value="<?php echo $array_plan['planid'] ?>" name="planid"/>
          <input type="button" name="button" value="返回" class="button" onclick="window.location.href = 'material_funds_plan.php'" />

        </td>
      </tr> -->
    </table>
    </form>
    </div>
   <div id="page">
    <?php $pages->getPage();?>
   </div>
    <?php
    }else{
    echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
    echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'material_funds_plan.php\'" /></p>';
  }
  ?>
 


</table>
</div>
<?php
		//$sql = "SELECT `db_material_order`.`orderid`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_material_order`.`employeeid`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,`db_material_order`.`order_amount` AS `sum`,`db_material_order`.`prepayment`,`db_material_order`.`supplierid` FROM `db_material_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_order`.`employeeid` INNER JOIN `db_material_order_list` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` WHERE `db_material_order`.`pay_type` = 'P' AND `db_material_order`.`order_status` = '1' AND `db_material_order`.`order_amount` > `db_material_order`.`prepayment` GROUP BY `db_material_order`.`orderid`";
		 $sql = "SELECT `db_material_account`.`tot_plan_amount`,`db_material_account`.`account_type`,`db_material_account`.`supplierid`,`db_material_account`.`orderidlist`,`db_material_account`.`accountid`,`db_material_account`.`account_number`,`db_material_account`.`orderidlist`,`db_material_account`.`account_time`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_cancel_amount`) AS `total_amount`  FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_account`.`employeeid` WHERE `db_material_account`.`status` = 'Y' AND `db_material_account`.`tot_plan_amount` < (`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_cut_payment`) GROUP BY `db_material_account`.`accountid`";

$result = $db->query($sql);
$pages = new page($result->num_rows,10);
$sqllist = $sql."ORDER BY `db_material_account`.`account_time` DESC".$pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>预付账款</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
       <!--  <th>供应商名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>对账日期：</th>
        <td><input type="text" name="specification" class="input_txt" /></td> -->
        <th>供应商：</th>
        <td><select name="data_source" id="data_source" class="input_txt txt">
            <!-- <option value="A"<?php if($data_source == 'A') echo " selected=\"selected\""; ?>>我的应付账款</option> -->
            <option value="B"<?php if($data_source == 'B') echo " selected=\"selected\""; ?>>应付账款</option>
            <option value="C"<?php if($data_source == 'C') echo " selected=\"selected\""; ?>>预付帐款</option>
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
     <tr>
     	<th>ID</th>
        <th>供应商</th>
        <th>对账单号</th>
        <th>对账时间</th>
        <th>发票号</th>
        <th>发票时间</th>
        <th>总金额</th>
        <th>计划金额</th>
        <th>未排余额</th>
        <th>操作</th>
    </tr>
    <?php
  while($row = $result->fetch_assoc()){
  ?>
  <form action="funds_plando.php" id="account" method="post">
  <tr>
        <td>
          <input type="checkbox" value="<?php echo $row_order_list['accountid'] ?>" name="accountid[]" />
        </td>
        <td>
          <?php echo $row['supplier_cname'] ?>
        </td>
        <td></td>
        <td>
          <?php echo $row['account_time'] ?>   
        </td>
        <td></td>
        <td></td>
        <td>
        	<?php echo $row['total_amount'] ?>
        </td>
        <td>
        	<?php echo $row['tot_plan_amount'] ?>
        </td>
        <td>
        	<?php echo number_format(($row['total_amount'] - $row['tot_plan_amount']),2,'.','') ?>
        </td>
        <td>
        	<a href="funds_plan_order_info.php?action=add&planid=<?php echo $planid ?>&accountid=<?php echo $row['accountid'] ?>">排款</a>
        </td>
      </tr>
      <?php } ?>
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