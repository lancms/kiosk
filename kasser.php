<?php
require_once 'config.php';

$action = $_GET['action'];

if(getuserrank() != 1 && $action != "byttkasse") die("Du har <b>IKKE</b> tilgang her!");

if(!isset($action)) {
	include_once 'top.php';
	echo "<table>";
	echo "<tr><th>kasse</th><th>Innhold</th></tr>";
	$q = query("SELECT * FROM kasser");
	while($r = fetch($q)) {
		echo "<tr><td>";
		echo "<a href=kasser.php?action=move&from=$r->ID>".$r->kassenavn;
		echo "</a></td><td>";
		echo $r->innhold;
		echo "</td></tr>";
	}
	echo "</table>";
	
	?>
	<br><br><br>		
	<form method=POST action=kasser.php?action=add>
	<input type=text name=kassenavn> Kassenavn
	<br><input type=text name=innhold> Orginalt innhold
	<br><input type=submit value='Legg til kasse'>
	</form>
	<?php
	include_once 'bottom.php';
}

elseif($action == "add") {
	$navn = $_POST['kassenavn'];
	$innhold = $_POST['innhold'];
	$logtext = "opprettet en ny kasse som heter $navn som starter med $innhold i kontanter....";
	query("INSERT INTO kasser SET kassenavn = '$navn', innhold = '$innhold'");
	query("INSERT INTO kasselog SET logtext = '$logtext', logtime = ".time().", userID = ".getcurrentuserid());
	header("Location: kasser.php");
}
elseif($action == "move") {
	include_once 'top.php';
	$from = $_GET['from'];

	echo "<form method=POST action=kasser.php?action=domove>";
	echo "<select name=fromkasse>";
	$q = query("SELECT * FROM kasser");
	while($r = fetch($q)) {
		echo "<option value='$r->ID' ";
		if($r->ID == $from) echo "SELECTED";
		echo ">$r->kassenavn ($r->innhold)</option>";
	}
	echo "</select> Fra";
	echo "<br><select name=tokasse>";
	$q2 = query("SELECT * FROM kasser");
	while($r2 = fetch($q2)) {
		echo "<option value='$r2->ID' ";
		if($r2->ID == $to) echo "SELECTED";
		echo ">$r2->kassenavn ($r2->innhold)</option>";
	}
	echo "</select> Til";
	echo "<br>";
	echo "<input type=text name=amount> Kor mykje penga?";
	echo "<br><input type=submit value='Overfør!'>";
	echo "</form>";		
	
	include_once 'bottom.php';
} 
elseif($action == "domove") {
	$from = $_POST['fromkasse'];
	$to = $_POST['tokasse'];
	$amount = $_POST['amount'];
	
	$test_from = query("SELECT * FROM kasser WHERE ID = $from AND innhold >= $amount");
	if(num($test_from) == 0) die("Det lå ikke så mye penger i den kassen at det var mulig........ *whops*");
	query("UPDATE kasser SET innhold = innhold - $amount WHERE ID = $from");
	query("UPDATE kasser SET innhold = innhold + $amount WHERE ID = $to");
	$logtext = "flyttet $amount fra $from til $to";
	query("INSERT INTO kasselog SET logtext = '$logtext', logtime = ".time().", userID = ".getcurrentuserid());
	header("Location: kasser.php");
} elseif($action == "byttkasse") {
	$nykasse = $_POST['nykasse'];
	$sID = session_id();
	query("UPDATE session SET kasse = $nykasse WHERE sID = '$sID'");
	header("Location: selg.php");
}
