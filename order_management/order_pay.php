<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查找客户信息
$customer_sql ="SELECT `customer_id`,`customer_code`,`customer_name` FROM `db_customer_info`";
$res = $db->query($customer_sql);
if($res->num_rows){
	$customer_list = [];
	while($customer = $res->fetch_assoc()){
		$customer_list[] = $customer; 
	}
}

if($_GET['submit']){
  $mould_name = trim($_GET['mould_name']);
  $client_name = trim($_GET['client_name']);
  $project_name = trim($_GET['project_name']);
  $sqlwhere = "  AND `client_name` LIKE '%$client_name%' AND `mould_name` LIKE '%$mould_name%' AND `project_name` LIKE '%$project_name%'";
}

//sql语句
$sql = "SELECT * FROM `db_mould_data` INNER JOIN `db_customer_info` as b ON `db_mould_data`.`client_name`=b.`customer_id` WHERE `is_approval` = '1' AND `order_approval`='1' AND `is_deal` = '1'".$sqlwhere;
$result = $db->query($sql);
//获取合计金额
if($result->num_rows){
    while($info = $result->fetch_assoc()){
       $tot_agreement += $info['agreement_price'];
       $tot_deal += $info['deal_price'];
       
        //获取税金
          if($info['currency'] == 'rmb_vat' || $info['currency'] == 'rmb'){
               $tot_vat += number_format(Floatval($info['deal_price'] * 0.13),2,'.','');
            }
          //获取计划金额和实际金额的合计
          $pay_sql = "SELECT * FROM `db_order_pay` WHERE `mould_id` =".$info['mould_dataid'];
         $tot_res = $db->query($pay_sql);
         if($tot_res->num_rows){
         			
         	 	   while($rows = $tot_res->fetch_assoc()){
		       $tot_one_plan += number_format(Floatval($rows['one_plan_amount']),2,'.','');
		       $tot_one_reality += number_format(Floatval($rows['one_reality_amount']),2,'.','');
		       $tot_two_plan += number_format(Floatval($rows['two_plan_amount']),2,'.','');
		       $tot_two_reality += number_format(Floatval($rows['two_reality_amount']),2,'.','');
		       $tot_three_plan += number_format(Floatval($rows['three_plan_amount']),2,'.','');
		       $tot_three_reality += number_format(Floatval($rows['three_reality_amount']),2,'.','');
		       $tot_four_plan += number_format(Floatval($rows['four_plan_amount']),2,'.','');
		       $tot_four_reality += number_format(Floatval($rows['four_reality_amount']),2,'.','');
		       //实际已收人民币计价
		       $reality_rmb = (number_format(Floatval($rows['one_reality_amount']),2,'.','') + number_format(Floatval($rows['two_reality_amount']),2,'.','') +  number_format(Floatval($rows['three_reality_amount']),2,'.','') +  number_format(Floatval($rows['four_reality_amount']),2,'.','')) * $info['mold_rate'];
		       $tot_reality_rmb += $reality_rmb;
		       //实际未收人民币计价

		       $reality_no_rmb = (number_format($info['agreement_price'],2,'.','') - number_format(Floatval($rows['one_reality_amount']),2,'.','') - number_format(Floatval($rows['two_reality_amount']),2,'.','') -  number_format(Floatval($rows['three_reality_amount']),2,'.','') -  number_format(Floatval($rows['four_reality_amount']),2,'.','')) ;
		       
		       $reality_no = $reality_no_rmb?$reality_no_rmb:0;
		       
		       $tot_no_rmb += $reality_no * $info['mold_rate'];
		       
		   }
		  //实际已收和未收
  		  $tot_reality_pay = $tot_one_reality + $tot_two_reality + $tot_three_reality + $tot_four_reality;
   		  $tot_reality_no_pay = $tot_deal - $tot_reality_all;
   		  //实际已收和未收人民币计价
   		  
         } else {
         		//没有查询到结果，直接赋值为合同金额
         		$tot_no_rmb += $info['agreement_price'];
         }

    }

    //税价合计
    $tot_all = $tot_deal + $tot_vat;

}


$pages = new page($result->num_rows,8);
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
  /*#main{table-layout:fixed;width:1350px;}*/
  #main tr td{word-wrap:break-word;word-break:break-all;}
  #main tr td input{width:120px;}
  #main .show .show_list{font-size:0.2px;}
  .deal_price,.order_vat,.order_total_rmb,.rmb_tot,.reality_pay,.reality_no_pay{background:#ddd;}
</style>
<script type="text/javascript" charset="utf-8">
    $(function(){
	$('.show_list').live('click',function(){
	 	  var mold_dataid = $(this).parent().children().children('[name^=id]:checkbox').val();
	 	  var agreement_price = $(this).siblings('.agreement_price').text();
	 	  var mould_no = $(this).parent().children('.mould_no').text();
	 	  //跳转页面，填写信息
     		 $('.show').each(function(){
    	           window.open('order_add_pay.php?mould_no='+mould_no+'&id='+mold_dataid+'&agreement_price='+agreement_price,'_self');
    	      			})
	})
	//计算合计
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
      	getSubtotal('.order_total_rmb','#order_total_rmb');
      	getSubtotal('.one_plan_amount','#one_plan_amount')
	getSubtotal('.one_reality_amount','#one_reality_amount')
	getSubtotal('.two_plan_amount','#two_plan_amount')
	getSubtotal('.two_reality_amount','#two_reality_amount')
	getSubtotal('.three_plan_amount','#three_plan_amount')
	getSubtotal('.three_reality_amount','#three_reality_amount')
	getSubtotal('.four_plan_amount','#four_plan_amount')
	getSubtotal('.four_reality_amount','#four_reality_amount')
	getSubtotal('.reality_pay','#reality_pay')
	getSubtotal('.reality_no_pay','#reality_no_pay')
	getSubtotal('.reality_pay_rmb','#reality_pay_rmb')
	getSubtotal('.reality_no_pay_rmb','#reality_no_pay_rmb')
	
    })
</script>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4 style="padding-left:10px">
     
  </h4>
  <form action="order_taskdo.php" name="search" method="get">
    <table >

      <tr>
   
       </tr>
       <tr>
       <td>客户名称</td>
       <td><input type="text" name="client_name" class="input_txt" /></td>
       <td></td>
       <td>项目名称</td>
       <td><input type="text" name="project_name" class="input_txt"></td>
       <td></td>
        <td>客户代码</td>
        <td><input type="text" name="mould_name" class="input_txt" /></td>
        <td>日期</td>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
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
        <th rowspan="3" width="12">ID</th>
        <th rowspan="3">日期</th>
        <th rowspan="3" width="25">客户代码</th>
        <th rowspan="3" width="60">客户名称</th>
        <th rowspan="3">客户订单号</th>
        <th rowspan="3">项目名称</th>
        <th rowspan="3">模具编号</th>
        <th colspan="5">合同内容</th>
        <th colspan="3">人民币计价</th>
        <th rowspan="3">收款比</th>
        <th colspan="4">一期</th>
        <th colspan="4">二期</th>
        <th colspan="4">三期</th>
        <th colspan="4">四期</th>
        <th colspan="4">小计</th>
        <th rowspan="3">损益 /<br>扣款</th>
      </tr>
      <tr>
      	<th rowspan="2">数量</th>
      	<th rowspan="2">单价</th>	
      	<th rowspan="2" width="45">币别</th>
      	<th rowspan="2">汇率</th>
      	<th rowspan="2">金额</th>
      	<th rowspan="2">未税金额</th>
      	<th rowspan="2">税金<br>(13%)</th>
      	<th rowspan="2">税价合计</th>
      	<th colspan="2">计划</th>
      	<th colspan="2">实际</th>
      	<th colspan="2">计划</th>
      	<th colspan="2">实际</th>
      	<th colspan="2">计划</th>
      	<th colspan="2">实际</th>
      	<th colspan="2">计划</th>
      	<th colspan="2">实际</th>
      	<th colspan="2">实际</th>
      	<th colspan="2">实际人民币计价</th>
      </tr>
      <tr>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>日期</th>
      	<th>金额</th>
      	<th>已收</th>
      	<th>未收</th>
      	<th>已收</th>
      	<th>未收</th>
      	
      </tr>
      <?php 
      	//处理时间格式
       	function getTime($time){
       		if($time != null){
       			echo date('Y-m',strtotime($time));
       		}
       	}
          while($row = $result->fetch_assoc()){
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
          
         //查询收款计划
                
         $pay_sql = "SELECT * FROM `db_order_pay` WHERE `mould_id` =".$row['mould_dataid'];

         $res = $db->query($pay_sql);
         $paylist = [];
         if($res->num_rows){
         		$paylist = $res->fetch_assoc();
       		  }

       	//计算收款比
       	$total_pay = intval($paylist['one_reality_amount'] + $paylist['two_reality_amount'] + $paylist['three_reality_amount'] + $paylist['four_reality_amount'] + $paylist['five_reality_amount']);
       	if($row['agreement_price'] != 0){
   	    	$pay_percent = $total_pay / $row['agreement_price'] * 100;
   	    }
   	    $pay_percent = number_format($pay_percent,2,'.','').'%';
       	//计算计划已收
       	$plan_pay = Floatval($paylist['one_plan_amount'] + $paylist['two_plan_amount'] + $paylist['three_plan_amount'] + $paylist['four_plan_amount']);
       	$plan_pay = number_format($plan_pay,2,'.','');
       	//计划未收
       	$plan_no_pay = Floatval($row['agreement_price'] - $plan_pay);
       	$plan_no_pay = number_format($plan_no_pay,2,'.','');
       	//实际已收
       	$reality_pay = Floatval($paylist['one_reality_amount'] + $paylist['two_reality_amount'] + $paylist['three_reality_amount'] + $paylist['four_reality_amount']);
       	$reality_pay = number_format($reality_pay,2,'.','');
       	
       	//人民币计价
       	$reality_pay_rmb = Floatval($reality_pay * $row['mold_rate']);
       	$reality_pay_rmb = number_format($reality_pay_rmb,2,'.','');
       	//实际未收
       	$reality_no_pay = Floatval($row['agreement_price'] - $reality_pay);
       	$reality_no_pay = number_format($reality_no_pay,2,'.','');
       	
       	//人民币计价
       	$reality_no_pay_rmb = Floatval($reality_no_pay * $row['mold_rate']);
       	
       	$reality_no_pay_rmb = number_format($reality_no_pay_rmb,2,'.','');

      ?>
     <tr class="show">
         <td><input type="checkbox" name="id[]" value="<?php echo $row['mould_dataid']; ?>" style="width:20px"/></td>
        <td class="show_list"><?php echo date('y-m-d',$row['deal_time']) ?></td>
        <td class="show_list"><?php echo strstr($row['customer_code'],'$$')?substr($row['customer_code'],strrpos($row['customer_code'],'$$')+2):$row['customer_code']?></td>
        <td class="show_list customer_name"><?php echo strstr($row['customer_name'],'$$')?substr($row['customer_name'],strrpos($row['customer_name'],'$$')+2):$row['customer_name']?></td>
        <td class="show_list"><?php echo $row['customer_order_no'] ?></td>
        <td class="show_list"><?php echo $row['project_name']?></td>        
        <td class="show_list mould_no"><?php echo 'JTL'.$row['mould_no']?></td>
        <td class="show_list"><?php echo $row['number']?$row['number']:1 ?></td>
        <td class="show_list"><?php echo $row['unit_price'] == 0?$row['agreement_price']:$row['unit_price']?></td>
        <td class="show_list"><?php echo $array_currency[$row['currency']]?></td>
        <td class="show_list"><?php echo $row['mold_rate']?></td>
        <td class="show_list agreement_price"><?php echo $row['agreement_price']?></td>
        <td class="show_list deal_price"><?php echo $row['deal_price']?></td>
        <td class="show_list order_vat"><?php echo $order_vat ?></td>
        <td class="show_list order_total_rmb"><?php echo $order_total_rmb ?></td>
        <td class="show_list"><?php echo $pay_percent ?></td>
        <td class="show_list"><?php getTime($paylist['one_plan_date'])?></td>
        <td class="show_list one_plan_amount"><?php echo $paylist['one_plan_amount']?></td>
        <td class="show_list"><?php getTime($paylist['one_reality_date'])?></td>
        <td class="show_list one_reality_amount"><?php echo $paylist['one_reality_amount']?></td>
         <td class="show_list"><?php getTime($paylist['two_plan_date'])?></td>
        <td class="show_list two_plan_amount"><?php echo $paylist['two_plan_amount']?></td>
        <td class="show_list"><?php getTime($paylist['two_reality_date'])?></td>
        <td class="show_list two_reality_amount"><?php echo $paylist['two_reality_amount']?></td>
         <td class="show_list"><?php getTime($paylist['three_plan_date'])?></td>
        <td class="show_list three_plan_amount"><?php echo $paylist['three_plan_amount']?></td>
        <td class="show_list"><?php getTime($paylist['three_reality_date'])?></td>
        <td class="show_list three_reality_amount"><?php echo $paylist['three_reality_amount']?></td>
         <td class="show_list"><?php getTime($paylist['four_plan_date'])?></td>
        <td class="show_list four_plan_amount"><?php echo $paylist['four_plan_amount']?></td>
        <td class="show_list"><?php getTime($paylist['four_reality_date'])?></td>
        <td class="show_list four_reality_amount"><?php echo $paylist['four_reality_amount']?></td>
        <td class="show_list reality_pay"><?php echo $reality_pay?></td>
        <td class="show_list reality_no_pay"><?php echo $reality_no_pay?></td>
        <td class="show_list reality_pay_rmb"><?php echo $reality_pay_rmb?></td>
        <td class="show_list reality_no_pay_rmb"><?php echo $reality_no_pay_rmb?></td>
        <td class="show_list"><?php echo $paylist['deducation'] ?></td>
      </tr> 

      <?php } 
      ?>
        <tr>
      	<td colspan="7">合 计</td>
      	<td></td>
      	<td></td>
      	<td></td>
      	<td></td>
      	<td id="agreement_price"><?php echo $tot_agreement ?></td>
      	<td id="deal_price" class="rmb_tot"><?php echo $tot_deal ?></td>
      	<td id="order_vat" class="rmb_tot"><?php echo $tot_vat ?></td>
      	<td id="order_total_rmb" class="rmb_tot"><?php echo $tot_all ?></td>
      	<td></td>
      	<td></td>
      	<td id="one_plan_amount"><?php echo $tot_one_plan ?></td>
      	<td></td>
      	<td id="one_reality_amount"><?php echo $tot_one_reality ?></td>
      	<td></td>
      	<td id="two_plan_amount"><?php echo $tot_two_plan ?></td>
      	<td></td>
      	<td id="two_reality_amount"><?php echo $tot_two_reality ?></td>
      	<td></td>
      	<td id="three_plan_amount"><?php echo $tot_three_plan ?></td>
      	<td></td>
      	<td id="three_reality_amount"><?php echo $tot_three_reality ?></td>
      	<td></td>
      	<td id="four_plan_amount"><?php echo $tot_four_plan ?></td>
      	<td></td>
      	<td id="four_reality_amount" ><?php echo $tot_four_reality ?></td>
      	<td id="reality_pay" class="rmb_tot"><?php echo $tot_reality_pay ?></td>
      	<td id="reality_no_pay" class="rmb_tot"><?php echo $tot_reality_no_pay ?></td>
      	<td id="reality_pay_rmb"><?php $tot_reality_rmb ?></td>
      	<td id="reality_no_pay_rmb"><?php echo $tot_no_rmb ?></td>
      	<td></td>

      </tr>
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