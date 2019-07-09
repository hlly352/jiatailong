<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
	$entry_number = trim($_GET['entry_number']);
	$sqlwhere = " AND `db_godown_entry`.`entry_number` LIKE '%$entry_number%'";
}
$sql = "SELECT `db_godown_entry`.`entryid`,`db_godown_entry`.`entry_number`,`db_godown_entry`.`entry_date`,`db_godown_entry`.`employeeid`,`db_godown_entry`.`dotime`,`db_employee`.`employee_name` FROM `db_godown_entry` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_godown_entry`.`employeeid` WHERE `db_godown_entry`.`dotype` = 'M' AND (`db_godown_entry`.`entry_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_godown_entry`.`entry_number` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
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
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具物料入库单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>入库单号：</th>
        <td><input type="text" name="entry_number" class="input_txt" /></td>
        <th>入库单日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='material_godown_entryae.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <form action="material_godown_entrydo.php" name="godown_entry" method="post">
    <?php
    if($result->num_rows){
		while($row_id = $result_id->Fetch_assoc()){
			$array_entryid .= $row_id['entryid'].','; 
		}
		$array_entryid = trim($array_entryid,',');
		$sql_entry = "SELECT `db_godown_entry_list`.`entryid`,COUNT(*) AS `count` FROM `db_godown_entry_list` INNER JOIN `db_godown_entry` ON `db_godown_entry`.`entryid` = `db_godown_entry_list`.`entryid` WHERE `db_godown_entry_list`.`entryid` IN ($array_entryid) AND `db_godown_entry`.`dotype` = 'M' GROUP BY `db_godown_entry_list`.`entryid`";
		$result_entry = $db->query($sql_entry);
		if($result_entry->num_rows){
			while($row_entry =$result_entry->fetch_assoc()){
				$array_entry[$row_entry['entryid']] = $row_entry['count'];
			}
		}else{
			$array_entry = array();
		}
    ?>
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="20%">入库单号</th>
        <th width="20%">入库单日期</th>
        <th width="20%">制单人</th>
        <th width="20%">时间</th>
        <th width="4%">项数</th>
        <th width="4%">Add</th>
        <th width="4%">List</th>
        <th width="4%">Print</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $entryid = $row['entryid'];
		  $count = array_key_exists($entryid,$array_entry)?$array_entry[$entryid]:0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $entryid; ?>"<?php if($row['employeeid'] != $employeeid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['entry_number']; ?></td>
        <td><?php echo $row['entry_date']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $count; ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="material_godown_entry_list_add.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="material_godown_entry_list.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a><?php } ?></td>
        <td><?php if($count>0){ ?><a href="material_godown_entry_print.php?id=<?php echo $entryid; ?>" target="_blank"><img src="../images/system_ico/print_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
</body>
</html>
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
  $entry_number = trim($_GET['entry_number']);
  $sqlwhere = " AND `db_godown_entry`.`entry_number` LIKE '%$entry_number%'";
}
$sql = "SELECT `db_godown_entry`.`entryid`,`db_godown_entry`.`entry_number`,`db_godown_entry`.`entry_date`,`db_godown_entry`.`employeeid`,`db_godown_entry`.`dotime`,`db_employee`.`employee_name` FROM `db_godown_entry` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_godown_entry`.`employeeid` WHERE `db_godown_entry`.`dotype` = 'C' AND (`db_godown_entry`.`entry_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_godown_entry`.`entry_number` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
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
<title>物控管理-希尔林</title>
</head>

<body>
<div id="table_search">
  <h4>加工刀具入库单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>入库单号：</th>
        <td><input type="text" name="entry_number" class="input_txt" /></td>
        <th>入库单日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='cutter_godown_entryae.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <form action="material_godown_entrydo.php" name="godown_entry" method="post">
    <?php
    if($result->num_rows){
    while($row_id = $result_id->Fetch_assoc()){
      $array_entryid .= $row_id['entryid'].','; 
    }
    $array_entryid = trim($array_entryid,',');
    $sql_entry = "SELECT `db_godown_entry_list`.`entryid`,COUNT(*) AS `count` FROM `db_godown_entry_list` INNER JOIN `db_godown_entry` ON `db_godown_entry`.`entryid` = `db_godown_entry_list`.`entryid` WHERE `db_godown_entry_list`.`entryid` IN ($array_entryid) AND `db_godown_entry`.`dotype` = 'C' GROUP BY `db_godown_entry_list`.`entryid`";
    $result_entry = $db->query($sql_entry);
    if($result_entry->num_rows){
      while($row_entry =$result_entry->fetch_assoc()){
        $array_entry[$row_entry['entryid']] = $row_entry['count'];
      }
    }else{
      $array_entry = array();
    }
    //print_r($array_entry);
    ?>
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="20%">入库单号</th>
        <th width="20%">入库单日期</th>
        <th width="20%">制单人</th>
        <th width="20%">时间</th>
        <th width="4%">项数</th>
        <th width="4%">Add</th>
        <th width="4%">List</th>
        <th width="4%">Print</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $entryid = $row['entryid'];
      $count = array_key_exists($entryid,$array_entry)?$array_entry[$entryid]:0;
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $entryid; ?>"<?php if($row['employeeid'] != $employeeid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['entry_number']; ?></td>
        <td><?php echo $row['entry_date']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $count; ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="cutter_godown_entry_list_add.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="cutter_godown_entry_list.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a><?php } ?></td>
        <td><?php if($count>0){ ?><a href="cutter_godown_entry_print.php?id=<?php echo $entryid; ?>" target="_blank"><img src="../images/system_ico/print_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
</body>
</html>
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
  $entry_number = trim($_GET['entry_number']);
  $sqlwhere = " AND `db_godown_entry`.`entry_number` LIKE '%$entry_number%'";
}
$sql = "SELECT `db_godown_entry`.`entryid`,`db_godown_entry`.`entry_number`,`db_godown_entry`.`entry_date`,`db_godown_entry`.`employeeid`,`db_godown_entry`.`dotime`,`db_employee`.`employee_name` FROM `db_godown_entry` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_godown_entry`.`employeeid` WHERE `db_godown_entry`.`dotype` = 'M' AND (`db_godown_entry`.`entry_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_godown_entry`.`entry_number` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
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
<title>物控管理-希尔林</title>
</head>

<body>
<div id="table_search">
  <h4>期间物料入库单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>入库单号：</th>
        <td><input type="text" name="entry_number" class="input_txt" /></td>
        <th>入库单日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='material_godown_entryae.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <form action="material_godown_entrydo.php" name="godown_entry" method="post">
    <?php
    if($result->num_rows){
    while($row_id = $result_id->Fetch_assoc()){
      $array_entryid .= $row_id['entryid'].','; 
    }
    $array_entryid = trim($array_entryid,',');
    $sql_entry = "SELECT `db_godown_entry_list`.`entryid`,COUNT(*) AS `count` FROM `db_godown_entry_list` INNER JOIN `db_godown_entry` ON `db_godown_entry`.`entryid` = `db_godown_entry_list`.`entryid` WHERE `db_godown_entry_list`.`entryid` IN ($array_entryid) AND `db_godown_entry`.`dotype` = 'M' GROUP BY `db_godown_entry_list`.`entryid`";
    $result_entry = $db->query($sql_entry);
    if($result_entry->num_rows){
      while($row_entry =$result_entry->fetch_assoc()){
        $array_entry[$row_entry['entryid']] = $row_entry['count'];
      }
    }else{
      $array_entry = array();
    }
    ?>
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="20%">入库单号</th>
        <th width="20%">入库单日期</th>
        <th width="20%">制单人</th>
        <th width="20%">时间</th>
        <th width="4%">项数</th>
        <th width="4%">Add</th>
        <th width="4%">List</th>
        <th width="4%">Print</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $entryid = $row['entryid'];
      $count = array_key_exists($entryid,$array_entry)?$array_entry[$entryid]:0;
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $entryid; ?>"<?php if($row['employeeid'] != $employeeid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['entry_number']; ?></td>
        <td><?php echo $row['entry_date']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $count; ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="material_godown_entry_list_add.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="material_godown_entry_list.php?entryid=<?php echo $entryid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a><?php } ?></td>
        <td><?php if($count>0){ ?><a href="material_godown_entry_print.php?id=<?php echo $entryid; ?>" target="_blank"><img src="../images/system_ico/print_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>