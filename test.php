<?php
require_once('./config.inc.php');
$query="SELECT * FROM users";
$result=mysql_query($query);
echo "<table>\n";
while ($row=mysql_fetch_array($result, MYSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($row as $col_value) {
        echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
}
echo "</table>\n";
mysql_free_result($result);
mysql_close($dbc);

