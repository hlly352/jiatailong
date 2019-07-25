<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
	$order_number = trim($_GET['order_number']);
	$supplierid = $_GET['supplierid'];
	if($supplierid){
		$sql_supplierid = " AND `db_material_order`.`supplierid` = '$supplierid'";
	}
	$order_status = $_GET['order_status'];
	if($order_status != NULL){
		$sql_order_status = " AND `db_material_order`.`order_status` = '$order_status'";
	}
	$sqlwhere = " AND `db_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid $sql_order_status";
}
$sql = "SELECT * FROM `db_material_funds_plan` INNER JOIN `db_employee` ON `db_material_funds_plan`.`employeeid` = `db_employee`.`employeeid` WHERE `db_material_funds_plan`.`plan_status` = 1 AND (`db_material_funds_plan`.`plan_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";

$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_funds_plan`.`plan_number` DESC" . $pages->limitsql;
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
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>付款计划</h4>
  <form action="" name="material_order" method="get">
    <table>
      <tr>
        <th>计划单号：</th>
        <td><input type="text" name="order_number" class="input_txt" /></td>
        <th>计划日期：</th>
        <td>
          <input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
        </td>
          </select></td>
  
        <td>
          <input type="submit" name="submit" value="查询" class="button" />
        </td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_orderid .= $row_id['orderid'].',';
	  }
	  $array_orderid = rtrim($array_orderid,',');
	  //订单明细数量
	  $sql_order_list = "SELECT `orderid`,COUNT(*) AS `count` FROM `db_material_order_list` WHERE `orderid` IN ($array_orderid) GROUP BY `orderid`";
	  $result_order_list = $db->query($sql_order_list);
	  if($result_order_list->num_rows){
		  while($row_order_list = $result_order_list->fetch_assoc()){
			  $array_order_list[$row_order_list['orderid']] = $row_order_list['count'];
		  }
	  }else{
		  $array_order_list = array();
	  }

  ?>
  <form action="material_funds_plando.php" name="material_order" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="16%">计划单号</th>
        <th width="10%">计划时间</th>
        <th width="10%">操作人</th>
        <th width="10%">操作时间</th>
        <th width="6%">项数</th>
        <th width="6%">计划状态</th>
        <th width="4%">操作</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $planid = $row['planid'];
      //查找项数
      $list_sql  = "SELECT COUNT(*) FROM `db_funds_plan_list` WHERE `planid` = '$planid'";
      $result_list = $db->query($list_sql);
      if($result_list->num_rows){
        $list_count = $result_list->fetch_row()[0];
      }
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $planid; ?>"<?php if( $employeeid != $row['employeeid']) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['plan_number']; ?></td>
        <td><?php echo $row['plan_date']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dodate']; ?></td>
        <td class="count" id="count-<?php echo $planid ?>"><?php echo $list_count; ?></td>
        <td>
          <span class="status" id="status-<?php echo $planid ?>">
            <?php echo $row['plan_status']; ?>  
          </span>
        </td>
        <td>
          <a href="funds_plan_info.php?planid=<?php echo $planid ?>">审核</a>
        </td>
      </tr>
      <?php } ?>
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
	  echo "<p class=\"tag\">系统提示：暂无订单记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>