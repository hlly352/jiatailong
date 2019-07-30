<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
	$supplierid = $_GET['supplierid'];
	if($supplierid){
		$sql_supplierid = " WHERE `db_material_account`.`supplierid` = '$supplierid'";
	}
	$sqlwhere = "$sql_supplierid"."AND (`db_material_account`.`account_time` BETWEEN '$sdate' AND '$edate')";
}
// $sql = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`listid`,`db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`amount`,`db_material_inout`.`process_cost`,`db_material_order_list`.`unit_price`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit_order`.`unit_name` AS `unit_name_order`,`db_unit_actual`.`unit_name` AS `unit_name_actual` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_inout`.`dotype` = 'I' AND (`db_material_inout`.`dodate` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$sql = "SELECT `db_material_account`.`accountid`,`db_material_account`.`account_type`,`db_material_invoice_list`.`amount` AS invoice_amount,`db_material_account`.`amount`,`db_material_account`.`account_time`,`db_supplier`.`supplier_cname`,`db_material_invoice_list`.`invoice_no`,`db_material_invoice_list`.`date`,`db_material_invoice_list`.`status`,`db_material_account`.`supplierid` FROM `db_material_account` LEFT JOIN `db_material_invoice_list` ON `db_material_account`.`accountid` = `db_material_invoice_list`.`accountid` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` ".$sqlwhere;
$result = $db->query($sql);
$result_total = $db->query($sql);
$_SESSION['material_inout_list_in'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_account`.`account_time` DESC" . $pages->limitsql;
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
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>物料发票管理</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
      <!--   <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" size="15" /></td>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" size="15" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" size="15" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" size="15" /></td>
        -->
        <th>对账日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>

      
			
	    <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
					echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <!-- <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_inout_in.php'" />-->
        </td> 
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_total = $result_total->fetch_assoc()){
		  $total_amount += $row_total['amount'];
		  $total_invoice_amount += $row_total['invoice_amount'];	
	  }																																				
  ?>
  <table>
    <tr>
      <th width="">ID</th>
      <th width="">对账时间</th>
      <th width="">类型</th>
      <th width="">供应商</th>
      <th width="">对账金额</th>
      <th width="">发票号</th>
      <th width="">发票金额</th>
      <th width="">发票日期</th>
      <th width="">状态</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		$accountid = $row['accountid'];
		$listid = $row['listid'];
		 if($row['account_type'] == 'O'){
      $sql_other_supplier = "SELECT `supplier_cname` FROM `db_other_supplier` WHERE `other_supplier_id` =".$row['supplierid'];
      $supplier_res = $db->query($sql_other_supplier);
      if($supplier_res->num_rows){
        $row['supplier_cname'] = $supplier_res->fetch_row()[0];
      }
  }
	?>
  <form action="material_balance_account_do.php" method="post">
    <tr>
      <td>
        <input type="checkbox" name="id[]" value="<?php echo $accountid?>">
      </td>
      <td><?php echo $row['account_time']; ?></td>
      <td>
        <?php
        $type = $row['account_type'];
        if($type == 'M'){
          echo '模具物料';
        } elseif($type == 'C'){
          echo '加工刀具';
        } elseif($type == 'O'){
          echo '期间物料';
        }
        ?>
      </td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $row['invoice_no']; ?></td>
      <td><?php echo $row['invoice_amount']; ?></td>
      <td><?php echo $row['date'];?></td>

      <td>
      	<?php 
      	$status = $row['status'];
      	if($status == null){
      			echo '<a href="material_invoice_info.php?action=add&id='.$row['accountid'].'">录入发票</a>';
      		}elseif($status == 'A'){
      			echo '待接收';
      		}elseif($status == 'C'){
      			echo '完成';
      		}
      	?>
      </td>
    </tr>
    <?php 
    	$amount += $row['amount'];
    	$invoice_amount += $row['invoice_amount'];
    } ?>
    <tr>
      <td colspan="4">Total</td>
      <td><?php echo number_format($amount,2,'.',''); ?></td>
      <td></td>
      <td><?php echo number_format($invoice_amount,2,'.',''); ?></td>
      <td></td>
      <td></td>
    </tr>
  </table>
  <!-- <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="审核" class="select_button" />
    </div> -->
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