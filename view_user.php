<?php
/* *************************************************************************
 *
 * Author: AlexLiu - bigtooth2006@sina.com
 *
 * QQ : 418270300
 *
 * Last modified: 2012-04-20 11:29
 *
 * Filename: view_user.php
 *
 * Description: 分页
 *
 * ***********************************************************************/

require_once('./config.inc.php');
$display=10;

if(isset($_GET['np'])) {
		$num_pages=$_GET['np'];
} else {
		$query="SELECT COUNT(*) FROM users ORDER BY registration_date ASC";
		$result=mysql_query($query);
		$row=mysql_fetch_array($result, MYSQL_NUM);
		$num_records=$row[0];

		if($num_records>$display) {
				$num_pages=ceil($num_records/$display);
		} else {
				$num_pages=1;
		}
}

if(isset($_GET['s'])) {
		$start=$_GET['s'];
} else {
		$start=0;
}

$query="SELECT last_name, first_name, DATE_FORMAT(registration_date, '%M %d, %Y') AS dr, user_id FROM users ORDER BY registration_date ASC LIMIT $start, $display";
$result=mysql_query($query);

echo '<table align="center" cellspacing="0" callpadding="5">
		<tr>
			<td align="left"><b>编辑</b></td>
			<td align="left"><b>删除</b></td>
			<td align="left"><b>LastName</b></td>
			<td align="left"><b>FirstName</b></td>
			<td align="left"><b>注册时间</b></td>
		</tr>';

while($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo '<tr>
				<td align="left"><a href="edit_user.php?id='.$row['user_id'].'">编辑</a></td>
				<td align="left"><a href="delete_user.php?id='.$row['user_id'].'">删除</a></td>
				<td align="left">'.$row['last_name'].'</td>
				<td align="left">'.$row['first_name'].'</td>
				<td align="left">'.$row['dr'].'</td>
				</tr>';
}

echo '</table>';
mysql_free_result($result);
mysql_close($dbc);

if($num_pages>1) {
		echo '<br><p>';
		$current_page=($start/$display)+1;
		if($current_page!=1) {
				echo '<a href="view_user.php?s='.($start-$display).'&np='.$num_pages.'">上一页</a>';
		}

		for($i=1; $i<=$num_pages; $i++) {
				if($i!=$current_page) {
						echo '<a href="view_user.php?s='.($display*($i-1)).'&np='.$num_pages.'">'.$i.'</a>';
				} else {
						echo $i.' ';
				}
		}

		if($current_page!=$num_pages) {
				echo '<a href="view_user.php?s='.($start+$display).'&np='.$num_pages.'">下一页</a>';
		}

		echo '</p>';
}

?>




