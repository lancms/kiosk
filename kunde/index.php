<?php

$mode = "kunde";
require_once '../config.php';



$kundeSID = $_GET['kundeSID'];
$action = $_GET['action'];


if(!isset($kundeSID)) {
	$q = query("SELECT * FROM session");
	echo "Velg bruker å følge etter: ";
	echo "<table>";
	while($r = fetch($q)) {
		echo "<tr><td><a href=?kundeSID=".$r->sID.">$r->sID</a>";
		echo "</td><td>";
		$q2 = query("SELECT name FROM users WHERE ID = $r->userID");
		$navn = fetch($q2);
		echo $navn->name;
		echo "</td></tr>";
	}
	echo "</table>";

}

else {
	include_once 'top.php';
	$q = query("SELECT * FROM temp_kurv WHERE sID = '$kundeSID'");
	if(num($q) == 0) {
		echo "Ingen varer. Kiosken tjener ikke penger :(";
	}

	else {
		$crewsalg = crewsalg($kundeSID);

		if($crewsalg > 1) {
			echo "CREWSALG: ";
			$userID = $crewsalg;

		}
		else {
			echo "Selger: ";
			$quser = query("SELECT * FROM session WHERE sID = '$kundeSID'");
			$ruser = fetch($quser);
			$userID = $ruser->userID;
		}

		$user = query("SELECT * FROM users WHERE ID = '$userID'");
		$useR = fetch($user);
		echo $useR->name;
		echo "<br><br>";
		$total = 0;

		echo "<table>";
		echo "<tr><th>Vare</th><th>Antall</th><th>Varepris</th><th>Totalt</th></tr>";
		while($r = fetch($q)) {
			echo "<tr><td>";

			$winfo = wareinfo($r->wareID);


			echo $winfo->name;
			echo "</td><td>";

			echo $r->amount;

			echo "</td><td>";

			if($crewsalg > 1) $price = $winfo->cPrice;
			else $price = $winfo->price;
			echo $price;

			echo "</td><td>";

			echo $price*$r->amount;
			$total = $total + ($price*$r->amount);

			echo "</td></tr>";

		} // End while

		echo "<tr><td></td><td></td><td></td><td><font color=red size=+5>$total</td></tr>";
	} // end else if num($q) != 0
	echo '</table>';
	include_once 'bottom.php';
} // end else
