<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>


<?php
echo (isset($customheader)) ? $customheader : '';
?>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title><?php echo title_prefix . $pagetitle; ?></title>
<link rel="stylesheet" type="text/css"
	href="<?php echo root_address; ?>global/yuimod_user.css" />
<link rel="stylesheet" type="text/css" href="../css/global.css" />
<link rel="stylesheet" type="text/css" media="all" href="../css/960_3_10_10.css" />
<link href="../css/front.css" media="screen, projection" rel="stylesheet" type="text/css">
<style type="text/css">
body {
	background:#ffffff;
	color: #333;
	padding: 40px 0 40px;
	text-align: left;
	font-size: 14px;
	font-family: Arial, Helvetica, sans-serif;
}
.container_3 {
	height:100%;
	width:840px;
	background:#ffffff;
}
#topbar table tr td {
	text-align: center;
}
.submit input {
	width: 150px;
	padding: 7px 15px;
	background: #099;
	border: 0;
	font-size: 12px;
	color: #FFFFFF;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}
.mypile input {
	width: 90px;
	padding: 9px 15px;
	background: #900;
	border: 0;
	font-size: 14px;
	color: #FFFFFF;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}
</style>

<!-- END TOP SECTION -->

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-960997-6']);
  _gaq.push(['_setDomainName', 'pilesite.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
<div id="topbar">
  <table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
	
	
	<?php
		$getname = mysqli_query($dbc, "SELECT * FROM user WHERE user_id='" . $_SESSION['ep_user_id'] . "';");
		$row = mysqli_fetch_assoc($getname);
		$welcomename = '<strong>' . $row['firstname'] . ' ' . $row['lastname'] . '</strong>&nbsp;&nbsp;';
	?>
      <td width="32" height="60"><a href="home.php"><img src="images/menu_logo.png" alt="Logo" border="0" /></a></td>

      <td width="375"><label for="textfield"></label>
        <?
	  echo $welcomename;
				include('global/files_list.php');
				for ($file = 0; $file < 1; $file++) {
					// loop goes through each file that should appear here
					echo (isset($notfirstitem)) ? ' | ' : '';
					$notfirstitem = true;
					if ((count($scripts[$file]) > 2) && (!empty($_SESSION['ep_user_id']))) {
						// if file is for logging in or out, it will change based on user status
						echo '<a href="' . root_address . $scripts[$file][4] . '">' . $scripts[$file][3] . "</a> ";	// if user is logged in, other values of array element is used
						continue;
					}
					echo '<a href="' . root_address . $scripts[$file][1] . '">' . $scripts[$file][0] . "</a> ";		// generates standard link to page
				}
		if (isset($_SESSION['ep_user_id'])) {
			for ($file = 0; $file < count($scripts); $file++) {
				// loop goes through each file that should appear in sidebar
				echo (((!empty($_SESSION['ep_user_id'])) && ($scripts[$file][2] == 1)) ||
				((!empty($_SESSION['ep_admin'])) && ($_SESSION['ep_admin'] == 1) && ($scripts[$file][2] == 2))) ? ' | <a href="' . root_address . $scripts[$file][1] . '">' . $scripts[$file][0] . "</a>\n" : '';
				// if (user is logged in and file only accessible by logged in users) or if (user is an admin and file is only accessible by admins), display
			}
		}
				?>
        <br />
        <br /></td>
      <td width="105"><br />
        <br /><br />
</td><td width="47"><a href="home.php"><img src="../images/28-star.png" alt="My Pile" width="26" height="26" /></a><br />  <br />
</td>
<td width="1"></td><td width="46"><a href="suggestions.php"><img src="../images/40-inbox.png" alt="Suggest" width="24" height="24" /></a><br />  <br /></td>
<td width="111">
<div id="container">
         <div id="topnav" class="topnav"> 
		  <a href="login" class="popularlink"><span>Explore</span></a>
  </div><br />


    <fieldset id="popularlink_menu">
      <p class="submit">    
    <input type="submit" value="Explore All" onclick="window.location.href='sites.php?orderby=pilecount&ordersort=desc&'" /><br />
  <?php
  
	$cats = mysqli_query($dbc, "SELECT * FROM category ORDER BY cat_id");
	
	while ($cat = mysqli_fetch_assoc($cats)){
		echo ' <input type="submit" value="' . $cat['cat_name'] . '" onclick="window.location.href=\'sites.php?orderby=pilecount&ordersort=desc&cat_name=' . $cat['cat_name'] . '\'" /><br />';
	}
?>
      </p><br />
</fieldset>
</div></td>
<td width="83">

<div id="container">
  <div id="topnav" class="topnav">
  <a href="login" class="uploadlink"><span>New</span></a> 
  </div>
  
  <fieldset id="uploadlink_menu">
  Add / Share Site:<br /><br />
  <?php
include("uploadsite.php");
?>
  </fieldset>
  
</div>

<script src="scripts/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
        $(document).ready(function() {

            $(".popularlink").click(function(e) {          
				e.preventDefault();
                $("fieldset#popularlink_menu").toggle();
				$(".popularlink").toggleClass("menu-open");
            });
			
			$("fieldset#popularlink_menu").mouseup(function() {
				return false
			});
			$(document).mouseup(function(e) {
				if($(e.target).parent("a.popularlink").length==0) {
					$(".popularlink").removeClass("menu-open");
					$("fieldset#popularlink_menu").hide();
				}
			});			
			
        });
</script>
<script type="text/javascript">
        $(document).ready(function() {

            $(".uploadlink").click(function(e) {          
				e.preventDefault();
                $("fieldset#uploadlink_menu").toggle();
				$(".uploadlink").toggleClass("menu-open");
            });
			
			$("fieldset#uploadlink_menu").mouseup(function() {
				return false
			});
			$(document).mouseup(function(e) {
				if($(e.target).parent("a.uploadlink").length==0) {
					$(".uploadlink").removeClass("menu-open");
					$("fieldset#uploadlink_menu").hide();
				}
			});			
			
        });
</script>


<script src="scripts/jquery.tipsy.js" type="text/javascript"></script>&nbsp;</td>
    </tr>
  </table>
</div>
<div class="container_3">
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
<?php echo '<h1>' . $pagetitle . '</h1>'; ?>
					<!-- END HEADER -->