<?php
class Image_controller extends CI_Controller {

	function __construct() { 
			parent::__construct();
			date_default_timezone_set("Asia/Jakarta");
			$this->load->helper(array("form", "url"));
	}
	
	function getExtension($str) {
		$i = strrpos($str,".");
		if (!$i) { return ""; } 
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return $ext;
	}
	
	function compressImage($ext,$target_file,$path,$actual_image_name,$newwidth) {
		if($ext=="jpg" || $ext=="jpeg" ){
			$src = imagecreatefromjpeg($target_file);
		}
		else if($ext=="png"){
			$src = imagecreatefrompng($target_file);
		}
		else if($ext=="gif"){
			$src = imagecreatefromgif($target_file);
		}
		else{
			$src = imagecreatefrombmp($target_file);
		}
																		
		list($width,$height)=getimagesize($target_file);
		$newheight=($height/$width)*$newwidth;
		$tmp=imagecreatetruecolor($newwidth,$newheight);
		imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
		$filename = $path.$actual_image_name;
		imagejpeg($tmp,$filename,100);
		imagedestroy($tmp);
		return $filename;
	}

	function addImage() {
		//http://localhost/api_general/index.php/Image_controller/addImage
		$path = "../api_general/assets/images/";
		if (!empty($_FILES['photo']['name'])) {
			$image_count = count($_FILES['photo']['name']);
			for($i=0; $i<$image_count; $i++) {
				$file_name = $_FILES['photo']['name'][$i];
				$mod_file_name = date("YmdHis")."_".$i.".".strtolower($this->getExtension($file_name));
				$target_file = $path.$mod_file_name;
				$valid_formats = array("jpg", "png", "gif", "bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
				$maxsize = 1536*1024;
				list($txt, $extension) = explode(".", $file_name);
				$extension = strtolower($this->getExtension($file_name));
				
				if(in_array($extension,$valid_formats)) {
					move_uploaded_file($_FILES['photo']['tmp_name'][$i], $target_file);
					$widthArray = 800;
					$this->compressImage($extension,$target_file,$path,$mod_file_name,$widthArray);
					$this->load->model("Image_model");
					$data = $this->Image_model->addImage($mod_file_name);
				}
				else{}
			}
			$array = array("status" => 1, "message" => "Success");
			echo json_encode($array);
		}
		else {
			$array = array("status" => 0, "message" => "Image empty");
			echo json_encode($array);
		}	
	}

}
?>