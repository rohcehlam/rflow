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
$varrequestOrigin_rsMaintenanceNotifs = filter_input(INPUT_GET, 'requestOrigin', FILTER_SANITIZE_SPECIAL_CHARS);
$varsubapp_rsMaintenanceNotifs = filter_input(INPUT_GET, 'subapp', FILTER_SANITIZE_SPECIAL_CHARS);
$varorigin_rsMaintenanceNotifs = filter_input(INPUT_GET, 'origin', FILTER_SANITIZE_SPECIAL_CHARS);
$varticket_rsMaintenanceNotifs = filter_input(INPUT_GET, 'ticket', FILTER_SANITIZE_SPECIAL_CHARS);
$varEEmployee_rsMaintenanceNotifs = filter_input(INPUT_GET, 'employee', FILTER_SANITIZE_SPECIAL_CHARS);

$args = array(
	 'engineer' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'status' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'app' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'summary' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'requestOrigin' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'subapp' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'origin' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'tricket' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'employee' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);
//var_dump($my_get);

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
				if ($varApp_rsMaintenanceNotifs || $varEmployee_rsMaintenanceNotifs || $varrequestOrigin_rsMaintenanceNotifs || $varStatus_rsMaintenanceNotifs || $varsubapp_rsMaintenanceNotifs || $varsummary_rsMaintenanceNotifs) {
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
					if (!$varApp_rsMaintenanceNotifs || !$varEmployee_rsMaintenanceNotifs || !$varrequestOrigin_rsMaintenanceNotifs || !$varStatus_rsMaintenanceNotifs || !$varsummary_rsMaintenanceNotifs) {
						echo " style=\"display: none;\"";
					}
					?>>Select a tab to filter the available RFCs</div>
					<a name="tabapp" id="tabapp"></a>

					<div id="tabscontent_app"<?php
					if (!$varApp_rsMaintenanceNotifs) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="rfas.php" method="get" name="filterApp" id="filterApp">
							Display RFCs for
							<select name="app" id="app">
								<option value="">Select Application</option>
								<?php do { ?>
									<option value="<?php echo $row_rsApps['applicationID'] ?>"<?php
									if ($varApp_rsMaintenanceNotifs && ($row_rsApps['applicationID'] == $varApp_rsMaintenanceNotifs)) {
										echo " selected=\"selected\"";
									}
									?>><?php echo $row_rsApps['application'] ?></option>
											  <?php
										  } while ($row_rsApps = $rsApps->fetch_assoc());
										  if ($rsApps->num_rows > 0) {
											  $rsApps->data_seek(0);
											  $row_rsApps = $rsApps->fetch_assoc();
										  }
										  ?>
							</select>
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>

					<a name="taborigin" id="taborigin"></a>
					<div id="tabscontent_origin"<?php
					if ($varorigin_rsMaintenanceNotifs) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="rfas.php" method="get" name="filterOrigin" id="filterOrigin">
							Display RFCs for <select name="origin" id="origin">
								<option value="">Select Origin</option>
								<option value="Ticket"<?php
								if (isset($varorigin_rsMaintenanceNotifs) && ($row_rsRFA['requestOrigin'] == $varEmployee_rsMaintenanceNotifs)) {
									echo " selected=\"selected\"";
								}
								?>><?php echo $row_rsEngineers['displayName'] ?></option>
							</select>&nbsp;#<input type="text" name="ticket" id="ticket"<?php
							if ($varticket_rsMaintenanceNotifs) {
								echo " value=\"$varticket_rsMaintenanceNotifs\"";
							}
							?> size="10" />
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>

					<a name="tabengineer" id="tabengineer"></a>
					<div id="tabscontent_engineer"<?php
					if ($varEEmployee_rsMaintenanceNotifs) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="rfas.php" method="get" name="filterEngineer" id="filterEngineer">
							Display RFCs submitted by <select name="engineer" id="engineer">
								<option value="">Select Engineer</option>
								<?php do { ?>
									<option value="<?php echo $row_rsEngineers['employeeID'] ?>"<?php
									if (isset($varEmployee_rsMaintenanceNotifs) && ($row_rsEngineers['employeeID'] == $varEmployee_rsMaintenanceNotifs)) {
										echo " selected=\"selected\"";
									}
									?>><?php echo $row_rsEngineers['displayName'] ?></option>
											  <?php
										  } while ($row_rsEngineers = $rsEngineers->fetch_assoc());
										  if ($rsEngineers->num_rows > 0) {
											  $rsEngineer->data_seek(0);
											  $row_rsEngineers = $rsEngineers->fetch_assoc();
										  }
										  ?>
							</select>
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>

					<a name="tabsummary" id="tabsummary"></a>
					<div id="tabscontent_summary"<?php
					if ($varsummary_rsMaintenanceNotifs) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="rfas.php" method="get" name="filterSummary" id="filterSummary">
							Display RFCs with the Summary containing <input type="text" name="summary" id="summary"<?php
							if ($varsummary_rsMaintenanceNotifs) {
								echo " value=\"$varsummary_rsMaintenanceNotifs\"";
							}
							?> size="20" />
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>    

					<a name="tabstatus" id="tabstatus"></a>
					<div id="tabscontent_status"<?php
					if ($varStatus_rsMaintenanceNotifs) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="rfas.php" method="get" name="filterStatus" id="filterStatus">
							Display <select name="status" id="status">
								<option value="">Select Status</option>
								<?php
								$select_selected = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
								$helper_select = array(
									 'All' => 'All',
									 'Pending Approval' => 'Pending Approval',
									 'Pre-approved' => 'Pre-approved',
									 'Approved' => 'Approved',
									 'Declined' => 'Declined',
									 'Returned' => 'Returned',
									 'Submitted for CAB Approval' => 'Submitted for CAB Approval',
									 'Approved by CAB' => 'Approved by CAB',
									 'Rejected by CAB' => 'Rejected by CAB',
									 'Returned by CAB' => 'Returned by CAB',
									 'Completed' => 'Completed',
									 'Resolved' => 'Resolved',
								);
								foreach ($helper_select as $key => $value) {
									echo "<option value='$key' " . (($select_selected == $key) ? "selected='selected'" : '') . ">$value</option>";
								}
								?>
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
					<thead>
						<tr>
							<th>Date<br />Submitted</th>
							<th>Submitted By</th>
							<th>Summary</th>
							<th>App</th>
							<th>Status</th>
							<th>Window</th>
							<?php sudoAuthData(null, null, "th", "edit", null); ?>
						</tr>
					</thead>
					<tbody>
						<?php
						while ($row_rsChangeRequests = $rsChangeRequests->fetch_assoc()) {
							?>
							<tr>
								<td><?php echo $row_rsChangeRequests['dateSubmitted']; ?></td>
								<td><?php echo $row_rsChangeRequests['displayName']; ?></td>
								<td><?php echo "<a href=\"rfa.php?function=view&amp;rfa=" . $row_rsChangeRequests['changeRequestID'] . "\">" . $row_rsChangeRequests['summary'] . "</a>"; ?></td>
								<td><?php echo $row_rsChangeRequests['application']; ?></td>
								<td><?php echo $row_rsChangeRequests['status']; ?></td>
								<td><?php echo $row_rsChangeRequests['windowStartDate']; ?></td>
								<?php sudoAuthData("rfa.php", "Update RFA", "td", "edit", "function=update&amp;rfa=" . $row_rsChangeRequests['changeRequestID']); ?>
							</tr>
						<?php } ?>
					</tbody>
				</table>

				<div id="count">Viewing <?php echo ($startRow_rsChangeRequests + 1) ?> through <?php echo min($startRow_rsChangeRequests + $maxRows_rsChangeRequests, $totalRows_rsChangeRequests) ?> of <?php echo $totalRows_rsChangeRequests ?> Change Requests</div>
				<?php if ($totalRows_rsChangeRequests > 25) { ?>
					<table class="pagination" width="50%" align="center">
						<tr>
							<td width="23%" align="center"><?php if ($pageNum_rsChangeRequests > 0) { // Show if not first page           ?>
									<a href="<?php printf("%s?pageNum_rsChangeRequests=%d%s", $currentPage, 0, $queryString_rsChangeRequests); ?>"><img src="../images/icons/first.jpg" alt="First" /></a>
								<?php } // Show if not first page       ?>
							</td>
							<td width="31%" align="center"><?php if ($pageNum_rsChangeRequests > 0) { // Show if not first page           ?>
									<a href="<?php printf("%s?pageNum_rsChangeRequests=%d%s", $currentPage, max(0, $pageNum_rsChangeRequests - 1), $queryString_rsChangeRequests); ?>"><img src="../images/icons/prev.jpg" alt="Previous" /></a>
								<?php } // Show if not first page       ?>
							</td>
							<td width="23%" align="center"><?php if ($pageNum_rsChangeRequests < $totalPages_rsChangeRequests) { // Show if not last page           ?>
									<a href="<?php printf("%s?pageNum_rsChangeRequests=%d%s", $currentPage, min($totalPages_rsChangeRequests, $pageNum_rsChangeRequests + 1), $queryString_rsChangeRequests); ?>"><img src="../images/icons/next.jpg" alt="Next" /></a>
								<?php } // Show if not last page       ?>
							</td>
							<td width="23%" align="center"><?php if ($pageNum_rsChangeRequests < $totalPages_rsChangeRequests) { // Show if not last page           ?>
									<a href="<?php printf("%s?pageNum_rsChangeRequests=%d%s", $currentPage, $totalPages_rsChangeRequests, $queryString_rsChangeRequests); ?>"><img src="../images/icons/final.jpg" alt="Final" /></a>
								<?php } // Show if not last page       ?>
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

