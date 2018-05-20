<?php
require_once 'config.php';
if(getuserrank() != 1) die("nei nei NEI! du må være admin her! ".getuserrank());

$action = $_GET['action'];
$text = $_GET['text'];
if(!isset($action)) {
include_once 'top.php';

?>
<br><br><?php if(isset($text)) echo $text; ?>
<ul>
<li><a href=admin.php?action=addware>Legg til varer</a>
<br><li><a href=admin.php?action=useradmin>Useradmin</a>
<br><li><a href=admin.php?action=wareadmin>Endre varer</a>
<br><li><a href=kasser.php>Kassadministrasjon</a>
<br><li><a href=admin.php?action=rabatter>Rabattadministrasjon</a>
<br><li><a href=admin.php?action=userimport>Brukerimport</a>
</ul>
<?php
include_once 'bottom.php';
}

elseif($action == "addware") {
include_once 'top.php';
?>
<form method=POST action=admin.php?action=doaddware>
<input name=name type=text> Navn
<br><input type=text name=price> Pris
<br><input type=text name=cPrice> Crewpris
<br><input type=text name=inPrice> Innkjøpspris
<br><input type=text name=barcode> Strekkode/ID
<br><input type=submit value='Legg til varen'>
</form>
<?php

include_once 'bottom.php';
}
elseif($action == "doaddware") {
$name = $_POST['name'];
$price = $_POST['price'];
$barcode = $_POST['barcode'];
$cPrice = $_POST['cPrice'];
$inPrice = $_POST['inPrice'];
if(empty($barcode)) die("Du m&aring; ha en strekkode");
$q = query("SELECT * FROM warez WHERE barcode = '$barcode'");
if(num($q)) die("Varen ligger allerede inne :/");

query("INSERT INTO warez SET barcode = '$barcode',
	price = '$price',
	name = '$name',
	cPrice = '$cPrice',
	inPrice = '$inPrice'
	");

header("Location: admin.php?text=Varen er lagt til");
}

elseif($action == "useradmin") {
include_once 'top.php';
?>
<form method=POST action=admin.php?action=adduser>
<input type=text name=nick value='Brukernavn'>
<br><input type=text name=username value='Brukerens navn'>
<br><input type=text name=password value='Passord'>
<br><select name=level><option value=0>Selger</option><option value=1>Admin</option></select>
<br><input type=submit value='Legg til bruker'>
</form>
<?php
$q = query("SELECT * FROM users WHERE ID != 1");
echo "<table>";
while($r = fetch($q)) {
echo "<tr><td>";
echo $r->nick;
echo "</td><td>";
echo $r->name;
echo "</td><td>";

if($r->ID != getcurrentuserid()) {
	echo "<form method=POST action=admin.php?action=toggleuserlevel&user=$r->ID>
	<input type=submit value='".$rank[$r->level]."'>
	</form>";
}
else {
	echo $rank[$r->level];
}
echo "</td></tr>";
}
echo "</table>";
include_once 'bottom.php';
}

elseif($action == "adduser") {
$nick = $_POST['nick'];
$pass = $_POST['password'];
$cpass = crypt_pass($pass);
$username = $_POST['username'];
$level = $_POST['level'];

query("INSERT INTO users SET nick = '$nick', name = '$username', password = '$cpass', level = $level");
header("Location: admin.php?action=useradmin");
}

elseif($action == "wareadmin") {
	include_once 'top.php';
	$q = query("SELECT * FROM warez");

	echo "<table>";
	?><tr><th>Navn</th><th>Brukerpris</th><th>Crewpris</th><th>Fargekode</th></tr><?php
	while($r = fetch($q)) {
		echo "<tr><td>";
		echo "<a href=admin.php?action=editware&amp;ware=$r->barcode>";
		echo $r->name;
		echo "</a></td><td>";
		echo $r->price;
		echo "</td><td>";
		echo $r->cPrice;
		echo "</td><td bgcolor=$r->color>";
		echo "</td></tr>";
	}
	echo "</table>";
	include_once 'bottom.php';
}
elseif($action == "editware" && isset($_GET['ware'])) {
	include_once 'top.php';
	$ware = $_GET['ware'];
	$winfo = wareinfo($ware);

	echo "<form method=POST action=admin.php?action=doeditware&ware=$ware>";
	echo "<input type=text name=name value='$winfo->name'> Navn";
	echo "<br><input type=text name=price value='$winfo->price'> Pris";
	echo "<br><input type=text name=cPrice value='$winfo->cPrice'> Crewpris";
	echo "<br><input type=text name=inPrice value='$winfo->inPrice'> Innkjøpspris";
	echo "<br><select name=wcolor>";
	for($i=0;$i<count($color);$i++) {
		echo "<option value=$color[$i]";
		if($winfo->color == $color[$i]) echo " SELECTED";
		echo ">$color[$i]</option>";
	}
	echo "</select> Vare-farge";
	if($winfo->must_prepare == 1) $mustprepare = "CHECKED";
	echo "<br><input type=checkbox name=mustprepare value=1 $mustprepare> Må forberedes?";
	if($winfo->active == 1) $selected = "CHECKED";
	echo "<br><input type=checkbox name=active value=1 $selected> Aktiv?";
	echo "<br><input type=submit value='Lagre'></form>";

	include_once 'bottom.php';
}
elseif($action == "doeditware" && isset($_GET['ware'])) {
	$ware = $_GET['ware'];
	$name = $_POST['name'];
	$price = $_POST['price'];
	$cPrice = $_POST['cPrice'];
	$inPrice = $_POST['inPrice'];
	$active = $_POST['active'];
	$must_prepare = $_POST['mustprepare'];
	$wcolor = $_POST['wcolor'];
	if(!isset($active)) $active = 0;

	query("UPDATE warez SET
		name = '$name',
		price = '$price',
		cPrice = '$cPrice',
		inPrice = '$inPrice',
		color = '$wcolor',
		active = '$active',
		must_prepare = '$must_prepare'
		WHERE barcode = '$ware'");
	header("Location: admin.php?text=varen er oppdatert");
}
elseif($action == "rabatter") {
	include_once 'top.php';
	$q = query("SELECT * FROM rabatter");
	echo "<table>";
	while($r = fetch($q)) {
		echo "<tr><td>";
		echo $r->name;
		echo "</td><td>";
		$winfo = wareinfo($r->wareID);
		echo $winfo->name;
		echo "</td><td>";
		if($r->active == 1) echo "Aktiv";
		else echo "Ikke aktiv";
	}
	echo "</table>";

	?>
	<form method=POST action=admin.php?action=addRabatt>
	<input type=text name=name size=25> Rabattnavn
	<br><input type=text name=startTime size=15> starttidspunkt (YYYY-MM-DD TT:MM)
	<br><input type=text name=stopTime size=15> stoptidspunkt (YYYY-MM-DD TT:MM)
	<br><select name=ware>
	<?php
		$q = query("SELECT * FROM warez");
		while($r = fetch($q)) {
			echo "<option value=$r->barcode>$r->name</option>";
		}
	?>
	</select>
	<br><input type=text name=newPrice> Pris når rabatten gjelder..
	<br><input type=submit value='Legg til rabatt'>
	</form>
	<?php
	include_once 'bottom.php';
}

elseif($action == "addRabatt") {
	$startTime = $_POST['startTime'];
	$stopTime = $_POST['stopTime'];
	$name = $_POST['name'];
	$wareID = $_POST['ware'];
	$newPrice = $_POST['newPrice'];

	$convertStart = strtotime($startTime);
	$convertStop = strtotime($stopTime);

	$q = query("INSERT INTO rabatter SET
		wareID = $wareID,
		startTime = $convertStart,
		stopTime = $convertStop,
		name = '$name',
		newPrice = $newPrice,
		active = 1
	");
	header("Location: admin.php?action=rabatter");
}


elseif($action == "toggleuserlevel") {
	$user = $_GET['user'];
	if(empty($user)) die("Nope, ingen slik bruker her, gitt");
	if($user == getcurrentuserid()) die("Sorry, du kan ikke endre egne rettigheter");
	$q = query("SELECT level FROM users WHERE ID = $user");
	$r = fetch($q);

	if($r->level == 0) $newlevel = 1;
	else $newlevel = 0;
	query("UPDATE users SET level = $newlevel WHERE ID = $user");
	header("Location: admin.php?action=useradmin");
}

elseif($action == "userimport") {
	include_once 'top.php';
	?>
	<form method=POST action=admin.php?action=douserimport>
	<textarea name=users rows=15 cols=65></textarea>
	<input type=submit value='Legg inn brukere'>
	<?php
	include_once 'bottom.php';
}


elseif($action == "douserimport") {
	$users = $_POST['users'];
	#echo $users;
	$line = split("\n", $users);

	for($i=0;$i<count($line);$i++) {

		$userinfo = split(":", $line[$i]);
		$ID = $userinfo[0];
		$name = $userinfo[1];
		$nick = $userinfo[2];
		$password = $userinfo[3];
		#print_r($userinfo);
		#die();
		$test = query("SELECT * FROM users WHERE ID = $ID");
		if(num($test) != 0) {
			query("UPDATE users SET name = '$name', nick = '$nick', password = '$password' WHERE ID = $ID");
			echo "Updated ID $ID to $name<br>\n";
		} // End if
		else {
			query("INSERT INTO users SET ID = '$ID', name = '$name', nick = '$nick', password = '$password'");
			echo "Inserted $ID to $name<br>\n";
		} // End else

	} // end for

}