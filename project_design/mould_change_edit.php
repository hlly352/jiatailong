<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$specification_id = $_GET['specification_id'];
$changeid = $_GET['changeid'];
$isconfirm = $_SESSION['system_shell'][$system_dir]['isconfirm'];
$isadmin   = $_SESSION['system_shell'][$system_dir]['isadmin'];
//查询当前模具更改联络单
// $sql_change = "SELECT * FROM `db_mould_change` WHERE `changeid` = '$changeid'";
// $result_change = $db->query($sql_change);
// $result_change = $db->query($sql_change);
// if($result_change->num_rows){
//     $info = $result_change->fetch_assoc();
// }
//查询模具信息
if(!empty($changeid)){
  $mould_sql = "SELECT `db_mould_change`.`tips`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`mould_name`,`db_mould_specification`.`customer_code`,`db_mould_change`.`designer`,`db_mould_change`.`engnieer`,`db_mould_change`.`check`,`db_mould_change`.`approval`,`db_mould_change`.`data_content`,`db_mould_change`.`change_parts`,`db_mould_change`.`cancel_parts`,`db_mould_change`.`image_path`,`db_mould_change`.`document_no`,`db_mould_change`.`geter`,`db_mould_change`.`document_location`,`db_mould_change`.`document_use`,`db_mould_change`.`special_require` FROM `db_mould_specification` INNER JOIN `db_mould_change` ON `db_mould_specification`.`mould_specification_id` = `db_mould_change`.`specification_id` WHERE `db_mould_change`.`changeid` = '$changeid'";
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
  $array_dept = array();
 if(stripos($info['data_dept'],'&&')){
  $array_dept = explode('&&',$info['data_dept']);
 }else{
  $array_dept[] = $info['data_dept']; 
 }
 //获取文档用途
 $array_use = explode('&&',$info['document_use']);
 //查询接收人员
 $geter = $info['geter'];
 $sql_employee = "SELECT deptid,GROUP_CONCAT(`employee_name`) AS `geter` FROM `db_employee` WHERE `employeeid` IN($geter) GROUP BY `deptid`";
 $result_employee = $db->query($sql_employee);
$sql_employee_name = "SELECT `employee_name`,`employeeid` FROM `db_employee` WHERE `employeeid` IN($geter)";
$result_employee_name = $db->query($sql_employee_name);
 //查询图纸联络单的修改次数
 //查找修改次数
 if($changeid){
    $document_no = $info['document_no'];
 }else{
  $mould_no = $info['mould_no'];
    $sql_number = "SELECT MAX(SUBSTRING(`document_no`,-3)+0) AS `max_number` FROM `db_mould_change` WHERE `specification_id` = '$specification_id'";
      $result_number = $db->query($sql_number);
      if($result_number->num_rows){
        $array_number = $result_number->fetch_assoc();
        $max_number = $array_number['max_number'];
        $next_number = $max_number + 1;
        $document_no = $mould_no.'_'.date('Ymd').'_I'.strtolen($next_number,3).$next_number;
      }else{
        $document_no =  $mould_no.'_'.date('Ymd')."_I001";
      } 
    }
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
        div{
            width:100%;
        }
    </style>
<script language="javascript" type="text/javascript">
              //实例化编辑器
              //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
              var ue = UE.getEditor('editor');


              function isFocus(e){
                  alert(UE.getEditor('editor').isFocus());
                  UE.dom.domUtils.preventDefault(e)
              }
              function setblur(e){
                  UE.getEditor('editor').blur();
                  UE.dom.domUtils.preventDefault(e)
              }
              function insertHtml() {
                  var value = prompt('插入html代码', '');
                  UE.getEditor('editor').execCommand('insertHtml', value)
              }
              function createEditor() {
                  enableBtn();
                  UE.getEditor('editor');
              }
              function getAllHtml() {
                  alert(UE.getEditor('editor').getAllHtml())
              }
              function getContent() {
                  var arr = [];
                  arr.push("使用editor.getContent()方法可以获得编辑器的内容");
                  arr.push("内容为：");
                  arr.push(UE.getEditor('editor').getContent());
                  alert(arr.join("\n"));
              }
              function getPlainTxt() {
                  var arr = [];
                  arr.push("使用editor.getPlainTxt()方法可以获得编辑器的带格式的纯文本内容");
                  arr.push("内容为：");
                  arr.push(UE.getEditor('editor').getPlainTxt());
                  alert(arr.join('\n'))
              }
              function setContent(isAppendTo) {
                  var arr = [];
                  arr.push("使用editor.setContent('欢迎使用ueditor')方法可以设置编辑器的内容");
                  UE.getEditor('editor').setContent('欢迎使用ueditor', isAppendTo);
                  alert(arr.join("\n"));
              }
              function setDisabled() {
                  UE.getEditor('editor').setDisabled('fullscreen');
                  disableBtn("enable");
              }

              function setEnabled() {
                  UE.getEditor('editor').setEnabled();
                  enableBtn();
              }

              function getText() {
                  //当你点击按钮时编辑区域已经失去了焦点，如果直接用getText将不会得到内容，所以要在选回来，然后取得内容
                  var range = UE.getEditor('editor').selection.getRange();
                  range.select();
                  var txt = UE.getEditor('editor').selection.getText();
                  alert(txt)
              }

              function getContentTxt() {
                  var arr = [];
                  arr.push("使用editor.getContentTxt()方法可以获得编辑器的纯文本内容");
                  arr.push("编辑器的纯文本内容为：");
                  arr.push(UE.getEditor('editor').getContentTxt());
                  alert(arr.join("\n"));
              }
              function hasContent() {
                  var arr = [];
                  arr.push("使用editor.hasContents()方法判断编辑器里是否有内容");
                  arr.push("判断结果为：");
                  arr.push(UE.getEditor('editor').hasContents());
                  alert(arr.join("\n"));
              }
              function setFocus() {
                  UE.getEditor('editor').focus();
              }
              function deleteEditor() {
                  disableBtn();
                  UE.getEditor('editor').destroy();
              }
              function disableBtn(str) {
                  var div = document.getElementById('btns');
                  var btns = UE.dom.domUtils.getElementsByTagName(div, "button");
                  for (var i = 0, btn; btn = btns[i++];) {
                      if (btn.id == str) {
                          UE.dom.domUtils.removeAttributes(btn, ["disabled"]);
                      } else {
                          btn.setAttribute("disabled", "true");
                      }
                  }
              }
              function enableBtn() {
                  var div = document.getElementById('btns');
                  var btns = UE.dom.domUtils.getElementsByTagName(div, "button");
                  for (var i = 0, btn; btn = btns[i++];) {
                      UE.dom.domUtils.removeAttributes(btn, ["disabled"]);
                  }
              }

              function getLocalData () {
                  alert(UE.getEditor('editor').execCommand( "getlocaldata" ));
              }

              function clearLocalData () {
                  UE.getEditor('editor').execCommand( "clearlocaldata" );
                  alert("已清空草稿箱")
              }
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
  //导出文件
  $('#export').live('click',function(){
    var changeid = $("input[name=changeid]").val();
    window.location.href = 'excel_mould_change.php?changeid='+changeid;
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
  <form action="mould_change_do.php" name="material_order" method="post" enctype="multipart/form-data">
    
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
        <th width="9%">客户代码</th>
        <td width="16%"><?php echo $info['customer_code'] ?></td>
        <th width="9%">项目名称</th>
        <td width="16%"><?php echo $info['project_name'] ?></td>
        <th width="9%">模具编号</th>
        <td width="16%">
          <?php echo $info['mould_no'] ?>
          <input type="hidden" value="<?php echo $info['mould_no'] ?>" name="mould_no" />  
        </td>
        <th width="9%">产品名称</th>
        <td width="14%"><?php echo $info['mould_name'] ?></td>
      </tr>
       <tr>
        <th>资料内容</th>
        <td colspan="3" style="text-align:left">
            <?php
              foreach($array_data_content as $k=>$content){
                $is_select = in_array($k,$array_content)?'checked':'';
                echo '<label><input '.$is_select.' type="checkbox" name="data_content[]" value="'.$k.'" />'.$content.'</label> &nbsp;&nbsp;';
              }
            ?>
        </td>
        <th>重点提示</th>
        <td colspan="3">
          <input type="text" name="tips" value="<?php echo $info['tips'] ?>" class="input_txt" style="width:85%">
        </td>
      </tr>
       <tr>
        <th> 修改零件编号</th>
        <td colspan="3"> 
          <input type="text" name="change_parts" value="<?php echo $info['change_parts'] ?>" class="input_txt" style="width:85%" />
        </td>
        <th>取消零件编号</th>
        <td colspan="3">
          <input type="text" name="cancel_parts" value="<?php echo $info['cancel_parts'] ?>" class="input_txt" style="width:85%"  />
        </td>
      </tr>
      <tr>
        <td colspan="8" style="height:150px;padding-top:10px;text-align:left">
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
              <?php echo htmlspecialchars_decode($info['image_path']); ?>
            </script>  
          </div>
    <!--       <div id="btns">
          <div>
            <button onclick="getAllHtml()">获得整个html的内容</button>
            <button onclick="getContent()">获得内容</button>
            <button onclick="setContent()">写入内容</button>
            <button onclick="setContent(true)">追加内容</button>
            <button onclick="getContentTxt()">获得纯文本</button>
            <button onclick="getPlainTxt()">获得带格式的纯文本</button>
            <button onclick="hasContent()">判断是否有内容</button>
            <button onclick="setFocus()">使编辑器获得焦点</button>
            <button onmousedown="isFocus(event)">编辑器是否获得焦点</button>
            <button onmousedown="setblur(event)" >编辑器失去焦点</button>

          </div>
          <div>
            <button onclick="getText()">获得当前选中的文本</button>
            <button onclick="insertHtml()">插入给定的内容</button>
            <button id="enable" onclick="setEnabled()">可以编辑</button>
            <button onclick="setDisabled()">不可编辑</button>
            <button onclick=" UE.getEditor('editor').setHide()">隐藏编辑器</button>
            <button onclick=" UE.getEditor('editor').setShow()">显示编辑器</button>
            <button onclick=" UE.getEditor('editor').setHeight(300)">设置高度为300默认关闭了自动长高</button>
          </div>
          <div>
              <button onclick="getLocalData()" >获取草稿箱内容</button>
              <button onclick="clearLocalData()" >清空草稿箱</button>
          </div>
          </div>
          <div>
              <button onclick="createEditor()">
              创建编辑器</button>
              <button onclick="deleteEditor()">
              删除编辑器</button>
          </div>-->
        </td> 
      </tr>
      <tr>
        <td>
          图档位置：              
        </td>
        <td colspan="7" style="text-align:left;padding-right:10px">
          <label><input name="document_use[]" value="K" <?php echo in_array('K',$array_use)?'checked':'' ?> type="checkbox" />开粗 </label> 
          <label><input name="document_use[]" <?php echo in_array('J',$array_use)?'checked':'' ?> value="J" type="checkbox" />精光 </label>
          <label><input name="document_use[]" <?php echo in_array('A',$array_use)?'checked':'' ?> value="A" type="checkbox" />按特殊要求： </label>
          <input type="text" name="special_require" class="input_txt" style="width:73%;" value="<?php echo $info['special_require'] ?>" />
        </td>
      </tr>
      <tr>
        <td>
          图档位置：
        </td>
        <td colspan="7" style="padding-right:10px">
          <input type="text" value="<?php echo $info['document_location'] ?>" class="input_txt" style="width:95%;" name="document_location" />
        </td>
      </tr>
       <tr>
        <th>原图设计师</th>
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
        <th>更改工程师</th>
        <td>
           <select name="engnieer">
            <option value="">--请选择--</option>
            <?php
              if($result_designs->num_rows){
                while($row_designs = $result_designs->fetch_assoc()){
                $is_select = $info['engnieer'] == $row_designs['employeeid']?'selected':'';
                  echo '<option '.$is_select.' value="'.$row_designs['employeeid'].'">'.$row_designs['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
        <th>审核</th>
        <td>
           <select name="check" <?php echo $isadmin == '1'?'':'disabled'; ?>>
            <option value="">--请选择--</option>
            <?php
              if($array_check){
               foreach($array_check as $k=>$v){
                $is_select = $info['check'] == $v['employeeid']?'selected':'';
                  echo '<option '.$is_select.' value="'.$v['employeeid'].'">'.$v['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
        <th>批准</th>
        <td>
           <select name="approval" <?php echo $isadmin == '1'?'':'disabled'; ?>>
            <option value="">--请选择--</option>
            <?php
              if($array_check){
               foreach($array_check as $k=>$v){
                $is_select = $info['approval'] == $v['employeeid']?'selected':'';
                  echo '<option '.$is_select.' value="'.$v['employeeid'].'">'.$v['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
      </tr>
      <?php if(!empty($changeid)){ ?>
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
          <input type="hidden" name="changeid" value="<?php echo $changeid ?>" />
          <input type="hidden" name="document_no" value="<?php echo $document_no; ?>" />
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