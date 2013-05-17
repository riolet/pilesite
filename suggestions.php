<?php
include('global/top.php');
if (empty($_SESSION['ep_user_id'])) {
	header("location:index.php");
}

$pagetitle = 'Suggested Sites';
include('global/header.php');

$urlgetarrayvalues = '';
foreach ($_GET as $parameter=>$parametervalue) {
	// aggregate all GET elements into single string
	$urlgetarrayvalues .= ($parametervalue=='') ? '' : $parameter . '=' . urlencode($parametervalue) . '&';
}

// if GET[action] is to edit or delete site
if (isset($_GET['action']) && ($_GET['action'] == 'add') && isset($_GET['site_id']) && is_numeric($_GET['site_id'])) {

	$selectsitequery = "SELECT * FROM site WHERE site_id=$_GET[site_id] LIMIT 1";
	$selectsite = mysqli_query($dbc, $selectsitequery);

	$sitedetails = mysqli_fetch_assoc($selectsite);
	// if action is to edit site
	// check if in pile - if yes, remove entry, else insert
	$checkquery = mysqli_query($dbc, "SELECT * FROM pile WHERE user_id='" . $_SESSION['ep_user_id'] . "' AND site_id='" . $_GET['site_id'] . "'");
	if (mysqli_num_rows($checkquery) == 0){
		mysqli_query($dbc, "INSERT INTO pile (user_id, site_id) VALUES ('" . $_SESSION['ep_user_id'] . "', '" . $_GET['site_id'] . "')");
	} else {
		mysqli_query($dbc, "DELETE FROM pile WHERE user_id='" . $_SESSION['ep_user_id'] . "' AND site_id='" . $_GET['site_id'] . "'");
	}
	$location = '?' . $urlgetarrayvalues . '&action=&site_id=';
	header("Location: $location");
}

if (isset($_GET['action']) && ($_GET['action'] == 'dismiss') && isset($_GET['suggestsite_id']) && is_numeric($_GET['suggestsite_id'])) {

	$selectsitequery = "SELECT * FROM suggestsite WHERE site_id='$_GET[suggestsite_id]' LIMIT 1";
	$selectsite = mysqli_query($dbc, $selectsitequery);

	$sitedetails = mysqli_fetch_assoc($selectsite);
	// check if in pile - if yes, remove entry, else insert
	$checkquery = mysqli_query($dbc, "SELECT * FROM pile WHERE user_id='" . $_SESSION['ep_user_id'] . "' AND site_id='" . $_GET['site_id'] . "'");
	mysqli_query($dbc, "DELETE FROM suggestsite WHERE suggestsite_id='" . $_GET['suggestsite_id'] . "'");
	$location = '?' . $urlgetarrayvalues . '&action=&suggestsite_id=';
	header("Location: $location");
}

include('includes/suggestions_list.php');
include('global/footer.php');
?>