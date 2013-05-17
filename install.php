<?php
session_start();
echo '<a href="index.php">Go to homepage</a><br/>';

if (isset($_GET['action']) && !empty($_GET['action'])) {
	// if GET[action] is set

	echo '<li>1 - prerequisites</li>
	<li>2 - database info and configuration</li>
	<li>3 - database connection</li>
	<li>4 - repair tables</li>
	<li>5 - fix admin account</li>
	<li>6 - final thoughts</li><br/><a href="?">Go to installation page main menu</a><br/>';
	if ($_GET['action']) {
		// display sites to various steps of installation page
		for ($a = 1; $a < 7; $a++) {
			if ($a == (int)$_GET['action']) {
				// if current page is x
				echo 'You are on step ' . $a . ' of 6.';							// display 'You are on page x'
			} else {
				echo '<a href="?action=' . $a . '">Step ' . $a . '</a>';			// else display site to page
			}
			echo '<br/>';
		}
	}
	echo '<br/><br/>';

	switch ($_GET['action']) {

		/* site to reset system, clear configuration files, drop tables */
		case 'reset':
			include('global/config.php');
			if (isset($_SESSION['ep_admin']) && ($_SESSION['ep_admin'] == 1)) {
				// only continue if user is admin and logged in
				if (isset($_GET['step']) && ($_GET['step'] == 2)) {
					// only continue if user has confirmed deletion
					$dbc = mysqli_connect(db_host, db_user, db_password, db_name) OR die ('Connection Error: ' . mysqli_connect_error());
					echo (mysqli_query($dbc, "DROP TABLE IF EXISTS `examinerreports`, `markschemes`, `modules`, `papers`, `permissions`, `results`, `users`")) ? 'Tables deleted. Remember to delete all files in /documents directory.<br/><br/>' : '';	// drop all tables
					include('sql/recreate.php');
					echo (mysqli_multi_query($dbc, $recreate)) ? 'Tables recreated.' : '';					// recreate all tables
				} else {																// display confirmation prompt
					echo 'Continuing will mean loss of ALL data and records - are you sure? <a href="?action=1&step=2">Yes</a>/<a href="?">No</a>';
				}
			} else {																	// if not logged in, display error message
				echo 'You must be registered as an administrator to reset the system. <a href="?action=">Back</a>';
			}

			break;

			/* prerequisites for setup */
		case 1:
			?>
<a href="?action=2&step=1">Continue</a>
<li>Make sure /global/config.php and /documents is writable</li>
<li>Make sure all files exist</li>
<li>Create a new mysql database and have login details on hand</li>
<li>Delete this file after installation</li>

<?php
break;

/* enter configuration details and write to file */
case 2:

	if (isset($_GET['step']) && ($_GET['step'] == 2)) {
		// if user is on page 2 of step
		if (isset($_POST['submit'])) {
			// if user has submitted form, process by updating the config file
			$confighandle = fopen('global/config.php', 'w') or die('Error: cannot open file.');
			fwrite($confighandle, "<?php\ndefine ('title_prefix', '" . $_POST['titleprefix']);
			fwrite($confighandle, "');\ndefine ('root_directory', '" . $_POST['rootdirectory']);
			fwrite($confighandle, "');\ndefine ('root_address', 'http://' . \$_SERVER['SERVER_NAME'] . root_directory);\ndefine ('db_host', '" . $_POST['dbhost']);
			fwrite($confighandle, "');\ndefine ('db_user', '" . $_POST['dbuser']);
			fwrite($confighandle, "');\ndefine ('db_password', '" . $_POST['dbpassword']);
			fwrite($confighandle, "');\ndefine ('db_name', '" . $_POST['dbname'] . "');\n?>");
			fclose($confighandle);
			echo "If no error appears above, configuration file was successfully changed. Otherwise, please check file permissions for global/config.php.<br/>";
		}
		include('global/config.php');

		echo 'Configuration details are as follows:';					// display latest details, regardless of having just updated or not
		echo '<br/>Title Prefix: ';
		echo defined('title_prefix')? title_prefix : '';
		echo '<br/>Root Directory: ';
		echo defined('root_directory')? root_directory : '';
		echo '<br/>Database Host: ';
		echo defined('db_host')? db_host : '';
		echo '<br/>Database User: ';
		echo defined('db_user')? db_user : '';
		echo '<br/>Database Name: ';
		echo defined('db_name')? db_name : '';
		echo '<br/>Database Password: ';
		echo defined('db_password')? db_password : '';

		echo '<br/>Click <a href="?action=2&step=1">here</a> to re-enter details, or <a href="?action=3&step=1">here</a> to continue.';

	} else {															// if user is on page 1 of setup

		include('global/config.php');								// display form with current details pre-filled
		?>
<form name="updateconfig" method="post" action="?action=2&step=2">
	Page title Prefix <input name="titleprefix" type="text"
		value="<?php echo defined('title_prefix') ? title_prefix : ''; ?>" /><br />
	Root Directory (usually '/', must end and start with '/'): <input
		name="rootdirectory" type="text"
		value="<?php echo defined('root_directory') ? root_directory : ''; ?>" /><br />
	Database Host <input name="dbhost" type="text"
		value="<?php echo defined('db_host') ? db_host : ''; ?>" /><br />
	Database Name <input name="dbname" type="text"
		value="<?php echo defined('db_name') ? db_name : ''; ?>" /><br />
	Database User <input name="dbuser" type="text"
		value="<?php echo defined('db_user') ? db_user : ''; ?>" /><br />
	Database Password <input name="dbpassword" type="text"
		value="<?php echo defined('db_password') ? db_password : ''; ?>" /> <input
		name="submit" type="submit" value="Continue" />
</form>
<a href="?action=3">Skip to step 3</a>
<br />
<br />

<?php
	}
	break;

	// check database connection
case 3:
	include('global/config.php');
	$dbc = mysqli_connect(db_host, db_user, db_password, db_name) OR die ('Connection Error: ' . mysqli_connect_error() . '<br/>Please check and confirm database configuration <a href="?action=2">here</a>.');			// attempt query connection, and echo error message if failure
	if ($dbc) {
		// if success, echo details and success message
		echo 'Database connection successful. <a href="?action=4&step=1">Continue here.</a><br/>email: ' . db_name . '<br/>Password: ' . db_password . '<br/>Host: ' . db_host . '<br/>Database: ' . db_name;
	}
	break;

	// check and repair all tables
case 4:
	include('global/config.php');
	$dbc = mysqli_connect(db_host, db_user, db_password, db_name) OR die ('Connection Error: ' . mysqli_connect_error());
	include('sql/recreate.php');
	echo (mysqli_multi_query($dbc, $recreate)) ? 'Tables recreated. <a href="?action=5&step=1">continue</a>' : 'Error occured - check mySQL server configuration <a href="?action=2">here</a>.';					// attempt to repair all tables and echo message depending on result
	break;


	// check or create user account
case 5:
	include('global/config.php');
	$dbc = mysqli_connect(db_host, db_user, db_password, db_name) OR die ('Connection Error: ' . mysqli_connect_error());
	$admincount = mysqli_num_rows(mysqli_query($dbc, "SELECT `user_id` FROM `user` WHERE `user_type`='1' AND `email`='admin'"));	// check 'admin' exists
	if (isset($_POST['submit'])){
		if(($_POST['newpw'] == $_POST['pwconfirm']) && !empty($_POST['newpw'])){
			// if passwords are the same and form submitted, run script
			if ($admincount == 1){
				// if an administrator called 'admin' exists
				echo (mysqli_query($dbc, "UPDATE `user` SET `password`='" . hash('sha256',$_POST['newpw']) . "' WHERE `email`='admin'")) ? 'Password updated.<br/>' : 'Password not updated.<br/>';						// update password
			} else {																// otherwise
				$deleteadmin = (mysqli_num_rows(mysqli_query($dbc, "SELECT * FROM `user` WHERE `email`='admin'")) != 0) ? mysqli_query($dbc, 'DELETE FROM user WHERE email="admin"') : '';						// if "admin" exists, but isn't admin user_type, delete it
				echo (mysqli_query($dbc, "INSERT INTO `user` (`user_type`, `email`, `password`, `firstname`, `lastname`) VALUES ('1', 'admin', '" . hash('sha256',$_POST['newpw']) . "', 'Default', 'Administrator')")) ? 'Admin created.' : 'Admin creation error.';
				$admincount = mysqli_num_rows(mysqli_query($dbc, "SELECT `user_id` FROM `user` WHERE user_type='1' AND email='admin'"));	// create new admin
			}
		} else {
			echo 'Passwords didn\'t match/empty.<br/>';								// echo error message if passwords aren't the same
		}
	}

	echo ($admincount == 1) ? 'Enter passwords below to update default administrator password for "admin". Or <a href="?action=6&step=1">continue</a>' : 'Default administrator not setup, enter password below to create new user called admin';
	?>
<form name="createadmin" method="post" action="?action=5&step=2">
	Enter password twice. <input name="newpw" type="password" /> <input
		name="pwconfirm" type="password" /> <input name="submit" type="submit"
		value="create admin" />
</form>

<?php
break;

case 6:				// tasks to do, sites, and short guide
	include('global/config.php');
	echo 'Setup/repair complete. Go the the <a href="index.php">home page</a>.';
	break;
	}						// end action switch
} else {																		// if GET[action] not set, display options
	echo '<a href="?action=reset">Reset system - requires admin account</a><br/><a href="?action=1">Repair/set up system</a><br/>
	<li>1 - prerequisites</li>
	<li>2 - database info and configuration</li>
	<li>3 - database connection</li>
	<li>4 - repair tables</li>
	<li>5 - fix admin account</li>';
}
?>