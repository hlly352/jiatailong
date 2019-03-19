<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$weldid = fun_check_int($_GET['id']);
$sql = "SELECT `db_mould_weld`.`mouldid`,`db_mould_weld`.`part_number`,`db_mould_weld`.`order_date`,`db_mould_weld`.`order_number`,`db_mould_weld`.`quantity`,`db_mould_weld`.`weld_cause`,`db_mould_weld`.`cost`,`db_mould_weld`.`applyer`,`db_mould_weld`.`plan_date`,`db_mould_weld`.`actual_date`,`db_mould_weld`.`inout_status`,`db_mould_weld`.`weld_status`,`db_mould_weld`.`remark`,`db_mould_weld`.`dotime`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_responsibility_team`.`team_name`,`db_mould_weld_type`.`weld_typename`,`db_employee`.`employee_name` FROM `db_mould_weld` LEFT JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_weld`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_weld`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_weld`.`workteamid` INNER JOIN `db_responsibility_team` ON `db_responsibility_team`.`teamid` = `db_mould_weld`.`teamid` INNER JOIN `db_mould_weld_type` ON `db_mould_weld_type`.`weld_typeid` = `db_mould_weld`.`weld_typeid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_weld`.`employeeid` WHERE `db_mould_weld`.`weldid` = '$weldid'";
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
  ?>
  <h4>零件烧焊信息</h4>
  <table>
    <tr>
      <th width="12%">模具编号：</th>
      <td width="12%"><?php echo $mould_number; ?></td>
      <th width="12%">零件编号：</th>
      <td width="12%"><?php echo $array['part_number']; ?></td>
      <th width="12%">外发时间：</th>
      <td width="12%"><?php echo $array['order_date']; ?></td>
      <th width="12%">申请组别：</th>
      <td width="12%"><?php echo $array['workteam_name']; ?></td>
    </tr>
    <tr>
      <th>外协单号：</th>
      <td><?php echo $array['order_number']; ?></td>
      <th>数量：</th>
      <td><?php echo $array['quantity']; ?></td>
      <th>烧焊原因：</th>
      <td><?php echo $array['weld_cause']; ?></td>
      <th>责任组别：</th>
      <td><?php echo $array['team_name']; ?></td>
    </tr>
    <tr>
      <th>供应商：</th>
      <td><?php echo $array['supplier_cname']; ?></td>
      <th>加工类型：</th>
      <td><?php echo $array['weld_typename']; ?></td>
      <th>金额：</th>
      <td><?php echo $array['cost']; ?></td>
      <th>申请人：</th>
      <td><?php echo $array['applyer']; ?></td>
    </tr>
    <tr>
      <th>计划回厂：</th>
      <td><?php echo $array['plan_date']; ?></td>
      <th>计划回厂：</th>
      <td><?php echo $array['actual_date']; ?></td>
      <th>进度状态：</th>
      <td><?php echo $array_mould_inout_status[$array['inout_status']]; ?></td>
      <th>状态：</th>
      <td><?php echo $array_status[$array['weld_status']]; ?></td>
    </tr>
    <tr>
      <th>备注：</th>
      <td><?php echo $array['remark']; ?></td>
      <th>操作人：</th>
      <td><?php echo $array['employee_name']; ?></td>
      <th>操作时间：</th>
      <td colspan="3"><?php echo $array['dotime']; ?></td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php
$sql_file = "SELECT `db_upload_file`.`fileid`,`db_upload_file`.`filedir`,`db_upload_file`.`filename`,`db_upload_file`.`upfilename`,`db_upload_file`.`dotime`,`db_employee`.`employee_name` FROM `db_upload_file` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_upload_file`.`employeeid` WHERE `db_upload_file`.`linkcode` = 'MW' AND `db_upload_file`.`linkid` = '$weldid' ORDER BY `db_upload_file`.`fileid` ASC";
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
<?php include "../footer.php"; ?>
</body>
</html>