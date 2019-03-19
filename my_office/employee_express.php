<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//读取快递公司
$sql_express_inc = "SELECT `incid`,`inc_cname` FROM `db_express_inc` ORDER BY `incid` ASC";
$result_express_inc = $db->query($sql_express_inc);
if($_GET['submit']){
	$express_num = trim($_GET['express_num']);
	$applyer_name = trim($_GET['applyer_name']);
	$express_incid = $_GET['express_incid'];
	if($express_incid){
		$sql_expressinc = " AND `db_employee_express`.`express_incid` = '$express_incid'";
	}
	$paytype = $_GET['paytype'];
	if($paytype){
		$sql_paytype = " AND `db_employee_express`.`paytype` = '$paytype'";
	}
	$approve_status = $_GET['approve_status'];
	if($approve_status){
		$sql_approve_status = " AND `db_employee_express`.`approve_status` = '$approve_status'";
	}
	$express_status = $_GET['express_status'];
	if($express_status != NULL){
		$sql_express_status = " AND `db_employee_express`.`express_status` = '$express_status'";
	}
	$sqlwhere = " AND `db_employee_express`.`express_num` LIKE '%$express_num%' AND `db_applyer`.`employee_name` LIKE '%$applyer_name%' $sql_expressinc $sql_paytype $sql_approve_status $sql_express_status";
}
$sql = "SELECT `db_employee_express`.`expressid`,`db_employee_express`.`reckoner`,`db_employee_express`.`apply_date`,`db_employee_express`.`express_num`,`db_employee_express`.`consignee_inc`,`db_employee_express`.`paytype`,`db_employee_express`.`cost`,`db_employee_express`.`express_item`,`db_employee_express`.`approve_status`,`db_employee_express`.`express_status`,`db_applyer`.`employee_name` AS `applyer_name`,`db_reckoner`.`employee_name` AS `reckoner_name`,`db_express_inc`.`inc_cname`,`db_express_inc`.`inc_ename`,`db_department`.`dept_name` FROM `db_employee_express` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express`.`express_incid` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_express`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_applyer`.`deptid` LEFT JOIN `db_employee` AS `db_reckoner` ON `db_reckoner`.`employeeid` = `db_employee_express`.`reckoner` WHERE (`db_employee_express`.`apply_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_all_cost = $db->query($sql);
$_SESSION['employee_express'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_employee_express`.`expressid` DESC" . $pages->limitsql;
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
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>快递</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>快递单号：</th>
        <td><input type="text" name="express_num" class="input_txt" size="15" /></td>
        <th>申请人：</th>
        <td><input type="text" name="applyer_name" class="input_txt" size="15" /></td>
        <th>申请日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>
        <th>快递公司：</th>
        <td><select name="express_incid">
            <option value="">所有</option>
            <?php
			if($result_express_inc->num_rows){
				while($row_express_inc = $result_express_inc->fetch_assoc()){
					echo "<option value=\"".$row_express_inc['incid']."\">".$row_express_inc['inc_cname']."</option>";
				}
			}
			?>
          </select></td>
        <th>付款方式：</th>
        <td><select name="paytype">
            <option value="">所有</option>
            <?php
			foreach($array_express_paytype as $paytype_key=>$paytype_value){
				echo "<option value=\"".$paytype_key."\">".$paytype_value."</option>";
			}
			?>
          </select></td>
        <th>审批：</th>
        <td><select name="approve_status">
            <option value="">所有</option>
            <?php
			foreach($array_office_approve_status as $approve_status_key=>$approve_status_value){
				echo "<option value=\"".$approve_status_key."\">".$approve_status_value."</option>";
			}
			?>
          </select></td>
        <th>状态：</th>
        <td><select name="express_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_employee_express.php'" /></td>
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
      <th width="8%">快递单号</th>
      <th width="6%">部门</th>
      <th width="6%">申请人</th>
      <th width="6%">申请日期</th>
      <th width="6%">快递公司</th>
      <th width="12%">收件人公司</th>
      <th width="22%">快递物品</th>
      <th width="6%">付款方式</th>
      <th width="6%">结算人</th>
      <th width="6%">费用</th>
      <th width="4%">审批</th>
      <th width="4%">状态</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$expressid = $row['expressid'];
		$reckoner_name = $row['reckoner']?$row['reckoner_name']:'--';
		$cost = $row['reckoner']?$row['cost']:'--';
		$consignee_inc = $row['consignee_inc'];
	?>
    <tr>
      <td><?php echo $expressid; ?></td>
      <td><?php echo $row['express_num']; ?></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><?php echo $row['applyer_name']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $row['inc_cname']; ?></td>
      <td><?php echo $row['consignee_inc']; ?></td>
      <td><?php echo $row['express_item']; ?></td>
      <td><?php echo $array_express_paytype[$row['paytype']];; ?></td>
      <td><?php echo $reckoner_name; ?></td>
      <td><?php echo $cost; ?></td>
      <td><?php echo $array_office_approve_status[$row['approve_status']];; ?></td>
      <td><?php echo $array_status[$row['express_status']];; ?></td>
      <td><a href="employee_express_info.php?id=<?php echo $expressid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="10">Total</td>
      <td><?php echo $total_cost; ?></td>
      <td colspan="3">&nbsp;</td>
    </tr>
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