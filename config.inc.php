<?php
/* *************************************************************************
 *
 * Author: AlexLiu - bigtooth2006@sina.com
 *
 * QQ : 418270300
 *
 * Last modified: 2012-04-17 19:59
 *
 * Filename: config.inc.php
 *
 * Description: 公共配置
 *
 * ***********************************************************************/

DEFINE('DB_HOST','localhost');
DEFINE('DB_USER','root');
DEFINE('DB_PASSWD','123456');
DEFINE('DB_NAME','sitename');

$dbc=mysql_connect(DB_HOST, DB_USER, DB_PASSWD) OR die('连接错误：'.mysql_error());
$db=mysql_select_db(DB_NAME) OR die('数据库选择失败：'.mysql_error());

function escape_data($data) {
		if(ini_get('magic_quotes_gpc')) {
				$data=stripslashes($data);
		}

		if(function_exists('mysql_real_escape_string')) {
				global $dbc;
				$data=mysql_real_escape_string(trim($data),$dbc);
		} else {
				$data=mysql_real_escape_string(trim($data));
		}
		return $data;
}
?>
