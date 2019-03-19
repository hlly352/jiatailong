<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$mould_name = trim($_GET['mould_name']);
	$sqlwhere = " WHERE `mould_name` LIKE '%$mould_name%'";
}
$sql = "SELECT * FROM `db_mould_data` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `mould_dataid` DESC" . $pages->limitsql;
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>模具报价-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>模具数据</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具名称</th>
        <td><input type="text" name="mould_name" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查找" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='mould_dataae.php?action=add'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_mould_dataid .= $row_id['mould_dataid'].',';
	  }
	  $array_mould_dataid = rtrim($array_mould_dataid,',');
	  $sql_group = "SELECT `mould_dataid`,COUNT(*) AS `count` FROM `db_mould_quote` WHERE `mould_dataid` IN ($array_mould_dataid) AND `quote_status` = 1 GROUP BY `mould_dataid`";
	  $result_group = $db->query($sql_group);
	  if($result_group->num_rows){
		  while($row_group = $result_group->fetch_assoc()){
			  $array_group[$row_group['mould_dataid']] = $row_group['count'];
		  }
	  }else{
		  $array_group = array();
	  }
  ?>
  <form action="mould_datado.php" name="list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">模具名称</th>
        <th width="8%">产品图片</th>
        <th width="8%">型腔数量</th>
        <th width="10%">产品零件号</th>
        <th width="10%">产品尺寸</th>
        <th width="8%">产品重量(g)</th>
        <th width="10%">模具尺寸</th>
        <th width="8%">模具重量(Kg)</th>
        <th width="8%">客户名称</th>
        <th width="8%">项目名称</th>
        <th width="4%">报价</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $mould_dataid = $row['mould_dataid'];
		  $image_filedir = $row['image_filedir'];
		  $image_filename = $row['image_filename'];
		  $image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
		  if(is_file($image_filepath)){
			  $image_file = "<img src=\"".$image_filepath."\" />";
		  }else{
			  $image_file = "<img src=\"../images/no_image_85_45.png\" width=\"85\" height=\"45\" />";
		  }
		  $count = array_key_exists($mould_dataid,$array_group)?$array_group[$mould_dataid]:0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $mould_dataid; ?>"<?php if($count > 0) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['mould_name']; ?></td>
        <td><a href="mould_photo.php?id=<?php echo $mould_dataid; ?>"><?php echo $image_file; ?></a></td>
        <td><?php echo $array_mould_cavity_type[$row['cavity_type']]; ?></td>
        <td><?php echo $row['part_number']; ?></td>
        <td><?php echo $row['p_length'].'*'.$row['p_length'].'*'.$row['p_length']; ?></td>
        <td><?php echo $row['p_weight']; ?></td>
        <td><?php echo $row['m_length'].'*'.$row['m_length'].'*'.$row['m_length']; ?></td>
        <td><?php echo $row['m_weight']; ?></td>
        <td><?php echo $row['client_name']; ?></td>
        <td><?php echo $row['project_name']; ?></td>
        <td><a href="mould_quote_list.php?id=<?php echo $mould_dataid; ?>"><img src="../images/system_ico/quote_11_12.png" width="11" height="12" /></a></td>
        <td><?php if($count == 0){ ?><a href="mould_dataae.php?id=<?php echo $mould_dataid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>