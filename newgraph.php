<?php

require_once 'config.php';
$s = $_SERVER["SERVER_SOFTWARE"];

if(stristr($s, "PHP/5")) {
	include_once 'jpgraph5/jpgraph.php';
	include_once 'jpgraph5/jpgraph_bar.php';
}
else {
	include_once 'jpgraph4/jpgraph.php';
	include_once 'jpgraph4/jpgraph_bar.php';
}

$action = $_GET['action'];

if($action == "salg_nar_pa_dognet") {
	$qc = query("SELECT barcode,color FROM warez");
	while($rc = fetch($qc)) {
		$color[$rc->ID] = $rc->color;
		$barcode = $rc->barcode;
		for($i=0;$i<24;$i++) $sale[$barcode][$i] = 0;
	} // End while fetch($rc)
	$q = query("SELECT * FROM history_salg");
	while($r = fetch($q)) {
		$time = date("G", $r->logUNIX);
		$wareID = $r->wareID;
		$price = $r->warePrice;
		$sale[$wareID][$time] = $sale[$wareID][$time] + $price;
		$tidspunkt[$time];
	} // End while

	// okey, lets fin out what colors everything has


	$g = new Graph(310*3,200*3,"auto");
	$g->SetScale("textlin");

	$g->SetShadow();
	$g->img->SetMargin(40,120,20,40);

	$count = 0;
	$plotarray = array();

	$q2 = query("SELECT * FROM warez");
	while($r2 = fetch($q2)) {
		#$check = query("SELECT * FROM history_salg WHERE wareID = $r2->barcode");
		#if(num($check) == 0) break;

		$bplot[$count] = new BarPlot($sale[$r2->barcode]);
		$bplot[$count]->SetLegend($r2->name);
;

		$bplot[$count]->SetFillColor($r2->color);
		$plotarray[] = $bplot[$count];
		$count++;

	} // End while fetch($r2);


	$gbplot = new AccBarPlot($plotarray);
#	$g->Add($bplot[0]);
	$g->Add($gbplot);
	$g->title->Set("Salg fordelt på når på døgnet");
	$g->xaxis->title->Set("Tidspunkt");
	$g->yaxis->title->Set("Kroner");
	$g->xaxis->SetTickLabels($tidspunkt);
	$g->Stroke();




} // End if action = salg når på døgnet

if($action == "stats_top_warez") {
	$warez = query("SELECT * FROM warez");
	while($r = fetch($warez)) {
		$name[] = $r->name;
		$barcolor[] = $r->color;
		$user_money = 0;
		$q = query("SELECT * FROM history_salg WHERE wareID = '$r->barcode'");
		while($r2 = fetch($q)) {
			$user_money = $user_money + $r2->warePrice;
		} // end while $r2

		$all[] = $user_money;
	} // End while($r)

	$graph = new Graph(310*3,200*3,"auto");
	$graph->SetScale("textlin");

	$graph->SetShadow();
	$graph->img->SetMargin(40,20,20,150);

	$b1plot = new BarPlot($all);
	$b1plot->SetFillColor($barcolor);
#	$b1plot->SetLegend("Solgt");
	$b1plot->value->SetFormat('%01.0f');
	$b1plot->value->show();


	$graph->xaxis->SetTickLabels($name);
	$graph->xaxis->SetLabelAngle(90);

	$graph->Add($b1plot);
	$graph->Stroke();
} // end if action = top warez


if($action == "stats_top_wareunits") {
	$warez = query("SELECT * FROM warez");

	while($r = fetch($warez)) {
		$units_sold = 0;
		$name[] = $r->name;
		$barcolors[] = $r->color;

		$q = query("SELECT * FROM history_salg WHERE wareID = '$r->barcode'");
		while($r2 = fetch($q)) {
			$units_sold = $units_sold + 1;
		} // end while $r2

		$all[] = $units_sold;

	} // End while($r)
//die(print_r($all));
	$graph = new Graph(310*3,200*3,"auto");
	$graph->SetScale("textlin");

	$graph->SetShadow();
	$graph->img->SetMargin(40,20,20,150);

	$b1plot = new BarPlot($all);
	$b1plot->SetFillColor($barcolors);
#	$b1plot->SetLegend("Solgt");
	$b1plot->value->SetFormat('%01.0f');
	$b1plot->value->show();


	$graph->xaxis->SetTickLabels($name);
	$graph->xaxis->SetLabelAngle(90);
	$graph->yaxis->title->Set("Antall enheter solgt");

	$graph->Add($b1plot);
	$graph->Stroke();
} // end if action = top wareunits

elseif($action == "top_crewperson")
{
	$qUsers = query("SELECT * FROM users WHERE ID != 1");
	while($rUsers = fetch($qUsers)) {
		$qCount = query("SELECT SUM(warePrice) AS price FROM history_salg WHERE crewSalg = ".$rUsers->ID);
		$r = fetch($qCount);
		if($r->price > 0) echo "<br>".$rUsers->name." -- ".$r->price."\n";
	} // end while

}

/* Dette er malen jeg bruker på jobben:
        $graph = new Graph(310*3,200*3,"auto");
        $graph->SetScale("textlin");

        $graph->SetShadow();
        $graph->img->SetMargin(40,120,20,40);

        $b1plot = new BarPlot($data1y);
        $b1plot->SetFillColor("orange");
        $b1plot->SetLegend("Tynnklienter");
#       $b1plot->value->show();

        $b2plot = new BarPlot($data2y);
        $b2plot->SetFillColor("blue");
        $b2plot->SetLegend("PCer");
#       $b2plot->value->Show();


        $gbplot = new AccBarPlot(array($b1plot,$b2plot));

        $graph->Add($gbplot);


        $graph->title->Set("Klienter/PCer ved hver skole");
        $graph->xaxis->title->Set("Skoler");
        $graph->yaxis->title->Set("Antall");

        $graph->xaxis->SetTickLabels($skoler);

        $graph->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

        $graph->Stroke();
*/
