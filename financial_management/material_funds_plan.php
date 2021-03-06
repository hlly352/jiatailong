<meta http-equiv="Content-Type" Content="text/html;charset=utf-8">
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));

//查询采购管理的管理员
$isconfirm = $_SESSION['system_shell'][$system_dir]['isconfirm'];
//查找总经办人员
$isadmin = $_SESSION['system_shell'][$system_dir]['isadmin'];
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
$sql = "SELECT * FROM `db_material_funds_plan` INNER JOIN `db_employee` ON `db_material_funds_plan`.`employeeid` = `db_employee`.`employeeid` WHERE (`db_material_funds_plan`.`plan_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";

$result = $db->query($sql);
$result_id = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_funds_plan`.`planid` DESC" . $pages->limitsql;
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
  $(function(){
    //动态更改提交状态
    var num = $('.status').size();
    for(var i=0;i<num;i++){
      console.log($('.status').eq(i).html());
      if(($('.status').eq(i).html()) == 0){
        $('.status').eq(i).html('审核').css('cursor','pointer').css('color','blue');
      } else if(($('.status').eq(i).html())==1) {
        $('.status').eq(i).html('撤回').css('cursor','pointer');
      }
    }

    $('.status').live('click',function(){
      var content = $(this).html();
      if(content == '审核' || content == '撤回'){
      var id = $(this).attr('id');
      var planid = id.substr(id.indexOf('-')+1);
      var count = $("#count-"+planid).html();
      if(count == 0){
        alert('请先添加项目');
      } else {
        $.post('../ajax_function/change_funds_plan_status.php',{planid:planid},function(data){
           var new_status = $("#status-"+data).html();
           if(new_status == '审核'){
            $("#status-"+data).html('撤回');
           }else{
            $("#status-"+data).html('审核');
           }
           window.location.reload();
        })
      }
    }
    })
  })
</script>
<title>财务管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>付款计划及执行</h4>
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
        <th>订单状态：</th>
        <td><select name="order_status">
            <option value="">所有</option>
            <?php
            foreach($array_order_status as $order_status_key=>$order_status_value){
        echo "<option value=\"".$order_status_key."\">".$order_status_value."</option>";
      }
      ?>
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
        <th rowspan="3">ID</th>
        <th rowspan="3">计划单号</th>
        <th rowspan="3">计划时间</th>
        <th rowspan="3">计划金额</th>
        <th rowspan="3">项数</th>
        <th colspan="3" >付款计划</th>
        <th colspan="6">付款执行</th>
        <th rowspan="3">详情</th>
      </tr>
      <tr>
        <th rowspan="2">采购审核</th>
        <th rowspan="2">财务审核</th>
        <th rowspan="2">总经办审批</th>
        <th rowspan="2">付款申请</th>
        <th rowspan="2">采购审核</th>
        <th rowspan="2">财务审核</th>
        <th rowspan="2">总经办审批</th>
        <th colspan="2">支付状态</th>
      </tr>
      <tr>
        <th>出纳</th>
        <th>会计</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
      $planid = $row['planid'];
      //判断采购是否审核过付款
      $purchase_sql = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` = 'B' AND `planid` = '$planid'";
      $result_purchase  = $db->query($purchase_sql);
      $count_purchase = $result_purchase->num_rows;
       //查找采购是否审核过付款
      $financial_sql = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status`= 'C' AND `planid` = '$planid'";
      $result_financial = $db->query($financial_sql);
      $count_financial = $result_financial->num_rows;
      //查找财务是否审批过付款
      $boss_sql = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` = 'D' AND `planid` = '$planid'";
      $result_boss = $db->query($boss_sql);
      $count_boss = $result_boss->num_rows;
      //查找总经办是否审核过付款
      $pay_sql  = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` = 'E' AND `planid` = '$planid'";
      $result_pay = $db->query($pay_sql);
      $count_pay = $result_pay->num_rows;
      //查找已经付款的项目
      $payment_sql = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` = 'F' AND `tot_payment` > 0 AND `planid` = '$planid'";

      $result_payment = $db->query($payment_sql);
      $count_payment = $result_payment->num_rows;
      //查找项数
      $list_sql  = "SELECT COUNT(*) AS `count`,SUM(`plan_amount`) AS `plan_amount` FROM `db_funds_plan_list` WHERE `planid` = '$planid'";
      $result_list = $db->query($list_sql);
      if($result_list->num_rows){
        $list_count = $result_list->fetch_assoc();
      }
    ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $planid; ?>"<?php if( $employeeid != $row['employeeid']) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['plan_number']; ?></td>
        <td><?php echo $row['plan_date']; ?></td>
        <td><?php echo $list_count['plan_amount']; ?></td>
        <td class="count" id="count-<?php echo $planid ?>"><?php echo $list_count['count']; ?></td>
        <td>
          
          <span class="status" id="status-<?php echo $planid ?>">
            <?php
              if($row['plan_status'] == 0){
                echo '未提交';
              }elseif($row['plan_status'] >0){
                echo ' <img src="../images/system_ico/dui.png"  />';
              }
            ?>  
          </span>
          
        </td>
        <td>
          <?php
            if($row['plan_status'] == 1){
              echo '<a href="funds_plan_info.php?action=financial&planid='.$planid.'">审核</a>';
            }elseif($row['plan_status'] >1){
              echo '<img src="../images/system_ico/dui.png"  />';
            }
          ?>
        </td>
        <td>
          <?php
            if($row['plan_status'] == '3'){
              echo '待审核';
            }elseif($row['plan_status'] > 3){
              echo '<img src="../images/system_ico/dui.png"  />';

            }
          ?>  
        </td>
        <td><?php if($row['plan_status'] == '5'){ ?>
            待申请
          <?php }elseif($row['plan_status'] > 5){
              echo '<img src="../images/system_ico/dui.png"  />';
            }
            ?>
        </td>
          <td>
            <?php
              if($row['plan_status'] == 5 || $row['plan_status'] == 7){
                if($count_purchase > 0){
                    echo '待审核';
                 }
              }elseif($row['plan_status'] > 7){

                echo '<img src="../images/system_ico/dui.png"  />';
              }
            ?>
          </td>
        <td>
          <?php
            if($row['plan_status'] <= 9){
              if($count_financial >0){  
               echo '<a href="funds_pay_apply.php?action=approval&id='.$planid.'">审核</a>';
              }
            }elseif($row['plan_status'] >9){
              echo '<img src="../images/system_ico/dui.png" />';
            }
          ?>
        </td>
        <td>
          <?php
             if($row['plan_status'] < 13){
              if($count_boss >0){  
               echo '待审核';
              }
            }elseif($row['plan_status'] >= 13){
              echo '<img src="../images/system_ico/dui.png" />';
            }
          ?>
        </td>
        <td>
         <?php
             if($row['plan_status'] < 15){
              if($count_pay >0){  
               echo '<a href="funds_pay_apply.php?action=pay&id='.$planid.'">支付</a>';
              }
            }elseif($row['plan_status'] >= 15){
              echo '<img src="../images/system_ico/dui.png" />';
            }
          ?>
        </td>
        <td>
          <?php
            if($row['plan_status'] < 17){
              if($count_payment > 0){
                 echo '<a href="funds_pay_apply.php?action=over&id='.$planid.'">审核</a>';
              }
            }elseif($row['plan_status'] =17){
              echo '<img src="../images/system_ico/dui.png" />';

            }
          ?>
        </td>
        <td><?php if($list_count){ ?>
          <a href="funds_plan_content.php?id=<?php echo $planid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a>
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
    echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>