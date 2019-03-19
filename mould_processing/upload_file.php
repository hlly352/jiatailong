<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$type = trim($_GET['type']);
$linkid = fun_check_int($_GET['id']);
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
<title>模具加工-苏州嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($type == 'MW'){
	  $sql = "SELECT `db_mould_weld`.`part_number`,`db_mould_weld`.`order_date`,`db_mould_weld`.`order_number`,`db_mould_weld`.`applyer`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname` FROM `db_mould_weld` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_weld`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_weld`.`supplierid` WHERE `db_mould_weld`.`weldid` = '$linkid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>烧焊资料上传</h4>
  <form action="upload_filedo.php" name="upload_file" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><?php echo $array['mould_number']; ?></td>
      </tr>
      <tr>
        <th>零件编号：</th>
        <td><?php echo $array['part_number']; ?></td>
      </tr>
      <tr>
        <th>外发时间：</th>
        <td><?php echo $array['order_date']; ?></td>
      </tr>
      <tr>
        <th>外协单号：</th>
        <td><?php echo $array['order_number']; ?></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><?php echo $array['supplier_cname']; ?></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><?php echo $array['applyer']; ?></td>
      </tr>
      <tr>
        <th>资料文件：</th>
        <td><input type="file" name="file[]" class="input_files" />
          <br />
          <input type="file" name="file[]" class="input_files" />
          <br />
          <input type="file" name="file[]" class="input_files" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="linkcode" value="<?php echo $type; ?>" />
          <input type="hidden" name="linkid" value="<?php echo $linkid; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }elseif($type == 'MO'){
	  $sql = "SELECT `db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`applyer`,`db_mould_outward`.`supplierid`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname` FROM `db_mould_outward` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` LEFT JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_outward`.`supplierid` WHERE `db_mould_outward`.`outwardid` = '$linkid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
      $supplier_cname = $array['supplierid'] ? $array['supplier_cname'] : '--';
  ?>
  <h4>外发资料上传</h4>
  <form action="upload_filedo.php" name="upload_file" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><?php echo $array['mould_number']; ?></td>
      </tr>
      <tr>
        <th>零件编号：</th>
        <td><?php echo $array['part_number']; ?></td>
      </tr>
      <tr>
        <th>外协时间：</th>
        <td><?php echo $array['order_date']; ?></td>
      </tr>
      <tr>
        <th>外协单号：</th>
        <td><?php echo $array['order_number']; ?></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><?php echo $supplier_cname; ?></td>
      </tr>
      <tr>
        <th>申请人：</th>
        <td><?php echo $array['applyer']; ?></td>
      </tr>
      <tr>
        <th>资料文件：</th>
        <td><input type="file" name="file[]" class="input_files" />
          <br />
          <input type="file" name="file[]" class="input_files" />
          <br />
          <input type="file" name="file[]" class="input_files" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="linkcode" value="<?php echo $type; ?>" />
          <input type="hidden" name="linkid" value="<?php echo $linkid; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }elseif($type == 'MT'){
	  $sql = "SELECT `db_mould_try`.`order_number`,`db_mould_try`.`try_date`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_try`.`supplierid` WHERE `db_mould_try`.`tryid` = '$linkid'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>试模资料上传</h4>
  <form action="upload_filedo.php" name="upload_file" method="post" enctype="multipart/form-data">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><?php echo $array['mould_number']; ?></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><?php echo $array['supplier_cname']; ?></td>
      </tr>
      <tr>
        <th>送货单号：</th>
        <td><?php echo $array['order_number']; ?></td>
      </tr>
      <tr>
        <th>试模日期：</th>
        <td><?php echo $array['try_date']; ?></td>
      </tr>
      <tr>
        <th>资料文件：</th>
        <td><input type="file" name="file[]" class="input_files" />
          <br />
          <input type="file" name="file[]" class="input_files" />
          <br />
          <input type="file" name="file[]" class="input_files" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="linkcode" value="<?php echo $type; ?>" />
          <input type="hidden" name="linkid" value="<?php echo $linkid; ?>" /></td>
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
<?php
$sql_file = "SELECT `db_upload_file`.`fileid`,`db_upload_file`.`filedir`,`db_upload_file`.`filename`,`db_upload_file`.`upfilename`,`db_upload_file`.`dotime`,`db_employee`.`employee_name` FROM `db_upload_file` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_upload_file`.`employeeid` WHERE `db_upload_file`.`linkcode` = '$type' AND `db_upload_file`.`linkid` = '$linkid' ORDER BY `db_upload_file`.`fileid` ASC";
$result_file = $db->query($sql_file);
?>
<div id="table_list">
  <?php if($result_file->num_rows){ ?>
  <form action="../upload/upload_filedo.php" name="list" method="post">
    <table>
      <caption>
      文件列表
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th>文件名称</th>
        <th width="10%">文件大小</th>
        <th width="10%">上传人</th>
        <th width="10%">上传时间</th>
        <th width="4%">Down</th>
      </tr>
      <?php
      while($row_file = $result_file->fetch_assoc()){
		  $fileid = $row_file['fileid'];
		  $filedir = $row_file['filedir'];
		  $filename = $row_file['filename'];
		  $file_path = "../upload/file/".$filedir.'/'.$filename;
		  $file_path_url = "/upload/file/".$filedir.'/'.$filename;
		  $filesize = (is_file)?fun_sizeformat(filesize($file_path)):0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $fileid; ?>" /></td>
        <td><?php echo $row_file['upfilename']; ?></td>
        <td><?php echo $filesize; ?></td>
        <td><?php echo $row_file['employee_name']; ?></td>
        <td><?php echo $row_file['dotime']; ?></td>
        <td><a href="../upload/download_file.php?id=<?php echo $fileid; ?>"><img src="../images/system_ico/download_10_10.png" width="10" height="10" /></a></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
    </div>
  </form>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>