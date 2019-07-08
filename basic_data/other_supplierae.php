<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
//读取供应商类型
$sql_supplier_type = "SELECT * FROM `db_supplier_type` ORDER BY `supplier_typecode` ASC, `supplier_typeid` ASC";
$result_supplier_type = $db->query($sql_supplier_type);
//读取供应商类型
$sql_supplier_business_type = "SELECT * FROM `db_supplier_business_type` ORDER BY `business_typeid` ASC";
$result_supplier_business_type = $db->query($sql_supplier_business_type);
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
<script language="javascript">
$(function(){
	$("#submit").click(function(){
		var supplier_cname = $("#supplier_cname").val();
		if(!$.trim(supplier_cname)){
			$("#supplier_cname").focus();
			return false;
		}
		var action = $("#action").val();
		var supplier_code = $("#supplier_code").val();
		if(action == 'edit' && (!zimu_reg.test(supplier_code) && !ri_a.test(supplier_code))){
			$("#supplier_code").focus();
			return false;
		}
		var supplier_name = $("#supplier_name").val();
		if(!$.trim(supplier_name)){
			$("#supplier_name").focus();
			return false;
		}
		var supplier_typeid = $('input[id=supplier_typeid]:checked').length;
		if(!supplier_typeid){
			alert('至少选择一种类型');
			return false;
		}
	})
})
</script>
<title>基础数据-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php if($action == "add"){ ?>
  <h4>期间物料供应商添加</h4>
  <form action="other_supplierdo.php" name="supplier" method="post">
    <table>
      <tr>
        <th width="20%">中文名：</th>
        <td width="80%"><input type="text" name="supplier_cname" id="supplier_cname" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>英文名：</th>
        <td><input type="text" name="supplier_ename" class="input_txt" /></td>
      </tr>
      <tr>
        <th>全称：</th>
        <td><input type="text" name="supplier_name" id="supplier_name" class="input_txt" size="35" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>地址：</th>
        <td><input type="text" name="supplier_address" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><?php
          foreach($array_mould_other_material as $k=>$v){
				echo "  <label><input type=\"checkbox\" name=\"supplier_type[]\" id=\"supplier_typeid\" value=\"".$k."\" />".$v."</label> ";
			
		    }
		    ?>
      
    </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }elseif($action == "edit"){
	  $supplierid = fun_check_int($_GET['id']);
	  $sql = "SELECT * FROM `db_other_supplier` WHERE `other_supplier_id` = '$supplierid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $supplier_typeid = $array['supplier_typeid'];
		  $business_typeid = $array['business_typeid'];
		  $array_supplier_typeid = explode(',',$supplier_typeid);
		  $array_supplier_business_typeid = explode(',',$business_typeid);
  ?>
  <h4>期间物料供应商修改</h4>
  <form action="other_supplierdo.php" name="supplier" method="post">
    <table>
      <tr>
        <th width="20%">中文名：</th>
        <td width="80%"><input type="text" name="supplier_cname" id="supplier_cname" value="<?php echo $array['supplier_cname']; ?>" class="input_txt" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>中文代码：</th>
        <td><input type="text" name="supplier_code" id="supplier_code" value="<?php echo $array['supplier_code']; ?>" class="input_txt" />
        <span class="tag"> *必填，单个字母或数字</span></td>
      </tr>
      <tr>
        <th>英文名：</th>
        <td><input type="text" name="supplier_ename" value="<?php echo $array['supplier_ename']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>全称：</th>
        <td><input type="text" name="supplier_name" id="supplier_name" value="<?php echo $array['supplier_name']; ?>" class="input_txt" size="35" />
          <span class="tag"> *必填</span></td>
      </tr>
      <tr>
        <th>地址：</th>
        <td><input type="text" name="supplier_address" value="<?php echo $array['supplier_address']; ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><?php
          foreach($array_mould_other_material as $k=>$v){

            if(substr_count($array['supplier_type'],$k) == 0){
              $is_check = '';
            }else{
              $is_check = 'checked';
            }
        echo "  <label><input type=\"checkbox\" ".$is_check." name=\"supplier_type[]\" id=\"supplier_typeid\" value=\"".$k."\" />".$v."</label> ";
      
        }
        ?>
      
        </td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="supplierid" value="<?php echo $supplierid; ?>" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>