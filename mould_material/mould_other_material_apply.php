<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
 $employee_name = $_SESSION['employee_info']['employee_name'];
$array_system_shell = $_SESSION['system_shell'][$system_dir];
$action = fun_check_action($_GET['action']);
//查询部门
$sql_department = "SELECT `deptid`,`dept_name` FROM `db_department` ORDER BY `deptid` ASC";
$result_department = $db->query($sql_department);
//查找期间物料类型
$sql_type = "SELECT * FROM `db_other_material_type`";
$result_type = $db->query($sql_type);
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
<script language="javascript" type="text/javascript">
$(function(){
  //获取默认分类下的商品名称
  function get_type(){
  var material_type = $("#material_type").val();
  var dataid = $("#dataid").val();
  $.post('../ajax_function/get_other_material_name.php',{material_type:material_type},function(data){
    $("#material_name").empty();
    for(var k in data){
      var is_select = dataid == data[k].dataid ?'selected':'';
      var inp = '<option '+is_select+' value="'+data[k].dataid+'">'+data[k].material_name+'</option>';
      $('#material_name').append(inp);
    }
  },'json')
}
  get_type();
  //物料类型更改时变更物料名称
  $("#material_type").live('change',function(){
    get_type();
  })
	$("#submit").click(function(){
		var part_number = $("#part_number").val();
		if(!$.trim(part_number)){
			$("#part_number").focus();
			return false;
		}
		var workteamid = $("#workteamid").val();
		if(!workteamid){
			$("#workteamid").focus();
			return false;
		}
		var order_number = $("#order_number").val();
		if(!$.trim(order_number)){
			$("#order_number").focus();
			return false;
		}else if(order_number.length != 7){
			$("#order_number").focus();
			return false;
		}
		var quantity = $("#quantity").val();
		if(!ri_b.test($.trim(quantity))){
			$("#quantity").focus();
			return false;
		}
		var supplierid = $("#supplierid").val();
		if(!supplierid){
			$("#supplierid").focus();
			return false;
		}
		var outward_typeid = $("#outward_typeid").val();
		if(!outward_typeid){
			$("#outward_typeid").focus();
			return false;
		}
		var cost = $("#cost").val();
		if(!rf_a.test($.trim(cost))){
			$("#cost").focus();
			return false;
		}
		var applyer = $("#applyer").val();
		if(!$.trim(applyer)){
			$("#applyer").focus();
			return false;
		}
		var inout_status = $("#inout_status").val();
		if(!$.trim(inout_status)){
			$("#inout_status").focus();
			return false;
		}
		var actual_date = $("#actual_date").val();
		if(inout_status == 1 && actual_date == '0000-00-00'){
			alert('请选择实际回厂时间');
			return false;
		}
	})
	$("#inout_status").change(function(){
		var inout_status = $(this).val();
		if(inout_status == 1){
			$("#actual_date").attr('disabled',false);
		}else if(inout_status == 0){
			$("#actual_date").attr('disabled',true);
		}
	})
})
</script>
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
  <div id="table_sheet">
  <?php
  if($action == "add"){
    //员工的下属
    $sql_employee = "SELECT `employeeid`,`employee_name` FROM `db_employee` WHERE `superior` = '$employeeid' AND `employee_status`= 1 AND `account_status` = 0 ORDER BY CONVERT(`employee_name` USING 'GBK') COLLATE 'GBK_CHINESE_CI' ASC";
    $result_employee = $db->query($sql_employee);
    //查找当前员工的部门
    $sql_employee_dept = 'SELECT `deptid` FROM `db_employee` WHERE `employeeid`='.$employeeid;
    $res_employee_dept = $db->query($sql_employee_dept);
    if($res_employee_dept->num_rows){
      $employee_dept = $res_employee_dept->fetch_row()[0];
    }
  ?>
  <h4>期间物料申请</h4>
  <form action="mould_other_materialdo.php" name="mould_other_material" method="post">
    <table >
      <tr>
        <th width="10%">申请时间：</th>
        <td width="15%">
            <input type="text" name="apply_date" value="<?php echo date('Y-m-d'); ?>"readOnly id="order_number" class="input_txt" />
        </td>
        <th width="10%">需求时间：</th>
        <td width="15%">
            <input type="text" name="requirement_date" value="<?php echo date('Y-m-d',strtotime(date('Y-m-d',time())."+5 day")); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" id="order_number" class="input_txt" />
        </td>
        <th>物料类型：</th>
        <td>
          <select name="material_type" id="material_type" class="input_txt txt">
              <?php
                if($result_type->num_rows){
                  while($row_type = $result_type->fetch_assoc()){
                    echo '<option value="'.$row_type['material_typeid'].'">'.$row_type['material_typename'].'</option>';
                  }
                }
              ?>
          </select>
        </td>
        <th width="10%">物料名称：</th>
        <td width="15%">
          <select name="material_name" id="material_name" class="input_txt txt"></select>
        </td>
      </tr>
      <tr>
        <th width="10%">物料规格：</th>
        <td width="15%">
          <input type="text" name="material_specification"  class="input_txt" />
        </td>
        <th width="10%">数量：</th>
        <td width="15%">
          <input type="text" name="quantity" id="quantity" class="input_txt" />
        </td>
        <th>单位：</th>
        <td>
          <input type="text" name="unit" class="input_txt" />
        </td>
        <th>标准库存：</th>
        <td>
          <input type="text" name="standard_stock" class="input_txt"/>
        </td>
      </tr>
      <tr>
        <th>库存量：</th>
        <td>
          <input type="text" name="stock" class="input_txt"/>
        </td>
         <th>申请人：</th>
           <td width=""><select name="applyer" class="input_txt txt">
            <option value="<?php echo $employeeid; ?>"><?php echo $employee_name; ?></option>
            <?php
      if($result_employee->num_rows){
        while($row_employee = $result_employee->fetch_assoc()){
          echo "<option value=\"".$row_employee['employeeid']."\">".$row_employee['employee_name']."</option>";
        }
      }
      ?>
          </select>
          <!-- <span class="tag"> *如需代理申请请下拉选择</span> -->
        </td>
        <th>申请部门：</th>
        <td><select name="apply_team" id="apply_team" class="input_txt txt">
            <option value="">请选择</option>
            <?php
            if($result_department->num_rows){
        while($row_department = $result_department->fetch_assoc()){
          $is_select = $row_department['deptid'] == $employee_dept?'selected':'';
          echo "<option ".$is_select." value=\"".$row_department['deptid']."\">".$row_department['dept_name']."</option>";
        }
      }
      ?>
          </select>
        </td>
         <th>备注：</th>
        <td colspan="3">
          <input type="text" name="remark" class="input_txt" />
        </td>
      </tr>
      <tr>
        <td colspan="8" style="text-align:center">
          <input type="submit" name="submit" id="submi" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" />
        </td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){

	  $material_id = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_mould_other_material` WHERE `mould_other_id`= '$material_id'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $row = $result->fetch_assoc();
       //查找申请人的信息
    $sql_applyer = "SELECT `employee_name` FROM `db_employee` WHERE `employeeid` =".$row['applyer'];
    $result_applyer = $db->query($sql_applyer);
    if($result_applyer->num_rows){
      $applyer_name = $result_applyer->fetch_row()[0];
    }
  ?>
  <h4>期间物料审批</h4>
  <form action="mould_other_materialdo.php" name="mould_outward" method="post">
   <table>
       <tr>
        <th width="10%">申请时间：</th>
        <td width="15%">
            <input type="text" name="apply_date" value="<?php echo $row['apply_date'] ?>" class="input_txt" />
        </td>
        <th width="10%">需求时间：</th>
        <td width="15%">
            <input type="text" name="requirement_date" value="<?php echo date('Y-m-d',strtotime(date('Y-m-d',time())."+5 day")); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" id="order_number" class="input_txt" />
        </td>
        <th>物料类型：</th>
        <td>
          <select name="material_type" id="material_type" class="input_txt txt">
              <?php
                if($result_type->num_rows){
                  while($row_type = $result_type->fetch_assoc()){
                    $is_select = $row_type['material_typeid'] == $row['material_type']?'selected':'';
                    echo '<option '.$is_select.' value="'.$row_type['material_typeid'].'">'.$row_type['material_typename'].'</option>';
                  }
                }
              ?>
          </select>
        </td>
        <th width="10%">物料名称：</th>
        <td width="15%">
          <select id="material_name" class="input_txt txt" name="material_name"></select>
          <input type="hidden" id='dataid' value="<?php echo $row['material_name'] ?>" />
        </td>
      </tr>
      <tr>
        <th width="10%">物料规格：</th>
        <td width="15%">
          <input type="text" name="material_specification"  class="input_txt" value="<?php echo $row['material_specification'] ?>" />
        </td>
        <th width="10%">数量：</th>
        <td width="15%">
          <input type="text" name="quantity" id="quantity" class="input_txt" value="<?php echo $row['quantity'] ?>" />
        </td>
        <th>单位：</th>
        <td>
          <input type="text" name="unit" value="<?php echo $row['unit'] ?>" class="input_txt" />
        </td>
        <th>标准库存：</th>
        <td>
          <input type="text" name="standard_stock" value="<?php echo $row['standard_stock'];?>" class="input_txt"/>
        </td>    
      <tr>
        <th>库存量：</th>
        <td>
          <input type="text" name="stock" value="<?php echo $row['stock']; ?>" class="input_txt"/>
        </td>
        <th>申请人：</th>
           <td width="">
              <input type="text" value="<?php echo $applyer_name ?>" readOnly class="input_txt"/>
          <!-- <span class="tag"> *如需代理申请请下拉选择</span> -->
        </td>
        <th>申请部门：</th>
        <td><select name="apply_team" id="apply_team" class="input_txt txt">
            <option value="">请选择</option>
            <?php
            if($result_department->num_rows){
        while($row_department = $result_department->fetch_assoc()){
          $is_select = $row_department['deptid'] == $row['apply_team']?'selected':'';
          echo "<option ".$is_select." value=\"".$row_department['deptid']."\">".$row_department['dept_name']."</option>";
        }
      }
      ?>
          </select>
        </td>
        <th>备注：</th>
        <td colspan="3">
          <input type="text" name="remark" value="<?php echo $row['remark'] ?>" class="input_txt" />
        </td>
      </tr>
      <tr>
        <td colspan="8" style="text-align:center">
          <input type="hidden" name="mould_other_id" value="<?php echo $row['mould_other_id'] ?>" />
          <input type="submit" name="submit" id="submi" value="通过" class="button" />
          &nbsp;
          <input type="submit" name="submit" id="submi" value="退回" class="button" />
          &nbsp;
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>