<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if(isset($_GET['id'])){
	$notice_typeid = fun_check_int($_GET['id']);
	$sql_notice_typeid = " AND `notice_typeid` = '$notice_typeid'";
}
if($_GET['submit']){
	$notice_title = $_GET['keyword'];
	$sql_notice_title = " AND `notice_title` LIKE '%$notice_title%'";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/base.css" type="text/css" rel="stylesheet" />
<link href="../css/myjtl.css" type="text/css" rel="stylesheet" />
<link href="../css/notice.css?v=20181108" type="text/css" rel="stylesheet" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<title>我的希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="myjtl_tag">
  <h4><a href="/myjtl/">回首页</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;通知公告</h4>
</div>
<?php
$sql_notice = "SELECT `noticeid`,`notice_title`,`notice_hits`,DATE_FORMAT(`dotime`,'%Y-%m-%d') AS `dodate`,DATEDIFF(CURDATE(),DATE_FORMAT(`dotime`,'%Y-%m-%d')) AS `diff_date` FROM `db_notice` WHERE `notice_status` = 1 $sql_notice_typeid $sql_notice_title";
$result_notice = $db->query($sql_notice);
$pages = new page($result_notice->num_rows,12);
$sqllist = $sql_notice . " ORDER BY `db_notice`.`noticeid` DESC" . $pages->limitsql;
$result_notice = $db->query($sqllist);
?>
<div id="notice">
  <div id="notice_left">
    <?php
    if($result_notice->num_rows){
	?>
    <div id="notice_list">
      <?php
      while($row_notice = $result_notice->fetch_assoc()){
		  $diff_date = $row_notice['diff_date'];
	  ?>
      <dl>
        <dd><a href="notice.php?id=<?php echo $row_notice['noticeid']; ?>"<?php if($diff_date <= 7) echo " style=\"color:#063\""; ?>><?php echo $row_notice['notice_title']; ?></a></dd>
        <dt>浏览：<?php echo $row_notice['notice_hits']."/".$row_notice['dodate']; ?></dt>
      </dl>
      <?php } ?>
      <div class="clear"></div>
    </div>
    <div id="page">
      <?php $pages->getPage();?>
    </div>
    <?php }else{ echo "<p class=\"tag\">暂无</p>"; } ?>
  </div>
  <div id="notice_right">
    <?php
    $sql_notice_type = "SELECT `notice_typeid`,`notice_typename` FROM `db_notice_type`";
	$result_notice_type = $db->query($sql_notice_type);
	if($result_notice_type->num_rows){	
	?>
    <div id="notice_type">
      <ul>
        <li><a href="/notice/">所有类型</a></li>
        <?php while($row_notice_type = $result_notice_type->fetch_assoc()){ ?>
        <li><a href="?id=<?php echo $row_notice_type['notice_typeid']; ?>"><?php echo $row_notice_type['notice_typename']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
    <?php } ?>
    <div id="notice_search">
      <form action="/notice/" name="search" method="get">
        <input type="text" name="keyword" class="search_input_txt" />
        <input type="submit" name="submit" value="搜索" class="seacr_button" />
      </form>
    </div>
    <?php
    $sql_top = "SELECT `noticeid`,`notice_title` FROM `db_notice` WHERE `notice_status` = 1 ORDER BY `notice_hits` DESC,`noticeid` DESC LIMIT 0,10";
	$result_top = $db->query($sql_top);
	if($result_top->num_rows){
	?>
    <div id="notice_top">
      <h4>点击排行 Top10</h4>
      <ul>
        <?php while($row_top = $result_top->fetch_assoc()){ ?>
        <li><a href="notice.php?id=<?php echo $row_top['noticeid']; ?>"><?php echo $row_top['notice_title']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
    <?php } ?>
  </div>
  <div class="clear"></div>
</div>
<?php include "../footer.php"; ?>
</body>
</html>