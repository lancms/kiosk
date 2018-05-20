</td><td><?php

$q = query("SELECT * FROM warez WHERE active = 1");
echo "<table>";
while($r = fetch($q)) {
	echo "<tr><td>";
	echo $r->name;
	echo "</td><td>";
	echo $r->price;
	echo "</td></tr>";
}
echo "</table>";

