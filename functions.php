<?php

function query($q) {
$qu = mysql_query($q) or die("Kunne ikke kjøre '$q' grunnet ".mysql_error());
return $qu;
}

function num($q) {
$n = mysql_num_rows($q);
return $n;
}

function fetch($q) {
$r = mysql_fetch_object($q);
return $r;
}


function crypt_pass($pass) {
return md5($pass);
}

function wareinfo ($barcode) {
$q = query("SELECT * FROM warez WHERE barcode = '$barcode'");
$r = fetch($q);
return $r;
}

function select_su() {
$my_rank = getuserrank();
$my_ID = getcurrentuserid();
$q = query("SELECT * FROM users WHERE level <= $my_rank AND ID > 1 ORDER BY name ASC");
echo "<select name=changeuser>";
echo "<option value=1>Logg ut</option>";
while($r = fetch($q)) {
echo "<option value=$r->ID";
if($r->ID == $my_ID) echo " SELECTED";
echo ">$r->name</option>";
}
echo "</select>";
}

function my_kurv_total() {
	global $cookiename;
	$crewsalg = crewsalg($_COOKIE[$cookiename]);
	if($crewsalg) $userow = "cPrice";
	else $userow = "price";
	$sID = session_id();
	$q = query("SELECT * FROM temp_kurv WHERE sID = '$sID'");
	while($r = fetch($q)) {
		$winfo = wareinfo($r->wareID);

		$rabatt = ware_rabatt($r->wareID);

		if($rabatt && !$crewsalg)
			$totalprice = $totalprice + ($rabatt * $r->amount);
		else $totalprice = $totalprice + ($winfo->$userow * $r->amount);
	}
	return $totalprice;
}

function my_kasse() {
	$sID = session_id();
	$q = query("SELECT * FROM session WHERE sID = '$sID'");
	$r = fetch($q);
	return $r->kasse;
}

function crewsalg($sID = "OWN") {
	global $cookiename;
	if($sID == "OWN") $sID = $_COOKIE[$cookiename];
	$q = query("SELECT * FROM session WHERE sID = '$sID'");
	$r = fetch($q);
	return $r->crewSalg;
}

function toggle_crewsalg() {
	global $cookiename;
	$sID = session_id();
	$current = crewsalg($_COOKIE[$cookiename]);
	if($current != 0) $new = 0;
	else $new = 0;
	query("UPDATE session SET crewSalg = $new WHERE sID = '$sID'");
}

function ware_rabatt($wareID) {

	$now = time();
	$q = query("SELECT * FROM rabatter WHERE wareID = '$wareID' AND startTime <= $now AND stopTime >= $now AND active = 1");
	$r = fetch($q);

	if(num($q) == 0) return FALSE;
	else return $r->newPrice;

}

