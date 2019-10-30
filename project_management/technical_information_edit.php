<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$specification_id = $_GET['specification_id'];
$from = $_GET['from'];
$mouldid = $_GET['mouldid'];
//查询模具信息
$mould_sql = "SELECT `project_name`,`mould_no`,`mould_name` FROM `db_mould_specification` WHERE `mould_specification_id` = '$specification_id'";
$result_mould = $db->query($mould_sql);
if($result_mould->num_rows){
  $mould_info = $result_mould->fetch_assoc();
}
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
//查询部门
$sql_dept = "SELECT * FROM `db_department` ORDER BY `deptid`";
$result_dept = $db->query($sql_dept);
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
  function get_doctype(){
    var data_type = $('#data_type').val();
    $.post('../ajax_function/get_doctype.php',{dataid:data_type},function(data){
      var select_doc = $('#radios');
      select_doc.empty();
      for(var k in data){
        var inp = '<label><input type="radio" checked name="doc_type" value="'+k+'" />'+data[k]+'</label> '
        select_doc.append(inp);
      }
    },'json')
  }
  //判断是否在数组中
  function is_arr(str,arr){
    var len = arr.length-1;
    while(len>=0){
      if(str === arr[len]){
      return true;
    }
      len--;

    }
    return false;
  }
  function get_employee(){
     var deptid = $('#dept').val();
    //获取当前部门的人员
    $.post('../ajax_function/get_dept_employee.php',{deptid:deptid},function(data){
      var select_num = $('.select_employee').size();
      var array_select = new Array();
      for(var j=0;j<select_num;j++){
        var employeeid = $('.select_employee').eq(j).attr('employeeid');
        array_select.push(employeeid);
      }
      $('#employee').empty();
      for(var i=0;i<data.length;i++){
        if(!is_arr(data[i].employeeid,array_select)){
          var span = '<span class="employee" id="employee_'+data[i].employeeid+'" style="padding:5px;cursor:pointer;">'+data[i].employee_name+'<span>';
          $('#employee').append(span);
       }
      }
    },'json')
  }
$(function(){
  get_doctype();
  $('#data_type').live('change',function(){
    get_doctype();
  })
  $("#submit").click(function(){
    var title = $.trim($('input[name = title]').val());
    var patt = /^.{1,8}$/;
    if(!title){
      alert('请填写标题');
      return false;
    }
    if(!patt.test(title)){
      alert('标题长度超过限制');
      return false;
    }
    var file = $('input[name = file]').val();
    if(!file){
      alert('请选择文件');
      return false;
    }
  })
  $('#dept').live('change',function(){
   get_employee();
  })
  $('.employee').live('click',function(){
     var id = $(this).attr('id');
     var employeeid = id.substr(id.lastIndexOf('_')+1);
     var name = $(this).html();
     var select_span = '<span class="select_employee" employeeid="'+employeeid+'" id="select_'+id+'" style="padding:5px;cursor:pointer;color:blue">'+name+'<input type="hidden" value="'+employeeid+'" name="employeeid[]"></span>';
     $('#select_employee').append(select_span);
     $(this).remove();
  })
  $('.select_employee').live('click',function(){
    $(this).remove();
    get_employee();
  })
})
</script>
<title>项目管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
      <script type="text/javascript">
        function ophiddenFile(){
          var dd = $("#hiddenFile").val().split("\\");
          $("#showFileName").val(dd[dd.length-1]);
          }
      </script>
  <h4>物料订单添加</h4>
  <form action="technical_information_do.php" name="material_order" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th width="20%">项目名称：</th>
        <td width="80%"><?php echo $mould_info['project_name'] ?></td>
      </tr>
      <tr>
        <th>模具编号：</th>
        <td><?php echo $mould_info['mould_no'] ?></td>
      </tr>
      <tr>
        <th>模具名称：</th>
        <td><?php echo $mould_info['mould_name'] ?></td>
      </tr>
      <tr>
        <th>标题：</th>
        <td><input type="text" name="title" placeholder="不能超过八个字符"</td></td>
      </tr>
      <tr>
        <th>文件来源：</th>
        <td>
          <input id='showFileName' type='text' readonly />&nbsp;&nbsp;
          <input type='button' value='请选择'  onClick='javascript:$("#hiddenFile").click();'/>
          <input id='hiddenFile' type='file' name="file" style="display:none" onchange='ophiddenFile();' />

        </td>
      </tr>
      <tr>
        <th>资料类型：</th>
        <td>
          <select name="data_type" id="data_type" class="input_txt txt">
            <?php 
              foreach($array_project_data_type as $k=>$v){
                switch($k)
                  {
                    case 1:
                     $is_select = $from == 'project_start'?'selected':'';
                    break;
                    case 3:
                     $is_select = $from == 'delivery_service'?'selected':'';
                    break;
                    default:
                      $is_select = '';

                  }
                echo '<option '.$is_select.' value="'.$k.'">'.$v[0].'</option>';
              }
             ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>文件类型：</th>
        <td id="radios">
          
        </td>
      </tr>
      <tr>
        <th>通知部门：</th>
        <td>
          <select class="input_txt txt" id="dept">
            <option value="">--请选择--</option>
            <?php 
              if($result_dept->num_rows){
                while($dept = $result_dept->fetch_assoc()){
                  echo '<option value="'.$dept['deptid'].'">'.$dept['dept_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <th>可选人员：</th>
        <td id="employee">
          
        </td>
      </tr>
      <tr>
        <th>已选人员：</th>
        <td id="select_employee">
          
        </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td>
          <input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="hidden" name="mouldid" vlaue="<?php echo $mouldid ?>">
          <input type="hidden" name="specification_id" value="<?php echo $specification_id ?>">
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="add" />
          <input type="hidden" name="from" value="<?php echo $_GET['from'] ?>" />
        </td>
      </tr>
    </table>
  </form>

  <?php

  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>