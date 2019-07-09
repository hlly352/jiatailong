<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	$material_number = trim($_GET['material_number']);
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['specification']);
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_number` LIKE '%$material_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
}
$sql = "SELECT `db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_material`.`complete_status`,`db_mould`.`mould_number` FROM `db_mould_material` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` WHERE `db_mould_material`.`materialid` NOT IN (SELECT `materialid` FROM `db_material_inquiry` GROUP BY `materialid`) AND `db_mould_material`.`materialid` NOT IN (SELECT `materialid` FROM `db_material_order_list` GROUP BY `materialid`) $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould`.`mould_number` DESC,`db_mould_material`.`materialid` ASC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>待询模具物料</h4>
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
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="material_inquirydo.php" name="material_inquiry" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">模具编号</th>
        <th width="6%">下单日期</th>
        <th width="6%">料单编号</th>
        <th width="4%">料单序号</th>
        <th width="10%">物料编码</th>
        <th width="12%">物料名称</th>
        <th width="12%">规格</th>
        <th width="6%">数量</th>
        <th width="6%">材质</th>
        <th width="6%">硬度</th>
        <th width="6%">品牌</th>
        <th width="4%">备件数量</th>
        <th width="12%">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $materialid = $row['materialid'];
		  $complete_status = $row['complete_status'];
		  $material_name_bg = $complete_status?'':" style=\"background:yellow\"";
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $materialid; ?>"<?php if($complete_status == 0) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_date']; ?></td>
        <td><?php echo $row['material_list_number']; ?></td>
        <td><?php echo $row['material_list_sn']; ?></td>
        <td><?php echo $row['material_number']; ?></td>
        <td<?php echo $material_name_bg; ?>><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $row['material_quantity']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['hardness']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['spare_quantity']; ?></td>
        <td><?php echo $row['remark']; ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="添加" class="select_button" />
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
</body>
</html>
<!--刀具待询-->
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
if($_GET['submit']){
  $purchase_number = rtrim($_GET['purchase_number']);
  $specification = trim($_GET['specification']);
  $typeid = $_GET['typeid'];
  if($typeid){
    $sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
  }
  $sqlwhere = " AND `db_cutter_purchase`.`purchase_number` LIKE '%$purchase_number%' AND `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid";
}
$sql = "SELECT `db_cutter_purchase_list`.`purchase_listid`,`db_cutter_purchase_list`.`quantity`,`db_cutter_purchase_list`.`plan_date`,`db_cutter_purchase_list`.`remark`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,`db_cutter_purchase`.`purchase_number`,`db_cutter_purchase`.`purchase_date`,`db_employee`.`employee_name` FROM `db_cutter_purchase_list` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_purchase_list`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_purchase`.`employeeid` WHERE `db_cutter_purchase_list`.`purchase_listid` NOT IN (SELECT `purchase_listid` FROM `db_cutter_inquiry` GROUP BY `purchase_listid`) AND `db_cutter_purchase_list`.`purchase_listid` NOT IN (SELECT `purchase_listid` FROM `db_cutter_order_list` GROUP BY `purchase_listid`) $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_purchase`.`purchaseid` DESC,`db_cutter_purchase_list`.`purchase_listid` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>采购管理-希尔林</title>
</head>

<body>
<div id="table_search">
  <h4>待询加工刀具</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>申购单号：</th>
        <td><input type="text" border="purchase_number" class="input_txt" /></td>
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
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="cutter_inquirydo.php" name="cutter_inquiry" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="8%">申购单号</th>
        <th width="6%">类型</th>
        <th width="12%">规格</th>
        <th width="6%">材质</th>
        <th width="10%">硬度</th>
        <th width="6%">品牌</th>
        <th width="6%">供应商</th>
        <th width="6%">数量</th>
        <th width="4%">单位</th>
        <th width="6%">申购人</th>
        <th width="8%">申购日期</th>
        <th width="8%">计划回厂日期</th>
        <th width="10%">备注</th>
      </tr>
      <?php while($row = $result->fetch_assoc()){ ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $row['purchase_listid']; ?>" /></td>
        <td><?php echo $row['purchase_number']; ?></td>
        <td><?php echo $row['type']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
        <td><?php echo $row['hardness']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td>件</td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['purchase_date']; ?></td>
        <td><?php echo $row['plan_date']; ?></td>
        <td><?php echo $row['remark']; ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="添加" class="select_button" disabled="disabled" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无数据！</p>";
  }
  ?>
</div>
</body>
</html>
<!--期间物料待询-->
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once '../class/page.php';
require_once 'shell.php';
//查询部门
$sql_department = "SELECT `deptid`,`dept_name` FROM `db_department` ORDER BY `deptid` ASC";
$result_department = $db->query($sql_department);
if($_GET['submit']){
    $material_name = trim($_GET['material_name']);
    $material_specification = trim($_GET['material_specification']);
    $apply_team = trim($_GET['apply_team']);
    $material_type = trim($_GET['material_type']);

   $sqlwhere = " AND `material_name` LIKE '%$material_name%' AND `material_specification` LIKE '%$material_specification%' AND `material_type` LIKE '%$material_type%' AND `apply_team` LIKE '%$apply_team%'";
}

$sql = "SELECT * FROM `db_mould_other_material` WHERE `status`='B' $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['mould_material_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_other_material`.`add_time` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>模具物料-希尔林</title>
</head>

<body>
<div id="table_search">
 <h4>待询期间物料</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>物料规格：</th>
        <td><input type="text" name="material_specification" class="input_txt" /></td>
        <th>申请部门：</th>
        <td>
          <select name="apply_team" class="input_txt txt">
              <option value="">所有</option>
              <?php 
                if($result_department->num_rows){
                  while($depart = $result_department->fetch_assoc()){
                    echo '<option value="'.$depart['deptid'].'">'.$depart['dept_name'].'</option>';
                  }
                }
              ?>
          </select>
        </td>
        <th>类型：</th>
        <td>
            <select name="material_type" class="input_txt txt">
              <option value="">所有</option>
              <?php
              foreach($array_mould_other_material as $key=>$value){
                echo "<option value=\"".$key."\">".$value."</option>";
                }
              ?>
            </select>
        </td>
        <td>
            <input type="submit" name="submit" value="查询" class="button" />
        </td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
    while($row_id = $result_id->fetch_assoc()){
      $array_materialid .= $row_id['materialid'].',';
    }
    $array_materialid = rtrim($array_materialid,',');
    $sql_order = "SELECT `materialid` FROM `db_material_order_list` WHERE `materialid` IN ($array_materialid) GROUP BY `materialid`";
    $result_order = $db->query($sql_order);
    if($result_order->num_rows){
      while($row_order = $result_order->fetch_assoc()){
        $array_order[] = $row_order['materialid'];
      }
    }else{
      $array_order = array();
    }
  ?>
  <form action="other_material_inquiry.php" name="mould_material_list" method="post">
    <table>
      <tr>
        <th width="">ID</th>
        <th width="">申请日期</th>
        <th width="">需求日期</th>
        <th width="">模具编号</th>
        <th width="">物料类型</th>
        <th width="">物料名称</th>
        <th width="">物料规格</th>
        <th width="">申购量</th>
        <th width="">单位</th>
        <th width="">库存量</th>
        <th width="">申请人</th>
        <th width="">申请部门</th>
        <th width="">备注</th>
        <th width="5%">状态</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
        //查询组别
        $sql_department = "SELECT `dept_name` FROM `db_department` WHERE `deptid`=".$row['apply_team'];
        $result_department = $db->query($sql_department);
        if($result_department->num_rows){
          $apply_team = $result_department->fetch_row();
        }
      //查询申请人
       $sql_applyer = "SELECT `employee_name` FROM `db_employee` WHERE `employeeid`=".$row['applyer'];
       $result_applyer = $db->query($sql_applyer);
       if($result_applyer->num_rows){
        $applyer = $result_applyer->fetch_row();
       }
      //如果是未审批状态，则可以点击审批
       if($row['status'] == 'A'){
        $status = '<a href="mould_other_material_apply.php?action=edit&id='.$row['mould_other_id'].'">'.$array_mould_material_status[$row['status']].'</a>';
       } else {
        $status = $array_mould_material_status[$row['status']];
       }
    ?>
     <tr>
        <td>
            <input type="checkbox" name="id[]" value="<?php echo $row['mould_other_id']; ?>"<?php if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> />
        </td>
        <td><?php echo $row['apply_date']; ?></td>
        <td><?php echo $row['requirement_date']; ?></td>
        <td><?php echo $row['mould_no']; ?></td>
        <td><?php echo $array_mould_other_material[$row['material_type']]; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['material_specification']; ?></td>
        <td><?php echo $row['quantity'] ?></td>
        <td><?php echo $row['unit']; ?></td>
        <td><?php echo $row['stock']; ?></td>
        <td><?php echo $applyer[0]; ?></td>
        <td><?php echo $apply_team[0]; ?></td>
        <td><?php echo $row['remark']; ?></td>
        <td><?php echo $status ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="hidden" name="action" value="add" />
      <input type="submit" name="submit" id="submit" value="添加" class="select_button"  disabled="disabled" />
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