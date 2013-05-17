<?php
include('global/top.php');
if (!empty($_SESSION['ep_user_id'])) {
	header("location:home.php");												// if not logged in, send user away
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PileSite</title>
<link rel="stylesheet" type="text/css" media="all"
	href="css/960_3_10_10.css" />
<style type="text/css">
body {
	background: #ffffff;
	color: #333;
	padding: 20px 0 40px;
	font-size: 14px;
	font-family: Arial, Helvetica, sans-serif;
}

.container_3 p {
	overflow: hidden;
	padding: 10px 0;
	text-align: center;
	font-size: 80%;
	background: #efefef;
}

.submit input {
	width: 200px;
	padding: 9px 15px;
	background: #099;
	border: 0;
	font-size: 14px;
	color: #FFFFFF;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}

.login input {
	width: 80px;
	padding: 9px 15px;
	background: #09C;
	border: 0;
	font-size: 14px;
	color: #FFFFFF;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}

.register input {
	width: 80px;
	padding: 9px 15px;
	background: #999;
	border: 0;
	font-size: 14px;
	color: #FFFFFF;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}

.container_4 {
	margin-left: auto;
	margin-right: auto;
	width: 620px;
}

.container_4 .grid_1 {
	width: 135px;
}
</style>
</head>

<body>
	<div class="container_3">
		<table width="953" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="309"><img src="images/logo.png" width="200" height="70" />
				</td>
				<td width="644"><form method="post" action="login.php">
						Email <input name="email" type="text" id="email" size="20" />
						Password <input name="password" type="password" id="password"
							size="20" /> <span class="login"> <input name="submitted"
							type="submit" value="Login">
						</span> <span class="register"> <input name="register"
							type="button" value="Register"
							onclick="window.location.href='register.php'">
						</span>
						<div class="login"></div>
					</form>
				</td>
			</tr>
		</table>
	  <div class="clear">&nbsp;</div>
	  
	  <div class="grid_2 push_1">
			<div class="container_4">

				<div class=".container_3 .grid_2">
					<p><iframe width="620" height="345" src="http://www.youtube.com/embed/PjOMLVhdtWA?rel=0" frameborder="0" allowfullscreen></iframe></p>
				</div>
Top 8 Sites:
				<div class="clear">&nbsp;</div>

		  <div class="grid_1">
					<p>135px</p>
				</div>
		  <div class="grid_1">
					<p>135px</p>
				</div>
		  <div class="grid_1">
					<p>135px</p>
				</div>
		  <div class="grid_1">
					<p>135px</p>
				</div>
				</p>

				<div class="clear">&nbsp;</div>

		  <div class="grid_1">
					<p>135px</p>
				</div>
		  <div class="grid_1">
					<p>135px</p>
				</div>
		  <div class="grid_1">
					<p>135px</p>
				</div>
		  <div class="grid_1">
					<p>135px</p>
				</div>
				</p>

			</div>
		</div>

		<div class="grid_1 pull_2">
			<p class="submit">

				<input type="submit" value="Explore All"
					onclick="window.location.href='sites.php?orderby=pilecount&ordersort=desc'" /><br />
				<br />
				<?php
				$cats = mysqli_query($dbc, "SELECT * FROM category");

				while ($cat = mysqli_fetch_assoc($cats)){
					echo ' <input type="submit" value="' . $cat['cat_name'] . '" onclick="window.location.href=\'sites.php?orderby=pilecount&ordersort=desc&cat_name=' . $cat['cat_name'] . '\'" /><br />';
				}

				?>
			</p>
		</div>

		<div class="clear">&nbsp;</div>
		<div class="grid_3">
			<p>
			  <script type="text/javascript"><!--
google_ad_client = "ca-pub-4356809686755322";
/* PileSite Banner */
google_ad_slot = "7188221980";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
			  <script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
			</p>
		</div>

		<div class="clear">Copyright PileSite 2012</div>
	</div>
</body>
</html>
