<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_fileid = $_POST['id'];
	$fileid = fun_convert_checkbox($array_fileid);
	$sql_file = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `fileid` IN ($fileid)";
	$result_file = $db->query($sql_file);
	if($result_file->num_rows){
		while($row_file = $result_file->fetch_assoc()){
			$filepath = "../upload/file/".$row_file['filedir'].'/'.$row_file['filename'];
			fun_delfile($filepath);
		}
	}
	$sql = "DELETE FROM `db_upload_file` WHERE `fileid` IN ($fileid)";
	$db->query($sql);
	header("location:".$_SERVER['HTTP_REFERER']);
}
?>