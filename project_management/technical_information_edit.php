<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$informationid = $_GET['informationid'];
$data_name = trim($_GET['data']);
$specification_id = $_GET['specification_id'];
$isconfirm = $_SESSION['system_shell'][$system_dir]['isconfirm'];
$isadmin   = $_SESSION['system_shell'][$system_dir]['isadmin'];
//查询模具信息
if(!empty($informationid)){
    $mould_sql = "SELECT *,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`mould_name`,`db_mould_specification`.`customer_code` FROM `db_mould_specification` LEFT JOIN `db_technical_information` ON `db_mould_specification`.`mould_specification_id` = `db_technical_information`.`specification_id` LEFT JOIN `db_technical_information_list` ON `db_technical_information_list`.`information_listid` = `db_technical_information`.`{$data_name}` WHERE `db_mould_specification`.`mould_specification_id` = '$specification_id'";
  }else{
    $mould_sql = "SELECT `project_name`,`mould_no`,`mould_name`,`customer_code` FROM `db_mould_specification` WHERE `mould_specification_id` = '$specification_id'";
  }
$result_mould = $db->query($mould_sql);
if($result_mould->num_rows){
  $info = $result_mould->fetch_assoc();
}
$check = $info['check'];
//查询设计部人员
$sql_design = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name` FROM `db_employee` INNER JOIN `db_department` ON `db_employee`.`deptid` = `db_department`.`deptid` WHERE `dept_name` LIKE '%工程%' ORDER BY `employeeid` DESC";
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
 //获取文件内容
 $file_ids = $info[$data_name.'_path'];
 $file_ids = substr($file_ids,1);
 $sql_file_list = "SELECT * FROM `db_technical_information_file` WHERE `information_fileid` IN($file_ids)";
 $result_file_list = $db->query($sql_file_list);
 $information_listid = $info['information_listid'];
 //查询接收人员
 $geter = $info['geter'];
 $sql_employee = "SELECT deptid,GROUP_CONCAT(`employee_name`) AS `geter` FROM `db_employee` WHERE `employeeid` IN($geter) GROUP BY `deptid`";
 $result_employee = $db->query($sql_employee);
$sql_employee_name = "SELECT `employee_name`,`employeeid` FROM `db_employee` WHERE `employeeid` IN($geter)";
$result_employee_name = $db->query($sql_employee_name);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
  th,td{height:30px;}
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
<script type="text/javascript" charset="utf-8" src="../js/utf8-php/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="../js/utf8-php/ueditor.all.min.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="../js/utf8-php/lang/zh-cn/zh-cn.js"></script>

    <style type="text/css">
        div{width:100%;}
        #file_info tr td{border:none;}
    </style>
<script language="javascript" type="text/javascript">
              //实例化编辑器
              //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
              var ue = UE.getEditor('editor');
$(function(){
  var check = '<?php echo $check; ?>';
  var ue = UE.getEditor('editor');
  if(check>0){
    $('input').not('#back').attr('disabled',true);
    $('select').attr('disabled',true);
    ue.ready(function() {
      //不可编辑
      ue.setDisabled();
      });
  }
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
$('#change_dept').live('click',function(){
  var par = $(this).parent().parent().prev();
  $(this).parent().parent().remove();
  var add = '<tr>          <th>接收部门</th>          <td >            <select class="input_txt txt" id="dept">              <option value="">--请选择--</option>            <?php
                foreach($array_data_dept as $k=>$dept){                  echo '<option value="'.$k.'">'.$dept.'</option>';
                }              ?>            </select>        </td>        <th>部门人员</th>        <td id="employee" colspan="5" style="text-align:left"></td>        </tr>        <tr>          <th>接收人员</th>          <td colspan="7" id="select_employee" style="text-align:left"><?php if($result_employee_name->num_rows){ while($row_employee_name =$result_employee_name->fetch_assoc()){ echo '<span class="select_employee" employeeid="'.$row_employee_name['employeeid'].'" id="select_'.$row_employee_name['employeeid'].'" style="padding:5px;cursor:pointer;color:blue">'.$row_employee_name['employee_name'].'<input type="hidden" value="'.$row_employee_name['employeeid'].'" name="employeeid[]"></span>'; }} ?></td>        </tr>';
  par.after(add);

    })
  })
</script>
<title>项目管理-嘉泰隆</title>
</head>
<body>
<?php include "header.php"; ?>
<div id="table_list" style="width:85%;margin:0px auto">
  <?php if($action == "add" || $action == 'edit'){ ?>
  <form action="technical_information_do.php" name="material_order" method="post" enctype="multipart/form-data">
    
    <table>
      <tr>
        <td rowspan="2" class="nobor"><img src='../jtl.png' width="100"></td>
        <th colspan="6" class="nobor" style="font-size:20px">
          <?php echo $data_name; ?>
        </th>
        <td class="nobor"></td>
      </tr>
      <tr>
        <td class="nobor" colspan="7"></td>
      </tr>
      <tr>
        <th width="9%">客户代码</th>
        <td width="16%"><?php echo $info['customer_code'] ?></td>
        <th width="9%">项目名称</th>
        <td width="16%"><?php echo $info['project_name'] ?></td>
        <th width="9%">模具编号</th>
        <td width="16%"><?php echo $info['mould_no'] ?></td>
        <th width="9%">产品名称</th>
        <td width="14%"><?php echo $info['mould_name'] ?></td>
      </tr>
      <tr>
        <td colspan="8" style="height:150px;padding-top:10px;text-align:left;border-bottom-color:white">
         <!--  <p style="text-align:left;background:white">
            修改内容贴图及说明：
          </p>
           <?php
            foreach($image_file as $k=>$v){
              $image_info = explode('**',$v);
              echo '<div style="float:left;width:46%;margin-left:3%;margin-bottom:10px" class="mould_image"><img width="100%" src='.$image_info[0].' ><span style="display:block;text-align:center">'.$image_info[1].'</span></div>';
            }
           ?>
          <input type="file" style="float:left" name="file[]" onchange="mould_change(this)">
          <div style="float:left;margin-left:3%;margin-bottom:2%;width:46%"></div> -->
          <div>
            <script id="editor" type="text/plain" style="width:100%;height:500px;">
              <?php echo html_entity_decode($info['ueditor']); ?>
            </script>  
          </div>
        </td> 
      </tr>
      <tr>
        <td colspan="8">
          <div style="border:1px solid #bbb">
            <table id="file_info">
              <tr>
                 <td width="8%">资料名称</td>
                 <td colspan="3" width="40%">
                   <input type="text" name="title" class="input_txt" style="width:100%" />
                 </td>
                 <td width="8%">资料来源</td>
                 <td colspan="3" width="40%">
                   <input type="file" name="file" class="input_txt" style="width:90%"/>
                 </td>
              </tr>
              <tr>
                <td>资料名称</td>
                <td></td>
                <td>文件名</td>
                <td></td>
                <td>时间</td>
                <td></td>
                <td>操作</td>
                <td></td>
              </tr>
              <?php if($result_file_list->num_rows){
                  while($row_file_list = $result_file_list->fetch_assoc()){
               ?>
                <tr>
                  <td><?php echo $row_file_list['title'] ?></td>
                  <td></td>
                  <td><?php echo $row_file_list['file_name']; ?></td>
                  <td></td>
                  <td><?php echo $row_file_list['dodate']; ?></td>
                  <td></td>
                  <td>
                    <a href="<?php echo $row_file_list['file_path'] ?>">查看</a>
                  </td>
                </tr>
              <?php }} ?>
            </table>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="8" style="height:4px"></td>
      </tr>
      <tr>
        <th>操作人</th>
        <td>
          <select name="do_employeeid">
            <option value="">--请选择--</option>
            <?php
              if($result_design->num_rows){
                while($row_design = $result_design->fetch_assoc()){
                $is_select = $info['do_employeeid'] == $row_design['employeeid']?'selected':'';
                  echo '<option '.$is_select.' value="'.$row_design['employeeid'].'">'.$row_design['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
        <th>复核</th>
        <td>
           <select name="checker">
            <option value="">--请选择--</option>
            <?php
              if($result_designs->num_rows){
                while($row_designs = $result_designs->fetch_assoc()){
                $is_select = $info['checker'] == $row_designs['employeeid']?'selected':'';
                  echo '<option '.$is_select.' value="'.$row_designs['employeeid'].'">'.$row_designs['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
        <th>项目经理</th>
        <td>
           <select name="manager" <?php echo $isadmin == '1'?'':'disabled'; ?>>
            <option value="">--请选择--</option>
            <?php
              if($array_check){
               foreach($array_check as $k=>$v){
                $is_select = $info['manager'] == $v['employeeid']?'selected':'';
                  echo '<option '.$is_select.' value="'.$v['employeeid'].'">'.$v['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
        <th>审批</th>
        <td>
           <select name="approvaler" <?php echo $isadmin == '1'?'':'disabled'; ?>>
            <option value="">--请选择--</option>
            <?php
              if($array_check){
               foreach($array_check as $k=>$v){
                $is_select = $info['approvaler'] == $v['employeeid']?'selected':'';
                  echo '<option '.$is_select.' value="'.$v['employeeid'].'">'.$v['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
      </tr>
      <?php if(!empty($informationid)){ ?>
      <tr>
      <th>接收部门</th>
        <td colspan="7" style="text-align:left">
        <?php
            if($result_employee->num_rows){
            $array_geter = array();
            while($row_employee = $result_employee->fetch_assoc()){
              echo $array_data_dept[$row_employee['deptid']].'：'.$row_employee['geter'].'  ';
            }
           }
          if(empty($check)){
            echo '<span style="margin-left:10px;cursor:pointer" id="change_dept"><img title="更改人员" alt="更改人员" src="../images/system_ico/edit_10_10.png" width="12"></span>';
          }
        ?>
        </td>
      </tr>
      <?php }else{ ?>
        <tr>
          <th>接收部门</th>
          <td >
            <select class="input_txt txt" id="dept">
              <option value="">--请选择--</option>
            <?php
                foreach($array_data_dept as $k=>$dept){
                  echo '<option value="'.$k.'">'.$dept.'</option>';
                }
              ?>
            </select>
        </td>
        <th>部门人员</th>
        <td id="employee" colspan="5" style="text-align:left"></td>
        </tr>
        <tr>
          <th>接收人员</th>
          <td colspan="7" id="select_employee" style="text-align:left"></td>
        </tr>
      <?php } ?>
     
      <tr>
        <td>签收部门：</td>
        <td colspan="7" style="text-align:left">
          <?php foreach ($array_data_dept as $key => $value): ?>
              <?php echo $value.':'; ?>
              <span style="padding-left:80px"></span>
          <?php endforeach ?>
        </td>
      </tr>
      <tr>
        <td colspan="8">
          <!-- <input type="button"  id="export" value="导出" class="button"> -->
          <input type="submit"  value="确定" class="button" />
          <input type="hidden" name="specification_id" value="<?php echo $_GET['specification_id'] ?>" />
          <input type="hidden" name="informationid" value="<?php echo $informationid ?>" />
          <input type="hidden" name="data_name"  value="<?php echo $data_name; ?>" />
          <input type="hidden" name="information_listid" value="<?php echo $information_listid; ?>" />
          <input type="hidden"  name="submit" value="确定" />
          <input type="button" value="返回" id="back" class="button" onclick="javascript:window.history.go(-1);" />
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