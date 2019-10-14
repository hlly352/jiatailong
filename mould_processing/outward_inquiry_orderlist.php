<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$inquiry_orderid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
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
  // $(function(){
  //   $('.status').live('click',function(){
  //     var id = $(this).attr('id');
  //     var listid = id.substr(id.lastIndexOf('_')+1);
  //     var val = $(this).html();
  //     var inp = '<input type="text" id="dates" size="10" value="<?php echo date('Y-m-d') ?>" name="back_date" onfocus="WdatePicker({dateFmt:\'yyyy-MM-dd\',isShowClear:false,readOnly:true})" />'
  //     if(val == '未回'){
  //       $("#back_date_"+listid).empty().append(inp);
  //     }else if(val == '已回'){
  //       $.post('../ajax_function/set_outward_back_date.php',{listid:listid},function(data){

  //       },'json')
  //     }
  //   })
  //   $('#dates').live('blur',function(){
  //     var back_date = $(this).val();
  //     var id = $(this).parent().attr('id');
  //     var array_id = id.split('_');
  //     var listid = array_id[2];
  //     $.post('../ajax_function/set_outward_back_date.php',{listid:listid,back_date:back_date},function(data){
  //       console.log(data);
  //     },'json')
     
  //   })
   
  // })
</script>
<title>采购管理-苏州嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  $sql_order = "SELECT `db_outward_inquiry_order`.`inquiry_number`,`db_outward_inquiry_order`.`inquiry_date`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_outward_inquiry_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_outward_inquiry_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_outward_inquiry_order`.`employeeid` WHERE `db_outward_inquiry_order`.`inquiry_orderid` = '$inquiry_orderid'";
  $result_order = $db->query($sql_order);
  if($result_order->num_rows){
    $array_order = $result_order->fetch_assoc();
  ?>
  <h4>外协加工订单</h4>
  <table>
    <tr>
      <th width="10%">询价单号：</th>
      <td width="15%"><?php echo $array_order['inquiry_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo $array_order['inquiry_date']; ?></td>
      <th width="10%">供应商：</th>
      <td width="15%"><?php echo $array_order['supplier_cname']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_order['employee_name']; ?></td>
    </tr>
  </table>
  <?php
  }else{
    die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
  }
  ?>
</div>
<?php
if($_GET['submit']){
  $mould_number = trim($_GET['mould_number']);
  $material_name = trim($_GET['material_name']);
  $specification = trim($_GET['specification']);
  $sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
}
$sql = "SELECT `db_outward_inquiry_orderlist`.`back_date`,`db_employee`.`employee_name`,`db_mould_outward_type`.`outward_typename`,`db_outward_inquiry_orderlist`.`plan_date`,`db_outward_inquiry_orderlist`.`listid`,`db_outward_inquiry`.`outward_quantity`,`db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_outward_inquiry`.`outward_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_outward_inquiry`.`outward_remark`,`db_mould_material`.`complete_status`,`db_mould_specification`.`mould_no`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code` FROM `db_outward_inquiry_orderlist` INNER JOIN `db_outward_inquiry` ON `db_outward_inquiry_orderlist`.`inquiryid` = `db_outward_inquiry`.`inquiryid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_outward_inquiry`.`materialid` INNER JOIN `db_employee` ON `db_outward_inquiry`.`employeeid` = `db_employee`.`employeeid` INNER JOIN `db_mould_specification` ON `db_mould_specification`.`mould_specification_id` = `db_mould_material`.`mouldid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_outward_inquiry`.`outward_typeid` WHERE `db_outward_inquiry_orderlist`.`inquiry_orderid` = '$inquiry_orderid' $sqlwhere ORDER BY `db_mould_specification`.`mould_no` DESC,`db_mould_material`.`materialid` ASC";
$result = $db->query($sql);
$result_id = $db->query($sql);
?>
<div id="table_search">
  <h4>订单明细</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <td><input type="hidden" name="id" value="<?php echo $orderid; ?>" />
          <input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_outward_inquiry_order.php?id=<?php echo $inquiry_orderid; ?>'" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='outward_inquiry_order_list_add.php?id=<?php echo $inquiry_orderid; ?>'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
 ?>
  <form action="outward_inquiry_orderdo.php" name="material_order_list" method="post">
    <table>
      <tr>
       <th width="">ID</th>
        <th width="">模具编号</th>
<!--         <th width="">料单编号</th>
        <th width="">料单序号</th> -->
        <th width="">物料编码</th>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th width="">数量</th>
        <th width="">材质</th>
        <th width="">硬度</th>
        <th width="">品牌</th>
        <th width="">申请人</th>
        <th width="">加工类型</th>
        <th width="">计划回厂时间</th>
        <th width="">实际回厂时间</th>
        <th width="15%">备注</th>
        <th width="">状态</th>
      </tr>
      <?php
    $amount = 0;
    $process_cost = 0;
    $total_amount = 0;
      while($row = $result->fetch_assoc()){
        $listid = $row['listid'];
    ?>
      <tr>
        <td>
          <input type="checkbox" name="id[]" value="<?php echo $listid; ?>"<?php //if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> />
        </td>
        <td><?php echo $row['mould_no']; ?></td>
        <td><?php echo $row['material_number']; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $row['outward_quantity']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['hardness'] ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['employee_name'] ?></td>
        <td><?php echo $row['outward_typename'] ?></td>
        <td><?php echo $row['plan_date'] ?></td>
        <td id="back_date_<?php echo $listid; ?>"><?php echo $row['back_date'] ?></td>
        <td><?php echo $row['outward_remark'] ?></td>
        <td>
          <?php
            if($row['back_date']){
              echo '<a href="outward_inquiry_orderdo.php?listid='.$listid.'&status=1&action=back_date" id="status_'.$listid.'" class="status">已回</a>';
            }else{
              echo '<a href="outward_inquiry_orderae.php?listid='.$listid.'&status=0&action=show" id="status_'.$listid.'" class="status">未回</a>';
            }
          ?>
        </td>
      </tr>
      <?php
    }
    ?>
      <tr>
        <td colspan="15">
          <input type="button" onclick="window.location.href='outward_inquiry_order.php'" value="返回" class="button" />
        </td>
      </tr>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" value="del_list" name="action" />
    </div>
  </form>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无物料记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>