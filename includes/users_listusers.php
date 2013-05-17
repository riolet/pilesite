<?php
if (mysqli_num_rows(mysqli_query($dbc, "SELECT email FROM user")) != 0) {
	// check there are users in the system
	echo '<a href="?">View All</a>';
	$columns = array(
			array('User Type', 'user_type'),
			array('User ID', 'user_id'),
			array('First Name', 'firstname'),
			array('Last Name','lastname'),
			array('Email','email')
	);		// set up column details
	$urlparameter = '';
	$queryparameter = '';
	$urlgetarrayvalues = '';
	foreach ($_GET as $parameter=>$parametervalue) {
		// aggregate all GET elements into single string
		$urlgetarrayvalues .= ($parametervalue=='') ? '' : $parameter . '=' . urlencode($parametervalue) . '&';
	}
	echo '<br/>';
	foreach ($columns as $field) {
		// keep track of conditions for selecting query results
		if ((isset($_GET[$field[1]]) && ($_GET[$field[1]] == '')) || (!isset($_GET[$field[1]]))) {
			continue;
		}
		echo $field[0] . ' - ' . htmlentities($_GET[$field[1]]) . ' <a href="?' . $urlgetarrayvalues . 'start=&pages=&' . $field[1] . '=">Remove</a><br/>';	// site to remove condition
		$urlparameter .= '&' . $field[1] . '=' . urlencode($_GET[$field[1]]);
		$queryparameter .= (empty($queryparameter)) ? 'WHERE ' : ' AND ';
		$queryparameter .= $field[1] . "='" . mysqli_real_escape_string($dbc, trim($_GET[$field[1]])) . "'";	// set up query with conditions
	}
	$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'user_type';					// get the GET data
	$orderby2 = isset($_GET['orderby2']) ? $_GET['orderby2'] : 'lastname';
	$ordersort = (isset($_GET['ordersort']) && ($_GET['ordersort'] == 'desc')) ? 'desc' : 'asc';
	$display = (isset($_GET['display']) && is_numeric($_GET['display']) && ($_GET['display'] > 0)) ? $_GET['display'] : 12;			// display 10 results per page by default
	$start = (isset($_GET['start']) && (is_numeric($_GET['start']))) ? $_GET['start'] : 0;					// record to start displaying from
	$countusers = mysqli_fetch_row(mysqli_query($dbc, "SELECT COUNT(user_id) FROM user $queryparameter"));
	$pages = ($countusers[0] > $display) ? ceil($countusers[0]/$display) : 1;								// pages there are
	$currentpage = ($start/$display) + 1;															// current page to calculate pagination

	$listusers = mysqli_query($dbc, "SELECT * FROM user $queryparameter ORDER BY " . mysqli_real_escape_string($dbc, trim($orderby)) . " $ordersort, " . mysqli_real_escape_string($dbc, trim($orderby2)) . " LIMIT $start, $display");
	// query for displaying results
	if (!$listusers) {
		echo 'Query error.';
	} elseif (mysqli_num_rows($listusers) == 0) {
		// if count=0, the prescribed conditions have returned no results
		echo 'No users found<br/><br/>';
	} else {
		echo '<br/>' . $countusers[0] . ' records found<br/>';
		echo '<table><tr><td>Edit</td><td>Delete</td>';
		foreach ($columns as $columndetails) {
			// go through each column to display
			echo '<td><a href="?display=' . $display . '&ordersort=' . $ordersort . '&start=' . $start . '&pages=' . $pages . '&orderby=' . $columndetails[1] . '&orderby2=' . $orderby . $urlparameter . '">' . $columndetails[0] . '</a></td>';						// each site will sort results by that column
		}
		echo '</tr>';
		while ($row = mysqli_fetch_assoc($listusers)) {
			// for each user, sites to edit/delete them
			echo '<tr>
			<td><a href="?action=edit&user_id=' . $row['user_id'] . '">Edit</a></td>
			<td><a href="?action=delete&user_id=' . $row['user_id'] . '">Delete</a></td>
			<td><a href="?display=' . $display . '&ordersort=' . $ordersort . '&orderby=' . $orderby . '&orderby2=' . $orderby2 . $urlparameter . '&user_type=';
			echo (!isset($_GET['user_type']) || (isset($_GET['user_type']) && ($_GET['user_type'] == ''))) ? $row['user_type'] : '';				// if no condition set, site defines condition, otherwise removes condition
			echo '">';
			echo ($row['user_type'] == 1) ? 'Admin' : 'User';			// for user type, use more friendly words than 0 and 1, so not included in loop
			echo '</a></td>';
			for ($i=1; $i<5; $i++) {
				// for each column, clicking in the field will display results with only that attribute
				echo '<td><a href="?display=' . $display . '&ordersort=' . $ordersort . '&orderby=' . $orderby . '&orderby2=' . $orderby2 . $urlparameter . '&' . $columns[$i][1] . '=';
				echo (!isset($_GET[$columns[$i][1]]) || (isset($_GET[$columns[$i][1]]) && ($_GET[$columns[$i][1]] == ''))) ? urlencode($row[$columns[$i][1]]) : '' ;		// if that column already has condition, site will remove it
				echo '">' . htmlentities($row[$columns[$i][1]]) . '</a></td>';
			}
			echo '</tr>';
		}
		echo '</table><br/>';

		if (mysqli_num_rows($listusers) != 1) {
			echo '<a href="?display=' . $display . '&ordersort=';
			echo ($ordersort == 'asc') ? 'desc' : 'asc';
			echo '&start=' . $start . '&pages=' . $pages . '&orderby=' . $orderby . '&orderby2=' . $orderby2 . $urlparameter . '">Toggle sort order</a><br/>';			// site to toggle sort order
		}



		if ($pages > 1) {
			echo '<br/>';						// display site to previous page
			echo ($currentpage != 1) ? '<a href="?display=' . $display . '&ordersort=' . $ordersort . '&start=' . ($start-$display) . '&pages=' . $pages . '&orderby=' . $orderby . '&orderby2=' . $orderby2 . $urlparameter . '">Previous</a> ' : '' ;
			for ($i = 1; $i <= $pages; $i++) {
				// display site for each page of results, unless on current page
				echo ($i != $currentpage) ? '<a href="?display=' . $display . '&ordersort=' . $ordersort . '&start=' . ($display*($i-1)) . '&pages=' . $pages . '&orderby=' . $orderby . '&orderby2=' . $orderby2 . $urlparameter . '">' . $i . '</a> ' : $i . ' ';
			}
			echo ($currentpage != $pages) ? '<a href="?display=' . $display . '&ordersort=' . $ordersort . '&start='  . ($start+$display) . '&pages=' . $pages . '&orderby=' . $orderby . '&orderby2=' . $orderby2 . $urlparameter .'">Next</a>' : '' ;
		}											// display site to next page

		echo '
		<form name="displayno" method="GET" action="">
		<input type="text" name="display" value="' . $display . '" />
		<input type="hidden" name="ordersort" value="' . $ordersort . '"/>
		<input type="hidden" name="orderby" value="' . $orderby . '"/>
		<input type="hidden" name="orderby2" value="' . $orderby2 . '"/>';

		foreach ($columns as $field) {
			if (isset($_GET[$field[1]]) && ($_GET[$field[1]] != '')) {
				// create hidden inputs to hold conditions for query
				echo '<input type="hidden" name="' . $field[1] . '" value="' . $_GET[$field[1]] . '"/>';
			}
		}

		echo '<input type="submit" name="submit" value="Display" /></form><br/>';


		// END if results all have certain condition

	}											// end-else for displaying users if they exist given the conditions
} else {
	echo 'No users to list.';
}
?>