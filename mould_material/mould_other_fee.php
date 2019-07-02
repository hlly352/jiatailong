<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$before_date = strtotime($sdate);
$after_date  = strtotime($edate);
//查询部门
$sql_department = "SELECT `deptid`,`dept_name` FROM `db_department` ORDER BY `deptid` ASC";
$result_department = $db->query($sql_department);
if($_GET['submit']){
    $material_name = trim($_GET['material_name']);
    $material_specification = trim($_GET['material_specification']);
    $apply_team = trim($_GET['apply_team']);
    $material_type = trim($_GET['material_type']);

	 $sqlwhere = " WHERE `material_name` LIKE '%$material_name%' AND `material_specification` LIKE '%$material_specification%' AND `material_type` LIKE '%$material_type%' AND `apply_team` LIKE '%$apply_team%' AND (`add_time` BETWEEN '$before_date' AND '$after_date')";
}

$sql = "SELECT * FROM `db_mould_other_material` $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['mould_other_material'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . "ORDER BY `add_time` DESC" . $pages->limitsql;
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
        <th>日期：</th>
        <td>
          <input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt"/>
          ---
          <input type="text"  name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt"/>
        </td>
        <td>
            <input type="submit" name="submit" value="查询" class="button" />
            <input type="button" id="excel_material" name="button" value="导出" class="button" onclick="location.href='excel_mould_other_material.php'" />
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
  <form action="mould_materialdo.php" name="mould_other_material" method="post">
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
        //获取当前物料的审批人
        if($row['approver'] == $employeeid){
          $approver = true;
        } else {
          $approver = false;
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
       if($row['status'] == 'A' && $approver){
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
    <!-- <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div> -->
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