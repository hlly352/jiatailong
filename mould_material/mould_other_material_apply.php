<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';

$employeeid = $_SESSION['employee_info']['employeeid'];
$employee_name = $_SESSION['employee_info']['employee_name'];
$array_system_shell = $_SESSION['system_shell'][$system_dir];
$isconfirm = $array_system_shell['isconfirm'];
$isadmin = $array_system_shell['isadmin'];
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
  //获取默认分类下的商品名称
  function get_type(){
    var material_type = $("#material_type").val();
    var dataid = $('#dataid').val();
    //获取物料名称
    $.post('../ajax_function/get_other_material_name.php',{material_type:material_type},function(data){
      //获取默认的物料规格
      // var materialid = data[0].dataid;
      // get_specification(materialid);
      //填充类型选项卡
      $("#material_name").empty();
        var default_inp = '<option value="">--请选择--</option>';
        $('#material_name').append(default_inp);
      for(var k in data){
        var is_select = dataid == data[k].dataid ?'selected':'';
        var inp = '<option '+is_select+' value="'+data[k].dataid+'">'+data[k].material_name+'</option>';
        $('#material_name').append(inp);
        var material_name = $('#material_name option:selected').val();
        var dataid = material_name?material_name:dataid; 
      }
    },'json')
}
// function get_specification(dataid){
//     //获取物料规格信息
//     $.post('../ajax_function/get_other_material_name.php',{dataid:dataid},function(data){
//         //获取默认库存
//         var standard_stock = data[0].standard_stock;
//         var stock = data[0].stock;
//         $('#standard_stock').val(standard_stock);
//         $('#stock').val(stock);
//         //填充规格选项卡
//         $('#material_specification').empty();
//         get_stock(data[0].sepcificationid);
//          for(var k in data){
//           var is_select = dataid == data[k].specificationid ?'selected':'';
//           var inp = '<option '+is_select+' value="'+data[k].specificationid+'">'+data[k].specification_name+'</option>';
//           $('#material_specification').append(inp);
//         }

//     },'json')
// }
// function get_stock(specificationid){
//   //获取库存信息
//   $.post('../ajax_function/get_other_material_name.php',{specificationid:specificationid},function(data){
//       if(data){
//         var standard_stock = data['standard_stock'];
//         var stock = data['stock'];
//         if(standard_stock){
//          $('#standard_stock').val(standard_stock);
//       }
//       if(stock){
//         $('#stock').val(stock);
//       }
//     }
//   },'json')
// }
$(function(){
  get_type();
  //物料类型更改时变更物料名称
  $("#material_type").live('change',function(){
    get_type();
  })
  //更改物料名称时查规格和库存
  // $("#material_name").live('change',function(){
  //   var dataid = $(this).val();
  //   get_specification(dataid);
  // })
  // //更改物料规格查库存
  // $("#material_specification").live('change',function(){
  //   var specificationid = $(this).val();
  //   get_stock(specificationid);
  // })
	
})
</script>
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
  <script type="text/javascript">
    $("#submi").click(function(){
    
    var quantity = $("#quantity").val();
    if(!ri_b.test($.trim(quantity))){
      $("#quantity").focus();
      alert('数量异常');
      return false;
    }
    var unit = $("input[name = unit]").val();
    if(!unit){
      $("input[name = unit]").focus();
      alert('请填写单位');
      return false;
    }
      
  })

  </script>
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
  <?php if($isconfirm == 1){ ?>
  <div id="table_search">
  <h4>期间物料申购</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>物料类型：</th>
        <td>
          <select name="material_type" id="material_type" class="input_txt txt">
            <option value="">--请选择--</option>       
            <?php if($result_type->num_rows){ 
              while($row_type = $result_type ->fetch_assoc()){
              echo '<option value="'.$row_type['material_typeid'].'">'.$row_type['material_typename'].'</option>';
                }
             } ?>
          </select>
        </td>
        <th>物料名称：</th>
        <td>
          <select name="material_name" id="material_name" class="input_txt txt">
          </select>
        </td>
        <th>库存量：</th>
        <td>
          <select name="stock_status" class="input_txt txt">
              <option value="">所有</option>
              <option value="A">零库存</option>
              <option value="B">低于标准库存</option>
          </select>
        </td>
        <input type="hidden" value="add" name="action"/>
        <td>
            <input type="submit" name="submit" value="查询" class="button" />
        <!--     <input type="button" id="excel_material" name="button" value="导出" class="button" onclick="location.href='excel_mould_other_material.php'" /> -->
        </td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
    if($_GET['submit']){
     $material_type = trim($_GET['material_type']);
     $material_name = trim($_GET['material_name']);
     $stock_status  = trim($_GET['stock_status']);
     if($material_type){
      $wheresql = "WHERE `db_other_material_type`.`material_typeid` = '$material_type'";
     }
     if($material_name){
      $wheresql .= " AND `db_other_material_specification`.`materialid` = '$material_name'";
     }
     if($stock_status == 'A'){
      $sql_status = " AND `db_other_material_specification`.`stock` = 0";
     }elseif($stock_status == 'B'){
      $sql_status = " AND (`db_other_material_specification`.`standard_stock` - `db_other_material_specification`.`stock`) > 0";
     }
     $wheresql .= $sql_status;
    }
    $sql = "SELECT `db_other_material_specification`.`specificationid`,`db_other_material_type`.`material_typename`,`db_other_material_data`.`material_name`,`db_other_material_specification`.`specification_name`,`db_other_material_specification`.`stock`,`db_other_material_specification`.`standard_stock`,`db_other_material_data`.`unit` FROM `db_other_material_specification` INNER JOIN `db_other_material_data` ON `db_other_material_specification`.`materialid` = `db_other_material_data`.`dataid` INNER JOIN `db_other_material_type` ON `db_other_material_data`.`material_typeid` = `db_other_material_type`.`material_typeid` $wheresql";
    $result = $db->query($sql);
    $pages = new page($result->num_rows,20);
    $sqllist = $sql . " ORDER BY `db_other_material_specification`.`specificationid` DESC" . $pages->limitsql;
    $result = $db->query($sqllist);
    if($result->num_rows){
  ?>
  <form action="material_control_apply.php" name="mould_other_material" method="post">
    <table>
      <tr>
        <th width="">ID</th>
        <th width="">物料类型</th>
        <th width="">物料名称</th>
        <th width="">物料规格</th>
        <th width="">库存量</th>
        <th width="">标准库存</th>
        <th width="">单位</th>
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
            <input type="checkbox" name="id[]" value="<?php echo $row['specificationid']; ?>"<?php if($row['stock'] >= $row['standard_stock'] && $row['stock']>0) echo " disabled=\"disabled\""; ?> />
        </td>
        <td><?php echo $row['material_typename']; ?></td>
        <td><?php echo is_numeric($row['material_name'])?$row['name']:$row['material_name']; ?></td>
        <td><?php echo $row['specification_name']; ?></td>
        <td><?php echo $row['stock']; ?></td>
        <td><?php echo $row['standard_stock']; ?></td>
        <td><?php echo $row['unit']; ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="申购" class="select_button" disabled="disabled" />
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
      <?php }else{ ?>
    <div id="table_sheet">
      <table>
      <form action="mould_other_materialdo.php" method="post">
      <tr>
        <th width="10%">申请时间：</th>
        <td width="15%">
            <input type="text" name="apply_date" value="<?php echo date('Y-m-d'); ?>"readOnly id="order_number" class="input_txt" />
        </td>
        <th width="10%">需求时间：</th>
        <td width="15%">
            <input type="text" name="requirement_date" value="<?php echo date('Y-m-d',strtotime(date('Y-m-d',time())."+5 day")); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" id="order_number" class="input_txt" />
        </td>
        <th width="10%">物料名称：</th>
        <td width="15%">
          <input type="text" name="material_name" class="input_txt" />
        </td>
        <th width="10%">数量：</th>
        <td width="15%">
          <input type="text" name="quantity" id="quantity" class="input_txt" />
        </td>
      </tr>
      <tr>
        <th>单位：</th>
        <td>
          <input type="text" name="unit" class="input_txt" />
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

      <?php }?>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
    $to = $_GET['to'];
	  $material_id = fun_check_int($_GET['id']);
    $sql = "SELECT `db_mould_other_material`.`apply_date`,`db_mould_other_material`.`requirement_date`,`db_other_material_data`.`material_name`,`db_mould_other_material`.`unit` AS `material_unit`,`db_other_material_data`.`unit`,`db_other_material_specification`.`standard_stock`,`db_other_material_specification`.`stock`,`db_other_material_type`.`material_typename`,`db_other_material_specification`.`specification_name`,`db_mould_other_material`.`quantity`,`db_mould_other_material`.`remark`,`db_employee`.`employee_name`,`db_department`.`dept_name`,`db_mould_other_material`.`material_name` AS `name` FROM `db_mould_other_material` INNER JOIN `db_employee` ON `db_mould_other_material`.`applyer` = `db_employee`.`employeeid` INNER JOIN `db_department` ON `db_mould_other_material`.`apply_team` = `db_department`.`deptid` LEFT JOIN `db_other_material_specification` ON `db_mould_other_material`.`material_name` = `db_other_material_specification`.`specificationid` LEFT JOIN `db_other_material_data` ON `db_other_material_data`.`dataid` = `db_other_material_specification`.`materialid` LEFT JOIN `db_other_material_type` ON `db_other_material_data`.`material_typeid` = `db_other_material_type`.`material_typeid` WHERE `db_mould_other_material`.`mould_other_id` = '$material_id'";
	  $result = $db->query($sql); 
	  if($result->num_rows){
		  $row = $result->fetch_assoc();
      $material_unit = $row['material_unit'];

  ?>
  <div id="table_sheet">
  <h4>期间物料审批</h4>
  <form action="mould_other_materialdo.php" name="mould_outward" method="post">
   <table >
      <?php if(!$material_unit){ ?>
      <tr>
        <th width="10%">申请时间：</th>
        <td width="15%">
           <?php echo $row['apply_date'] ?>
        </td>
        <th width="10%">需求时间：</th>
        <td width="15%">
             <?php echo $row['requirement_date'] ?>
        </td>
        <th>物料类型：</th>
        <td>
         <?php echo $row['material_typename'] ?>
        </td>
        <th width="10%">物料名称：</th>
        <td width="15%">
          <?php echo $row['material_name'] ?>
        </td>
      </tr>
      <tr>
        <th width="10%">物料规格：</th>
        <td width="15%">
          <?php echo $row['specification_name'] ?>
        </td>
        <th width="10%">数量：</th>
        <td width="15%">
          <?php echo $row['quantity'] ?>
        </td>
        <th>单位：</th>
        <td>
          <?php echo $row['unit'] ?>
        </td>
        <th>标准库存：</th>
        <td>
          <?php echo $row['standard_stock'] ?>
        </td>
      </tr>
      <tr>
        <th>库存量：</th>
        <td>
          <?php echo $row['stock'] ?>
        </td>
         <th>申请人：</th>
           <td width="">
           <?php echo $row['employee_name'] ?>
        </td>
        <th>申请部门：</th>
        <td>
          <?php echo $row['dept_name'] ?>
        </td>
         <th>备注：</th>
        <td colspan="3">
          <input type="text" name="remark" value="<?php echo $row['remark'] ?>" />
        </td>
      </tr>
      <input type="hidden" name="mould_other_id" value="<?php echo $row['mould_other_id'] ?>" />
      
      <?php }else{ ?>
              <tr>
        <th width="10%">申请时间：</th>
        <td width="15%">
            <?php echo $row['apply_date'] ?>
        </td>
        <th width="10%">需求时间：</th>
        <td width="15%">
           <?php echo $row['requirement_date'] ?>
        </td>
        <th width="10%">物料名称：</th>
        <td width="15%">
          <?php echo $row['name'] ?>
        </td>
        <th width="10%">数量：</th>
        <td width="15%">
          <?php echo $row['quantity'] ?>
        </td>
      </tr>
      <tr>
        <th>单位：</th>
        <td>
          <?php echo $row['material_unit'] ?>
        </td>
         <th>申请人：</th>
           <td width=""> 
              <?php echo $row['employee_name'] ?>
           </td>
        <th>申请部门：</th>
        <td>
          <?php echo $row['dept_name'] ?>
        </td>
         <th>备注：</th>
        <td colspan="3">
          <input type="text" name="remark" value="<?php echo $row['remark'] ?>" />
        </td>
          <input type="hidden" name="mould_other_id" value="<?php echo $row['mould_other_id'] ?>" />
      </tr>

      <?php }?>
      <tr>
         <td colspan="8" style="text-align:center">
          <input type="submit" name="submit" id="submi" value="通过" class="button" />
          &nbsp;
          <input type="submit" name="submit" id="submi" value="退回" class="button" />
          &nbsp;
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="mould_other_id" value="<?php echo $material_id; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" />
          <input type="hidden" name="to" value="<?php echo $to ?>" />
        </td>
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