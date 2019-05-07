<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);

$employeeid = $_SESSION['employee_info']['employeeid'];
//通过员工id查询姓名和部门
$sql = "SELECT a.employee_name,b.`dept_name` FROM `db_employee` as a INNER JOIN `db_department` as b ON a.deptid = b.deptid WHERE a.employeeid = ".$employeeid;
$res = $db->query($sql);
$dept = [];
while($rows = $res->fetch_assoc()){
	$dept = $rows;
}
//查找市场部和项目部的人员
$min_boss_sql = "SELECT `employee_name`,`employeeid`,`deptid` FROM `db_employee` WHERE `deptid` IN (2,7)";
$min_boss = $db->query($min_boss_sql);
$employees = [];
if($min_boss->num_rows){
	while($row = $min_boss->fetch_assoc()){
		$employees[] = $row;
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

	$(function(){
		$('tr').removeClass('even');
		
	var add_contacts= '	<tr >	  	  	 <th width="11%" >联系人姓名：</th>	     	   	<td width="18%">	      	   		<input teyp="text" name="contacts_name[]" /> <span  class="del del_contacts">删除</span>  	 	   </td>		  	</tr>	<tr>	  		 <th>所属公司 ：</th>	     		 <td>	      			<input type="text" name="contacts_company[]" />	     		 </td>		  	</tr>  	<tr>	  		 <th>职务：</th>	     		 <td>	      			<input type="text" name="contacts_work[]" />	     		 </td>		  	</tr>	  	<tr>	  		 <th>电话：</th>			<td>			      <input type="text" name="contacts_tel[]" />			</td>		  	</tr>	  	<tr>			<th>手机：</th>			<td>			     <input type="text" name="contacts_phone[]" />			</td>	  	</tr>	  	<tr>	  		 <th>邮箱：</th>	      		<td>	      			<input type="text" name="contacts_email[]" />	      		</td>		  	</tr>	  	<tr>	  		  <th>备注：</th>			  <td>			      <input type="text" name="contacts_note[]" />			  </td>	  	</tr>';
	var add_company = '<tr>		      <th width="11%">分公司代码：</th>		      <td width="18%">		      	<input type="text" name="customer_code[]" />	<span  class="del del_company">删除</span>	      </td>		</tr>		<tr>		      <th>客户等级 ：</th>		      <td>		      		<select name="customer_grade[]" style="width:250px;height:30px">		      		<?php foreach($array_customer_grade as $k=>$v){ 
		      			echo '<option value="'.$v.'">'.$v.'</option>';
		      			}
		      			?>		      	</select>		      		      </td>		</tr>		<tr>		      <th>客户名称：</th>		      <td>		      	<input type="text" name="customer_name[]" />		      </td>		</tr>		<tr>		      <th>客户类型：</th>		      <td>		      	<input type="text" name="customer_type[]" />		      </td>		</tr>		<tr>		      <th>主营业务：</th>		      <td>		      	<input type="text" name="customer_business[]" />		      </td>		</tr>		<tr>		      <th>网址：</th>		      <td>		      	<input type="text" name="customer_url[]" />		      </td>		</tr>		<tr>		      <th>地址：</th>		      <td>		      	<input type="text" name="customer_address[]" />		      </td>		</tr>		<tr class="last_tr">		      <th>邮编：</th>		      <td class="post">		      	<input type="text" name ="customer_post[]" />		      </td>	    	</tr>';
	
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
			var type = $('input[name ^= customer_type]').eq(i).val();
			if(!$.trim(type)){
				alert('客户类型不能为空');
				$('input[name ^= customer_type]').eq(i).focus();
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
				alert('联系人手机,电话,手机至少填写一项');
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
		//判断跟进状态的必需字段不能为空
		var goal = $('input[name = status_goal]').val();
		if(!$.trim(goal)){
			alert('跟进目的不能为空');
			$('input[name = status_goal]').focus();
			return false;
		}
		var result = $('input[name = status_result]').val()
		if(!$.trim(result)){
			alert('跟进效果不能为空');
			$('input[name = status_result]').focus();
			return false;
		}
		var plan = $('input[name = status_plan]').val();
		if(!$.trim(plan)){
			alert('下步计划不能为空');
			$('input[name = status_plan]').focus();
			return false;
		}

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
	form{background:white;}
	table:not(#customer_status) input{width:250px;height:25px;}
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
  if($action == 'add'){
	  $sql_employee = "SELECT `employee_name`,`phone`,`email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
	  $result_employee = $db->query($sql_employee);
	  $array_employee = $result_employee->fetch_assoc();
  ?>
  <h4 style="background:#eee">客户信息</h4>
  <form action="customer_datado.php" method="post">


 
	  <table style="width:400px;float:left;;position:relative;left:100px">
	  	<tr>
	  		<td colspan="2" style="text-align:center">客户信息</td>
	  	</tr>
	  	<tr>
		      <th width="11%">客户代码：</th>
		      <td>
		      	<input type="text" name="customer_code[]" />
		      </td>
		</tr>
		<tr>
		      <th width="11%">客户等级：</th>
		      <td>
		      	<select name="customer_grade[]" style="width:250px;height:30px">
		      		<?php foreach($array_customer_grade as $k=>$v){ 
		      			echo '<option value="'.$v.'">'.$v.'</option>';
		      			}
		      			?>

		      	</select>
		      	
		      </td>
		</tr>
		<tr>
		      <th width="11%">客户名称：</th>
		      <td width="18%">
		      	<input type="text" name="customer_name[]" />
		      </td>
		</tr>
		<tr>
		      <th>客户类型：</th>
		      <td>
		      	<input type="text" name="customer_type[]" />
		      </td>
		</tr>
		<tr>
		      <th>主营业务：</th>
		      <td>
		      	<input type="text" name="customer_business[]" />
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
	  <table style="width:400px;float:left;position:relative;left:100px;background:rgb(240,243,247)">
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
	  		 <th>所属公司：</th>
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
	  <table style="width:400px;float:left;position:relative;left:100px">
	  	<tr>
	  		<td colspan="2" style='text-align:center'>
	  			负责人信息
	  		</td>
	  	</tr>	
	  	<tr>
	  	 	<th width="11%">总负责人：</th>
	   		 <td width="18%">
			      	<input type="text" name="boss_name[]" value="杨春民" readonly />
			 </td>	
	  	</tr>
		<tr>
		    	 <th width="11%">负责人：</th>
			 <td>
			      <select name="min_boss[]" id="min_boss" style="width:250px;height:30px">
			      		<option>--请选择--</option>
			      	<?php foreach($employees as $k=>$v){
			      		echo '<option value="'.$v['employee_name'].'">'.$v['employee_name'].'</option>';
			      		} ?>
			      </select>
			 </td>
	   	 </tr>
	  	<tr>
	  		 <th width="11%">所属部门：</th>
			 <td>
			      <input type="text" id="boss_unit" name="boss_unit[]" value="" />
			 </td>
	  	</tr>
	  	
	  </table>
<div style="clear:both"></div>
<h4 style="background:#eee">首次状态</h4>
<div style="border-bottom:1px solid #ddd"></div>
<div>
	<table id="customer_status" style="width:95%;margin:30px auto;">
		
		<tr>
	 		<th>时间</th>
	 		<th>客户公司</th>	
	 		<th>联系人</th>
	 		<th>负责人</th>
	 		<th>跟进目的</th>
	 		<th>跟进效果</th>
	 		<th>下步计划</th>
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
	 			<input type="text" name="status_boss" id="status_boss">
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
  	<button>保存</button>
  </div>
</form>
   <!--
      <td colspan="2" style="text-align:center">
      	
      </td>
    </tr>
   
    <tr id="add_button">
    	<td colspan="6" style="text-align:center">
    		<input type="submit" name="submit" value="添加"/>
    	</td>
    </tr>
    
  </table>
  </form>
  <!--  <ul class="reg_ul">
      <li>
          <span>客户名称：</span>
          <input type="text" name="customer_name" value="" placeholder="4-8位用户名" class="customer_name">
          <span class="tip name_hint"></span>
      </li>
      <li>
          <span>客户代码：</span>
          <input type="text" name="customer_code" value="" placeholder="" class="customer_code">
          <span class="tip code_hint"></span>
      </li>
       <li>
          <span>客户系数：</span>
          <input type="text" name="customer_value" value="" placeholder="" class="customer_value">
          <span class="tip value_hint"></span>
      </li>
      <li>
          <span>联系人：</span>
          <input type="text" name="customer_contacts" value=""  placeholder="联系人姓名" class="customer_contacts">
          <span class="tip contacts_hint"></span>
      </li>
        <li>
          <span>手机号码：</span>
          <input type="text" name="customer_phone" value="" placeholder="手机号" class="customer_phone">
          <span class="tip phone_hint"></span>
      </li>
        <li>
          <span>邮箱：</span>
          <input type="text" name="customer_email" value="" placeholder="邮箱" class="customer_email">
          <span class="tip email_hint"></span>
      </li>
      <li>
          <span>地址：</span>
          <input type="text" name="customer_address" value="" placeholder="地址" class="customer_address">
          <span class="tip address_hint"></span>
      </li>
    
    <input type="hidden" value="add" name="action" >
      <li>
        <button type="submit" value="add" name="submit" class="red_button">添加</button>
      </li>
    </ul>
  </div>
 </form>
  <?php
  	}  
  
  ?>-->
</div>
<?php include "../footer.php"; ?>
</body>
</html>