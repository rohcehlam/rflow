<?php require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "serverAdd")) {
  $insertSQL = sprintf("INSERT INTO servers (serverID, hostname, publicIP, privateIP, datacenterID, serverTypeID, notes) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['serverID'], "int"),
                       GetSQLValueString($_POST['hostname'], "text"),
                       GetSQLValueString($_POST['publicIP'], "text"),
                       GetSQLValueString($_POST['privateIP'], "text"),
                       GetSQLValueString($_POST['datacenter'], "int"),
                       GetSQLValueString($_POST['serverType'], "int"),
                       GetSQLValueString($_POST['notes'], "text"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($insertSQL, $connProdOps) or die(mysql_error());

  $insertGoTo = "serverAdd.php?sent=y";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsDatacenters = "SELECT datacenterID, datacenter FROM datacenters ORDER BY datacenter ASC";
$rsDatacenters = mysql_query($query_rsDatacenters, $connProdOps) or die(mysql_error());
$row_rsDatacenters = mysql_fetch_assoc($rsDatacenters);
$totalRows_rsDatacenters = mysql_num_rows($rsDatacenters);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsServerTypes = "SELECT serverTypeID, serverType FROM servertypes ORDER BY serverType ASC";
$rsServerTypes = mysql_query($query_rsServerTypes, $connProdOps) or die(mysql_error());
$row_rsServerTypes = mysql_fetch_assoc($rsServerTypes);
$totalRows_rsServerTypes = mysql_num_rows($rsServerTypes);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: Add a Server</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../inc/js/js.js"></script>
</head>
<body>
<?php buildHeader("server", "Servers", "serverAdd", "Add a Server", null); ?>
<div align="center">
<div class="casing" align="left"><br />
<form method="post" name="serverAdd" action="<?php echo $editFormAction; ?>">
  <table class="add" cellspacing="0" align="center">
    <tr>
      <td class="contrast"><label for="hostname">Hostname:</label></td>
      <td><input type="text" name="hostname" id="hostname" value="" size="32"></td>
    </tr>
    <tr>
      <td class="contrast"><label for="publicIP">Public IP:</label></td>
      <td><input type="text" name="publicIP" id="publicIP" value="" size="32"></td>
    </tr>
    <tr>
      <td class="contrast"><label for="privateIP">Private IP:</label></td>
      <td><input type="text" name="privateIP" id="privateIP" value="" size="32"></td>
    </tr>
    <tr>
      <td class="contrast"><label for="datacenter">Datacenter:</label></td>
      <td class="icon"><select name="datacenter" id="datacenter">
        <option>Select Datacenter</option>
          <?php do { ?>
        <option value="<?php echo $row_rsDatacenters['datacenterID']; ?>"><?php echo $row_rsDatacenters['datacenter']; ?></option>
          <?php } while ($row_rsDatacenters = mysql_fetch_assoc($rsDatacenters)); ?>
        </select><?php addComponent("datacenterAdd.php", "Add a datacenter", "add"); ?>
      </td>
	</tr>
    <tr>
      <td class="contrast"><label for="serverType">Server Type:</label></td>
      <td class="icon"><select name="serverType" id="serverType">
	    <option>Select Server Type</option>
        <?php do { ?>
        <option value="<?php echo $row_rsServerTypes['serverTypeID']?>"><?php echo $row_rsServerTypes['serverType']?></option>
        <?php } while ($row_rsServerTypes = mysql_fetch_assoc($rsServerTypes));
  $rows = mysql_num_rows($rsServerTypes);
  if($rows > 0) {
      mysql_data_seek($rsServerTypes, 0);
	  $row_rsServerTypes = mysql_fetch_assoc($rsServerTypes);
  } ?>
      </select><?php addComponent("serverTypeAdd.php", "Add a server type", "add"); ?></td>
    <tr>
      <td valign="top" class="contrast"><label for="notes">Notes:</label></td>
      <td><textarea name="notes" id="notes" cols="50" rows="5" wrap="VIRTUAL"></textarea></td>
    </tr>
    <tr class="button"><td colspan="2"><input name="add" type="submit" id="add" value="Add Server" /><?php sentSuccessful("Server added successfully!"); ?></td></tr>
  </table>
  <input type="hidden" name="MM_insert" value="serverAdd" />
</form><br />
<?php buildFooter("0"); ?>
</div></div>
</body>
</html><?php
mysql_free_result($rsDatacenters);
mysql_free_result($rsServerTypes);
?>