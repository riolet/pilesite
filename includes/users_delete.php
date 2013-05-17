<?php
if (isset($_GET['confirm']) && ($_GET['confirm'] == 'true')) {
	// check if user has confimed deletion
	if ($_GET['user_id'] == $_SESSION['ep_user_id']) {
		// if user trying to delete himself
		echo 'Error - cannot delete current user.';										// echo error message
	} else {
		echo (mysqli_query($dbc, "DELETE FROM user USING user LEFT JOIN pile USING (user_id) WHERE user_id='$_GET[user_id]'")) ? 'User deleted <a href="?">back</a>' : 'Query error.';																// if not deleting himself, run and echo query result
	}
} else {
	if ($_GET['user_id'] == $_SESSION['ep_user_id']) {
		// else ask if user confirms deletion
		echo 'Cannot delete current user.';												// if user trying to delete himself, echo error message instead
	} else {
		echo 'Are you sure? This will also delete all user data. <a href="?action=delete&confirm=true&user_id=' . $_GET['user_id'] . '">Yes</a>/<a href="?user_id=' . $_GET['user_id'] . '">No</a>';															// otherwise ask if user really wants to continue
	}
}
?>