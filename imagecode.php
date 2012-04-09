<?php
session_start();
//echo $_SESSION['code'];
//echo $_POST["code"];
if(strtolower($_POST["code"])==strtolower($_SESSION['code'])) {
		echo "验证码输入正确！";
}else{
		echo "验证码输入错误，请重新输入！";
}

?>


<html>
		<head>
				<title>验证表单测试</title>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		</head>
		<body>
				<form method="post" action="login.php">
						<input type="text" name="code"><img src="code.php" onclick="this.src='code.php?'+Math.random()"><br>
						<input type="submit" name="sub" value="提交"><br>
				</form>
		</body>
</html>
