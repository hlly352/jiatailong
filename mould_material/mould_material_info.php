<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$mouldid = fun_check_int($_GET['id']);
// $sql = "SELECT `mouldid` FROM `db_mould`";
// $result = $db->query($sql);
// while($row = $result->fetch_assoc()){
//   $mouldid .= ','.$row['mouldid']; 
// }
// $mould_sql = "UPDATE `db_mould_material` SET `material_type` = 'N' WHERE `mouldid` NOT IN($mouldid)";
// $db->query($mould_sql);
$sql_mould = "SELECT `db_mould_specification`.`mould_specification_id`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`customer_code`,`db_mould_status`.`mould_statusname` FROM `db_mould_specification`  LEFT JOIN `db_mould_status` ON `db_mould_status`.`mould_statusid` = `db_mould_specification`.`mould_statusid` WHERE `db_mould_specification`.`mould_specification_id` = '$mouldid'";
$result_mould = $db->query($sql_mould);
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
<title>模具物料-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result_mould->num_rows){
    $array_mould = $result_mould->fetch_assoc();
  ?>
  <h4>模具物料</h4>
  <table>
    <tr>
      <th width="10%">代码：</th>
      <td width="15%"><?php echo $array_mould['customer_code']; ?></td>
      <th width="10%">项目名称：</th>
      <td width="15%"><?php echo $array_mould['project_name']; ?></td>
      <th width="10%">模具编号：</th>
      <td width="15%"><?php echo $array_mould['mould_no']; ?></td>
      <th width="10%">目前状态：</th>
      <td width="15%"><?php echo $array_mould['mould_statusname']; ?></td>
    </tr>
  </table>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php
$sql_mould_material = "SELECT `material_date`,`material_list_number`,`material_list_sn`,`material_number`,`material_name`,`specification`,`material_quantity`,`texture`,`hardness`,`brand`,`spare_quantity`,`remark`,`complete_status`,SUBSTRING(`material_number`,1,1) AS `material_number_code` FROM `db_mould_material` WHERE `db_mould_material`.`parentid` = '0' AND `mouldid` = '$mouldid' ORDER BY `material_date` DESC,`materialid` DESC";
$result_mould_material = $db->query($sql_mould_material);
?>
<div id="table_list">
  <?php if($result_mould_material->num_rows){ ?>
  <table>
    <caption>
    物料清单
    </caption>
    <tr>
      <th width="4%">序号</th>
      <th width="6%">下单日期</th>
      <th width="8%">料单编号</th>
      <th width="4%">料单序号</th>
      <th width="10%">物料编码</th>
      <th width="10%">物料名称</th>
      <th width="12%">规格</th>
      <th width="4%">数量</th>
      <th width="8%">材质</th>
      <th width="8%">硬度</th>
      <th width="8%">品牌</th>
      <th width="4%">备件数量</th>
      <th width="14%">备注</th>
    </tr>
    <?php
  $i = 1;
  while($row_mould_material = $result_mould_material->fetch_assoc()){
    $specification_bg = '';
    $material_name_bg = '';
    $material_number_code = $row_mould_material['material_number_code'];
    $specification = $row_mould_material['specification'];
    if(in_array($material_number_code,array(1,2,3,4,5))){
      $tag_a = substr_count($specification,'*');
      $tag_b = substr_count($specification,'#');
      $specification_bg = ($tag_a != 2 || $tag_b != 1)?" style=\"background:orange\"":'';
    }
    $material_name_bg = $row_mould_material['complete_status']?'':" style=\"background:yellow\"";
  ?>
    <tr>
      <td><?php echo $i; ?></td>
      <td><?php echo $row_mould_material['material_date']; ?></td>
      <td><?php echo $row_mould_material['material_list_number']; ?></td>
      <td><?php echo $row_mould_material['material_list_sn']; ?></td>
      <td><?php echo $row_mould_material['material_number']; ?></td>
      <td<?php echo $material_name_bg; ?>><?php echo $row_mould_material['material_name']; ?></td>
      <td<?php echo $specification_bg; ?>><?php echo $specification; ?></td>
      <td><?php echo $row_mould_material['material_quantity']; ?></td>
      <td><?php echo $row_mould_material['texture']; ?></td>
      <td><?php echo $row_mould_material['hardness']; ?></td>
      <td><?php echo $row_mould_material['brand']; ?></td>
      <td><?php echo $row_mould_material['spare_quantity']; ?></td>
      <td><?php echo $row_mould_material['remark']; ?></td>
    </tr>
    <?php
  $i++;
  }
  ?>
  </table>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无物料记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>