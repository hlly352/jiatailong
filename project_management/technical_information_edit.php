<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$specification_id = $_GET['specification_id'];
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
    if(!file){
      alert('请选择文件');
      return false;
    }
	})
})
</script>
<title>采购管理-希尔林</title>
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
        <th>文件来源：</th>
        <td>
          <input id='showFileName' type='text' readonly />&nbsp;&nbsp;
          <input type='button' value='请选择'  onClick='javascript:$("#hiddenFile").click();'/>
          <input id='hiddenFile' type='file' name="file" style="display:none" onchange='ophiddenFile();' />

        </td>
      </tr>
      <tr>
        <th>文件类型：</th>
        <td>
          <label><input type="radio" value="project_data" checked name="file_type" />项目资料</label>
          &nbsp;
          <label><input type="radio" value="mould_data" name="file_type" />模具资料</label>
          &nbsp;
          <label><input type="radio" value="flow" name="file_type" />跟进流程</label>
          &nbsp;
          <label><input type="radio" value="report" name="file_type" />总结报告</label>
          &nbsp;
          <label><input type="radio" value="standard" name="file_type" />设计标准</label>
          &nbsp;
          <label><input type="radio" value="drawing" name="file_type" />2D图纸</label>
        </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td>
          <input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="hidden" name="mouldid" vlaue="<?php echo $mouldid ?>">
          <input type="hidden" name="specification_id" value="<?php echo $specification_id ?>">
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" />
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