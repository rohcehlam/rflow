<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "leaveTypeAdd")) {
  $insertSQL = sprintf("INSERT INTO leavetypes (leaveType, notes) VALUES (%s, %s)",
                       GetSQLValueString($_POST['leaveType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "leaveTypes.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "leaveTypeUpdate")) {
  $updateSQL = sprintf("UPDATE leavetypes SET leaveType=%s, notes=%s WHERE leaveTypeID=%s",
                       GetSQLValueString($_POST['leaveType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['leaveTypeID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "leaveTypes.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ($_GET['function'] != "add") {
	$varLeaveType_rsLeaveTypes = "1";
	if (isset($_GET['leaveType'])) {
	  $varLeaveType_rsLeaveTypes = (get_magic_quotes_gpc()) ? $_GET['leaveType'] : addslashes($_GET['leaveType']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsLeaveTypes = sprintf("SELECT leaveTypeID, leaveType, notes FROM leavetypes WHERE leaveTypeID = %s", $varLeaveType_rsLeaveTypes);
	$rsLeaveTypes = mysql_query($query_rsLeaveTypes, $connProdOps) or die(mysql_error());
	$row_rsLeaveTypes = mysql_fetch_assoc($rsLeaveTypes);
	$totalRows_rsLeaveTypes = mysql_num_rows($rsLeaveTypes);
} ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Leave Type</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mobile365: Leave Types</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<div align="center">
<div class="casing">
<form method="post" name="leaveTypeForm" action="leaveType.php">
  <table align="center" class="add" cellpadding="2" cellspacing="0">
    <tr>
      <td class="contrast"><label>Leave Type:</label></td>
      <td><?php formField("text", "leaveType", $row_rsLeaveTypes['leaveType'], "32", "255", null, null); ?></td>
    </tr>
    <tr>
      <td align="right" valign="top" nowrap class="contrast"><label>Notes:</label></td>
      <td><?php formField("textarea", "notes", $row_rsLeaveTypes['notes'], "50", null, "5", "virtual"); ?></td>
    </tr>
<?php if ($_GET['function'] == "add") { ?>
    <tr class="button"><td colspan="2"><input type="submit" value="Add Leave Type" /></td></tr>
<?php } elseif ($_GET['function'] == "update") { ?>
    <tr class="button"><td colspan="2"><input type="submit" value="Update Leave Type" /></td></tr>
<?php } ?>
  </table>
<?php if ($_GET['function'] == "add") { ?>
  <input type="hidden" name="MM_insert" value="leaveTypeAdd" />
<?php } elseif ($_GET['function'] == "update") { ?>
  <input type="hidden" name="leaveTypeID" value="<?php echo $row_rsLeaveTypes['leaveTypeID']; ?>" />
  <input type="hidden" name="MM_update" value="leaveTypeUpdate" />
<?php } ?>
</form>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html>
<?php
mysql_free_result($rsLeaveTypes);
?>