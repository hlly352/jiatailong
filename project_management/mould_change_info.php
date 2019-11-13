<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$specification_id = $_GET['specification_id'];
if($_GET['submit']){
  $document_no = trim($_GET['document_no']);
  $specification_id = trim($_GET['specification_id']);
  $sqlwhere = "AND `db_mould_change`.`document_no` LIKE '%$document_no%'";
}
//查询当前模具的所有模具更改联络单
$sql_change = "SELECT *,`db_designer`.`employee_name` AS `designer`,`db_engnieer`.`employee_name` AS `engnieer`,`db_check`.`employee_name` AS `check`,`db_approval`.`employee_name` AS `approval` FROM `db_mould_change` INNER JOIN `db_mould_specification` ON `db_mould_change`.`specification_id` = `db_mould_specification`.`mould_specification_id` LEFT JOIN `db_employee` AS `db_designer` ON `db_designer`.`employeeid` = `db_mould_change`.`designer` LEFT JOIN `db_employee` AS `db_engnieer` ON `db_engnieer`.`employeeid` = `db_mould_change`.`engnieer` LEFT JOIN `db_employee` AS `db_check` ON `db_check`.`employeeid` = `db_mould_change`.`check` LEFT JOIN `db_employee` AS `db_approval` ON `db_approval`.`employeeid` = `db_mould_change`.`approval` WHERE `db_mould_change`.`specification_id` = '$specification_id' $sqlwhere  ORDER BY `db_mould_change`.`document_no` DESC";

$result_change = $db->query($sql_change);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
  th,td{height:30px;}
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
<title>项目管理-嘉泰隆</title>
</head>
<body>
<?php include "header.php"; ?>
<div id="table_search">
  <form action="" method="get">
    <table>
      <tr>
        <th>文件编号：</th>
        <td>
          <input type="text" name="document_no" class="input_txt">
        </td>
        <td>
           <input type="submit" name="submit" value="查询" class="button" />
           <input type="hidden" value="<?php echo $action; ?>" name="action" />
           <input type="hidden" value="<?php echo $specification_id; ?>" name="specification_id" />
        </td>
      </tr>
    </table>
   
  </form>
</div>
  <?php if($action == 'show'){
    if($result_change->num_rows){
      while($info = $result_change->fetch_assoc()){
        $document_no = $info['document_no'];
        //获取资料内容
         $array_content = array();
         if(stripos($info['data_content'],'&&')){
          $array_content = explode('&&',$info['data_content']);
         }else{
          $array_content[] = $info['data_content']; 
         }
        //获取图片路径
       $image_file = explode('$',$info['image_path']);
       //去除最后一项
       array_pop($image_file);
        //获取文档用途
        $array_use = explode('&&',$info['document_use']);
        $special_require = $info['special_require'];
        //查询接收人员
       $geter = $info['geter'];
       $sql_employee = "SELECT deptid,GROUP_CONCAT(`employee_name`) AS `geter` FROM `db_employee` WHERE `employeeid` IN($geter) GROUP BY `deptid`";
       $result_employee = $db->query($sql_employee);
   ?>
<div id="table_list" style="width:85%;margin:0px auto">
  <form action="mould_change_do.php" name="material_order" method="post" enctype="multipart/form-data">
    
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
        <th width="9%">客户代码</th>
        <td width="14%"><?php echo $info['customer_code'] ?></td>
        <th width="9%">项目名称</th>
        <td width="14%"><?php echo $info['project_name'] ?></td>
        <th width="9%">模具编号</th>
        <td width="14%"><?php echo $info['mould_no'] ?></td>
        <th width="9%">产品名称</th>
        <td width="14%"><?php echo $info['mould_name'] ?></td>
      </tr>
       <tr>
        <th>资料内容</th>
        <td colspan="7" style="text-align:left">
          <?php foreach($array_content as $content): 
            echo $array_data_content[$content].'  ';
           endforeach ?>
        </td>
      </tr>
       <tr>
        <th> 修改零件编号</th>
        <td colspan="3"><?php echo $info['change_parts'] ?></td>
        <th>取消零件编号</th>
        <td colspan="3"><?php echo $info['cancel_parts'] ?></td>
      </tr>
      <tr>
        <td colspan="8" style="height:150px;padding-top:10px;text-align:left">
          <p style="text-align:left;background:white">
            修改内容贴图及说明：
          </p>
           <?php
            foreach($image_file as $k=>$v){
              $image_info = explode('##',$v);
              echo '<div style="float:left;margin-left:20px" class="mould_image" style="margin-left:10px"><img width="510" height="230" src='.$image_info[0].' ><span style="display:block;text-align:center">'.$image_info[1].'</span></div>';
            }
           ?>
        </td>
      </tr>
      <tr>
        <td>
          以上所有图档
        </td>
        <td colspan="7" style="text-align:left;padding-right:10px">
          <?php
            foreach($array_use as $use):
              echo $array_mould_change_use[$use].'  ';
              echo $use == 'A'?$special_require:'';
            endforeach;
          ?>
        </td>
      </tr>
      <tr>
        <td>
          图档位置：
        </td>
        <td colspan="7" style="padding-right:10px;text-align:left">
          <?php echo $info['document_location']; ?>
        </td>
      </tr>
       <tr>
        <th>原图设计师</th>
        <td><?php echo $info['designer']; ?></td>
        <th>更改工程师</th>
        <td><?php echo $info['engnieer'] ?></td>
        <th>审核</th>
        <td><?php echo $info['check'] ?></td>
        <th>批准</th>
        <td><?php echo $info['approval'] ?></td>
      </tr>
      <tr>
      <th>接收部门</th>
        <td colspan="7" style="text-align:left">
        <?php
            if($result_employee->num_rows){
            $array_geter = array();
            while($row_employee = $result_employee->fetch_assoc()){
              echo $array_data_dept[$row_employee['deptid']].'('.$row_employee['geter'].')  ';
            }
           }
        ?>
        </td>
      </tr>     
      <tr>
        <td>签收部门：</td>
        <td colspan="7" style="text-align:left">
          <?php foreach ($array_data_dept as $key => $value): ?>
              <?php echo $value.':'; ?>
              <span style="padding:40px"></span>
          <?php endforeach ?>
        </td>
      </tr>
      <tr>
        <td colspan="8">
          <input type="button" value="返回" class="button" onclick="javascript:window.history.go(-1);" />
        </td>
      </tr>
    </table>
   </div>
  </form>

</div>
  <?php

  }}}
  ?>
<?php include "../footer.php"; ?>
</body>
</html>