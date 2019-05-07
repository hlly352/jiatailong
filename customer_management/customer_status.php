<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$employee_id = $_SESSION['employee_info']['employeeid'];

if($_GET['submit']){
	$customer_name = trim($_GET['customer_name']);
	$customer_code = trim($_GET['customer_code']);
	$contacts_name = trim($_GET['contacts_name']);
	$contacts_phone = trim($_GET['contacts_phone']);
	$sqlwhere = "  `customer_name` LIKE '%$customer_name%' AND `customer_code` LIKE '%$customer_code%' AND `contacts_phone` LIKE '%$contacts_phone%' AND `contacts_name` LIKE '%$contacts_name%'";
}

//查询客户状态记录表


	$sql = "SELECT * FROM `db_customer_status` WHERE `add_time` IN (SELECT max(`add_time`) FROM `db_customer_status`  GROUP BY `customer_id`) ".$sqlwhere;
	
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `add_time` DESC" . $pages->limitsql;
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
<script type="text/javascript">
function getdate(timestamp) {
        var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
        var Y = date.getFullYear() + '-';
        var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
        var D = date.getDate() + ' ';
        var h = date.getHours() + ':';
        var m = date.getMinutes() + ':';
        var s = date.getSeconds();
        return Y+M+D;
    }

    	$(function(){
		//点击每一行内容,跳转到内容详情页面'[name^=id]:checkbox'
		$('.show_list').live('click',function(){
		
			var trs = $(this).parent().children().children('input[name^=id]').val();
		
			window.open('customer_status_show.php?action=show&id='+trs,'_self');
				})

		//点击查看获取数据
		$('.show_history').live('click',function(){
			var status_tr = $(this).parent().parent();
			var customer_id = $(this).parent().prevAll('input:hidden').val();
			var current_button = $(this);
			//当前按钮的序列
			var button_index = $('.show_history').index($(this));
			if($(this).attr('class') == 'show_history'){

			//通过状态id获取客户的历史状态
			$.post('../ajax_function/customer_status_history.php',{customer_id:customer_id},function(data){
				var count = data[0].count;
				
				//遍历得到的历史数据
				for(var i=0;i<data.length - 1;i++){
					//动态添加行
					var new_trs = '<tr class="new_tr'+button_index+'"> <td style=""><input type="checkbox" name="id[]" value="'+data[i].customer_status_id+'"<?php if($count > 0) echo " disabled=\"disabled\""; ?> /></td>        <td style="">'+data[i].status_time+'</td>        <td style="">'+data[i].status_customer+'</td>        <td style="">'+data[i].status_boss+'</td>        <td style="">'+data[i].status_goal+'</td>        <td style="">'+data[i].status_result+'</td>        <td style="">'+data[i].status_plan+'</th>        <td style="">'+data[i].status_note+'</td>        <td style=""></td></tr>';
					status_tr.after(new_trs)
				}
				
			},'json')
			//给按钮增加类名,作为下一次判断的依据
			current_button.addClass('show');
			
			current_button.css('background','grey').text('收起');
				
			} else {
				//删除对应按钮序列下的所有新添加行
				$('.new_tr'+button_index).remove();
				$(this).removeClass('show');
				$(this).css('background','green');
				$(this).text('查看');
			}
		})
	})
      </script>
<title>模具报价-嘉泰隆</title>
<style type="text/css">
	#main{table-layout:fixed;width:1350px;}
	#main tr td{word-wrap:break-word;word-break:break-all;}
	table{table-layout:fixed;}
	
</style>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4 style="padding-left:10px">
  	 
  </h4>
  <form action="" name="search" method="get">
    <table >
      <tr>
      	
          
       </tr>
       <tr>
       <td>客户名称</td>
       <td><input type="text" name="customer_name" class="input_txt" /></td>
       <td></td>
       <td>客户代码</td>
       <td><input type="text" name="customer_code" class="input_txt"></td>
       <td></td>
        <td>联系人</td>
        <td><input type="text" name="contacts_name" class="input_txt" /></td>
        <td>手机号</td>
        <td><input type="text" name="contacts_phone" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查找" class="button" />
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){

		  $array_customer_id .= $row_id['id'].',';
	  }
	  $array_customer_id = rtrim($array_customer_id,',');

	  $sql_group = "SELECT `id`,COUNT(*) AS `count` FROM `db_customer_info` WHERE `id` IN ($array_customer_id) AND `customer_status` = '0' GROUP BY `id`";
	
	  $result_group = $db->query($sql_group);
	  if($result_group->num_rows){
		  while($row_group = $result_group->fetch_assoc()){
		  	
			  $array_group[$row_group['id']] = $row_group['count'];
		  }
	  }else{
		  $array_group = array();
	  }
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
	$system_sql = "SELECT `isconfirm`,`isadmin` FROM `db_system_employee` WHERE `employeeid`='$employee_id' AND `systemid`=".$system_id;
	$system_res = $db->query($system_sql);

	$system_info = [];
	while($system_admin = $system_res->fetch_assoc()){
		$system_info = $system_admin;
	}
	
	
  ?>
  <form action="customer_datado.php" method="post">
    <table id="main" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width:40px">ID</th>
        <th style="">跟进时间</th>
        <th style="">客户名称</th>
        <th style="">负责人</th>
        <th style="">跟进目的</th>
        <th style="">跟进效果</th>
        <th style="">跟进计划</th>
        <th style="">备注</th>
        <th style="">查看历史</th>
      <?php
      while($row = $result->fetch_assoc()){
      	//查询状态的历史个数
      	$sql = "SELECT COUNT(*) FROM `db_customer_status` WHERE `customer_id`=".$row['customer_id'];
      	$res = $db->query($sql);
      	if($res->num_rows){
      		$row['count'] = $res->fetch_row()[0];
      	}
	  ?>
     <tr class="show">
     
        <td><input type="checkbox" name="id[]" value="<?php echo $row['customer_status_id']; ?>"<?php if($count > 0) echo " disabled=\"disabled\""; ?> /></td>
        <td class="show_list"><?php echo $row['status_time'] ?></td>     
        <td class="show_list"><?php echo $row['status_customer'] ?></td>
        <td class="show_list"><?php echo $row['status_boss']; ?></td>
        <td class="show_list"><?php echo $row['status_goal']; ?></td> 
        <td class="show_list"><?php echo $row['status_result'] ?></td>
        <td class="show_list"><?php echo $row['status_plan'] ?></td>
        <td class="show_list"><?php echo $row['status_note'] ?></td>
         <input type="hidden" name="customer_id" value="<?php echo $row['customer_id'] ?>"></td>
      <!-- <td><a href="mould_quote_list.php?id=<?php echo $mould_dataid; ?>"><img src="../images/system_ico/quote_11_12.png" width="11" height="12" /></a></td> -->
        <td><button onclick="JavaScript:return false;" class="<?php echo $row['count']>1?'show_history':' ' ?>" style="<?php echo $row['count']>1?'background:green':' '; ?>">查看</button></td>
      </tr> 
      <?php } ?>
    </table>
    <?php 
    	if($system_info['isadmin'] == '1'){
    	?>
  <!--  <div id="checkall">
	      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
	      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
	      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />

	      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />     
      	      <input type="hidden" name="action" value="status_del" />
    </div>-->
    	<?php }?>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
</form>
<?php include "../footer.php"; ?>
</body>
</html>