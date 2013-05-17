<?php	// ****************************************************** TODO sticky form for category
if (isset($_POST['submitted'])) {
	echo "<br/>";	// TODO pilecount bug <> 0

	// if form has been submitted
	if (!empty($_POST['url']) && !empty($_POST['addpile']) && !empty($_POST['cat_id']) && !empty($_POST['suggestsite'])) {

		$myurl = geturl($_POST['url']);	// geturl() only ouputs valid url or bool false
		$mytitle = ($myurl != false) ? gettitle($myurl) : false;

		if ($myurl != false && $mytitle != false) {

			// check if details are empty
			$query = "SELECT * FROM site WHERE url='" . $myurl . "' LIMIT 1";
			$result = mysqli_query($dbc, $query);

			if ($result) {		// check if query successful
				$rowcount = mysqli_num_rows($result);

				if ($rowcount == 0) {		// need to insert
					$cat_id = $_POST['cat_id'];

					if (mysqli_query($dbc, "INSERT INTO site (url, cat_id, title) VALUES ('" . $myurl . "', '" . $cat_id . "', '" . mysqli_real_escape_string($dbc, trim($mytitle)) . "')")) {			// try insert
						$result = mysqli_query($dbc, $query);
						$sticky = false;
						
					} else {						// catch
						echo "Insertion error<br/>";
						$sticky = true;
					}
				} else {								// if site exists
					$sticky = false;		// "site exists - query success<br/>";
					
					echo "Submitted!";
				}

				if ($sticky == false) {
					$row = mysqli_fetch_assoc($result);
				
					if ($_POST['addpile'] == 'yes') {
						$result = mysqli_query($dbc, "SELECT * FROM pile WHERE site_id=" . $row['site_id'] . " AND user_id=" . $_SESSION['ep_user_id']);
						if ($result) {
							$colrowcount = mysqli_num_rows($result);
							if ($colrowcount == 0) {
								// adding item to personal pile
								$colrow = mysqli_fetch_assoc($result);
								if (mysqli_query($dbc, "INSERT INTO pile (user_id, site_id) VALUES (" . $_SESSION['ep_user_id'] . ", " . $row['site_id'] . ")")) {
									echo "Item inserted into pile<br/>";

								} else {
									echo "pile item added error<br/>";
								}
							} else if ($colrowcount == 1) {
								// already exists in pile
								if ($rowcount == 0) {
									echo "Integrity error - exist in pile, not sites table.<br/>";
								} // "Item already exists in pile and sites table<br/>";

							} else {
								echo "pile rowcount error<br/>";
							}
						} else {
							echo "pile query error<br/>";
						}
					}


					if ($_POST['suggestsite'] == 'yes') {
						if ($_POST['shareemail'] == '') {
							echo 'No email to share site with.';
						} else {
							$currentuser = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM user WHERE user_id='" . $_SESSION['ep_user_id'] . "'"));
							$emails = array_unique(array_map("checkemail", explode(';', $_POST['shareemail'])));
							if (in_array('', $emails) || count($emails) == 0 || in_array($currentuser['email'], $emails)) {
								$sticky = true;
								echo 'Enter valid emails please.';
							} else {					// no error, share ahead
								
							
								$insertsharequery = '';
								foreach ($emails as $email){
									$checksharequery = mysqli_query($dbc, "SELECT * FROM suggestsite WHERE sender_user_id='" . $_SESSION['ep_user_id'] . "' AND receiver_user_email='" . $email . "' AND site_id='" . $row['site_id'] . "'");
									if (mysqli_num_rows($checksharequery) == 0) {		// check if not already suggested
										$insertsharequery .= "('" . $_SESSION['ep_user_id'] . "', '" . $email . "', '" . $row['site_id'] . "'), ";
									}
									
									$checkemailquery = mysqli_query($dbc, "SELECT * FROM user WHERE email='" . $email . "' LIMIT 1");
									if (mysqli_num_rows($checkemailquery) == 0) {		// if recipient not registered
										$body = '
Hi

' . $currentuser['firstame'] . ' ' . $currentuser['lastname'] . ' has suggested a site for you on PileSite:

' . $row['url'] . '

Visit piilesite.com to sign up and manage your sites!

Regards,

PileSite
"A new way to share and discover the web!"

			';



			mail($email, 'Registration Confirmation', $body, 'From: blackhole@pilesite.com');
									}
								}
								if (!empty($insertsharequery)) {
									$insertsharequery = "INSERT INTO suggestsite (sender_user_id, receiver_user_email, site_id) VALUES " . $insertsharequery;
									$insertsharequery = substr_replace($insertsharequery ,"",-2);	// remove commas
									mysqli_query($dbc, $insertsharequery);
									echo $insertsharequery;
								}
								// *****************************************************************************************************
							
							
							}
						}
					
					
					}
				}	
				
				// check if in pile and add
			} else {
				echo "Query error<br/>";										// if query error, echo error message
				$sticky = true;
			}
		} else {
			echo 'Invalid website';
			$sticky = true;
		}
	} else {
		echo "Details cannot be blank.<br/>";								// if details blank, output error message
		$sticky = true;
	}
	//echo $scriptresult;
}

$categories = mysqli_query($dbc, "SELECT * FROM category");

?>

<form method="POST" action="">

	<p>
		site: <input type="text" name="url"
			value="<?php echo (isset($_POST['submitted']) && $sticky == TRUE) ? $myurl : '' ?>" />
		<br /> Category: <select name="cat_id">
			<?php

			while ($row = mysqli_fetch_assoc($categories)) {
				echo '<option value="' . $row['cat_id'] . '">' . $row['cat_name'] . '</option>';
			}

			?>
		</select> <br /> <br />
		<?php
		
		if (!empty($_SESSION['ep_user_id'])) {
			echo '
			Add to my pile? <input type="radio"
			name="addpile" value="yes" checked="checked" /> Yes <input
			type="radio" name="addpile" value="no" /> No<br /> <br />
			
			Share with others? <input type="radio" name="suggestsite" value="yes" /> Yes <input
			name="suggestsite" type="radio" value="no" checked="checked" /> No<br />
		Email: <input type="text" name="shareemail" />
		';
		}
		
		?>
	</p>
	<p>
		<br />     <p class="submit"><input type="submit" name="submitted" /></p>
	</p>
	</p>
</form>
