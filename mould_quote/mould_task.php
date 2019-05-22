<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
if($_GET['submit']){
  $mould_name = trim($_GET['mould_name']);
  $client_name = trim($_GET['client_name']);
  $project_name = trim($_GET['project_name']);
  $sqlwhere = "  AND `client_name` LIKE '%$client_name%' AND `mould_name` LIKE '%$mould_name%' AND `project_name` LIKE '%$project_name%'";
}

//sql语句
$sql = "SELECT * FROM `db_mould_task` WHERE `task_status` = '1'".$sqlwhere;

$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `task_time` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sqllist);
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

<title>模具报价-嘉泰隆</title>
<style type="text/css">
  #main{table-layout:fixed;width:1350px;}
  #main tr td{word-wrap:break-word;word-break:break-all;}
  #main tr td input{width:120px;}
</style>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4 style="padding-left:10px">
     
  </h4>
  <form action="" name="search" method="get">
    <table >

      <tr>
   
       </tr>
       <tr>
       <td>客户名称</td>
       <td><input type="text" name="client_name" class="input_txt" /></td>
       <td></td>
       <td>项目名称</td>
       <td><input type="text" name="project_name" class="input_txt"></td>
       <td></td>
        <td>模具名称</td>
        <td><input type="text" name="mould_name" class="input_txt" /></td>
        <td>报价日期</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查找" class="button" />
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <form action="mould_taskdo.php?action=add" name="list" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
     <caption style="font-size:25px">苏州嘉泰隆机械科技有限公司<br>临时任务</caption>
      <tr>
        <th style="">时间</th>
        <th style="">客户名称</th>
        <th style="">客户代码</th>
        <th style="">模号</th>
        <th style="">模具名称</th>
        <th style="">规格</th>
        <th style="">材质</th>
        <th style="">数量</th>
        <th style="">单价(RMB)</th>
        <th style="">金额(RMB)</th>
        <th style="">备注</th>
      <?php
       if($result->num_rows){
          while($row = $result->fetch_assoc()){
      
    ?>
     <tr class="show">
        <td class="show_list"><?php echo $row['task_time'] ?></td>
        <td class="show_list"><?php echo $row['customer_name'] ?></td>
        <td class="show_list"><?php echo $row['customer_code']?></td>
        <td class="show_list"><?php echo $row['mould_no']?></td>
        <td class="show_list"><?php echo $row['mould_name']?></td>
        <td class="show_list"><?php echo $row['size']?></td>
        <td class="show_list"><?php echo $row['material']?></td>
        <td class="show_list"><?php echo $row['number']?></td>
        <td class="show_list"><?php echo $row['unit_price']?></td>
        <td class="show_list"><?php echo $row['price']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>

      </tr> 
      <?php } ?>

    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
 
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{?>
      
           <tr class="show">
              <td class="show_list"><input type="text" name="task_time"></td>
              <td class="show_list"><input type="text" name="customer_name"></td>
              <td class="show_list"><input type="text" name="customer_code"></td>
              <td class="show_list"><input type="text" name="mould_no"></td>
              <td class="show_list"><input type="text" name="mould_name"></td>
              <td class="show_list"><input type="text" name="size"></td>
              <td class="show_list"><input type="text" name="material"></td>
              <td class="show_list"><input type="text" name="number"></td>
              <td class="show_list"><input type="text" name="unit_price"></td>
              <td class="show_list"><input type="text" name="price"></td>
              <td class="show_list"><input type="text" name="notes"></td>
          </tr>
          <tr>
              <td colspan="11" style="align:center"><input type="submit" value="保存" ></td>
          </tr>
    </form>
  </table>
<?php    }  ?>
</div>
 <?php include "../footer.php"; ?>
</body>
</html>