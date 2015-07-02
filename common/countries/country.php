<?php require_once('../../Connections/connProdOps.php');
require_once('../../inc/functions.php');

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "countryAdd")) {
  $insertSQL = sprintf("INSERT INTO countries (country, countryISO, countryCode, `zone`, digits, notes) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['country'], "text"),
                       GetSQLValueString($_POST['countryISO'], "text"),
                       GetSQLValueString($_POST['countryCode'], "text"),
                       GetSQLValueString($_POST['zone'], "int"),
                       GetSQLValueString($_POST['digits'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "country.php?function=add&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "countryUpdate")) {
  $updateSQL = sprintf("UPDATE countries SET zone=%s, countryCode=%s, countryISO=%s, country=%s, digits=%s, notes=%s WHERE countryID=%s",
                       GetSQLValueString($_POST['zone'], "int"),
                       GetSQLValueString($_POST['countryCode'], "text"),
                       GetSQLValueString($_POST['countryISO'], "text"),
                       GetSQLValueString($_POST['country'], "text"),
                       GetSQLValueString($_POST['digits'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['countryID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());
}

if ($_GET['function'] != "add") {
	$varCountry_rsCountries = "1";
	if (isset($_GET['country'])) {
	  $varCountry_rsCountries = (get_magic_quotes_gpc()) ? $_GET['country'] : addslashes($_GET['country']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsCountries = sprintf("SELECT zone, countryCode, countryISO, country, digits, countryID, notes FROM countries WHERE countryID = %s", $varCountry_rsCountries);
	$rsCountries = mysql_query($query_rsCountries, $connProdOps) or die(mysql_error());
	$row_rsCountries = mysql_fetch_assoc($rsCountries);
	$totalRows_rsCountries = mysql_num_rows($rsCountries);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("a Country"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if ($_GET['function'] == "add") {
			buildHeaderNEW("country", "Countries", "country", "Add a Country", null);
	} elseif ($_GET['function'] == "update") {
			buildHeaderNEW("country","Countries","country","Update a Country","Add a Country");
	} else {
			buildHeaderNEW("country", "Countries", "country", "View a Country", "Add a Country");
	} ?>
<div align="center">
<div class="casing" align="left"><br />
<form method="post" name="country" action="country.php">
  <table class="add" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <td width="89" class="contrast"><label for="country">Country:</label></td>
      <td colspan="3"><?php formField("text", "country", $row_rsCountries['country'], "32", "255", null, null); ?></td>
    </tr>
    <tr>
      <td class="contrast"><label for="countryISO">Country ISO:</label></td>
      <td width="105"><?php formField("text", "countryISO", $row_rsCountries['countryISO'], "10", "10", null, null); ?></td>
      <td width="102" class="contrast"><label for="countryCode">Country Code:</label></td>
      <td width="108"><?php formField("text", "countryCode", $row_rsCountries['countryCode'], "5", "5", null, null); ?></td>
    </tr>
    <tr>
      <td class="contrast"><label for="zone">Zone:</label></td>
      <td><?php formField("text", "zone", $row_rsCountries['zone'], "3", "3", null, null); ?></td>
      <td class="contrast"><label for="digits">Digits:</label></td>
      <td><?php formField("text", "digits", $row_rsCountries['digits'], "5", "5", null, null); ?></td>
    </tr>
    <tr>
      <td valign="top" class="contrast"><label for="notes">Notes:</label></td>
      <td colspan="3"><?php formField("textarea", "notes", $row_rsCountries['notes'], "45", null, "5", "virtual"); ?> </td>
    </tr>
<?php if($_GET['function'] == "add") { ?>
    <tr class="button"><td colspan="4"><input type="submit" name="add" id="add" value="Add Country" /><?php sentSuccessful("Country added successfully!"); ?></td></tr>
<?php } elseif($_GET['function'] == "update") { ?>
    <tr class="button"><td colspan="4"><input type="submit" name="update" id="update" value="Update Country" /><?php sentSuccessful("Country updated successfully!"); ?></td></tr>
<?php } ?>
  </table>
<?php if($_GET['function'] == "add") {
	echo "<input type=\"hidden\" name=\"MM_insert\" value=\"countryAdd\" />";
} elseif($_GET['function'] == "update") {
	echo "<input type=\"hidden\" name=\"MM_update\" value=\"countryUpdate\" />";
	echo "<input type=\"hidden\" name=\"countryID\" value=\"" . $_GET['country'] . "\" />";
} ?>
</form>
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsCountries);
?>