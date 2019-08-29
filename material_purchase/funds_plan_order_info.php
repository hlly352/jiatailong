<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$accountid = $_GET['accountid'];
$action = $_GET['action'];
$planid = $_GET['planid'];
$account_type = $_GET['account_type'];
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
      if(plan_amount > amount){
        alert('计划金额不能大于对账金额');
        return false;
      }
    }
  })
  //单个添加
  $('.but').live('click',function(){
    var id = $(this).attr('id');
    var listid = id.substr(id.lastIndexOf('_') + 1);
    var order_listid = $('#order_listid_'+listid).val();
    var planid = $('input[name=planid]').val();
    var accountid = $('input[name=accountid]').val();
    var plan_amount = $('#plan_amount_'+listid).val();
    var order_amount = parseFloat($.trim($('#order_amount_'+listid).html()));
    if($.trim(plan_amount)){
      if(!rf_b.test(plan_amount) || (plan_amount > order_amount)){
        alert('请输入正确的金额');
        $(this).css('background','#eee');
        return false;
      }else{
        window.location.href = 'funds_plando.php?accountid='+accountid+'&plan_amount='+plan_amount+'&order_listid='+order_listid+'&planid='+planid+'&action=add';
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
        <div id="table_list">
<?php 
  if($action == 'add'){
?>
    <?php
        //通过对账单号在对账详情表中查找订单信息
         $order_sql = "SELECT `db_account_order_list`.`listid`,`db_account_order_list`.`order_number`,`db_account_order_list`.`order_amount`,`db_account_order_list`.`process_cost`,`db_account_order_list`.`cancel_amount`,`db_account_order_list`.`cut_payment`,`db_account_order_list`.`plan_amount` FROM `db_account_order_list` WHERE `accountid` = '$accountid' AND `plan_status` = 'A' AND `db_account_order_list`.`plan_amount` < (`db_account_order_list`.`order_amount` + `db_account_order_list`.`process_cost` - `db_account_order_list`.`cancel_amount` - `db_account_order_list`.`cut_payment`)";
          $result = $db->query($order_sql);
          if($result->num_rows){ ?>
        <form action="funds_plando.php" method="post">
            <table>
              <tr>
                <th>ID</th>
                <th width="13%">合同号</th>
                <th width="13%">物料金额</th>
                <th width="13%">加工费</th>
                <th width="13%">核销金额</th>
                <th width="13%">品质扣款</th>
                <th width="13%">对账金额</th>
                <th width="13%">计划金额</th>
                <th>操作</th>
              </tr>
             
             <?php 
               while($row_list = $result->fetch_assoc()){    
              ?>
            <tr>
              <td><input type="checkbox" value="<?php echo $row_list['listid'] ?>" id="order_listid_<?php echo $row_list['listid'] ?>" name="id[]" ?></td>
              <td><?php echo $row_list['order_number'] ?></td>
              <td><?php echo $row_list['order_amount'] ?></td>
              <td><?php echo $row_list['process_cost'] ?></td>
              <td><?php echo $row_list['cancel_amount'] ?></td>
              <td><?php echo $row_list['cut_payment'] ?></td>
              <td id="order_amount_<?php echo $row_list['listid'] ?>" class="amount">
              <?php
                 echo number_format(($row_list['order_amount'] + $row_list['process_cost'] - $row_list['cancel_amount'] - $row_list['cut_payment'] - $row_list['plan_amount']),2,'.','');
                    ?>
            </td>
            <td>
              <input type="text" name="plan_amount_<?php echo $row_list['listid'] ?>" id="plan_amount_<?php echo $row_list['listid'] ?>">
            </td>
            <td>
              <a href="#" id="but_<?php echo $row_list['listid'] ?>" class="but" >添加</a>
            </td>    
           </tr>
        <?php  
              }
            
       ?>
          
        </table>
        <div id="checkall">
          <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
          <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
          <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
          <input type="submit" name="submit" id="submit" value="添加" class="select_button"  disabled="disabled" />
          <input type="hidden" name="data_source" value="B">
          <input type="hidden" value="<?php echo $planid ?>" name="planid"/>
          <input type="hidden" value="<?php echo $accountid ?>" name="accountid" />
          <input type="hidden" name="action" value="add" />
        </div>
      </form>
      <table>
          <tr>
            <td colspan="15">
              <input type="button" name="button" value="返回" class="button" onclick="window.location.href='funds_plan_list_add.php?id=<?php echo $planid ?>'" />
            </td>
          </tr>
      </table>
      <?php
      }else{

        echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
         echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'funds_plan_list_add.php?id='.$planid.'\'" /></p>';
    }
    ?>
    </div>
   
  <?php }elseif($action == 'del'){
        $accountid = $_GET['accountid'];
        $account_type = $_GET['account_type'];
        //通过对账单号在对账详情表中查找订单信息
        $order_sql = "SELECT `db_funds_plan_list`.`listid`,`db_account_order_list`.`order_number`,`db_account_order_list`.`order_amount`,`db_account_order_list`.`process_cost`,`db_account_order_list`.`cancel_amount`,`db_account_order_list`.`cut_payment`,(`db_account_order_list`.`order_amount` + `db_account_order_list`.`process_cost` - `db_account_order_list`.`cancel_amount` - `db_account_order_list`.`cut_payment`) AS `total_amount`,`db_funds_plan_list`.`plan_amount` FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid` WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_account_order_list`.`accountid` = '$accountid' AND `db_funds_plan_list`.`plan_amount` > 0";
        

          $result = $db->query($order_sql);
          if($result->num_rows){ ?>
        <div id="table_list">
        <form action="funds_plando.php" method="post">
            <table>
              <tr>
                <th>ID</th>
                <th width="13%">合同号</th>
                <th width="13%">物料金额</th>
                <th width="13%">加工费</th>
                <th width="13%">核销金额</th>
                <th width="13%">品质扣款</th>
                <th width="13%">应付金额</th>
                <th width="13%">计划金额</th>
                <th>操作</th>
              </tr>
             
             <?php 
               while($row_list = $result->fetch_assoc()){
               
              ?>
            <tr>
              <td><input type="checkbox" value="<?php echo $row_list['listid'] ?>" id="order_listid_<?php echo $row_list['listid'] ?>" name="id[]" ?></td>
              <td><?php echo $row_list['order_number'] ?></td>
              <td><?php echo $row_list['order_amount'] ?></td>
              <td><?php echo $row_list['process_cost'] ?></td>
              <td><?php echo $row_list['cancel_amount'] ?></td>
              <td><?php echo $row_list['cut_payment'] ?></td>
              <td id="order_amount_<?php echo $row_list['listid'] ?>">
                <?php echo $row_list['total_amount'] ?>
            </td>
            <td><?php echo $row_list['plan_amount'] ?></td>
            <td>
              <a href="funds_plando.php?listid=<?php echo $row_list['listid'] ?>&action=del" id="button_<?php echo $row_list['listid'] ?>" >删除</a>
            </td>    
           </tr>
        <?php  
              }
            
       ?>
          
        </table>
      <!--   <div id="checkall">
          <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
          <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
          <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
          <input type="submit" name="submit" id="submit" value="删除" class="select_button"  disabled="disabled" />
          <input type="hidden" name="data_source" value="B">
          <input type="hidden" value="<?php echo $planid ?>" name="planid"/>
          <input type="hidden" value="<?php echo $accountid ?>" name = "accountid" />
          <input type="hidden" name="action" value="del" />
        </div> -->
      </form>
      <table>
          <tr>
            <td colspan="15">
              <input type="button" name="button" value="返回" class="button" onclick="window.history.go(-1)" />
            </td>
          </tr>
      </table>
      </div>
      <?php
      }else{
        echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
        echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'funds_plan_list_add.php?id='.$planid.'\'" /></p>';   
    }
    ?>
   


  </table>
  </div>
  <?php }elseif($action == 'purchase'){ 
        $accountid = $_GET['accountid'];
        //通过对账单号在对账详情表中查找订单信息
         $order_sql = "SELECT `db_funds_plan_list`.`listid`,`db_account_order_list`.`order_number`,`db_account_order_list`.`order_amount`,`db_account_order_list`.`process_cost`,`db_account_order_list`.`cancel_amount`,`db_account_order_list`.`cut_payment`,(`db_account_order_list`.`order_amount` + `db_account_order_list`.`process_cost` - `db_account_order_list`.`cancel_amount` - `db_account_order_list`.`cut_payment`) AS `total_amount`,`db_funds_plan_list`.`plan_amount` FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid` WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_account_order_list`.`accountid` = '$accountid'";

          $result = $db->query($order_sql);
          if($result->num_rows){ ?>
        <div id="table_list">
        <form action="funds_plando.php" method="post">
            <table>
              <tr>
                <th>ID</th>
                <th width="13%">合同号</th>
                <th width="13%">物料金额</th>
                <th width="13%">加工费</th>
                <th width="13%">核销金额</th>
                <th width="13%">品质扣款</th>
                <th width="13%">应付金额</th>
                <th width="13%">计划金额</th>
              </tr>
             
             <?php 
               while($row_list = $result->fetch_assoc()){
              ?>
            <tr>
              <td><input type="checkbox" value="<?php echo $row_list['listid'] ?>" id="order_listid_<?php echo $row_list['listid'] ?>" name="id[]" ?></td>
              <td><?php echo $row_list['order_number'] ?></td>
              <td><?php echo $row_list['order_amount'] ?></td>
              <td><?php echo $row_list['process_cost'] ?></td>
              <td><?php echo $row_list['cancel_amount'] ?></td>
              <td><?php echo $row_list['cut_payment'] ?></td>
              <td id="order_amount_<?php echo $row_list['listid'] ?>">
                <?php echo $row_list['total_amount'] ?>
            </td>
            <td><?php echo $row_list['plan_amount'] ?></td>   
           </tr>
        <?php  
              }
            
       ?>
          
        </table>
       
      </form>
      <table>
          <tr>
            <td colspan="15">
              <input type="button" name="" value="审核" class="button" onclick="window.location.href='funds_apply_do.php?from=purchase&action=complete&planid=<?php echo $planid ?>&accountid=<?php echo $accountid ?>'" />
             <!--  <input type="button" name="" value="撤回" class="button"  onclick="window.location.href='funds_apply_do.php?from=purchase&action=back&planid=<?php echo $planid ?>&accountid=<?php echo $accountid ?>'" /> -->
              <input type="button" name="button" value="返回" class="button" onclick="window.history.go(-1)" />
            </td>
          </tr>
      </table>
      </div>
      <?php
      }else{
        echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
        echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'funds_plan_list_add.php?id='.$planid.'\'" /></p>';   
    }
    ?>
   


  </table>
  </div>
  <?php }elseif($action == 'boss'){ 
      $accountid = $_GET['accountid'];
        //通过对账单号在对账详情表中查找订单信息
         $order_sql = "SELECT `db_funds_plan_list`.`listid`,`db_account_order_list`.`order_number`,`db_account_order_list`.`order_amount`,`db_account_order_list`.`process_cost`,`db_account_order_list`.`cancel_amount`,`db_account_order_list`.`cut_payment`,(`db_account_order_list`.`order_amount` + `db_account_order_list`.`process_cost` - `db_account_order_list`.`cancel_amount` - `db_account_order_list`.`cut_payment`) AS `total_amount`,`db_funds_plan_list`.`plan_amount` FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid` WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_account_order_list`.`accountid` = '$accountid'";

          $result = $db->query($order_sql);
          if($result->num_rows){ ?>
        <div id="table_list">
        <form action="funds_plando.php" method="post">
            <table>
              <tr>
                <th>ID</th>
                <th width="13%">合同号</th>
                <th width="13%">物料金额</th>
                <th width="13%">加工费</th>
                <th width="13%">核销金额</th>
                <th width="13%">品质扣款</th>
                <th width="13%">应付金额</th>
                <th width="13%">计划金额</th>
              </tr>
             
             <?php 
               while($row_list = $result->fetch_assoc()){
              ?>
            <tr>
              <td><input type="checkbox" value="<?php echo $row_list['listid'] ?>" id="order_listid_<?php echo $row_list['listid'] ?>" name="id[]" ?></td>
              <td><?php echo $row_list['order_number'] ?></td>
              <td><?php echo $row_list['order_amount'] ?></td>
              <td><?php echo $row_list['process_cost'] ?></td>
              <td><?php echo $row_list['cancel_amount'] ?></td>
              <td><?php echo $row_list['cut_payment'] ?></td>
              <td id="order_amount_<?php echo $row_list['listid'] ?>">
                <?php echo $row_list['total_amount'] ?>
            </td>
            <td><?php echo $row_list['plan_amount'] ?></td>   
           </tr>
        <?php  
              }
            
       ?>
          
        </table>
       
      </form>
      <table>
          <tr>
            <td colspan="15">
              <input type="button" name="" value="审核" class="button" onclick="window.location.href='funds_apply_do.php?from=boss&action=complete&planid=<?php echo $planid ?>&accountid=<?php echo $accountid ?>'" />
            <!--   <input type="button" name="" value="撤回" class="button"  onclick="window.location.href='funds_apply_do.php?from=boss&action=back&planid=<?php echo $planid ?>&accountid=<?php echo $accountid ?>'" /> -->
              <input type="button" name="button" value="返回" class="button" onclick="window.history.go(-1)" />
            </td>
          </tr>
      </table>
      </div>
      <?php
      }else{
        echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
        echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'funds_plan_list_add.php?id='.$planid.'\'" /></p>';   
    }
    ?>
   


  </table>
  </div>
  <?php }elseif($action == 'funds'){
     $accountid = $_GET['accountid'];
        //通过对账单号在对账详情表中查找订单信息
            $order_sql = "SELECT `db_account_order_list`.`listid`,`db_account_order_list`.`order_number`,`db_account_order_list`.`order_amount`,`db_account_order_list`.`process_cost`,`db_account_order_list`.`cancel_amount`,`db_account_order_list`.`cut_payment`,(`db_account_order_list`.`order_amount` + `db_account_order_list`.`process_cost` - `db_account_order_list`.`cancel_amount` - `db_account_order_list`.`cut_payment`) AS `total_amount` FROM `db_account_order_list` WHERE `db_account_order_list`.`accountid` = '$accountid'";
        
          $result = $db->query($order_sql);
          if($result->num_rows){ ?>
        <div id="table_list">
        <form action="funds_plando.php" method="post">
            <table>
              <tr>
                <th>ID</th>
                <th width="13%">合同号</th>
                <th width="13%">物料金额</th>
                <th width="13%">加工费</th>
                <th width="13%">核销金额</th>
                <th width="13%">品质扣款</th>
                <th width="13%">应付金额</th>
                <th width="13%">计划金额</th>
              </tr>
             
             <?php 
               while($row_list = $result->fetch_assoc()){
                $listid = $row_list['listid'];
                //查询计划金额
                $plan_sql = "SELECT `plan_amount` FROM `db_funds_plan_list` WHERE `order_listid` = '$listid'";
                $result_plan = $db->query($plan_sql);
                if($result_plan->num_rows){
                  $plan_amount = $result_plan->fetch_assoc()['plan_amount'];
                }else{
                  $plan_amount = 0;
                }
              ?>
            <tr>
              <td><input type="checkbox" value="<?php echo $row_list['listid'] ?>" id="order_listid_<?php echo $row_list['listid'] ?>" name="id[]" ?></td>
              <td><?php echo $row_list['order_number'] ?></td>
              <td><?php echo $row_list['order_amount'] ?></td>
              <td><?php echo $row_list['process_cost'] ?></td>
              <td><?php echo $row_list['cancel_amount'] ?></td>
              <td><?php echo $row_list['cut_payment'] ?></td>
              <td id="order_amount_<?php echo $row_list['listid'] ?>">
                <?php
                 echo number_format(($row_list['order_amount'] + $row_list['process_cost'] - $row_list['cancel_amount'] - $row_list['cut_payment'] - $row_list['plan_amount']),2,'.','');
                    ?>
            </td>
            <td><?php echo $plan_amount ?></td>
           </tr>
        <?php  
          }
       ?> 
          <tr>
            <td colspan="15">
              <input type="button" name="button" value="返回" class="button" onclick="window.history.go(-1)" />
            </td>
          </tr>
      </table>
      </div>
      <?php }else{

        echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
        echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'material_funds_manage.php\'" /></p>';
          }   ?>
<?php }elseif($action == 'pay'){ 
   $accountid = $_GET['accountid'];
        //通过对账单号在对账详情表中查找订单信息
         $order_sql = "SELECT `db_funds_plan_list`.`listid`,`db_account_order_list`.`order_number`,`db_account_order_list`.`order_amount`,`db_account_order_list`.`process_cost`,`db_account_order_list`.`cancel_amount`,`db_account_order_list`.`cut_payment`,(`db_account_order_list`.`order_amount` + `db_account_order_list`.`process_cost` - `db_account_order_list`.`cancel_amount` - `db_account_order_list`.`cut_payment`) AS `total_amount`,`db_funds_plan_list`.`plan_amount` FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid` WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_account_order_list`.`accountid` = '$accountid'";

          $result = $db->query($order_sql);
          if($result->num_rows){ ?>
        <div id="table_list">
        <form action="funds_plando.php" method="post">
            <table>
              <tr>
                <th>ID</th>
                <th width="13%">合同号</th>
                <th width="13%">物料金额</th>
                <th width="13%">加工费</th>
                <th width="13%">核销金额</th>
                <th width="13%">品质扣款</th>
                <th width="13%">应付金额</th>
                <th width="13%">计划金额</th>
              </tr>
             
             <?php 
               while($row_list = $result->fetch_assoc()){
              ?>
            <tr>
              <td><input type="checkbox" value="<?php echo $row_list['listid'] ?>" id="order_listid_<?php echo $row_list['listid'] ?>" name="id[]" ?></td>
              <td><?php echo $row_list['order_number'] ?></td>
              <td><?php echo $row_list['order_amount'] ?></td>
              <td><?php echo $row_list['process_cost'] ?></td>
              <td><?php echo $row_list['cancel_amount'] ?></td>
              <td><?php echo $row_list['cut_payment'] ?></td>
              <td id="order_amount_<?php echo $row_list['listid'] ?>">
                <?php echo $row_list['total_amount'] ?>
            </td>
            <td><?php echo $row_list['plan_amount'] ?></td>   
           </tr>
        <?php  
              }
            
       ?>
          
        </table>
       
      </form>
      <table>
          <tr>
            <td colspan="15">
              <input type="button" name="button" value="返回" class="button" onclick="window.history.go(-1)" />
            </td>
          </tr>
      </table>
      </div>
      <?php
      }else{
        echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
        echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'funds_plan_list_add.php?id='.$planid.'\'" /></p>';   
    }
    ?>
   


  </table>
  </div>


  <?php }elseif($action == 'show'){
        
         $accountid = $_GET['accountid'];
        //通过对账单号在对账详情表中查找订单信息
            $order_sql = "SELECT `db_funds_plan_list`.`listid`,`db_account_order_list`.`order_number`,`db_account_order_list`.`order_amount`,`db_account_order_list`.`process_cost`,`db_account_order_list`.`cancel_amount`,`db_account_order_list`.`cut_payment`,(`db_account_order_list`.`order_amount` + `db_account_order_list`.`process_cost` - `db_account_order_list`.`cancel_amount` - `db_account_order_list`.`cut_payment`) AS `total_amount`,`db_funds_plan_list`.`plan_amount` FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid`  WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_account_order_list`.`accountid` = '$accountid' AND `db_funds_plan_list`.`plan_amount` > 0";
          $result = $db->query($order_sql);
          if($result->num_rows){ ?>
        <div id="table_list">
        <form action="funds_plando.php" method="post">
            <table>
              <tr>
                <th>ID</th>
                <th width="13%">合同号</th>
                <th width="13%">物料金额</th>
                <th width="13%">加工费</th>
                <th width="13%">核销金额</th>
                <th width="13%">品质扣款</th>
                <th width="13%">应付金额</th>
                <th width="13%">计划金额</th>
              </tr>
             
             <?php 
               while($row_list = $result->fetch_assoc()){
               
              ?>
            <tr>
              <td><input type="checkbox" value="<?php echo $row_list['listid'] ?>" id="order_listid_<?php echo $row_list['listid'] ?>" name="id[]" ?></td>
              <td><?php echo $row_list['order_number'] ?></td>
              <td><?php echo $row_list['order_amount'] ?></td>
              <td><?php echo $row_list['process_cost'] ?></td>
              <td><?php echo $row_list['cancel_amount'] ?></td>
              <td><?php echo $row_list['cut_payment'] ?></td>
              <td id="order_amount_<?php echo $row_list['listid'] ?>">
                <?php
                 echo number_format(($row_list['order_amount'] + $row_list['process_cost'] - $row_list['cancel_amount'] - $row_list['cut_payment'] - $row_list['plan_amount']),2,'.','');
                    ?>
            </td>
            <td><?php echo $row_list['plan_amount'] ?></td>
           </tr>
        <?php  
          }
       ?> 
          <tr>
            <td colspan="15">
              <input type="button" name="button" value="返回" class="button" onclick="window.history.go(-1)" />
            </td>
          </tr>
      </table>
      </div>
      <?php }}else{

        echo "<p class=\"tag\">系统提示：暂无未付款项</p>";
        echo '<p class="tag"><input type="button" name="button" value="返回" class="button" onclick="window.location.href = \'material_funds_manage.php\'" /></p>';
          }   ?>


<?php include "../footer.php"; ?>
</body>
</html>