<?php
header("Content-type:text/html;charset=utf-8");
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$fileid = fun_check_int($_GET['id']); //获取文件ID
$sql_file = "SELECT `filedir`,`filename`,`upfilename` FROM `db_upload_file` WHERE fileid = '$fileid'";
$result_file = $db->query($sql_file);
$array_file = $result_file->fetch_assoc();
$file_path = "../upload/file/".$array_file['filedir']."/".$array_file['filename']; //文件真实路径
$file_downname = $array_file['upfilename']; //获取文件下载名
$encoded_filename = urlencode($file_downname); //将字符串以URL编码
$encoded_filename = str_replace("+", "%20", $encoded_filename);
$browser = $_SERVER["HTTP_USER_AGENT"]; //获取浏览器信息
if(file_exists($file_path)){
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: '.filesize($file_path));
	if(preg_match("/MSIE/", $browser)){
		header('Content-Disposition: attachment; filename="'.$encoded_filename.'"');
	}elseif(preg_match("/Firefox/", $browser)){
		header('Content-Disposition: attachment; filename*="utf8\'\''.$file_downname.'"');
	}else{
		header('Content-Disposition: attachment; filename="'.$file_downname.'"');
	}
    ob_clean();
    flush();
	readfile($file_path);
    exit;
}else{
	echo "文件不存在";
}
?>