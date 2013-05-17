<?php
if (empty($_POST['firstname']) || empty($_POST['lastname'])) {
	// check if names are empty
	$scriptresults[] = 'Fields cannot be blank.';
}
if ((strlen($_POST['firstname']) > 20) || (strlen($_POST['lastname']) > 20)) {
	// check if names are too long
	$scriptresults[] = 'Names must be less than 20 characters.';
}
if (mysqli_real_escape_string($dbc, trim(str_replace(" ", "", $_POST['email']))) != $_POST['email']) {
	$scriptresults[] = 'Invalid email';														// check if email has special characters or spaces
}
if ((strlen($_POST['email']) < 4) || (strlen($_POST['email']) > 20)) {
	$scriptresults[] = 'email must be between 4 and 20 characters.';							// check if email is too long/short
}
$email = mysqli_real_escape_string($dbc, trim(strtolower($_POST['email'])));
if (mysqli_num_rows(mysqli_query($dbc, "SELECT user_id FROM user WHERE email='$email'")) != 0) {
	$scriptresults[] = 'email already in use';												// check if email is already in use
}

if (empty($scriptresults)) {
	// if no errors
	$user_type = isset($_POST['user_type']) ? 1 : 0;
	if (mysqli_query($dbc, "INSERT INTO user (`user_type`, `email`, `password`, `firstname`, `lastname`) VALUES ('$user_type', '$email', '" . hash('sha256',$_POST['firstname'].$_POST['lastname']) . "', '" . mysqli_real_escape_string($dbc, trim($_POST['firstname'])) . "', '" . mysqli_real_escape_string($dbc, trim($_POST['lastname'])) . "')")) {
		// and query success
		$createsuccess = true;																			// set $createsuccess to true
		$successmessage = htmlentities($_POST['firstname'] . ' ' . $_POST['lastname']) . '\'s details inserted successfully. email is ' . htmlentities($_POST['email']) . ', password is ' . $_POST['firstname'].$_POST['lastname'];						// append new user info to $successmessage
		$successmessage .= ($user_type == 1) ? ', user has administrator priviledges' : '';				// append info if new user is admin
		$scriptresults[] = $successmessage;															// add $successmessage to output array
	} else {
		$scriptresults[] = 'System error - user details couldn\'t be added.';					// else display error message
	}
}
foreach ($scriptresults as $scriptresult) {
	echo $scriptresult, '<br/>';																// echo contents of output array regardless of query success
}
?>