<?php require_once('../../Connections/connProdOps.php'); 
require_once('../../inc/functions.php');

//Only query for the list of customer types if we're not adding a customer type
if ($_GET['function'] != "add") {
	$colname_rsCustomerType = "1";
	if (isset($_GET['customerType'])) {
	  $colname_rsCustomerType = (get_magic_quotes_gpc()) ? $_GET['customerType'] : addslashes($_GET['customerType']);
	}
	mysql_select_db($database_connProdOps, $connProdOps);
	$query_rsCustomerType = sprintf("SELECT customerTypeID, customerType, notes FROM customertypes WHERE customerTypeID = %s", $colname_rsCustomerType);
	$rsCustomerType = mysql_query($query_rsCustomerType, $connProdOps) or die(mysql_error());
	$row_rsCustomerType = mysql_fetch_assoc($rsCustomerType);
	$totalRows_rsCustomerType = mysql_num_rows($rsCustomerType);
}

//update customer type info if we're updating
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "customerTypeUpdate")) {
  $updateSQL = sprintf("UPDATE customertypes SET customerType=%s, notes=%s WHERE customerTypeID=%s",
                       GetSQLValueString($_POST['customerType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['customerTypeID'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "customerType.php?sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//add customer type info if we're adding
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "customerTypeAdd")) {
  $insertSQL = sprintf("INSERT INTO customertypes (customerTypeID, customerType, notes) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['customerTypeID'], "int"),
                       GetSQLValueString($_POST['customerType'], "text"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "customerType.php?function=add&sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("a Customer Type"); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../../inc/js/js.js"></script>
</head>
<body>
<?php if ($_GET['function'] == "add") {
			buildHeader("customerType", "Customer Types", "customerType", "Add a Customer Type", null);
	} elseif ($_GET['function'] == "update") {
			buildHeader("customerType", "Customer Types", "customerType", "Update Customer Type", "Add a Customer Type");
	} else {
			buildHeader("customerType", "Customer Types", "customerType", "View a Customer Type", "Add a Customer Type");
	} ?>
<div align="center">
<div class="casing" align="left">
<form action="customerType.php" method="post" name="customerTypeForm" id="customerTypeForm">
  <table class="<?php echo $_GET['function']; ?>" align="center" cellspacing="0" cellpadding="2">
    <tr>
      <td width="109" class="contrast"><label for="customerType">Customer Type:</label></td>
      <td><?php formField("text", "customerType", $row_rsCustomerType['customerType'], "32", "255", null, null, "1"); ?></td>
    </tr>
    <tr>
      <td valign="top" class="contrast"><label for="notes">Notes:</label></td>
      <td><?php formField("textarea", "notes", $row_rsCustomerType['notes'], "50", null, "5", "virtual", "2"); ?></td>
    </tr>
<?php if ($_GET['function'] == "add") { ?>
	<tr class="button"><td colspan="2"><input type="submit" name="add" id="add" value="Add Customer Type" /><?php sentSuccessful("Customer Type Added Successfully!"); ?></td></tr>
<?php } elseif ($_GET['function'] == "update") { ?>
	<tr class="button"><td colspan="2"><input type="submit" name="update" id="update" value="Update Customer Type" /><?php sentSuccessful("Customer Type Updated Successfully!"); ?></td></tr>
<?php } ?>
  </table>
<?php if ($_GET['function'] == "add") { ?>
	<input type="hidden" name="MM_insert" value="customerTypeAdd" />
	<input type="hidden" name="customerTypeID" value="" />
<?php } elseif ($_GET['function'] == "update") { ?>
	<input type="hidden" name="MM_update" id="MM_update" value="customerTypeUpdate" />
	<input type="hidden" name="customerTypeID" value="<?php echo $row_rsCustomerType['customerTypeID']; ?>" />
<?php } ?>
</form>
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html>
<?php
mysql_free_result($rsCustomerType);
?>