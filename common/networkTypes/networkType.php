<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "networkTypeAdd")) {
  $insertSQL = sprintf("INSERT INTO networktypes (networkTypeID, networkType, notes) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['networkTypeID'], "int"),
                       GetSQLValueString($_POST['networkType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "networkType.php?sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "networkTypeUpdate")) {
  $updateSQL = sprintf("UPDATE networktypes SET networkType=%s, notes=%s WHERE networkTypeID=%s",
                       GetSQLValueString($_POST['networkType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['networkTypeID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "networkType.php?sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$varNetworkType_rsNetworkTypes = "1";
if (isset($_GET['networkType'])) {
  $varNetworkType_rsNetworkTypes = (get_magic_quotes_gpc()) ? $_GET['networkType'] : addslashes($_GET['networkType']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsNetworkTypes = sprintf("SELECT networkTypeID, networkType, notes FROM networktypes WHERE networkTypeID = %s", $varNetworkType_rsNetworkTypes);
$rsNetworkTypes = mysql_query($query_rsNetworkTypes, $connProdOps) or die(mysql_error());
$row_rsNetworkTypes = mysql_fetch_assoc($rsNetworkTypes);
$totalRows_rsNetworkTypes = mysql_num_rows($rsNetworkTypes);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if ($_GET['function'] == "add") {
	buildHeaderNEW("networkType", "Network Types", "networkType", "Add a Network Type", null);
} elseif ($_GET['function'] == "update") {
	buildHeaderNEW("networkType", "Network Types", "networkType", "Update a Network Type", "Add a Network Type");
} else {
	buildHeaderNEW("networkType", "Network Types", "networkType", "View a Network Type", "Add a Network Type");
} ?>
<div align="center">
<div class="casing" align="left">
<form method="post" name="networkType" action="networkType.php">
  <table class="update" cellspacing="0" align="center">
    <tr>
      <td class="contrast"><label>Network Type:</label></td>
      <td><?php formField("text", "networkType", $row_rsNetworkTypes['networkType'], "32", "255", null, null); ?></td>
    </tr>
    <tr>
      <td valign="top" class="contrast"><label>Notes:</label></td>
      <td><?php formField("textarea", "notes", $row_rsNetworkTypes['notes'], "50", null, "5", "virtual"); ?></td>
    </tr>
<?php if ($_GET['function'] == "update") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="update" id="update" value="Update Network Type" /><?php sentSuccessful("Network Type Updated Successfully!"); ?></td></tr>
<?php } elseif ($_GET['function'] == "add") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="add" id="add" value="Add Network Type" /><?php sentSuccessful("Network Type Updated Successfully!"); ?></td></tr>
<?php } ?>
  </table>
<?php if ($_GET['function'] == "update") { ?>
      <input type="hidden" name="networkTypeID" value="<?php echo $row_rsNetworkTypes['networkTypeID']; ?>" />
      <input type="hidden" name="MM_update" value="networkTypeUpdate" />
<?php } elseif ($_GET['function'] == "add") { ?>
      <input type="hidden" name="MM_insert" value="networkTypeAdd" />
<?php } ?>
</form>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html>
<?php
mysql_free_result($rsNetworkTypes);
?>
