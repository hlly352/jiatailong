<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
if($_GET['submit']){
	$mould_name = trim($_GET['mould_name']);
	$quote_status = $_GET['quote_status'];
	if($quote_status != NULL){
		$sql_status = " AND `db_mould_quote`.`quote_status` = '$quote_status'";
	}
	$sqlwhere = " AND `db_mould_data`.`mould_name` LIKE '%$mould_name%' $sql_status";
}
$sql= "SELECT `db_mould_quote`.`quoteid`,`db_mould_quote`.`quote_date`,`db_mould_quote`.`ver_num`,`db_mould_quote`.`total_price`,`db_mould_quote`.`total_price_usd`,`db_mould_quote`.`total_price_vat`,`db_mould_quote`.`total_price_txn`,`db_mould_quote`.`quote_status`,`db_mould_data`.`mould_name`,`db_mould_data`.`client_name`,`db_mould_data`.`project_name`,`db_employee`.`employee_name` FROM `db_mould_quote` INNER JOIN `db_mould_data` ON `db_mould_data`.`mould_dataid` = `db_mould_quote`.`mould_dataid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_quote`.`employeeid` WHERE (`db_mould_quote`.`quote_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_quote`.`mould_dataid` DESC,`db_mould_quote`.`ver_num` ASC" . $pages->limitsql;
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
<title>模具报价-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具数据</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具名称：</th>
        <td><input type="text" name="mould_name" class="input_txt" /></td>
        <th>报价日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>状态：</th>
        <td><select name="quote_status">
            <option value="">所有</option>
            <?php foreach($array_quote_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($quote_status == $status_key && $quote_status != NULL) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查找" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <caption>
    报价列表
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">模具名称</th>
      <th width="8%">客户名称</th>
      <th width="8%">项目名称</th>
      <th width="8%">报价日期</th>
      <th width="8%">报价人</th>
      <th width="8%">版本</th>
      <th width="10%">价格(不含税)</th>
      <th width="10%">价格(USD)</th>
      <th width="10%">价格(含税)</th>
      <th width="10%">优惠价(含税)</th>
      <th width="8%">状态</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		$quote_status = $row['quote_status'];
		$total_price_txn = $quote_status?number_format($row['total_price_txn'],2):'--'; 
	?>
    <tr>
      <td><?php echo $row['quoteid']; ?></td>
      <td><?php echo $row['mould_name']; ?></td>
      <td><?php echo $row['client_name']; ?></td>
      <td><?php echo $row['project_name']; ?></td>
      <td><?php echo $row['quote_date']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['ver_num']; ?></td>
      <td><?php echo number_format($row['total_price'],2); ?></td>
      <td><?php echo number_format($row['total_price_usd'],2); ?></td>
      <td><?php echo number_format($row['total_price_vat'],2); ?></td>
      <td><?php echo $total_price_txn; ?></td>
      <td><?php echo $array_quote_status[$quote_status]; ?></td>
    </tr>
    <?php } ?>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无报价！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>