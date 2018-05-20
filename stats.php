<?php
require_once 'config.php';

include_once 'top.php';

$page = $_GET['page'];

if(isset($page)) {
	echo "<img src=$page.php>";

}
else {
	echo "<a href=stats.php?page=stats_top_sales>Salg pr. selger</a>";
#	echo "<br><a href=stats.php?page=stats_top_warez>Topp solgte varer etter omsatte kroner</a>";
	echo "<br><a href=newgraph.php?action=stats_top_wareunits>Topp solgte varer etter antall enheter solgt</a>";
	echo "<br><a href=newgraph.php?action=stats_top_warez>Topp solgte varer etter omsatte kroner</a>";
#	echo "<br><a href=stats.php?page=stats_top_hours>Salg fordelt på timer på døgnet</a>";
	echo "<br><a href=newgraph.php?action=salg_nar_pa_dognet>Salg fordelt på timer på døgnet</a>";
	echo "<br><a href=stats.php?page=stats_top_earnings>Topp solgte varer etter tjente kroner</a>";
}


include_once 'bottom.php';
