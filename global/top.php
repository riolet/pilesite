<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
	// output buffering for all pages - allows headers mid-page, compresses page where possible
	ob_start("ob_gzhandler");
} else {
	ob_start();
}

session_start();
include('config.php');											// include database connection parameters and initiate connection
$dbc = mysqli_connect(db_host, db_user, db_password, db_name) or die ('Connection Error: ' . mysqli_connect_error());

function geturl($url)
{
	// initialize cURL
	$curl = curl_init($url);
	curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER  => true,
			CURLOPT_FOLLOWLOCATION  => true,
	));

	// execute the request
	$result = curl_exec($curl);

	// fail if the request was not successful
	if ($result === false) {
		curl_close($curl);
		return false;
	}

	// extract the target url
	$redirectUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
	curl_close($curl);

	return $redirectUrl;
}


function gettitle($url){

	if( !($data = file_get_contents($url)) )
	{
		return false;
	}
	if( preg_match("#<title>(.+)<\/title>#iU", $data, $t))  {
		return trim($t[1]);
	} else {
		return false;
	}
}

function checkemail($email){

	$email = str_replace(" ", "", trim($email));
	if (!preg_match ('/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/', $email)) {
		return '';
	}
	if ((strlen($email) < 4) || (strlen($email) > 100)) {
		return '';
	}
	
	return strtolower($email);
}

function capstring($string, $length){
	if (strlen($string) > $length){
		return substr_replace($string, '', $length) . '...';
	} else {
		return $string;
	}
}


?>