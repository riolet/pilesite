<?php // activate.php
// This page activates the user's account.

include('global/top.php');

// Validate $_GET['x'] and $_GET['y']:
$x = $y = FALSE;
if (isset($_GET['x']) && preg_match ('/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/', $_GET['x']) ) {
	$x = $_GET['x'];
}
if (isset($_GET['y']) && (strlen($_GET['y']) == 32 ) ) {
	$y = $_GET['y'];
}

// If $x and $y aren't correct, redirect the user.
if ($x && $y) {

	// Update the database...
	$q = "UPDATE user SET activate='' WHERE (email='" . mysqli_real_escape_string($dbc, $x) . "' AND activate='" . mysqli_real_escape_string($dbc, $y) . "') LIMIT 1";
	$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

	// Print a customized message:
	if (mysqli_affected_rows($dbc) == 1) {
		header("Location: login.php?active=active");
	} else {
		header("Location: login.php?active=no");
	}

	mysqli_close($dbc);

} else { // Redirect.
	header("Location: login.php?active=no");
}


?>