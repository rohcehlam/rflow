<?php require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');
session_start();

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsMaintenanceNotifs = 25;
$pageNum_rsMaintenanceNotifs = 0;
if (isset($_GET['pageNum_rsMaintenanceNotifs'])) {
  $pageNum_rsMaintenanceNotifs = $_GET['pageNum_rsMaintenanceNotifs'];
}
$startRow_rsMaintenanceNotifs = $pageNum_rsMaintenanceNotifs * $maxRows_rsMaintenanceNotifs;

$varEmployee_rsMaintenanceNotifs = "1";
if (isset($_GET['employee'])) {
  $varEmployee_rsMaintenanceNotifs = (get_magic_quotes_gpc()) ? $_GET['employee'] : addslashes($_GET['employee']);
}
$varStatus_rsMaintenanceNotifs = "1";
if (isset($_GET['status'])) {
  $varStatus_rsMaintenanceNotifs = (get_magic_quotes_gpc()) ? $_GET['status'] : addslashes($_GET['status']);
}

mysql_select_db($database_connProdOps, $connProdOps);
if (isset($_GET['employee'])) {
        $query_rsMaintenanceNotifs = sprintf("SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.employeeID, startTime AS startTimeSort, startDate AS startDateSort, TIME_FORMAT(startTime, '%%H:%%i') as startTime, DATE_FORMAT(startDate, '%%m/%%d/%%Y') as startDate, employees.displayName, maintenancenotifs.status FROM maintenancenotifs, employees WHERE maintenancenotifs.employeeID=employees.employeeID AND maintenancenotifs.employeeID=%s ORDER BY startDateSort DESC, startTimeSort DESC", $varEmployee_rsMaintenanceNotifs);
//display all maintenance notifications
} elseif ((isset($_GET['status'])) && ($_GET['status'] == "All")) {
        $query_rsMaintenanceNotifs = "SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.employeeID, startTime AS startTimeSort, startDate AS startDateSort, TIME_FORMAT(startTime, '%H:%i') as startTime, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate, employees.displayName, maintenancenotifs.status FROM maintenancenotifs, employees WHERE maintenancenotifs.employeeID=employees.employeeID ORDER BY startDateSort DESC, startTimeSort DESC";
//display maintenance notifications for the status selected by the user
} elseif ((isset($_GET['status'])) && ($_GET['status'] != "All")) {
        $query_rsMaintenanceNotifs = sprintf("SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.employeeID, startTime AS startTimeSort, startDate AS startDateSort, TIME_FORMAT(startTime, '%%H:%%i') as startTime, DATE_FORMAT(startDate, '%%m/%%d/%%Y') as startDate, employees.displayName, maintenancenotifs.status FROM maintenancenotifs, employees WHERE maintenancenotifs.status='%s' AND maintenancenotifs.employeeID=employees.employeeID ORDER BY startDateSort DESC, startTimeSort DESC", $varStatus_rsMaintenanceNotifs);
} elseif (!isset($_GET['status'])) {
        $query_rsMaintenanceNotifs = "SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.employeeID, startTime AS startTimeSort, startDate AS startDateSort, TIME_FORMAT(startTime, '%H:%i') as startTime, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate, employees.displayName, maintenancenotifs.status FROM maintenancenotifs, employees WHERE maintenancenotifs.employeeID=employees.employeeID AND (maintenancenotifs.status='Open' OR maintenancenotifs.status='Extended') ORDER BY startDateSort DESC, startTimeSort DESC";
}

$query_limit_rsMaintenanceNotifs = sprintf("%s LIMIT %d, %d", $query_rsMaintenanceNotifs, $startRow_rsMaintenanceNotifs, $maxRows_rsMaintenanceNotifs);
$rsMaintenanceNotifs = mysql_query($query_limit_rsMaintenanceNotifs, $connProdOps) or die(mysql_error());
$row_rsMaintenanceNotifs = mysql_fetch_assoc($rsMaintenanceNotifs);

if (isset($_GET['totalRows_rsMaintenanceNotifs'])) {
  $totalRows_rsMaintenanceNotifs = $_GET['totalRows_rsMaintenanceNotifs'];
} else {
  $all_rsMaintenanceNotifs = mysql_query($query_rsMaintenanceNotifs);
  $totalRows_rsMaintenanceNotifs = mysql_num_rows($all_rsMaintenanceNotifs);
}
$totalPages_rsMaintenanceNotifs = ceil($totalRows_rsMaintenanceNotifs/$maxRows_rsMaintenanceNotifs)-1;

$queryString_rsMaintenanceNotifs = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsMaintenanceNotifs") == false &&
        stristr($param, "totalRows_rsMaintenanceNotifs") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsMaintenanceNotifs = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsMaintenanceNotifs = sprintf("&totalRows_rsMaintenanceNotifs=%d%s", $totalRows_rsMaintenanceNotifs, $queryString_rsMaintenanceNotifs);

//employee filter list
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEmployees = "SELECT employeeID, engineer, displayName FROM employees WHERE engineer = 'y' ORDER BY displayName ASC";
$rsEmployees = mysql_query($query_rsEmployees, $connProdOps) or die(mysql_error());
$row_rsEmployees = mysql_fetch_assoc($rsEmployees);
$totalRows_rsEmployees = mysql_num_rows($rsEmployees);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php buildTitle("Maintenance Notifications"); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="../inc/global.css" rel="stylesheet" type="text/css" />
        <link href="../inc/menu.css" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="..//images/logos/favicon.ico" type="image/x-icon" />
        <script type="text/javascript" src="../inc/js/menu.js"></script>
        <script type="text/javascript" src="../inc/js/js.js"></script>
        <script type="text/javascript" src="../inc/js/calendarDateInput2.js"></script>
        <?php include_once("../inc/js/MaintenanceTabs.php"); ?>
</head>
<body>
<?php buildMenu(); ?>
<script type="text/javascript">
dolphintabs.init("menunav", 1)
</script>

<div class="casing" align="left">
<?php buildHeader("maintenance", null, "maintenances", "Maintenance Notifications", "Add a Maintenance Notification"); ?>
              <!-- TABS -->
      <div id="tabs">
                <span class="<?php if ((isset($_GET['employee'])) || (isset($_GET['status']))) { echo "tabbak"; } else { echo "tabfor"; } ?>" id="tab_none"><a href="maintenances.php?filter=none">No Filter</a></span>
                <span class="<?php if (isset($_GET['employee'])) { echo "tabfor"; } else { echo "tabbak"; } ?>" id="tab_employee"><a href="#tabemployee" onclick="return showTab('employee')">Engineer</a></span>
                <span class="<?php if (isset($_GET['status'])) { echo "tabfor"; } else { echo "tabbak"; } ?>" id="tab_status"><a href="#tabstatus" onclick="return showTab('status')">Status</a></span>
      <!-- TABS BODY -->
      <div id="tabscontent">
        <!-- DETAILS -->
        <a name="tabnone" id="tabnone"></a>
        <div id="tabscontent_none"<?php if ((isset($_GET['employee'])) || (isset($_GET['status']))) { echo " style=\"display: none;\""; } ?>>Select a tab to filter the available Maintenance Notifications</div>
        <a name="tabemployee" id="tabemployee"></a>
        <div id="tabscontent_employee"<?php if (!isset($_GET['employee'])) { echo " style=\"display: none;\""; } ?>>
          <form action="maintenances.php" method="get" name="filterEmployee" id="filterEmployee">
            Display Maintenance Notifications by
            <select name="employee" id="employee">
              <option value="">Select Engineer</option>
              <?php do { ?>
              <option value="<?php echo $row_rsEmployees['employeeID']; ?>"<?php if (isset($_GET['employee']) && (($row_rsEmployees['employeeID']) == ($_GET['employee']))) { echo " selected=\"selected\""; } ?>><?php echo $row_rsEmployees['displayName']; ?></option>
              <?php } while ($row_rsEmployees = mysql_fetch_assoc($rsEmployees));
  $rows = mysql_num_rows($rsEmployees);
  if($rows > 0) {
      mysql_data_seek($rsEmployees, 0);
          $row_rsEmployees = mysql_fetch_assoc($rsEmployees);
  }
?>
            </select>
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
        <a name="tabstatus" id="tabstatus"></a>
        <div id="tabscontent_status"<?php if (!isset($_GET['status'])) { echo " style=\"display: none;\""; } ?>>
          <form action="maintenances.php" method="get" name="filterStatus" id="filterStatus">
            Display <select name="status" id="status">
              <option value="">Select Status</option>
              <option value="All"<?php if (isset($_GET['status']) && ($_GET['status'] == 'All')) { echo " selected=\"selected\""; } ?>>All</option>
              <option value="Canceled"<?php if (isset($_GET['status']) && ($_GET['status'] == 'Canceled')) { echo " selected=\"selected\""; } ?>>Canceled</option>
              <option value="Closed"<?php if (isset($_GET['status']) && ($_GET['status'] == 'Closed')) { echo " selected=\"selected\""; } ?>>Closed</option>
              <option value="Extended"<?php if (isset($_GET['status']) && ($_GET['status'] == 'Extended')) { echo " selected=\"selected\""; } ?>>Extended</option>
              <option value="Open"<?php if (isset($_GET['status']) && ($_GET['status'] == 'Open')) { echo " selected=\"selected\""; } ?>>Open</option>
                </select>&nbsp;Maintenance Notifications
        <input type="submit" name="Submit" value="Submit" />
          </form>
        </div>
        </div>
        </div>

      <br />
<?php if (isset($_GET['employee'])) {
        echo "<h3>Maintenance Notifications by <em>" . $row_rsMaintenanceNotifs['displayName'] . "</em></h3>";
} elseif (isset($_GET['status'])) {
        echo "<h3><em>" . $_GET['status'] . "</em> Maintenance Notifications</h3>";
} elseif ((!isset($_GET['employee'])) && (!isset($_GET['status']))) {
        if ($totalRows_rsMaintenanceNotifs > 0) {
                echo "<h3><em>Pending</em> Maintenance Notifications</h3>";
        } else {
                echo "<h3>There are no <em>pending</em> Maintenance Notifications</h3>";
        }
}

//hide the data table if there are no records to show
if ($totalRows_rsMaintenanceNotifs > 0) { ?>
      <table class="data" align="center" cellspacing="0" cellpadding="2">
        <tr>
          <th width="6%">Date</th>
          <th width="6%">Start<br />Time</th>
          <th width="6%">ID</th>
          <th>Reason</th>
<?php if (!(isset($_GET['employee']))) {
        echo "<th>Engineer</th>";
}
if ( (!isset($_GET['status'])) || ($_GET['status'] == "All") ) {
        echo "<th>Status</th>";
} ?>
        </tr>
<?php
        $num=0;
                do {
        $num++;
        echo "<tr";
                if ($num % 2) {
                        echo " class=\"odd\"";
                }
        echo ">\n";
        ?>
          <td><?php echo $row_rsMaintenanceNotifs['startDate']; ?></td>
          <td><?php echo $row_rsMaintenanceNotifs['startTime']; ?></td>
          <td><a title="View Maintenance Notification" href="maintenance.php?function=view&amp;maintenance=<?php echo $row_rsMaintenanceNotifs['maintenanceNotifsID']; ?>"><?php echo $row_rsMaintenanceNotifs['maintenanceNotifsID']; ?></a></td>
          <td><a title="View Maintenance Notification" href="maintenance.php?function=view&amp;maintenance=<?php echo $row_rsMaintenanceNotifs['maintenanceNotifsID']; ?>"><?php echo stripslashes($row_rsMaintenanceNotifs['reason']); ?></a></td>
<?php if (!(isset($_GET['employee']))) {
        echo "<td>" . $row_rsMaintenanceNotifs['displayName'] . "</td>";
}
if ( (!isset($_GET['status'])) || ($_GET['status'] == "All") ) {
        echo "<td>" . $row_rsMaintenanceNotifs['status'] . "</td>";
} ?>
        </tr>
        <?php } while ($row_rsMaintenanceNotifs = mysql_fetch_assoc($rsMaintenanceNotifs)); ?>
</table>
<?php } //end if ($totalRows_rsMaintenanceNotifs > 0) ?>
<?php if ($totalRows_rsMaintenanceNotifs > 0) { ?>
                        <div id="count">Viewing <?php echo ($startRow_rsMaintenanceNotifs + 1) ?> through <?php echo min($startRow_rsMaintenanceNotifs + $maxRows_rsMaintenanceNotifs, $totalRows_rsMaintenanceNotifs) ?> of <?php echo $totalRows_rsMaintenanceNotifs ?> Maintenance Notifications</div>
<?php } ?>
<?php if ($totalRows_rsMaintenanceNotifs > 25) { ?>
<table class="pagination" align="center">
  <tr>
    <td width="23%" align="center">
      <?php if ($pageNum_rsMaintenanceNotifs > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsMaintenanceNotifs=%d%s", $currentPage, 0, $queryString_rsMaintenanceNotifs); ?>"><img src="../images/icons/first.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="31%" align="center">
      <?php if ($pageNum_rsMaintenanceNotifs > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsMaintenanceNotifs=%d%s", $currentPage, max(0, $pageNum_rsMaintenanceNotifs - 1), $queryString_rsMaintenanceNotifs); ?>"><img src="../images/icons/prev.jpg" /></a>
      <?php } // Show if not first page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsMaintenanceNotifs < $totalPages_rsMaintenanceNotifs) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsMaintenanceNotifs=%d%s", $currentPage, min($totalPages_rsMaintenanceNotifs, $pageNum_rsMaintenanceNotifs + 1), $queryString_rsMaintenanceNotifs); ?>"><img src="../images/icons/next.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
    <td width="23%" align="center">
      <?php if ($pageNum_rsMaintenanceNotifs < $totalPages_rsMaintenanceNotifs) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsMaintenanceNotifs=%d%s", $currentPage, $totalPages_rsMaintenanceNotifs, $queryString_rsMaintenanceNotifs); ?>"><img src="../images/icons/final.jpg" /></a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php } ?>
<?php buildFooter("0"); ?>
</div>
</body>
</html><?php
mysql_free_result($rsEmployees);
mysql_free_result($rsMaintenanceNotifs);
?>

