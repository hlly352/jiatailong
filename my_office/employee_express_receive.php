<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
//读取快递公司
$sql_inc = "SELECT `incid`,`inc_cname` FROM `db_express_inc`  ORDER BY `inc_ename` ASC,`incid` ASC";
$result_inc = $db->query($sql_inc);
if($_GET['submit']){
	$express_num = trim($_GET['express_num']);
	$apply_status = $_GET['apply_status'];
	if($apply_status != NULL){
		$sql_apply_status = " AND `db_employee_express_receive`.`apply_status` = '$apply_status'";
	}
	$get_status = $_GET['get_status'];
	if($get_status != NULL){
		$sql_get_status = " AND `db_employee_express_receive`.`get_status` = '$get_status'";
	}
	$express_status = $_GET['express_status'];
	if($express_status != NULL){
		$sql_express_status = " AND `db_employee_express_receive`.`express_status` = '$express_status'";
	}
	$sqlwhere = " AND `db_employee_express_receive`.`express_num` LIKE '%$express_num%' $sql_express_incid $sql_apply_status $sql_get_status $sql_express_status";
}
$sql = "SELECT `db_employee_express_receive`.`expressid`,`db_employee_express_receive`.`express_num`,`db_employee_express_receive`.`sender`,`db_employee_express_receive`.`cost`,`db_employee_express_receive`.`applyer`,`db_employee_express_receive`.`confirmor`,`db_employee_express_receive`.`registrant`,`db_employee_express_receive`.`receipt_date`,`db_employee_express_receive`.`apply_status`,`db_employee_express_receive`.`get_status`,`db_employee_express_receive`.`express_status`,`db_receiver`.`employee_name` AS `receiver_name`,`db_registrant`.`employee_name` AS `registrant_name`,`db_applyer`.`employee_name` AS `applyer_name`,`db_confirmor`.`employee_name` AS `confirmor_name`,`db_express_inc`.`inc_cname` FROM `db_employee_express_receive` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express_receive`.`express_incid` INNER JOIN `db_employee` AS `db_receiver` ON `db_receiver`.`employeeid` = `db_employee_express_receive`.`receiver` INNER JOIN `db_employee` AS `db_registrant` ON `db_registrant`.`employeeid` = `db_employee_express_receive`.`registrant` LEFT JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_express_receive`.`applyer` LEFT JOIN `db_employee` AS `db_confirmor` ON `db_confirmor`.`employeeid` = `db_employee_express_receive`.`confirmor` WHERE `db_employee_express_receive`.`receiver` = '$employeeid' $sqlwhere";
$result = $db->query($sql);
$result_all_cost = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_employee_express_receive`.`expressid` DESC" . $pages->limitsql;
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
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>我的快递</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>快递单号：</th>
        <td><input type="text" name="express_num" class="input_txt" /></td>
        <th>申领状态：</th>
        <td><select name="apply_status">
            <option value="">所有</option>
            <?php
			foreach($array_express_apply_status as $apply_status_key=>$apply_status_value){
				echo "<option value=\"".$apply_status_key."\">".$apply_status_value."</option>";
			}
			?>
          </select>
        <th>提件状态：</th>
        <td><select name="get_status">
            <option value="">所有</option>
            <?php
			foreach($array_express_get_status as $get_status_key=>$get_status_value){
				echo "<option value=\"".$get_status_key."\">".$get_status_value."</option>";
			}
			?>
          </select></td>
        <th>状态：</th>
        <td><select name="express_status">
            <option value="">所有</option>
            <?php
			foreach($array_status as $status_key=>$status_value){
				echo "<option value=\"".$status_key."\">".$status_value."</option>";
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_all_cost = $result_all_cost->fetch_assoc()){
		  $total_cost += $row_all_cost['cost'];
	  }
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">快递公司</th>
      <th width="8%">快递单号</th>
      <th width="6%">收件人</th>
      <th width="12%">寄件方</th>
      <th width="8%">收件日期</th>
      <th width="6%">费用</th>
      <th width="6%">申领状态</th>
      <th width="8%">申领人</th>
      <th width="6%">提件状态</th>
      <th width="8%">提件确认人</th>
      <th width="8%">登记人</th>
      <th width="4%">状态</th>
      <th width="4%">申领</th>
      <th width="4%">Info</th>
    </tr>
    <?php
      while($row = $result->fetch_assoc()){
		  $expressid = $row['expressid'];
		  $expressinc = $row['inc_cname'];
		  $applyer = $row['applyer'];
		  $confirmor = $row['confirmor'];
		  $applyer_name = $applyer?$row['applyer_name']:'--';
		  $confirmor_name = $confirmor?$row['confirmor_name']:'--';
		  $apply_status = $array_express_apply_status[$row['apply_status']];
		  $get_status = $array_express_get_status[$row['get_status']];
		  $express_status = $array_status[$row['express_status']];
	  ?>
    <tr>
      <td><?php echo $expressid; ?></td>
      <td><?php echo $expressinc; ?></td>
      <td><?php echo $row['express_num']; ?></td>
      <td><?php echo $row['receiver_name']; ?></td>
      <td><?php echo $row['sender']; ?></td>
      <td><?php echo $row['receipt_date']; ?></td>
      <td><?php echo $row['cost']; ?></td>
      <td><?php echo $apply_status; ?></td>
      <td><?php echo $applyer_name; ?></td>
      <td><?php echo $get_status; ?></td>
      <td><?php echo $confirmor_name; ?></td>
      <td><?php echo $row['registrant_name']; ?></td>
      <td><?php echo $express_status; ?></td>
      <td><?php if($row['apply_status'] == 0){ ?>
        <a href="employee_express_receive_apply.php?id=<?php echo $expressid; ?>"><img src="../images/system_ico/confirm_10_10.png" width="10" height="10" />
        <?php } ?>
        </a></td>
      <td><a href="employee_express_receive_info.php?id=<?php echo $expressid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="6">Total</td>
      <td><?php echo $total_cost; ?></td>
      <td colspan="8">&nbsp;</td>
    </tr>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录!</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>