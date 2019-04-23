<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
if($_GET['submit']){
	$customer_name = trim($_GET['customer_name']);
	$customer_code = trim($_GET['customer_code']);
	$customer_contacts = trim($_GET['customer_contacts']);
	$customer_phone = trim($_GET['customer_phone']);
	$sqlwhere = "  AND `customer_name` LIKE '%$customer_name%' AND `customer_code` LIKE '%$customer_code%' AND `customer_contacts` LIKE '%$custmer_phone%' AND `customer_contacts` LIKE '%$custmer_contacts%'";
}
/*$sql = "SELECT * FROM `db_mould_data` 
WHERE time in (
SELECT max(a.time)
FROM db_mould_data a
GROUP BY mold_id)".$sqlwhere;*/
$sql = "SELECT * FROM `db_customer_info` WHERE `customer_status` = '1'".$sqlwhere;

$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `add_time` DESC" . $pages->limitsql;
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
<script type="text/javascript">
function getdate(timestamp) {
        var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
        var Y = date.getFullYear() + '-';
        var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
        var D = date.getDate() + ' ';
        var h = date.getHours() + ':';
        var m = date.getMinutes() + ':';
        var s = date.getSeconds();
        return Y+M+D;
    }
	
      </script>
<title>模具报价-希尔林</title>
<style type="text/css">
	#main{table-layout:fixed;width:1350px;}
	#main tr td{word-wrap:break-word;word-break:break-all;}
	
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
       <td><input type="text" name="customer_name" class="input_txt" /></td>
       <td></td>
       <td>项目代码</td>
       <td><input type="text" name="customer_code" class="input_txt"></td>
       <td></td>
        <td>联系人</td>
        <td><input type="text" name="customer_contacts" class="input_txt" /></td>
        <td>手机号</td>
        <td><input type="text" name="customer_phone" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查找" class="button" />
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){

		  $array_customer_id .= $row_id['id'].',';
	  }
	  $array_customer_id = rtrim($array_customer_id,',');

	  $sql_group = "SELECT `id`,COUNT(*) AS `count` FROM `db_customer_info` WHERE `id` IN ($array_customer_id) AND `customer_status` = '0' GROUP BY `id`";
	
	  $result_group = $db->query($sql_group);
	  if($result_group->num_rows){
		  while($row_group = $result_group->fetch_assoc()){
		  	
			  $array_group[$row_group['id']] = $row_group['count'];
		  }
	  }else{
		  $array_group = array();
	  }
	
  ?>
  <form action="customer_datado.php" name="list" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
      <tr>
        <th style="">ID</th>
        <th style="">客户名称</th>
        <th style="">客户代码</th>
        <th style="">客户系数</th>
        <th style="">联系人</th>
        <th style="">手机号</th>
        <th style="">邮箱</th>
        <th style="">地址</th>
        <th style="">添加时间</th>
        <th style="">修改</th>
      <?php
      while($row = $result->fetch_assoc()){

      	$id = $row['id'];
	  $count = array_key_exists($id,$array_group)?$array_group[$id]:0;
	  ?>
     <tr class="show">
     <i class="info_list">
        <td><input type="checkbox" name="id[]" value="<?php echo $row['id']; ?>"<?php if($count > 0) echo " disabled=\"disabled\""; ?> /></td>
        <td class="show_list"><?php echo $row['customer_name'] ?></td>
        <td class="show_list"><?php echo $row['customer_code']; ?></td>
        <td class="show_list"><?php echo $row['customer_value']; ?></td> 
        <td class="show_list"><?php echo $row['customer_contacts'] ?></td>
        <td class="show_list"><?php echo $row['customer_phone'] ?></td>
        <td class="show_list"><?php echo $row['customer_email']; ?></td>
        <td class="show_list"><?php echo $row['customer_address']; ?></td>
        <td class="show_list"><?php echo date('Y-m-d',$row['add_time']) ?></td>     	
        </i>	
            <input type="hidden" class="mold_id_val" value="<?php echo $row['mold_id'] ?>"></span></td>
      <!-- <td><a href="mould_quote_list.php?id=<?php echo $mould_dataid; ?>"><img src="../images/system_ico/quote_11_12.png" width="11" height="12" /></a></td> -->
        <td><?php if($count == 0){ ?><a href="mould_dataae.php?id=<?php echo $mould_dataid; ?>&action=edit"><input type="button" value="修改"></a><?php } ?> </td>
      </tr> 
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>