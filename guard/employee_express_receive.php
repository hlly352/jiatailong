<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
//读取快递公司
if($_GET['submit']){
	$express_num = trim($_GET['express_num']);
	$receiver_name = trim($_GET['receiver_name']);
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
	$sqlwhere = " WHERE `db_employee_express_receive`.`express_num` LIKE '%$express_num%' AND `db_receiver`.`employee_name` LIKE '%$receiver_name%' $sql_express_incid $sql_apply_status $sql_get_status $sql_express_status";
}
$sql = "SELECT `db_employee_express_receive`.`expressid`,`db_employee_express_receive`.`express_num`,`db_employee_express_receive`.`sender`,`db_employee_express_receive`.`cost`,`db_employee_express_receive`.`applyer`,`db_employee_express_receive`.`confirmor`,`db_employee_express_receive`.`registrant`,`db_employee_express_receive`.`receipt_date`,`db_employee_express_receive`.`apply_status`,`db_employee_express_receive`.`get_status`,`db_employee_express_receive`.`express_status`,`db_receiver`.`employee_name` AS `receiver_name`,`db_registrant`.`employee_name` AS `registrant_name`,`db_applyer`.`employee_name` AS `applyer_name`,`db_confirmor`.`employee_name` AS `confirmor_name`,`db_express_inc`.`inc_cname`,`db_department`.`dept_name` FROM `db_employee_express_receive` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express_receive`.`express_incid` INNER JOIN `db_employee` AS `db_receiver` ON `db_receiver`.`employeeid` = `db_employee_express_receive`.`receiver` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_receiver`.`deptid` INNER JOIN `db_employee` AS `db_registrant` ON `db_registrant`.`employeeid` = `db_employee_express_receive`.`registrant` LEFT JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_express_receive`.`applyer` LEFT JOIN `db_employee` AS `db_confirmor` ON `db_confirmor`.`employeeid` = `db_employee_express_receive`.`confirmor` $sqlwhere";
$result = $db->query($sql);
$result_all_cost = $db->query($sql);
$_SESSION['employee_express_receive'] = $sql;
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
<script language="javascript" src="../js/jquery-1.6.4.min.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>门卫管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>收快递</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>快递单号：</th>
        <td><input type="text" name="express_num" class="input_txt" /></td>
        <th>收件人：</th>
        <td><input type="text" name="receiver_name" class="input_txt" /></td>
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
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='employee_express_receiveae.php?action=add'" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_employee_express_receive.php'" /></td>
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
  <form action="employee_express_receivedo.php" name="employee_express_receive_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="8%">快递公司</th>
        <th width="8%">快递单号</th>
        <th width="8%">部门</th>
        <th width="6%">收件人</th>
        <th width="6%">收件日期</th>
        <th width="12%">寄件方</th>
        <th width="6%">费用</th>
        <th width="6%">申领人</th>
        <th width="6%">确认人</th>
        <th width="6%">登记人</th>
        <th width="4%">申领</th>
        <th width="4%">提件</th>
        <th width="4%">状态</th>
        <th width="4%">Confirm</th>
        <th width="4%">Edit</th>
        <th width="4%">Info</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $expressid = $row['expressid'];
		  $apply_status = $row['apply_status'];
		  $get_status = $row['get_status'];
		  $applyer_name = $row['applyer']?$row['applyer_name']:'--';
		  $confirmor_name = $row['confirmor']?$row['confirmor_name']:'--';
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $expressid; ?>"<?php if($row['registrant'] != $accountid || $row['getstatus']) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['inc_cname']; ?></td>
        <td><?php echo $row['express_num']; ?></td>
        <td><?php echo $row['dept_name']; ?></td>
        <td><?php echo $row['receiver_name']; ?></td>
        <td><?php echo $row['receipt_date']; ?></td>
        <td><?php echo $row['sender']; ?></td>
        <td><?php echo $row['cost']; ?></td>
        <td><?php echo $applyer_name; ?></td>
        <td><?php echo $confirmor_name; ?></td>
        <td><?php echo $row['registrant_name']; ?></td>
        <td><?php echo $array_express_get_status[$get_status]; ?></td>
        <td><?php echo $array_express_apply_status[$apply_status]; ?></td>
        <td><?php echo $array_status[$row['express_status']]; ?></td>
        <td><?php if($apply_status && !$get_status){ ?>
          <a href="employee_express_receive_confirm.php?id=<?php echo $expressid; ?>"><img src="../images/system_ico/confirm_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
        <td><?php if($row['registrant'] == $employeeid && !$row['get_status']){ ?>
          <a href="employee_express_receiveae.php?id=<?php echo $expressid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
        <td><a href="employee_express_receive_info.php?id=<?php echo $expressid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="7">Total</td>
        <td><?php echo $total_cost; ?></td>
        <td colspan="9">&nbsp;</td>
      </tr>
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