<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);

$employeeid = $_SESSION['employee_info']['employeeid'];
//通过状态id 查询客户状态信息
$customer_status_id = $_GET['id'];
$customer_sql = "SELECT * FROM `db_customer_info` as a INNER JOIN `db_customer_status` as b ON a.`customer_id` = b.`customer_id` WHERE b.`customer_id` =(SELECT `customer_id` FROM `db_customer_status` WHERE `customer_status_id` = '$customer_status_id') ORDER BY b.add_time ASC";
$result = $db->query($customer_sql);
if($result ->num_rows){
	$array_customer = array();
	while($res = $result->fetch_assoc()){
		$array_customer[] = $res;
	}
}
//查找市场部和总经办的人员
$min_boss_sql = "SELECT `employee_name`,`employeeid`,`deptid` FROM `db_employee` WHERE `deptid` =1 AND `employee_status` = '1' OR `deptid` = 2 AND `employee_status` = '1'";

$min_boss = $db->query($min_boss_sql);
$employees = [];
if($min_boss->num_rows){
	while($row = $min_boss->fetch_assoc()){
		$employees[] = $row;
 	}
}
//查找总经办的人员
$boss_sql = "SELECT `employee_name`,`employeeid`,`deptid` FROM `db_employee` WHERE `deptid` =1 AND `employee_status` = '1'";

$boss = $db->query($boss_sql);
$employeess = [];
if($boss->num_rows){
  while($row = $boss->fetch_assoc()){
    $employeess[] = $row;
  }
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script langeage="javascript" type="text/javascript" src="../js/add_customer.js"></script>
<script language="javascript" type="text/javascript">
	//获取当前时间
	function getNowDate() {
		var date = new Date();
		var seperator1 = "-";
		var seperator2 = ":";
		var month = date.getMonth() + 1<10? "0"+(date.getMonth() + 1):date.getMonth() + 1;
		var strDate = date.getDate()<10? "0" + date.getDate():date.getDate();
		var currentdate = date.getFullYear() + seperator1  + month  + seperator1  + strDate;
		return currentdate;
	}
 	 nowDate = getNowDate();
	//获取当前页面的客户信息,组成数组
	function getCurrentInfo(key,new_key,classname){
		//获取数目
		var current_val = new Array();
		if(key == 'customer_grade'){
			var num = $('.grades').size();
		} else if(key == 'boss_names'){
			var num = $('.boss_names').size();
		} else{
			var num = $('input[name ^= '+key+']').size();
		}
		//获取所有的值
		for(var i=0;i<num;i++){
			 if(key == 'customer_grade') {

				 current_val.push($('.grades').eq(i).val());
			} else if(key == 'boss_names'){
				 current_val.push($.trim($('.boss_names').eq(i).val()));
			} else {
				current_val.push($.trim($('input[name ^= '+key+']').eq(i).val()));
			}
			
		}
		//把对应的值添加到下拉框中
		var sel = $('<select name ^="'+new_key+'"></select>');
		for(k in current_val){
				var opt = '<option selected value="'+current_val[k]+'">'+current_val[k]+'</option>';
			if(current_val[k]){	
			sel.append(opt);
			}
		}
		var index = $(classname).size()-1;
		if(sel.children().length !=0){
			$(classname).eq(index).append(sel);
		}
	}
	$(function(){
		$('tr').removeClass('even');

		//新增联系人
		var add_contacts= '<tr>	  	  	 <th>姓名：</th>	     	   	<td width="">	      	   		<input teyp="text" name="contacts_name[]" />	     	      <span  class="del del_contacts">删除</span> 	   </td>		  	</tr>	  	<tr>	  		 <th>所属公司：</th>	     		 <td>	      			<input type="text" name="contacts_company[]" />	     		 </td>		  	</tr>	  	<tr>	  		 <th>职务：</th>	     		 <td>	      			<input type="text" name="contacts_work[]" />	     		 </td>	  	</tr>	  		  	<tr>			<th>电话/手机：</th>			<td>			     <input type="text" name="contacts_phone[]" />			</td>	  	</tr>	  	<tr>	  		 <th>邮箱：</th>	      		<td>	      			<input type="text" name="contacts_email[]" />	      		</td>		  	</tr>	  	<tr>	  		  <th>备注：</th>			  <td>			      <input type="text" name="contacts_note[]" />			  </td>	  	</tr>';
		var add_company ='<tr>		      <th width="">代码：</th>		      <td style="width:70px">		      	<input type="text" name="customer_code[]" style="width:70px" readonly/>		      </td>		      <td style="width:40px">等级：</td>		      <td>		      	<select name="customer_grade[]" class="grades" style="height:30px;width:70px">		      		<?php foreach($array_customer_grade as $k=>$v){ 
		      			echo '<option value="'.$v.'">'.$v.'</option>';
		      			}
		      			?>		      	</select>	<span  class="del del_company">删除</span> 	   			      </td>		</tr>		<tr>		      <th width="">名称：</th>		      <td width="" colspan="3">		      	<input type="text" name="customer_name[]" />		      </td>		</tr>		<tr>		      <th>主营业务：</th>		      <td colspan="3">		      	<input type="text" name="customer_business[]" />		      </td>		</tr>		<tr>		      <th>网址：</th>		      <td colspan="3">		      	<input type="text" name="customer_url[]" />		      </td>		</tr>		<tr>		      <th>地址：</th>		      <td colspan="3">		      	<input type="text" name="customer_address[]" />		      </td>		</tr>		<tr class="last_tr">		      <th>邮编：</th>		      <td class="post" colspan="3">		      	<input type="text" name ="customer_post[]" />		      </td>	    	</tr>	';


	//动态添加联系人
	$('#add_contacts').live('click',function(){
		$(this).parent().parent().before(add_contacts);
		
	})
	//动态添加分公司
	$('#add_company').live('click',function(){
		$(this).parent().parent().before(add_company);
	})
	//动态删除联系人
	$('.del_contacts').live('click',function(){
		$(this).parent().parent().nextAll().slice(0,6).remove();
		$(this).parent().parent().remove();
		$(this).remove();
	})
	//动态删除分公司信息
	$('.del_company').live('click',function(){
		$(this).parent().parent().nextAll().slice(0,7).remove();
		$(this).parent().parent().remove();
		$(this).remove();
	})
	//负责人信息默认不能更改
	$('.customer_boss').children().prop('disabled',true);
	//动态添加负责人信息
	$('#add_boss').live('click',function(){
		$('.customer_boss').eq(0).children().prop('disabled',false);
		$('.customer_boss').eq(1).children().prop('disabled',false);
		$('.customer_boss').eq(2).children().prop('disabled',false);
		
		var customer_boss = $('.customer_boss').children().eq(0).val();
		var min_boss = $('.customer_boss').children().eq(1).val();
		var boss_unit = $('.customer_boss').children().eq(2).val();
		var old_boss = '<tr class="boss_val">	  	 	<th width="" >总负责人：</th>	   		 <td>			      	<input type="text" name="boss_name[]" value="'+customer_boss+'" readonly style="color:grey"  />			 </td>		  	</tr>		<tr class="boss_val">		    	 <th width="">负责人：</th>			 <td>			<input type="text" name="min_boss[]" value="'+min_boss+'" readonly style="color:grey"  class="" />		 </td>	   	 </tr>	  	<tr class="boss_val">	  		 <th width="">所属部门：</th>			 <td class="customer_boss">			      <input type="text" id="boss_unit" name="boss_unit[]" readonly style="color:grey"  value="'+boss_unit+'" />			 </td>	  	</tr>  ';
		if($('.boss_val').length == 0){
			$(this).parent().parent().after(old_boss);
			$('.customer_boss').eq(0).children().val(' ');
			$('.customer_boss').eq(1).children().val(' ');
			$('.customer_boss').eq(2).children().val(' ');
		}

	})
	//添加客户信息自动添加信息到状态跟进表中
	
	$('input[name ^= customer_name]').live('blur',function(){
		$('input[name ^= status_customer]').val($(this).val());
	})
	$('input[name ^=contacts_name]').live('blur',function(){
		$('input[name ^= status_contacts]').val($(this).val());
	})
	$('input[name ^=min_boss]').live('blur',function(){
		$('input[name = status_boss]').val($(this).val());
	})
	$('input[name = status_time]').val(nowDate);
	
	//添加客户状态信息
	
	//动态添加客户状态
	$('#add_status').live('click',function(){
		//获取客户基本信息
		var  customer_status = '<tr class="status_val">	 		<td>	 			<input type="text" name="status_time[]" value="'+nowDate+'">	 		</td>	 <td class="new_status status_codes">					</td><td class="new_status status_grades">	 		    	</td>		<td class="status_names new_status" >		 		</td>		 		<td class="new_status status_contactss"> 		</td>	<td class="new_status status_phones">	 			</td> 		<td class="new_status status_bosss">	 				</td>	 		<td>	 			<input type="text" name="status_goal[]">	 		</td>	 		<td>	 			<input type="text" name="status_result[]" >	 		</td>	 		<td>	 			<input type="text" name="status_plan[]"  >	 		</td>	 		<td>	 			<input type="text" name="status_note[]" >	 		</td>		</tr>';
	

		$(this).parent().parent().before(customer_status);
		getCurrentInfo('customer_code','status_code','.status_codes');
		getCurrentInfo('customer_grade','status_grade','.status_grades');
		getCurrentInfo('customer_name','status_customer','.status_names');
		getCurrentInfo('contacts_name','status_contacts','.status_contactss');
		getCurrentInfo('contacts_phone','status_phone','.status_phones');
		getCurrentInfo('boss_names','status_boss','.status_bosss');

	})
	
	//根据权限决定是否可以修改
	if($('#authority').val() != '1'){
	
		$('.old_data').children().children().children().children().attr('readonly',true);
		$('.old_data').css({'border':'none','outline':'none'})
	}
	//撤销更换负责人
	$('#cancel_boss').live('click',function(){
		$('.customer_boss').children().prop('disabled',true);
		//把值填回到原来的输入框中
		var customer_boss = $('.boss_val').eq(0).children().children('input').val();
		var min_boss = $('.boss_val').eq(1).children().children('input').val();
		var boss_unit = $('.boss_val').eq(2).children().children('input').val();
		if(customer_boss != null){
			$('.customer_boss').eq(0).children().val(customer_boss);
		}
		if(min_boss != null){
			$('.customer_boss').children().eq(1).val(min_boss);
		}
		if(boss_unit != null){
	 		$('.customer_boss').children().eq(2).val(boss_unit);
	 	}
		$('.boss_val').remove();
	})
	//撤销状态
	$('#cancel_status').live('click',function(){
		if($('.status_val').length >0){
			$('.status_val:last').remove();
		}
	})
	//提交信息的时候判断所需内容是否为空
	$('button').click(function(){
		var num = $('input[name ^= customer_name]').size();
		var contacts_num = $('input[name ^= contacts_name]').size();
		for(var i=0;i<num;i++){
			var name = $('input[name ^= customer_name]').eq(i).val();
			if(!$.trim(name)){
				alert('客户名称不能为空');
				$('input[name ^= customer_name]').eq(i).focus();
				return false;
			}
			var address = $('input[name ^= customer_address]').eq(i).val();
			if(!$.trim(address)){
				alert('地址不能为空');
				$('input[name ^= customer_address]').eq(i).focus();
				return false;
			}
			
		}
		for(var j=0;j<contacts_num;j++){
			var name = $('input[name ^= contacts_name]').eq(j).val();
			if(!$.trim(name)){
				alert('联系人姓名不能为空');
				$('input[name ^= contacts_name]').eq(j).focus();
				return false;
			}
			var phone = $('input[name ^= contacts_phone]').eq(j).val();
			var tel = $('input[name ^= contacts_tel]').eq(j).val();
			var email = $('input[name ^= contacts_email]').eq(j).val();
			if((!$.trim(phone)) && (!$.trim(tel)) && (!$.trim(email))){
				alert('联系人电话/手机,邮箱至少填写一项');
				if(!$.trim(phone)){
					$('input[name ^= contacts_phone]').eq(j).focus();
				} else if(!$.trim(tel)){
					$('input[name ^= contacts_tel]').eq(j).focus();
				} else {
					$('input[name ^= contacts_email]').eq(j).focus();
				}
				return false;
			}
		}
		var boss_val = $('.current_boss').eq(0).children().val();
		if(boss_val == 0){
			alert('请选择负责人');
			$(".current_boss").eq(0).children().focus();
			return false;
			}
		//判断是否添加了状态
		if($('table:has(.status_val)').length    >0){
		//判断跟进状态的必需字段不能为空
		var goal = $('input[name ^= status_goal]').val();
		if(!$.trim(goal)){
			alert('跟进目的不能为空');
			$('input[name ^= status_goal]').focus();
			return false;
		}
		var result = $('input[name ^= status_result]').val()
		if(!$.trim(result)){
			alert('跟进效果不能为空');
			$('input[name ^= status_result]').focus();
			return false;
		}
		var plan = $('input[name ^= status_plan]').val();
		if(!$.trim(plan)){
			alert('下步计划不能为空');
			$('input[name ^= status_plan]').focus();
			return false;
		}

	}	

			$('.customer_boss').children().prop('disabled',false);
	
		
	})
	//选择负责人后自动获取部门
	$('#min_boss').bind('change',function(){

		var boss_val = $(this).val();
		$.post("../ajax_function/boss_dept.php",

		{boss_val:boss_val}, function(data,status){

			var depts = data.split('##');
			$('#boss_unit').val(depts[0]);
			$('#status_boss').val(depts[1]);

		});
	})


	})
</script>
<style type="text/css" media="screen">
	form{background:white;margin-left:-10px;}
	table tr td,table tr th{text-align:left;}
	table tr th{width:110px;}
	table:not(#customer_status) input{width:200px;height:25px;}
	.del{display:inline-block;width:40px;height:23px;background:#eee;text-align:center;line-height:23px;font-size:13px;cursor:pointer;}
	#save{clear:both;width:100%;height:100px;text-align:center;margin-top:20px;}
	#save button{width:180px;height:40px;cursor:pointer;margin-top:40px;}
	#customer{table-layout:fixed;}
	#customer_status tr td{border:1px solid #ddd;}
	#customer_status tr td{height:20px;width:100px;word-wrap:break-word;word-break:break-all}
	#customer_status tr th{text-align:center;}
	#customer_status tr td input{width:110px;}
	.new_status select{width:110px;}
</style>
<title>客户管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == 'show'){
  	//查找当前登录用户权限
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
	$system_sql = "SELECT `isconfirm`,`isadmin` FROM `db_system_employee` WHERE `employeeid`='$employeeid' AND `systemid`=".$system_id;
	$system_res = $db->query($system_sql);

	$system_info = [];
	while($system_admin = $system_res->fetch_assoc()){
		$system_info = $system_admin;
	}
	
	  $contacts_res = [$array_customer[0]['contacts_name'],$array_customer[0]['contacts_company'],$array_customer[0]['contacts_work'],$array_customer[0]['contacts_phone'],$array_customer[0]['contacts_email'],$array_customer[0]['contacts_note']];

	  $contacts = getdata($contacts_res);
	  //获取客户的公司信息
	  $customer_res = [$array_customer[0]['customer_code'],$array_customer[0]['customer_grade'],$array_customer[0]['customer_name'],$array_customer[0]['customer_business'],$array_customer[0]['customer_url'],$array_customer[0]['customer_address'],$array_customer[0]['customer_post']];

	  $customer = getdata($customer_res);
	  //获取负责人信息
	  $customer_res = array($array_customer[0]['boss_name'],$array_customer[0]['min_boss'],$array_customer[0]['boss_unit']);
	  
	  $boss = getdata($customer_res);
	
	
  ?>
  <input type="hidden" style="color:red" id="authority" value="<?php echo $system_info['isadmin'] ?>" >
  <h4 style="background:#eee">客户信息</h4>
  <form action="customer_datado.php" method="post" >
  	<input type="hidden" name="customer_id" value="<?php echo  $array_customer[0]['customer_id'] ?>">
  	
 <div style="width:1210px;margin:0px auto">
	  <table style="width:400px;float:left" class="old_data">
	  	<tr>
	  		<td colspan="4" style="text-align:center">客户信息</td>
	  	</tr>
	  	<?php foreach($customer as $k=>$v){ ?>
	  
	  	<tr>
		      <th width="">代码：</th>
		      <td style="width:70px">
		      	<input type="text" name="customer_code[]" style="width:70px" value="<?php echo $v[0] ?>"/>
		      </td>
		      <td style="width:40px">等级：</td>
		      <td>
		      	<select name="customer_grade[]" class="grades" style="height:30px;width:70px">
		      		<?php foreach($array_customer_grade as $ks=>$vs){ ?>
		      			<option <?php echo $v[1]==$vs?"selected":" " ?> value="<?php echo $vs ?>"><?php echo $vs ?></option>
		      			
		      			<?php }?>

		      	</select>
		      	
		      </td>
		</tr>
		<tr>
		      <th width="">名称：</th>
		      <td width="" colspan="3">
		      	<input type="text" name="customer_name[]" value="<?php echo $v[2] ?>" />
		      </td>
		</tr>
		<tr>
		      <th>主营业务：</th>
		      <td colspan="3">
		      	<input type="text" name="customer_business[]" value="<?php echo $v[3] ?>"/>
		      </td>
		</tr>
		<tr>
		      <th>网址：</th>
		      <td colspan="3">
		      	<input type="text" name="customer_url[]" value="<?php echo $v[4] ?>"/>
		      </td>
		</tr>
		<tr>
		      <th>地址：</th>
		      <td colspan="3">
		      	<input type="text" name="customer_address[]" value="<?php echo $v[5] ?>" />
		      </td>
		</tr>
		<tr class="last_tr">
		      <th>邮编：</th>
		      <td class="post" colspan="3">
		      	<input type="text" name ="customer_post[]" value="<?php echo $v[6] ?>" />
		      </td>
	    	</tr>
	    	<?php }?>
	    	<tr>
	    		<td colspan="4" style="text-align:center">
	    			<p id="add_company" style="width:100px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">新增分公司</p>
	    		</td>
	    	</tr>
	  </table>
	  <table style="width:400px;float:left;background:rgb(240,243,247)" class="old_data">
	  	<tr>
	  		<td colspan="2" style="text-align:center">联系人信息</td>
	  	</tr>
	  	<?php foreach($contacts as $k=>$v){ ?>
	  	<tr>
	  	  	 <th>姓名：</th>
	     	   	<td width="">
	      	   		<input teyp="text" name="contacts_name[]" value="<?php echo $v[0] ?>" />
	     	 	   </td>	
	  	</tr>
	  	<tr>
	  		 <th>所属公司：</th>
	     		 <td>
	      			<input type="text" name="contacts_company[]" value="<?php echo $v[1] ?>" />
	     		 </td>	
	  	</tr>
	  	<tr>
	  		 <th>职务：</th>
	     		 <td>
	      			<input type="text" name="contacts_work[]" value="<?php echo $v[2] ?>"/>
	     		 </td>	
	  	</tr>
	  	
	  	<tr>
			<th>电话/手机：</th>
			<td>
			     <input type="text" name="contacts_phone[]" value="<?php echo $v[3] ?>"/>
			</td>
	  	</tr>
	  	<tr>
	  		 <th>邮箱：</th>
	      		<td>
	      			<input type="text" name="contacts_email[]" value="<?php echo $v[4] ?>" />
	      		</td>	
	  	</tr>
	  	<tr>
	  		  <th>备注：</th>
			  <td>
			      <input type="text" name="contacts_note[]" value="<?php echo $v[5] ?>" />
			  </td>
	  	</tr>
	  	<?php } ?>
	  	<tr>
	  		<td colspan="2" style="text-align:center">
	  			<p id="add_contacts" style="width:100px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">新增联系人</p>
	  		</td>
	  	</tr>
	  </table>
	  <table style="width:400px;float:left">
	  	<tr>
	  		<td colspan="2" style='text-align:center'>
	  			负责人信息
	  		</td>
	  	</tr>	
	  	<?php  foreach($boss as $k=>$v){ ?>
	  	<tr>
	  	 	<th width="" >总负责人：</th>
	   		 <td width="" class="customer_boss">
			
		              <select name="boss_name[]" id="" style="width:200px;height:30px">
		                 <?php foreach($employeess as $ks=>$vs){ ?>
		              	<option <?php echo $v[0]==$vs['employee_name']?'selected':' ' ?> value="<?php echo $vs['employee_name'] ?>"><?php echo $vs['employee_name'] ?></option>
		           	<?php     } ?>
		            </select>
     		  	</td>      	
	  	</tr>
		<tr>
		    	 <th width="">负责人：</th>
			 <td class="customer_boss">
			      <select name="min_boss[]" id="min_boss" class="boss_names" style="width:200px;height:30px">
			      		<option value="0">--请选择--</option>
			      	<?php foreach($employees as $ks=>$vs){ ?>
			      		<option <?php echo $vs['employee_name']==$v[1]?"selected":"" ?> value="<?php echo $vs['employee_name'] ?>" ><?php echo $vs['employee_name'] ?></option>
			      		<?php } ?>
			      </select>
			 </td>
	   	 </tr>
	  	<tr>
	  		 <th width="">所属部门：</th>
			 <td class="customer_boss">
			      <input type="text" id="boss_unit" name="boss_unit[]" value="<?php echo $v[2] ?>" />
			 </td>
	  	</tr>
	  	<?php 

	  		if($system_info['isadmin'] == '1' && $k == 0){

	  	 ?>
	  	<tr>

	  		<td colspan="2" style="text-align:center">
	  			<p id="add_boss" style="width:80px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">负责人更换</p>
	  			<p id="cancel_boss" style="width:60px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">撤销</p>
	  		</td>
	  	</tr>
	  	<?php } }?>
	  </table>
	 </div>
<div style="clear:both"></div>
<h4 style="background:#eee;width:100%;position:relative;left:0px;top:20px;">状态履历</h4>

<div>
	<table id="customer_status" style="width:95%;margin:40px auto;">

		<tr>
	 		<th>时间</th>
	 		<th>客户代码</th>
	 		<th>客户等级</th>
	 		<th>公司名称</th>
	 		<th>公司联系人</th>
	 		<th>电话/手机</th>
	 		<th>负责人</th>
	 		<th>跟进目的</th>
	 		<th>跟进效果</th>
	 		<th>下步计划</th>
	 		<th>备注</th>
		</tr>
		<?php 
			if(is_array($array_customer)){
			foreach($array_customer as $k=>$v){ 
		?>
		<tr>
	 		<td>
	 			<?php echo $v['status_time']  ?>
	 		</td>
	 		<td class="code">
	 			<?php echo $v['status_code']  ?>
	 		</td>
	 		<td class="grade">
	 			<?php echo $v['status_grade']  ?>
	 		</td>
	 		<td class="customer">
				<?php echo $v['status_customer'] ?>
	 		</td>	
	 		<td class="contacts">
	 			<?php echo $v['status_contacts'] ?>
	 		</td>
	 		<td class="phone">
	 			<?php echo $v['status_phone']  ?>
	 		</td>
	 		<td class="boss">
	 			<?php echo $v['status_boss'] ?>
	 		</td>
	 		<td>
	 			<?php echo $v['status_goal'] ?>
	 		</td>
	 		<td>
	 			<?php echo $v['status_result'] ?>
	 		</td>
	 		<td>
	 			<?php echo $v['status_plan'] ?>
	 		</td>
	 		<td>
	 			<?php echo $v['status_note'] ?>
	 		</td>
		</tr>
		<?php }} ?>
		<tr>
	  		<td colspan="11" style="text-align:center">
	  			<p id="add_status" style="width:100px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">添加跟进状态</p>
	  			<p id="cancel_status" style="width:100px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">撤销</p>
	  		</td>
	  	</tr>
	</table>
</div>
  <div id="save">
  	<input type="hidden" name="submit" value="submit" />
  	<input type="hidden" name="action" value="status_edit" />
  	<button>保存</button>
  	<button onclick="javascript:history.go(-1);return false;">返回</button>
  </div>
</form>

  <?php
  	}  
  
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>