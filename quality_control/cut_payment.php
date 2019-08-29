<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
if($_GET['submit']){
  $supplierid = $_GET['supplierid'];
  if($supplierid){
    $sql_supplierid = " AND `db_cut_payment`.`supplierid` = '$supplierid'";
  }
  $cut_payment_type = $_GET['cut_payment_type'];
  $sqlwhere = "$sql_supplierid AND `db_cut_payment`.`cut_payment_type` LIKE '%$cut_payment_type%'";
}

$sql = "SELECT `db_cut_payment`.`cutid`,`db_cut_payment`.`order_number`,`db_cut_payment`.`material_name`,`db_cut_payment`.`specification`,`db_cut_payment`.`image`,`db_cut_payment`.`cut_cause`,`db_cut_payment`.`cut_payment`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,`db_cut_payment`.`add_time` FROM `db_cut_payment` INNER JOIN `db_employee` ON `db_cut_payment`.`employeeid` = `db_employee`.`employeeid` INNER JOIN `db_supplier` ON `db_cut_payment`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_cut_payment`.`add_time` BETWEEN '$sdate' AND '$edate' $sqlwhere";

$result = $db->query($sql);
$result_total = $db->query($sql);
$_SESSION['material_inout_list_in'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cut_payment`.`add_time` DESC" . $pages->limitsql;
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
<title>采购管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>应付账款管理</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
      <!--   <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" size="15" /></td>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" size="15" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" size="15" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" size="15" /></td>
        -->
        <th>扣款时间：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>

      <th>扣款类型：</th>
      <td>
      	<select name="cut_payment_type" class="input_txt txt">
      		<option value="M">模具物料</option>
      		<option value="C">加工刀具</option>
      		<option value="O">期间物料</option>
      	</select>
      </td>
      <th>供应商：</th>
        <td><select name="supplierid" class="input_txt txt">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
        while($row_supplier = $result_supplier->fetch_assoc()){
          echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
        }
      }
      ?>
          </select></td>
        <td>
          <input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="add" value="添加扣款" class="button" onclick="window.location.href='create_cut_payment.php?action=add'" />
        </td> 
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
    while($row_total = $result_total->fetch_assoc()){
      $total_amount += $row_total['amount'];
      $total_process_cost += $row_total['process_cost'];  
    }                                                                       
  ?>
  <table>
    <tr>
      <th width="">ID</th>
      <th width="">合同号</th>
      <th width="">名称</th>
      <th width="15%">图片</th>
      <th width="">规格</th>
      <th width="">扣款金额</th>
      <th width="">扣款原因</th>
      <th width="">供应商</th>
      <th width="">扣款人</th>
      <th width="">时间</th>
      <th width="">扣款单</th>
    </tr>
    <?php
  while($row = $result->fetch_assoc()){
    $cutid = $row['cutid'];
    $image = '<img width="150" height="80" src='.$row['image'].'>';
  ?>
  <form action="material_balance_account_do.php" method="post">
    <tr>
      <td>
        <input type="checkbox" name="id[]" value="<?php echo $cutid?>">
      </td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $image; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $row['cut_payment'];?></td>
      <td><?php echo $row['cut_cause'];?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['employee_name'] ?></td>
      <td><?php echo $row['add_time'] ?></td>
      <td><a href="cut_payment_print.php?cutid=<?php echo $cutid; ?>">打印</a></td>
    </tr>
    <?php } ?>
  </table>
  <!-- <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="审核" class="select_button" />
    </div> -->
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