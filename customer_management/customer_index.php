<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$employee_id = $_SESSION['employee_info']['employeeid'];

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
//获取当前页面的路径
$system_url =  dirname(__FILE__);

$system_pos =  strrpos($system_url,DIRECTORY_SEPARATOR);
$system_url = substr($system_url,$system_pos);
//通过路径查询对应的模块id
$system_id_sql = "SELECT `systemid` FROM `db_system` WHERE `system_dir` LIKE '%$system_url%'";
$system_id_res = $db->query($system_id_sql);
$system_id = $system_id_res->fetch_row()[0];
if($system_id ==' '){
	header('location:../myjtl/index.php');
}
//查询登录用户是否是客户管理的管理员
$system_sql = "SELECT `isadmin` FROM `db_system_employee` WHERE `employeeid`='$employee_id' AND `systemid`=".$system_id;
$system_res = $db->query($system_sql);

$system_info = [];
while($system_admin = $system_res->fetch_row()){
	$system_info = $system_admin;
}

//判断是否是管理员来决定查询方法

if($system_info[0] == '1'){
	$sql = "SELECT * FROM `db_customer_info` WHERE `status` = '1' ";
} else {
	$sql = "SELECT * FROM `db_customer_info` WHERE `status` = '1' AND `adder_id` = '$employee_id'".$sqlwhere;
	}

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
<title>模具报价-嘉泰隆</title>
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
        <th style="">添加时间</th>
        <th style="">客户名称</th>
        <th style="">客户代码</th>
        <th style="">客户类型</th>
        <th style="">联系人</th>
        <th style="">手机号</th>
        <th style="">邮箱</th>
        <th style="">地址</th>
        <th style="">负责人</th>
        <th style="">职务</th>
        <th style="">跟进状态</th>
        <th style="">修改</th>
      <?php
      while($row = $result->fetch_assoc()){

      	$id = $row['cusomer_id'];
	  $count = array_key_exists($id,$array_group)?$array_group[$id]:0;
	  //获取联系人的信息
	  if(strstr($row['contacts_name'],'$$')){
	  	$contacts_name = explode('$$',$row['contacts_name']);
	  	$contacts_phone = explode('$$',$row['contacts_phone']);
	  	$contacts_email = explode('$$',$row['contacts_email']);

	  } else {
	  	$contacts_name = $row['contacts_name'];
	  	$contacts_phone = $row['contacts_phone'];
	  	$contacts_email = $row['contacts_email'];
	  }

	  ?>
     <tr class="show">
     <i class="info_list">
        <td><input type="checkbox" name="id[]" value="<?php echo $row['customer_id']; ?>"<?php if($count > 0) echo " disabled=\"disabled\""; ?> /></td>
        <td class="show_list"><?php echo date('Y-m-d',$row['add_time']) ?></td>     
        <td class="show_list"><?php echo $row['customer_name'] ?></td>
        <td class="show_list"><?php echo $row['customer_code']; ?></td>
        <td class="show_list"><?php echo $row['customer_type']; ?></td> 
        <td class="show_list"><?php echo getin($contacts_name) ?></td>
        <td class="show_list"><?php echo getin($contacts_phone) ?></td>
        <td class="show_list"><?php echo getin($contacts_email) ?></td>
        <td class="show_list"><?php echo $row['customer_address'] ?></td>
        <td class="show_list"><?php echo $row['boss_name']; ?></td>
        <td class="show_list"><?php echo $row['boss_unit']; ?></td>
        <td class="show_list"><?php echo $row['customer_status']; ?></td>
        </i>	
            <input type="hidden" class="mold_id_val" value="<?php echo $row['mold_id'] ?>"></span></td>
      <!-- <td><a href="mould_quote_list.php?id=<?php echo $mould_dataid; ?>"><img src="../images/system_ico/quote_11_12.png" width="11" height="12" /></a></td> -->
        <td><?php if($count == 0){ ?><a href="customer_edit.php?id=<?php echo $row['customer_id']; ?>&action=edit"><input type="button" value="修改"></a><?php } ?> </td>
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