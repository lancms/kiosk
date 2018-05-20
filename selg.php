<?php
require_once 'config.php';

$action = $_GET['action'];
$display_bytt_kasse = TRUE;
if(crewsalg($_COOKIE[$cookiename])) $pricerow = "cPrice";
else $pricerow = "price";

if(!isset($action)) {
	include_once 'top.php';
	?>
	<form method=POST action=selg.php?action=selg name=barfield>
	<input type=text name=barcode tabindex=1>
	<input type=submit style='visibility: hidden;'>

	</form>
	<script type="text/javascript"> document.forms['barfield'].barcode.focus(); </script>

	<form method=POST action=selg.php?action=selg>
	<select name=barcode tabindex=3>
	<?php

	$q = query("SELECT * FROM warez WHERE active = 1 ORDER BY name ASC");
	while($r = fetch($q)) {
		echo "<option value=$r->barcode>$r->name</option>\n\n";
	}

	?>
	</select>
	<input type=submit value='legg til handlekurv!'></form><br><br>
	<?php
	$q = query("SELECT * FROM temp_kurv WHERE sID = '".$_COOKIE[$cookiename]."' ORDER BY unixtime DESC");
	echo "</td><td>";
	echo "<div class=total_kurv>".my_kurv_total()."</div>";
	echo "<form method=POST action=selg.php?action=gjorsalget>
	<input type=text name=veksel size=3 value=0 tabindex=2> Veksel
	<br><input type=text name='levert_ut'> Ikke all maten ferdig? Nick på personen.
	<br><input type=submit value='";
	if(crewsalg() != 0 && num($q) == 0) echo "Bytt bruker";
	else echo "SELG!";
	echo "' accesskey='s'></form>";
	echo "</td><td valign=top>";

	echo "<b>Til avhenting</b><br>";

	$koQ = query("SELECT * FROM temp_ko ORDER BY unixtime ASC");
	echo "<table>";
	while($koR = fetch($koQ)) {
		$winfo = wareinfo($koR->wareID);
		echo "<tr><td>
		$koR->brukernavn
		</td><td>
		$koR->amount x ".$winfo->name."
		</td><td>
		".date("H:i", $koR->unixtime)."
		</td><td>";
		if($koR->amount == 1) echo "<form method=POST action=selg.php?action=hentet&amount=ALL&koID=$koR->ID><input type=submit value='Hentet'></form>";
		else {
			echo "<form method=POST action=selg.php?action=hentet&amount=1&koID=$koR->ID><input type=submit value='Hentet 1'></form></td>";
			echo "<td><form method=POST action=selg.php?action=hentet&amount=ALL&koID=$koR->ID><input type=submit value='Hentet alt'></form>";
		}
		echo "</td></tr>";

	} // End while fetch(koR)
	echo "</table>";

	echo "</td></tr>"; // slutt på høyre celle og midtre rad

	/* Lister ut alt som ligger i kurven */
	echo "<tr><td>";

	echo "<table>";



	while($r = fetch($q)) {
		$winfo = wareinfo($r->wareID);
		echo "<tr><td>";
		echo $winfo->name;

		echo "</td><td>";
		echo $r->amount;

		echo "</td><td>";
		echo "<form method=POST action=selg.php?action=fjernsalg>
		<input type=hidden name=fjernID value='$r->ID'>
		<input type=submit value='Fjern vare'></form>";
		echo "</td><td>";
		echo "(".$winfo->$pricerow.")";
		echo "</td></tr>";
	}

	echo "</table>";

	echo "</td><td>";
	if(isset($_GET['veksel']))
	echo "<div class=viktig_info>".$_GET['veksel']."</div>";

	echo "</td><td>";
	$q = query("SELECT * FROM warez WHERE must_prepare = 1 AND groupbase = 0");
	while($r = fetch($q)) {
		echo $r->name."<form method=POST action=selg.php?action=updateprepared&amp;prepared=$r->barcode>";
		echo "<input type=text name=amount size=2 value=$r->prepared><input type=submit style='visibility: hidden;'></form>\n\n";

	}
	//echo "</td></tr>";
	include_once 'bottom.php';
}

elseif($action == "selg") {
	$barcode = $_POST['barcode'];
	$sID = $_COOKIE[$cookiename];
	$q = query("SELECT * FROM warez WHERE barcode = '$barcode'");
	if(num($q) != 0) {
		$exists = query("SELECT * FROM temp_kurv WHERE sID = '$sID' AND wareID = '$barcode'");
		if(num($exists) == 0) {
			query("INSERT INTO temp_kurv SET sID = '".$_COOKIE[$cookiename]."', wareID = '$barcode', unixtime = ".time());
		} else {
			query("UPDATE temp_kurv SET amount = amount + 1 WHERE sID = '$sID' AND wareID = '$barcode'");
		}
	header("Location: selg.php");
	}
	elseif(stristr($barcode, "UID")) {
	// Were doing something with the user here...
	$bar = str_replace("UID", "", $barcode);
	query("UPDATE session SET crewSalg = '$bar' WHERE sID = '$_COOKIE[$cookiename]'");
	header("Location: selg.php");
	} // End if barcode = UID
	else die("WTF? Ingen har lagt inn den varen her.....");
}

elseif($action == "gjorsalget") {
	$q = query("SELECT * FROM temp_kurv WHERE sID = '".$_COOKIE[$cookiename]."'");
	if(num($q) == 0) {

		if(crewsalg() != 0) {
			query("UPDATE session SET crewsalg = 0, userID = ".crewsalg()." WHERE sID = '$_COOKIE[$cookiename]'");
			header("Location: selg.php");
			die();
		}

		else {
			include_once 'top.php';
			die("Prøve å faktisk selge noe før du gjør et salg? *hva skal man gjøre med de håpløse selgerne her*");
		}
	} // End if num == 0

	$userID = getcurrentuserid();
	$crewsalg = crewsalg();
	$total = my_kurv_total();
	$kasse = my_kasse();
	query("INSERT INTO history_salg_overall SET salesperson = $userID,
		crewsalg = $crewsalg,
		money = $total,
		kasse = $kasse
		");
	query("UPDATE kasser SET innhold = innhold + $total WHERE ID = $kasse");

	while($r = fetch($q)) {
		$winfo = wareinfo($r->wareID);
		$price = $winfo->$pricerow;
		$rabatt = ware_rabatt($r->wareID);
		if($rabatt) $price = $rabatt;
		if($rabatt) $rabatt = 1;
		$amount = $r->amount;
		if($winfo->must_prepare == 1) {
			if($winfo->groupbase != 0) {
				$w2 = wareinfo($winfo->groupbase);
				$prepares = $winfo->groupbase_multiplier;
				$groupbase = $w2->barcode;
			}
			else {
				$prepares = 1;
				$groupbase = $winfo->barcode;
			}
			query ("UPDATE warez SET prepared = prepared - $prepares WHERE barcode = '$groupbase'");
		} // End if must_prepare
		if(!empty($_POST['levert_ut'])  && $winfo->must_prepare == 1) {
			// What to do hvis ikke alt ble levert ut (og man må forberede det...)
			$levert_ut = $_POST['levert_ut'];


			query("INSERT INTO temp_ko SET brukernavn = '$levert_ut',
				wareID = '$winfo->barcode',
				amount = $amount,
				unixtime = ".time());
		} // end if !empty(levert_ut)
		while($amount) {

			query("INSERT INTO history_salg SET salesperson = '$userID',
			logUNIX = '".time()."',
			wareID = '".$r->wareID."',
			warePrice = '".$price."',
			crewSalg = ".crewsalg().",
			kasse = '$kasse',
			rabatt = '$rabatt'
			");
			$amount--;
		} // End while(amount)


		query("DELETE FROM temp_kurv WHERE ID = $r->ID");
	} // End while $r = fetch($q)
	$veksel = $_POST['veksel'];
	query("UPDATE session SET crewSalg = 0 WHERE sID = '".$_COOKIE[$cookiename]."'");
	if($veksel > 0) {
		$tilbake = $veksel - $total;
		header("Location: selg.php?veksel=$tilbake");
	} else
		header("Location: selg.php");
} // End if(action=gjorsalget)

elseif($action == "fjernsalg" && isset($_POST['fjernID'])) {
	$fjernID = $_POST['fjernID'];
	$sID = $_COOKIE[$cookiename];
	$q = query("SELECT * FROM temp_kurv WHERE sID = '$sID' AND ID = $fjernID");
	$r = fetch($q);
	if($r->amount == 1)
		query("DELETE FROM temp_kurv WHERE ID = $fjernID AND sID = '$sID'");
	else query("UPDATE temp_kurv SET amount = amount - 1 WHERE ID = $fjernID");
	header("Location: selg.php");
 } // End if action = fjernsalget


elseif($action == "updateprepared" && isset($_GET['prepared'])) {
	$prepared = $_GET['prepared'];
	$amount = $_POST['amount'];
	query("UPDATE warez SET prepared = '$amount' WHERE barcode = '$prepared'");

	header("Location: selg.php");
} // End if action == updateprepared

elseif($action == "hentet") {
	// action=hentet&amount=ALL&koID=$koR->ID
	$amount = $_GET['amount'];
	$koID = $_GET['koID'];

	if(empty($koID) || empty($amount)) die("WTF am I supposed to do?");
	if($amount == "ALL") query("DELETE FROM temp_ko WHERE ID = $koID");

	else query("UPDATE temp_ko SET amount = amount - $amount WHERE ID = $koID");
	header("Location: selg.php");
}