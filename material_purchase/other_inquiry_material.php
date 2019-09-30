<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
//查询部门
$sql_department = "SELECT `deptid`,`dept_name` FROM `db_department` ORDER BY `deptid` ASC";
$result_department = $db->query($sql_department);
//获取所有物料类型
$sql_type = "SELECT `material_typeid`,`material_typename` FROM `db_other_material_type` ORDER BY `material_typeid`";
$result_type = $db->query($sql_type);
if($_GET['submit']){
    $material_type = trim($_GET['material_type']);
    if($material_type){
        $sqlwhere = ' AND `db_other_material_data`.`material_typeid` ='.$material_type;
    }
    $material_name = trim($_GET['material_name']);
    if($material_name){
      $sqlwhere .= " AND (`db_mould_other_material`.`material_name` LIKE '%$material_name%' OR `db_other_material_data`.`material_name` LIKE '%$material_name%')";
    }

    $material_specification = trim($_GET['material_specification']);
    if($material_specification){
      $sqlwhere .=  " AND `db_other_material_specification`.`specification_name` LIKE '%$material_specification%'";
    }

    $apply_team = trim($_GET['apply_team']);
   $sqlwhere .= " AND `apply_team` LIKE '%$apply_team%' $sqltype";
}

    $sql = "SELECT `db_mould_other_material`.`mould_other_id`,`db_mould_other_material`.`apply_date`,`db_mould_other_material`.`requirement_date`,`db_other_material_type`.`material_typename`,`db_other_material_data`.`material_name` AS `data_name`,`db_other_material_specification`.`material_name`,`db_other_material_specification`.`specification_name`,`db_mould_other_material`.`quantity`,`db_other_material_data`.`unit`,`db_mould_other_material`.`unit` AS `material_unit`,`db_department`.`dept_name`,`db_mould_other_material`.`remark` FROM `db_mould_other_material` INNER JOIN `db_department` ON `db_mould_other_material`.`apply_team` = `db_department`.`deptid` LEFT JOIN `db_other_material_specification` ON `db_other_material_specification`.`specificationid` = `db_mould_other_material`.`material_name` LEFT JOIN `db_other_material_data` ON `db_other_material_specification`.`materialid` = `db_other_material_data`.`dataid` LEFT JOIN `db_other_material_type` ON `db_other_material_data`.`material_typeid` = `db_other_material_type`.`material_typeid` WHERE `db_mould_other_material`.`status` = 'E' AND `db_mould_other_material`.`inquiryid` = '$employeeid' AND `db_mould_other_material`.`mould_other_id` NOT IN(SELECT `materialid` FROM `db_other_material_orderlist`) $sqlwhere";

$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['other_material_inquiry'] = $sql;
$pages = new page($result->num_rows,20);
$sqllist = $sql . " ORDER BY `db_mould_other_material`.`add_time` DESC" . $pages->limitsql;
$_SESSION['excel_other_inquiry_material'] = $sqllist;
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
<?php include "header.php"; ?>
<div id="table_search">
 <h4>期间物料询价</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>类型：</th>
        <td>
            <select name="material_type" class="input_txt txt">
              <option value="">所有</option>
              <?php
                if($result_type->num_rows){
                  while($row = $result_type->fetch_assoc()){
                    echo '<option value="'.$row['material_typeid'].'">'.$row['material_typename'].'</option>';
                    $typeid .= $row['material_typeid'].',';
                  }
                }
                
                $typeid = rtrim($typeid,',');
              ?>
            </select>
        </td>
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
        <td>
          <input type="submit" name="submit" value="查询" class="button" />
          <input type="button" value="导出" class="button" onclick="window.location.href='excel_other_inquiry_material.php'"/>
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
        <th width="">申购日期</th>
        <th width="">需求日期</th>
        <th width="">物料类型</th>
        <th width="">物料名称</th>
        <th width="">物料规格</th>
        <th width="">申购数量</th>
        <th width="">单位</th>
        <th width="">部门</th>
        <th width="">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
       
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
        <td><?php echo $row['material_typename']; ?></td>
        <td><?php echo $row['material_unit']?$row['material_name']:$row['data_name']; ?></td>
        <td><?php echo $row['specification_name']; ?></td>
        <td><?php echo $row['quantity'] ?></td>
        <td><?php echo $row['material_unit']?$row['material_unit']:$row['unit']; ?></td>
        <td><?php echo $row['dept_name']; ?></td>
        <td><?php echo $row['remark']; ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button"  disabled="disabled" />
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