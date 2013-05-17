<?php
include('global/top.php');
if (empty($_SESSION['ep_user_id'])) {
	header("location:login.php");
}
if ($_SESSION['ep_admin'] != 1){
	// send user away if not admin/logged in
	header("location:login.php?action=admin");
}
$pagetitle = 'Manage Users';
include('global/header.php');
// if GET[action] is to edit or delete user
if (isset($_GET['action']) && (($_GET['action'] == 'delete') || ($_GET['action'] == 'edit')) && isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
	$selectuserquery = "SELECT * FROM user WHERE user_id=$_GET[user_id]";
	$selectuser = mysqli_query($dbc, $selectuserquery);
	if (mysqli_num_rows($selectuser) == 1) {
		if ($_GET['action'] == 'delete') {
			// if action is to delete user
			include ('includes/users_delete.php');
		} else {																		// if action is to edit user
			$userdetails = mysqli_fetch_assoc($selectuser);
			if (isset($_POST['edit']) && ($_POST['edit'] == 'details')) {
				// if editing details
				include ('includes/users_updatedetails.php');
				$userdetails = mysqli_fetch_assoc(mysqli_query($dbc, $selectuserquery));		// query to get updated information after edit
			} elseif (isset($_POST['edit']) && ($_POST['edit'] == 'password')) {
				include ('includes/users_updatepassword.php');								// if editing password
			}


			?>
<a href="?action=delete&user_id=<?php echo $_GET['user_id']; ?>">Delete</a>
<br />
<form name="edituser" method="post" action="">
	User ID:
	<?php echo $userdetails['user_id']; ?>
	<br /> First Name: <input name="firstname" type="text"
		value="<?php echo htmlentities($userdetails['firstname']); ?>" /><br />
	Last Name: <input name="lastname" type="text"
		value="<?php echo htmlentities($userdetails['lastname']) ?>" /><br />
	email: <input name="email" type="text"
		value="<?php echo htmlentities($userdetails['email']) ?>" /><br />
	Admin: <input name="user_type" type="checkbox" value="admin"
	<?php echo ($userdetails['user_type'] == 1) ? ' checked="yes"' : ''; ?> /><br />
	<input name="edit" type="hidden" value="details" /><input type="submit"
		name="submit" value="Update Details">
</form>
<br />
<br />

<form name="editpassword" method="post" action="">
	Password: <input name="newpw1" type="password" /><br /> Confirm
	Password: <input name="newpw2" type="password" /><br /> <input
		name="edit" type="hidden" value="password" /> <input type="submit"
		name="submit" value="Update Password">
</form>
<br />
<br />
<a href="?user_id=<?php echo $_GET['user_id']; ?>">View User</a>
|
<a href="?">View All</a>

<?php
echo ($_SESSION['ep_user_id'] == $_GET['user_id']) ? ' | <a href="account.php">Back to account page</a>' : '';
		}																				// endif for edit user
	} elseif ($selectuser == true) {
		echo 'User not found<br/><a href="javascript:history.go(-1)">Go Back</a>';			// if query succeeded, but didn't return 1 user
	} else {
		echo 'System error<br/><a href="javascript:history.go(-1)">Go Back</a>';			// if query failed
	}
} elseif (isset($_GET['action']) && ($_GET['action'] == 'create')) {
	// if creating user, not edit nor delete
	if (isset($_POST['submit'])) {
		$createsuccess = false;																// assume creation failure first, then include script
		include ('includes/users_create.php');
	}
	echo (isset($_POST['submit']) && !$createsuccess) ? '<a href="?action=create">Clear form</a><br/>' : '';
	?>

<form name="createuser" method="post" action="">
	First Name: <input name="firstname" type="text"
		value="<?php echo (isset($_POST['submit']) && !$createsuccess) ? $_POST['firstname'] : ''; ?>" /><br />
	Last Name: <input name="lastname" type="text"
		value="<?php echo (isset($_POST['submit']) && !$createsuccess) ? $_POST['lastname'] : ''; ?>" /><br />
	email: <input name="email" type="text"
		value="<?php echo (isset($_POST['submit']) && !$createsuccess) ? $_POST['email'] : ''; ?>" /><br />
	Admin: <input name="user_type" type="checkbox" value="admin"
	<?php echo (isset($_POST['submit']) && !$createsuccess && isset($_POST['user_type'])) ? ' checked="yes"' : ''; ?> /><br />
	<input type="submit" name="submit" value="Create New User">
</form>
<a href="?">Back to Viewing page</a>


<?php
} else {																						// if action not set, show table to display users
	echo '<a href="?action=create">Create User</a><br/>';
	include('includes/users_listusers.php');
}																								// end-else displaying list of users
include('global/footer.php');
?>