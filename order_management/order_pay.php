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
  //获取当前的月份和年份
  $now_year=$before_year=$after_year = date('Y');
  $now_month=$before_month=$after_month = date('m');
  //新数组用来接收横坐标
  $x_arr = [];
  // //获取当前时间的前一年时间
  for($i=0;$i<12;$i++){	
  	$before_month --;
  	if($before_month==0){
  		$before_month = 12;
  		$before_year--;
  	}
  	$before_month = $before_month<10?'0'.$before_month:$before_month;
  	array_unshift($x_arr,$before_year.'-'.$before_month);
  }
  //插入当前月
  $x_arr[] = date('Y').'-'.date('m');
  //获取当前时间后一年半的时间
  for($j=0;$j<18;$j++){
  	$after_month ++;
  	if($after_month == 13){
  		$after_month = 1;
  		$after_year ++;
  	}
  	$after_month = $after_month<10?'0'.$after_month:$after_month;
  	$x_arr[] = $after_year.'-'.$after_month;
  }
//  var_dump($x_arr);
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
//接收折线图数据
$y_plan_arr = array();
$y_reality_arr = array();
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
  	//抓取折线图所需数据
  	
  	foreach($x_arr as $k=>$v){
  		//计划金额
  		switch($v){
  			case $rows['one_plan_date']:
  				$y_plan_arr[$v] += $rows['one_plan_amount'] * $info['mold_rate'];
  	
  			break;
  			case $rows['two_plan_date']:
  				$y_plan_arr[$v] += $rows['two_plan_amount'] * $info['mold_rate'];
  	
  			break;
  			case $rows['three_plan_date']:
  				$y_plan_arr[$v] += $rows['three_plan_amount'] * $info['mold_rate'];
  	
  			break;
  			case $rows['four_plan_date']:
  				$y_plan_arr[$v] += $rows['four_plan_amount'] * $info['mold_rate'];
  	
  			break;
  			
  			default:
  				$y_plan_arr[$v] += 0;
  				
  		}
  		switch($v){
  			case $rows['one_reality_date']:
  				$y_reality_arr[$v] += $rows['one_reality_amount'] * $info['mold_rate'];
  	
  			break;
  			case $rows['two_reality_date']:
  				$y_reality_arr[$v] += $rows['two_reality_amount'] * $info['mold_rate'];
  	
  			break;
  			case $rows['three_reality_date']:
  				$y_reality_arr[$v] += $rows['three_reality_amount'] * $info['mold_rate'];
  	
  			break;
  			case $rows['four_plan_date']:
  				$y_reality_arr[$v] += $rows['four_reality_amount'] * $info['mold_rate'];
  	
  			break;
  			default:
  				$y_reality_arr[$v] += 0;
  		}
      
      		 }
      		
  	}
      		//实际已收和未收
        		$tot_reality_pay = $tot_one_reality + $tot_two_reality + $tot_three_reality + $tot_four_reality;

     	    } else {
            //没有查询到结果，直接赋值为合同金额
            $tot_no_rmb += $info['agreement_price'];
         }

    }
        
        $tot_reality_no_pay = $tot_agreement - $tot_reality_pay;
    //税价合计
    $tot_all = $tot_deal + $tot_vat;

}
//var_dump($y_plan_arr);
//var_dump($y_reality_arr);
$plan_arr = [];
$reality_arr = [];
foreach($y_plan_arr as $v){
	$plan_arr[] = $v;
}
foreach($y_reality_arr as $v){
	$reality_arr[] = 0;
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
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript" src="../js/code/highcharts.js"></script>
<script language="javascript" type="text/javascript" src="../js/code/modules/series-label.js" charset="utf-8"></script>


<title>订单管理-嘉泰隆</title>
<style type="text/css">
  /*#main{table-layout:fixed;width:1350px;}*/
  #main tr td{word-wrap:break-word;word-break:break-all;}
  #main tr td input{width:120px;}
  #main .show .show_list{font-size:0.2px;}
  .deal_price,.order_vat,.order_total_rmb,.rmb_tot,.reality_pay,.reality_no_pay{background:#ddd;}
   .input_tx{width:80px;margin-right:10px;}
</style>
<script type="text/javascript" charset="utf-8">
    $(function(){
      //把逾期未收的单元格设置为黄色背景
      var num = $('.mould_no').size();
      for(var i=0;i<num;i++){
        //计划日期的个数
        var plan_num = $('.mould_no').eq(i).siblings('.plan_date').size();
        for(var j=0;j<plan_num;j++){
          //获取计划的日期
          var plan_date_str = $('.mould_no').eq(i).siblings('.plan_date').eq(j).text();
          //获取月份
          if(plan_date_str){
            var plan_date_mon = plan_date_str.substr(plan_date_str.indexOf('-')+1);
            //获取当前月份
            var now_date = new Date()
            var now_mon = now_date.getMonth() + 1;
            
            var offset_mon = now_mon - parseInt(plan_date_mon);
              //判断是否有内容，无内容且超期的为黄色背景
              var reality_date = $('.mould_no').eq(i).siblings('.plan_date').eq(j).next().next().text();
              if(reality_date){
                  //获取填写的月份
                  var reality_date_mon = reality_date.substr(reality_date.indexOf('-')+1);
                  var reality_offset_mon = reality_date_mon - parseInt(plan_date_mon);
                  if(reality_offset_mon >0){
                    $('.mould_no').eq(i).siblings('.plan_date').eq(j).next().next().css('color','orange');
                  $('.mould_no').eq(i).siblings('.plan_date').eq(j).next().next().next().css('color','orange');
                  }
              } else {
                 if(offset_mon >0){
                $('.mould_no').eq(i).siblings('.plan_date').eq(j).next().next().css('background','yellow');
                $('.mould_no').eq(i).siblings('.plan_date').eq(j).next().next().next().css('background','yellow');
                }
              }
              
            }
          
        
          //console.log(offset_mon);
          //console.log(plan_date_mon);
        }
        //var one_plan = $('.mould_no').eq(i).siblings('.plan_date').eq(0).text();
        //console.log(one_plan);
      }
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
  /*function getSubtotal(className,subName){
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
  getSubtotal('.reality_no_pay_rmb','#reality_no_pay_rmb')*/
  //获取当前的月份和年份
  var nowtime = new Date();
  var now_year=before_year=after_year = nowtime.getFullYear();
  var now_month=before_month=after_month = nowtime.getMonth();
  //新数组用来接收横坐标
  var x_arr = [];
  // //获取当前时间的前一年时间
  for(var i=0;i<11;i++){	
  	before_month --;
  	if(before_month==0){
  		before_month = 12;
  		before_year--;
  	}
  	before_month = before_month<10?'0'+before_month:before_month;
  	x_arr.unshift(before_year+'/'+before_month);
  }
  //插入当前月
  x_arr.push(now_year+'/'+now_month);
  //获取当前时间后一年半的时间
  for(var j=0;j<19;j++){
  	after_month ++;
  	if(after_month == 13){
  		after_month = 1;
  		after_year ++;
  	}
  	after_month = after_month<10?'0'+after_month:after_month;
  	x_arr.push(after_year+'/'+after_month);
  }
  //通过ajax 获取折线图的数据
/*  $.post('../ajax_function/getChartsDate.php',{arr:x_arr},function(data,status){
  	console.log(data);
  },'json')*/
  //把php 数组转换为js 数组
  var plan_str =  eval(<?php echo json_encode($plan_arr);?>);
  var reality_str = eval(<?php echo json_encode($reality_arr); ?>);
  //收款信息折线图  
  var title = {
               text: '收款计划与实际'   
           };
           var subtitle = {
                
           };
           var xAxis = {
               categories: x_arr
           };
           var yAxis = {
              title: {
                 text: '金额'
              },
              plotLines: [{
                 value: 0,
                 width: 1,
                 color: '#808080'
              }]
           };   

           var tooltip = {
              valueSuffix: '元'
           }

           var legend = {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle',
              borderWidth: 0
           };

           var series =  [
              {
                 name: '计划',
                 data:plan_str
              }, 
              {
                 name: '实际',
                 data: reality_str
              }, 
        
           ];
          
           var json = {};

           json.title = title;
           json.subtitle = subtitle;
           json.xAxis = xAxis;
           json.yAxis = yAxis;
           json.tooltip = tooltip;
           json.legend = legend;
           json.series = series;

           $('#container').highcharts(json);
  
      
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
        <th rowspan="2" width="25">汇率</th>
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
        <td class="show_list mould_no"><?php echo $row['mould_no']?></td>
        <td class="show_list"><?php echo $row['number']?$row['number']:1 ?></td>
        <td class="show_list"><?php echo $row['unit_price'] == 0?$row['agreement_price']:$row['unit_price']?></td>
        <td class="show_list"><?php echo $array_currency[$row['currency']]?></td>
        <td class="show_list"><?php echo $row['mold_rate']?></td>
        <td class="show_list agreement_price"><?php echo $row['agreement_price']?></td>
        <td class="show_list deal_price"><?php echo $row['deal_price']?></td>
        <td class="show_list order_vat"><?php echo $order_vat ?></td>
        <td class="show_list order_total_rmb"><?php echo $order_total_rmb ?></td>
        <td class="show_list"><?php echo $pay_percent ?></td>
        <td class="show_list plan_date"><?php getTime($paylist['one_plan_date'])?></td>
        <td class="show_list one_plan_amount"><?php echo $paylist['one_plan_amount']?></td>
        <td class="show_list reality_date"><?php getTime($paylist['one_reality_date'])?></td>
        <td class="show_list one_reality_amount"><?php echo $paylist['one_reality_amount']?></td>
         <td class="show_list plan_date"><?php getTime($paylist['two_plan_date'])?></td>
        <td class="show_list two_plan_amount"><?php echo $paylist['two_plan_amount']?></td>
        <td class="show_list reality_date"><?php getTime($paylist['two_reality_date'])?></td>
        <td class="show_list two_reality_amount"><?php echo $paylist['two_reality_amount']?></td>
         <td class="show_list plan_date"><?php getTime($paylist['three_plan_date'])?></td>
        <td class="show_list three_plan_amount"><?php echo $paylist['three_plan_amount']?></td>
        <td class="show_list reality_date"><?php getTime($paylist['three_reality_date'])?></td>
        <td class="show_list three_reality_amount"><?php echo $paylist['three_reality_amount']?></td>
         <td class="show_list plan_date"><?php getTime($paylist['four_plan_date'])?></td>
        <td class="show_list four_plan_amount"><?php echo $paylist['four_plan_amount']?></td>
        <td class="show_list reality_date"><?php getTime($paylist['four_reality_date'])?></td>
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
        <td id="reality_pay_rmb"><?php echo $tot_reality_rmb ?></td>
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
<div id="container" style="width: 90%; height: 400px; margin: 0 auto"></div>
 <?php include "../footer.php"; ?>
</body>
</html>