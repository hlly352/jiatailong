<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = $_GET['action'];
//接收图片信息
$file = $_FILES['file'];
$image = $_FILES['image'];
if($image){
		//拼接图片存储路径
    $filedir = date("Ymd");
	$upfiledir = "../upload/mould_image/".$filedir."/";
	$upload_name = $upfiledir.$image['name'];
	 //得到传输的数据
		 if(($_FILES['image']['tmp_name'][0]) != null){
			if($_FILES['image']['name']){
				 //图片上传
			
				if(is_uploaded_file($image['tmp_name'])){
					move_uploaded_file($image['tmp_name'],$upload_name);
				}
			}
		}
	//插入到模具资料中
	$sql_data = "UPDATE `db_mould_data` SET `upload_final_path`='".$upload_name."' WHERE `mould_dataid`=".$_POST['mould_id'];
	$db->query($sql_data);
	if($db->affected_rows){
		echo 'ok';
	}
}

//判断是否接收到图片
if($file){
	//拼接图片存储路径
    $filedir = date("Ymd");
	$upfiledir = "../upload/mould_image/".$filedir."/";
	 //得到传输的数据
		 if(($_FILES['file']['tmp_name'][0]) != null){
			if($_FILES['file']['name']){
				 //图片上传
				$upload = new upload();
				$upload->upload_files($upfiledir);
				$target_path =  '';
				$target_name = '';
				$final_path = '';
				$upload_final_path = '';
				//图片上传后得到图片的信息
				$upload_info = $upload->array_upload_files;
				//从图片信息中提取图片的存储路径
				foreach($upload_info as $key=>$value){
					foreach($value as $ks=>$vs){
			
					if($ks == 'upload_target_path'){
						$target_path = $vs;
					} elseif($ks == 'upload_final_name'){
						$target_name = $vs;
					}
					$final_path = $target_path.$target_name;
				}
				$upload_final_path .= $final_path.'$';
				

				}
			}
		}

}

//执行添加操作
if($action == 'add'){
	$data = $_POST;
	//遍历得到的结果
	$sql_key = ' ';
				foreach($data as $key=>$value){
				if(is_array($value)){
					$value = implode('$$',$value);
				}
				$sql_key .= '`'.$key.'`,';
				$sql_val .= '"'.$value.'",';
					
				}
				$sql_val .= '"'.$employeeid.'","'.time().'","'.$upload_final_path.'"';
				$sql_key .= '`employeeid`,`specification_time`,`upload_final_path`';
	
		
		
	
		//去除最后一个逗号
		$sql_value = substr($sql_value,0,strlen($sql_value)-1);
		 $specification_sql = "INSERT INTO `db_mould_specification`($sql_key) VALUES(".$sql_val.")";

		 $mould_data_sql = "UPDATE `db_mould_data` SET `is_start`='1' WHERE `mould_dataid`={$data['mould_id']}";
		
		//执行sql语句
		$db->query($specification_sql);
		if($db->affected_rows){
			$db->query($mould_data_sql);
			if($db->affected_rows){
				header('location:order_gather.php');
			} else {
				header('location:order_gather.php');
			}
		} else {
			header('location:order_gather.php');
			}
	} elseif($action == 'edit'){
		$data = $_POST;
		//获取项目的id值
		$id = $data['specification_id'];
		$from = $data['from'];
		unset($data['specification_id']);
		unset($data['from']);
		//把数组转换为字符串
		foreach($data as $k=>$v){
			if(is_array($v)){
				$data[$k] = implode('$$',$v);
			} else {
				$data[$k] = $v;
			}
		}
		
		//拼接sql语句
		$sql_word = '';
		foreach($data as $k=>$v){
			$sql_word .='`'.$k.'`="'.$v.'",';
		}
		//更新时间
		$sql_word .= '`specification_time`="'.time().'"';
		//若原来有图片，则把原来的图片删除
		if($upload_final_path){
			$sqls = "SELECT `upload_final_path` FROM `db_mould_specification` WHERE `mould_specification_id` =".$id;
			$res = $db->query($sqls);

			if($res->num_rows){
				$row = $res->fetch_row()[0];
				$file_path = explode('$',$row);
			     array_pop($file_path);
				
				foreach($file_path as $k=>$v){
					$upload->delfile($v);
					}
				}
				//把新的图片地址写入到数据库中
				$sql_word .= ',`upload_final_path`="'.$upload_final_path.'"';
			}

		$sql = "UPDATE `db_mould_specification` SET $sql_word WHERE `mould_specification_id`=".$id;
		
		$db->query($sql);
		if($db->affected_rows){
			
			if($from == 'summary'){
				header('location:../project_management/project_summary.php');
			} else{
				header('location:../project_management/new_project.php');
			}
		}

	}

?>