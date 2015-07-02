<?php require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE servers SET publicIP=%s, hostname=%s, privateIP=%s, datacenterID=%s, notes=%s, serverTypeID=%s WHERE serverID=%s",
                       GetSQLValueString($_POST['publicIP'], "text"),
                       GetSQLValueString($_POST['hostname'], "text"),
                       GetSQLValueString($_POST['privateIP'], "text"),
                       GetSQLValueString($_POST['datacenter'], "int"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['serverType'], "int"),
                       GetSQLValueString($_POST['server'], "int"));

  mysql_select_db($database_connProdOps, $connProdOps);
  $Result1 = mysql_query($updateSQL, $connProdOps) or die(mysql_error());

  $updateGoTo = "servers.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsServers = "1";
if (isset($_GET['server'])) {
  $colname_rsServers = (get_magic_quotes_gpc()) ? $_GET['server'] : addslashes($_GET['server']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsServers = sprintf("SELECT servers.serverID, servers.publicIP, servers.hostname, servers.privateIP, datacenters.datacenter, servers.notes, servertypes.serverType FROM servers LEFT JOIN datacenters ON servers.datacenterID = datacenters.datacenterID LEFT JOIN servertypes ON servers.serverTypeID=serverTypes.serverTypeID WHERE servers.serverID=%s", $colname_rsServers);
$rsServers = mysql_query($query_rsServers, $connProdOps) or die(mysql_error());
$row_rsServers = mysql_fetch_assoc($rsServers);
$totalRows_rsServers = mysql_num_rows($rsServers);

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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mobile365: <?php if ($_GET['function'] == "update") {
					echo "Update";
				} else {
					echo "View";
				} ?> a Server</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../inc/js/js.js"></script>
</head>
<body>
<?php if ($_GET['function'] == "update") {
	buildHeader("server", "Servers", "server", "Update a Server", "Add a Server");
} else {
	buildHeader("server", "Servers", "server", "View a Server", "Add a Server");
} ?>
<div align="center">
<div class="casing"><br />
<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table class="update" align="center" cellspacing="0">
    <tr>
      <td class="contrast">Hostname:</td>
      <td><?php if ($_GET['function'] == "update") {
					echo "<input type=\"text\" name=\"hostname\" value=\"";
				}
				echo $row_rsServers['hostname'];
				if ($_GET['function'] == "update") {
					echo "\" size=\"32\" maxlength=\"255\" />";
				} ?></td>
    </tr>
    <tr>
      <td nowrap class="contrast">Public IP:</td>
      <td><?php if ($_GET['function'] == "update") {
					echo "<input type=\"text\" name=\"publicIP\" value=\"";
				}
				echo $row_rsServers['publicIP'];
				if ($_GET['function'] == "update") {
					echo "\" size=\"32\" />";
				} ?></td>
    </tr>
    <tr>
      <td nowrap class="contrast">Private IP:</td>
      <td><?php if ($_GET['function'] == "update") {
					echo "<input type=\"text\" name=\"privateIP\" value=\"";
				}
				echo $row_rsServers['privateIP'];
				if ($_GET['function'] == "update") {
					echo "\" size=\"32\" />";
				} ?></td>
    </tr>
    <tr>
      <td class="contrast">Datacenter:</td>
      <td class="icon"><?php if ($_GET['function'] == "update") { ?>
				<select name="datacenter">
					<option selected="selected">Select Datacenter</option>
          <?php do { ?>
          <option value="<?php echo $row_rsDatacenters['datacenterID']?>"<?php if (!(strcmp($row_rsDatacenters['datacenterID'], $row_rsServers['datacenterID']))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsDatacenters['datacenter']?></option>
          <?php } while ($row_rsDatacenters = mysql_fetch_assoc($rsDatacenters));
  $rows = mysql_num_rows($rsDatacenters);
  if($rows > 0) {
      mysql_data_seek($rsDatacenters, 0);
	  $row_rsDatacenters = mysql_fetch_assoc($rsDatacenters);
  } ?>
        </select>&nbsp;<?php sudoAuth("datacenterAdd","Add a Datacenter","add"); ?><?php 
						} else {
							echo $row_rsServers['datacenter'];
						} ?></td>
    <tr>
      <td nowrap="nowrap" class="contrast">Server Type:</td>
      <td class="icon"><?php if ($_GET['function'] == "update") { ?>
	  		<select name="serverType">
	  		<option selected="selected">Select Server Type</option>
          <?php do { ?>
          <option value="<?php echo $row_rsServerTypes['serverTypeID']?>"<?php if (!(strcmp($row_rsServerTypes['serverTypeID'], $row_rsServers['serverTypeID']))) {echo " selected=\"selected\"";} ?>><?php echo $row_rsServerTypes['serverType']?></option>
          <?php } while ($row_rsServerTypes = mysql_fetch_assoc($rsServerTypes));
  $rows = mysql_num_rows($rsServerTypes);
  if($rows > 0) {
      mysql_data_seek($rsServerTypes, 0);
	  $row_rsServerTypes = mysql_fetch_assoc($rsServerTypes);
  }
?>
      </select>&nbsp;<?php sudoAuth("serverTypeAdd","Add a Server Type","add"); ?><?php 
						} else {
							echo $row_rsServers['serverType'];
						} ?></td>
    </tr>
    <tr>
      <td valign="top" class="contrast">Notes:</td>
      <td><?php if ($_GET['function'] == "update") {
	  				echo "<textarea name=\"notes\" cols=\"50\" rows=\"5\" wrap=\"VIRTUAL\">";
				}
				echo stripslashes($row_rsServers['notes']);
				if ($_GET['function'] == "update") {
					echo "</textarea>";
				} ?></td>
    </tr>
<?php if ($_GET['function'] == "update") { ?>
	<tr class="button"><td colspan="2"><input name="submit" type="submit" id="submit" value="Update Server" /></td></tr>
<?php } ?>
  </table>
  <input type="hidden" name="server" value="<?php echo $row_rsServers['serverID']; ?>" />
  <input type="hidden" name="MM_update" value="form1" />
</form><br />
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsServers);
mysql_free_result($rsDatacenters);
mysql_free_result($rsServerTypes);
?>