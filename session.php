<?php



// connect to database

#db_connect();



/*

*****************************

* Session functions         *

*****************************

Various functions for session information

*/



// update session, or create a new session ID.

// this MUST run each time the page shall be displayed.

$userIP = $HTTP_SERVER_VARS['REMOTE_ADDR'];



if(!isset($_COOKIE[$cookiename]))

{

    srand((double)microtime()*1000000); // create new seed

    $new_session = md5(rand(0,9999999)); // random session ID



    session_id($new_session); // set session ID



    session_start(); // start session

    setcookie($cookiename, $new_session); // remember session
    
    if($usestats) require_once 'stats_sessionstart.php';



    mysql_query("INSERT INTO session (userID, sID, logUNIX, IP) VALUES(1, '$new_session',".time().", '$userIP')")

        or die("Could not create session : ".mysql_error());

}

else

{

	$sID = $_COOKIE[$cookiename];

	$check_session = mysql_query("SELECT * FROM session WHERE sID = '$sID'");

	if(mysql_num_rows($check_session) != 0) {

    	session_id($_COOKIE[$cookiename]); // set session, this cookie must expire after session end.

	} else {

		setcookie($cookiename, "", time()-$session_alive_time);

	}



}



// update current session so it will not be deleted during the clean-up.

if(getcurrentuserid())

{

    mysql_query("UPDATE session SET logUNIX = ".time()." WHERE userID = ".getcurrentuserid()." AND IP = '$userIP'")

        or die("Could not update current session : ".mysql_error());

}



// clean database for abandoned session

$remove_time = time()-$session_alive_time;



mysql_query("DELETE FROM session WHERE logUNIX < '$remove_time'")

    or die("Could not clean session table : ".mysql_error());





// getuserid(sID) returns the user ID from a session

function getuserid($sid)

{



    $dbs = mysql_query("SELECT userID FROM session WHERE sid = '$sid'") // search for session ID table "session" in the DB.

        or die(mysql_error());



    if(($dbs == null) || (!mysql_num_rows($dbs))) // if the user is not logged in, or found in the DB, for various reasons,

        return 0;   // return 0.



    $row = mysql_fetch_row($dbs); // get first row (should be the only row returned)


	if(!empty($row[0]))
    return $row[0]; // return userID
    else return 1;

}



function getuseridx($nick, $password)

{

    $password = crypt_pwd($password);



    $dbs = mysql_query("SELECT ID, password FROM users WHERE nick LIKE '$nick'")

        or die(mysql_error());



    $row = mysql_fetch_row($dbs);



    //if(($row == null) || (mysql_num_rows($row) == 0))

    //    return -1;



    if($row[1] != $password)

        return -2;



    return $row[0];

}



// getcurrentuserid() returns the user ID of the current processing user.

function getcurrentuserid()

{

    $uid = getuserid(session_id()); // call getuserid and pass the current session ID.
    if($uid == NULL || $uid == FALSE) $uid = 1;
    return $uid;

}

function getuserrank()  {

	$user = getcurrentuserid();

	$query = mysql_query("SELECT level FROM users WHERE ID = '$user'") or die(mysql_error());

	$row = mysql_fetch_row($query);

	return $row[0];

}

// log_in(nick, password) retrieves the user ID from nick and logs the user in the database.

function log_in($nick, $password)

{

    // Returns 0  if the user is already logged in.

    // Returns -1 if the user is not found in the database

    // Returns -2 if the password does not match.

    // Returns -3 if the user has not verified yet

    // Returns positive value if the login was successful



    if(getcurrentuserid() != 1)  // this user is already logged in.

        return 0;



    $password = crypt_pwd($password); // hash the password



    $dbs = mysql_query("SELECT ID, password, verified FROM users WHERE nick LIKE '$nick'")

        or die("Log in failed : ".mysql_error()."\n<br>Please contact the administrator");



    //if((!$dbs) || (mysql_num_rows($dbs) == 0))

    //    return -1; // this user is not found in the database



    $row = mysql_fetch_row($dbs);



    if($password != $row[1])

    {

        return -2;

    }

    if($row[2] != 0) {

        return -3;

    }



    $uid = $row[0]; // get user id



    $sid = session_id(); // get session ID



    $dbs = mysql_query("UPDATE session SET userID = $uid WHERE sID = '$sid'") // update database

        or die(mysql_error());



    return 1;

}



// log_out() logs out the current user in session.

function log_out()

{

    if(!getcurrentuserid()) // check if the user is actually logged in

        return 0;



    // Update session

    mysql_query("UPDATE session SET userID = 1 WHERE sID = '".session_id()."'")

        or die("Could not log out: ".mysql_error());



    //setcookie("sID", "");

    $_SESSION['userrank'] = "";



    return 1;



}







?>
