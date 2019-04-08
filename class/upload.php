<?php
/*
 * Upload File Class Build By zzs in 2011-02-22
 */
class upload{

	private $upload_name;                //上传文件名
	private $upload_tmp_name;            //上传临时文件名
	private $upload_final_name;          //上传最终文件名
	private $upload_target_path;         //上传目标文件路径
	private $upload_filetype;            //上传文件类型
	private $upload_uploadedfile_type;   //允许上传文件类型
	private $upload_file_size;           //上传文件的大小
	private $allow_uploaded_maxsize;     //允许上传文件大小最大值
	private $upload_error;               //上传错误代码
	public  $upload_file_arr;            //上传成功文件数组

	function __construct(){

		$this->upload_name = $_FILES['file']['name'];
		$this->upload_tmp_name = $_FILES['file']['tmp_name'];
		$this->upload_final_name = date("His");
		$this->upload_file_size = $_FILES['file']['size'];
		$this->upload_error =$_FILES['file']['error'];

	}

	//File[]多上传文件
	public function upload_files($file_dir){
		$this->upload_target_path = $file_dir;
		$this->mk_dir($this->upload_target_path);
		if(is_array($this->upload_name)){
			for($i=0; $i<count($this->upload_name); $i++){
				if($this->upload_error[$i] == 0){
					if(is_uploaded_file($this->upload_tmp_name[$i])){
						if(move_uploaded_file($this->upload_tmp_name[$i], $this->upload_target_path . $this->upload_final_name . $i . $this->get_fileinfo($this->upload_name[$i],1))){
							$this->array_upload_files[]= array('upload_name' => $this->upload_name[$i],'upload_file_size' => $this->upload_file_size[$i],'upload_final_name' => $this->upload_final_name . $i . $this->get_fileinfo($this->upload_name[$i],1),'upload_file_number' =>$i,'upload_target_path'=>$this->upload_target_path);
							
						}
					}
				}
			}
		}
	}

	//file单文件上传
	public function upload_file($file_dir){
		$this->upload_target_path = $file_dir;
		$this->mk_dir($this->upload_target_path);
		if($this->upload_error == 0){
			if(is_uploaded_file($this->upload_tmp_name)){
				if(move_uploaded_file($this->upload_tmp_name, $this->upload_target_path . $this->upload_final_name . $this->get_fileinfo($this->upload_name,1))){
					$this->array_upload_file= array('upload_name' => $this->upload_name,'upload_file_size' => $this->upload_file_size,'upload_final_name' => $this->upload_final_name . $this->get_fileinfo($this->upload_name,1));
				}
			}
		}
	}

	//生产缩略图
//	public function smallimagedo($filename,$smallfilename,$width='',$hight=''){
//		ini_set ('memory_limit', '128M');
//		$percent = $width/$hight;
//		//获取图片宽高类型
//		if(is_array(getimagesize($filename))){
//			list($width_orig, $height_orig, $filetype) = getimagesize($filename);
//			//获取源文件资源句柄。接收参数为图片路径，返回句柄
//			switch ($filetype){
//				case 1 :
//					$source = @imagecreatefromgif($filename);
//					break;
//				case 2 :
//					$source = @imagecreatefromjpeg($filename);
//					break;
//				case 3 :
//					$source = @imagecreatefrompng($filename);
//					break;
//				default:
//					$source = @imagecreatefromstring($filename);
//					break;
//			}
//			$width = ($width>$width_orig)? $width_orig:$width;
//			$hight = ($hight>$height_orig)? $height_orig:$hight;
//			if(($width_orig/$height_orig)<$percent){
//				$width = ($width_orig*$hight)/$height_orig;
//			}elseif(($width_orig/$height_orig)>$percent){
//				$hight = ($height_orig*$width)/$width_orig;
//			}
//			$thumb = imagecreatetruecolor($width, $hight);
//			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $hight, $width_orig, $height_orig);
//			switch ($filetype){
//				case 1 :
//					@imagegif($thumb,$smallfilename,100);
//					break;
//				case 2 :
//					@imagejpeg($thumb,$smallfilename,100);
//					break;
//				case 3 :
//					@imagepng($thumb,$smallfilename);
//					break;
//			}
//		}
//	}
//	
//	

	//删除单个文件
	public function delfile($filename){
		if(file_exists($filename)){
			@unlink($filename);
		}
	}

	//获取文件名后缀
	private function get_fileinfo($filepath,$id){
		$arr_filename = pathinfo($filepath);
		if($id == "1"){
			$filetype = $arr_filename['extension'];
			return "." . $filetype;
		}elseif ($id == "2"){
			$filename = $arr_filename['filename'];
			return $filename;
		}elseif ($id == "3"){
			$filedir = $arr_filename['dirname'];
			return $filedir . "/";
		}elseif ($id == "4"){
			$filebasename = $arr_filename['basename'];
			return $filebasename;
		}
	}

	//判断文件是否存在
	private function mk_dir($pathname){
		if(!is_dir($pathname)){
			mkdir($pathname);
		}
	}

}

?>