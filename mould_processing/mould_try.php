<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(2,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	$supplierid = $_GET['supplierid'];
	if($supplierid){
		$sql_supplierid = " AND `db_mould_try`.`supplierid` = '$supplierid'";
	}
	$try_status = $_GET['try_status'];
	if($try_status != NULL){
		$sql_try_status = " AND `db_mould_try`.`try_status` = '$try_status'";
	}
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' $sql_supplierid $sql_try_status";
}else{
	$try_status = 1;
	$sqlwhere = " AND `db_mould_try`.`try_status` = '$try_status'";
}
$sql = "SELECT `db_mould_try`.`tryid`,`db_mould_try`.`order_number`,`db_mould_try`.`try_date`,CONCAT('T',`db_mould_try`.`try_times`) AS `try_times`,CONCAT('T',`db_mould_try`.`tonnage`) AS `tonnage`,`db_mould_try`.`unit_price`,`db_mould_try`.`cost`,`db_mould_try`.`remark`,`db_mould_try`.`try_status`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_try_cause`.`try_causename` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_try`.`supplierid` INNER JOIN `db_mould_try_cause` ON `db_mould_try_cause`.`try_causeid` = `db_mould_try`.`try_causeid` WHERE (`db_mould_try`.`try_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_allid =$db->query($sql); 
$_SESSION['mould_try'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_try`.`tryid` DESC" . $pages->limitsql;
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
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>试模记录</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>试模日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
					echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
				}
			}
			?>
          </select></td>
        <th>状态：</th>
        <td><select name="try_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $try_status && $try_status != NULL) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_moult_try.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_allid = $result_allid->fetch_assoc()){
		  $array_all_tryid .= $row_allid['tryid'].',';
	  }
	  $array_all_tryid = rtrim($array_all_tryid,',');
	  while($row_id = $result_id->fetch_assoc()){
		  $array_tryid .= $row_id['tryid'].',';
	  }
	  $array_tryid = rtrim($array_tryid,',');
	  //计算总费用
	  $sql_cost = "SELECT SUM(`cost`) AS `total_cost` FROM `db_mould_try` WHERE `tryid` IN ($array_all_tryid)";
	  $result_cost = $db->query($sql_cost);
	  $array_cost = $result_cost->fetch_assoc();
	  //统计是否有附件
	  $sql_file = "SELECT `linkid`,COUNT(*) AS `count` FROM `db_upload_file` WHERE `linkcode` = 'MT' AND `linkid` IN ($array_tryid) GROUP BY `linkid`";
	  $result_file = $db->query($sql_file);
	  if($result_file->num_rows){
		  while($row_file = $result_file->fetch_assoc()){
			  $array_file[$row_file['linkid']] = $row_file['count'];
		  }
	  }else{
		  $array_file = array();
	  }
  ?>
  <form action="mould_trydo.php" name="mould_try_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="8%">模具编号</th>
        <th width="8%">供应商</th>
        <th width="8%">送货单号</th>
        <th width="8%">试模日期</th>
        <th width="6%">试模次数</th>
        <th width="8%">试模原因</th>
        <th width="6%">啤机吨位</th>
        <th width="6%">含税单价</th>
        <th width="6%">金额</th>
        <th width="16%">备注</th>
        <th width="4%">状态</th>
        <th width="4%">File</th>
        <th width="4%">Edit</th>
        <th width="4%">Info</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $tryid = $row['tryid'];
		  $file_count = array_key_exists($tryid,$array_file)?$array_file[$tryid]:0;
		  $file_image = ($file_count>0)?"<img src=\"../images/system_ico/file_10_10.png\" width=\"10\" height=\"10\" />":"<img src=\"../images/system_ico/upload_10_10.png\" width=\"10\" height=\"10\" />";
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $tryid; ?>" /></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['try_date']; ?></td>
        <td><?php echo $row['try_times']; ?></td>
        <td><?php echo $row['try_causename']; ?></td>
        <td><?php echo $row['tonnage']; ?></td>
        <td><?php echo $row['unit_price']; ?></td>
        <td><?php echo $row['cost']; ?></td>
        <td><?php echo $row['remark']; ?></td>
        <td><?php echo $array_status[$row['try_status']]; ?></td>
        <td><a href="upload_file.php?id=<?php echo $tryid; ?>&type=MT"><?php echo $file_image; ?></a></td>
        <td><a href="mould_tryae.php?id=<?php echo $tryid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
        <td><a href="mould_try_info.php?id=<?php echo $tryid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="9">Total</td>
        <td><?php echo $array_cost['total_cost']; ?></td>
        <td colspan="6">&nbsp;</td>
      </tr>
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