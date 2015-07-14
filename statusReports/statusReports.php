<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	 'pageNum_rsStatusReports' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'app' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'customer' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'employee' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'reporttype' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'subject' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'ticket' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'wrm' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'sortBy' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'sortOrder' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'totalRows_rsStatusReports' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);
$my_server = filter_input_array(INPUT_SERVER, [
	 'PHP_SELF' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'QUERY_STRING' => FILTER_SANITIZE_SPECIAL_CHARS,
		  ]);

$currentPage = $my_server["PHP_SELF"];

$maxRows_rsStatusReports = 45;
$pageNum_rsStatusReports = 0;
if (isset($my_get['pageNum_rsStatusReports'])) {
	$pageNum_rsStatusReports = $my_get['pageNum_rsStatusReports'];
}
$startRow_rsStatusReports = $pageNum_rsStatusReports * $maxRows_rsStatusReports;

//define various queries for each filter type
if (isset($my_get['app'])) {
	if (!isset($my_get['sortBy'])) {
		$query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.applicationID=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varApp_rsStatusReports);
	} else {
		$query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.applicationID=%s ORDER BY %ss.%s %s", $varApp_rsStatusReports, $varSortBy_rsStatusReports, $varSortBy_rsStatusReports, $varSortOrder_rsStatusReports);
	}
} elseif (isset($my_get['customer'])) {
	$query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.customerID=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varCarrier_rsStatusReports);
} elseif (isset($my_get['employee'])) {
	$query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.employeeID=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varEmployee_rsStatusReports);
} elseif (isset($my_get['reporttype'])) {
	$query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.reportTypeID=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varReportType_rsStatusReports);
} elseif (isset($my_get['subject'])) {
	$query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.subject REGEXP '%s' ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varSubject_rsStatusReports);
} elseif (isset($my_get['ticket'])) {
	$query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.magicTicket=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varTicket_rsStatusReports);
} elseif (isset($my_get['wrm'])) {
	$query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.wrm=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varWRM_rsStatusReports);
} else {
	$query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID ORDER BY statusreports.endDate DESC, statusreports.endTime DESC");
}
$query_limit_rsStatusReports = sprintf("%s LIMIT %d, %d", $query_rsStatusReports, $startRow_rsStatusReports, $maxRows_rsStatusReports);
$rsStatusReports = $conn->query($query_limit_rsStatusReports);
$row_rsStatusReports = $rsStatusReports->fetch_assoc();

if (isset($my_get['totalRows_rsStatusReports'])) {
	$totalRows_rsStatusReports = $my_get['totalRows_rsStatusReports'];
} else {
	$all_rsStatusReports = $conn->query($query_rsStatusReports);
	$totalRows_rsStatusReports = $all_rsStatusReports->num_rows;
}
$totalPages_rsStatusReports = ceil($totalRows_rsStatusReports / $maxRows_rsStatusReports) - 1;

//$queryString_rsStatusReports = "";
if (!empty($my_server['QUERY_STRING'])) {
	$params = explode("&", $my_server['QUERY_STRING']);
	$newParams = array();
	foreach ($params as $param) {
		if (stristr($param, "pageNum_rsStatusReports") == false &&
				  stristr($param, "totalRows_rsStatusReports") == false) {
			array_push($newParams, $param);
		}
	}
	if (count($newParams) != 0) {
		$queryString_rsStatusReports = "&amp;" . htmlentities(implode("&", $newParams));
	}
}
$queryString_rsStatusReports = sprintf("&amp;totalRows_rsStatusReports=%d%s", $totalRows_rsStatusReports, $queryString_rsStatusReports);

//select applications for application filter list
$query_rsApps = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApps = $conn->query($query_rsApps);
$row_rsApps = $rsApps->fetch_assoc();
$totalRows_rsApps = $rsApps->num_rows;

//select customers for customer filter list
$query_rsCarrier = "SELECT customerID, customer FROM customers ORDER BY customer ASC";
$rsCarrier = $conn->query($query_rsCarrier);
$row_rsCarrier = $rsCarrier->fetch_assoc();
$totalRows_rsCarrier = $rsCarrier->num_rows;

//select employees for employee filter list
$query_rsEngineers = "SELECT employeeID, displayName FROM employees WHERE employees.engineer ='y' ORDER BY displayName ASC";
$rsEngineers = $conn->query($query_rsEngineers);
$row_rsEngineers = $rsEngineers->fetch_assoc();
$totalRows_rsEngineers = $rsEngineers->num_rows;

//select report types for report type filter list
$query_rsReportTypes = "SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC";
$rsReportTypes = $conn->query($query_rsReportTypes);
$row_rsReportTypes = $rsReportTypes->fetch_assoc();
$totalRows_rsReportTypes = $rsReportTypes->num_rows;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php buildTitle("Status Reports"); ?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link href="../inc/global.css" rel="stylesheet" type="text/css" />
		<link href="../inc/menu.css" rel="stylesheet" type="text/css" />	
		<script type="text/javascript" src="../inc/js/menu.js"></script>
		<script type="text/javascript" src="../inc/js/js.js"></script>
		<script type="text/javascript" src="../inc/js/statusReportsTabs.js"></script>
	</head>
	<body>
		<?php buildMenu(); ?>
		<script type="text/javascript">
			dolphintabs.init("menunav", 3);
		</script>
		<!-- <iframe src="statusReportActions.php" scrolling="no" style="float:right; width:19.5%;" frameborder="0" height="100%"></iframe> -->
		<div class="casing" align="left">
			<?php buildHeader("statusReport", null, "statusReports", "Status Reports", "Add a Status Report"); ?>
			<!-- TABS -->
			<div id="tabs">
				<span class="<?php
				if ((isset($my_get['app'])) || (isset($my_get['customer'])) || (isset($my_get['employee'])) || (isset($my_get['reporttype'])) || (isset($my_get['subject'])) || (isset($my_get['ticket'])) || (isset($my_get['wrm']))) {
					echo "tabbak";
				} else {
					echo "tabfor";
				}
				?>" id="tab_none"><a href="statusReports.php?filter=none">No Filter</a>
				</span>
				<?php tab("app", "Application"); ?>
				<?php tab("customer", "Customer"); ?>
				<?php tab("engineer", "Engineer"); ?>
				<?php tab("reporttype", "Report Type"); ?>
				<?php tab("subject", "Subject"); ?>
				<?php tab("ticket", "Ticket"); ?>
				<?php tab("wrm", "Case"); ?>
				<!-- TABS BODY -->
				<div id="tabscontent">
					<!-- DETAILS -->
					<a name="tabnone" id="tabnone"></a>
					<div id="tabscontent_none"<?php
					if ((isset($my_get['app'])) || (isset($my_get['customer'])) || (isset($my_get['employee'])) || (isset($my_get['reporttype'])) || (isset($my_get['subject'])) || (isset($my_get['ticket'])) || (isset($my_get['wrm']))) {
						echo " style=\"display: none;\"";
					}
					?>>Select a tab to filter the available Status Reports</div>
					<a name="tabapp" id="tabapp"></a>
					<div id="tabscontent_app"<?php
					if (!isset($my_get['app'])) {
						echo " style=\"display: none;\"";
					}
					?>>
						<form action="statusReports.php" method="get" name="filterApp" id="filterApp">
							Display Status Reports for
							<select name="app" id="app">
								<option value="">Select Application</option>
								<?php
								do {
									?>
									<option value="<?php echo $row_rsApps['applicationID'] ?>"<?php
								if (isset($my_get['app']) && ($row_rsApps['applicationID'] == $my_get['app'])) {
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
					<a name="tabcustomer" id="tabcustomer"></a>
					<div id="tabscontent_customer"<?php
										  if (!isset($my_get['customer'])) {
											  echo " style=\"display: none;\"";
										  }
										  ?>>
						<form action="statusReports.php" method="get" name="filterCustomer" id="filterCustomer">
							Display Status Reports for <select name="customer" id="customer">
								<option value="">Select Customer</option>
								<?php do { ?>
									<option value="<?php echo $row_rsCarrier['customerID'] ?>"<?php
								if (isset($my_get['customer']) && ($row_rsCarrier['customerID'] == $my_get['customer'])) {
									echo " selected=\"selected\"";
								}
									?>><?php echo $row_rsCarrier['customer'] ?></option>
											  <?php
										  } while ($row_rsCarrier = $rsCarrier->fetch_assoc());
										  if ($rsCarrier->num_rows > 0) {
											  $rsCarrier->data_seek(0);
											  $row_rsCarrier = $rsCarrier->fetch_assoc();
										  }
										  ?>
							</select>
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>
					<a name="tabengineer" id="tabengineer"></a>
					<div id="tabscontent_engineer"<?php
										  if (!isset($my_get['employee'])) {
											  echo " style=\"display: none;\"";
										  }
										  ?>>
						<form action="statusReports.php" method="get" name="filterEngineer" id="filterEngineer">
							Display Status Reports by <select name="employee" id="employee">
								<option value="">Select Engineer</option>
								<?php do { ?>
									<option value="<?php echo $row_rsEngineers['employeeID'] ?>"<?php
								if (isset($my_get['employee']) && ($row_rsEngineers['employeeID'] == $my_get['employee'])) {
									echo " selected=\"selected\"";
								}
									?>><?php echo $row_rsEngineers['displayName'] ?></option>
											  <?php
										  } while ($row_rsEngineers = $rsEngineers->fetch_assoc());
										  if ($rsEngineers->num_rows > 0) {
											  $rsEngineers->data_seek(0);
											  $row_rsEngineers = $rsEngineers->fetch_assoc();
										  }
										  ?>
							</select>
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>
					<a name="tabreporttype" id="tabreporttype"></a>
					<div id="tabscontent_reporttype"<?php
										  if (!isset($my_get['reporttype'])) {
											  echo " style=\"display: none;\"";
										  }
										  ?>>
						<form action="statusReports.php" method="get" name="filterReportType" id="filterReportType">
							Display 
							<select name="reporttype" id="reporttype">
								<option value="">Select Report Type</option>
								<?php do { ?>
									<option value="<?php echo $row_rsReportTypes['reportTypeID'] ?>"<?php
								if (isset($my_get['reporttype']) && ($row_rsReportTypes['reportTypeID'] == $my_get['reporttype'])) {
									echo " selected=\"selected\"";
								}
									?>><?php echo $row_rsReportTypes['reportType'] ?></option>
											  <?php
										  } while ($row_rsReportTypes = $rsReportTypes->fetch_assoc());
										  if ($rsReportTypes->num_rows > 0) {
											  $rsReportTypes->data_seek(0);
											  $row_rsReportTypes = $rsReportTypes->fetch_assoc();
										  }
										  ?>
							</select> Status Reports
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>
					<a name="tabsubject" id="tabsubject"></a>
					<div id="tabscontent_subject"<?php
										  if (!isset($my_get['subject'])) {
											  echo " style=\"display: none;\"";
										  }
										  ?>>
						<form action="statusReports.php" method="get" name="filterSubject" id="filterSubject">
							Display Status Reports with the Subject containing <input type="text" name="subject" id="subject"<?php
					if (isset($my_get['subject'])) {
						echo " value=\"" . $my_get['subject'] . "\"";
					}
										  ?> size="20" />
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>
					<a name="tabticket" id="tabticket"></a>
					<div id="tabscontent_ticket"<?php
							if (!isset($my_get['ticket'])) {
								echo " style=\"display: none;\"";
							}
										  ?>>
						<form action="statusReports.php" method="get" name="filterTicket" id="filterTicket">
							Display Status Reports for Ticket # <input type="text" name="ticket" id="ticket"<?php
					if (isset($my_get['ticket'])) {
						echo " value=\"" . $my_get['ticket'] . "\"";
					}
										  ?> size="10" />
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>
					<a name="tabwrm" id="tabwrm"></a>
					<div id="tabscontent_wrm"<?php
							if (!isset($my_get['wrm'])) {
								echo " style=\"display: none;\"";
							}
										  ?>>
						<form action="statusReports.php" method="get" name="filterWRM" id="filterWRM">
							Display Status Reports for Case # <input type="text" name="wrm" id="wrm"<?php
					if (isset($my_get['wrm'])) {
						echo " value=\"" . $my_get['wrm'] . "\"";
					}
										  ?> size="10" />
							<input type="submit" name="Submit" value="Submit" />
						</form>
					</div>
				</div>
			</div><br />

			<?php
			//Start filter headers
			if (isset($my_get['app'])) {
				echo "<h3>Status Reports for <em>" . $row_rsStatusReports['application'] . "</em></h3>\n";
			} elseif (isset($my_get['customer'])) {
				echo "<h3>Status Reports for <em>" . $row_rsStatusReports['customer'] . "</em></h3>\n";
			} elseif (isset($my_get['employee'])) {
				echo "<h3>Status Reports by <em>" . $row_rsStatusReports['displayName'] . "</em></h3>\n";
			} elseif (isset($my_get['reporttype'])) {
				echo "<h3><em>" . $row_rsStatusReports['reportType'] . "</em> Status Reports</h3>\n";
			} elseif (isset($my_get['subject'])) {
				echo "<h3>Status Reports with the Subject containing <em>" . $my_get['subject'] . "</em></h3>\n";
			} elseif (isset($my_get['ticket'])) {
				echo "<h3>Status Reports with <em>Ticket #" . $row_rsStatusReports['magicTicket'] . "</em></h3>\n";
			} elseif (isset($my_get['wrm'])) {
				echo "<h3>Status Reports for <em>Case #" . $row_rsStatusReports['wrm'] . "</em></h3>\n";
			}
			?>
			<table class="data" align="center" cellpadding="2" cellspacing="0">
				<tr>
					<th>Date</th>
					<th>ID</th>
					<th>Subject</th>
					<?php
					if (!isset($my_get['app'])) {
						echo "          <th>App</th>\n";
					}
					if (!isset($my_get['customer'])) {
						echo "          <th>Customer</th>\n";
					}
					if (!isset($my_get['employee'])) {
						echo "          <th>Engineer</th>\n";
					}
					if (!isset($my_get['reporttype'])) {
						echo "          <th>Report Type</th>\n";
					}
					if (!isset($my_get['ticket'])) {
						echo "          <th>Ticket</th>\n";
					}
					if (!isset($my_get['wrm'])) {
						echo "          <th>Case</th>\n";
					}
					//	sudoAuthData(null, null, "th", null, null); 
					?>
				</tr>
				<?php
				$num = 0;
				while ($row_rsStatusReports = $rsStatusReports->fetch_assoc()) {
					$num++;
					echo "<tr";
					if ($num % 2) {
						echo " class=\"odd\"";
					}
					echo ">\n";
					?>
					<td><?php echo $row_rsStatusReports['endDate']; ?></td>
					<td><a title="View this Status Report" href="statusReport.php?function=view&amp;statusReport=<?php echo $row_rsStatusReports['statusReportID']; ?><?php
				if (isset($my_get['corp'])) {
					echo "&amp;corp=y";
				}
					?>"><?php echo $row_rsStatusReports['statusReportID']; ?></a></td>
					<td><a title="View this Status Report" href="statusReport.php?function=view&amp;statusReport=<?php echo $row_rsStatusReports['statusReportID']; ?><?php
						 if (isset($my_get['corp'])) {
							 echo "&amp;corp=y";
						 }
					?>"><?php echo stripslashes($row_rsStatusReports['subject']); ?></a></td>
							 <?php
//App column
						if (!isset($my_get['app'])) {
							echo "          <td>" . $row_rsStatusReports['application'] . "</td>\n";
						}
//Carrier column
						if (!isset($my_get['customer'])) {
							echo "          <td>" . $row_rsStatusReports['customer'] . "</td>\n";
						}
//Employee column
						if (!isset($my_get['employee'])) {
							echo "          <td>" . $row_rsStatusReports['displayName'] . "</td>\n";
						}
//Report Type column
						if (!isset($my_get['reporttype'])) {
							echo "          <td>" . $row_rsStatusReports['reportType'] . "</td>\n";
						}
//Ticket column
						if (!isset($my_get['ticket'])) {
							echo "          <td>";
							if ($row_rsStatusReports['magicTicket'] == "0") {
								echo "-";
							} else {
								echo $row_rsStatusReports['magicTicket'];
							}
							echo "</td>\n";
						}
//WRM column
						if (!isset($my_get['wrm'])) {
							echo "          <td>";
							if ($row_rsStatusReports['wrm'] == "0") {
								echo "-";
							} else {
								echo $row_rsStatusReports['wrm'];
							}
							echo "</td>\n";
						}

						//	sudoAuthData("statusReportUpdate", "Update Status Report", "td", "edit", "statusReport=" . $row_rsStatusReports['statusReportID']); 
						?>
					</tr>
				<?php } ?>
			</table>
			<div id="count">Viewing <?php echo ($startRow_rsStatusReports + 1) ?> through <?php echo min($startRow_rsStatusReports + $maxRows_rsStatusReports, $totalRows_rsStatusReports) ?> of <?php echo $totalRows_rsStatusReports ?> Status Reports</div>
			<?php if ($totalRows_rsStatusReports > 45) { ?>
				<table class="pagination" align="center">
					<tr>
						<td width="23%" align="center"><?php if ($pageNum_rsStatusReports > 0) { // Show if not first page 
					?><a href="<?php printf("%s?pageNum_rsStatusReports=%d%s", $currentPage, 0, $queryString_rsStatusReports); ?>"><img src="../images/icons/first.jpg" alt="First Page" /></a><?php } // Show if not first page 
				?></td>
						<td width="31%" align="center"><?php if ($pageNum_rsStatusReports > 0) { // Show if not first page 
							?><a href="<?php printf("%s?pageNum_rsStatusReports=%d%s", $currentPage, max(0, $pageNum_rsStatusReports - 1), $queryString_rsStatusReports); ?>"><img src="../images/icons/prev.jpg" alt="Previous" /></a><?php } // Show if not first page 
				?></td>
						<td width="23%" align="center"><?php if ($pageNum_rsStatusReports < $totalPages_rsStatusReports) { // Show if not last page 
							?><a href="<?php printf("%s?pageNum_rsStatusReports=%d%s", $currentPage, min($totalPages_rsStatusReports, $pageNum_rsStatusReports + 1), $queryString_rsStatusReports); ?>"><img src="../images/icons/next.jpg" alt="Next" /></a><?php } // Show if not last page 
				?></td>
						<td width="23%" align="center"><?php if ($pageNum_rsStatusReports < $totalPages_rsStatusReports) { // Show if not last page 
							?><a href="<?php printf("%s?pageNum_rsStatusReports=%d%s", $currentPage, $totalPages_rsStatusReports, $queryString_rsStatusReports); ?>"><img src="../images/icons/final.jpg" alt="Last page" /></a><?php } // Show if not last page 
				?></td>
					</tr>
				</table>
			<?php } ?>
			<?php buildFooter("0"); ?>
		</div>
	</body>
</html>