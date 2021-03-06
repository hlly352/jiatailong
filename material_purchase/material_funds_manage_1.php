<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$planid = $_GET['id'];
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
<script language="javascript" type="text/javascript">
$(function(){
  //查找所有的小计单元格
  var num = $('.count').size();
  var arr = new Array();
  for(var i=0;i<num;i++){
    var name = $('.count').eq(i).attr('class');
    arr[i] = name;
  
  }
   
        var obj = {}; 
        for(var i= 0, l = arr.length; i< l; i++){ 
            var item = arr[i]; 
            obj[item] = (obj[item] +1 ) || 1;

        }
        console.log(obj);
    for(var j=0;j<obj.length;j++){
      console.log(obj[j]);
    }
    
  $('#plan_date').val();
  //失去焦点
  $("input[name^=plan_amount]").blur(function(){
    
    var plan_amount = $(this).val();
    if(!rf_a.test(plan_amount) && $.trim(plan_amount)){
      alert('请输入数字');
      $(this).val(this.defaultValue);
    }else{
      if($.trim(plan_amount)){
        $(this).val(parseFloat($(this).val()).toFixed(2));
        var id = $(this).attr('id');
        var accountid = id.substr(id.indexOf('-')+1);
        // var array_accountid = $(this).attr('id').split('-');
        // var accountid = array_accountid[1];
        //获取对应的对账金额
        // var amount = $("#amount-"+accountid).html();
        // if(amount < $.trim(plan_amount)){
        //  alert('计划金额不能大于对账金额');
        //  return false;
        // }
        
      }
    }
  })
  $("input[name^=plan_amount]").one('blur',function(){
    var plan_amount = $(this).val();
    if(!$.trim(plan_amount)){
      var id = $(this).attr('id');
      var accountid = id.substr(id.indexOf('-')+1);
      $(this).val($.trim($('#amount-'+accountid).html()));
    }
  })
    //点击添加按钮
  $('input[name=submit]').live('click',function(){
    var num = $('input[name ^= plan_amount]').size();
    for(var i=0;i<num;i++){
      var plan_amount = $.trim($('input[name ^= plan_amount]').eq(i).val());
      var amount = parseFloat($('.amount').eq(i).html());
      if(plan_amount>amount){
        alert('计划金额不能大于对账金额');
        return false;
      }
    }
  })
})
</script>
<title>采购管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>应付账款</h4>
  <form action=""  method="get">
   <?php
  $sql_plan = "SELECT * FROM `db_material_funds_plan` INNER JOIN `db_employee` ON `db_material_funds_plan`.`employeeid` = `db_employee`.`employeeid` WHERE `planid`= $planid";
  $result_plan = $db->query($sql_plan);
  if($result_plan->num_rows){
    $array_plan = $result_plan->fetch_assoc();
    $plan_date = $array_plan['plan_date'];
  ?>
    <table>
       <tr>
      <th width="10%">付款单号：</th>
      <td width="15%"><?php echo $array_plan['plan_number']; ?></td>
      <th width="10%">计划日期：</th>
      <td width="15%"><?php echo $array_plan['plan_date']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_plan['employee_name']; ?></td>
    </tr>
    </table>
   <?php }else{
    die("<p class=\"tag\">系统提示：暂无付款计划！</p></div>"); ?>
    <?php } ?>
  </form>
</div>


<div id="table_list">
<?php
  //查找当前计划单下面的所有计划内容
  $plan_list_sql = "SELECT `db_material_order`.`supplierid`,`db_funds_plan_list`.`listid`,`db_funds_plan_list`.`order_amount`,`db_funds_plan_list`.`process_cost`,`db_funds_plan_list`.`accountid`,`db_funds_plan_list`.`cancel_amount`,`db_funds_plan_list`.`cut_payment`,`db_funds_plan_list`.`plan_amount`,`db_supplier`.`supplier_cname`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_material_order`.`supplierid` FROM `db_funds_plan_list` INNER JOIN `db_material_order` ON `db_funds_plan_list`.`orderid` = `db_material_order`.`orderid` INNER JOIN `db_supplier` ON `db_material_order`.`supplierid` = `db_supplier`.`supplierid` WHERE `planid` = '$planid'  ORDER BY `db_material_order`.`supplierid`";
  $result_list = $db->query($plan_list_sql);
  if($result_list->num_rows){
    ?>
    <table>
        <tr>
          <th>ID</th>
          <th>对账时间</th>
          <th>合同号</th>
          <th>供应商</th>
          <th>物料金额</th>
          <th>加工费</th>
          <th>核销金额</th>
          <th>品质扣款</th>
          <th>对账金额</th>
          <th>计划金额</th>
          <th>小计</th>
          <th>操作</th>
        </tr>
  <?php
    while($row_list = $result_list->fetch_assoc()){
      $total_order_amount += $row_list['order_amount'];
      $total_process_cost += $row_list['process_cost'];
      $total_cancel_amount += $row_list['cancel_amount'];
      $total_cut_payment += $row_list['cut_payment'];
      $total_account_amount += $row_list['order_amount'] + $row_list['process_cost'] - $row_list['cancel_amount'] - $row_list['cut_payment'];
      $total_plan_amount += $row_list['plan_amount'];
      $total_sql = "SELECT SUM(`db_funds_plan_list`.`plan_amount`) AS `total`,COUNT(`db_funds_plan_list`.`listid`) AS `count` FROM `db_funds_plan_list` INNER JOIN `db_material_order` ON `db_funds_plan_list`.`orderid` = `db_material_order`.`orderid` WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_material_order`.`supplierid` = ".$row_list['supplierid']." GROUP BY `db_material_order`.`supplierid`"; 
      $result_total = $db->query($total_sql);
      if($result_total->num_rows){
        $total = array();
        while($row_total = $result_total->fetch_assoc()){
          $total[$row_list['supplierid']] = $row_total;
        }
        
      }
    
      // //判断是对账款还是预付款
      // if($row_list['accountid']){
      //  $info_sql = "SELECT * FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_material_account`.`accountid` =".$row_list['accountid'];
      // echo $info_sql.'<br>';
      // $result_info = $db->query($info_sql);
      // if($result_info->num_rows){
      //  $row_info = $result_info->fetch_assoc();
      // } else{
      //  //$info_sql = "SELECT `db_material_order`.`orderid`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_material_order`.`employeeid`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,`db_material_order`.`order_amount` AS `sum`,`db_material_order`.`prepayment` FROM `db_material_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_order`.`employeeid` INNER JOIN `db_material_order_list` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` WHERE `db_material_order`.`orderid` =".$row_list['orderid'];
        
      //}
          
    ?>
    <tr>
          <td>
            <input type="checkbox" value="<?php echo $row_list['listid'] ?>">
          </td>
          <td><?php echo $row_list['order_date'] ?></td>
          <td><?php echo $row_list['order_number'] ?></td>
          <td><?php echo $row_list['supplier_cname']?></td>
          <td><?php echo $row_list['order_amount']?></td>
          <td><?php echo $row_list['process_cost'] ?></td>
          <td><?php echo $row_list['cancel_amount'] ?></td>
          <td><?php echo $row_list['cut_payment'] ?></td>
          <td>
            <?php 
              $account_amount = $row_list['order_amount'] + $row_list['process_cost'] - $row_list['cancel_amount'] - $row_list['cut_payment'];
              echo number_format($account_amount,2,'.','');
            ?>    
          </td>
          <td><?php echo $row_list['plan_amount'] ?></td>
          <?php
          
           ?>

          <td class="count count_<?php echo $row_list['supplierid'] ?>"><?php echo $total[$row_list['supplierid']]['total'] ?></td>
          <?php 
          ?>
          <td>
            <a href="funds_plando.php?action=del&id=<?php echo $row_list['listid'] ?>">删除</a>
          </td>
      </tr> 
      
  
<?php
    }
  
?>  
  <tr>
    <td colspan="4">总计</td>
    <td><?php echo number_format($total_order_amount,2,'.','') ?></td>
    <td><?php echo number_format($total_process_cost,2,'.','') ?></td>
    <td><?php echo number_format($total_cancel_amount,2,'.','') ?></td>
    <td><?php echo number_format($total_cut_payment,2,'.','') ?></td>
    <td><?php echo number_format($total_account_amount,2,'.','') ?></td>
    <td><?php echo number_format($total_plan_amount,2,'.','') ?></td>
    <td><?php echo number_format($total_plan_amount,2,'.','') ?></td>
    <td></td>
  </tr>
  <tr>
    <td colspan="12">
      <input type="button" class="button" value="确认" onclick="window.location.href='material_funds_plan.php'" />
    </td>
  </tr>
  </table>

<?php
 }else{
    echo "<p class=\"tag\">系统提示：暂无付款计划！</p></div>";
  }

?>
</div>
 <div id="table_search">
  <h4>应付账款</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
       <!--  <th>供应商名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>对账日期：</th>
        <td><input type="text" name="specification" class="input_txt" /></td> -->
        <th>供应商：</th>
        <td><select name="data_source" id="data_source" class="input_txt txt">
            <!-- <option value="A"<?php if($data_source == 'A') echo " selected=\"selected\""; ?>>我的应付账款</option> -->
            <option value="B"<?php if($data_source == 'B') echo " selected=\"selected\""; ?>>应付账款</option>
            <option value="C"<?php if($data_source == 'C') echo " selected=\"selected\""; ?>>预付帐款</option>
          </select></td>
        <td><input type="submit" name="submit" id="submit" value="查询" class="button" />
          <input type="hidden" name="id" value="<?php echo $planid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
//$data_source = $_GET['data_source']?trim($_GET['data_source']):'B';


  $order_list_sql = "SELECT `db_material_account`.`account_type`,`db_material_account`.`supplierid`,`db_material_account`.`orderidlist`,`db_material_account`.`accountid`,`db_material_account`.`account_number`,`db_material_account`.`orderidlist`,`db_material_account`.`account_time`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,(`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cut_payment` - `db_material_account`.`tot_cancel_amount`) AS `total_amount`  FROM `db_material_account`INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` INNER JOIN `db_material_invoice_list` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_account`.`employeeid` WHERE `db_material_account`.`status` = 'P' AND `db_material_account`.`tot_plan_amount` <= (`db_material_account`.`tot_amount` + `db_material_account`.`tot_process_cost` - `db_material_account`.`tot_cancel_amount` - `db_material_account`.`tot_cut_payment`) GROUP BY `db_material_account`.`accountid`";

  $result = $db->query($order_list_sql);
  $pages = new page($result->num_rows,5);
  $sqllist = $order_list_sql . " ORDER BY `db_material_account`.`accountid` DESC" . $pages->limitsql;
  $result_order_list = $db->query($sqllist);
  if($result_order_list->num_rows){ ?>
    <div id="table_list">
      <form action="funds_plando.php" method="post">
        <table>
          <tr>
            <th>ID</th>
            <th>对账单号</th>
            <th>对账时间</th>
            <th>发票号</th>
            <th>发票时间</th>
            <th>总金额</th>
            <th>供应商</th>
            <th>合同号</th>
            <th>物料金额</th>
            <th>加工费</th>
            <th>核销金额</th>
            <th>品质扣款</th>
            <th>对账金额</th>
            <th>操作</th>
          </tr>
  <?php
    while($row_order_list = $result_order_list->fetch_assoc()){
      $accountid = $row_order_list['accountid'];
      //查找发票号
      $invoice_sql = "SELECT `invoice_no`,`date` FROM `db_material_invoice_list` WHERE `accountid` = '$accountid'";
      $result_invoice = $db->query($invoice_sql);
      //查找合并单元格的个数
      $order_sql = "SELECT COUNT(`orderid`) AS `count` FROM `db_account_order_list` WHERE `accountid` = '$accountid' AND `plan_status` = 'A'";
      
      $result_order = $db->query($order_sql);
      if($result_order->num_rows){
        $count = $result_order->fetch_assoc()['count'];
      }
      $count = $count + 1;
  ?>
      <tr>
        <td rowspan="<?php echo $count ?>">
          <input type="checkbox" value="<?php echo $row_order_list['accountid'] ?>" name="accountid[]" />
        </td>
        <td rowspan="<?php echo $count ?>">
          <?php echo $row_order_list['account_number'] ?>
            
        </td>
        <td rowspan="<?php echo $count ?>">
          <?php echo $row_order_list['account_time'] ?>
            
        </td>
        <td rowspan="<?php echo $count ?>">
         <?php 
            if($result_invoice->num_rows){
              while($row_invoice = $result_invoice->fetch_assoc()){
                echo $row_invoice['invoice_no'].'<br>';
            }
          }
          ?>
        </td>
        <td rowspan="<?php echo $count ?>">
          <?php
            $result_invoice = $db->query($invoice_sql);
            if($result_invoice->num_rows){
              while($row_invoice = $result_invoice->fetch_assoc()){
                echo $row_invoice['date'].'<br>';
              }
            }
          ?>
        </td>
        <td rowspan="<?php echo $count ?>">
          <?php echo $row_order_list['total_amount'] ?>
        </td>
        <td rowspan="<?php echo $count ?>">
          <?php echo $row_order_list['supplier_cname'] ?>
        </td>
      </tr>
  <?php

      //通过对账单号在对账详情表中查找订单信息
      // $sql = "SELECT `db_material_order`.`orderid`,`db_material_order`.`order_number`,`db_supplier`.`supplier_cname`,SUM(`db_material_inout`.`amount`) AS `sum`,SUM(`db_material_inout`.`cancel_amount`) AS `cancel_amount`,SUM(`db_material_inout`.`cut_payment`) AS `cut_payment`,SUM(`db_material_order_list`.`process_cost`) AS `process_cost` FROM `db_material_order` INNER JOIN `db_material_order_list` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_material_inout` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_supplier` ON `db_material_order`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_account_list` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` WHERE `db_material_account_list`.`accountid` = '$accountid' AND `db_material_order`.`orderid` NOT IN(SELECT `orderid` FROM `db_funds_plan_list` GROUP BY `orderid`) GROUP BY `db_material_order`.`orderid`";
       $order_sql = "SELECT `db_account_order_list`.`listid`,`db_account_order_list`.`order_amount`,`db_material_order`.`order_number`,`db_account_order_list`.`process_cost`,`db_account_order_list`.`cancel_amount`,`db_account_order_list`.`cut_payment` FROM `db_account_order_list` INNER JOIN `db_material_order` ON `db_account_order_list`.`orderid` = `db_material_order`.`orderid` WHERE `accountid` = '$accountid' AND `plan_status` = 'A'";
        $result = $db->query($order_sql);
        if($result->num_rows){
          while($row_list = $result->fetch_assoc()){
      ?>
        <tr>
          <td><?php echo $row_list['order_number'] ?></td>
          <td><?php echo $row_list['order_amount'] ?></td>
          <td><?php echo $row_list['process_cost'] ?></td>
          <td><?php echo $row_list['cancel_amount'] ?></td>
          <td><?php echo $row_list['cut_payment'] ?></td>
          <td>
          <?php
             echo number_format(($row_list['order_amount'] + $row_list['process_cost'] - $row_list['cancel_amount'] - $row_list['cut_payment']),2,'.','');
                ?>
        </td>
        <td>
          <a href="funds_plando.php?listid=<?php echo $row_list['listid'] ?>">添加</a>
        </td>    
       </tr>
    <?php  
          }
        }
   }?>
        <tr>
        <td colspan="15">
          <input type="submit" name="submit" value="添加" class="button" />
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="data_source" value="B">
          <input type="hidden" value="<?php echo $array_plan['planid'] ?>" name="planid"/>
          <input type="button" name="button" value="返回" class="button" onclick="window.location.href = 'material_funds_plan.php'" />

        </td>
      </tr>
    </table>
    </form>
    </div>
   <div id="page">
    <?php $pages->getPage();?>
   </div>
    <?php
    }else{
    echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
    echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'material_funds_plan.php\'" /></p>';
  }
  ?>
 


</table>
</div>
<?php
    $sql = "SELECT `db_material_order`.`orderid`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_material_order`.`employeeid`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,`db_material_order`.`order_amount` AS `sum`,`db_material_order`.`prepayment`,`db_material_order`.`supplierid` FROM `db_material_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_order`.`employeeid` INNER JOIN `db_material_order_list` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` WHERE `db_material_order`.`pay_type` = 'P' AND `db_material_order`.`order_status` = '1' AND `db_material_order`.`order_amount` > `db_material_order`.`prepayment` GROUP BY `db_material_order`.`orderid`";

$result = $db->query($sql);
$pages = new page($result->num_rows,10);
$sqllist = $sql."ORDER BY `db_material_order`.`dotime` DESC".$pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>预付账款</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
       <!--  <th>供应商名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>对账日期：</th>
        <td><input type="text" name="specification" class="input_txt" /></td> -->
        <th>供应商：</th>
        <td><select name="data_source" id="data_source" class="input_txt txt">
            <!-- <option value="A"<?php if($data_source == 'A') echo " selected=\"selected\""; ?>>我的应付账款</option> -->
            <option value="B"<?php if($data_source == 'B') echo " selected=\"selected\""; ?>>应付账款</option>
            <option value="C"<?php if($data_source == 'C') echo " selected=\"selected\""; ?>>预付帐款</option>
          </select></td>
        <td><input type="submit" name="submit" id="submit" value="查询" class="button" />
          <input type="hidden" name="id" value="<?php echo $planid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="funds_plando.php" name="material_list" id="account" method="post">
    <table>
     <tr>
      <th width="">ID</th>
      <th width="">添加时间</th>
      <th width="">合同号</th>
      <th width="">供应商</th>
      <th width="">订单金额</th>
      <th width="">预付金额</th>
      <th width="">剩余金额</th>
      <th width="">操作人</th>
    </tr>
    <?php
  while($row = $result->fetch_assoc()){
  ?>
  <form action="funds_plando.php" id="account" method="post">
    <tr>
      <td>
        <input type="checkbox" name="id[]" value="<?php echo $row['orderid']?>">
      </td>
      <td><?php echo $row['order_date']; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo number_format($row['sum'],2,'.',''); ?></td>
      <td>
        <input type="text" value="<?php echo number_format(($row['sum'] - $row['prepayment']),2,'.','') ?>" name="plan_amount_<?php echo $row['orderid'] ?>" class="input_txt" />
      </td>
      <td><?php echo number_format(($row['sum'] - $row['prepayment']),2,'.','') ?></td>
      <input type="hidden" value="<?php echo $row['sum'] ?>" name="order_amount_<?php echo $row['orderid'] ?>" />
      <input type="hidden" value="<?php echo $row['supplierid'] ?>" name="supplierid_<?php echo $row['orderid'] ?>" />
      <input type="hidden" value="M" name="account_type_<?php echo $row['orderid'] ?>" />
      <td><?php echo $row['employee_name']; ?></td>
    </tr>

      <?php }  ?>
      <tr>
        <td colspan="15"><input type="submit" name="submit" value="添加" class="button" />
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="data_source" value="C">
          <input type="hidden" value="<?php echo $array_plan['planid'] ?>" name="planid"/>
          <input type="button" name="button" value="返回" class="button" onclick="window.location.href = 'material_funds_plan.php'" />

        </td>
      </tr>
    </table>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
    echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'material_funds_plan.php\'" /></p>';
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>