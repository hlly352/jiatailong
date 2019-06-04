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
<title>订单管理-嘉泰隆</title>
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
    <dt>订单管理模块操作说明</dt>
    <dd class="tag"> 订单管理>>2019-05-31</dd>
    <dd class="content">1.临时订单：新建不通过正常报价流程的临时性订单，例如：打样订单，改模订单...... 特别提示：正常流程的订单，从《模具报价》模块中的“报价汇总”发起。</dd>
    <dd class="content">2.订单审核：成交的订单经由财务人员审核，市场人员和财务人员可对未审核的订单进行订正。</dd>
    <dd class="content">3.订单汇总：查看所有经过审核的订单及启动该订单的项目。</dd>
    <dd class="content">4.收款管理：市场人员和财务人员对订单的收款情况进行跟踪和管理。</dd>
    <dd class="content">5.发票管理：财务人员对收款的发票情况进行跟踪和管理。</dd>
      <p class="p1">特别说明 :</p>
  <p class="p2">
    本系统资料属我司最重要商业秘密须保密。未经我方明确许可，不得传递，复制，使用或泄露其内容。保留所有权利，特别是申请知识产权保护之权利。

  </p>
  </dl>
</div>
 
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