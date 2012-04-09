<?php
session_start();
require_once('ValidationCode.class.php');

$code=new ValidationCode(300,80,4);
//$code->showImage();
$code->showImage("DroidSansMono.ttf");
$_SESSION['code']=$code->getCheckCode();
