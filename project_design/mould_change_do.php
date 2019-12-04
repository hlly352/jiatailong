<meta http-equiv="content-Type" content="text/html;charset=utf-8">
<?php
	require_once '../global_mysql_connect.php';
	require_once '../function/function.php';
	require_once '../config/config.php';
	require_once '../class/upload.php';
	require_once 'shell.php';
	date_default_timezone_set("Asia/chongqing");
error_reporting(E_ERROR);
header("Content-Type: text/html; charset=utf-8");

$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("../js/php/config.json")), true);
$action = $_GET['action'];

switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* 上传图片 */
    case 'uploadimage':
    /* 上传涂鸦 */
    case 'uploadscrawl':
    /* 上传视频 */
    case 'uploadvideo':
    /* 上传文件 */
    case 'uploadfile':
        $result = include("../js/php/action_upload.php");
        break;

    /* 列出图片 */
    case 'listimage':
        $result = include("../js/php/action_list.php");
        break;
    /* 列出文件 */
    case 'listfile':
        $result = include("../js/php/action_list.php");
        break;

    /* 抓取远程文件 */
    case 'catchimage':
        $result = include("../js/php/action_crawler.php");
        break;

    default:
        $result = json_encode(array(
            'state'=> '请求地址出错'
        ));
        break;
}

/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参数不合法'
        ));
    }
} else {
    echo $result;
}
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$data = $_POST;
	if($_POST['submit']){
	$array_geter = $data['employeeid'];
	$geter = fun_convert_checkbox($array_geter);
	$document_no = $data['document_no'];
	$document_use = $data['document_use'];
	$special_require = $data['special_require'];
	$document_location = str_replace('\\','/',trim($data['document_location']));
	$specification_id = $data['specification_id'];
	$changeid = $data['changeid'];
	$mould_no = $data['mould_no'];
	$designer = $data['designer'];
	$image_path = htmlspecialchars(trim($data['editorValue']));
	$check    = $data['check'];
	$approval = $data['approval'];
	$engnieer = $data['engnieer'];
	$tips = trim($data['tips']);
	$data_content = $data['data_content'];
	$data_dept = $data['data_dept'];
	$change_parts = $data['change_parts'];
	$cancel_parts = $data['cancel_parts'];
	if($data_content){
		$data_content = implode('&&',$data_content);
	}
	if($data_dept){
		$data_dept = implode('&&',$data_dept);
	}
	if($document_use){
		$document_use = implode('&&',$document_use);
	}
		// $file = $_FILES['file'];
		// //判断是否接收到图片
		// $image_path = ' ';
		// if($file['name'][0] != null){
		// 	//拼接图片存储路径
		//     $filedir = date("Ymd");
		// 	$upfiledir = "../upload/technical_other/".$filedir."/";
		// 	 //得到传输的数据
		// 		 if(($_FILES['file']['tmp_name'][0]) != null){
		// 			if($_FILES['file']['name']){
		// 				 //图片上传
		// 				$upload = new upload();
		// 				$upload->upload_files($upfiledir);
		// 				$target_path =  '';
		// 				$target_name = '';
		// 				$final_path = '';
		// 				$upload_final_path = '';
		// 				//图片上传后得到图片的信息
		// 				$upload_info = $upload->array_upload_files;
		// 				//从图片信息中提取图片的存储路径
		// 				foreach($upload_info as $key=>$value){
		// 					foreach($value as $ks=>$vs){
		// 					if($ks == 'upload_target_path'){
		// 						$target_path = $vs;
		// 					} elseif($ks == 'upload_final_name'){
		// 						$target_name = $vs;
		// 					}
		// 					$final_path = $target_path.$target_name;
		// 				}
		// 				$image_path .= $final_path.'**'.$data['pic_remark'][$key].'$';
		// 				}
		// 			}
		// 		}
		// 	}
		// 	$image_path = trim($image_path);

		$date = date('Y-m-d');
	if(empty($changeid)){
		//插入新的图纸联络单
		$sql_change = "INSERT INTO `db_mould_change`(`specification_id`,`designer`,`engnieer`,`approval`,`check`,`data_content`,`data_dept`,`change_parts`,`cancel_parts`,`image_path`,`document_no`,`document_use`,`special_require`,`document_location`,`employeeid`,`geter`,`dodate`,`tips`) VALUES('$specification_id','$designer','$engnieer','$approval','$check','$data_content','$data_dept','$change_parts','$cancel_parts','$image_path','$document_no','$document_use','$special_require','$document_location','$employeeid','$geter','$date','$tips')";
		echo $sql_change;
		$db->query($sql_change);
		$changeid = $db->insert_id;
		header('location:mould_change.php');
		
	}else{
		$sql = "UPDATE `db_mould_change` SET `designer` = '$designer',`engnieer` = '$engnieer',`approval` = '$approval',`check` = '$check',`data_content` = '$data_content',`data_dept` = '$data_dept',`change_parts` = '$change_parts',`cancel_parts` ='$cancel_parts',`image_path` = '$image_path',`document_use` = '$document_use',`special_require` = '$special_require',`document_location` = '$document_location',`employeeid` = '$employeeid',`geter` = '$geter',`tips` = '$tips' WHERE `changeid` = '$changeid'";
		$db->query($sql);
		header('location:mould_change.php');
	}
	//判断是否审核人
	if($check > 0){
		//查找发件人
		$sql_send = "SELECT `email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
		$send = '';
		$result_send = $db->query($sql_send);
		if($result_send->num_rows){
			$send = $result_send->fetch_assoc()['email'];
		}
		$array_dept = $_POST['data_dept'];
		$depts = fun_convert_checkbox($array_dept);
		//查询收件人信息
		$sql_email = "SELECT `email` FROM `db_employee` WHERE `employeeid` IN($geter)";
		$result_email = $db->query($sql_email);
		$address = array();
		if($result_email->num_rows){
			while($row_email = $result_email->fetch_assoc()){
				$address[] = $row_email['mail'];
			}
		}

		$address[] = 'hr.04@hl.com';
		$subject = $mould_no.'的模具更改联络单';
		$body = '点击链接查看：<a href="'.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/project_design/mould_change_edit.php?action=edit&specification_id='.$specification_id.'&changeid='.$changeid.'">http://localhost/project_design/mould_change_edit.php?action=edit&specification_id=24&changeid=1</a>';
		send('hr.04@hl.com',$address,$subject,$body);
	}
}
?>