<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$customer_id = $_GET['id'];
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
	$(function(){
	//更改类名
	$('tr').removeClass('even');
	
	var add_contacts= '	<tr >	  	  	 <th width="11%" >联系人姓名：</th>	     	   	<td width="18%">	      	   		<input teyp="text" name="contacts_name[]" /> <span  class="del del_contacts">删除</span>  	 	   </td>		  	</tr>	  	<tr>	  		 <th>职务：</th>	     		 <td>	      			<input type="text" name="contacts_work[]" />	     		 </td>		  	</tr>	  	<tr>	  		 <th>电话：</th>			<td>			      <input type="text" name="contacts_tel[]" />			</td>		  	</tr>	  	<tr>			<th>手机：</th>			<td>			     <input type="text" name="contacts_phone[]" />			</td>	  	</tr>	  	<tr>	  		 <th>邮箱：</th>	      		<td>	      			<input type="text" name="contacts_email[]" />	      		</td>		  	</tr>	  	<tr>	  		  <th>备注：</th>			  <td>			      <input type="text" name="contacts_note[]" />			  </td>	  	</tr>';
	var add_company = '<tr>		      <th width="11%">客户名称：</th>		      <td width="18%">		      	<input type="text" name="customer_name[]" />	<span  class="del del_company">删除</span>	      </td>		</tr>		<tr>		      <th>客户代码 ：</th>		      <td>		      	<input type="text" name="customer_code[]" />		      </td>		</tr>		<tr>		      <th>客户类型：</th>		      <td>		      	<input type="text" name="customer_type[]" />		      </td>		</tr>		<tr>		      <th>电话：</th>		      <td>		      	<input type="text" name="customer_tel[]" />		      </td>		</tr>		<tr>		      <th>邮箱：</th>		      <td>		      	<input type="text" name="customer_email[]" />		      </td>		</tr>		<tr>		      <th>网址：</th>		      <td>		      	<input type="text" name="customer_url[]" />		      </td>		</tr>		<tr>		      <th>地址：</th>		      <td>		      	<input type="text" name="customer_address[]" />		      </td>		</tr>		<tr class="last_tr">		      <th>邮编：</th>		      <td class="post">		      	<input type="text" name ="customer_post[]" />		      </td>	    	</tr>';
	
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
		$(this).parent().parent().nextAll().slice(0,5).remove();
		$(this).parent().parent().remove();
		$(this).remove();
	})
	//动态删除分公司信息
	$('.del_company').live('click',function(){
		$(this).parent().parent().nextAll().slice(0,7).remove();
		$(this).parent().parent().remove();
		$(this).remove();
	})
	})
</script>
<style type="text/css" media="screen">
	form{background:white;}
	input{width:250px;height:25px;}
	.del{display:inline-block;width:50px;height:23px;background:#eee;text-align:center;line-height:23px;font-size:13px;cursor:pointer;}
	#save{clear:both;width:100%;height:100px;text-align:center;margin-top:20px;}
	#save button{width:180px;height:40px;cursor:pointer;margin-top:40px;}
</style>
<title>客户管理-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == 'edit'){
	  $sql_customer_info = "SELECT * FROM `db_customer_info` WHERE `customer_id` = '$customer_id'";
	  $result_customer = $db->query($sql_customer_info);
	  $array_customer = $result_customer->fetch_assoc();
	  //获取联系人的信息
	  $contacts_res = [$array_customer['contacts_name'],$array_customer['contacts_work'],$array_customer['contacts_tel'],$array_customer['contacts_phone'],$array_customer['contacts_email'],$array_customer['contacts_note']];

	  $contacts = getdata($contacts_res);
	  //获取客户的公司信息
	  $customer_res = [$array_customer['customer_name'],$array_customer['customer_code'],$array_customer['customer_type'],$array_customer['customer_tel'],$array_customer['customer_email'],$array_customer['customer_url'],$array_customer['customer_address'],$array_customer['customer_post']];
	  $customer = getdata($customer_res);


  ?>
  <h4 style="background:white">客户信息</h4>
  <form action="customer_datado.php" method="post">
  <div >
 
	  <table style="width:450px;float:left;;position:relative;left:35px">
	  	<tr>
	  		<td colspan="2" style="text-align:center">客户信息</td>
	  	</tr>
	  	<?php 
	  		foreach($customer as $k=>$v){
	  	?>
	  	<tr>
		      <th width="11%">客户名称：</th>
		      <td width="18%">
		      	<input type="text" name="customer_name[]" value="<?php  echo $v[0] ?>" />
		      </td>
		</tr>
		<tr>
		      <th width="11%">客户代码 ：</th>
		      <td>
		      	<input type="text" name="customer_code[]" value="<?php echo $v[1] ?>" />
		      </td>
		</tr>
		<tr>
		      <th>客户类型：</th>
		      <td>
		      	<input type="text" name="customer_type[]" value="<?php echo $v[2] ?>" />
		      </td>
		</tr>
		<tr>
		      <th>电话：</th>
		      <td>
		      	<input type="text" name="customer_tel[]" value="<?php echo $v[3] ?>" />
		      </td>
		</tr>
		<tr>
		      <th>邮箱：</th>
		      <td>
		      	<input type="text" name="customer_email[]" value="<?php echo $v[4] ?>" />
		      </td>
		</tr>
		<tr>
		      <th>网址：</th>
		      <td>
		      	<input type="text" name="customer_url[]" value="<?php echo $v[5] ?>" />
		      </td>
		</tr>
		<tr>
		      <th>地址：</th>
		      <td>
		      	<input type="text" name="customer_address[]" value="<?php echo $v[6] ?>" />
		      </td>
		</tr>
		<tr class="last_tr">
		      <th>邮编：</th>
		      <td class="post">
		      	<input type="text" name ="customer_post[]" value="<?php echo $v[7] ?>" />
		      </td>
	    	</tr>
	    	<?php } ?>
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
	  	<?php 
	  		foreach($contacts as $k=>$v){
	  	 ?>
	  	<tr>
	  	  	 <th width="11%">联系人姓名：</th>
	     	   	<td width="18%">
	      	   		<input teyp="text" name="contacts_name[]" value="<?php echo $v[0] ?>" />
	      	   		<?php  
	      	   			if($k > 0){
	      	   				echo ' <span  class="del del_contacts">删除</span>  ';
	      	   			}
	      	   		?>
	     	 	   </td>	
	  	</tr>
	  	<tr>
	  		 <th>职务：</th>
	     		 <td>
	      			<input type="text" name="contacts_work[]" value="<?php echo $v[1] ?>"/>
	     		 </td>	
	  	</tr>
	  	<tr>
	  		 <th>电话：</th>
			<td>
			      <input type="text" name="contacts_tel[]" value="<?php echo $v[2] ?>" />
			</td>	
	  	</tr>
	  	<tr>
			<th>手机：</th>
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
	  <table style="width:450px;float:left;position:relative;left:35px">
	  	<tr>
	  		<td colspan="2" style='text-align:center'>
	  			目前状态
	  		</td>
	  	</tr>	
	  	<tr>
	  	 	<th width="11%">负责人：</th>
	   		 <td width="18%">
			      	<input type="text" name="boss_name" value="<?php echo $array_customer['boss_name'] ?>"/>
			 </td>	
	  	</tr>
	  	<tr>
	  		 <th width="11%">所属部门：</th>
			 <td>
			      <input type="text" name="boss_unit" value="<?php echo $array_customer['boss_unit'] ?>" />
			 </td>
	  	</tr>
	  	<tr>
		    	 <th width="11%">跟进状态：</th>
			 <td>
			      <input type="text" name="customer_status" value="<?php echo $array_customer['customer_status'] ?>"/>
			 </td>
	   	 </tr>
	  </table>
</div>
  <div id="save">
  	<input type="hidden" name="submit" value="submit" />
  	<input type="hidden" name="action" value="edit" />
  	<button>保存</button>
  </div>
</form>


  <?php
  	}  
  
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>