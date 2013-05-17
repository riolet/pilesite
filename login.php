<?php
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
	// if GET[action] wants to logout
	session_start();
	$_SESSION = array();
	session_destroy();
	setcookie('PPSESSID', '', time()-3600);
	header("location:index.php?status=out");							// send user to homepage to say goodbye
	exit;																// prevents any more headers from being sent
}

include('global/top.php');
$pagetitle = 'Login';
include('global/header.php');

if (isset($_GET['active'])){

	if ($_GET['active'] == 'active') {
		echo 'Your account has been activated - please login below.';
	} else {
		echo 'Your account could not be activated. Please re-check the site or contact the system administrator.';
	}
}

if (isset($_POST['submitted'])) {
	// if form has been submitted
	if (!empty($_POST['email']) && !empty($_POST['password'])) {
		// check if details are empty
		$result = mysqli_query($dbc, "SELECT * FROM user WHERE email='" . mysqli_real_escape_string($dbc, trim($_POST['email'])) . "' and password='" . hash('sha256',$_POST['password']) . "'");

		if ($result) {
			if (mysqli_num_rows($result) == 1) {

				// if query success and one user with same credentials, set session
				$row = mysqli_fetch_assoc($result);

				if ($row['activate'] == ''){
					$_SESSION['ep_user_id'] = $row['user_id'];
					$_SESSION['ep_admin'] = ($row['user_type'] == 1) ? 1 : 0;					// set admin session to 1 if user is admin
					header("location:home.php?status=in");							// if login successful, redirect to homepage
				} else {
					$body = '
					Hi

					Click below to confirm your account:

					http://www.pilesite.com/activate.php?x=' . urlencode($row['email']) . '&y=' . $row['activate'] . '

					Regards,
					Admin';


					mail($row['email'], 'Registration Confirmation', $body, 'From: blackhole@pilesite.com');
					echo 'Account not activated yet! Activation email has been resent to ' . $row['email'] . '.';
				}
			} else {
				$scriptresult = 'Invalid details';								// if no. of users with same credentials isn't 1, echo error message
			}
		} else {
			$scriptresult = 'Query error';										// if query error, echo error message
		}
	} else {
		$scriptresult = 'Details cannot be blank.';								// if details blank, output error message
	}
}

if(isset($_GET['action']) && $_GET['action'] == 'admin') {
	// if GET[action] is 'admin', page that redirected here required admin access
	echo 'Admin access only - please login below';
}

if (isset($scriptresult)) {
	echo $scriptresult . '<br/><br/>';											// if login attempt fails, notify user
}
?>
<form method="post" action="?">
	Email<br /> <input name="email" type="text"
		value="<? echo (isset($_POST['email'])) ? $_POST['email'] : ''; ?>" /><br />
	Password<br /> <input name="password" type="password" /><br /> <input
		name="submitted" type="submit" value="Login" />
</form>
<br/><br/>

<?php
include('global/footer.php');
?>