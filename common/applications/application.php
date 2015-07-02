<?php require_once('../../Connections/connProdOps.php');
require_once('../../inc/functions.php');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "appAdd")) {
  $insertSQL = sprintf("INSERT INTO applications (application, notes) VALUES (%s, %s)",
                       GetSQLValueString($_POST['application'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "app.php?function=add&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "appUpdate")) {
  $updateSQL = sprintf("UPDATE applications SET application=%s, notes=%s WHERE applicationID=%s",
                       GetSQLValueString($_POST['application'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['applicationID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "apps.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ($_GET['function'] != "add") {
	$varApp_rsApps = "1";
	if (isset($_GET['app'])) {
	  $varApp_rsApps = (get_magic_quotes_gpc()) ? $_GET['app'] : addslashes($_GET['app']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsApps = sprintf("SELECT * FROM applications WHERE applicationID = %s", $varApp_rsApps);
	$rsApps = mysql_query($query_rsApps, $connProdOps) or die(mysql_error());
	$row_rsApps = mysql_fetch_assoc($rsApps);
	$totalRows_rsApps = mysql_num_rows($rsApps);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("an Application"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if ($_GET['function'] == "add") {
			buildHeaderNEW("application", "Applications", "app", "Add an Application", null);
	} elseif ($_GET['function'] == "update") {
			buildHeaderNEW("application","Applications","app","Update Application","Add an Application");
	} else {
			buildHeaderNEW("application", "Applications", "app", "View an Application", "Add an Application");
	} ?>
<div align="center">
<div class="casing" align="left">
<form name="appForm" id="appForm" method="post" action="application.php">
  <table class="update" align="center" cellspacing="0" cellpadding="2">
    <tr>
      <td class="contrast"><label for="application">Application:</label></td>
      <td><?php formField("text", "application", $row_rsApps['application'], "32", "255", null, null, "1"); ?></td>
    </tr>
    <tr>
      <td valign="top" class="contrast"><label for="notes">Notes:</label></td>
      <td><?php formField("textarea", "notes", $row_rsApps['notes'], "50", null, "5", "virtual", "2"); ?></td>
    </tr>
<?php if($_GET['function'] == "add") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="add" id="add" value="Add Application" /><?php sentSuccessful("Application added successfully!"); ?></td></tr>
<?php } elseif($_GET['function'] == "update") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="update" id="update" value="Update Application" /><?php sentSuccessful("Application updated successfully!"); ?></td></tr>
<?php } ?>
  </table>
<?php if($_GET['function'] == "add") { ?>
	<input type="hidden" name="MM_insert" value="applicationAdd" />
<?php } elseif($_GET['function'] == "update") { ?>
  <input type="hidden" name="MM_update" value="applicationUpdate" />
  <input type="hidden" name="applicationID" value="<?php echo $row_rsApps['applicationID']; ?>" />
<?php } ?>
</form>
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsApps);
?>