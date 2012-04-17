<?php
/* *************************************************************************
 *
 * Author: AlexLiu - bigtooth2006@sina.com
 *
 * QQ : 418270300
 *
 * Last modified: 2012-04-17 19:57
 *
 * Filename: validationCode.class.php
 *
 * Description: 验证码图片生成类
 *
 * ***********************************************************************/

class validationCode {
		private $width;
		private $height;
		private $codeNum;
		private $image;
		private $disturbColorNum;
		private $checkCode;

		function __construct($width=80, $height=20, $codeNum=4) {
				$this->width=$width;
				$this->height=$height;
				$this->codeNum=$codeNum;
				$this->checkCode=$this->createCheckCode();
				$number=floor($width*$height/16);
				if($number>240-$codeNum) {
						$this->disturbColorNum=240-$codeNum;
				}else{
						$this->disturbColorNum=$number;
				}
		}

		function showImage($fontface="") {
				$this->createImage();
				$this->setDisturbColor();
				$this->outputText($fontface);
				$this->outputImage();
		}

		function getCheckCode() {
				return $this->checkCode;
		}

		private function createImage() {
				//创建图像资源
				$this->image=imagecreatetruecolor($this->width,$this->height);
				//随机创建图像背景颜色并为背景填充颜色
				$backColor=imagecolorallocate($this->image, rand(220,255), rand(220,255), rand(220,255));
				imagefill($this->image, 0, 0,$backColor);
				//设置边框颜色并绘制矩形边框
				$border=imagecolorallocate($this->image, 0, 0, 0);
				imagerectangle($this->image, 0, 0, $this->width-1, $this->height-1, $border);
		}

		private function setDisturbColor() {
				for($i=0; $i<=$this->disturbColorNum; $i++) {
						$color=imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
						imagesetpixel($this->image, rand(1,$this->width-2) ,rand(1,$this->height-2), $color);
				}
				for($i=0; $i<=10; $i++) {
						$color=imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
						imagearc($this->image, rand(-8,$this->width+8), rand(-16,$this->height+16), rand(36,360), rand(27,270), rand(49,58), rand(27,38), $color);
				}
		}
		
		private function createCheckCode() {
				$code="23456789qwertyuipasdfghjkzxcvbnmQWERTYUPASDFGHJKLZXCVBNM";
				$string='';
				for($i=0; $i<$this->codeNum; $i++) {
						$char=$code{rand(0,strlen($code)-1)};
						$string.=$char;
				}
				return $string;
		}

		private function outputText($fontface) {
				for($i=0; $i<$this->codeNum; $i++) {
						$fontcolor=imagecolorallocate($this->image, rand(0,98), rand(0,98), rand(0,98));
						if($fontface=="") {
								$fontsize=rand(3,5);
								$x=floor($this->width/$this->codeNum)*$i+3;
								$y=rand(0,$this->height-15);
								imagechar($this->image, $fontsize, $x, $y, $this->checkCode{$i}, $fontsize);
						}else{
								$fontsize=rand(18,26);
								$x=floor(($this->width-8)/$this->codeNum)*$i+8;
								$y=rand($fontsize+6,$this->height);
								imagettftext($this->image, $fontsize, rand(-26,26), $x, $y, $fontcolor, $fontface, $this->checkCode{$i});
						}
				}
		}

		private function outputImage(){
				header("Content-Type:image/png");
				imagepng($this->image);
		}

		function __destruct() {
				imagedestroy($this->image);
		}

}

