<?php require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsServers = 25;
$pageNum_rsServers = 0;
if (isset($_GET['pageNum_rsServers'])) {
  $pageNum_rsServers = $_GET['pageNum_rsServers'];
}
$startRow_rsServers = $pageNum_rsServers * $maxRows_rsServers;<a href="servers.php">Mobile365: View Servers</a>

$varDatacenter_rsServers = "1";
if (isset($_GET['datacenter'])) {
  $varDatacenter_rsServers = (get_magic_quotes_gpc()) ? $_GET['datacenter'] : addslashes($_GET['datacenter']);
}
$varServerType_rsServers = "1";
if (isset($_GET['serverType'])) {
  $varServerType_rsServers = (get_magic_quotes_gpc()) ? $_GET['serverType'] : addslashes($_GET['serverType']);
}

mysql_select_db($database_connProdOps, $connProdOps);
if (isset($_GET['datacenter'])) {
	$query_rsServers = sprintf("SELECT servers.serverID, servers.publicIP, servers.hostname, servers.privateIP, datacenters.datacenter, servers.notes, servertypes.serverType, servers.rack, servers.lastUpdated, servers.employeeID, employees.displayName FROM servers LEFT JOIN datacenters ON servers.datacenterID = datacenters.datacenterID LEFT JOIN servertypes ON servers.serverTypeID=serverTypes.serverTypeID LEFT JOIN employees ON servers.employeeID=employees.employeeID WHERE servers.datacenterID=%s ORDER BY hostname ASC",$varDatacenter_rsServers);
} elseif (isset($_GET['serverType'])) {
	$query_rsServers = sprintf("SELECT servers.serverID, servers.publicIP, servers.hostname, servers.privateIP, datacenters.datacenter, servers.notes, servertypes.serverType, servers.rack, servers.lastUpdated, servers.employeeID, employees.displayName FROM servers LEFT JOIN datacenters ON servers.datacenterID = datacenters.datacenterID LEFT JOIN servertypes ON servers.serverTypeID=serverTypes.serverTypeID LEFT JOIN employees ON servers.employeeID=employees.employeeID WHERE servers.serverTypeID=%s ORDER BY hostname ASC",$varServerType_rsServers);
} else {
	$query_rsServers = "SELECT servers.serverID, servers.publicIP, servers.hostname, servers.privateIP, datacenters.datacenter, servers.notes, servertypes.serverType, servers.rack, servers.lastUpdated, servers.employeeID, employees.displayName FROM servers LEFT JOIN datacenters ON servers.datacenterID = datacenters.datacenterID LEFT JOIN servertypes ON servers.serverTypeID=serverTypes.serverTypeID LEFT JOIN employees ON servers.employeeID=employees.employeeID ORDER BY hostname ASC";
}
$query_limit_rsServers = sprintf("%s LIMIT %d, %d", $query_rsServers, $startRow_rsServers, $maxRows_rsServers);
$rsServers = mysql_query($query_limit_rsServers, $connProdOps) or die(mysql_error());
$row_rsServers = mysql_fetch_assoc($rsServers);

if (isset($_GET['totalRows_rsServers'])) {
  $totalRows_rsServers = $_GET['totalRows_rsServers'];
} else {
  $all_rsServers = mysql_query($query_rsServers);
  $totalRows_rsServers = mysql_num_rows($all_rsServers);
}
$totalPages_rsServers = ceil($totalRows_rsServers/$maxRows_rsServers)-1;

$queryString_rsServers = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsServers") == false && 
        stristr($param, "totalRows_rsServers") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsServers = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsServers = sprintf("&totalRows_rsServers=%d%s", $totalRows_rsServers, $queryString_rsServers);

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
<title>Mobile365: View Servers</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../inc/global.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="../inc/js/js.js"></script>
	<?php include_once('../inc/js/serverTabs.php'); ?>
</head>
<body>
<?php buildHeader("server", null, "servers", "Servers", "Add a Server"); ?>
<div align="center">
<div class="casing" align="left"><br />
	      <!-- TABS -->
      <div id="tabs">
	  	<span class="<?php if ((isset($_GET['datacenter'])) || (isset($_GET['serverType']))) { echo "tabbak"; } else { echo "tabfor"; } ?>" id="tab_none"><a href="servers.php?filter=none">No Filter</a></span>
		<span class="<?php if (isset($_GET['datacenter'])) { echo "tabfor"; } else { echo "tabbak"; } ?>" id="tab_datacenter"><a href="#tabdatacenter" onClick="return showTab('datacenter')">Datacenter</a></span>
		<span class="<?php if (isset($_GET['serverType'])) { echo "tabfor"; } else { echo "tabbak"; } ?>" id="tab_serverType"><a href="#tabserverType" onClick="return showTab('serverType')">Server Type</a></span>
      <!-- TABS BODY -->
      <div id="tabscontent">
        <!-- DETAILS -->
        <a name="tabnone" id="tabnone"></a>
        <div id="tabscontent_none"<?php if ((isset($_GET['datacenter'])) || (isset($_GET['serverType']))) { echo " style=\"display: none;\""; } ?>>Select a tab to filter/sort the available Servers</div>
        <a name="tabdatacenter" id="tabdatacenter"></a>
        <div id="tabscontent_datacenter"<?php if (!isset($_GET['datacenter'])) { echo " style=\"display: none;\""; } ?>>
          <form action="servers.php" method="get" name="filterDatacenter" id="filterDatacenter">
            Display Servers in 
              <select name="datacenter" id="datacenter">
				<option<?php if (!isset($_GET['datacenter'])) { echo " selected=\"selected\""; } ?>>Select Datacenter</option>
                <?php do { ?>
                <option value="<?php echo $row_rsDatacenters['datacenterID']?>"><?php echo $row_rsDatacenters['datacenter']?></option>
                <?php } while ($row_rsDatacenters = mysql_fetch_assoc($rsDatacenters));
  $rows = mysql_num_rows($rsDatacenters);
  if($rows > 0) {
      mysql_data_seek($rsDatacenters, 0);
	  $row_rsDatacenters = mysql_fetch_assoc($rsDatacenters);
  } ?>
              </select>
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
        <a name="tabServerType" id="tabServerType"></a>
        <div id="tabscontent_serverType"<?php if (!isset($_GET['serverType'])) { echo " style=\"display: none;\""; } ?>>
          <form action="servers.php" method="get" name="filterServerType" id="filterServerType">
            Display Servers of <select name="serverType" id="serverType">
				<option<?php if (!isset($_GET['serverType'])) { echo " selected=\"selected\""; } ?>>Select Server Type</option>
<?php do { ?>
	<option value="<?php echo $row_rsServerTypes['serverTypeID']?>"><?php echo $row_rsServerTypes['serverType']?></option>
<?php } while ($row_rsServerTypes = mysql_fetch_assoc($rsServerTypes));
  $rows = mysql_num_rows($rsServerTypes);
  if($rows > 0) {
      mysql_data_seek($rsServerTypes, 0);
	  $row_rsServerTypes = mysql_fetch_assoc($rsServerTypes);
  } ?>	</select> Server Type
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
	</div>
</div><br />

<?php if (isset($_GET['datacenter'])) {
	echo "<h3>" . $row_rsServers['datacenter'] . "</h3>";
} elseif (isset($_GET['serverType'])) {
	echo "<h3>" . $row_rsServers['serverType'] . "s</h3>";
} ?>
<table class="data" align="center" cellspacing="0">
  <tr>
    <th>Private IP</th>
    <th>Public IP</th>
    <th>Hostname</th>
<?php if (!(isset($_GET['datacenter']))) {
	echo "<th>Datacenter</th>";
}
if (!(isset($_GET['serverType']))) {
	echo "<th>Server Type</th>";
} ?>
	<th>Rack</th>
	<th>Updated</th>
	<th>Updater</th>
<?php sudoAuthDataOLD(null, null, "th", null, null); ?>
  </tr>
<?php 
	$num=0;
		do { 
	$num++;
	echo "<tr";
		if ($num % 2) {
			echo " class=\"odd\"";
		}
	echo ">";
	?>
    <td><?php echo $row_rsServers['privateIP']; ?></td>
    <td><?php echo $row_rsServers['publicIP']; ?></td>
    <td><a href="server.php?server=<?php echo $row_rsServers['serverID']; ?>"><?php echo $row_rsServers['hostname']; ?></a></td>
<?php if (!(isset($_GET['datacenter']))) {
	echo "<td>" . $row_rsServers['datacenter'] . "</td>";
}
if (!(isset($_GET['serverType']))) {
	echo "<td>" . $row_rsServers['serverType'] . "</td>";
} ?>
	<td><?php echo $row_rsServers['rack']; ?></td>
	<td><?php echo $row_rsServers['lastUpdated']; ?></td>
	<td><?php echo $row_rsServers['displayName']; ?></td>
	<?php sudoAuthDataOLD("server", "Update Server", "td", "edit", "function=update&amp;server=" . $row_rsServers['serverID']); ?>
  </tr>
  <?php } while ($row_rsServers = mysql_fetch_assoc($rsServers)); ?>
</table>
<br>
<div id="count">Viewing <?php echo ($startRow_rsServers + 1) ?> through <?php echo min($startRow_rsServers + $maxRows_rsServers, $totalRows_rsServers) ?> of <?php echo $totalRows_rsServers ?> Servers</div>
<br />
<table class="pagination" align="center">
  <tr><td width="23%" align="center">
      <?php if ($pageNum_rsServers > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsServers=%d%s", $currentPage, 0, $queryString_rsServers); ?>"><img src="../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsServers > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsServers=%d%s", $currentPage, max(0, $pageNum_rsServers - 1), $queryString_rsServers); ?>"><img src="../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsServers < $totalPages_rsServers) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsServers=%d%s", $currentPage, min($totalPages_rsServers, $pageNum_rsServers + 1), $queryString_rsServers); ?>"><img src="../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td><td width="23%" align="center">
      <?php if ($pageNum_rsServers < $totalPages_rsServers) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsServers=%d%s", $currentPage, $totalPages_rsServers, $queryString_rsServers); ?>"><img src="../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td></tr>
</table><br />
<?php buildFooter("0"); ?>
</div>
</div>
</body>
</html><?php
mysql_free_result($rsServers);
mysql_free_result($rsDatacenters);
mysql_free_result($rsServerTypes);
?>