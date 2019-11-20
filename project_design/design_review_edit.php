<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = fun_check_action($_GET['action']);
$specification_id = $_GET['specification_id'];
$reviewid = $_GET['reviewid'];
//查询模号
$sql_mould_no = "SELECT `mould_no` FROM `db_mould_specification` WHERE `mould_specification_id` = '$specification_id'";
$result_mould_no = $db->query($sql_mould_no);
if($result_mould_no->num_rows){
  $mould_no = $result_mould_no->fetch_assoc()['mould_no'];
}
//查询子分类信息
function search_category($db,$pid){
  $sql = "SELECT `db_mould_check_type`.`id`,`db_mould_check_type`.`pid`,`db_mould_check_type`.`path`,`db_mould_check_type`.`typename`,COUNT(a.`id`) AS `count` FROM `db_mould_check_type` INNER JOIN `db_mould_check_type` a ON `db_mould_check_type`.`id` = a.`path` WHERE `db_mould_check_type`.`pid` = '$pid' GROUP BY `db_mould_check_type`.`id`";
  //查询当前类所对应的项目汇总信息
  //$sql = "SELECT `db_mould_check_type`.`id`,COUNT(`db_mould_check_data`.`id`) AS `count` FROM `db_mould_check_data` INNER JOIN `db_mould_check_type` ON `db_mould_check_data`.`categoryid` = `db_mould_check_type`.`id` WHERE `db_mould_check_type`.`pid` = '$pid' GROUP BY `db_mould_check_data`.`id`";
  $result = $db->query($sql);
  if($result->num_rows){
    while($row = $result->fetch_assoc()){
      search_category($db,$row['pid']);
    }
  }else{

  }
}

//判断是否有评审表
if($reviewid){
  $mould_sql = "SELECT *,`db_mould_specification`.`cavity_num`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`mould_name`,`db_design_review`.`surface_require`,`db_design_review`.`projecter`,`db_design_review`.`designer`,`db_design_review`.`mould_coefficient` FROM `db_mould_specification` LEFT JOIN `db_design_review` ON `db_mould_specification`.`mould_specification_id` = `db_design_review`.`specification_id` WHERE `db_mould_specification`.`mould_specification_id` = '$specification_id'";
}else{
    //查询文件编号
    $sql_max = "SELECT MAX(SUBSTRING(`document_no`,-3)+0) AS `max_number` FROM `db_design_review` WHERE `specification_id` = '$specification_id'";
    $result_max = $db->query($sql_max);
    if($result_max->num_rows){
        $max_number = $result_max->fetch_assoc()['max_number'];
        $document_number = $max_number + 1;
        $document_no = $mould_no.'_'.date('Ymd').'_C'.strtolen($document_number,3).$document_number;
      }else{
        $document_no = $mould_no.'_C'.date('Ymd').'_001';
      }
    //新建评审表
     $mould_sql = "INSERT INTO `db_design_review`(`specification_id`,`document_no`,`employeeid`,`time`) VALUES('$specification_id','$document_no','$employeeid','".time()."')";
     $db->query($mould_sql);
     $reviewid = $db->insert_id;
}
//查询所有的评审项目信息
$sql_data = "SELECT `db_mould_check_data`.`categoryid`,GROUP_CONCAT(`db_mould_check_data`.`id`,'##',`db_mould_check_data`.`checkname`) AS `dataname`,`db_mould_check_type`.`typename` FROM `db_mould_check_data` INNER JOIN `db_mould_check_type` ON `db_mould_check_data`.`categoryid` = `db_mould_check_type`.`id` GROUP BY `db_mould_check_data`.`categoryid` ORDER BY `db_mould_check_data`.`categoryid` ASC,`db_mould_check_data`.`id` ASC";
// echo $sql_data;
$result_data = $db->query($sql_data);
//查找判定结果
$sql_review_list = "SELECT `db_design_review_list`.`dataid`,`db_design_review_list`.`remark`,`db_design_review_list`.`approval`,`db_design_review_list`.`image_path` FROM `db_design_review_list` INNER JOIN `db_design_review` ON `db_design_review_list`.`reviewid` = `db_design_review`.`reviewid` WHERE `db_design_review`.`specification_id` = '$specification_id' AND `db_design_review`.`reviewid` = '$reviewid'";
$result_review_list = $db->query($sql_review_list);
  $array_review_list = array();
if($result_review_list->num_rows){
  while($row_review_list = $result_review_list->fetch_assoc()){
    $array_review_list[$row_review_list['dataid']] = $row_review_list;
  }
}
var_dump($array_review_list);
 //获取图片路径
 $image_file = explode('$',$info['image_path']);
 //去除最后一项
 array_pop($image_file);
 //获取资料内容和接收部门
 $array_content = array();
 if(stripos($info['data_content'],'&&')){
  $array_content = explode('&&',$info['data_content']);
 }else{
  $array_content[] = $info['data_content']; 
 }
  $array_dept = array();
 if(stripos($info['data_dept'],'&&')){
  $array_dept = explode('&&',$info['data_dept']);
 }else{
  $array_dept[] = $info['data_dept']; 
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
  <?php if($result_data->num_rows){ ?>
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
    </table>
    <table>
      <tr>
        <th>类别</th>
        <th>序号</th>
        <th>评审记录</th>
        <th>判定</th>
        <th>备注</th>
        <th>图片</th>
      </tr>
    <?php
      $i = 1; 
      //获取所有的项目信息
      while($row_data = $result_data->fetch_assoc()){ 
        $array_data = explode(',',$row_data['dataname']);
        $rows = count($array_data);
      ?>
      <tr>
        <th rowspan="<?php echo ($rows+1); ?>"><?php echo $row_data['typename'] ?>
          <a href="design_review_info.php?action=edit&specification_id=<?php echo $specification_id; ?>&reviewid=<?php echo $reviewid; ?>&categoryid=<?php echo $row_data['categoryid'] ?>">(点击进入检查)</a>
        </th>
      </tr>
      <?php
        //获取大类里的详细项目
        foreach($array_data as $j => $v){
          $array_data_info = explode('##',$v);
          $dataid = $array_data_info[0];
          $checkname = $array_data_info[1];
      ?>
      <tr>
        <td><?php echo $i.'.'.($j+1); ?></td>
        <td id="<?php echo $dataid; ?>"><?php echo $checkname ?></td>
        <td><?php echo array_key_exists($dataid,$array_review_list)?$array_review_list[$dataid]['approval']:''; ?></td>
        <td><?php echo array_key_exists($dataid,$array_review_list)?$array_review_list[$dataid]['remark']:''; ?></td>
        <td><?php echo array_key_exists($dataid,$array_review_list)?$array_review_list[$dataid]['image_path']:''; ?></td>
      </tr>
     <?php }
     $i++;
     } ?>
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
    }else{
      echo '暂无项目';
    }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>