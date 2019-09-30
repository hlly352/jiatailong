<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
  $material_name = trim($_GET['material_name']);
  $specification = trim($_GET['specification']);
  if($specification){
    $specification_str = "AND `db_other_material_specification`.`specification_name` LIKE '%$specification%'";
  }
  $sqlwhere = " AND (`db_other_material_specification`.`material_name` LIKE '%$material_name%' OR `db_other_material_data`.`material_name` LIKE '%$material_name%' ) $specification_str";
} 
  //查询所有可出库物料
  $sql = "SELECT `db_other_material_specification`.`specificationid`,`db_other_material_specification`.`materialid`,`db_other_material_specification`.`type`,`db_other_material_specification`.`specification_name`,`db_other_material_specification`.`material_name`,`db_other_material_specification`.`stock`,`db_other_material_specification`.`last_date` FROM `db_other_material_specification` LEFT JOIN `db_mould_other_material` ON `db_mould_other_material`.`mould_other_id` = `db_other_material_specification`.`materialid` LEFT JOIN `db_other_material_data` ON `db_other_material_specification`.`materialid` = `db_other_material_data`.`dataid` WHERE `db_other_material_specification`.`stock` >0 AND (`db_other_material_specification`.`last_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_other_material_specification`.`specificationid` ASC" . $pages->limitsql;
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
  <h4 class="tit">
    <a href="other_material_out_list.php">
      <input type="button" value="期间物料出库" class="butn blue">
    </a>
    <a href="other_material_outdown.php">
      <input type="button" value="期间物料出库单打印" class="butn">
    </a>
  </h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" size="15" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" size="15" /></td>
        <th>订单日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>
       <!--  <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
        while($row_supplier = $result_supplier->fetch_assoc()){
          echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
        }
      }
      ?>
          </select></td> -->
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="other_material_batch_out.php" name="material_batch_out" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="12%">物料名称</th>
        <th width="16%">规格</th>
        <th width="8%">库存</th>
        <th width="6%">单位</th>
        <th width="10%">订单日期</th>
        <th width="4%">Out</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
        if($row['type'] == 'A'){
          $sql_info = "SELECT `material_name`,`unit` FROM `db_other_material_data` WHERE `dataid` = ".$row['materialid'] ;
        }elseif($row['type'] == 'B'){
          $sql_info = "SELECT `unit` FROM `db_mould_other_material` WHERE `mould_other_id` = ".$row['materialid'];
        }
        $result_info = $db->query($sql_info);
        if($result_info->num_rows){
          $info = $result_info->fetch_assoc();
        }
        
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $row['specificationid']; ?>" /></td>
        <td><?php echo $row['material_name']?$row['material_name']:$info['material_name']; ?></td>
        <td><?php echo $row['specification_name']; ?></td>
        <td><?php echo $row['stock']; ?></td>
        <td><?php echo $info['unit']; ?></td>
        <td><?php echo $row['last_date']; ?></td>
        <td><a href="other_material_out_listout.php?id=<?php echo $listid; ?>&inout=<?php echo $row['inout_quantity'] ?>&inoutid=<?php echo $row['inoutid'] ?>&action=add"><img src="../images/system_ico/out_10_8.png" width="10" height="8" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="出库" class="select_button" />
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