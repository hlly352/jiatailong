<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$listid = $_GET['id'];
//查询加工类型
$outward_type_sql = "SELECT `outward_typeid`,`outward_typename` FROM `db_mould_outward_type` ORDER BY `outward_typename` ASC";
$result_outward_type = $db->query($outward_type_sql);

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
<title>采购管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>

<?php
if($_GET['submit']){
  $mould_number = trim($_GET['mould_number']);
  $material_name = trim($_GET['material_name']);
  $specification = trim($_GET['specification']);
  $outward_typeid = trim($_GET['outward_typeid']);
  $sqlwhere = " AND `db_mould_specification`.`mould_no` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' AND `db_outward_order`.`outward_typeid` LIKE '%$outward_typeid%'";
}
if($listid){
  $sqls = "WHERE `db_outward_inquiry_orderlist`.`listid` = '$listid'";
}

$sql = "SELECT `db_outward_inquiry_orderlist`.`back_date`,`db_employee`.`employee_name`,`db_mould_outward_type`.`outward_typename`,`db_outward_inquiry_orderlist`.`plan_date`,`db_outward_inquiry_orderlist`.`listid`,`db_outward_inquiry`.`outward_quantity`,`db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_outward_inquiry`.`outward_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_outward_inquiry`.`outward_remark`,`db_mould_material`.`complete_status`,`db_mould_specification`.`mould_no`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code` FROM `db_outward_inquiry_orderlist` INNER JOIN `db_outward_inquiry` ON `db_outward_inquiry_orderlist`.`inquiryid` = `db_outward_inquiry`.`inquiryid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_outward_inquiry`.`materialid` INNER JOIN `db_employee` ON `db_outward_inquiry`.`employeeid` = `db_employee`.`employeeid` INNER JOIN `db_mould_specification` ON `db_mould_specification`.`mould_specification_id` = `db_mould_material`.`mouldid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_outward_inquiry`.`outward_typeid` $sqls";

$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql." ORDER BY `db_mould_specification`.`mould_no` DESC,`db_mould_material`.`materialid` ASC".$pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>外协加工明细</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>加工类型：</th>
        <td>
          <select class="outward_typeid" class="input_txt txt">
            <option value="">所有</option>
            <?php
              if($result_outward_type->num_rows){
                while($row_outward_type = $result_outward_type->fetch_assoc()){
                  echo '<option value="'.$row_outward_type['outward_typeid'].'">'.$row_outward_type['outward_typename'].'</option>';
                }
              }
            ?>
          </select>
        </td>
        <td><input type="hidden" name="id" value="<?php echo $orderid; ?>" />
          <input type="submit" name="submit" value="查询" class="button" />
         </td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
 ?>
  <form action="mould_outward_orderdo.php" name="material_order_list" method="post">
    <table>
           <tr>
       <th width="">ID</th>
        <th width="">模具编号</th>
<!--         <th width="">料单编号</th>
        <th width="">料单序号</th> -->
        <th width="">物料编码</th>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th width="">数量</th>
        <th width="">材质</th>
        <th width="">硬度</th>
        <th width="">品牌</th>
        <th width="">申请人</th>
        <th width="">加工类型</th>
        <th width="">计划回厂时间</th>
        <th width="">回厂时间</th>
        <th width="15%">备注</th>
      </tr>
      <?php
    $amount = 0;
    $process_cost = 0;
    $total_amount = 0;
      while($row = $result->fetch_assoc()){
        $listid = $row['listid'];
    ?>
      <tr>
        <td>
          <input type="checkbox" name="id[]" value="<?php echo $listid; ?>"<?php //if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> />
        </td>
        <td><?php echo $row['mould_no']; ?></td>
        <td><?php echo $row['material_number']; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $row['outward_quantity']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['hardness'] ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['employee_name'] ?></td>
        <td><?php echo $row['outward_typename'] ?></td>
        <td><?php echo $row['plan_date'] ?></td>
        <td><?php echo $row['back_date']; ?></td>
        <td><?php echo $row['outward_remark'] ?></td>
      </tr>
      <?php
    }
    ?>
      <tr>
      <?php
      $total_order_quantity += $row['order_quantity'];
      $total_amount += $row['amount'];
    
    ?>
      <!-- <tr>
        <td colspan="9">Total</td>
        <td><?php echo number_format($total_order_quantity,2); ?></td>
        <td></td>
        <td><?php echo number_format($total_amount,2); ?></td>
      </tr> -->
      <tr>
        <td colspan="14">
          <input type="button" onclick="window.location.href='mould_outward_order.php'" value="返回" class="button" />
        </td>
      </tr>
    </table>
  </form>
  <div id="page">
    <?php $pages->getPage(); ?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无物料记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>