<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$expressid = fun_check_int($_GET['id']);
$sql = "SELECT `db_employee_express`.`expressid`,`db_employee_express`.`express_num`,`db_employee_express`.`applyer`,`db_employee_express`.`agenter`,`db_employee_express`.`apply_date`,`db_employee_express`.`consignee_inc`,`db_employee_express`.`express_item`,`db_employee_express`.`paytype`,`db_employee_express`.`dotime`,`db_employee_express`.`approve_status`,`db_applyer`.`employee_name` AS `applyer_name`,`db_agenter`.`employee_name` AS `agenter_name`,`db_express_inc`.`inc_cname`,`db_express_inc`.`inc_ename` FROM `db_employee_express` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express`.`express_incid` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_express`.`applyer` INNER JOIN `db_employee` AS `db_agenter` ON `db_agenter`.`employeeid` = `db_employee_express`.`agenter` WHERE `db_employee_express`.`approve_status` = 'A' AND `db_employee_express`.`express_status` = 1 AND (`db_applyer`.`superior` = '$employeeid' OR (`db_applyer`.`position_type` IN ('A','B') AND `db_employee_express`.`applyer` = '$employeeid')) AND `db_employee_express`.`expressid` = '$expressid'";
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
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$approve_status = $array['approve_status'];
	$agenter_name = ($array['applyer'] == $array['agenter'])?'--':$array['agenter_name'];
?>
<div id="table_sheet">
  <h4>快递审批</h4>
  <form action="employee_express_approvedo.php" name="employee_express_approve" method="post">
    <table>
      <tr>
        <th width="20%">快递单号：</th>
        <td width="80%"><?php echo $array['express_num']; ?></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><?php echo $array['applyer_name']; ?></td>
      </tr>
      <tr>
        <th>代理人：</th>
        <td><?php echo $agenter_name; ?></td>
      </tr>
      <tr>
        <th>申请日期：</th>
        <td><?php echo $array['apply_date']; ?></td>
      </tr>
      <tr>
        <th>快递公司：</th>
        <td><?php echo $array['inc_cname'].'('.$array['inc_ename'].')'; ?></td>
      </tr>
      <tr>
        <th>收件人公司：</th>
        <td><?php echo $array['consignee_inc']; ?></td>
      </tr>
      <tr>
        <th>快递物品：</th>
        <td><?php echo $array['express_item']; ?></td>
      </tr>
      <tr>
        <th>付款方式：</th>
        <td><?php echo $array_express_paytype[$array['paytype']]; ?></td>
      </tr>
      <tr>
        <th>操作时间：</th>
        <td><?php echo $array['dotime']; ?></td>
      </tr>
      <tr>
        <th>审批意见：</th>
        <td><input type="text" name="approve_content" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>审批状态：</th>
        <td><select name="approve_status">
            <?php
          foreach($array_office_approve_status as $approve_status_key=>$approve_status_value){
			  if( $approve_status_key != $approve_status){
				  echo "<option value=\"".$approve_status_key."\">".$approve_status_value."</option>";
			  }
		  }
		  ?>
          </select></td>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="expressid" value="<?php echo $expressid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
$sql_approve = "SELECT `db_office_approve`.`approveid`,`db_office_approve`.`approve_content`,`db_office_approve`.`approve_status`,`db_office_approve`.`dotime`,`db_employee`.`employee_name` FROM `db_office_approve` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_office_approve`.`approver` WHERE `db_office_approve`.`linkid` = '$expressid' AND `db_office_approve`.`approve_type` = 'E' ORDER BY `db_office_approve`.`approveid` DESC";
$result_approve = $db->query($sql_approve);
?>
<div id="table_list">
  <?php if($result_approve->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">审批人</th>
      <th>审批意见</th>
      <th width="10%">审批状态</th>
      <th width="10%">审批时间</th>
    </tr>
    <?php while($row_approve = $result_approve->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_approve['approveid']; ?></td>
      <td><?php echo $row_approve['employee_name']; ?></td>
      <td><?php echo $row_approve['approve_content']; ?></td>
      <td><?php echo $array_office_approve_status[$row_approve['approve_status']]; ?></td>
      <td><?php echo $row_approve['dotime']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无审批记录！</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>