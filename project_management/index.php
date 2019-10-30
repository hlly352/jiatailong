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
<link href="css/main.css?v=603" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" type="text/css" href="../images/logo/jtl.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>项目管理-苏州嘉泰隆</title>
</head>
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
    <dt>项目管理模块操作说明</dt>
    <dd class="tag"> 项目管理>>2019-10-24</dd>
    <dd class="content">1.新的项目：由市场人有名发起的项目汇总，项目人员负责完善项目的技术资料，管理人员负责审核已完成资料的项目。</dd>
    <dd class="content">2.项目汇总：查询所有通过审核的项目信息，并可以导出电子表格。</dd>
    <dd class="content">3.项目信息：关于项目的所有的重要资料汇总。</dd>
    <dd class="content">4.技术资料：查看项目的技术性资料，项目人员可以更改相应的技术资料。</dd>
      <p class="p1">特别说明 :</p>
  <p class="p2">
    本系统资料属我司最重要商业秘密须保密。未经我方明确许可，不得传递，复制，使用或泄露其内容。保留所有权利，特别是申请知识产权保护之权利。

  </p>
  </dl>
</div>
<body>
<?php
$sql = "SELECT `db_system_help`.`helpid`,`db_system_help`.`help_title`,DATE_FORMAT(`db_system_help`.`dotime`,'%Y-%m-%d') AS `dodate`,DATEDIFF(CURDATE(),`db_system_help`.`dotime`) AS `diff_date` FROM `db_system_help` INNER JOIN `db_system` ON `db_system`.`systemid` = `db_system_help`.`systemid` WHERE `db_system_help`.`help_status` = 1 AND `db_system`.`system_dir` = '$system_dir' ORDER BY `db_system_help`.`dotime` DESC";
$result = $db->query($sql);
if($result->num_rows){
?>
<div id="help_list">
  <h4>系统说明</h4>
  <ul>
    <?php
	$i = 1;
    while($row = $result->fetch_assoc()){
	?>
    <li><b><?php echo $i; ?>.</b> <a href="../myjtl/system_help_info.php?id=<?php echo $row['helpid']; ?>" target="_blank"<?php if($row['diff_date'] <= 7) echo " style=\"color:#03F\""; ?>><?php echo $row['help_title']; ?></a> <span> <?php echo $row['dodate']; ?></span></li>
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