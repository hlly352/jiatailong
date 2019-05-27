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
  #main tr td input{width:120px;}
  #add_task{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;padding-top:2px;}
  #add_task+input{width:80px;height:25px; display: inline-block;cursor:pointer;background-image: linear-gradient(#ddd, #bbb);border: 1px solid rgba(0,0,0,.2);border-radius: .3em;box-shadow: 0 1px white inset;text-align: center;line-height:25px;}
</style>
<script type="text/javascript" charset="utf-8">
    $(function(){
    	/*
	var new_task = ' <tr class="task">              <td class="show_list"><input type="text" name="task_time" value="<?php echo date('Y-m-d',time()) ?>"></td>              <td class="show_list"><select name="customer_name" class="customer_names" style="width:120px">    <option value="0">--选择客户--</option>          		<?php foreach($customer_list as $k=>$v){?>              		<option value="<?php echo $v['customer_id']?>"><?php echo strstr($v['customer_name'],'$$')?substr($v['customer_name'],strrpos($v['customer_name'],'$$')+2):$v['customer_name'] ?> </option>              			<?php }?>              	</select></td>              <td class="show_list"><input type="text" name="customer_code" class="customer_codes"></td>              <td class="show_list"><input type="text" name="mould_no"></td>              <td class="show_list"><input type="text" name="mould_name"></td>              <td class="show_list"><input type="text" name="size"></td>              <td class="show_list"><input type="text" name="material"></td>              <td class="show_list"><input type="text" name="number"></td>              <td class="show_list"><input type="text" name="unit_price"></td>              <td class="show_list"><input type="text" name="price"></td>              <td class="show_list"><input type="text" name="notes"></td>          </tr>';
	var is_add = true;
	$('#add_task').live('click',function(){
		if(is_add == true){
			$(this).parent().parent().before(new_task);
			$(this).text('撤销');
			is_add = false;
		}else{
			$(this).parent().parent().prev('.task').remove();
			$(this).text('新建临时任务');
			is_add = true;
		}
	})
	//自动计算金额
	$('input[name=number],input[name=unit_price]').live('change',function(){
		var number = parseInt($.trim($('input[name=number]').val()));
		var unit_price = parseInt($.trim($('input[name=unit_price]').val()));
		if(number && unit_price){
			var price = parseInt(number*unit_price);
		}
		$('input[name=price]').val(price);
	})*/
	//选择客户后自动获取客户代码
	$('.customer_names').live('change',function(){
		var customer_id = $(this).val();
		$.post('../ajax_function/order_customer_code.php',{customer_id:customer_id},function(data){
			$('.customer_codes').val(data);
		})
	})
	//点击保存时验证数据
	$('input:submit').live('click',function(){
	    //客户名称
	   var customer_name = $.trim($(this).parent().parent().prev('.task').children().children('.customer_names').val());
	    if(customer_name =='0'){
	      alert('请选择客户');
	      $(this).parent().parent().prev('.task').children().children('.customer_names').focus();
	      return false;
	    }


		
		//单价
		var unit_price = $.trim($(this).parent().parent().prev('.task').children().children('input[name=unit_price]').val());

		if(!unit_price){
			alert('请输入单价');
			$('input[name=unit_price]').focus();
			return false;
		}else{
			var info = /\d+/.test(unit_price);
			if(!info){
				alert('请输入数字');
				$('input[name=unit_price]').focus();
				return false;
			}
		}
		//数量
		var number = $.trim($(this).parent().parent().prev('.task').children().children('input[name=number]').val());
		if(!number){
			alert('请输入数量');
			$('input[name=number]').focus();
			return false;
		}else{
			var infos = /\d+/.test(number);
			if(!infos){
				alert('请输入数字');
				$('input[name=number]').focus();
				return false;
			}
		}
	
		//金额
		var price = $(this).parent().parent().prev('.task').children().children('input[name=deal_price]').val();

		if(!price){
			alert('请输入金额');
			$('input[name=deal_price]').focus();
			return false;
		}else{
			var info = /\d+/.test(price);
			if(!info){
				alert('请输入数字');
				$('input[name=deal_price]').focus();
				return false;
			}
		}	
	})
	//自动计算金额
	$("#unit_price,#number,#mold_rate,#currency").live('change',function(){
		var number = $('#number').val();
		var unit_price = $('#unit_price').val();
		var mold_rate = $('#mold_rate').val();
		var currency = $('#currency').val();
		if(number && unit_price && mold_rate){
			$('#agreement_price').val(parseInt(parseInt(number) * parseInt(unit_price)));
			if(currency == 'rmb_vat'){
				var rmb_vat = parseFloat(parseInt(number) * parseInt(unit_price) * parseInt(mold_rate));
				var rmb_without_vat = parseInt(rmb_vat/1.13);
				$('#deal_price').val(rmb_without_vat);
			}else{
				$('#deal_price').val(parseInt(parseInt(number) * parseInt(unit_price) * parseInt(mold_rate)));
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
              <td class="show_list"><input type="text" name="customer_code" class="customer_codes"></td>
              <td class="show_list">
              	<select name="client_name" class="customer_names" style="width:120px">  
              		 <option value="0">--选择客户--</option>          	
              			<?php foreach($customer_list as $k=>$v){?>              	
              		<option value="<?php echo $v['customer_id']?>">
              			<?php echo strstr($v['customer_name'],'$$')?substr($v['customer_name'],strrpos($v['customer_name'],'$$')+2):$v['customer_name'] ?> 
              		</option>              			
              		<?php }?>
                   	</select>
              </td>
              
              <td class="show_list"><input type="text" name="project_name"></td>
              <td class="show_list"><input type="text" name="mold_id"></td>
              <td class="show_list"><input type="text" name="upload_final_path"/></td>
              <td class="show_list"><input type="text" name="number" id="number"></td>
              <td class="show_list"><input type="text" name="unit_price" id="unit_price"></td>
              <td class="show_list">
              	<select name="currency" id="currency" style="width:120px;height:20px">
                		<?php foreach($array_currency as $k=>$v){
                			echo '<option value="'.$k.'">'.$v.'</option>';
                		}?>
                	</select>
              </td>
              <td class="show_list"><input type="text" name="mold_rate" id="mold_rate"></td>
              <td class="show_list"><input type="text" name="agreement_price" id="agreement_price"/></td>
              <td class="show_list"><input type="text" name="deal_price" id="deal_price"></td>
          </tr>
          <tr>
              <td colspan="12" style="align:center">
              	<input type="submit" value="保存" style="margin-top:5px;height:29px;width:80px">
              </td>
          </tr>
    </form>
  </table>
</div>
 <?php include "../footer.php"; ?>
</body>
</html>