<?php require_once('../../Connections/connProdOps.php');
require_once('../../inc/functions.php');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "serverTypeAdd")) {
  $insertSQL = sprintf("INSERT INTO servertypes (serverTypeID, serverType, notes) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['serverTypeID'], "int"),
                       GetSQLValueString($_POST['serverType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "serverType.php?sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "serverTypeUpdate")) {
  $updateSQL = sprintf("UPDATE servertypes SET serverType=%s, notes=%s WHERE serverTypeID=%s",
                       GetSQLValueString($_POST['serverType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['serverTypeID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "serverType.php?serverType=" . $row_rsServerTypes['serverTypeID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsServerTypes = "1";
if (isset($_GET['serverType'])) {
  $colname_rsServerTypes = (get_magic_quotes_gpc()) ? $_GET['serverType'] : addslashes($_GET['serverType']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsServerTypes = sprintf("SELECT * FROM servertypes WHERE serverTypeID = %s", $colname_rsServerTypes);
$rsServerTypes = mysql_query($query_rsServerTypes, $connProdOps) or die(mysql_error());
$row_rsServerTypes = mysql_fetch_assoc($rsServerTypes);
$totalRows_rsServerTypes = mysql_num_rows($rsServerTypes);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: <?php if ($_GET['function'] == "update") {
					echo "Update";
				} else {
					echo "View";
				} ?> Server Type</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if ($_GET['function'] == "update") {
			buildHeader("serverType","Server Types","serverType","Update a Server Type","Add a Server Type");
	} else {
			buildHeader("serverType", "Server Types", "serverType", "View a Server Type", "Add a Server Type");
	} ?>
<div align="center">
<div class="casing" align="left"><br />
<form method="post" name="serverType" action="serverType.php">
  <table class="update" align="center" cellspacing="0">
    <tr>
      <td nowrap="nowrap" class="contrast"><label>Server Type:</label></td>
      <td><?php if ($_GET['function'] == "update") {
					echo "<input type=\"text\" name=\"serverType\" value=\"";
				}
				echo $row_rsServerTypes['serverType'];
				if ($_GET['function'] == "update") {
					echo "\" size=\"32\" />";
				} ?></td>
    </tr>
    <tr>
      <td valign="top" class="contrast"><label>Notes:</label></td>
      <td><?php if ($_GET['function'] == "update") {
					echo "<textarea name=\"notes\" cols=\"50\" rows=\"5\" wrap=\"virtual\">";
				}
				echo $row_rsServerTypes['notes'];
				if ($_GET['function'] == "update") {
					echo "</textarea>";
				} ?></td>
    </tr>
<?php if ($_GET['function'] == "update") { ?>
    <tr class="button"><td colspan="2"><input name="update" type="submit" id="update" value="Update Server Type" /></td></tr>
<?php } ?>
  </table>
  <input type="hidden" name="MM_update" value="serverTypeUpdate" />
  <input type="hidden" name="serverTypeID" value="<?php echo $row_rsServerTypes['serverTypeID']; ?>" />
</form>
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsServerTypes);
?>