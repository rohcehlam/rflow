<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php'); 

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "subapplicationAdd")) {
  $insertSQL = sprintf("INSERT INTO subapplications (subapplication, notes) VALUES (%s, %s)",
                       GetSQLValueString($_POST['subapplication'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "subapplications.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "subapplicationUpdate")) {
  $updateSQL = sprintf("UPDATE subapplications SET subapplication=%s, notes=%s WHERE subapplicationID=%s",
                       GetSQLValueString($_POST['subapplication'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['subapplicationID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "subapplications.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ($_GET['function'] != "add") {
	$varSubapp_rsSubapplications = "1";
	if (isset($_GET['subapplication'])) {
	  $varSubapp_rsSubapplications = (get_magic_quotes_gpc()) ? $_GET['subapplication'] : addslashes($_GET['subapplication']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsSubapplications = sprintf("SELECT subapplicationID, subapplication, notes FROM subapplications WHERE subapplicationID = %s", $varSubapp_rsSubapplications);
	$rsSubapplications = mysql_query($query_rsSubapplications, $connProdOps) or die(mysql_error());
	$row_rsSubapplications = mysql_fetch_assoc($rsSubapplications);
	$totalRows_rsSubapplications = mysql_num_rows($rsSubapplications);
} ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Subapplications</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if ($_GET['function'] == "add") {
	buildHeaderNEW("subapplication", "Subapplications", "subapplication", "Add a Subapplication", null);
} elseif ($_GET['function'] == "update") {
	buildHeaderNEW("subapplication", "Subapplications", "subapplication", "Update a Subapplication", "Add a Subapplication");
} else {
	buildHeaderNEW("subapplication", "Subapplications", "subapplication", "View a Subapplication", "Add a Subapplication");
} ?>
<div align="center">
<div class="casing" align="left">
<form method="post" name="subApplicationForm" action="subapplication.php">
  <table align="center" cellpadding="2" cellspacing="0" class="add">
    <tr>
      <td class="contrast"><label>Subapplication:</label></td>
      <td><?php formField("text", "subapplication", $row_rsSubapplications['subapplication'], "32", "255", null, null); ?></td>
    </tr>
    <tr>
      <td valign="top" class="contrast"><label>Notes:</label></td>
      <td><?php formField("textarea", "notes", $row_rsSubapplications['notes'], "50", null, "5", "virtual"); ?></td>
    </tr>
<?php if ($_GET['function'] == "add") { ?>
    <tr class="button"><td colspan="2"><input type="submit" value="Add Subapplication" /></td></tr>
<?php } elseif ($_GET['function'] == "update") { ?>
    <tr class="button"><td colspan="2"><input type="submit" value="Update Subapplication" /></td></tr>
<?php } ?>
  </table>
<?php if ($_GET['function'] == "add") { ?>
  <input type="hidden" name="MM_insert" value="subapplicationAdd" />
<?php } elseif ($_GET['function'] == "update") { ?>
  <input type="hidden" name="subapplicationID" value="<?php echo $row_rsSubapplications['subapplicationID']; ?>" />
  <input type="hidden" name="MM_update" value="subapplicationUpdate" />
<?php } ?>
</form>
</div></div>
</body>
</html><?php
mysql_free_result($rsSubapplications);
?>