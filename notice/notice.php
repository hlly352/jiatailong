<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$noticeid = fun_check_int($_GET['id']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/base.css" type="text/css" rel="stylesheet" />
<link href="../css/myjtl.css" type="text/css" rel="stylesheet" />
<link href="../css/notice.css" type="text/css" rel="stylesheet" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<title>我的希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="myjtl_tag">
  <h4><a href="/myjtl/">MY WORK</a> >> <a href="/notice/">通知公告</a></h4>
</div>
<?php
$sql_notice = "SELECT `db_notice`.`noticeid`,`db_notice`.`notice_title`,`db_notice`.`notice_content`,`db_notice`.`notice_hits`,`db_notice`.`dotime`,`db_notice_type`.`notice_typename` FROM `db_notice` INNER JOIN `db_notice_type` ON `db_notice_type`.`notice_typeid` = `db_notice`.`notice_typeid` WHERE `db_notice`.`notice_status` = 1 AND `db_notice`.`noticeid` = '$noticeid'";
$result_notice = $db->query($sql_notice);
?>
<div id="notice">
  <div id="notice_left">
    <?php
    if($result_notice->num_rows){
		$array_notice = $result_notice->fetch_assoc();
		$db->query("UPDATE `db_notice` SET `notice_hits` = `notice_hits`+1 WHERE `noticeid` = '$noticeid'");
	?>
    <div id="notice_content_main">
      <h2><?php echo $array_notice['notice_title']; ?></h2>
      <h5>类型：<?php echo $array_notice['notice_typename']; ?> 浏览：<?php echo $array_notice['notice_hits']; ?> 发布时间：<?php echo $array_notice['dotime']; ?></h5>
      <div id="notice_content"><?php echo $array_notice['notice_content']; ?></div>
    </div>
    <?php
	$sqlpreid = "SELECT `noticeid`,`notice_title` FROM `db_notice` WHERE `noticeid` < '$noticeid' AND `notice_status` = 1 ORDER BY `noticeid` DESC LIMIT 0,1";
	$result_preid = $db->query($sqlpreid);
	if($result_preid->num_rows){
		$arr_preid= $result_preid->fetch_assoc();
		$prelink = "上一篇：<a href=\"notice.php?id=" . $arr_preid['noticeid'] ."\">" . $arr_preid['notice_title'] . "</a>";
	}else{
		$prelink = "上一篇：暂无";
	}
	$sqlnextid = "SELECT `noticeid`,`notice_title` FROM `db_notice` WHERE `noticeid` > '$noticeid' AND `notice_status` = 1 ORDER BY `noticeid` ASC LIMIT 0,1";
	$result_nextid = $db->query($sqlnextid);
	if($result_nextid->num_rows){
		$arr_nextid = $result_nextid->fetch_assoc();
		$nextlink = "下一篇：<a href=\"notice.php?id=" . $arr_nextid['noticeid']. "\">" . $arr_nextid['notice_title'] . "</a>";
	}else{
		$nextlink = "下一篇：暂无";
	}
	?>
    <div id="notice_link">
      <ul>
        <li><?php echo $prelink; ?></li>
        <li><?php echo $nextlink; ?></li>
      </ul>
    </div>
    <?php } ?>
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