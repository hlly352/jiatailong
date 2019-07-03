<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>采购管理-希尔林</title>
<style>
#help_info {
  width:980px;
  margin:10px auto;
}
#help_info dl dt {
  font-size:24px;
  text-align:center;
  padding:5px 0;
  margin-bottom: 20px;
}
#help_info dl dd.tag {
  margin:10px 0;
  padding:4px 0 4px 18px;
  border-bottom:1px solid #999;
  font-size:13px;
  color:#666;
  background:url(../images/system_ico/article_12_16.png) no-repeat  left center;
}
#help_info dl dd.content {
  font-size:15px;
  color:#333;
  margin-top:10px;
  line-height:24px;
  color: blue;
}
#help_info .p1{color:red;margin-top:20px;}
#help_info .p2{color:blue;font-size:16px;font-family:'黑体';}
</style>
</head>
<body>
<?php include "header.php"; ?>
  <div id="help_info">
  <dl>
    <dt> 采购管理操作流程：</dt>
    <dd class="tag">  采购管理>>2019-07-03</dd>
    <dd class="content">1、登录管理系统，进入<采购管理>页面。</dd>
    <dd class="content">2、点击<待询物料>进入该页面，勾选所需物料，点击‘添加’，物料会转入<询价单>。</dd>
    <dd class="content">3、点击<询价单>进入该页面，勾选所需物料，点击‘导出’，自动生成“采购询价单”，然后进行报价。</dd>
    <dd class="content">4、点击<采购订单>进入该页面，点击‘添加’，选择供应商、交期，生成新的订单，在新订单页面勾选所需的已确定好价格的物料，填好该物料的相关信息后点击‘添加’，再点击‘导出’，生成“购销合同”，等合作双方签字盖章后，再回到<采购订单>主页面，选择相应的合同，点击‘Edit’,将‘订单状态’改成‘已下单’。</dd>
  </dl>
</div>
<?php
$sql = "SELECT `db_system_help`.`helpid`,`db_system_help`.`help_title`,DATE_FORMAT(`db_system_help`.`dotime`,'%Y-%m-%d') AS `dodate`,DATEDIFF(CURDATE(),`db_system_help`.`dotime`) AS `diff_date` FROM `db_system_help` INNER JOIN `db_system` ON `db_system`.`systemid` = `db_system_help`.`systemid` WHERE `db_system_help`.`help_status` = 1 AND `db_system`.`system_dir` = '$system_dir' ORDER BY `db_system_help`.`dotime` DESC";
$result = $db->query($sql);
if($result->num_rows){
?>
<div id="help_list">
  <!-- <h4>系统说明</h4> -->
  <ul>
    <?php
	$i = 1;
    while($row = $result->fetch_assoc()){
	?>
    <!-- <li><b><?php echo $i; ?>.</b> <a href="../myjtl/system_help_info.php?id=<?php echo $row['helpid']; ?>" target="_blank"<?php if($row['diff_date'] <= 7) echo " style=\"color:#03F\""; ?>><?php echo $row['help_title']; ?></a> <span> <?php echo $row['dodate']; ?></span></li> -->
    <?php
	$i++;
	}
	?>
  </ul>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>