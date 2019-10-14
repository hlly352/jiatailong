<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$isadmin = $_SESSION['system_shell'][$system_dir]['isadmin'];
if($_GET['submit']){
  $mould_number = trim($_GET['mould_number']);
  $material_number = trim($_GET['material_number']);
  $material_name = trim($_GET['material_name']);
  $specification = trim($_GET['specification']);
  $sqlwhere = " AND `db_mould_specification`.`mould_no` LIKE '%$mould_number%' AND `db_mould_material`.`material_number` LIKE '%$material_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
}
if($isadmin == 1){
    $sql = "SELECT `db_employee`.`employee_name`,`db_mould_outward_type`.`outward_typename`,`db_outward_inquiry`.`inquiryid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_specification`.`mould_no`,`db_outward_inquiry`.`outward_remark` FROM `db_outward_inquiry` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_outward_inquiry`.`materialid` INNER JOIN `db_mould_specification` ON `db_mould_specification`.`mould_specification_id` = `db_mould_material`.`mouldid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_outward_inquiry`.`outward_typeid` INNER JOIN `db_employee` ON `db_outward_inquiry`.`employeeid` = `db_employee`.`employeeid` WHERE `db_outward_inquiry`.`status` = '0' AND `db_outward_inquiry`.`inquiryid` NOT IN (SELECT `inquiryid` FROM `db_outward_inquiry_orderlist` GROUP BY `inquiryid`) $sqlwhere";
}else{
     $sql = "SELECT `db_employee`.`employee_name`,`db_mould_outward_type`.`outward_typename`,`db_outward_inquiry`.`inquiryid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_specification`.`mould_no`,`db_outward_inquiry`.`outward_remark` FROM `db_outward_inquiry` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_outward_inquiry`.`materialid` INNER JOIN `db_mould_specification` ON `db_mould_specification`.`mould_specification_id` = `db_mould_material`.`mouldid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_outward_inquiry`.`outward_typeid` INNER JOIN `db_employee` ON `db_outward_inquiry`.`employeeid` = `db_employee`.`employeeid` WHERE `db_outward_inquiry`.`employeeid` = '$employeeid' AND `db_outward_inquiry`.`status` = '0' AND `db_outward_inquiry`.`inquiryid` NOT IN (SELECT `inquiryid` FROM `db_outward_inquiry_orderlist` GROUP BY `inquiryid`) $sqlwhere";
}
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_specification`.`mould_no` DESC,`db_mould_material`.`materialid` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
//$_SESSION['outward_inquiry_list'] = $sql;
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
<div id="table_search">
  <h4>外协加工询价单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料编号：</th>
        <td><input type="text" name="material_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
         <!--  <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_outward_inquiry.php'" /></td> -->
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="outward_inquiry_do.php" name="material_inquiry_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">模具编号</th>
    <!--     <th width="6%">料单编号</th>
        <th width="4%">料单序号</th> -->
        <th width="8%">物料编码</th>
        <th width="10%">物料名称</th>
        <th width="10%">规格</th>
        <th width="6%">数量</th>
        <th width="6%">材质</th>
        <th width="6%">硬度</th>
        <th width="6%">品牌</th>
        <th width="4%">申请人</th>
        <th width="6%">加工类型</th>
        <th width="12%">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $inquiryid = $row['inquiryid'];
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $inquiryid; ?>" /></td>
        <td><?php echo $row['mould_no']; ?></td>
     <!--    <td><?php echo $row['material_list_number']; ?></td>
        <td><?php echo $row['material_list_sn']; ?></td> -->
        <td><?php echo $row['material_number']; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $row['material_quantity']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['hardness']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['outward_typename']; ?></td>
        <td><?php echo $row['outward_remark']; ?></td>
      </tr>
      <?php } ?>
    </table>

    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>