<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);

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

	$(function(){
		$('tr').removeClass('even');
		//新增联系人
		var add_contacts= '	<tr >	  	  	 <th width="11%" >联系人姓名：</th>	     	   	<td width="18%">	      	   		<input teyp="text" name="contacts_name[]" /> <span  class="del del_contacts">删除</span>  	 	   </td>		  	</tr>	<tr>	  		 <th>所属公司 ：</th>	     		 <td>	      			<input type="text" name="contacts_company[]" />	     		 </td>		  	</tr>  	<tr>	  		 <th>职务：</th>	     		 <td>	      			<input type="text" name="contacts_work[]" />	     		 </td>		  	</tr>	  	<tr>	  		 <th>电话：</th>			<td>			      <input type="text" name="contacts_tel[]" />			</td>		  	</tr>	  	<tr>			<th>手机：</th>			<td>			     <input type="text" name="contacts_phone[]" />			</td>	  	</tr>	  	<tr>	  		 <th>邮箱：</th>	      		<td>	      			<input type="text" name="contacts_email[]" />	      		</td>		  	</tr>	  	<tr>	  		  <th>备注：</th>			  <td>			      <input type="text" name="contacts_note[]" />			  </td>	  	</tr>';
		var add_company = '<tr>		      <th width="11%">分公司名称：</th>		      <td width="18%">		      	<input type="text" name="customer_name[]" />	<span  class="del del_company">删除</span>	      </td>		</tr>		<tr>		      <th>客户代码 ：</th>		      <td>		      	<input type="text" name="customer_code[]" />		      </td>		</tr>		<tr>		      <th>客户类型：</th>		      <td>		      	<input type="text" name="customer_type[]" />		      </td>		</tr>		<tr>		      <th>电话：</th>		      <td>		      	<input type="text" name="customer_tel[]" />		      </td>		</tr>		<tr>		      <th>邮箱：</th>		      <td>		      	<input type="text" name="customer_email[]" />		      </td>		</tr>		<tr>		      <th>网址：</th>		      <td>		      	<input type="text" name="customer_url[]" />		      </td>		</tr>		<tr>		      <th>地址：</th>		      <td>		      	<input type="text" name="customer_address[]" />		      </td>		</tr>		<tr class="last_tr">		      <th>邮编：</th>		      <td class="post">		      	<input type="text" name ="customer_post[]" />		      </td>	    	</tr>';

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
	//动态添加负责人信息
	$('#add_boss').live('click',function(){
		var customer_boss = $('.customer_boss').children().eq(0).val();
		var min_boss = $('.customer_boss').children().eq(1).val();
		var boss_unit = $('.customer_boss').children().eq(2).val();
		var old_boss = '<tr>	  	 	<th width="11%">总负责人：</th>	   		 <td width="18%" class="">			      	<input type="text" name="boss_name" value="'+customer_boss+'"/>			 </td>		  	</tr>		<tr>		    	 <th width="">负责人：</th>			 <td class="">			      <input type="text" name="min_boss" value="'+min_boss+'"/>			 </td>	   	 </tr>	  	<tr>	  		 <th width="11%">所属部门：</th>			 <td class="">			      <input type="text" name="boss_unit"  value="'+boss_unit+'"/>			 </td>	  	</tr>	  ';
		$(this).parent().parent().after(old_boss);
		$('.customer_boss').children().val(' ');
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
	})

</script>
<style type="text/css" media="screen">
	form{background:white;}
	table:not('#customer') input{width:250px;height:25px;}
	.del{display:inline-block;width:50px;height:23px;background:#eee;text-align:center;line-height:23px;font-size:13px;cursor:pointer;}
	#save{clear:both;width:100%;height:100px;text-align:center;margin-top:20px;}
	#save button{width:180px;height:40px;cursor:pointer;margin-top:40px;}
	#customer_status tr td{border:1px solid #ddd;}
	#customer_status tr td{height:20px;word-wrap:break-word;word-break:break-all}
	#customer_status tr th{text-align:center;}
</style>
<title>客户管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == 'show'){
	  $sql_employee = "SELECT `employee_name`,`phone`,`email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
	  $result_employee = $db->query($sql_employee);
	  $array_employee = $result_employee->fetch_assoc();
  ?>
  <h4 style="background:white">客户信息</h4>
  <form action="customer_datado.php" method="post">


 
	  <table style="width:450px;float:left;;position:relative;left:35px">
	  	<tr>
	  		<td colspan="2" style="text-align:center">客户信息</td>
	  	</tr>
	  	<tr>
		      <th width="11%">客户名称：</th>
		      <td width="18%">
		      	<input type="text" name="customer_name[]" />
		      </td>
		</tr>
		<tr>
		      <th width="11%">客户代码 ：</th>
		      <td>
		      	<input type="text" name="customer_code[]" />
		      </td>
		</tr>
		<tr>
		      <th>客户类型：</th>
		      <td>
		      	<input type="text" name="customer_type[]" />
		      </td>
		</tr>
		<tr>
		      <th>电话：</th>
		      <td>
		      	<input type="text" name="customer_tel[]" />
		      </td>
		</tr>
		<tr>
		      <th>邮箱：</th>
		      <td>
		      	<input type="text" name="customer_email[]" />
		      </td>
		</tr>
		<tr>
		      <th>网址：</th>
		      <td>
		      	<input type="text" name="customer_url[]" />
		      </td>
		</tr>
		<tr>
		      <th>地址：</th>
		      <td>
		      	<input type="text" name="customer_address[]" />
		      </td>
		</tr>
		<tr class="last_tr">
		      <th>邮编：</th>
		      <td class="post">
		      	<input type="text" name ="customer_post[]" />
		      </td>
	    	</tr>
	    	<tr>
	    		<td colspan="2" style="text-align:center">
	    			<p id="add_company" style="width:100px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">新增分公司</p>
	    		</td>
	    	</tr>
	  </table>
	  <table style="width:450px;float:left;position:relative;left:35px;background:rgb(240,243,247)">
	  	<tr>
	  		<td colspan="2" style="text-align:center">联系人信息</td>
	  	</tr>
	  	<tr>
	  	  	 <th width="11%">联系人姓名：</th>
	     	   	<td width="18%">
	      	   		<input teyp="text" name="contacts_name[]" />
	     	 	   </td>	
	  	</tr>
	  	<tr>
	  		 <th>所属公司 ：</th>
	     		 <td>
	      			<input type="text" name="contacts_company[]" />
	     		 </td>	
	  	</tr>
	  	<tr>
	  		 <th>职务：</th>
	     		 <td>
	      			<input type="text" name="contacts_work[]" />
	     		 </td>	
	  	</tr>
	  	<tr>
	  		 <th>电话：</th>
			<td>
			      <input type="text" name="contacts_tel[]" />
			</td>	
	  	</tr>
	  	<tr>
			<th>手机：</th>
			<td>
			     <input type="text" name="contacts_phone[]" />
			</td>
	  	</tr>
	  	<tr>
	  		 <th>邮箱：</th>
	      		<td>
	      			<input type="text" name="contacts_email[]" />
	      		</td>	
	  	</tr>
	  	<tr>
	  		  <th>备注：</th>
			  <td>
			      <input type="text" name="contacts_note[]" />
			  </td>
	  	</tr>
	  	<tr>
	  		<td colspan="2" style="text-align:center">
	  			<p id="add_contacts" style="width:100px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">新增联系人</p>
	  		</td>
	  	</tr>
	  </table>
	  <table style="width:450px;float:left;position:relative;left:35px">
	  	<tr>
	  		<td colspan="2" style='text-align:center'>
	  			目前状态
	  		</td>
	  	</tr>	
	  	<tr>
	  	 	<th width="11%">总负责人：</th>
	   		 <td width="18%" class="customer_boss">
			      	<input type="text" name="boss_name" />
			 </td>	
	  	</tr>
		<tr>
		    	 <th width="11%">负责人：</th>
			 <td class="customer_boss">
			      <input type="text" name="min_boss" />
			 </td>
	   	 </tr>
	  	<tr>
	  		 <th width="11%">所属部门：</th>
			 <td class="customer_boss">
			      <input type="text" name="boss_unit" />
			 </td>
	  	</tr>
	  	<tr>
	  		<td colspan="2" style="text-align:center">
	  			<p id="add_boss" style="width:100px;height:15px;background:grey;display:inline-block;cursor:pointer;border-radius:4px">负责人更换</p>
	  		</td>
	  	</tr>
	  	
	  </table>
<div style="clear:both;border-bottom:1px solid grey">
	
</div>
<div>
	<table id="customer_status" style="width:95%;margin:30px auto;">
		<caption style="padding-bottom:10px">跟进状态记录表</caption>
		<tr>
	 		<th>时间</th>
	 		<th>客户</th>	
	 		<th>联系人</th>
	 		<th>负责人</th>
	 		<th>目标</th>
	 		<th>效果</th>
	 		<th>计划</th>
	 		<th>备注</th>
		</tr>
		<tr>
	 		<td>
	 			<input type="text" name="status_time" >
	 		</td>
	 		<td>
	 			<input type="text" name="status_customer">
	 		</td>	
	 		<td>
	 			<input type="text" name="status_contacts">
	 		</td>
	 		<td>
	 			<input type="text" name="status_boss" >
	 		</td>
	 		<td>
	 			<input type="text" name="status_goal">
	 		</td>
	 		<td>
	 			<input type="text" name="status_result">
	 		</td>
	 		<td>
	 			<input type="text" name="status_plan">
	 		</td>
	 		<td>
	 			<input type="text" name="status_note">
	 		</td>
		</tr>
	</table>
</div>
  <div id="save">
  	<input type="hidden" name="submit" value="submit" />
  	<input type="hidden" name="action" value="add" />
  	<button>修改</button>
  </div>
</form>

  <?php
  	}  
  
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>