<?php
require '_secret.php';

$connstring = get_connection_string();
$dbconn = pg_connect($connstring) or die ('Failed to connect!');
$query = 'select * from ot_users';
$result = pg_query($query) or die ('Failed to execute query.');

echo "<h1>User Database Dump</h1>\n";
echo "<table style=\"border: 1px solid black\">\n";
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
	echo "\t<tr>\n";
	foreach ($line as $col_value) {
		echo "\t\t<td style='border: 1px solid black'>"
			. "$col_value</td>\n";
	}
	echo "\t</tr>\n";
}
echo "</table>\n";
echo "<p><a href=\"/index.php\">Index</a></p>\n";

pg_free_result($result);
pg_close($dbconn);
?>
