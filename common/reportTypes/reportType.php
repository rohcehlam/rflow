<?php require_once('../../Connections/connProdOps.php');
require_once('../../inc/functions.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "reportTypeUpdate")) {
  $updateSQL = sprintf("UPDATE reporttypes SET reportType=%s, notes=%s WHERE reportTypeID=%s",
                       GetSQLValueString($_POST['reportType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['reportTypeID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "reportTypes.php?sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsReportTypes = "1";
if (isset($_GET['reportType'])) {
  $colname_rsReportTypes = (get_magic_quotes_gpc()) ? $_GET['reportType'] : addslashes($_GET['reportType']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsReportTypes = sprintf("SELECT * FROM reporttypes WHERE reportTypeID = %s", $colname_rsReportTypes);
$rsReportTypes = mysql_query($query_rsReportTypes, $connProdOps) or die(mysql_error());
$row_rsReportTypes = mysql_fetch_assoc($rsReportTypes);
$totalRows_rsReportTypes = mysql_num_rows($rsReportTypes);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: <?php if ($_GET['function'] == "update") {
					echo "Update";
				} else {
					echo "View";
				} ?> a Report Type</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if ($_GET['function'] == "update") {
	buildHeader("reportType","Report Types","reportType","Update a Report Type","Add a Report Type");
} else {
	buildHeader("reportType","Report Types","reportType","View Report Type","Add a Report Type");
} ?>
<div align="center">
<div class="casing"><br />
<form method="post" name="reportTypeUpdate" action="<?php echo $editFormAction; ?>">
  <table class="update" cellspacing="0" align="center">
    <tr>
      <td nowrap="nowrap" class="contrast"><label for="reportType">Report Type:</label></td>
      <td><?php formField("text", "reportType", $row_rsReportTypes['reportType'], "32", "255", null, null); ?></td>
    </tr>
    <tr>
      <td valign="top" class="contrast"><label for="notes">Notes:</label></td>
      <td><?php if ($_GET['function'] == "update") {
					echo "<textarea name=\"notes\" id=\"notes\" cols=\"50\" rows=\"5\" wrap=\"virtual\">";
				}
				echo stripslashes($row_rsReportTypes['notes']);
				if ($_GET['function'] == "update") {
					echo "</textarea>";
				} ?></td>
    </tr>
<?php if ($_GET['function'] == "update") { ?>
	<tr class="button"><td colspan="2"><input type="submit" value="Update Report Type" /><?php sentSuccessful("Report Type updated successfully!"); ?></td></tr>
<?php } ?>
  </table>
  <input type="hidden" name="MM_update" value="reportTypeUpdate" />
  <input type="hidden" name="reportTypeID" value="<?php echo $row_rsReportTypes['reportTypeID']; ?>" />
</form><br />
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsReportTypes);
?>