<?php
require_once 'config.php';
$action = $_GET['action'];

if(!isset($action)) {
?>
<form method=POST action=login.php?action=login>
<input type=text name=username> Brukernavn
<br><input type=password name=password> Passord
<br><input type=submit value='Logg inn'>
</form>
<?php
die();
}

elseif($action == "login") {
$user = $_POST['username'];
$pass = $_POST['password'];

$cpass = crypt_pass($pass);
$q = query("SELECT * FROM users WHERE nick LIKE '$user' AND password = '$cpass'");
$r = fetch($q);

if(num($q) == 0) die("Feil brukernavn eller passord");
else query("UPDATE session SET userID = '$r->ID' WHERE sID = '".$_COOKIE[$cookiename]."'");

header("Location: index.php");

}
elseif($action == "changeuser") {
$changeuser = $_POST['changeuser'];

query("UPDATE session SET userID = $changeuser WHERE sID = '".$_COOKIE[$cookiename]."'");
header("Location: selg.php");
}

elseif($action == "togglecrewsalg") {
	toggle_crewsalg();
	header("Location: selg.php");
}