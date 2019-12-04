<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$material_typename = trim($_GET['material_typename']);
  $sqlwhere = " WHERE `name` LIKE '%$material_typename%'";
}
$sql = "SELECT * FROM `db_project_review_data` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `id` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
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
<script type="text/javascript">
  $(function(){
    $('.sort').dblclick(function(){
      $('#sort_val').remove();
      var inp = '<input type="text" id="sort_val" size="5"/>';
      $(this).html(inp);
      $('#sort_val').focus();
      
    }).mouseover(function(){
      $(this).css('cursor','pointer');
    })
    $('#sort_val').live('blur',function(){
      var id = $(this).parent().attr('id');
      var checkid = id.substr(id.lastIndexOf('_')+1);
      var sort = $('#sort_val').val();
      $.post('../ajax_function/switch_check_sort.php',{id:checkid,sort:sort},function(data){
        window.location.reload();
      })
    })
  })
</script>
<title>基础数据-嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>项目评审项目</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>项目名称：</th>
        <td><input type="text" name="material_typename" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='project_reviewae.php?action=add'" />
          <input type="text" style="display:none;" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="project_review_do.php" name="material_type_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="60%">项目名称</th>
        <th width="5%">排序</th>
        <th width="4%">Edit</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $id = $row['id'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $id; ?>" /></td>
        <td><?php echo $row['name']; ?></td>
        <td class="sort" id="sort_<?php echo $id ?>"><?php echo $row['sort']; ?></td>
        <td width="4%"><a href="project_reviewae.php?id=<?php echo $id; ?>&action=edit&type=<?php echo $_GET['type'] ?>&page=<?php echo $_GET['page'] ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
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