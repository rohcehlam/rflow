<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "protocolAdd")) {
  $insertSQL = sprintf("INSERT INTO protocols (protocolID, protocol, notes) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['protocolID'], "int"),
                       GetSQLValueString($_POST['protocol'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "protocols.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "protocolUpdate")) {
  $updateSQL = sprintf("UPDATE protocols SET protocol=%s, notes=%s WHERE protocolID=%s",
                       GetSQLValueString($_POST['protocol'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['protocolID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "protocol.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ($_GET['function'] != "add") {
	$varProtocol_rsProtocols = "1";
	if (isset($_GET['protocol'])) {
	  $varProtocol_rsProtocols = (get_magic_quotes_gpc()) ? $_GET['protocol'] : addslashes($_GET['protocol']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsProtocols = sprintf("SELECT protocolID, protocol, notes FROM protocols WHERE protocolID = %s", $varProtocol_rsProtocols);
	$rsProtocols = mysql_query($query_rsProtocols, $connProdOps) or die(mysql_error());
	$row_rsProtocols = mysql_fetch_assoc($rsProtocols);
	$totalRows_rsProtocols = mysql_num_rows($rsProtocols);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled Document</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if($_GET['function'] == "add") {
			buildHeaderNEW("protocol", "Protocols", "protocol", "Add a Protocol", null);
	} elseif($_GET['function'] == "update") {
			buildHeaderNEW("protocol", "Protocols", "protocol", "Update a Protocol", "Add a Protocol");
	} else {
			buildHeaderNEW("protocol", "Protocols", "protocol", "View a Protocol", "Add a Protocol");
	} ?>
<?php buildHeaderNEW("protocol", "Protocols", "protocol", "View a Protocol", "Add a Protocol"); ?>
<div align="center">
<div class="casing" align="left">
<?php if (!isset($_SESSION['MM_Username'])) { ?><div class="login"><a title="Login" href="login.php">Login</a></div><?php } ?>
<form method="post" name="protocolForm" action="protocol.php">
  <table align="center" class="view" cellpadding="2" cellspacing="0">
    <tr>
      <td width="23%" class="contrast"><label for="protocol">Protocol:</label></td>
      <td width="77%"><?php formField("text", "protocol", $row_rsProtocols['protocol'], "32", "255", null, null); ?></td>
    </tr>
    <tr>
      <td valign="top" class="contrast"><label for="notes">Notes:</label></td>
      <td><?php formField("textarea", "notes", $row_rsProtocols['notes'], "50", null, "5", "virtual"); ?></td>
    </tr>
<?php if($_GET['function'] == "add") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="add" id="add" value="Add Protocol" /><?php sentSuccessful("Protocol added successfully!"); ?></td></tr>
<?php } elseif($_GET['function'] == "update") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="update" id="update" value="Update Protocol" /><?php sentSuccessful("Protocol updated successfully!"); ?></td></tr>
<?php } ?>
  </table>
<?php if($_GET['function'] == "add") { ?>
	<input type="hidden" name="MM_insert" value="protocolAdd" />
<?php } elseif($_GET['function'] == "update") { ?>
  <input type="hidden" name="MM_update" value="protocolUpdate" />
  <input type="hidden" name="protocolID" value="" />
<?php } ?>
</form>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsProtocols);
?>