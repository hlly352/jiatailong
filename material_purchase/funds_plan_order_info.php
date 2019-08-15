<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$accountid = $_GET['accountid'];
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
  //计划金额默认为对账金额
  var num = $('input[name ^= plan_amount]').size();
  for(var i = 0;i<num;i++){
    var id = $('input[name ^= plan_amount]').eq(i).attr('id');
    var listid = id.substr(id.lastIndexOf('_') + 1);
   $("#plan_amount_"+listid).val($.trim($('#order_amount_'+listid).html()));
  }
    //计划金额不能超过对账金额
    $('input[name ^= plan_amount]').live('focus',function(){
      $(this).css('background','white');
    })
    $("input[name^=plan_amount]").live('blur',function(){
    var plan_amount = parseFloat($(this).val());
    var id = $(this).attr('id');
    var listid = id.substr(id.lastIndexOf('_') + 1);
    var order_amount = $.trim($('#order_amount_'+listid).html());
    if($.trim(plan_amount)){
      if(!rf_b.test(plan_amount) || (plan_amount > order_amount)){
        alert('请输入正确的金额');
        $(this).css('background','#eee');
        
      }
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
  //单个添加
  $('.but').live('click',function(){
    var id = $(this).attr('id');
    var listid = id.substr(id.lastIndexOf('_') + 1);
    var plan_amount = $('#plan_amount_'+listid).val();
    var order_amount = parseFloat($.trim($('#order_amount_'+listid).html()));
    if($.trim(plan_amount)){
      if(!rf_b.test(plan_amount) || (plan_amount > order_amount)){
        alert('请输入正确的金额');
        $(this).css('background','#eee');
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
        <h4>对账单明细</h4>     
    </div>
  <?php

      //通过对账单号在对账详情表中查找订单信息
       $order_sql = "SELECT `db_account_order_list`.`listid`,`db_account_order_list`.`order_amount`,`db_material_order`.`order_number`,`db_account_order_list`.`process_cost`,`db_account_order_list`.`cancel_amount`,`db_account_order_list`.`cut_payment` FROM `db_account_order_list` INNER JOIN `db_material_order` ON `db_account_order_list`.`orderid` = `db_material_order`.`orderid` WHERE `accountid` = '$accountid' AND `plan_status` = 'A'";
        $result = $db->query($order_sql);
        if($result->num_rows){ ?>
      <div id="table_list">
      <form action="funds_plando.php" method="post">
          <table>
            <tr>
              <th>ID</th>
              <th>合同号</th>
              <th>物料金额</th>
              <th>加工费</th>
              <th>核销金额</th>
              <th>品质扣款</th>
              <th>对账金额</th>
              <th>计划金额</th>
              <th>操作</th>
            </tr>
           
           <?php 
             while($row_list = $result->fetch_assoc()){    
            ?>
          <tr>
            <td><input type="checkbox" value="<?php echo $row_list['listid'] ?>" name="listid[]" ?></td>
            <td><?php echo $row_list['order_number'] ?></td>
            <td><?php echo $row_list['order_amount'] ?></td>
            <td><?php echo $row_list['process_cost'] ?></td>
            <td><?php echo $row_list['cancel_amount'] ?></td>
            <td><?php echo $row_list['cut_payment'] ?></td>
            <td id="order_amount_<?php echo $row_list['listid'] ?>">
            <?php
               echo number_format(($row_list['order_amount'] + $row_list['process_cost'] - $row_list['cancel_amount'] - $row_list['cut_payment']),2,'.','');
                  ?>
          </td>
          <td>
            <input type="text" name="plan_amount[]" id="plan_amount_<?php echo $row_list['listid'] ?>">
          </td>
          <td>
            <a id="but_<?php echo $row_list['listid'] ?>" class="but" href="funds_plando.php?action=add&plan_amount=listid=<?php echo $row_list['listid'] ?>">添加</a>
          </td>    
         </tr>
      <?php  
            }
          
     ?>
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
    <?php
    }else{
    echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
    echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'material_funds_plan.php\'" /></p>';
  }
  ?>
 


</table>
</div>
 
<?php include "../footer.php"; ?>
</body>
</html>