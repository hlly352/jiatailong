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
<title>模具报价-希尔林</title>
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
#help_info .p2{color:blue;padding-left:15px;font-size:16px;font-family:'黑体';}
</style>
</head>

<body>
<?php include "header.php"; ?>
<div id="help_info">
  <dl>
    <dt>客户管理模块操作说明</dt>
    <dd class="tag"> 客户管理>>2019-05-13</dd>
    <dd class="content">1.新建客户：操作者或管理者添加新客户的信息和首次跟进状态。</dd>
    <dd class="content">2.跟进客户：记录、查看客户的跟进状态。</dd>
    <dd class="content">3.客户列表：汇总、查看客户的所有信息。</dd>
  </dl>
  <p class="p1">特别说明 :</p>
<p class="p2">
    本系统资料属我司最重要商业秘密，必须须保密。未经我方明确许可，不得传递，复制，使用或泄露其内容。保留所有权利，特别是申请知识产权保护之权利。

  </p>
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