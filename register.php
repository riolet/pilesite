<?php
include('global/top.php');
if (!empty($_SESSION['ep_user_id'])) {
	header("location:home.php");
}

$pagetitle = 'Register';
include('global/header.php');
// if GET[action] is to edit or delete user


if (isset($_POST['submit'])) {
	$createsuccess = false;																// assume creation failure first, then include script


	if ($_POST['newpw1'] != $_POST['newpw2']) {
		$scriptresults[] = 'Your new password confirmation doesn\'t match.';			// check if password confirmation matches
	}
	if (strlen($_POST['newpw1']) < 6) {
		$scriptresults[] = 'Passwords have to be longer than 6 characters.';			// check if password is long enough to be secure
	}

	if (empty($_POST['firstname']) || empty($_POST['lastname'])) {
		// check if names are empty
		$scriptresults[] = 'Fields cannot be blank.';
	}
	if ((strlen($_POST['firstname']) > 20) || (strlen($_POST['lastname']) > 20)) {
		// check if names are too long
		$scriptresults[] = 'Names must be less than 20 characters.';
	}
	
	$email = checkemail($_POST['email']);
	
	if ($email == '') {
		$scriptresults[] = 'Invalid email';														// check if email valid
	}
	
	$email = mysqli_real_escape_string($dbc, $email);
	if (mysqli_num_rows(mysqli_query($dbc, "SELECT user_id FROM user WHERE email='$email'")) != 0) {
		$scriptresults[] = 'Email already in use';												// check if email is already in use
	}

	if (empty($scriptresults)) {
		// if no errors
		$user_type = isset($_POST['user_type']) ? 1 : 0;
		$a = md5(uniqid(rand(), true));
		if (mysqli_query($dbc, "INSERT INTO user (`user_type`, `email`, `password`, `firstname`, `lastname`, `activate`) VALUES ('0', '$email', '" . hash('sha256', $_POST['newpw2']) . "', '" . mysqli_real_escape_string($dbc, trim($_POST['firstname'])) . "', '" . mysqli_real_escape_string($dbc, trim($_POST['lastname'])) . "', '" . $a . "')")) {
			// and query success
			$createsuccess = true;																			// set $createsuccess to true
			$body = '
Hi

Thank you for registering on PileSite. Please click below to confirm your account:

http://www.pilesite.com/activate.php?x=' . urlencode($email) . '&y=' . $a . '

Regards,

PileSite
"A new way to share and discover the web!"';

			mail($email, 'Registration Confirmation', $body, 'From: blackhole@pilesite.com');
			$successmessage = htmlentities($_POST['firstname'] . ' ' . $_POST['lastname']) . ' has been registered successfully. Please check your email for activation email.';						// append new user info to $successmessage
			$scriptresults[] = $successmessage;															// add $successmessage to output array
		} else {
			$scriptresults[] = 'System error - user details couldn\'t be added.';					// else display error message
		}
	}
	foreach ($scriptresults as $scriptresult) {
		echo $scriptresult, '<br/>';																// echo contents of output array regardless of query success
	}

}
echo (isset($_POST['submit']) && !$createsuccess) ? '<a href="?action=create">Clear form</a><br/>' : '';
?>

<form name="createuser" method="post" action="">
	First Name: <input name="firstname" type="text"
		value="<?php echo (isset($_POST['submit']) && !$createsuccess) ? $_POST['firstname'] : ''; ?>" /><br />
	Last Name: <input name="lastname" type="text"
		value="<?php echo (isset($_POST['submit']) && !$createsuccess) ? $_POST['lastname'] : ''; ?>" /><br />
	Email: <input name="email" type="text"
		value="<?php echo (isset($_POST['submit']) && !$createsuccess) ? $_POST['email'] : ''; ?>" /><br />
	Password: <input name="newpw1" type="password" /><br /> Confirm
	Password: <input name="newpw2" type="password" /><br /> 
    <p class="submit">  <input type="submit" name="submit" value="Submit"></p>
</form>


<?php
// end-else displaying list of users
include('global/footer.php');
?>