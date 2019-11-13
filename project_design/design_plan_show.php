<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$isadmin = $_SESSION['system_shell'][$system_dir]['isadmin'];
$designid = $_GET['designid'];
//查询设计计划的文件信息
$sql = "SELECT `design_plan_info`,`design_plan_path`,`specification_id` FROM `db_design_plan` WHERE `designid` = '$designid'";
$result_design = $db->query($sql);
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

<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "show"){ ?>
      <script type="text/javascript">
        function ophiddenFile(){
          var dd = $("#hiddenFile").val().split("\\");
          $("#showFileName").val(dd[dd.length-1]);
          }
      </script>
  <h4>资料查看</h4>
  <?php
     if($result_design->num_rows){
        $design_info = $result_design->fetch_row();
        $titles = explode('&',$design_info[0]);
        $paths  = explode('&',$design_info[1]);
        $specificationid = $design_info[2];
  ?>
  <form action="design_plan_do.php" name="material_order" method="post" enctype="multipart/form-data">
    <table>
      <?php 
        foreach($titles as $k=>$v){
        if(!empty($v)){
          $title = explode('#',$v);
        ?>
      <tr>
        <th width="15%">资料名称：</th>
        <td width="15%"><?php echo $title[0]  ?></td>
        <th width="5%">文件名</th>
        <td width="15%"><?php echo $title[1] ?></td>
        <th width="5%">时间：</th>
        <td width="15%"><?php echo $title[2] ?></td>
        <td width="">
          <a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].substr($paths[$k],2) ?>">查看</a>
          &nbsp;&nbsp;
          <?php if($isadmin == 1){ ?>
          <a href="design_plan_do.php?action=del&designid=<?php echo $designid ?>&key=<?php echo $k ?>" onclick="javascript:return confirm('确认删除?');">删除</a>
        <?php }?>
        </td>
      </tr>
     
     <?php } }?>
      <tr>
        
        <td colspan="8" style="text-align:center">
          <input type="button" name="" class="button" value="添加" id="add" onclick="javascript:window.location.href='design_plan_edit.php?action=add&specification_id=<?php echo $specificationid; ?>&designid=<?php echo $designid ?>'">
          <input type="button" name="button" value="返回" class="button" onclick="window.history.go(-1)" />
        </td>
      </tr>
    </table>
  </form>

  <?php

  
}else{
  echo "<p class=\"tag\">系统提示：暂无记录</p>";
}
}
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>