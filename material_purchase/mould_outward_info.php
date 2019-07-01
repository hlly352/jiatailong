<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$outwardid = fun_check_int($_GET['id']);
$sql = "SELECT `db_mould_outward`.`mouldid`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`cost`,`db_mould_outward`.`iscash`,`db_mould_outward`.`applyer`,`db_mould_outward`.`plan_date`,`db_mould_outward`.`actual_date`,`db_mould_outward`.`inout_status`,`db_mould_outward`.`outward_status`,`db_mould_outward`.`remark`,`db_mould_outward`.`dotime`,`db_mould_outward`.`supplierid`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_mould_outward_type`.`outward_typename`,`db_employee`.`employee_name` FROM `db_mould_outward` LEFT JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` LEFT JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_outward`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_outward`.`workteamid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_mould_outward`.`outward_typeid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_outward`.`employeeid` WHERE `db_mould_outward`.`outwardid` = '$outwardid'";
$result = $db->query($sql);
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
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $mould_number = $array['mouldid']?$array['mould_number']:'--';
	  $inout_status = $array['inout_status'];
	  $actual_date = $inout_status?$array['actual_date']:'--';
    $supplier_cname = $array['supplierid'] ? $array['supplier_cname'] : '--';
  ?>
  <h4>外协加工信息</h4>
  <table>
    <tr>
      <th width="10%">模具编号：</th>
      <td width="15%"><?php echo $mould_number; ?></td>
      <th width="10%">零件编号：</th>
      <td width="15%"><?php echo $array['part_number']; ?></td>
      <th width="10%">外协时间：</th>
      <td width="15%"><?php echo $array['order_date']; ?></td>
      <th width="10%">申请组别：</th>
      <td width="15%"><?php echo $array['workteam_name']; ?></td>
    </tr>
    <tr>
      <th>外协单号：</th>
      <td><?php echo $array['order_number']; ?></td>
      <th>数量：</th>
      <td><?php echo $array['quantity']; ?></td>
      <th>供应商：</th>
      <td><?php echo $supplier_cname; ?></td>
      <th>类型：</th>
      <td><?php echo $array['outward_typename']; ?></td>
    </tr>
    <tr>
      <th>金额：</th>
      <td><?php echo $array['cost']; ?></td>
      <th>申请人：</th>
      <td><?php echo $array['applyer']; ?></td>
      <th>计划回厂：</th>
      <td><?php echo $array['plan_date']; ?></td>
      <th>实际回厂：</th>
      <td><?php echo $actual_date; ?></td>
    </tr>
    <tr>
      <th>现金：</th>
      <td><?php echo $array_is_status[$array['iscash']]; ?></td>
      <th>进度状态：</th>
      <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
      <th>状态：</th>
      <td><?php echo $array_status[$array['outward_status']]; ?></td>
      <th>操作人：</th>
      <td><?php echo $array['employee_name']; ?></td>
    </tr>
    <tr>
      <th>操作时间：</th>
      <td><?php echo $array['dotime']; ?></td>
      <th>备注：</th>
      <td colspan="5"><?php echo $array['remark']; ?></td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php
$sql_file = "SELECT `db_upload_file`.`fileid`,`db_upload_file`.`filedir`,`db_upload_file`.`filename`,`db_upload_file`.`upfilename`,`db_upload_file`.`dotime`,`db_employee`.`employee_name` FROM `db_upload_file` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_upload_file`.`employeeid` WHERE `db_upload_file`.`linkcode` = 'MO' AND `db_upload_file`.`linkid` = '$outwardid' ORDER BY `db_upload_file`.`fileid` ASC";
$result_file = $db->query($sql_file);
?>
<div id="table_list">
  <?php if($result_file->num_rows){ ?>
  <table>
    <caption>
    资料文件列表
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th>文件名称</th>
      <th width="10%">文件大小</th>
      <th width="10%">上传人</th>
      <th width="10%">上传时间</th>
      <th width="4%">Down</th>
    </tr>
    <?php
      while($row_file = $result_file->fetch_assoc()){
		  $fileid = $row_file['fileid'];
		  $filedir = $row_file['filedir'];
		  $filename = $row_file['filename'];
		  $file_path = "../upload/file/".$filedir.'/'.$filename;
		  $file_path_url = "/upload/file/".$filedir.'/'.$filename;
		  $filesize = (is_file)?fun_sizeformat(filesize($file_path)):0;
	  ?>
    <tr>
      <td><?php echo $fileid; ?></td>
      <td><?php echo $row_file['upfilename']; ?></td>
      <td><?php echo $filesize; ?></td>
      <td><?php echo $row_file['employee_name']; ?></td>
      <td><?php echo $row_file['dotime']; ?></td>
      <td><a href="../upload/download_file.php?id=<?php echo $fileid; ?>"><img src="../images/system_ico/download_10_10.png" width="10" height="10" /></a></td>
    </tr>
    <?php } ?>
  </table>
  <?php } ?>
</div>
<?php
$sql_pay = "SELECT `db_cash_pay`.`payid`,`db_cash_pay`.`pay_date`,`db_cash_pay`.`pay_amount`,`db_cash_pay`.`dotime`,`db_cash_pay`.`remark`,`db_employee`.`employee_name` FROM `db_cash_pay` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cash_pay`.`employeeid` WHERE `db_cash_pay`.`linkid` = '$outwardid' AND `db_cash_pay`.`data_type` = 'MO' ORDER BY `db_cash_pay`.`pay_date` DESC,`db_cash_pay`.`payid` DESC";
$result_pay = $db->query($sql_pay);
?>
<div id="table_list">
  <?php if($result_pay->num_rows){ ?>
  <table>
    <caption>
    付款记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="16%">付款日期</th>
      <th width="16%">付款金额</th>
      <th width="16%">付款人</th>
      <th width="20%">操作时间</th>
      <th width="28%">备注</th>
    </tr>
    <?php
    while($row_pay = $result_pay->fetch_assoc()){
		$pay_amount = $row_pay['pay_amount'];
	?>
    <tr<?php echo ($row_pay['payid'] == $payid)?" style=\"background:#DCD9FD\"":''; ?>>
      <td><?php echo $row_pay['payid']; ?></td>
      <td><?php echo $row_pay['pay_date']; ?></td>
      <td><?php echo $pay_amount; ?></td>
      <td><?php echo $row_pay['employee_name']; ?></td>
      <td><?php echo $row_pay['dotime']; ?></td>
      <td><?php echo $row_pay['remark']; ?></td>
    </tr>
    <?php
	$all_pay_amount += $pay_amount;
    }
	?>
    <tr>
      <td colspan="2">Total</td>
      <td><?php echo number_format($all_pay_amount,2); ?></td>
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无付款记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>