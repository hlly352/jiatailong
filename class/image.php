<?php
/*
 * Small Images Class Build By zzs in 2011-03-01
 */

class image{

	private $filename;       //被缩略图片名
	private $smallfilename;  //缩略图图片名
	private $width;          //缩略图片宽度
	private $hight;          //缩略图片高度
	private $width_orig;     //被缩略图片宽度
	private $height_orig;    //被缩略图片高度
	private $filetype;       //被缩略图片类型
	private $percent;        //图片高宽比例值
	private $source;         //被缩略图片资源
	private $thumb;          //缩略图片资源

	//生成缩略图
	public function smallimagedo($filename,$smallfilename,$width='',$hight=''){
		ini_set ('memory_limit', '512M');
		$this->filename = $filename;
		$this->smallfilename = $smallfilename;
		$this->width = $width;
		$this->hight = $hight;
		$this->percent = $width/$hight;
		//获取图片宽高类型
		if(is_array(getimagesize($this->filename))){
			list($this->width_orig, $this->height_orig, $this->filetype) = @getimagesize($this->filename);
			//获取源文件资源句柄。接收参数为图片路径，返回句柄
			switch ($this->filetype){
				case 1 :
					$this->source = @imagecreatefromgif($this->filename);
					break;
				case 2 :
					$this->source = @imagecreatefromjpeg($this->filename);
					break;
				case 3 :
					$this->source = @imagecreatefrompng($this->filename);
					break;
				default:
					$this->source = @imagecreatefromstring($this->filename);
					break;
			}
			$this->width = ($this->width>$this->width_orig)? $this->width_orig : $this->width;
			$this->hight = ($this->hight>$this->height_orig)? $this->height_orig : $this->hight;
			if(($this->width_orig/$this->height_orig)<$this->percent){
				$this->width = ($this->width_orig*$this->hight)/$this->height_orig;
			}elseif(($this->width_orig/$this->height_orig)>$this->percent){
				$this->hight = ($this->height_orig*$this->width)/$this->width_orig;
			}
			$this->thumb = imagecreatetruecolor($this->width, $this->hight);
			imagecopyresampled($this->thumb, $this->source, 0, 0, 0, 0, $this->width, $this->hight, $this->width_orig, $this->height_orig);
			switch ($this->filetype){
				case 1 :
					@imagegif($this->thumb,$this->smallfilename,100);
					break;
				case 2 :
					@imagejpeg($this->thumb,$this->smallfilename,100);
					break;
				case 3 :
					@imagepng($this->thumb,$this->smallfilename);
					break;
			}
		}
	}
	public function __destruct() {
		if(is_resource($this->thumb)) {
			imagedestroy($this->thumb);
		}
		if(is_resource($this->source)) {
			imagedestroy($this->source);
		}
	}
}
?>