<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
if($_GET['submit']){
	$apply_number = rtrim($_GET['apply_number']);
	$specification = rtrim($_GET['specification']);
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
	}
	$sqlwhere = " AND `db_cutter_apply`.`apply_number` LIKE '%$apply_number%' AND `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid ";
}
$sql = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`listid`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`old_quantity`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`remark`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`employeeid`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_mould`.`mould_number`,`db_employee`.`employee_name` FROM `db_cutter_inout` INNER JOIN `db_cutter_apply_list` ON `db_cutter_apply_list`.`apply_listid` = `db_cutter_inout`.`apply_listid` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_cutter_apply_list`.`mouldid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` WHERE (`db_cutter_inout`.`dodate` BETWEEN '$sdate' AND '$edate') AND `db_cutter_inout`.`dotype` = 'O' $sqlwhere";
$result = $db->query($sql);
$_SESSION['cutter_inout_list_out'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_inout`.`inoutid` DESC" . $pages->limitsql;
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
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>刀具出库记录</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>申领编号：</th>
        <td><input type="text" name="apply_number" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="typeid">
            <option value="">所有</option>
            <?php
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_type['typeid']; ?>"<?php if($row_cutter_type['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type['type']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <th>出库日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_cutter_inout_out.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">申领编号</th>
      <th width="8%">类型</th>
      <th width="12%">规格</th>
      <th width="8%">材质</th>
      <th width="10%">硬度</th>
      <th width="5%">数量</th>
      <th width="5%">更换数量</th>
      <th width="4%">单位</th>
      <th width="8%">申领人</th>
      <th width="8%">模具编号</th>
      <th width="8%">出库日期</th>
      <th width="4%">Edit</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$inoutid = $row['inoutid'];
		$listid = $row['listid']
	?>
    <tr>
      <td><?php echo $inoutid; ?></td>
      <td><?php echo $row['apply_number']; ?></td>
      <td><?php echo $row['type']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
      <td><?php echo $row['hardness']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['old_quantity']; ?></td>
      <td>件</td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['dodate']; ?></td>
      <td><?php if($row['employeeid'] == $employeeid){ ?>
        <a href="cutter_out_list_out.php?id=<?php echo $inoutid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
        <?php } ?></td>
      <td><a href="cutter_inout_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
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