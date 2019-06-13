<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';

$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查找客户信息
$mould_sql ="SELECT * FROM `db_mould_data` INNER JOIN `db_customer_info` ON `db_mould_data`.`client_name`=`db_customer_info`.`customer_id` WHERE `db_mould_data`.`mould_dataid`= {$_GET['mould_id']}";

$res = $db->query($mould_sql);
if($res->num_rows){
	$mouldinfo = [];
	while($mould = $res->fetch_assoc()){
		$mouldinfo = $mould; 
	}
}

      //获取图片地址
          $src = $mouldinfo['upload_final_path'];
          $src = $src?strstr($src,'$$')?substr($src,strpos($src,'$$')+2):$src:' ';
//查看当前用户是否是管理员
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
<script type="text/javascript" charset="utf-8">
	$(function(){
	//自动计算价税合计
	if($('.currency').val() == 'rmb'){
			var total_rmb = (parseFloat($('.agreement_price').val()) + parseFloat($('.order_vat').val())).toFixed(2);
			$('.order_total_rmb').val(total_rmb);
		} else {
			var total_rmb = parseFloat($('.agreement_price').val()).toFixed(2);
			$('.order_total_rmb').val(total_rmb);
		}
	//选择客户后自动获取客户代码
	$('.customer_names').live('change',function(){
		var num = $('.customer_names').index(this);
		var customer_id = $(this).val();
		$.post('../ajax_function/order_customer_code.php',{customer_id:customer_id},function(data){
			$('.customer_codes').eq(num).val(data);
		})
	})
	 //审批订单
	$('#order_approval').click(function(){
	  	//获取订单id
	  	var url = window.location.search;
	  	if(url.indexOf('?') != -1){
	  		var str = url.substr(1);
	  		var strs = str.split('&');
	  		var obj= new Object();
	  		for(k in strs){
	  			obj[strs[k].split('=')[0]] = strs[k].split('=')[1];
	  		}
	  	}
	  	//跳转到处理审核页面
	  	window.open('order_taskdo.php?action=order_approval&mould_id='+obj.mould_id,'_self');
	  })
	//点击保存时验证数据
	$('input:submit').live('click',function(){
	    //客户名称
	    var num = $('.customer_names').size();
	    for(var i=0;i<num;i++){
	    	var customer_name = $.trim($('.customer_names').eq(i).val());
	    	if(customer_name =='0'){
	     	 alert('请选择客户');
	     	 $('.customer_names').eq(i).focus();
	    	  return false;
	    }

	    }
	
	 
	//数量
	var number_num = $('.number').size();
	for(var i=0;i<number_num;i++){
			var number = $.trim($('.number').eq(i).val());
		if(!number){
			alert('请输入数量');
			$('.number').eq(i).focus();
			return false;
		}else{
			var infos = /\d+/.test(number);
			if(!infos){
				alert('请输入数字');
				$('.number').eq(i).focus();
				return false;
			}
		}
	}
	//单价
	var unit_price_num = $('.unit_price').size();
	for(var i=0;i<unit_price_num;i++){
			var unit_price = $.trim($('.unit_price').eq(i).val());
		if(!unit_price){
			alert('请输入单价');
			$('.unit_price').eq(i).focus();
			return false;
		}else{
			var infos = /\d+/.test(unit_price);
			if(!infos){
				alert('请输入数字');
				$('.unit_price').eq(i).focus();
				return false;
			}
		}
	}
	   
	//金额
	var deal_price_num = $('.deal_price').size();
	for(var i=0;i<deal_price_num;i++){
			var deal_price = $.trim($('.deal_price').eq(i).val());
		if(!deal_price){
			alert('请输入金额');
			$('.deal_price').eq(i).focus();
			return false;
		}else{
			var infos = /\d+/.test(deal_price);
			if(!infos){
				alert('请输入数字');
				$('.deal_price').eq(i).focus();
				return false;
			}
		}
	}
		
	
		
			
	})
	//自动切换汇率
	$('.currency').live('change',function(){
		var currency = $(this).val();
		//人民币汇率为 1
		if(currency.indexOf('rmb') != -1){
			$('.mold_rate').val('1');
		} else {
			$('.mold_rate').val(' ');
		}
	})
	//自动计算金额
	$(".unit_price,.number,.mold_rate,.currency").live('change',function(){
		var number = $('.number').val();
		var unit_price = $('.unit_price').val();
		var mold_rate = $('.mold_rate').val();
		var currency = $('.currency').val();
		
		if(number && unit_price && mold_rate){
			var agreement_price = parseFloat(number * unit_price);
			agreement_price = agreement_price.toFixed(2);
			$('.agreement_price').val(agreement_price);
			if(currency == 'rmb_vat'){
				var rmb_vat = parseFloat(number * unit_price * mold_rate/1.13);
				var rmb_without_vat = rmb_vat.toFixed(2);
				$('.deal_price').val(rmb_without_vat);
	       //计算税金
       	 var order_vat = parseFloat(number * unit_price * mold_rate / 1.13 * 0.13);
 
	      }else{
	        var deal_price = parseFloat(number * unit_price * mold_rate);
	        deal_price = deal_price.toFixed(2);
	        $('.deal_price').val(deal_price);
	        //判断是否是人民币未税
	          if(currency == 'rmb'){
	            var order_vat = parseFloat(number * unit_price * mold_rate * 0.13);
	          } else {
	            var order_vat = 0;
	          }
	   
	      }
	      //格式化税金
	      order_vat = order_vat.toFixed(2);
	      $('.order_vat').val(order_vat);
	      //计算价税合计
	      
	      if(currency == 'rmb'){

	        var order_total_rmb = parseFloat($('.deal_price').val()) + parseFloat(order_vat);
	      } else {
	        var order_total_rmb = parseFloat($('.agreement_price').val()) * mold_rate;
	      }
	      //格式化价税合计
	 
	      order_total_rmb = parseFloat(order_total_rmb).toFixed(2);
	      $('.order_total_rmb').val(order_total_rmb);
	    }
		})
	})
</script>
<title>订单管理-嘉泰隆</title>
<style type="text/css">
  #main{table-layout:fixed;width:1350px;}
  #main tr td{word-wrap:break-word;word-break:break-all;}
  #main tr td input{width:90%;}
  #order_approval,#back,#no_approval{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;padding-top:2px;margin-left:10px;}
  #no_approval{background:grey;}
  #edit{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;}
</style>
</head>

<body>
<?php include "header.php"; ?>

  <h4 style="padding-left:10px">
     
  </h4>

<div id="table_list">
  <form action="order_taskdo.php?action=order_approval_edit" name="list" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
      <tr>
        <th style="" rowspan="2">日期</th>
        <th style="" rowspan="2">客户代码</th>
        <th style="" rowspan="2">客户名称</th>
        <th style="" rowspan="2">项目名称</th>
        <th style="" rowspan="2">模具编号</th>
        <th style="" rowspan="2">零件名称</th>
        <th style="" rowspan="2">图片/内容</th>
        <th style="" colspan="5">合同内容</th>
        <th style="" colspan="3">人民币计价</th>
      </tr>
      <tr>
        <th style="">数量</th>
        <th style="">单价</th>
        <th style="">币别</th>
        <th style="">汇率</th>
        <th style="">金额</th>
        <th style="">人民币未税价格</th>
        <th style="">税金</th>
        <th style="">价税合计</th>
     </tr>

     <tr class="task">
              <td class="show_list"><?php echo date('Y-m',$mouldinfo['deal_time'])?></td>
              <td class="show_list"><?php echo strstr($mouldinfo['customer_code'],'$$')?substr($mouldinfo['customer_code'],strrpos($mouldinfo['customer_code'],'$$')+2):$mouldinfo['customer_code']?></td>
              <td class="show_list"><?php echo strstr($mouldinfo['customer_name'],'$$')?substr($mouldinfo['customer_name'],strrpos($mouldinfo['customer_name'],'$$')+2):$mouldinfo['customer_name']?></td>
              
              <td class="show_list"><?php echo $mouldinfo['project_name']?></td>
              <td class="show_list"><?php echo $mouldinfo['mould_no']?></td>
              <td class="show_list"><?php echo $mouldinfo['mould_name']?></td>
              <td class="show_list"><?php echo strstr($src,'/')?'<img src='.$src.' width="50" height="35"/>': $src ?></td>
              <td class="show_list"><input type="text" name="number" class="number" id="number" value="<?php echo $mouldinfo['number'] ?>"></td>
              <td class="show_list"><input type="text" name="unit_price" class="unit_price" id="unit_price" value="<?php echo $mouldinfo['unit_price'] ?>"></td>
              <td class="show_list">
              	<select name="currency" id="currency" class="currency" style="width:90%;height:20px">
                		<?php foreach($array_currency as $k=>$v){ ?>
                			<option <?php echo $k==$mouldinfo['currency']?'selected':' ' ?> value="<?php echo $k ?>"><?php echo $v ?></option>
                		<?php }?>
                	</select>
              </td>
              <td class="show_list"><input type="text" name="mold_rate" id="mold_rate" value="<?php echo $mouldinfo['mold_rate'] ?>" class="mold_rate"></td>
              <td class="show_list"><input type="text" name="agreement_price" id="agreement_price" value="<?php echo $mouldinfo['agreement_price'] ?>" class="agreement_price"/></td>
              <td class="show_list"><input type="text" name="deal_price" value="<?php echo $mouldinfo['deal_price'] ?>" id="deal_price" class="deal_price"></td>
              <input type="hidden" name="mould_id" value="<?php echo $_GET['mould_id'] ?>">
              <td class="show_list"><input type="text" class="order_vat" id="order_vat" name="order_vat" value="<?php echo $mouldinfo['order_vat']?$mouldinfo['order_vat']:0 ?>"></td>
              <td  class="show_list"><input type="text" class="order_total_rmb"></td>
          </tr>
          <tr>
              <td colspan="15" style="align:center">
             	 
              	<input id="edit" type="submit" value="保 存" style="margin-top:5px;height:29px;width:80px">

              	<span id="<?php echo $system_info[0]=='1'?'order_approval':'no_approval' ?>">审 核</span>
             
              	<span id="back" onclick="window.history.go(-1)">返 回</span>
              </td>
          </tr>
    </form>
  </table>
</div>
 <?php include "../footer.php"; ?>
</body>
</html>