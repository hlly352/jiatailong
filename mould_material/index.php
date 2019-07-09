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
<title>模具物料-希尔林</title>
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
  padding-left: 13px;
}
#help_info .p1{color:red;margin-top:20px;}
#help_info .p2{color:blue;font-size:16px;font-family:'黑体';}
</style>
</head>

<body>
<?php include "header.php"; ?>
<div id="help_info">
  <dl>
    <dt> 物料申请使用流程：</dt>
    <dd class="tag"> 物料申请>>2019-07-01</dd>
    <dd>一、模具物料申请（与模具物料编号相关的所有物料）</dd>
    <dd class="content"> 1、一般物料
      模具物料申请→对应模号→下载模板→将要上传的物料信息粘贴到模板→确定上传。</dd>
    <dd class="content"> 2、CNC铜料
    模具物料申请→对应模号→模具物料添加→填写物料信息（其中规格栏填写铜料重量，红色标记必填项）→确定上传。</dd>
    <dd class="content"> 3、模具物料的更改
      上传后的物料可在模具物料栏中查询到，系统自动按照上传时间排序，最新上传的在最前面。如需更改点击最后的编辑按钮，修改后再点击确定。</dd>
    <dd class="content"> 4、模具物料的删除
      在物料可编辑的情况下，可以删除物料。选择要删除的物料，在复选框中打钩，点击删除即可。
      物料不可编辑时则需采购部删除订单后再进行修改，修改流程同上。</dd>
    <br>
    <dd>二、期间物料申请（无法确定到单套模具的物料，例如易耗品，办公用品，福利品...）</dd>
    <dd class="content">
       1、申请期间物料：申请人填写物料信息，申请期间物料。
    </dd>
    <dd class="content">
      2、审批期间物料：部门经理对申请的物料信息进行审批。
    </dd>
    <dd class="content">
      3、查看申请记录：查看所有期间物料的申请记录。
    </dd>
  </dl>
      <p class="p1">特别说明 :</p>
      <p class="p2">
         1、 模板的前三列（料单编号、料单序号、物料编码）需为文本格式；
      </p>
      <p class="p2">
      2、 模板导入前需将第一行删除；
      </p>
      <p class="p2">
      3、 钢料尺寸规格后面需加“#”，如未添加会显示橙色警报；
      </p>
      <p class="p2">
      4、 上传的物料必填项未填写完整时会显示黄色警报，必须填写完整后采购才能下单；
      </p>
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