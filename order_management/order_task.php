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
  $sqlwhere = "  AND `client_name` LIKE '%$client_name%' AND `mould_name` LIKE '%$mould_name%' AND `project_name` LIKE '%$project_name%' ORDER BY `task_time` DESC";
}

//sql语句
$sql = "SELECT * FROM `db_order_task` WHERE `task_status` = '1'".$sqlwhere;

$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `task_time` DESC" . $pages->limitsql;
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
  #main{table-layout:fixed;width:1350px;}
  #main tr td{word-wrap:break-word;word-break:break-all;}
  #main tr td input{width:100px;}
  #add_task,#del_task{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;padding-top:2px;margin-left:10px;}
  #save_task{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;}
</style>
<script type="text/javascript" charset="utf-8">
    $(function(){
	var new_task = '  <tr class="task">              <td class="show_list"><input type="text" name="" value="<?php echo date('Y-m-d',time()) ?>"></td>              <td class="show_list"><input type="text" name="customer_code[]" class="customer_codes"></td>              <td class="show_list">              	<select name="client_name[]" class="customer_names" style="width:100px">                		 <option value="0">--选择客户--</option>          	              			<?php foreach($customer_list as $k=>$v){?>              	              		<option value="<?php echo $v['customer_id']?>">              			<?php echo strstr($v['customer_name'],'$$')?substr($v['customer_name'],strrpos($v['customer_name'],'$$')+2):$v['customer_name'] ?>               		</option>              			              		<?php }?>                   	</select>              </td>         <td><input type="text" name="customer_order_no[]" /></td>                   <td class="show_list"><input type="text" name="project_name[]"></td>              <td class="show_list"><input type="text" name="mould_no[]"></td>              <td class="show_list"><input type="text" name="upload_final_path[]"/></td>              <td class="show_list"><input type="text" name="number[]" id="number" class="number"></td>              <td class="show_list"><input type="text" name="unit_price[]" id="unit_price" class="unit_price"></td>              <td class="show_list">              	<select name="currency[]" id="currency" style="width:100px;height:20px" class="currency">                		<?php foreach($array_currency as $k=>$v){
                			echo '<option value="'.$k.'">'.$v.'</option>';
                		}?>                	</select>              </td>              <td class="show_list"><input type="text" name="mold_rate[]" class="mold_rate" id="mold_rate" value="1"></td>              <td class="show_list"><input type="text" name="agreement_price[]" class="agreement_price" id="agreement_price"/></td>              <td class="show_list"><input type="text" name="deal_price[]" class="deal_price" id="deal_price"></td>          </tr>';
	var del_but = '&nbsp;<span id="del_task">撤 销</span>';
	//添加新临时任务
	$('#add_task').live('click',function(){
		$(this).parent().parent().before(new_task);
		var tr_num = $(this).parent().parent().prevAll().size();
		if(tr_num == 3){
			$(this).after(del_but);
		}
		
	})
	//撤除新临时任务
	$('#del_task').live('click',function(){
		$(this).parent().parent().prev('.task').remove();
		var tr_num = $(this).parent().parent().prevAll().size();
		if(tr_num <3){
			$(this).remove();
		}
	})
	//选择客户后自动获取客户代码
	$('.customer_names').live('change',function(){
		var num = $('.customer_names').index(this);
		var customer_id = $(this).val();
		$.post('../ajax_function/order_customer_code.php',{customer_id:customer_id},function(data){
			$('.customer_codes').eq(num).val(data);
		})
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
		var num = $('.currency').index(this);
		var currency = $('.currency').eq(num).val();
		//人民币汇率为 1
		if(currency.indexOf('rmb') != -1){
			$('.mold_rate').eq(num).val('1');
		} else {
			$('.mold_rate').eq(num).val(' ');
		}
	})
	//自动计算金额
	$(".unit_price,.number,.mold_rate,.currency").live('change',function(){
		var num = $(this).parent().parent().prevAll().size() -1;
		var number = $('.number').eq(num).val();
		var unit_price = $('.unit_price').eq(num).val();
		var mold_rate = $('.mold_rate').eq(num).val();
		var currency = $('.currency').eq(num).val();
		
		if(number && unit_price && mold_rate){
			var agreement_price = parseFloat(number * unit_price);
			agreement_price = agreement_price.toFixed(2);
			$('.agreement_price').eq(num).val(agreement_price);
			if(currency == 'rmb_vat'){
				var rmb_vat = parseFloat(number * unit_price * mold_rate/1.13);
				var rmb_without_vat = rmb_vat.toFixed(2);
				$('.deal_price').eq(num).val(rmb_without_vat);
			}else{
				var deal_price = parseFloat(number * unit_price * mold_rate);
				deal_price = deal_price.toFixed(2);
				$('.deal_price').eq(num).val(deal_price);
			}
		}
	})
    })
</script>
</head>

<body>
<?php include "header.php"; ?>

  <h4 style="padding-left:10px">
     
  </h4>

<div id="table_list">
  <form action="order_taskdo.php?action=add" name="list" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
      <tr>
        <th style="">日期</th>
        <th style="">客户代码</th>
        <th style="">客户名称</th>
        <th style="">客户订单编号</th>
        <th style="">项目名称</th>
        <th style="">模具编号</th>
        <th style="">任务内容</th>
        <th style="">数量</th>
        <th style="">单价</th>
        <th style="">币别</th>
        <th style="">汇率</th>
        <th style="">金额</th>
        <th style="">人民币未税价格</th>
     </tr>

     <tr class="task">
              <td class="show_list"><input type="text" name="" value="<?php echo date('Y-m-d',time()) ?>"></td>
              <td class="show_list"><input type="text" name="customer_code[]" class="customer_codes"></td>
              <td class="show_list">
              	<select name="client_name[]" class="customer_names" style="width:100px">  
              		 <option value="0">--选择客户--</option>          	
              			<?php foreach($customer_list as $k=>$v){?>              	
              		<option value="<?php echo $v['customer_id']?>">
              			<?php echo strstr($v['customer_name'],'$$')?substr($v['customer_name'],strrpos($v['customer_name'],'$$')+2):$v['customer_name'] ?> 
              		</option>              			
              		<?php }?>
                   	</select>
              </td>
              <td><input type="text" name="customer_order_no[]" /></td>
              <td class="show_list"><input type="text" name="project_name[]"></td>
              <td class="show_list"><input type="text" name="mould_no[]"></td>
              <td class="show_list"><input type="text" name="upload_final_path[]"/></td>
              <td class="show_list"><input type="text" name="number[]" class="number" id="number"></td>
              <td class="show_list"><input type="text" name="unit_price[]" class="unit_price" id="unit_price"></td>
              <td class="show_list">
              	<select name="currency[]" id="currency" class="currency" style="width:100px;height:20px">
                		<?php foreach($array_currency as $k=>$v){
                			echo '<option value="'.$k.'">'.$v.'</option>';
                		}?>
                	</select>
              </td>
              <td class="show_list"><input type="text" name="mold_rate[]" id="mold_rate" value="1" class="mold_rate"></td>
              <td class="show_list"><input type="text" name="agreement_price[]" id="agreement_price" class="agreement_price"/></td>
              <td class="show_list"><input type="text" name="deal_price[]" id="deal_price" class="deal_price"></td>
          </tr>
          <tr>
              <td colspan="13" style="align:center">
             	 <span id="add_task">新 建</span>
              	&nbsp;&nbsp;
              	<input id="save_task" type="submit" value="保 存" style="margin-top:5px;height:29px;width:80px">
              </td>
          </tr>
    </form>
  </table>
</div>
 <?php include "../footer.php"; ?>
</body>
</html>