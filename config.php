<?php

mysql_connect("localhost", "kiosk", "kioskpass");
mysql_select_db("kiosk");

$session_alive_time = 18000;
$cookiename = "GlobeKiosk";

$rank[0] = "Selger";
$rank[1] = "Admin";

$crewsalg[0] = "Crewsalg er av";
$crewsalg[1] = "Crewsalg er P�!";


$color[] = "red";
$color[] = "yellow";
$color[] = "green";
#$color[] = "pink"; // Does not validate
$color[] = "brown";
$color[] = "purple";
$color[] = "blue";
$color[] = "black";
$color[] = "white";
$color[] = "grey";
#$color[] = "orange"; // Does not validate
$color[] = "maroon";
$color[] = "lime";
$color[] = "navy";
$color[] = "aqua";
$color[] = "teal";



$copyright[] = "Kiosk er copyright Laaknor. Lisens er gitt Globe til fri bruk, s� lenge det er til ikke-kommerisielt bruk";
$copyright[] = "For � bytte til en annen selger: kj�r strekkoden p� kortet ditt, og velg 'Bytt bruker'";
$copyright[] = "Lei av � bruke musa? shift+alt+s er hurtigtasten for � trykke 'Selg!' (firefox2)";
$copyright[] = "Lei av � bruke musa? alt+s er hurtigtasten for � trykke 'Selg!' (Internet Exploiter)";
$copyright[] = "Lei av � bruke musa? trykk &lt;tab&gt; for � hoppe til vinduet for � fullf�re salget";


require_once 'session.php';
require_once 'functions.php';
sort($color);


if(getuserrank() == -1 && $mode != 'kunde') include 'login.php';

