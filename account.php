<?php
include('global/top.php');
if (empty($_SESSION['ep_user_id'])) {
	header("location:/");												// if not logged in, send user away
}

$pagetitle = 'Account';
include('global/header.php');

if (isset($_POST['changepw'])) {												// check if form has been submitted
	if ($_POST['newpw1'] != $_POST['newpw2']) {
		$scriptresults[] = 'Your new passwords don\'t match.';					// check if new passwords are too long/short and if they match
	} elseif (strlen($_POST['newpw1']) < 6) {
		$scriptresults[] = 'Your new password is too short.';
	}

	if (mysqli_num_rows(mysqli_query($dbc, "SELECT password FROM user WHERE user_id='$_SESSION[ep_user_id]' AND password='" . hash('sha256', $_POST['currentpw']) . "'")) != 1) {
		$scriptresults[] = 'Your current password wasn\'t entered correctly.';	// check if current password was entered correctly
	}
	if (empty($scriptresults)) {												// if no errors, run query and echo relevant message based on result
		echo (mysqli_query($dbc, "UPDATE user SET password='" . hash('sha256', $_POST['newpw1']) . "' WHERE user_id='$_SESSION[ep_user_id]'")) ? 'Your password has been changed' : 'Password could not be changed - system error.';
		echo '<br/><br/>';
	} else {
		foreach ($scriptresults as $scriptresult) {								// othersiwe echo error messages
			echo $scriptresult, '<br/>';
		}
	}
	echo '<br/>';
}

$result = mysqli_query($dbc, "SELECT * FROM user WHERE user_id='$_SESSION[ep_user_id]'");
if (mysqli_num_rows($result) == 1){								// lookup userid and if exists, display details and form to change password
	$userdetails = mysqli_fetch_assoc($result);

	echo 'First Name: ' . htmlentities($userdetails['firstname']) . "<br/>\nLast Name: " . htmlentities($userdetails['lastname']) . "<br/>\nemail: " . htmlentities($userdetails['email']) . "<br/>\n";									// display details
	echo ($userdetails['user_type'] == 1) ? 'Admin User<br/>' : '';
	echo '<br/><form method="post" action="">
	Current Password: <input type="password" name="currentpw"/><br/>
	New Password: <input type="password" name="newpw1"/><br/>
	Confirm Password: <input type="password" name="newpw2"/><br/>
	<input type="submit" name="changepw" value="Change Password">
	</form>';
	echo ($_SESSION['ep_admin'] == 1) ? '<a href="users.php?action=edit&user_id=' . $_SESSION['ep_user_id'] . '">Edit More details</a>' : '';
}																// site to edit more if user is an admin

include('global/footer.php');
?>