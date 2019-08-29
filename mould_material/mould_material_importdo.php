<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../phpExcelReader/Excel/reader.php';
require_once 'shell.php';
if($_POST['submit']){
	$mouldid = $_POST['mouldid'];
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
				if($material_number == '900'){
					$tot_material_list_sn = $material_list_sn;
					$tot_material_list_number = $material_list_number;
					$tot_material_number = '900';
					$tot_material_name = '红铜';
					$tot_material_quantity += $material_quantity;
					$tot_specification = $specification;

				}
				if($material_number && $material_name && $specification && $material_quantity){
					$complete_status = ($material_list_number && $material_list_sn && $material_number && $material_name && $specification && $material_quantity && $texture)?1:0;
					if($material_number == '900'){
						$sql_list .= "(NULL,'$mouldid','$material_date','$material_list_number','$material_list_sn','$material_number','$material_name','$specification','$material_quantity','$texture','$hardness','$brand','$spare_quantity','$remark','$complete_status','$employeeid','$dotime','D'),";
					}else{
						$sql_list .= "(NULL,'$mouldid','$material_date','$material_list_number','$material_list_sn','$material_number','$material_name','$specification','$material_quantity','$texture','$hardness','$brand','$spare_quantity','$remark','$complete_status','$employeeid','$dotime',''),";
					}
				}
			}
			$sql_list = rtrim($sql_list,',');
			//插入铜料合计信息
			$tot_sql = "INSERT INTO `db_mould_material` (`materialid`,`mouldid`,`material_date`,`material_list_number`,`material_list_sn`,`material_number`,`material_name`,`specification`,`material_quantity`,`complete_status`,`employeeid`,`dotime`,`type`) VALUES(NULL,'$mouldid','$material_date','$tot_material_list_number','$tot_material_list_sn','$tot_material_number','$tot_material_name','$tot_specification','$tot_material_quantity','1','$employeeid','$dotime','Z')";
	
			$db->query($tot_sql);
			$sql = "INSERT INTO `db_mould_material` (`materialid`,`mouldid`,`material_date`,`material_list_number`,`material_list_sn`,`material_number`,`material_name`,`specification`,`material_quantity`,`texture`,`hardness`,`brand`,`spare_quantity`,`remark`,`complete_status`,`employeeid`,`dotime`,`type`) VALUES". $sql_list;
			$db->query($sql);
			if($db->insert_id){
				header("location:mould_material_list.php");
			}
		}
	}
}
?>