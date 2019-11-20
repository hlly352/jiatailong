<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$specification_id = htmlspecialchars(trim($_GET['specification_id']));
$reviewid = htmlspecialchars(trim($_GET['reviewid']));
$categoryid = htmlspecialchars(trim($_GET['categoryid']));
//项目信息
$sql_project = "SELECT `project_name`,`mould_name`,`customer_code`,`mould_no` FROM `db_mould_specification` WHERE `mould_specification_id` = '$specification_id'";
$result_project = $db->query($sql_project);
if($result_project ->num_rows){
	$mould_info = $result_project->fetch_assoc();
}
//查询对应项目类型的所有项目
if($categoryid){
	$sql_data = "SELECT `db_mould_check_data`.`id`,`db_mould_check_data`.`checkname` FROM `db_mould_check_data` INNER JOIN `db_mould_check_type` ON `db_mould_check_data`.`categoryid` = `db_mould_check_type`.`id` WHERE `db_mould_check_type`.`id` = '$categoryid'";
}else{
	$sql_data = "SELECT `id`,`checkname` FROM `db_mould_check_data` WHERE `degree` = 'B'";
	}
$result_data = $db->query($sql_data);
 //获取图片路径
 $image_file = explode('$',$info['image_path']);
 //去除最后一项
 array_pop($image_file);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
  th,td{height:30px;}
  img.not('#logo'){height:150px;}
  #table_list tr .nobor{border:none;background:white;}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script type="text/javascript" src="../js/view_img.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
 
  $("#submit").click(function(){
    var file = $('input[name = file]').val();
    var title = $.trim($('input[name = title]').val());
    var patt = /^.{1,8}$/;
    if(!title && file){
      alert('请填写标题');
      return false;
    }
    if(title){
      if(!patt.test(title)){
        alert('标题长度超过限制');
        return false;
      }
    }
  })
  //导出文件
  $('#export').live('click',function(){
    var reviewid = $("input[name=reviewid]").val();
    window.location.href = 'excel_design_review.php?reviewid='+reviewid;
  })

})
</script>
<title>工程设计-嘉泰隆</title>
</head>
<body>
<?php include "header.php"; ?>
<div id="table_list" style="width:85%;margin:0px auto">
  <?php if($action == "add" || $action == 'edit'){ ?>
  <form action="design_review_do.php" name="material_order" method="post" enctype="multipart/form-data">
   
    <table>
      <tr>
        <td rowspan="2" class="nobor"><img src='../jtl.png' width="100"></td>
        <th colspan="6" class="nobor" style="font-size:20px">
          模具更改联络单
        </th>
        <td class="nobor"></td>
      </tr>
      <tr>
        <td class="nobor" colspan="5"></td>
        <th class="nobor">文件编号：</th>
        <td class="nobor" style="text-align:left"><?php echo $document_no; ?></td>
      </tr>
      <tr>
        <th width="10%">客户代码</th>
        <td width="15%"><?php echo $mould_info['customer_code'] ?></td>
        <th width="10%">项目名称</th>
        <td width="15%"><?php echo $mould_info['project_name'] ?></td>
        <th width="10%">模具编号</th>
        <td width="15%">
          <?php echo $mould_info['mould_no'] ?>
          <input type="hidden" value="<?php echo $mould_info['mould_no'] ?>" name="mould_no" />  
        </td>
        
        <th width="10%">产品名称</th>
        <td width="15%"><?php echo $mould_info['mould_name'] ?></td>
      </tr>
    <tr> 
      <th>序号</th>  
      <th colspan="4">评审记录</th>
      <th>判定</th>
      <th>备注</th>
      <th>图片</th>
    </tr>
    <?php
    	$i = 1;
    	if($result_data->num_rows){
    		while($row_data = $result_data->fetch_assoc()){
    			$dataid = $row_data['id'];
    			//查找已经评审的信息
    			$sql_complete = "SELECT * FROM `db_design_review_list` WHERE `reviewid` = '$reviewid' AND `dataid` = '$dataid'";
    			$result_complete = $db->query($sql_complete);
    			$data_complete = array();
    			if($result_complete->num_rows){
    				$data_complete = $result_complete->fetch_assoc();
    			}
 
    ?>
    <tr>
       <td><?php echo $i; ?></td> 
       <td colspan="4"><?php echo $row_data['checkname'] ?></td>
       <input type="hidden" name="dataid[]" value="<?php echo $dataid; ?>" />
       <td>
       		<label>
       			<input type="radio" value="1" name="approval_<?php echo $dataid; ?>" <?php echo $data_complete['approval'] == '1'?'checked':''; ?> />
       		是
       		</label>
       		<label>
       			<input type="radio" value="0" name="approval_<?php echo $dataid; ?>" <?php echo $data_complete['approval'] == '0'?'checked':''; ?> />	
       			否
       		</label>
       	</td>
       <td>
       		<input type="text" class="input_txt" name="remark_<?php echo $dataid; ?>" value="<?php echo $data_complete['remark']  ?>" />
       </td>
       <td>
       		<?php echo $data_complete['image_path'] ?>	
       		<span></span>
       		<input type="file" name="image_<?php echo $dataid; ?>" onchange="view_data(this)" />
       </td>
    </tr>
    <?php
    	$i++;
     }} ?>
    <tr>
        <td colspan="8">
          <!-- <input type="button"  id="export" value="导出" class="button"> -->
          <input type="submit"  value="确定" class="button" />
          <input type="hidden" name="specification_id" value="<?php echo $_GET['specification_id'] ?>" />
          <input type="hidden" name="document_no" vlaue="<?php echo $document_no; ?>" />
          <input type="hidden" name="reviewid" value="<?php echo $reviewid ?>" />
          <input type="hidden"  name="submit" value="确定" />
          <input type="button" value="返回" class="button" onclick="javascript:window.history.go(-1);" />
        </td>
      </tr>
    </table>
   </div>
  </form>
  <?php

  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>