<?php
error_reporting(E_ALL & ~E_NOTICE);

function buildMenu() {
	if (isset($_SESSION['MM_Username'])) {
		echo "<div id='menucontainer' style='float:left;'>\n";
		echo "	<div id=\"menunav\">\n";
		echo "		<ul>\n";
		echo "			<li><a href=\"../rfas/rfas.php\">RFCs</a></li>\n";
		echo "			<li><a href=\"../statusReports/statusReports.php\">Status Reports</a></li>\n";
		echo "			<li><a href=\"../maintenances/maintenances.php\">Maintenance Notifications</a></li>\n";
		echo "			<li><a href=\"../supportRequests/supportRequests.php\">Support Requests</a></li>\n";
		echo "			<li><a href=\"../userPortals/myPortal.php\">My Portal</a></li>\n";
		echo "		</ul>\n";
		echo "<div style='float:right;'>"
		. "<img src='../images/masflight-logo.png' alt='masFlight' height='50'>"
		. "</div>";

		echo "	</div>\n";
		echo "	<div id=\"menu_inner\">\n";
		echo "	</div>\n";
		echo "</div>\n";
	} else {
		echo "<div id='menucontainer' style='float:left;'>\n";
		echo "	<div id=\"menunav\">\n";
		echo "		<ul>\n";
		echo "			<li><a href=\"../userPortals/index.php\">User Portals</a></li>\n";
		echo "		</ul>\n";
		echo "<div style='float:right;'>"
		. "<img src='../images/masflight-logo.png' alt='masFlight' height='50'>"
		. "</div>";
		echo "	</div>\n";
		echo "	<div id=\"menu_inner\">\n";
		echo "	</div>\n";
		echo "</div>\n";
	}

	/* echo "<div id=\"menucontainer\">\n";
	  echo "	<div id=\"menunav\">\n";
	  echo "		<ul>\n";
	  echo "			<li><a href=\"../rfas/rfas.php\" rel=\"cman\">Change Management</a></li>\n";
	  echo "			<li><a href=\"../statusReports/statusReports.php\" rel=\"reporting\">Reporting</a></li>\n";
	  echo "		</ul>\n";
	  echo "	</div>\n";
	  echo "	<div id=\"menu_inner\">\n";
	  echo "		<ul id=\"cman\" class=\"innercontent\">\n";
	  echo "			<li><a href=\"../rfas/rfas.php\">RFCs</a></li>\n";
	  echo "			<li><a href=\"../maintenances/maintenances.php\">Maintenance Notifications</a></li>\n";
	  echo "		</ul>\n";
	  echo "		<ul id=\"reporting\" class=\"innercontent\">\n";
	  echo "			<li><a href=\"../statusReports/statusReports.php\">Status Reports</a></li>\n";
	  echo "		</ul>\n";
	  echo "	</div>\n";
	  echo "</div>\n"; */
}

function buildNewHeader($rootPage, $rootPageText, $pageText = '', $destiny = '', $addText = '') {
	?>
	<div class="page-header">
		 <div class='row'>
			  <div class='col-xs-10'>
					<small>
						 <ul style='margin-top: 8px;' class='breadcrumb'>
							  <li><a href='../userPortals/myPortal.php'>Home</a></li>						
							  <?php if ($pageText != '') { ?>
								  <li><a href='<?php echo $rootPage; ?>'><?php echo $rootPageText; ?></a></li>
								  <li class='active'><?php echo $pageText; ?></li>
							  <?php } else { ?>
								  <li class='active'><?php echo $rootPageText; ?></li>
							  <?php } ?>
						 </ul>
					</small>
			  </div>
			  <div class='col-xs-2'>
					<?php if ($addText != '') { ?>
						<a style='margin-top: 12px;' class='btn btn-primary' href='<?php echo $destiny; ?>?function=add'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp;<?php echo $addText; ?></a>
					<?php } ?>
					&nbsp;</div>
			  <?php //buildHeader("statusReport", null, "statusReports", "Status Reports", "Add a Status Report"); ?>
		 </div>
	</div>
	<?php
}

function buildHeader($rootPage, $parentPageText, $page, $pageText, $addText) {
	echo "<table align=\"left\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\"><tr><td class=\"jstime\"><script type=\"text/javascript\">document.write(TODAY);</script></td>\n";
	echo "<td class=\"breadcrumbs\"><a title=\"My Portal\" href=\"../userPortals/myPortal.php\">My Portal</a> &raquo; ";
	if ($parentPageText != null) {
		echo "<a title=\"" . $parentPageText . "\" href=\"" . $rootPage . "s.php\">" . $parentPageText . "</a> &raquo; " . $pageText;
	} else {
		echo $pageText;
	}
	echo "</td>\n";
	echo "<td class=\"add\">";
	if ($addText != null) {
		echo "<a  class='btn btn-app' title=\"" . $addText . "\" href=\"" . $rootPage . ".php?function=add\"><img style='margin-right:5px;' src=\"../images/icons/add.gif\" alt=\"Add\" />" . $addText . "</a>";

		/* echo "<a class='btn btn-app btn-edit-usr' href='".$rootPage.".php?function=add'  title='$addText'>
		  <i class='fa fa-users'></i> Add Users
		  </a>"; */
	}
	echo "</td></tr></table>";
}

function buildHeaderNEW($rootPage, $parentPageText, $page, $pageText, $addText) {
	echo "<div align=\"center\"><a title=\"Sybase|365 Website\" href=\"\" target=\"_blank\"><img src=\"../images/logos/masflight.png\" alt=\"masflight Logo\" /></a></div>\n";
	echo "<table align=\"center\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"jstime\"><script type=\"text/javascript\">document.write(TODAY);</script></td>\n";
	echo "<td class=\"breadcrumbs\"><a title=\"Production Operations Homepage\" href=\"/index.html\">Home</a> &raquo; ";
	if (!isset($_GET['corp'])) {
		if ($parentPageText != null) {
			echo "<a title=\"" . $parentPageText . "\" href=\"" . $rootPage . "s.php\">" . $parentPageText . "</a> &raquo; " . $pageText;
		} else {
			echo $pageText;
		}
	} else {
		echo "<a title=\"View Status Reports\" href=\"../statusReports/statusReports.php?corp=y\">View Status Reports</a> | <a title=\"View Maintenance Notifications\" href=\"../maintenances/maintenances.php?corp=y\">View Maintenance Notifications</a>";
	}
	echo "</td>\n";
	echo "<td class=\"add\">";
	if (($addText != null) && (!isset($_GET['corp']))) {
		echo "<img src=\"../images/icons/add.gif\" alt=\"Add\" /><a title=\"" . $addText . "\" href=\"" . $rootPage . ".php?function=add\">" . $addText . "</a>";
	}
	echo "</td></tr></table>";
}

function sudoAuth($linkedPage, $linkText, $icon) {
	if (!isset($_GET['sudo_auth'])) {
		$_GET['sudo_auth'] = null;
	}
	global $sudo;
	$sudo = $_GET['sudo_auth'];
	if (isset($sudo) && ($sudo == "admin")) {
		echo "&nbsp;<img src=\"../images/icons/" . $icon . ".gif\" alt=\"" . $icon . "\" /><a title=\"" . $linkText . "\" href=\"" . $linkedPage . ".php?sudo_auth=admin\">" . $linkText . "</a>";
	}
}

function addComponent($linkedPage, $linkText, $icon) {
	echo "&nbsp;<a title=\"" . $linkText . "\" href=\"#\" onclick=\"MM_openBrWindow('" . $linkedPage . "?function=add','','width=825,height=430')\"><img src=\"../images/icons/" . $icon . ".gif\" alt=\"" . $icon . "\" /></a>";
}

//use this function for adding & updating components
function adminComponent($linkedPage, $linkText, $icon, $winWidth, $winHeight) {
	if (isset($_GET['function']) && (($_GET['function'] == "update") || ($_GET['function'] == "add"))) {
		echo "&nbsp;<a title=\"" . $linkText . "\" href=\"#\" onclick=\"MM_openBrWindow('" . $linkedPage . "?function=" . $icon . "','','width=" . $winWidth . ",height=" . $winHeight . "')\"><img src=\"../images/icons/" . $icon . ".gif\" alt=\"" . $icon . "\" /></a>";
	}
}

//use this function for adding & updating components
function adminComponentLimited($linkedPage, $linkText, $icon, $winWidth, $winHeight, $authorizedGroup) {
	if (isset($_SESSION['MM_UserGroup']) && ($_SESSION['MM_UserGroup'] == $authorizedGroup)) {
		if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
			echo "&nbsp;<a title=\"" . $linkText . "\" href=\"#\" onclick=\"MM_openBrWindow('" . $linkedPage . "?function=" . $icon . "','','width=" . $winWidth . ",height=" . $winHeight . "')\"><img src=\"../images/icons/" . $icon . ".gif\" alt=\"" . $icon . "\" /></a>";
		}
	}
}

function sudoAuthDataOLD($linkedPage, $linkedText, $tdth, $icon, $param) {
	if (!isset($_GET['sudo_auth'])) {
		$_GET['sudo_auth'] = null;
	}
	global $sudo;
	$sudo = $_GET['sudo_auth'];
	if (isset($sudo) && ($sudo == "admin")) {
		if ($tdth == "th") {
			echo "<th></th>";
		} elseif ($tdth == "td") {
			echo "<td><a title=\"" . $linkedText . "\" href=\"" . $linkedPage . ".php?sudo_auth=admin&amp;" . $param . "\"><img src=\"../images/icons/" . $icon . ".gif\" alt=\"" . $icon . "\" />";
		}
	}
}

function sudoAuthData($linkedPage, $linkedText, $tdth, $icon, $param) {
	if (isset($_SESSION['MM_Username'])) {
		if ($tdth == "th") {
			echo "<th></th>\n";
		} elseif ($tdth == "td") {
			?>
			<td align="center">
				 <a title="<?php echo $linkedText; ?>" href="<?php echo "{$linkedPage}?{$param}"; ?>">
					  <span class="glyphicon <?php echo (($icon == 'edit') ? 'glyphicon-edit' : 'glyphicon-remove-sign'); ?>"></span>
				 </a>
			</td>
			<?php
		}
	}
}

function buildFooter($colspan, $year = "2015", $version = "1.1") {
	if ($colspan != "0") {
		echo "<tr><td colspan=\"" . $colspan . "\" class=\"footer\">&copy; " . $year . " Marks Systems Inc, -  masFlight<br/> Technical Support. Version " . $version . "</td></tr>\n";
	} else {
		echo "<div class=\"footer\">&copy; " . $year . "  Marks Systems Inc, -  masFlight<br/> Technical Support. Version " . $version . "</div>\n";
	}
}

function sentSuccessful($message) {
	$my_get = filter_input_array(INPUT_GET, array('sent' => FILTER_SANITIZE_SPECIAL_CHARS,));
	if ((isset($my_get['sent'])) && ($my_get['sent'] == 'y')) {
		echo "<div class=\"successful\">" . $message . "</div>\n";
	}
}

function requiredField() {
	if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
		echo "&nbsp;<abbr class=\"required\" title=\"Required\">*</abbr>";
	}
}

function formField($type, $name, $value, $width, $maxchars, $rows, $wrap, $order, $required) {
	if ($type == "text") {
		if (($_GET['function'] == "update") || ($_GET['function'] == "add")) {
			echo "<input type=\"text\" name=\"" . $name . "\" id=\"" . $name . "\" value=\"";
		}
		if (($_GET['function'] == "view") && (($value == null) || ($value == "0"))) {
			echo "-";
		}
		if (($value != null) && ($value != "0")) {
			echo stripslashes($value);
		}
		if (($_GET['function'] == "update") || ($_GET['function'] == "add")) {
			echo "\" size=\"" . $width . "\" maxlength=\"" . $maxchars . "\" tabindex=\"" . $order . "\" />";
		}
		if ($required == "y") {
			requiredField();
		}
	} elseif ($type == "textarea") {
		if (($_GET['function'] == "update") || ($_GET['function'] == "add")) {
			echo "<textarea name=\"" . $name . "\" id=\"" . $name . "\" cols=\"" . $width . "\" rows=\"" . $rows . "\" tabindex=\"" . $order . "\" wrap=\"" . $wrap . "\">";
			echo stripslashes($value);
		}
		if (($_GET['function'] != "update") && ($_GET['function'] != "add")) {
			echo stripslashes(nl2br($value));
		}
		if (($_GET['function'] == "update") || ($_GET['function'] == "add")) {
			echo "</textarea>";
		}
	}
}

function buildTitle($remainder, $company = "masflight: ") {
	echo $company;
	if (isset($_GET['function']) && ($_GET['function'] == "update")) {
		echo "Update";
	} elseif (isset($_GET['function']) && ($_GET['function'] == "add")) {
		echo "Add";
	} elseif (isset($_GET['function']) && ($_GET['function'] == "view")) {
		echo "View";
	}
	echo " " . $remainder;
}

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
	$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
	switch ($theType) {
		case "text":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
		case "long":
		case "int":
			$theValue = ($theValue != "") ? intval($theValue) : "NULL";
			break;
		case "double":
			$theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
			break;
		case "date":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
		case "defined":
			$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			break;
	}
	return $theValue;
}

function login() {
	if (!isset($_SESSION['MM_Username'])) {
		echo "<div class=\"login\"><a title=\"Login\" href=\"../userPortals/index.php?ref=" . $_SERVER['PHP_SELF'] . "\">Login</a></div>";
	} else {
		echo "<div class=\"login\">Welcome, " . $_SESSION['firstName'] . "!&nbsp;<a title=\"Logout\" href=\"../userPortals/myPortal.php?doLogout=true\">Logout</a></div>";
	}
}

function noRecords($recordset) {
	echo "<div class=\"norecords\">There are no " . $recordset . " for the filtering specified.</div>";
}

function tab($tabname, $tabtext) {
	echo "<span class=\"";
	if (isset($_GET['' . $tabname . ''])) {
		echo "tabfor";
	} else {
		echo "tabbak";
	}
	echo "\" id=\"tab_" . $tabname . "\"><a href=\"#tab" . $tabname . "\" onclick=\"return showTab('" . $tabname . "')\">" . $tabtext . "</a></span>\n";
}

function makeLabel($forValue, $labelText) {
	if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
		echo "<label for=\"" . $forValue . "\">" . $labelText . "</label>";
	} else {
		echo $labelText;
	}
}

function determineState($var, $trueState, $trueAction, $falseAction) {
	if ($var == $trueState) {
		echo $trueAction;
	} else {
		echo $falseAction;
	}
}

function build_header() {
	?>
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />

	<link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- Font Awesome Icons -->
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- Ionicons -->
	<link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
	<!-- DATA TABLES -->
	<link href="../bootstrap/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
	<!-- Theme style -->
	<link href="../css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
	<!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
	<link href="../css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
	<link href="../css/global_app.css" rel="stylesheet" type="text/css" />

	<!-- jQuery 2.1.4 -->
	<script src="../js/jQuery-2.1.4.min.js"></script>
	<!-- jQuery UI 1.11.2 -->
	<script src="http://code.jquery.com/ui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>
	<!-- Bootstrap 3.3.2 JS -->
	<script src="../js/bootstrap.min.js" type="text/javascript"></script>    

	<!-- DATA TABES SCRIPT -->
	<script src="../bootstrap/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="../bootstrap/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

	<!-- AdminLTE App -->
	<script src="../js/app.min.js" type="text/javascript"></script>

	<!-- Global App JS -->
	<script src="../js/global_app.js" type="text/javascript"></script>

	<link href="../css/custom.css" rel="stylesheet" type="text/css" />
	<?php
}

function build_navbar($conn, $active = 0) {
	?>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		 <div class="container-fluid">
			  <!-- Collect the nav links, forms, and other content for toggling -->
			  <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
					<div class="navbar-header">
						 <a href="../userPortals/myPortal.php" class="navbar-brand"><img width="100" src="../images/masflight-logo.png"/></a>
					</div>
					<ul class="nav navbar-nav">
						 <li<?php echo ($active == 0) ? ' class="active"' : ''; ?>><a href="../userPortals/myPortal.php">Dashboard</a></li>
						 <li<?php echo ($active == 1) ? ' class="active"' : ''; ?>><a href="../rfas/rfas.php">RFCs</a></li>
						 <li<?php echo ($active == 2) ? ' class="active"' : ''; ?>><a href="../statusReports/statusReports.php">Status Reports</a></li>
						 <li<?php echo ($active == 3) ? ' class="active"' : ''; ?>><a href="../maintenances/maintenances.php">Maintenance Notifications</a></li>
						 <li<?php echo ($active == 4) ? ' class="active"' : ''; ?>><a href="../supportRequests/supportRequests.php">Support Requests</a></li>
						 <li<?php echo ($active == 5) ? ' class="active"' : ''; ?>><a href="http://54.144.64.79/masflight_projects/index.php">Projects</a></li>                                
					</ul>                      
			  </div><!-- /.navbar-collapse -->
			  <!-- Navbar Right Menu -->
			  <div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						 <?php if (!isset($_SESSION['employee'])) { ?>
							 <li>
								  <a href="index.php"><span class='glyphicon glyphicon-log-in'></span>&nbsp;Login</a>
							 </li>
							 <?php
						 } else {
							 $query_rsEmployeeInfo = "SELECT employeeID, firstName, displayName FROM employees WHERE employeeID = {$_SESSION['employee']}";
							 $rsEmployeeInfo = $conn->query($query_rsEmployeeInfo) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
							 $row_rsEmployeeInfo = $rsEmployeeInfo->fetch_assoc();
							 ?>
							 <li>
								  <a href='#'>Welcome, <?php echo $row_rsEmployeeInfo['firstName']; ?>!</a>
							 </li>
							 <li>
								  <a href="../userPortals/myPortal.php?doLogout=true"><span class='glyphicon glyphicon-log-out'></span>&nbsp;Logout</a>
							 </li>
						 <?php } ?>
						 <li>&nbsp;</li>
					</ul>
			  </div>
		 </div><!-- /.container-fluid -->
	</nav>
	<?php
}

function build_footer() {
	?>
	<footer class="footer">
		 <div class="container">
			  <p class="text-muted" style="text-align: center;">&copy; 2015 Marks Systems Inc, - masFlight<br/>Technical Support. Version 2.0</p>
		 </div>
	</footer>
	<?php
}

