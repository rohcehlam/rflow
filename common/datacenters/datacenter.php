<?php require_once('../../Connections/connProdOps.php');
	require_once('../../inc/functions.php');

if ($_GET['function'] != "add") {
	$varDatacenter_rsDatacenters = "1";
	if (isset($_GET['datacenter'])) {
	  $varDatacenter_rsDatacenters = (get_magic_quotes_gpc()) ? $_GET['datacenter'] : addslashes($_GET['datacenter']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsDatacenters = sprintf("SELECT * FROM datacenters WHERE datacenterID = %s", $varDatacenter_rsDatacenters);
	$rsDatacenters = mysql_query($query_rsDatacenters, $connProdOps) or die(mysql_error());
	$row_rsDatacenters = mysql_fetch_assoc($rsDatacenters);
	$totalRows_rsDatacenters = mysql_num_rows($rsDatacenters);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "datacenterUpdate")) {
  $updateSQL = sprintf("UPDATE datacenters SET datacenter=%s, notes=%s WHERE datacenterID=%s",
                       GetSQLValueString($_POST['datacenter'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['datacenterID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "datacenter.php?function=view&datacenter=" . $_POST['datacenter'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "datacenterAdd")) {
  $insertSQL = sprintf("INSERT INTO datacenters (datacenter, notes) VALUES (%s, %s)",
                       GetSQLValueString($_POST['datacenter'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "datacenter.php?function=add&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("a Datacenter"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if ($_GET['function'] == "add") {
			buildHeaderNEW("datacenter", "Datacenters", "datacenter", "Add Datacenter", null);
		} elseif ($_GET['function'] == "update") {
			buildHeaderNEW("datacenter", "Datacenters", "datacenter", "Update Datacenter", "Add a Datacenter");
		} else {
			buildHeaderNEW("datacenter", "Datacenters", "datacenter", "View Datacenter", "Add a Datacenter");
		} ?>
<div align="center">
<div class="casing" align="left">
<form action="datacenter.php" method="post" name="datacenterForm" id="datacenterForm">
<table class="<?php echo $_GET['function']; ?>" align="center" cellspacing="0" cellpadding="2">
  <tr>
    <td class="contrast"><label for="datacenter">Datacenter:</label></td>
    <td><?php formField("text", "datacenter", $row_rsDatacenters['datacenter'], "32", "255", null, null); ?></td>
  </tr>
  <tr>
    <td valign="top" class="contrast"><label for="notes">Notes:</label></td>
      <td><?php formField("textarea", "notes", $row_rsDatacenters['notes'], "50", null, "5", "virtual"); ?></td>
  </tr>
<?php if ($_GET['function'] == "add") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="add" id="add" value="Add Datacenter" /><?php sentSuccessful("Datacenter added successfully!"); ?></td></tr>
<?php } elseif ($_GET['function'] == "update") { ?>
    <tr class="button"><td colspan="2"><input type="submit" name="update" id="update" value="Update Datacenter" /><?php sentSuccessful("Datacenter updated successfully!"); ?></td></tr>
<?php } ?>
</table>
<?php if ($_GET['function'] == "add") { ?>
	<input type="hidden" name="MM_insert" value="datacenterAdd" />
<?php } elseif ($_GET['function'] == "update") { ?>
	<input type="hidden" name="MM_update" value="datacenterUpdate" />
	<input type="hidden" name="datacenterID" value="<?php echo $row_rsDatacenters['datacenterID']; ?>" />
<?php } ?>
</form>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsDatacenters);
?>