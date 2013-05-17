<?php
if ($_POST['newpw1'] != $_POST['newpw2']) {
	$scriptresults[] = 'Your new password confirmation doesn\'t match.';			// check if password confirmation matches
}
if (strlen($_POST['newpw1']) < 6) {
	$scriptresults[] = 'Passwords have to be longer than 6 characters.';			// check if password is long enough to be secure
}
if (empty($scriptresults)) {
	$scriptresults[] = (mysqli_query($dbc, "UPDATE user SET password='" . hash('sha256', $_POST['newpw2']) . "' WHERE user_id='$_GET[user_id]'")) ? 'Password updated.' : 'Error - password not changed - contact system adminstrator';					// if no errors, run query and add query result to output
}
foreach ($scriptresults as $scriptresult) {
	echo $scriptresult, '<br/>';													// echo output to page regardless of success/failure
}
?>