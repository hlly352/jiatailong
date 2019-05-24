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
	})
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
		//判断是否有添加项
    if($(this).prev().attr('id') == 'add_task'){
		if($(this).parent().parent().prev('.task').length == 0){
			alert('请先点击新建临时任务');
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
			var info = /\d+/.test(number);
			if(!info){
				alert('请输入数字');
				$('input[name=number]').focus();
				return false;
			}
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
		//金额
		var price = $(this).parent().parent().prev('.task').children().children('input[name=price]').val();
		if(!number){
			alert('请输入数量');
			$('input[name=price]').focus();
			return false;
		}else{
			var info = /\d+/.test(price);
			if(!info){
				alert('请输入数字');
				$('input[name=price]').focus();
				return false;
			}
		}	
	})
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
      <tr>
        <th style="">时间</th>
        <th style="">客户名称</th>
        <th style="">客户代码</th>
        <th style="">模号</th>
        <th style="">模具名称</th>
        <th style="">规格</th>
        <th style="">材质</th>
        <th style="">数量</th>
        <th style="">单价(RMB)</th>
        <th style="">金额(RMB)</th>
        <th style="">备注</th>
      <?php

       if($result->num_rows !=0){
          while($row = $result->fetch_assoc()){
      
    ?>
     <tr class="show">
        <td class="show_list"><?php echo date('Y-m-d',$row['task_time'])?></td>
        <td class="show_list"><?php echo $row['customer_name'] ?></td>
        <td class="show_list"><?php echo $row['customer_code']?></td>
        <td class="show_list"><?php echo $row['mould_no']?></td>
        <td class="show_list"><?php echo $row['mould_name']?></td>
        <td class="show_list"><?php echo $row['size']?></td>
        <td class="show_list"><?php echo $row['material']?></td>
        <td class="show_list"><?php echo $row['number']?></td>
        <td class="show_list"><?php echo $row['unit_price']?></td>
        <td class="show_list"><?php echo $row['price']?></td>
        <td class="show_list"><?php echo $row['notes']?></td>

      </tr> 

      <?php } ?>
      <tr>
              <td colspan="11" style="align:center">
              	<span id="add_task">新建临时任务</span>
              	&nbsp;&nbsp;
              	<input type="submit" value="保 存" style="margin-top:5px;height:29px;width:80px">
              </td>
          </tr>
       </table>
    <!--<div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
 
  <div id="page">
    <?php $pages->getPage();?>
  </div>-->
  <?php
  }else{?>
      
           <tr class="task">
              <td class="show_list"><input type="text" name="task_time" value="<?php echo date('Y-m-d',time()) ?>"></td>
              <td class="show_list">
              	<select name="customer_name" class="customer_names" style="width:120px">  
              		 <option value="0">--选择客户--</option>          	
              			<?php foreach($customer_list as $k=>$v){?>              	
              		<option value="<?php echo $v['customer_id']?>">
              			<?php echo strstr($v['customer_name'],'$$')?substr($v['customer_name'],strrpos($v['customer_name'],'$$')+2):$v['customer_name'] ?> 
              		</option>              			
              		<?php }?>
                   	</select>
              </td>
              <td class="show_list"><input type="text" name="customer_code" class="customer_codes"></td>
              <td class="show_list"><input type="text" name="mould_no"></td>
              <td class="show_list"><input type="text" name="mould_name"></td>
              <td class="show_list"><input type="text" name="size"></td>
              <td class="show_list"><input type="text" name="material"></td>
              <td class="show_list"><input type="text" name="number"></td>
              <td class="show_list"><input type="text" name="unit_price"></td>
              <td class="show_list"><input type="text" name="price"></td>
              <td class="show_list"><input type="text" name="notes"></td>
          </tr>
          <tr>
              <td colspan="11" style="align:center">
              	<input type="submit" value="保存" style="margin-top:5px;height:29px;width:80px">
              </td>
          </tr>
    </form>
  </table>
<?php    }  ?>
</div>
 <?php include "../footer.php"; ?>
</body>
</html>