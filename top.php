<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Content-Type: text/html; charset=ISO-8859-1");
require_once 'config.php';
//header("Location: index.html")
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head><title>GlobeLAN Kiosk</title>
<link type="text/css" rel=stylesheet href=styles.css />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body onKeyPress="microsoftKeyPress()">
<table><tr><td><?php include 'menu.php'; ?></td>
<td>
<?php /*<form method=POST action=login.php?action=togglecrewsalg><input type=submit value='<?php
$salg = crewsalg();
echo $crewsalg[$salg]; '>
</form>  */
?>
<?php echo "Crewsalg: ";
$q1 = query("SELECT * FROM session WHERE sID = '$_COOKIE[$cookiename]'");
$r1 = fetch($q1);
if($r1->crewSalg != 0) {

	$q = query("SELECT * FROM users WHERE ID = '$r1->crewSalg'");
	$r = fetch($q);
	echo $r->name;
	echo "<form method=POST action=login.php?action=togglecrewsalg><input type=submit value='Skru av crewsalg'></form>";
}
else echo "Av";
?>
</td>
<td width=40%>
<?php /*
<form method=POST action=login.php?action=changeuser>
<?php select_su(); ?>
<input type=submit value='Bytt bruker'>
</form>
*/ ?>
<?php
$q = query("SELECT users.name,session.userID FROM users,session WHERE users.ID = session.userID AND session.sID = '$_COOKIE[$cookiename]'");
$r = fetch($q);
echo "Selger: ".$r->name;
	/* Gjør det mulig å bytte kasse */

	echo "<form method=POST action=kasser.php?action=byttkasse>";
	$currentkasse = my_kasse();
	echo "<select name=nykasse>";
	$qk = query("SELECT * FROM kasser");
	while($rk = fetch($qk)) {
		echo "<option value=$rk->ID";
		if($rk->ID == $currentkasse) echo " SELECTED";
		echo ">$rk->kassenavn ($rk->innhold)</option>\n\n\n";
	}
	echo "</select>";
	echo "<input type=submit value='Bytt kasse'>";
	echo "</form>";


?>
</td></tr>
<tr><td>
