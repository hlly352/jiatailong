<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$sdate_time = strtotime($sdate);
$edate_time = strtotime($edate);
//查找客户信息
$customer_sql ="SELECT `customer_id`,`customer_code`,`customer_name` FROM `db_customer_info`";
$res = $db->query($customer_sql);
if($res->num_rows){
	$customer_list = [];
	while($customer = $res->fetch_assoc()){
		$customer_list[] = $customer; 
	}
}
//获取当前页面的路径
$system_url =  dirname(__FILE__);

$system_pos =  strrpos($system_url,DIRECTORY_SEPARATOR);
$system_url = substr($system_url,$system_pos);
//通过路径查询对应的模块id
$system_id_sql = "SELECT `systemid` FROM `db_system` WHERE `system_dir` LIKE '%$system_url%'";
$system_id_res = $db->query($system_id_sql);
$system_id = $system_id_res->fetch_row()[0];
if($system_id ==' '){
  header('location:../myjtl/index.php');
}
//查询登录用户是否是客户管理的管理员
$system_sql = "SELECT `isadmin` FROM `db_system_employee` WHERE `employeeid`='$employeeid' AND `systemid`=".$system_id;
$system_res = $db->query($system_sql);

$system_info = [];
while($system_admin = $system_res->fetch_row()){
  $system_info = $system_admin;
}
//接受搜索条件
if($_GET['submit']){
  $customer_code = trim($_GET['customer_code']);
  $client_name = trim($_GET['customer_name']);
  $customer_order_no = trim($_GET['customer_order_no'])?"AND a.`customer_order_no` LIKE '%".$_GET['customer_order_no']."%'":' ';
  $project_name = trim($_GET['project_name']);
  $mould_no = trim($_GET['mould_no'])?"AND a.`mould_no` LIKE '%".$_GET['mould_no']."%'":' ';;
  $mould_name = trim($_GET['mould_name']);
  $currency = trim($_GET['currency']);
//拼接搜索条件
  $sqlwhere = "AND b.`customer_code` LIKE '%$customer_code%' AND b.`customer_name` LIKE '%$customer_name%' ".$customer_order_no."AND a.`project_name` LIKE '%$project_name%' ".$mould_no." AND a.`mould_name` LIKE '%$mould_name%' AND a.`currency` LIKE '%$currency%' AND (a.`deal_time` BETWEEN '$sdate_time' AND '$edate_time')";
}

//sql语句
if($system_info[0] == '1'){
        $sql = "SELECT * FROM `db_mould_data` as a INNER JOIN `db_customer_info` as b ON a.`client_name`=b.`customer_id` WHERE a.`is_approval` = '1' AND a.`order_approval`='1' AND a.`is_deal` = '1'".$sqlwhere;
    } else {
        $sql = "SELECT * FROM `db_mould_data` as a INNER JOIN `db_customer_info` as b ON a.`client_name`=b.`customer_id` WHERE a.`is_approval` = '1' AND a.`order_approval`='1' AND a.`is_deal` = '1' AND a.`employeeid` = '$employeeid'".$sqlwhere;
    }


$result = $db->query($sql);
//获取合计金额
if($result->num_rows){
    while($rows = $result->fetch_assoc()){
       $tot_agreement += $rows['agreement_price'];
       $tot_deal += $rows['deal_price'];
        //获取税金
          if($rows['currency'] == 'rmb_vat' || $rows['currency'] == 'rmb'){
               $tot_vat += number_format(Floatval($rows['deal_price'] * 0.13),2,'.','');
            }

    }

    //税价合计
    $tot_all = $tot_deal + $tot_vat;
}

$pages = new page($result->num_rows,30);
$sqllist = $sql . " ORDER BY `order_approval_time` DESC" . $pages->limitsql;
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

<title>订单管理-嘉泰隆</title>
<style type="text/css">
 /* #main{table-layout:fixed;width:100%}*/
  #main tr td{word-wrap:break-word;word-break:break-all;font-size:0.1px;}
  #main tr td input{width:120px;}
  #add_task{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;padding-top:2px;}
  #add_task+input{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;}
  .deal_price,.order_vat,.order_total_rmb,.rmb_tot{background:#ddd;}
  .input_tx{width:80px;margin-right:10px;}
</style>
<script type="text/javascript" charset="utf-8">
    $(function(){
	//点击每一行跳转到启动项目
      $('.show_list').live('click',function(){
        $('.show').each(function(){
          window.open('order_start.php','_self');
        })
      })
      /*//计算合计
        function getSubtotal(className,subName){
          var number = $(className).size();
          var subtotal = 0;
          for(var i=0;i<number;i++){
            if($(className).eq(i).text()){
            subtotal += parseFloat($(className).eq(i).text());
            }
          
          }
          
          subtotal = subtotal.toFixed(2);
          $(subName).text(subtotal);
        }
        getSubtotal('.agreement_price','#agreement_price');
        getSubtotal('.deal_price','#deal_price');
        getSubtotal('.order_vat','#order_vat');
        getSubtotal('.order_total_rmb','#order_total_rmb');*/
    })
</script>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4 style="padding-left:10px">
     
  </h4>
  <form action="" name="search" method="get">
    <table >
       <tr>
       <td>客户代码</td>
       <td><input type="text" name="customer_code" class="input_tx" /></td>
       <td></td>
       <td>客户名称</td>
       <td><input type="text" name="customer_name" class="input_tx"></td>
       <td></td>
        <td>客户订单号</td>
        <td><input type="text" name="customer_order_no" class="input_tx" /></td>
        <td></td>
        <td>项目名称</td>
        <td><input type="text" name="project_name" class="input_tx" /></td>
        <td></td>
        <td>模具编号</td>
        <td><input type="text" name="mould_no" class="input_tx" /></td>
        <td></td>
        <td>零件名称</td>
        <td><input type="text" name="mould_name" class="input_tx" /></td>
        <td></td>
        <td>币别</td>
         <td>
             <select class="input_tx input_txt" style="height:25px" name="currency">
                 <option value="">所有</option>

                 <?php foreach($array_currency as $k=>$v){ 
                     echo '<option value="'.$k.'">'.$v.'</option>';
                }?>
      </select>
        </td>
        <td>日期</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_tx" />
          --
          &nbsp;&nbsp;
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_tx" /></td>
        <td><input type="submit" name="submit" value="查找" class="button" />
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <form action="order_taskdo.php?action=add" name="list" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
          <?php
         if($result->num_rows){
            
        
    ?>
      <tr>
        <th  rowspan="2" width="18">ID</th>
        <th  rowspan="2">日期</th>
        <th  rowspan="2" width="25">客户代码</th>
        <th  rowspan="2" width="60">客户名称</th>
        <th  rowspan="2">客户订单号</th>
        <th  rowspan="2">项目名称</th>
        <th  rowspan="2">模具编号</th>
        <th  rowspan="2">零件名称</th>
        <th  rowspan="2">零件图片</th>
        <th  colspan="5">合同内容</th>
        <th  colspan="3">人民币计价</th>
        <th  colspan="4">进 度</th>
        <th  colspan="2">成 本</th>
        <th  colspan="2">盈 亏</th>
        <th  rowspan="2">损益/扣款</th>
        <th  rowspan="2">状 态</th>
      </tr>
      <tr>
        <th width="18">数量</th>
         <th>单价</th>
        <th width="45">币别</th>
        <th>汇率</th>
        <th>金额</th>
        <th>未税金额</th>
        <th>税金<br>(13%)</th>
        <th>价税合计</th>
        <th>收款比</th>
        <th>发票比</th>
        <th>付款比</th>
        <th>完工比</th>
        <th>材料成本</th>
        <th>制造费用</th>
        <th>金额</th>
        <th>盈亏比</th>
      </tr>
      <?php 
          while($row = $result->fetch_assoc()){
          //查询收款计划
        
         $pay_sql = "SELECT * FROM `db_order_pay` WHERE `mould_id` =".$row['mould_dataid'];
         $res = $db->query($pay_sql);
         $paylist = [];
         if($res->num_rows){
            $paylist = $res->fetch_assoc();
            }
        //查询发票情况
         $bill_sql = "SELECT * FROM `db_order_bill` WHERE `mould_id` =".$row['mould_dataid'];
         $res = $db->query($bill_sql);
         $bill_list = [];
         if($res->num_rows){
            $bill_list = $res->fetch_assoc();
            }
     
        //计算收款进度
        $total_pay = intval($paylist['one_reality_amount'] + $paylist['two_reality_amount'] + $paylist['three_reality_amount'] + $paylist['four_reality_amount'] + $paylist['five_reality_amount']);
        if($row['agreement_price'] != 0){
          $pay_percent = $total_pay / $row['agreement_price'] * 100;
        }
        $pay_percent = number_format($pay_percent,2,'.','').'%';
          //获取图片地址
          $src = $row['upload_final_path'];
          $src = $src?strstr($src,'$$')?substr($src,strpos($src,'$$')+2):$src:' ';
          //获取税金
          if($row['currency'] == 'rmb_vat' || $row['currency'] == 'rmb'){
               $order_vat = Floatval($row['deal_price'] * 0.13);
               $order_vat = number_format($order_vat,2,'.','');
            }else{
              $order_vat = 0;
            }
         
          //计算价税合计
          if($row['currency'] == 'rmb_vat'){
                
              $order_total_rmb = $row['agreement_price']*$row['mold_rate'];
          }else{
              $order_total_rmb = $row['deal_price'] + $order_vat;
              $order_total_rmb = number_format($order_total_rmb,2,'.','');
                
          }
        //计算发票比
        $total_bill = intval($bill_list['one_amount']) + intval($bill_list['two_amount']) + intval($bill_list['three_amount']) + intval($bill_list['four_amount']);
        if($order_total_rmb != 0){
          $bill_percent = floatval($total_bill / $order_total_rmb) * 100;
          $bill_percent = number_format($bill_percent,2,'.','').'%';
        }
           

      ?>
     <tr class="show">
         <td><input type="checkbox" name="id[]" value="<?php echo $row['mould_dataid']; ?>" style="width:20px"/></td>
        <td class="show_list"><?php echo date('y-m-d',$row['deal_time']) ?></td>
        <td class="show_list"><?php echo strstr($row['customer_code'],'$$')?substr($row['customer_code'],strrpos($row['customer_code'],'$$')+2):$row['customer_code']?></td>
        <td class="show_list"><?php echo strstr($row['customer_name'],'$$')?substr($row['customer_name'],strrpos($row['customer_name'],'$$')+2):$row['customer_name']?></td>
        <td class="show_list"><?php echo $row['customer_order_no'] ?></td>
        <td class="show_list"><?php echo $row['project_name']?></td>  
        <td class="show_list"><?php echo 'JTL'.$row['mould_no']?></td>
        <td class="show_list"><?php echo $row['mould_name']?></td>
        <td class="show_list"><?php echo strstr($src,'/')?'<img src='.$src.' width="50" height="35"/>': $src ?></td>
        <td class="show_list"><?php echo $row['number']?$row['number']:1 ?></td>
        <td class="show_list"><?php echo $row['unit_price'] == 0?$row['agreement_price']:$row['unit_price']?></td>
        <td class="show_list"><?php echo $array_currency[$row['currency']]?></td>
        <td class="show_list"><?php echo $row['mold_rate']?></td>
        <td class="show_list agreement_price"><?php echo number_format($row['agreement_price'],2,'.','')?></td>
        <td class="show_list deal_price"><?php echo number_format($row['deal_price'],2,'.','')?></td>
        <td class="show_list order_vat"><?php echo $order_vat ?></td>
        <td class="show_list order_total_rmb"><?php echo $order_total_rmb?></td>
        <td class="show_list"><?php echo $pay_percent ?></td>
        <td class="show_list"><?php echo $bill_percent?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>
        <td class="show_list"></td>
        <td class="show_list"><span>待启动</span></td>
      </tr> 

      <?php } ?>
       <tr>
        <td colspan="8">合 计</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td id="agreement_price" ><?php echo $tot_agreement ?></td>
        <td id="deal_price" class="rmb_tot"><?php echo $tot_deal ?></td>
        <td id="order_vat" class="rmb_tot"><?php echo $tot_vat ?></td>
        <td id="order_total_rmb" class="rmb_tot"><?php echo $tot_all ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
       </table>
       </table>
    <!--<div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
    -->
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php  } else{
     echo '<p class="tag">系统提示：暂无记录！</p>';
  } ?>
      
</div>
 <?php include "../footer.php"; ?>
</body>
</html>