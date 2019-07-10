<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>模具刀具-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php

  //类型
  $sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
  $result_cutter_type = $db->query($sql_cutter_type);
  $result_cutter_typeid = $db->query($sql_cutter_type);
  //品牌
  $sql_cutter_brand = "SELECT `brandid`,`brand` FROM `db_cutter_brand` ORDER BY `brandid` ASC";
  $result_cutter_brand = $db->query($sql_cutter_brand);
  //供应商
  $sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) > 0 ORDER BY `supplier_code` ASC";
  $result_supplier = $db->query($sql_supplier);
?>

<?php
if($_GET['submit']){
  $typeid = $_GET['typeid'];
  if($typeid){
    $sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
  }
  $specification = trim($_GET['specification']);
  $texture = $_GET['texture'];
  if($texture){
    $sql_texture = " AND `db_cutter_hardness`.`texture` = '$texture'";
  }
  $hardness = trim($_GET['hardness']);
  $sqlwhere = " WHERE `db_cutter_specification`.`specification` LIKE '%$specification%' AND `db_cutter_hardness`.`hardness` LIKE '%$hardness%' $sql_typeid $sql_texture";
}
$sql_cutter_list = "SELECT `db_mould_cutter`.`cutterid`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness` FROM `db_mould_cutter` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` $sqlwhere";
$result_cutter_list = $db->query($sql_cutter_list);
$pages = new page($result_cutter_list->num_rows,10);
$sql_cutter_list = $sql_cutter_list . " ORDER BY `db_cutter_specification`.`typeid` ASC,`db_cutter_hardness`.`texture` ASC,`db_mould_cutter`.`cutterid` DESC" . $pages->limitsql;
$result_cutter_list = $db->query($sql_cutter_list);
$result_id = $db->query($sql_cutter_list);
?>
<div id="table_search">
  <h4>刀具数据</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>类型：</th>
        <td><select name="typeid">
            <option value="">所有</option>
            <?php
      if($result_cutter_typeid->num_rows){
        while($row_cutter_typeid = $result_cutter_typeid->fetch_assoc()){
      ?>
            <option value="<?php echo $row_cutter_typeid['typeid']; ?>"<?php if($row_cutter_typeid['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_typeid['type']; ?></option>
            <?php
        }
      }
      ?>
          </select></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>材质：</th>
        <td><select name="texture">
            <option value="">所有</option>
            <?php foreach($array_cutter_texture as $texture_key=>$texture_value){ ?>
            <option value="<?php echo $texture_key; ?>"<?php if($texture_key == $texture) echo " selected=\"selected\"" ?>><?php echo $texture_value; ?></option>
            <?php } ?>
          </select></td>
        <th>硬度：</th>
        <td><input type="text" name="hardness" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="明细" class="button" onclick="location.href='cutter_purchase_list_info.php?purchaseid=<?php echo $purchaseid; ?>'" />
          <input type="hidden" name="purchaseid" value="<?php echo $purchaseid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result_cutter_list->num_rows){
    while($row_id = $result_id->fetch_assoc()){
      $array_cutterid .= $row_id['cutterid'].',';
    }
    $array_cutterid = rtrim($array_cutterid,',');
    $sql_surplus = "SELECT `db_cutter_purchase_list`.`cutterid`,SUM(`db_cutter_order_list`.`surplus`) AS `surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`cutterid` IN ($array_cutterid) AND `db_cutter_order_list`.`surplus` > 0 GROUP BY `db_cutter_purchase_list`.`cutterid`";
    $result_surplus = $db->query($sql_surplus);
    if($result_surplus->num_rows){
      while($row_surplus = $result_surplus->fetch_assoc()){
      $array_surplus[$row_surplus['cutterid']] = $row_surplus['surplus'];
      }
    }else{
      $array_surplus = array();
    }
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="18%">类型</th>
      <th width="24%">规格</th>
      <th width="18%">材质</th>
      <th width="22%">硬度</th>
      <th width="6%">库存</th>
      <th width="4%">单位</th>
      <th width="4%">Add</th>
    </tr>
    <?php
    while($row_cutter_list = $result_cutter_list->fetch_assoc()){
    $cutterid = $row_cutter_list['cutterid'];
    $surplus = array_key_exists($cutterid,$array_surplus)?$array_surplus[$cutterid]:0;
  ?>
    <tr>
      <td><?php echo $cutterid; ?></td>
      <td><?php echo $row_cutter_list['type']; ?></td>
      <td><?php echo $row_cutter_list['specification']; ?></td>
      <td><?php echo $array_cutter_texture[$row_cutter_list['texture']]; ?></td>
      <td><?php echo $row_cutter_list['hardness']; ?></td>
      <td><?php echo $surplus; ?></td>
      <td>件</td>
      <td><a href="cutter_purchase_list_add.php?purchaseid=<?php echo $purchaseid ?>&cutterid=<?php echo $cutterid ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
    </tr>
    <?php } ?>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无刀具数据！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>