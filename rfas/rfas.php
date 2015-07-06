<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');

$currentPage = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS);
session_start();

$maxRows_rsChangeRequests = 25;
$pageNum_rsChangeRequests = filter_input(INPUT_GET, 'pageNum_rsChangeRequests', FILTER_SANITIZE_SPECIAL_CHARS);

$startRow_rsChangeRequests = $pageNum_rsChangeRequests * $maxRows_rsChangeRequests;

$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') as dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') as windowStartDate FROM changerequests LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID LEFT JOIN applications ON changerequests.applicationID=applications.applicationID WHERE changerequests.status='Pending Approval' or changerequests.status='Submitted for CAB Approval' or changerequests.status='Returned' ORDER BY windowStartDate DESC, windowStartTime DESC";

$varEmployee_rsMaintenanceNotifs = filter_input(INPUT_GET, 'engineer', FILTER_SANITIZE_SPECIAL_CHARS);
$varStatus_rsMaintenanceNotifs = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
$varApp_rsMaintenanceNotifs = filter_input(INPUT_GET, 'app', FILTER_SANITIZE_SPECIAL_CHARS);
$varsummary_rsMaintenanceNotifs = filter_input(INPUT_GET, 'summary', FILTER_SANITIZE_SPECIAL_CHARS);

if (filter_input(INPUT_GET, 'app', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') AS dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') AS windowStartDate 
    FROM changerequests 
    LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID 
    LEFT JOIN applications ON changerequests.applicationID=applications.applicationID 
    WHERE changerequests.applicationID='$varApp_rsMaintenanceNotifs' 
    ORDER BY windowStartDate DESC, windowStartTime DESC
    ";
} elseif (filter_input(INPUT_GET, 'summary', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') AS dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') AS windowStartDate 
    FROM changerequests 
    LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID 
    LEFT JOIN applications ON changerequests.applicationID=applications.applicationID 
    WHERE summary LIKE '%$varsummary_rsMaintenanceNotifs%'
    ORDER BY windowStartDate DESC, windowStartTime DESC ";
} elseif (filter_input(INPUT_GET, 'engineer', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') AS dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') AS windowStartDate 
    FROM changerequests 
    LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID 
    LEFT JOIN applications ON changerequests.applicationID=applications.applicationID 
    WHERE 
    employeeID=employees.employeeID
    AND changerequests.submittedBy='$varEmployee_rsMaintenanceNotifs' 
    ORDER BY windowStartDate DESC, windowStartTime DESC ";
} elseif (filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) == "All") {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') as dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') as windowStartDate FROM changerequests LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID LEFT JOIN applications ON changerequests.applicationID=applications.applicationID ORDER BY windowStartDate DESC, windowStartTime DESC";
} elseif (filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS) != "All") {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') as dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') as windowStartDate 
        FROM changerequests 
        LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID 
        LEFT JOIN applications ON changerequests.applicationID=applications.applicationID 
        WHERE changerequests.status='$varStatus_rsMaintenanceNotifs'  or changerequests.status='Returned' 
        ORDER BY windowStartDate DESC, windowStartTime DESC";
} elseif (filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$query_rsChangeRequests = "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') as dateSubmitted, changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') as windowStartDate FROM changerequests LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID LEFT JOIN applications ON changerequests.applicationID=applications.applicationID WHERE changerequests.status='Pending Approval' or changerequests.status='Returned' ORDER BY windowStartDate DESC, windowStartTime DESC";
}

$query_limit_rsChangeRequests = sprintf("%s LIMIT %d, %d", $query_rsChangeRequests, $startRow_rsChangeRequests, $maxRows_rsChangeRequests);
$rsChangeRequests = $conn->query($query_limit_rsChangeRequests);
$row_rsChangeRequests = $rsChangeRequests->fetch_assoc();

$totalRows_rsChangeRequests = filter_input(INPUT_GET, 'totalRows_rsChangeRequests', FILTER_SANITIZE_SPECIAL_CHARS);
if (!$totalRows_rsChangeRequests) {
	$all_rsChangeRequests = $conn->query($query_rsChangeRequests);
	$totalRows_rsChangeRequests = $all_rsChangeRequests->num_rows;
}
$totalPages_rsChangeRequests = ceil($totalRows_rsChangeRequests / $maxRows_rsChangeRequests) - 1;

$queryString_rsChangeRequests = "";
$temp = filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_SPECIAL_CHARS);
if (!empty($temp)) {
	$params = explode("&", $temp);
	$newParams = array();
	foreach ($params as $param) {
		if (stristr($param, "pageNum_rsChangeRequests") == false &&
				  stristr($param, "totalRows_rsChangeRequests") == false) {
			array_push($newParams, $param);
		}
	}
	if (count($newParams) != 0) {
		$queryString_rsChangeRequests = "&" . htmlentities(implode("&", $newParams));
	}
}
$queryString_rsChangeRequests = sprintf("&totalRows_rsChangeRequests=%d%s", $totalRows_rsChangeRequests, $queryString_rsChangeRequests);

//select applications for application filter list
$query_rsApps = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApps = $conn->query($query_rsApps);
$row_rsApps = $rsApps->fetch_assoc();
$totalRows_rsApps = $rsApps->num_rows;

//select employees for employee filter list
$query_rsEngineers = "SELECT employeeID, displayName FROM employees WHERE employees.engineer ='y' ORDER BY displayName ASC";
$rsEngineers = $conn->query($query_rsEngineers);
$row_rsEngineers = $rsEngineers->fetch_assoc();
$totalRows_rsEngineers = $rsEngineers->num_rows;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php buildTitle("Change Requests"); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="../inc/global.css" rel="stylesheet" type="text/css" />
		<link href="../inc/menu.css" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
		<script type="text/javascript" src="../inc/js/menu.js"></script>
		<script type="text/javascript" src="../inc/js/js.js"></script>
		<script type="text/javascript" src="../inc/js/calendarDateInput2.js"></script>
		<script type="text/javascript">
			var tabs = new Array();
			tabs[0] = "none";
			tabs[1] = "app";
			tabs[2] = "engineer";
			tabs[3] = "status";
			tabs[4] = "summary";
		</script>        
	</head>
	<body>
		<?php buildMenu(); ?>
		<script type="text/javascript">
			dolphintabs.init("menunav", 1)
		</script>
		<div class="casing" align="left">
			<?php buildHeader("rfa", null, "rfas", "RFCs", "Add an RFC"); ?>
			<!-- TABS -->
			<div id="tabs">
				<span class="<?php
				if ((isset($_GET['app'])) || (isset($_GET['engineer'])) || (isset($_GET['requestOrigin'])) || (isset($_GET['status'])) || (isset($_GET['subapp'])) || (isset($_GET['summary']))) {
					echo "tabbak";
				} else {
					echo "tabfor";
				}
				?>" id="tab_none"><a href="rfas.php?filter=none">No Filter</a></span>
						<?php
						tab("app", "Application");
						tab("engineer", "Engineer");
						//tab("requestOrigin","Origin");
						tab("status", "Status");
						tab("summary", "Summary");
						?>
				<!-- TABS BODY -->
				<div id="tabscontent">
					<!-- DETAILS -->
					<a name="tabnone" id="tabnone"></a>
					<div id="tabscontent_none"<?php
					if ((isset($_GET['app'])) || (isset($_GET['engineer'])) || (isset($_GET['requestOrigin'])) || (isset($_GET['status'])) || (isset($_GET['summary']))) {
						echo " style=\"display: none;\"";
					}
					?>>Select a tab to filter the available RFCs</div>
					<a name="tabapp" id="tabapp"></a>

					<div id="tabscontent_app"<?php
					if (!isset($_GET['app'])) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="rfas.php" method="get" name="filterApp" id="filterApp">
							Display RFCs for
							<select name="app" id="app">
								<option value="">Select Application</option>
								<?php do { ?>
									<option value="<?php echo $row_rsApps['applicationID'] ?>"<?php
									if (isset($_GET['app']) && ($row_rsApps['applicationID'] == $_GET['app'])) {
										echo " selected=\"selected\"";
									}
									?>><?php echo $row_rsApps['application'] ?></option>
											  <?php
										  } while ($row_rsApps = mysql_fetch_assoc($rsApps));
										  $rows = mysql_num_rows($rsApps);
										  if ($rows > 0) {
											  mysql_data_seek($rsApps, 0);
											  $row_rsApps = mysql_fetch_assoc($rsApps);
										  }
										  ?>
							</select>
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>

					<a name="taborigin" id="taborigin"></a>
					<div id="tabscontent_origin"<?php
					if (!isset($_GET['origin'])) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="rfas.php" method="get" name="filterOrigin" id="filterOrigin">
							Display RFCs for <select name="origin" id="origin">
								<option value="">Select Origin</option>
								<option value="Ticket"<?php
								if (isset($_GET['origin']) && ($row_rsRFA['requestOrigin'] == $_GET['engineer'])) {
									echo " selected=\"selected\"";
								}
								?>><?php echo $row_rsEngineers['displayName'] ?></option>
							</select>&nbsp;#<input type="text" name="ticket" id="ticket"<?php
							if (isset($_GET['ticket'])) {
								echo " value=\"" . $_GET['ticket'] . "\"";
							}
							?> size="10" />
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>

					<a name="tabengineer" id="tabengineer"></a>
					<div id="tabscontent_engineer"<?php
					if (!isset($_GET['employee'])) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="rfas.php" method="get" name="filterEngineer" id="filterEngineer">
							Display RFCs submitted by 
                                                        <select name="engineer" id="engineer">
								<option value="">Select Engineer</option>
								<?php do { ?>
									<option value="<?php echo $row_rsEngineers['employeeID'] ?>"<?php
									if (isset($_GET['engineer']) && ($row_rsEngineers['employeeID'] == $_GET['engineer'])) {
										echo " selected=\"selected\"";
									}
									?>><?php echo $row_rsEngineers['displayName'] ?></option>
											  <?php
										  } while ($row_rsEngineers = mysql_fetch_assoc($rsEngineers));
										  $rows = mysql_num_rows($rsEngineers);
										  if ($rows > 0) {
											  mysql_data_seek($rsEngineers, 0);
											  $row_rsEngineers = mysql_fetch_assoc($rsEngineers);
										  }
										  ?>
							</select>
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>

					<a name="tabsummary" id="tabsummary"></a>
					<div id="tabscontent_summary"<?php
					if (!isset($_GET['summary'])) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="rfas.php" method="get" name="filterSummary" id="filterSummary">
							Display RFCs with the Summary containing <input type="text" name="summary" id="summary"<?php
							if (isset($_GET['summary'])) {
								echo " value=\"" . $_GET['summary'] . "\"";
							}
							?> size="20" />
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>    

					<a name="tabstatus" id="tabstatus"></a>
					<div id="tabscontent_status"<?php
					if (!isset($_GET['status'])) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="rfas.php" method="get" name="filterStatus" id="filterStatus">
							Display <select name="status" id="status">
								<option value="">Select Status</option>
								<option value="All"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'All')) {
									echo " selected=\"selected\"";
								}
								?>>All</option>
								<option value="Pending Approval"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Pending Approval')) {
									echo " selected=\"selected\"";
								}
								?>>Pending Approval</option>
								<option value="Pre-approved"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Pre-approved')) {
									echo " selected=\"selected\"";
								}
								?>>Pre-approved</option>
								<option value="Approved"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Approved')) {
									echo " selected=\"selected\"";
								}
								?>>Approved</option>
								<option value="Declined"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Declined')) {
									echo " selected=\"selected\"";
								}
								?>>Declined</option>
								<option value="Returned"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Returned')) {
									echo " selected=\"selected\"";
								}
								?>>Returned</option>
								<option value="Submitted for CAB Approval"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Submitted for CAB Approval')) {
									echo " selected=\"selected\"";
								}
								?>>Submitted for CAB Approval</option>
								<option value="Approved by CAB"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Approved by CAB')) {
									echo " selected=\"selected\"";
								}
								?>>Approved by CAB</option>
								<option value="Rejected by CAB"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Rejected by CAB')) {
									echo " selected=\"selected\"";
								}
								?>>Rejected by CAB</option>
								<option value="Returned by CAB"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Returned by CAB')) {
									echo " selected=\"selected\"";
								}
								?>>Returned by CAB</option>
								<option value="Completed"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Completed')) {
									echo " selected=\"selected\"";
								}
								?>>Completed</option>
								<option value="Resolved"<?php
								if (isset($_GET['status']) && ($_GET['status'] == 'Resolved')) {
									echo " selected=\"selected\"";
								}
								?>>Resolved</option>
							</select>&nbsp;Maintenance Notifications
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>



				</div>
			</div>                    
			<?php
			if ($totalRows_rsChangeRequests > 0) {
				echo "<h3><em>Pending</em> RFCs</h3>";
			} else {
				echo "<h3>There are no <em>pending</em> RFCs</h3>";
			}

//hide the data table if there are no records to show
			if ($totalRows_rsChangeRequests > 0) {
				?>
				<table class="data" align="center" cellpadding="2" cellspacing="0">
					<tr>
						<th>Date<br />Submitted</th>
						<th>Submitted By</th>
						<th>Summary</th>
						<th>App</th>
						<th>Status</th>
						<th>Window</th>
						<?php sudoAuthData(null, null, "th", "edit", null); ?>
					</tr>
					<?php
					$num = 0;
					do {
						$num++;
						echo "<tr";
						if ($num % 2) {
							echo " class=\"odd\"";
						}
						echo ">";
						?>
						<td><?php echo $row_rsChangeRequests['dateSubmitted']; ?></td>
						<td><?php echo $row_rsChangeRequests['displayName']; ?></td>
						<td><?php echo "<a href=\"rfa.php?function=view&amp;rfa=" . $row_rsChangeRequests['changeRequestID'] . "\">" . $row_rsChangeRequests['summary'] . "</a>"; ?></td>
						<td><?php echo $row_rsChangeRequests['application']; ?></td>
						<td><?php echo $row_rsChangeRequests['status']; ?></td>
						<td><?php echo $row_rsChangeRequests['windowStartDate']; ?></td>
						<?php sudoAuthData("rfa.php", "Update RFA", "td", "edit", "function=update&amp;rfa=" . $row_rsChangeRequests['changeRequestID']); ?>
						</tr>
					<?php } while ($row_rsChangeRequests = mysql_fetch_assoc($rsChangeRequests)); ?>
				</table>

				<div id="count">Viewing <?php echo ($startRow_rsChangeRequests + 1) ?> through <?php echo min($startRow_rsChangeRequests + $maxRows_rsChangeRequests, $totalRows_rsChangeRequests) ?> of <?php echo $totalRows_rsChangeRequests ?> Change Requests</div>
				<?php if ($totalRows_rsChangeRequests > 25) { ?>
					<table class="pagination" width="50%" align="center">
						<tr>
							<td width="23%" align="center"><?php if ($pageNum_rsChangeRequests > 0) { // Show if not first page    ?>
									<a href="<?php printf("%s?pageNum_rsChangeRequests=%d%s", $currentPage, 0, $queryString_rsChangeRequests); ?>"><img src="../images/icons/first.jpg" alt="First" /></a>
								<?php } // Show if not first page   ?>
							</td>
							<td width="31%" align="center"><?php if ($pageNum_rsChangeRequests > 0) { // Show if not first page    ?>
									<a href="<?php printf("%s?pageNum_rsChangeRequests=%d%s", $currentPage, max(0, $pageNum_rsChangeRequests - 1), $queryString_rsChangeRequests); ?>"><img src="../images/icons/prev.jpg" alt="Previous" /></a>
								<?php } // Show if not first page   ?>
							</td>
							<td width="23%" align="center"><?php if ($pageNum_rsChangeRequests < $totalPages_rsChangeRequests) { // Show if not last page    ?>
									<a href="<?php printf("%s?pageNum_rsChangeRequests=%d%s", $currentPage, min($totalPages_rsChangeRequests, $pageNum_rsChangeRequests + 1), $queryString_rsChangeRequests); ?>"><img src="../images/icons/next.jpg" alt="Next" /></a>
								<?php } // Show if not last page   ?>
							</td>
							<td width="23%" align="center"><?php if ($pageNum_rsChangeRequests < $totalPages_rsChangeRequests) { // Show if not last page    ?>
									<a href="<?php printf("%s?pageNum_rsChangeRequests=%d%s", $currentPage, $totalPages_rsChangeRequests, $queryString_rsChangeRequests); ?>"><img src="../images/icons/final.jpg" alt="Final" /></a>
								<?php } // Show if not last page   ?>
							</td>
						</tr>
					</table>
					<?php
				}
			}
			?>
			<?php buildFooter("0"); ?>
		</div>
		</div>
	</body>
</html>

