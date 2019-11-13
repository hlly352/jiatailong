<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$specification_id = $_GET['specification_id'];
$designid = $_GET['designid'];
$from = $_GET['from'];
//查询当前模具的设计计划信息
$sql_plan = "SELECT * FROM `db_design_plan` WHERE `designid` = '$designid'";
$result_plan = $db->query($sql_plan);

//查询模具信息
$mould_sql = "SELECT `project_name`,`mould_no`,`mould_name` FROM `db_mould_specification` WHERE `mould_specification_id` = '$specification_id'";
$result_mould = $db->query($mould_sql);
if($result_mould->num_rows){
  $mould_info = $result_mould->fetch_assoc();
}
//查询设计部人员
$sql_design = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name` FROM `db_employee` INNER JOIN `db_department` ON `db_employee`.`deptid` = `db_department`.`deptid` WHERE `dept_name` LIKE '%人事%' ORDER BY `employeeid` DESC";
$result_design = $db->query($sql_design);
$results_design = $db->query($sql_design);
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

})
</script>
<title>项目管理-嘉泰隆</title>
</head>

<body>
<?php if(empty($from)) include "header.php"; ?>
<?php if(!empty($from)){ ?>
  
  <script type="text/javascript">
    $(function(){
    $('input:text').attr('disabled','disabled');
    $('select').attr('disabled','disabled');
    })
  </script>
<?php } ?>
<div id="table_search">
  <h4>物料订单添加</h4>
  <form>
 <table>
      <tr>
        <th width="10%">项目名称：</th>
        <td width="21%"><?php echo $mould_info['project_name'] ?></td>
        <th width="10%">模具编号：</th>
        <td width="21%"><?php echo $mould_info['mould_no'] ?></td>
        <th width="10%">模具名称：</th>
        <td width="21%"><?php echo $mould_info['mould_name'] ?></td>
      </tr>
  </table>
  </form>
</div>
<div id="table_list">
  <?php if($action == "add"){ ?>
      <script type="text/javascript">
        function ophiddenFile(){
          var dd = $("#hiddenFile").val().split("\\");
          $("#showFileName").val(dd[dd.length-1]);
          }
      </script>
  <form action="design_plan_do.php" name="material_order" method="post" enctype="multipart/form-data">
   
    <table>
      <tr>
        <th>2D绘图员</th>
        <th>设计组长</th>
        <th>优先等级</th>
        <th>模具最终<br />确认时间</th>
        <th>T0时间</th>
        <th>内容</th>
        <th>DFM</th>
        <th>方案定案会</th>
        <th>产品数据</th>
        <th>设计开始时间</th>
        <th>2D结构图</th>
        <th>3D-V1传图</th>
        <th>3D-V2传图</th>
        <th>客户确认<br />ok可定料<br />时间</th>
        <th>订购模仁</th>
        <th>订购热嘴</th>
        <th>精加工评审</th>
        <th>模仁NC开粗图</th>
        <th>订购模胚</th>
        <th>机加工图</th>
        <th>模仁NC精加工图</th>
        <th>订购散件料</th>
        <th>订购标准件</th>
        <th>模仁2D图</th>
        <th>其他散件图</th>
        <th>晒字图下发</th>
      </tr>
      <?php 
        $row = $result_plan->fetch_assoc();
      ?>
      <tr>
        <td rowspan="2">
          <select name="drawer_2d">
            <option value="">-请选择-</option>
            <?php
              if($result_design->num_rows){
                while($row_design = $result_design->fetch_assoc()){
                   $is_select = $row_design['employeeid'] == $row['drawer_2d']?'selected':'';
                  echo '<option '.$is_select.' value="'.$row_design['employeeid'].'">'.$row_design['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
        <td rowspan="2">
          <select name="design_group">
            <option value="">-请选择-</option>
            <?php
              if($results_design->num_rows){
                while($rows_design = $results_design->fetch_assoc()){
                  $is_select = $rows_design['employeeid'] == $row['design_group']?'selected':'';
                  echo '<option '.$is_select.' value="'.$rows_design['employeeid'].'">'.$rows_design['employee_name'].'</option>';
                }
              }
            ?>
          </select>
        </td>
        <td rowspan="2"><input type="text" id="degree" name="first_degree" value="<?php echo $row['first_degree'] ?>"  class="input_txt" size="9" /></td>
        <td rowspan="2"><input type="text" name="final_confirm" onfocus="WdatePicker({dateFmt:'yyyy/MM/dd',isShowClear:false,readOnly:true})" value="<?php echo $row['final_confirm'] ?>" class="input_txt" size="9" /></td>
        <td rowspan="2"><input type="text" name="t0_time" value="<?php echo $row['t0_time'] ?>"  onfocus="WdatePicker({dateFmt:'yyyy/MM/dd',isShowClear:false,readOnly:true})" class="input_txt"  size="9" /></td>
        <td>计划</td>
        <?php 
          foreach($array_design_plan as $name){
            echo '<td><input type="text" value="'.$row["plan_".$name].'" name="plan_'.$name.'" onfocus="WdatePicker({dateFmt:\'yyyy/MM/dd\',isShowClear:false,readOnly:true})" class="input_txt" size="9"></td>';
          }
        ?>
      </tr>
      <tr>
        <td>实际</td>
        <?php 
          foreach($array_design_plan as $name){
            echo '<td><input type="text" name="real_'.$name.'" value="'.$row["real_".$name].'" onfocus="WdatePicker({dateFmt:\'yyyy/MM/dd\',isShowClear:false,readOnly:true})" class="input_txt" class="input_txt" size="7"></td>';
          }
        ?>
      </tr>
      <tr>
        <td colspan="6"></td>
        <th colspan="2">文件名</th>
        <td colspan="3">
          <input type="text" name="title" class="input_txt" />
        </td>
        <th colspan="3">选择文件</th>
        <td colspan="6">
          <input id='showFileName' class="input_txt" type='text' readonly />&nbsp;&nbsp;
          <input type='button' value='请选择'  onClick='javascript:$("#hiddenFile").click();'/>
          <input id='hiddenFile' type='file' name="file" style="display:none" onchange='ophiddenFile();' />
        </td>
      </tr>
      <tr>
        <td colspan="26">
          <?php if(empty($from)){ ?>
            <input type="submit" name="submit" id="submit" value="确定" class="button" />
          <?php } ?>
          <input type="hidden" name="specification_id" value="<?php echo $specification_id ?>">
          <input type="hidden" name="designid" value="<?php echo $designid; ?>"/>
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
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