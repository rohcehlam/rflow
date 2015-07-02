<?php
error_reporting(E_ALL & ~E_NOTICE);
function buildMenu(){
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
        }else{
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
        
        /*echo "<div id=\"menucontainer\">\n";
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
	echo "</div>\n";*/
    
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
                
                /*echo "<a class='btn btn-app btn-edit-usr' href='".$rootPage.".php?function=add'  title='$addText'>
                    <i class='fa fa-users'></i> Add Users
                </a>";*/
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
	global $sudo;
	$sudo = $_SESSION['MM_Username'];
	if (isset($sudo)) {
		if ($tdth == "th") {
			echo "<th";
			if ($icon == "edit") {
				echo " class=\"edit\"";
			} elseif ($icon == "delete") {
				echo " class=\"delete\"";
			}
			echo "></th>\n";
		} elseif ($tdth == "td") {
			echo "<td align=\"center\"><a title=\"" . $linkedText . "\" href=\"" . $linkedPage . "?" . $param . "\"><img src=\"../images/icons/" . $icon . ".gif\" alt=\"" . $icon . "\" /></a></td>\n";
		}
	}
}
function buildFooter($colspan, $year="2015", $version="1.1") {
	if ($colspan!="0") {
		echo "<tr><td colspan=\"" . $colspan . "\" class=\"footer\">&copy; " . $year . " Marks Systems Inc, -  masFlight<br/> Technical Support. Version " . $version . "</td></tr>\n";
	} else {
		echo "<div class=\"footer\">&copy; " . $year . "  Marks Systems Inc, -  masFlight<br/> Technical Support. Version " . $version . "</div>\n";
	}
}
function sentSuccessful($message) {
	if ((isset($_GET["sent"])) && ($_GET["sent"] == 'y')) {
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
		if(($_GET['function'] == "update") || ($_GET['function'] == "add")) {
			echo "<input type=\"text\" name=\"" . $name . "\" id=\"" . $name . "\" value=\"";
		}
		if (($_GET['function'] == "view") && (($value == null) || ($value == "0")) ) {
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
		if(($_GET['function'] == "update") || ($_GET['function'] == "add")) {
			echo "<textarea name=\"" . $name . "\" id=\"" . $name . "\" cols=\"" . $width . "\" rows=\"" . $rows . "\" tabindex=\"" . $order . "\" wrap=\"" . $wrap . "\">";
			echo stripslashes($value);
		}
		if(($_GET['function'] != "update") && ($_GET['function'] != "add")) {
			echo stripslashes(nl2br($value));
		}
		if(($_GET['function'] == "update") || ($_GET['function'] == "add")) {
			echo "</textarea>";
		}
	}
}
function buildTitle($remainder, $company="masflight: ") {
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
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
{
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
function tab($tabname,$tabtext) {	
        echo "<span class=\"";
		if (isset($_GET['' . $tabname . ''])) { echo "tabfor"; } else { echo "tabbak"; }
	echo "\" id=\"tab_" . $tabname . "\"><a href=\"#tab" . $tabname . "\" onclick=\"return showTab('" . $tabname . "')\">" . $tabtext . "</a></span>\n";
}
function makeLabel($forValue, $labelText) {
	if ( ($_GET['function'] == "add") || ($_GET['function'] == "update")) {
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