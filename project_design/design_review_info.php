<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$from = $_GET['from'];
$action = fun_check_action($_GET['action']);
$specification_id = htmlspecialchars(trim($_GET['specification_id']));
$reviewid = htmlspecialchars(trim($_GET['reviewid']));
$categoryid = htmlspecialchars(trim($_GET['categoryid']));
  //查找文件号
  $sql_document = "SELECT `document_no` FROM `db_design_review` WHERE `reviewid` = '$reviewid'";
  $result_document = $db->query($sql_document);
  if($result_document->num_rows){
    $document_no = $result_document->fetch_assoc()['document_no'];
  }


//项目信息
$sql_project = "SELECT `project_name`,`mould_name`,`customer_code`,`mould_no` FROM `db_mould_specification` WHERE `mould_specification_id` = '$specification_id'";
$result_project = $db->query($sql_project);
if($result_project ->num_rows){
  $mould_info = $result_project->fetch_assoc();
}
//查询对应项目类型的所有项目
if($categoryid){
  if($categoryid == 'eng'){
    $sql_data = "SELECT `db_mould_check_data`.`id`,`db_mould_check_data`.`checkname` FROM `db_mould_check_data` INNER JOIN `db_design_review_list` ON `db_mould_check_data`.`id` = `db_design_review_list`.`dataid` WHERE `db_design_review_list`.`approval` = '0' AND `db_design_review_list`.`reviewid` = '$reviewid'";
  }else{
    $sql_data = "SELECT `db_mould_check_type`.`typename`,`db_mould_check_data`.`id`,`db_mould_check_data`.`checkname` FROM `db_mould_check_data` INNER JOIN `db_mould_check_type` ON `db_mould_check_data`.`categoryid` = `db_mould_check_type`.`id` WHERE `db_mould_check_type`.`id` = '$categoryid' ORDER BY `db_mould_check_data`.`sort`";
  }
}else{
  $sql_data = "SELECT `id`,`checkname` FROM `db_mould_check_data` WHERE `degree` = 'B'";
  }
$result_data = $db->query($sql_data);
$result_datas = $db->query($sql_data);
if($result_datas->num_rows){
  $title = $result_datas->fetch_assoc()['typename'];
}
  if($title){
    $title = $title;
  }
  if($categoryid){
    if($categoryid == 'eng'){
      $title = '未通过项目';
    }else{
      $title = $title;
    }
  }else{
    $title = '评审会项目';
  }
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
<script type="text/javascript" charset="utf-8" src="../js/utf8-php/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="../js/utf8-php/ueditor.all.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8" src="../js/utf8-php/lang/zh-cn/zh-cn.js"></script>
    <style type="text/css">
        div{
            width:100%;
        }
    </style>
<script language="javascript" type="text/javascript">
$(function(){
 
  $("#submit").click(function(){
    //判定的项目添加dataid
    var num = $('.checkname').size();
    for(var i = 0;i < num; i++){
      var id = $('.checkname').eq(i).attr('id');
      var dataid = id.substr(id.lastIndexOf('_')+1);
      var approval = $('input[name=approval_'+dataid+']:checked').val();
      if(approval){
        $('#dataid_'+dataid).val(dataid);
      }
    }
  })
  //导出文件
  $('#export').live('click',function(){
    var reviewid = $("input[name=reviewid]").val();
    window.location.href = 'excel_design_review.php?reviewid='+reviewid;
  })
  $('#back').live('click',function(){
    var from = '<?php echo $from; ?>';
    window.location.href = 'design_review'+from+'.php?action=edit&specification_id=<?php echo $specification_id; ?>&reviewid=<?php echo $reviewid; ?>';
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
   
    <table style="margin-bottom:20px">
      <tr>
        <td rowspan="2" class="nobor"><img src='../jtl.png' width="100"></td>
        <th colspan="6" class="nobor" style="font-size:20px">
          <?php echo $title; ?>
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
      <th colspan="3">评审目录</th>
      <th>判定</th>
      <th colspan="3">评审记录</th>
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
       <td colspan="3" class="checkname" style="text-align:left" id="checkname_<?php echo $dataid; ?>">
          <?php 
            $checkname =  $row_data['checkname'];
            echo str_pos($checkname,$db,$specification_id);
          ?>
       </td>
       <input type="hidden" id="dataid_<?php echo $dataid; ?>" name="dataid[]" />
       <td>
          <label>
            <input type="radio" value="1" name="approval_<?php echo $dataid; ?>" <?php echo $data_complete['approval'] == '1'?'checked':''; ?> />
            是
          </label>
          <label>
            <input type="radio" value="0" name="approval_<?php echo $dataid; ?>" <?php echo $data_complete['approval'] == '0'?'checked':''; ?> /> 
            否
          </label>
          <label>
            <input type="radio" value="2" name="approval_<?php echo $dataid; ?>" <?php
              echo $data_complete['approval'] == '2'?'checked':''; ?> />
            无
          </label>
        </td>
        <td colspan="3">
          <!-- <div>
            <script type="text/plain" id="container" name="content" style="width:200px;height:400px;"></script>
          </div> -->
          <script type="text/plain" id="remark_<?php echo $i; ?>" name="remark_<?php echo $dataid; ?>" style="width: 100%;height:150px;">
            <?php echo html_entity_decode($data_complete['remark']); ?>
          </script>
          <script type="text/javascript">
              UE.getEditor('remark_<?php echo $i ?>');
          </script>
        </td>
       <!-- <td>
            <input type="text" class="input_txt" name="remark_<?php echo $dataid; ?>" value="<?php echo $data_complete['remark']  ?>" />
        </td>
        <td colspan="2" style="text-align:left"> 
           <?php
          if($data_complete['image_path']){
            $array_image_path = explode('&&',$data_complete['image_path']);
            foreach($array_image_path as $path){
              $image_info = explode('**',$path);
            echo '<div style="float:left;width:46%;margin-left:2%;margin-bottom:10px" class="mould_image"><img width="100%" height="100px" src='.$image_info[0].' ><span style="display:block;text-align:center">'.$image_info[1].'</span></div>';
          }
         } ?>
          <input type="file" name="image_<?php echo $dataid; ?>[]" onchange="design_review(this,<?php echo $dataid; ?>)" />
          <div style="width:46%;float:left;margin-bottom:2%;margin-left:2%"></div>
       </td> -->
    </tr>
    <?php
      $i++;
     }} ?>
    <tr>
        <td colspan="8">
          <!-- <input type="button"  id="export" value="导出" class="button"> -->
          <input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="hidden" name="specification_id" value="<?php echo $_GET['specification_id'] ?>" />
          <input type="hidden" name="document_no" vlaue="<?php echo $document_no; ?>" />
          <input type="hidden" name="reviewid" value="<?php echo $reviewid ?>" />
          <input type="button" value="返回" class="button" id="back"/>
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