<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$specification_id = $_GET['specification_id'];
$reviewid = $_GET['reviewid'];

//查询模具信息
if($reviewid){
  $mould_sql = "SELECT *,`db_mould_specification`.`cavity_num`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`mould_name`,`db_design_review`.`surface_require`,`db_design_review`.`projecter`,`db_design_review`.`designer`,`db_design_review`.`mould_coefficient` FROM `db_mould_specification` LEFT JOIN `db_design_review` ON `db_mould_specification`.`mould_specification_id` = `db_design_review`.`specification_id` WHERE `db_mould_specification`.`mould_specification_id` = '$specification_id'";
}else{
   $mould_sql = "SELECT `cavity_num`,`customer_code`,`project_name`,`mould_no`,`mould_name` FROM `db_mould_specification` WHERE `db_mould_specification`.`mould_specification_id` = '$specification_id'";
}
$result_mould = $db->query($mould_sql);
if($result_mould->num_rows){
  $info = $result_mould->fetch_assoc();
}
//查询设计部人员
$sql_design = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name` FROM `db_employee` INNER JOIN `db_department` ON `db_employee`.`deptid` = `db_department`.`deptid` WHERE `dept_name` LIKE '%人事%' ORDER BY `employeeid` DESC";
$result_design = $db->query($sql_design);
$result_designs = $db->query($sql_design);
//查询审核人员
$sql_check = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name` FROM `db_employee` INNER JOIN `db_system_employee` ON `db_employee`.`employeeid` = `db_system_employee`.`employeeid` INNER JOIN `db_system` ON `db_system`.`systemid` = `db_system_employee`.`systemid` WHERE `db_system`.`system_dir` = '$system_dir' AND `db_system_employee`.`isadmin` = '1'";
$result_check = $db->query($sql_check);
if($result_check->num_rows){
  $array_check = array();
  while($row_check = $result_check->fetch_assoc()){
    $array_check[] = $row_check;
  }
}
//查询文件编号
if(empty($reviewid)){
  $sql_max = "SELECT MAX(SUBSTRING(`document_no`,-3)+0) AS `max_number` FROM `db_design_review` WHERE `reviewid` = '$specification_id'";
  $result_max = $db->query($sql_max);
  if($result_max->num_rows){
      $max_number = $result_max->fetch_assoc()['max_number'];
      $document_number = $max_number + 1;
      $document_no = $info['mould_no'].'_'.date('Ymd').'_C'.strtolen($document_number,3).$document_number;
    }else{
      $document_no = $info['mould_no'].'_C'.date('Ymd').'_001';
    }
  }else{
    $document_no = $info['document_no'];
  }
 //获取图片路径
 $image_file = explode('$',$info['image_path']);
 //去除最后一项
 array_pop($image_file);
 //获取资料内容和接收部门
 $array_content = array();
 if(stripos($info['data_content'],'&&')){
  $array_content = explode('&&',$info['data_content']);
 }else{
  $array_content[] = $info['data_content']; 
 }
  $array_dept = array();
 if(stripos($info['data_dept'],'&&')){
  $array_dept = explode('&&',$info['data_dept']);
 }else{
  $array_dept[] = $info['data_dept']; 
 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
  th,td{height:30px;}
  img.not('#logo'){height:150px;}
  #table_list tr .nobor{border:none;background:white;}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script type="text/javascript" src="../js/view_img.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
 
  $("#submit").click(function(){
    var file = $('input[name = file]').val();
    var title = $.trim($('input[name = title]').val());
    var patt = /^.{1,8}$/;
    if(!title && file){
      alert('请填写标题');
      return false;
    }
    if(title){
      if(!patt.test(title)){
        alert('标题长度超过限制');
        return false;
      }
    }
  })
  //导出文件
  $('#export').live('click',function(){
    var reviewid = $("input[name=reviewid]").val();
    window.location.href = 'excel_design_review.php?reviewid='+reviewid;
  })

})
</script>
<title>工程设计-嘉泰隆</title>
</head>
<body>
<?php include "header.php"; ?>
<div id="table_list" style="width:85%;margin:0px auto">
  <?php if($action == "add" || $action == 'edit'){ ?>
  <form action="design_review_do.php" name="material_order" method="post" enctype="multipart/form-data">
   
    <table>
      <tr>
        <td rowspan="2" class="nobor"><img src='../jtl.png' width="100"></td>
        <th colspan="6" class="nobor" style="font-size:20px">
          模具更改联络单
        </th>
        <td class="nobor"></td>
      </tr>
      <tr>
        <td class="nobor" colspan="5"></td>
        <th class="nobor">文件编号：</th>
        <td class="nobor" style="text-align:left"><?php echo $document_no; ?></td>
      </tr>
      <tr>
        <th width="10%">客户代码</th>
        <td width="15%"><?php echo $info['customer_code'] ?></td>
        <th width="10%">项目名称</th>
        <td width="15%"><?php echo $info['project_name'] ?></td>
        <th width="10%">模具编号</th>
        <td width="15%">
          <?php echo $info['mould_no'] ?>
          <input type="hidden" value="<?php echo $info['mould_no'] ?>" name="mould_no" />  
        </td>
        
        <th width="10%">产品名称</th>
        <td width="15%"><?php echo $info['mould_name'] ?></td>
      </tr>
      <tr>
        <th>模具穴数</th>
        <td>
          <?php echo $info['cavity_num'] ?>
        </td>
        <th>外观要求</th>
        <td>
          <input type="text" name="surface_require" value="<?php echo $info['surface_require'] ?>" class="input_txt" size="10" />
        </td>    
        <th>设计师</th>
        <td>
          <select name="designer">
            <option value="">--请选择--</option>
            <?php
              if($result_design->num_rows){
                while($row_design = $result_design->fetch_assoc()){
                $is_select = $info['designer'] == $row_design['employeeid']?'selected':'';
                  echo '<option '.$is_select.' value="'.$row_design['employeeid'].'">'.$row_design['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
        <th>项目负责人</th>
        <td>
           <select name="projecter">
            <option value="">--请选择--</option>
            <?php
              if($result_designs->num_rows){
                while($row_designs = $result_designs->fetch_assoc()){
                $is_select = $info['projecter'] == $row_designs['employeeid']?'selected':'';
                  echo '<option '.$is_select.' value="'.$row_designs['employeeid'].'">'.$row_designs['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
      </tr>
    <tr> 
      <th>评审流程</th>  
      <th>评审流程</th>
      <th colspan="6">评审记录</th>
    </tr>
    <tr>
      <th>1</th>
      <th>缩水检查</th>
      <td colspan="2">
        <textarea cols="50" name="shrink_check" rows="4">
          <?php echo $info['shrink_check'] ?>
        </textarea>
      </td>
      <td colspan="4">
        <span class="mould_image">
          <img src="<?php echo $info['shrink_check_path'] ?>">
        </span>
        <input type="file" name="shrink_check[]" onchange="review_scan(this,'shrink_check')" />
        <span></span>
      </td>
    </tr>
    <tr>
      <th>2</th>
      <th>PL线确认</th>
     <td colspan="2">
        <textarea name="pl_confirm" cols="50" rows="4">
          <?php echo $info['pl_confirm'] ?>
        </textarea>
      </td>
       <td colspan="4">
       <span class="mould_image">
          <img src="<?php echo $info['pl_confirm_path'] ?>">
        </span>
        <input type="file" name="pl_confirm" onchange="review_scan(this)" />
        <span></span>
      </td>
    </tr>
    <tr>
      <th>3</th>
      <th>进胶方式 </th>
     <td colspan="2">
        <textarea name="gum_method" cols="50" rows="4">
          <?php echo $info['gum_method'] ?>
        </textarea>
      </td>
      <td colspan="4">
        <img src="<?php echo $info['gum_method_path'] ?>">
        <input type="file" name="gum_method" onchange="review_scan(this)" />
        <span></span>
      </td>
    </tr>
    <tr>
      <th>4</th>
      <th>模仁大小</th>
      <td colspan="2">
        <textarea name="mold_size" cols="50" rows="4">
          <?php echo $info['mold_size'] ?>
        </textarea>
      </td>
       <td colspan="4">
        <img src="<?php echo $info['mold_size_path'] ?>">
        <input type="file" name="mold_size" onchange="review_scan(this)" />
        <span></span>
      </td>
    </tr>
    <tr>
      <th>5</th>
      <th>镶件结构 </th>
     <td colspan="2">
        <textarea name="insert" cols="50" rows="4">
          <?php echo $info['insert'] ?>
        </textarea>
      </td>
       <td colspan="4">
        <img src="<?php echo $info['insert_path'] ?>">
        <input type="file" name="insert" onchange="review_scan(this)" />
        <span></span>
      </td>
    </tr> 
      <tr>
      <th>6</th>
      <th>顶出方案 </th>
      <td colspan="2">
        <textarea name="eject_method" cols="50" rows="4">
          <?php echo $info['eject_method'] ?>
        </textarea>
      </td>
       <td colspan="4">
        <img src="<?php echo $info['eject_method_path'] ?>">
        <input type="file" name="eject_method" onchange="review_scan(this)" />
        <span></span>
      </td>
    </tr> 
      <tr>
      <th>7</th>
      <th>冷却设计 </th>
     <td colspan="2">
        <textarea name="cool_design" cols="50" rows="4">
          <?php echo $info['cool_design'] ?>
        </textarea>
      </td>
       <td colspan="4">
        <img src="<?php echo $info['cool_design_path'] ?>">
        <input type="file" name="cool_design" onchange="review_scan(this)" />
        <span></span>
      </td>
    </tr> 
    <tr>
      <th>8</th>
      <th>定位方式 </th>
      <td colspan="2">
        <textarea name="positioning_method" cols="50" rows="4">
          <?php echo $info['positioning_method'] ?>
        </textarea>
      </td>
       <td colspan="4">
        <img src="<?php echo $info['positioning_method_path'] ?>">
        <input type="file" name="positioning_method" onchange="review_scan(this)" />
        <span></span>
      </td>
    </tr> 
    <tr>
      <th>9</th>
      <th>顶出行程</th>
     <td colspan="2">
        <textarea name="eject_stroke" cols="50" rows="4">
          <?php echo $info['eject_stroke'] ?>
        </textarea>
      </td>
       <td colspan="4">
        <img src="<?php echo $info['eject_stroke_path'] ?>">
        <input type="file" name="eject_stroke" onchange="review_scan(this)" />
        <span></span>
      </td>
    </tr> 
    <tr>
      <th>10</th>
      <th>模架大小</th>
      <td colspan="2">
        <textarea name="base_size" cols="50" rows="4">
          <?php echo $info['base_size'] ?>
        </textarea>
      </td>
       <td colspan="4">
        <img src="<?php echo $info['base_size_path'] ?>">
        <input type="file" name="base_size" onchange="review_scan(this)" />
        <span></span>
      </td>
    </tr>
    <tr>
        <td colspan="8">
          <input type="button"  id="export" value="导出" class="button">
          <input type="submit"  value="确定" class="button" />
          <input type="hidden" name="specification_id" value="<?php echo $_GET['specification_id'] ?>" />
          <input type="hidden" name="document_no" vlaue="<?php echo $document_no; ?>" />
          <input type="hidden" name="reviewid" value="<?php echo $reviewid ?>" />
          <input type="hidden"  name="submit" value="确定" />
          <input type="button" value="返回" class="button" onclick="javascript:window.history.go(-1);" />
        </td>
      </tr>
    </table>
   </div>
  </form>

  <?php

  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>