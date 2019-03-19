<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	if($mould_number){
		$sql_mould_number = " AND `db_mould`.`mould_number` LIKE '%$mould_number%'";
	}
	$order_number = trim($_GET['order_number']);
	$supplierid = $_GET['supplierid'];
	if($supplierid){
		$sql_supplierid = " AND `db_mould_outward`.`supplierid` = '$supplierid'";
	}
	$inout_status = $_GET['inout_status'];
	if($inout_status != NULL){
		$sql_inout_status = " AND `db_mould_outward`.`inout_status` = '$inout_status'";
	}
	$outward_status = $_GET['outward_status'];
	if($outward_status != NULL){
		$sql_outward_status = " AND `db_mould_outward`.`outward_status` = '$outward_status'";
	}
	$sqlwhere = " AND `db_mould_outward`.`order_number` LIKE '%$order_number%' $sql_mould_number $sql_supplierid $sql_inout_status $sql_outward_status";
}else{
	$outward_status = 1;
	$sqlwhere = " AND `db_mould_outward`.`outward_status` = '$outward_status'";
}
$sql = "SELECT `db_mould_outward`.`outwardid`,`db_mould_outward`.`mouldid`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`applyer`,`db_mould_outward`.`plan_date`,`db_mould_outward`.`actual_date`,`db_mould_outward`.`inout_status`,`db_mould_outward`.`outward_status`,`db_mould`.`mould_number`,`db_mould_workteam`.`workteam_name`,`db_mould_outward_type`.`outward_typename`,IF(`db_mould_outward`.`inout_status` = 1,DATEDIFF(`db_mould_outward`.`actual_date`,`db_mould_outward`.`plan_date`),DATEDIFF(`db_mould_outward`.`plan_date`,CURDATE())) AS `diff_date` FROM `db_mould_outward` LEFT JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_outward`.`workteamid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_mould_outward`.`outward_typeid` WHERE (`db_mould_outward`.`order_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_allid =$db->query($sql); 
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_outward`.`plan_date` DESC,`db_mould_outward`.`outwardid` DESC" . $pages->limitsql;
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
  <h4>外协加工</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>外协单号：</th>
        <td><input type="text" name="order_number" class="input_txt" /></td>
        <th>外协时间：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
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
        <td><select name="outward_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $outward_status && $outward_status != NULL) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='mould_outward_add.php'"></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="7%">模具编号</th>
      <th width="25%">零件编号</th>
      <th width="8%">外协时间</th>
      <th width="8%">申请组别</th>
      <th width="8%">外协单号</th>
      <th width="4%">数量</th>
      <th width="8%">类型</th>
      <th width="6%">申请人</th>
      <th width="7%">计划回厂</th>
      <th width="7%">实际回厂</th>
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
      <td><?php echo $row['outwardid']; ?></td>
      <td><?php echo $mould_number; ?></td>
      <td><?php echo $row['part_number']; ?></td>
      <td><?php echo $row['order_date']; ?></td>
      <td><?php echo $row['workteam_name']; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['outward_typename']; ?></td>
      <td><?php echo $row['applyer']; ?></td>
      <td<?php echo $plan_date_bg; ?>><?php echo $row['plan_date']; ?></td>
      <td><?php echo $actual_date; ?></td>
      <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
      <td><?php echo $array_status[$row['outward_status']]; ?></td>
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