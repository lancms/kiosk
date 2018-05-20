&nbsp;|&nbsp;<?php

//meny("index.php", "Hovedsiden");
if(getuserrank() == -1) {
// User not logged in
}


if(getuserrank() >= 0) {
// user is salesperson or higher
meny("selg.php", "Selg!");

meny("stats.php", "Statistikk");

}


if(getuserrank() == 1) {
// user is admin

meny("admin.php", "Admin");
}

function meny($link, $text) {
echo "<a href=$link>$text</a>&nbsp;|&nbsp;";
}
