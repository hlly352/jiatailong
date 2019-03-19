<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(2,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	if($mould_number){
		$sql_mould_number = " AND `db_mould`.`mould_number` LIKE '%$mould_number%'";
	}
	$supplierid = $_GET['supplierid'];
	if($supplierid){
		$sql_supplierid = " AND `db_mould_weld`.`supplierid` = '$supplierid'";
	}
	$inout_status = $_GET['inout_status'];
	if($inout_status != NULL){
		$sql_inout_status = " AND `db_mould_weld`.`inout_status` = '$inout_status'";
	}
	$weld_status = $_GET['weld_status'];
	if($weld_status != NULL){
		$sql_weld_status = " AND `db_mould_weld`.`weld_status` = '$weld_status'";
	}
	$sqlwhere = " $sql_mould_number $sql_supplierid $sql_inout_status $sql_weld_status";
}else{
	$weld_status = 1;
	$sqlwhere = " AND `db_mould_weld`.`weld_status` = '$weld_status'";
}
$sql = "SELECT `db_mould_weld`.`weldid`,`db_mould_weld`.`mouldid`,`db_mould_weld`.`part_number`,`db_mould_weld`.`order_date`,`db_mould_weld`.`order_number`,`db_mould_weld`.`quantity`,`db_mould_weld`.`weld_cause`,`db_mould_weld`.`cost`,`db_mould_weld`.`applyer`,`db_mould_weld`.`plan_date`,`db_mould_weld`.`actual_date`,`db_mould_weld`.`inout_status`,`db_mould_weld`.`weld_status`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_responsibility_team`.`team_name`,`db_mould_weld_type`.`weld_typename`,IF(`db_mould_weld`.`inout_status` = 1,DATEDIFF(`db_mould_weld`.`actual_date`,`db_mould_weld`.`plan_date`),DATEDIFF(`db_mould_weld`.`plan_date`,CURDATE())) AS `diff_date` FROM `db_mould_weld` LEFT JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_weld`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_weld`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_weld`.`workteamid` INNER JOIN `db_responsibility_team` ON `db_responsibility_team`.`teamid` = `db_mould_weld`.`teamid` INNER JOIN `db_mould_weld_type` ON `db_mould_weld_type`.`weld_typeid` = `db_mould_weld`.`weld_typeid` WHERE (`db_mould_weld`.`order_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_allid =$db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_weld`.`plan_date` DESC,`db_mould_weld`.`weldid` DESC" . $pages->limitsql;
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
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>零件烧焊</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>外发时间：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
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
        <th>进度状态：</th>
        <td><select name="inout_status">
            <option value="">所有</option>
            <?php
			foreach($array_mould_inout_status as $inout_status_key=>$inout_status_value){
				echo "<option value=\"".$inout_status_key."\">".$inout_status_value."</option>";
			}
			?>
          </select></td>
        <th>状态：</th>
        <td><select name="weld_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $weld_status && $weld_status != NULL) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="6%">模具编号</th>
      <th width="8%">零件编号</th>
      <th width="6%">外发时间</th>
      <th width="6%">申请组别</th>
      <th width="8%">外协单号</th>
      <th width="4%">数量</th>
      <th width="10%">烧焊原因</th>
      <th width="6%">责任组别</th>
      <th width="8%">供应商</th>
      <th width="4%">类型</th>
      <th width="4%">金额</th>
      <th width="6%">申请人</th>
      <th width="6%">计划回厂</th>
      <th width="6%">实际回厂</th>
      <th width="4%">进度状态</th>
      <th width="4%">状态</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		 $mould_number = $row['mouldid']?$row['mould_number']:'--';
		$inout_status = $row['inout_status'];
		$actual_date = $inout_status?$row['actual_date']:'--';
		$diff_date = $row['diff_date'];
		if($inout_status && $diff_date>0){
			$actual_date = "<font color=red>".$actual_date."</font>";
		}else{
			$actual_date = $actual_date;
		}
		if(!$inout_status && $diff_date <=1){
			$plan_date_bg = " style=\"background:#F00;\"";
		}elseif(!$inout_status && ($diff_date == 2 || $diff_date == 3)){
			$plan_date_bg = " style=\"background:#FF0;\"";
		}else{
			$plan_date_bg = "";
		}
	?>
    <tr>
      <td><?php echo $row['weldid']; ?></td>
      <td><?php echo $mould_number; ?></td>
      <td><?php echo $row['part_number']; ?></td>
      <td><?php echo $row['order_date']; ?></td>
      <td><?php echo $row['workteam_name']; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['weld_cause']; ?></td>
      <td><?php echo $row['team_name']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['weld_typename']; ?></td>
      <td><?php echo $row['cost']; ?></td>
      <td><?php echo $row['applyer']; ?></td>
      <td<?php echo $plan_date_bg; ?>><?php echo $row['plan_date']; ?></td>
      <td><?php echo $actual_date; ?></td>
      <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
      <td><?php echo $array_status[$row['weld_status']]; ?></td>
    </tr>
    <?php } ?>
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