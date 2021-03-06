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
    $sql_supplierid = " AND `db_outward_order`.`supplierid` = '$supplierid'";
  }
  $order_status = $_GET['order_status'];
  if($order_status != NULL){
    $sql_order_status = " AND `db_outward_order`.`order_status` = '$order_status'";
  }
  $sqlwhere = " AND `db_outward_order`.`order_number` LIKE '%$order_number%' $sql_supplierid $sql_order_status";
}
$sql = "SELECT `db_outward_order`.`employeeid`,`db_outward_order`.`orderid`,`db_outward_order`.`order_number`,`db_outward_order`.`order_date`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,`db_outward_order`.`dotime`,`db_outward_order`.`order_status` FROM `db_outward_order` INNER JOIN `db_employee` ON `db_outward_order`.`employeeid` = `db_employee`.`employeeid` LEFT JOIN `db_outward_inquiry_order` ON `db_outward_order`.`inquiry_orderid` = `db_outward_inquiry_order`.`inquiry_orderid` LEFT JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_outward_inquiry_order`.`supplierid`";
$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_outward_order`.`order_number` DESC" . $pages->limitsql;
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
<script type="text/javascript">
  $('a').live('click',function(){
    var id = $(this).attr('id');
    var orderid = id.substr(id.lastIndexOf('_')+1);
    $.post('../ajax_function/set_outward_order_status.php',{orderid:orderid},function(data){
      location.reload();
    })
  })
</script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>外协加工订单</h4>
  <form action="" name="material_order" method="get">
    <table>
      <tr>
        <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" /></td>
        <th>订单日期：</th>
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
        <th>订单状态：</th>
        <td><select name="order_status">
            <option value="">所有</option>
            <?php
            foreach($array_order_status as $order_status_key=>$order_status_value){
        echo "<option value=\"".$order_status_key."\">".$order_status_value."</option>";
      }
      ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='mould_outward_orderdo.php?action=add'" /></td>
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
    $sql_order_list = "SELECT `orderid`,COUNT(*) AS `count` FROM `db_outward_order_list` WHERE `orderid` IN ($array_orderid) GROUP BY `orderid`";
    $result_order_list = $db->query($sql_order_list);
    if($result_order_list->num_rows){
      while($row_order_list = $result_order_list->fetch_assoc()){
        $array_order_list[$row_order_list['orderid']] = $row_order_list['count'];
      }
    }else{
      $array_order_list = array();
    }
    //订单是否有出入库记录
    $sql_material_inout = "SELECT `db_outward_order_list`.`orderid` FROM `db_material_inout` INNER JOIN `db_outward_order_list` ON `db_outward_order_list`.`listid` = `db_material_inout`.`listid` GROUP BY `db_outward_order_list`.`orderid`";
    $result_material_inout = $db->query($sql_material_inout);
    if($result_material_inout->num_rows){
      while($row_material_inout = $result_material_inout->fetch_assoc()){
        $array_material_inout[] = $row_material_inout['orderid'];
      }
    }else{
      $array_material_inout = array();
    }
  ?>
  <form action="mould_outward_orderdo.php" name="material_order" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="12%">合同号</th>
        <th width="8%">订单日期</th>
        <th width="8%">供应商</th>
        <th width="8%">操作人</th>
        <th width="12%">操作时间</th>
        <th width="6%">项数</th>
        <th width="6%">订单状态</th>
        <th width="4%">Add</th>
        <th width="4%">Excel</th>
        <th width="4%">List</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $orderid = $row['orderid'];
      $list_count = array_key_exists($orderid,$array_order_list)?$array_order_list[$orderid]:0;
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $orderid; ?>"<?php if($row['order_status'] == 2 || $employeeid != $row['employeeid']) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['order_date']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
        <td><?php echo $list_count; ?></td>
        <td>
          <?php 
            if($row['order_status'] == 0){
                echo '<a href="#" id="status_'.$orderid.'" onclick="return false">未提交</a>';
            }elseif($row['order_status'] == 1){
                echo '<a href="#" id="status_'.$orderid.'" onclick="return false">已提交</a>';
            }elseif($row['order_status'] == 2){
                echo '完成';
            }
          ?></td>
        <td><?php if($employeeid == $row['employeeid'] && $row['order_status'] == '0' && $list_count == 0){ ?>
          <a href="outward_order_list_add.php?id=<?php echo $orderid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
       <!--  <td><?php if($list_count >0 && $employeeid == $row['employeeid']){ ?>
          <a href="mould_outward_orderae.php?id=<?php echo $orderid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td> -->
        <td><?php if($list_count){ ?>
          <a href="excel_outward_order.php?id=<?php echo $orderid; ?>"><img src="../images/system_ico/excel_10_10.png" width="10" height="10" />
          <?php } ?>
          </a></td>
        <td><?php if($employeeid == $row['employeeid'] && $list_count){ ?>
          <a href="outward_order_list.php?id=<?php echo $orderid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a>
          <?php } ?></td>
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