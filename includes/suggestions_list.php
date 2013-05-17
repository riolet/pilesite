<?php
if (mysqli_num_rows(mysqli_query($dbc, "SELECT url FROM site")) != 0) {
	// check there are sites in the system

	$columns = array(
			array('User ID','user_id'),
			array('site ID', 'site_id'),
			array('Category', 'cat_name', 25),
			array('Title', 'title', 50),
			array('Suggested By', 'firstname', 50),
			array('URL', 'url')
	);		// set up column details
	$urlparameter = '';
	$queryparameter = '';

	$filtered = false;
	$buffer = '';
	foreach ($columns as $field) {
		// keep track of conditions for selecting query results
		if ((isset($_GET[$field[1]]) && ($_GET[$field[1]] == '')) || (!isset($_GET[$field[1]]))) {
			continue;
		}
		$filtered = true;
		$buffer .= $field[0] . ' - ' . htmlentities($_GET[$field[1]]) . ' <a href="?' . $urlgetarrayvalues . 'start=&pages=&' . $field[1] . '=">Remove</a><br/>';	// site to remove condition
		$urlparameter .= '&' . $field[1] . '=' . urlencode($_GET[$field[1]]);
		$queryparameter .= (empty($queryparameter)) ? 'WHERE ' : ' AND ';
		$queryparameter .= $field[1] . "='" . mysqli_real_escape_string($dbc, trim($_GET[$field[1]])) . "'";	// set up query with conditions
	}
	echo ($filtered == true) ? '<a href="?">Remove Filters</a>' . $buffer : $buffer;
	
	$mydetails = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM user WHERE user_id='" . $_SESSION['ep_user_id'] . "'"));
	
	if (empty($queryparameter)) {
		$queryparameter = "WHERE suggestsite.receiver_user_email='" . $mydetails['email'] . "'";
	} else {
		$queryparameter .= " AND suggestsite.receiver_user_email='" . $mydetails['email'] . "'";
	}
	
	$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'cat_name';					// get the GET data
	$ordersort = (isset($_GET['ordersort']) && ($_GET['ordersort'] == 'desc')) ? 'desc' : 'asc';
	$display = (isset($_GET['display']) && is_numeric($_GET['display']) && ($_GET['display'] > 0)) ? $_GET['display'] : 20;			// display x results per page by default
	$start = (isset($_GET['start']) && (is_numeric($_GET['start']))) ? $_GET['start'] : 0;					// record to start displaying from
	$countsites = mysqli_fetch_row(mysqli_query($dbc, "SELECT COUNT(suggestsite.suggestsite_id) FROM site INNER JOIN category USING(cat_id) INNER JOIN suggestsite USING(site_id) $queryparameter GROUP BY site.site_id"));

	$pages = ($countsites[0] > $display) ? ceil($countsites[0]/$display) : 1;								// pages there are
	$currentpage = ($start/$display) + 1;															// current page to calculate pagination

	$listsitequery = "SELECT * FROM site INNER JOIN category USING(cat_id) INNER JOIN suggestsite USING(site_id) INNER JOIN user ON user.user_id=suggestsite.sender_user_id $queryparameter ORDER BY " . mysqli_real_escape_string($dbc, trim($orderby)) . " $ordersort LIMIT $start, $display";
	$listsites = mysqli_query($dbc, $listsitequery);

	$listmysites = mysqli_query($dbc, "SELECT * FROM site INNER JOIN category USING(cat_id) INNER JOIN pile ON pile.site_id=site.site_id WHERE pile.user_id='" . $_SESSION['ep_user_id'] . "' LIMIT $start, $display");

	while ($row = mysqli_fetch_assoc($listmysites)) {
		$mypile[$row['site_id']] = $row;
	}
	

	// query for displaying results
	if (!$listsites) {
		echo 'Query error.';
	} elseif (mysqli_num_rows($listsites) == 0) {
		// if count=0, the prescribed conditions have returned no results
		echo 'No sites found<br/><br/>';
	} else {
		echo $countsites[0] . ' records found<br/><br/>';
		echo '<table><thead><tr>';

		if (!empty($_SESSION['ep_user_id'])) {
			echo '<th></th>';
		}
		for ($i=2; $i<5; $i++) {

			// go through each column to display
			echo '<th><a href="?display=' . $display . '&ordersort=' . $ordersort . '&start=' . $start . '&pages=' . $pages . '&orderby=' . $columns[$i][1] . $urlparameter . '">' . $columns[$i][0] . '</a></th>';						// each site will sort results by that column
		}
		echo '</tr></thead>';
		while ($row = mysqli_fetch_assoc($listsites)) {

			// for each site, sites to edit/delete them
			echo '<tr>';
				

				
			if (!empty($_SESSION['ep_user_id'])) {
				echo '<td><a href="?action=add&site_id=' . $row['site_id'] . '&' . $urlgetarrayvalues . '">';

				if (isset($mypile[$row['site_id']])) {
					echo '<img src="/images/Crystal_Clear_action_edit_remove.png" alt="Remove from Pile" height="20px" width="20px" />';
				} else {
					echo '<img src="/images/Crystal_Clear_action_edit_add.png" alt="Add to Pile" height="20px" width="20px" />';
				}
				echo '</a></td>';
			}
				
			for ($i=2; $i<5; $i++) {
				if ($i == 3){
					echo '<td><a href="' . $row['url'] . '" target="_blank">' . htmlentities($row['title']) . '</a></td>';
					continue;
				}
				// for each column, clicking in the field will display results with only that attribute
				echo '<td><a href="?display=' . $display . '&ordersort=' . $ordersort . '&orderby=' . $orderby . $urlparameter . '&' . $columns[$i][1] . '=';
				echo (!isset($_GET[$columns[$i][1]]) || (isset($_GET[$columns[$i][1]]) && ($_GET[$columns[$i][1]] == ''))) ? urlencode($row[$columns[$i][1]]) : '' ;		// if that column already has condition, link will remove it
				echo '" title="' . htmlentities($row[$columns[$i][1]]) . '">' . htmlentities(capstring($row[$columns[$i][1]], $columns[$i][2])) . '</a></td>';
			}
			
			echo '<td><a href="?action=dismiss&suggestsite_id=' . $row['suggestsite_id'] . '">Dismiss</a></td>';

			echo '</tr>';
		}
		echo '</table><br/>';

		if (mysqli_num_rows($listsites) != 1) {
			echo '<a href="?display=' . $display . '&ordersort=';
			echo ($ordersort == 'asc') ? 'desc' : 'asc';
			echo '&start=' . $start . '&pages=' . $pages . '&orderby=' . $orderby . $urlparameter . '">Toggle sort order</a><br/>';			// site to toggle sort order
		}



		if ($pages > 1) {
			echo '<br/>';						// display site to previous page
			echo ($currentpage != 1) ? '<a href="?display=' . $display . '&ordersort=' . $ordersort . '&start=' . ($start-$display) . '&pages=' . $pages . '&orderby=' . $orderby  . $urlparameter . '">Previous</a> ' : '' ;
			for ($i = 1; $i <= $pages; $i++) {
				// display site for each page of results, unless on current page
				echo ($i != $currentpage) ? '<a href="?display=' . $display . '&ordersort=' . $ordersort . '&start=' . ($display*($i-1)) . '&pages=' . $pages . '&orderby=' . $orderby  . $urlparameter . '">' . $i . '</a> ' : $i . ' ';
			}
			echo ($currentpage != $pages) ? '<a href="?display=' . $display . '&ordersort=' . $ordersort . '&start='  . ($start+$display) . '&pages=' . $pages . '&orderby=' . $orderby . $urlparameter .'">Next</a>' : '' ;
		}											// display site to next page

		echo '
		<form name="displayno" method="GET" action="">
		<input type="text" name="display" value="' . $display . '" />
		<input type="hidden" name="ordersort" value="' . $ordersort . '"/>
		<input type="hidden" name="orderby" value="' . $orderby . '"/>';

		foreach ($columns as $field) {
			if (isset($_GET[$field[1]]) && ($_GET[$field[1]] != '')) {
				// create hidden inputs to hold conditions for query
				echo '<input type="hidden" name="' . $field[1] . '" value="' . $_GET[$field[1]] . '"/>';
			}
		}

		echo '<input type="submit" name="submit" value="Display" /></form><br/>';


		// END if results all have certain condition

	}											// end-else for displaying sites if they exist given the conditions
} else {
	echo 'No sites to list.';
}
?>