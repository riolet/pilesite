<?php
if (empty($_POST['firstname']) || empty($_POST['lastname'])) {
	// check if any fields are blank
	$scriptresults[] = 'Fields cannot be blank.';
}
if ((strlen($_POST['firstname']) > 20) || (strlen($_POST['lastname']) > 20)) {
	// check if any fields are too long
	$scriptresults[] = 'Names must be less than 20 characters.';
}
if (mysqli_real_escape_string($dbc, trim(str_replace(" ", "", $_POST['email']))) != $_POST['email']) {
	// check if email has special characters
	$scriptresults[] = 'Invalid email';
}
if ((strlen($_POST['email']) < 4) || (strlen($_POST['email']) > 20)) {
	// check if email is too long/short
	$scriptresults[] = 'email must be between 4 and 20 characters.';
}
$email = mysqli_real_escape_string($dbc, trim(strtolower($_POST['email'])));
$emailinuse = mysqli_query($dbc, "SELECT user_id FROM user WHERE email='$email'");
$user_idinuse = mysqli_fetch_row($emailinuse);
if ((mysqli_num_rows($emailinuse) != 0) && ((mysqli_num_rows($emailinuse) != 1)  || ($_GET['user_id'] != $user_idinuse[0]))) {
	$scriptresults[] = 'email already in use';																	// check if email is in use and not current user
}

if (($_SESSION['ep_user_id'] == $_GET['user_id']) && !isset($_POST['user_type'])) {
	$scriptresults[] = 'Cannot demote current user.';																// check if admin is demoting himself
}

if (empty($scriptresults)) {
	// if no errors
	$user_type = isset($_POST['user_type']) ? 1 : 0;
	$scriptresults[] = (mysqli_query($dbc, "UPDATE user SET firstname='" . mysqli_real_escape_string($dbc, trim($_POST['firstname'])) . "', email='$email', lastname='" . mysqli_real_escape_string($dbc, trim($_POST['lastname'])) . "', user_type='$user_type' WHERE user_id='$_GET[user_id]'")) ?
	htmlentities($_POST['firstname']) . ' ' . htmlentities($_POST['lastname']) . '\'s details updated successfully' :	// run and echo query result
	'System error - user details couldn\'t be updated.<br/><a href="?action=edit&user_id=' . $_GET['user_id'] . '">Go Back</a>';
}

foreach ($scriptresults as $scriptresult) {
	echo $scriptresult, '<br/>';																					// print output to screen regardless of query result
}
?>