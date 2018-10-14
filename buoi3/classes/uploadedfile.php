<?php
	namespace MApp;
	if(defined('M_RUNNING')){
		class UploadedFile{
			public static function getMimeType($destfile){
				if(file_exists($destfile)){
					$finfo = new \FInfo();
					return $finfo->file($destfile, FILEINFO_MIME_TYPE);
				}else{
					return '';
				}
			}
			public static function getExtension($mimetype){
				return explode('/', $mimetype)[1];
			}
			public static function valueInArray($value, $array){
				foreach($array as $v){
					if($value == $v){
						return true;
					}
				}
				return false;
			}
			public static function scaleImageToPng($src, $dest, $dw, $dh){
				$ratio = $dw / $dh;
				$mimetype = self::getMimeType($src);
				$newimage = imagecreatetruecolor($dw, $dh);
				if($mimetype == ''){
					return false;
				}
				$ext = self::getExtension($mimetype);
				
				$image = null;
				switch($ext){
					case 'png':
					$image = imagecreatefrompng($src);
					break;
					case 'x-ms-bmp':
					$image = imagecreatefromwbmp($src);
					break;
					case 'jpeg':
					$image = imagecreatefromjpeg($src);
					break;
					case 'gif':
					$image = imagecreatefromgif($src);
					break;
				}
				if($image!=null){
					$sw = imagesx($image);
					$sh = imagesy($image);
					if($sw / $sh >= $ratio){
						#scale by height
						$sx = ($sw - ( $ratio * $sh) ) / 2;
						$sy = 0;
						$sw = $ratio * $sh;
					}else{
						#scale by width
						$sx = 0;
						$sy = ($sh - ($sw / $ratio)) / 2;
						$sh = $sw / $ratio;
					}
					imagecopyresampled($newimage, $image, 0, 0, $sx, $sy, $dw, $dh, $sw, $sh);
					imagepng($newimage, $dest);
					return true;
				}else{
					return false;
				}
			}
		}
	}
?>