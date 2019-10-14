<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$modify_id = $_GET['modify_id'];
$data = $_GET['data'];

$isadmin = $_SESSION['system_shell'][$system_dir]['isadmin'];

//查询对应信息
$information_sql = "SELECT `{$data}`,`{$data}_path`,`modify_id` FROM `db_mould_modify` WHERE `modify_id` = '$modify_id'";
$result_information = $db->query($information_sql);



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
     if($result_information->num_rows){
        $information_info = $result_information->fetch_row();
        $titles = explode('&',$information_info[0]);
        $paths  = explode('&',$information_info[1]);
        $specificationid = $information_info[2];
  ?>
  <form action="technical_information_do.php" name="material_order" method="post" enctype="multipart/form-data">
    <table>
      <?php 
        foreach($titles as $k=>$v){
        if(!empty($v)){
          $title = explode('#',$v);
        ?>
      <tr>
        <th width="25%">资料名称：</th>
        <td width="15%"><?php echo $title[0]  ?></td>
        <th width="5%">文件名</th>
        <td width="15"><?php echo $title[1] ?></td>
        <th width="5%">时间：</th>
        <td width="15"><?php echo $title[2] ?></td>
        <td width="20%">
          <a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].substr($paths[$k],2) ?>">查看</a>
          &nbsp;&nbsp;
          <?php if($isadmin == 1){ ?>
          <a href="technical_information_do.php?action=del&informationid=<?php echo $_GET['informationid'] ?>&key=<?php echo $k ?>&data=<?php echo $_GET['data'] ?>" onclick="javascript:return confirm('确认删除?');">删除</a>
        <?php }?>
        </td>
      </tr>
     
     <?php } }?>
      <tr>
        
        <td colspan="8" style="text-align:center">
          <input type="button" name="" class="button" value="添加" id="add" onclick="javascript:window.location.href='technical_information_edit.php?action=add&specification_id=<?php echo $specificationid ?>'">
          <input type="button" name="button" value="返回" class="button" onclick="window.location.href='technical_information.php'" />
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