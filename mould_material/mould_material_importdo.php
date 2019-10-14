<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../phpExcelReader/Excel/reader.php';
require_once 'shell.php';
if($_POST['submit']){
	$mouldid = $_POST['mouldid'];
	$type = $_POST['type'];
	$material_date = fun_getdate();
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('CP936');
	if($_FILES['file']['type'] ==  "application/vnd.ms-excel" || $_FILES['file']['type'] == 'application/octet-stream'){ //判断文件是否为Excel03版本
		$filepath = ($_FILES['file']['tmp_name']); //获取文件临时文件名
		$data->read($filepath); //读取临时文件
		error_reporting(E_ALL ^ E_NOTICE);
		$array_material = $data->sheets[0]['cells']; //获取Excel文件数据
		if(is_array($array_material)){ //如果文件有数据
			//普通物料
			if($type == 'N'){
				foreach($array_material as $value){ //循环读取
					$material_list_number = trim($value[1]);
					$material_list_sn = (strlen($value[2])<2)?'0'.trim($value[2]):trim($value[2]);
					$material_number = iconv("GB2312","UTF-8//IGNORE",trim($value[3]));
					$material_name = str_replace_array(iconv("GB2312","UTF-8//IGNORE",trim($value[4])));
					$specification = str_replace_array(iconv("GB2312","UTF-8//IGNORE",trim($value[5])));
					$material_quantity = $value[6];
					$texture = iconv("GB2312","UTF-8//IGNORE",trim($value[7]));
					$hardness = iconv("GB2312","UTF-8//IGNORE",trim($value[8]));
					$brand = iconv("GB2312","UTF-8//IGNORE",trim($value[9]));
					$remark = str_replace_array(iconv("GB2312","UTF-8//IGNORE",trim($value[10])));
					$spare_quantity = trim($value[11]);
					if($material_number && $material_name && $specification && $material_quantity){
						$complete_status = ($material_list_number && $material_list_sn && $material_number && $material_name && $specification && $material_quantity && $texture)?1:0;
						$sql_list .= "(NULL,'$mouldid','$material_date','$material_list_number','$material_list_sn','$material_number','$material_name','$specification','$material_quantity','$texture','$hardness','$brand','$spare_quantity','$remark','$complete_status','$employeeid','$dotime'),";
					}
				}
			 $sql = "INSERT INTO `db_mould_material` (`materialid`,`mouldid`,`material_date`,`material_list_number`,`material_list_sn`,`material_number`,`material_name`,`specification`,`material_quantity`,`texture`,`hardness`,`brand`,`spare_quantity`,`remark`,`complete_status`,`employeeid`,`dotime`) VALUES". $sql_list;
			 $db->query($sql);

			//电极物料
			}elseif($type == 'E'){
				$array_id = '';
				foreach($array_material as $value){
				  $material_list_sn = trim($value[1]);
				  $material_number = str_replace_array(iconv("GB2312","UTF-8//IGNORE",trim($value[2])));
				  $specification = str_replace_array(iconv("GB2312","UTF-8//IGNORE",trim($value[4])));
				  $material_name = str_replace_array(iconv("GB2312","UTF-8//IGNORE",trim($value[5])));
				  $texture = iconv("GB2312","UTF-8//IGNORE",trim($value[6]));
				  $hardness = iconv("GB2312","UTF-8//IGNORE",trim($value[7]));
				  $brand = iconv("GB2312","UTF-8//IGNORE",trim($value[8]));
				  $remark = iconv("GB2312","UTF-8//IGNORE",trim($value[21]));
				  $material_quantity = $value[9];
				  $material_weight = $value[10];
				  $real_weight = 0;
				  if(strpos($material_weight,'*')){
				  	$number = substr($material_weight,0,strpos($material_weight,'*'));
				  	$unit_wei = substr($material_weight,strpos($material_weight,'*')+1);
				  	$real_weight = $number * $unit_wei;
				  }else{
				  	$real_weight = $material_weight;
				  }


				  if($material_name && $material_list_sn && $specification){
				  	//信息汇总
				  	$tot_material_quantity += $material_quantity;
				  	$tot_material_weight += $real_weight;

				  	$sql_fen = "INSERT INTO `db_mould_material` (`materialid`,`mouldid`,`material_date`,`material_list_number`,`material_list_sn`,`material_number`,`material_name`,`specification`,`material_quantity`,`texture`,`hardness`,`brand`,`spare_quantity`,`remark`,`complete_status`,`employeeid`,`dotime`) VALUES(NULL,'$mouldid','$material_date','','$material_list_sn','$material_number','$material_name','$specification','$material_quantity','$texture','$hardness','$brand','','$remark','1','$employeeid','$dotime')";
				  	$db->query($sql_fen);
				  	if($db->affected_rows){
				  		$array_id .= $db->insert_id.',';
				  	}
				  }
				}
			}
			$array_id = rtrim($array_id,',');
			//插入汇总信息
			$sql_tot = "INSERT INTO `db_mould_material` (`mouldid`,`material_date`,`material_number`,`material_name`,`material_quantity`,`texture`,`complete_status`,`employeeid`,`dotime`,`specification`) VALUES('$mouldid','$material_date','900','红铜','$tot_material_quantity','红铜','1','$employeeid','$dotime','$tot_material_weight')";
			$db->query($sql_tot);
			if($db->affected_rows){
				//把汇总信息的id填充到分信息中
				$sql_parent = "UPDATE `db_mould_material` SET `parentid` = ".$db->insert_id." WHERE `materialid` IN ($array_id)";
				$db->query($sql_parent);
			}
		}
	}
		header("location:mould_material_list.php");
}
?>