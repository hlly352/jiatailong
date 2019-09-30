<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));

$isadmin = $_SESSION['system_shell'][$system_dir]['isadmin'];
$isconfirm = $_SESSION['system_shell'][$system_dir]['isconfirm'];
$before_date = strtotime($sdate);
$after_date  = strtotime($edate);
//查询部门
$sql_department = "SELECT `deptid`,`dept_name` FROM `db_department` ORDER BY `deptid` ASC";
$result_department = $db->query($sql_department);
//查找最终审批人的id
$sql_approver = "SELECT `employeeid` FROM `db_employee` WHERE `employee_name` LIKE '%何志武%'";
$result_approver = $db->query($sql_approver);
if($result_approver->num_rows){
  $approverid = $result_approver->fetch_row()[0];
}
if($_GET['submit']){
    $material_name = trim($_GET['material_name']);
    $material_specification = trim($_GET['material_specification']);
    $apply_team = trim($_GET['apply_team']);
    $material_type = trim($_GET['material_type']);

	 $sqlwhere = " AND `db_other_material_data`.`material_name` LIKE '%$material_name%' AND `db_mould_other_material``material_name` LIKE '%$material_name%' AND `db_other_material_specification`.`specification_name` LIKE '%$material_specification%' AND `material_type` LIKE '%$material_type%' AND `apply_team` LIKE '%$apply_team%' AND (`add_time` BETWEEN '$before_date' AND '$after_date')";
}

if($isadmin == 1 && $isconfirm == 1){
  $sql = "SELECT `db_mould_other_material`.`mould_other_id`,`db_mould_other_material`.`apply_date`,`db_mould_other_material`.`requirement_date`,`db_other_material_specification`.`specification_name`,`db_other_material_data`.`material_name` AS `data_name`,`db_other_material_specification`.`material_name`,`db_mould_other_material`.`unit` AS `material_unit`,`db_other_material_data`.`unit`,`db_other_material_specification`.`specification_name`,`db_mould_other_material`.`quantity`,`db_other_material_specification`.`stock`,`db_other_material_type`.`material_typename`,`db_mould_other_material`.`apply_team`,`db_mould_other_material`.`applyer`,`db_mould_other_material`.`remark`,`db_mould_other_material`.`status`,`db_mould_other_material`.`approver` FROM `db_mould_other_material`  LEFT JOIN `db_other_material_specification` ON `db_mould_other_material`.`material_name` = `db_other_material_specification`.`specificationid` LEFT JOIN `db_other_material_data` ON `db_other_material_data`.`dataid` = `db_other_material_specification`.`materialid` LEFT JOIN `db_other_material_type` ON `db_other_material_data`.`material_typeid` = `db_other_material_type`.`material_typeid` WHERE `db_mould_other_material`.`applyer` != 0 $sqlwhere";
}else{
  $sql = "SELECT `db_mould_other_material`.`mould_other_id`,`db_mould_other_material`.`apply_date`,`db_mould_other_material`.`requirement_date`,`db_other_material_specification`.`specification_name`,`db_other_material_data`.`material_name` AS `data_name`,`db_other_material_specification`.`material_name`,`db_mould_other_material`.`unit` AS `material_unit`,`db_other_material_data`.`unit`,`db_other_material_specification`.`specification_name`,`db_mould_other_material`.`quantity`,`db_other_material_specification`.`stock`,`db_other_material_type`.`material_typename`,`db_mould_other_material`.`apply_team`,`db_mould_other_material`.`applyer`,`db_mould_other_material`.`remark`,`db_mould_other_material`.`status`,`db_mould_other_material`.`approver` FROM `db_mould_other_material`  LEFT JOIN `db_other_material_specification` ON `db_mould_other_material`.`material_name` = `db_other_material_specification`.`specificationid` LEFT JOIN `db_other_material_data` ON `db_other_material_data`.`dataid` = `db_other_material_specification`.`materialid` LEFT JOIN `db_other_material_type` ON `db_other_material_data`.`material_typeid` = `db_other_material_type`.`material_typeid` WHERE (`db_mould_other_material`.`applyer` = '$employeeid' OR `db_mould_other_material`.`approver` = '$employeeid') $sqlwhere";
}

$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['mould_other_material'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . "ORDER BY `db_mould_other_material`.`add_time` DESC" . $pages->limitsql;
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
<script type="text/javascript">
  //导出
  $('#excel_material').live('click',function(){
    document.mould_other_material.action = 'excel_mould_other_material.php';
    document.mould_other_material.submit();
  })
</script>
<title>模具物料-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>期间物料</h4>
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
       <!--  <th>类型：</th>
        <td>
            <select name="material_type" class="input_txt txt">
              <option value="">所有</option>
              <?php
              foreach($array_mould_other_material as $key=>$value){
    			      echo "<option value=\"".$key."\">".$value."</option>";
        	    	}
        	   	?>
            </select>
        </td> -->
        <th>日期：</th>
        <td>
          <input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt"/>
          ---
          <input type="text"  name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt"/>
        </td>
        <td>
            <input type="submit" name="submit" value="查询" class="button" />
        <!--     <input type="button" id="excel_material" name="button" value="导出" class="button" onclick="location.href='excel_mould_other_material.php'" /> -->
            <input type="button" name="button" value="申请" class="button" onclick="location.href='mould_other_material_apply.php?action=add'" />

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
  <form action="mould_other_materialdo.php" name="mould_other_material" method="post">
    <table>
      <tr>
        <th width="">ID</th>
        <th width="">申请日期</th>
        <th width="">需求日期</th>
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
        //查询所有未通过的订单
        $sql_init = "SELECT `mould_other_id` FROM `db_mould_other_material` WHERE `status` = 'A'";
        $result_init = $db->query($sql_init);
        if($result_init->num_rows){
          $array_init_id = array();
          while($row_init = $result_init->fetch_assoc()){
            $array_init_id[] = $row_init['mould_other_id'];
           }
        }else{
          $array_init_id = array();
        }
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
        switch ($row['status'])
          {
            case 'A':
              if($row['approver'] == $employeeid && $row['approver'] != $approverid){
                $status = '<a href="mould_other_material_apply.php?to=B&action=edit&id='.$row['mould_other_id'].'">审核</a>';
              }elseif($row['approver'] == $approverid && $employeeid == $approverid){
                $status = '<a href="mould_other_material_apply.php?to=C&action=edit&id='.$row['mould_other_id'].'">审批</a>';
              }else{
                $status = '待审核';
              }
            break;
            case 'B':
              if($employeeid == $approverid){
                $status = '<a href="mould_other_material_apply.php?to=C&action=edit&id='.$row['mould_other_id'].'">审批</a>';
              }else{
                $status = '待审批';
              }
            break;
            default;
              $status = $array_mould_material_status[$row['status']];
          }
	  ?>
      <tr>
        <td>
            <input type="checkbox" name="id[]" value="<?php echo $row['mould_other_id']; ?>"<?php if(!($employeeid == $row['applyer'] || $employeeid == $row['approver']) || !(in_array($row['mould_other_id'],$array_init_id))) echo " disabled=\"disabled\""; ?> />
        </td>
        <td><?php echo $row['apply_date']; ?></td>
        <td><?php echo $row['requirement_date']; ?></td>
        <td><?php echo $row['material_typename']; ?></td>
        <td><?php echo $row['material_unit']?$row['material_name']:$row['data_name']; ?></td>
        <td><?php echo $row['specification_name']; ?></td>
        <td><?php echo $row['quantity'] ?></td>
        <td><?php echo $row['material_unit']?$row['material_unit']:$row['unit']; ?></td>
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
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
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