<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "connTypeAdd")) {
  $insertSQL = sprintf("INSERT INTO conntypes (connType, notes) VALUES (%s, %s)",
                       GetSQLValueString($_POST['connType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "connType.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "connUpdate")) {
  $updateSQL = sprintf("UPDATE conntypes SET connType=%s, notes=%s WHERE connTypeID=%s",
                       GetSQLValueString($_POST['connType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['connTypeID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "connTypes.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$varConnType_rsConnTypes = "1";
if (isset($_GET['connType'])) {
  $varConnType_rsConnTypes = (get_magic_quotes_gpc()) ? $_GET['connType'] : addslashes($_GET['connType']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsConnTypes = sprintf("SELECT * FROM conntypes WHERE connTypeID = %s", $varConnType_rsConnTypes);
$rsConnTypes = mysql_query($query_rsConnTypes, $connProdOps) or die(mysql_error());
$row_rsConnTypes = mysql_fetch_assoc($rsConnTypes);
$totalRows_rsConnTypes = mysql_num_rows($rsConnTypes);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("a Connection Type"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if($_GET['function'] == "add") {
			buildHeader("connType", "Connection Types", "connType", "Add Connection Type", null);
	} elseif($_GET['function'] == "update") {
			buildHeader("connType", "Connection Types", "connType", "Update Connection Type", "Add a Connection Type");
	} else {
			buildHeader("connType", "Connection Types", "connType", "View Connection Type", "Add a Connection Type");
	} ?>
<div align="center">
<div class="casing" align="left">
<form action="connType.php" method="post" name="connTypeForm" id="connTypeForm">
  <table class="update" align="center" cellspacing="0" cellpadding="2">
    <tr>
      <td class="contrast" nowrap="nowrap"><label for="connType">Connection Type:</label></td>
      <td><?php formField("text", "connType", $row_rsConnTypes['connType'], "32", "255", null, null); ?></td>
    </tr>
    <tr>
      <td class="contrast" valign="top"><label for="notes">Notes:</label></td>
      <td><?php formField("textarea", "notes", $row_rsConnTypes['notes'], "50", null, "5", "virtual"); ?></td>
    </tr>
<?php if($_GET['function'] == "add") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="add" id="add" value="Add Connection Type" /><?php sentSuccessful("Connection Type added successfully!"); ?></td></tr>
<?php } elseif($_GET['function'] == "update") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="update" id="update" value="Update Connection Type" /><?php sentSuccessful("Connection Type updated successfully!"); ?></td></tr>
<?php } ?>
  </table>
<?php if($_GET['function'] == "add") { ?>
	<input type="hidden" name="MM_insert" value="connTypeAdd" />
<?php } elseif($_GET['function'] == "update") { ?>
  <input type="hidden" name="MM_update" value="connTypeUpdate" />
  <input type="hidden" name="connTypeID" value="<?php echo $row_rsConnTypes['connTypeID']; ?>" />
<?php } ?>
</form>
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsConnTypes);
?>