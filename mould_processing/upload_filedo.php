<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
if($_POST['submit']){
	$linkid = $_POST['linkid'];
	$linkcode = $_POST['linkcode'];
	$filedir = date("Ymd");
	$upload_path = "../upload/file/" . $filedir . "/";
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($_FILES['file']['name']){	
		$upload = new upload();
		$upload->upload_files($upload_path);
		if(is_array($array_upload_files = $upload->array_upload_files)){
			for($i=0; $i<count($array_upload_files); $i++){
				$filename = $array_upload_files[$i]['upload_final_name'];
				$upfilename = $array_upload_files[$i]['upload_name'];
				$sql_file = "INSERT INTO `db_upload_file` (`fileid`,`filedir`,`filename`,`upfilename`,`employeeid`,`dotime`,`linkcode`,`linkid`) VALUES (NULL,'$filedir','$filename','$upfilename','$employeeid','$dotime','$linkcode','$linkid')";
				$db->query($sql_file);
			}
		}
	}
	header("location:".$_SERVER['HTTP_REFERER']);
}
?>