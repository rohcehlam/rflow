<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');

session_start();
// ** Logout the current user. **
$logoutAction = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS) . "?doLogout=true";
if (filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_SPECIAL_CHARS)) {
	$logoutAction .="&" . htmlentities(filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_SPECIAL_CHARS));
}

//if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
if ((filter_input(INPUT_GET, 'doLogout', FILTER_SANITIZE_SPECIAL_CHARS) == "true")) {
//to fully log out a visitor we need to clear the session varialbles
	unset($_SESSION['MM_Username']);
	unset($_SESSION['MM_UserGroup']);

	$logoutGoTo = "index.php?loggedoff=y";
	if ($logoutGoTo) {
		header("Location: $logoutGoTo");
		exit;
	}
}

$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
// For security, start by assuming the visitor is NOT authorized.
	$isValid = False;

// When a visitor has logged into this site, the Session variable MM_Username set equal to their username.
// Therefore, we know that a user is NOT logged in if that Session variable is blank.
	if (!empty($UserName)) {
// Besides being logged in, you may restrict access to only certain users based on an ID established when they login.
// Parse the strings into arrays.
		$arrUsers = Explode(",", $strUsers);
		$arrGroups = Explode(",", $strGroups);
		if (in_array($UserName, $arrUsers)) {
			$isValid = true;
		}
// Or, you may restrict access to only certain users based on their username.
		if (in_array($UserGroup, $arrGroups)) {
			$isValid = true;
		}
		if (($strUsers == "") && true) {
			$isValid = true;
		}
	}
	return $isValid;
}

$MM_restrictGoTo = "index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
	$MM_qsChar = "?";
	//$MM_referrer = $_SERVER['PHP_SELF'];
	$MM_referrer = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS);
	if (strpos($MM_restrictGoTo, "?")) {
		$MM_qsChar = "&";
	}
	if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) {
		$MM_referrer .= "?" . $QUERY_STRING;
	}
	$MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
	header("Location: " . $MM_restrictGoTo);
	exit;
}

//my projects
$varEmployee_rsMyProjects = "1";
if (isset($_SESSION['employee'])) {
	$varEmployee_rsMyProjects = (get_magic_quotes_gpc()) ? $_SESSION['employee'] : addslashes($_SESSION['employee']);
}
$query_rsMyProjects = sprintf("SELECT projectID, projectName, status, applications.application, customers.customer, organizingEngineerID, DATE_FORMAT(targetDate, '%%m/%%d/%%Y') as targetDate, wrm, ticket FROM projects LEFT JOIN applications ON projects.applicationID=applications.applicationID LEFT JOIN customers ON projects.primaryCustomerID=customers.customerID WHERE organizingEngineerID = %s AND status <> 'Completed' ORDER BY targetDate ASC", $varEmployee_rsMyProjects);
$rsMyProjects = $conn->query($query_rsMyProjects);
$row_rsMyProjects = $rsMyProjects->fetch_assoc();
$totalRows_rsMyProjects = $rsMyProjects->num_rows;

//my support requests
$varEmployee_rsSupportRequests = "1";
if (isset($_SESSION['employee'])) {
	$varEmployee_rsSupportRequests = (get_magic_quotes_gpc()) ? $_SESSION['employee'] : addslashes($_SESSION['employee']);
}
$query_rsSupportRequests = sprintf("SELECT escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, applications.application, subject, assignedTo, status, ticket, customers.customer, DATE_FORMAT(targetDate, '%%m/%%d/%%Y') as targetDate FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN customers ON escalations.customerID=customers.customerID WHERE assignedTo = %s AND status <> 'Closed' AND status <> 'Returned' ORDER BY targetDate ASC", $varEmployee_rsSupportRequests);
$rsSupportRequests = $conn->query($query_rsSupportRequests);
$row_rsSupportRequests = $rsSupportRequests->fetch_assoc();
$totalRows_rsSupportRequests = $rsSupportRequests->num_rows;

//pending maintenances
$query_rsPendingMaintenances = "SELECT maintenanceNotifsID, reason, TIME_FORMAT(startTime,'%k:%i') AS startTime, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate FROM maintenancenotifs WHERE status = 'Open' OR status='Extended' ORDER BY startDate DESC, startTime DESC";
$rsPendingMaintenances = $conn->query($query_rsPendingMaintenances);
$row_rsPendingMaintenances = $rsPendingMaintenances->fetch_assoc();
$totalRows_rsPendingMaintenances = $rsPendingMaintenances->num_rows;

$colname_rsEmployeeInfo = "1";
if (isset($_SESSION['employee'])) {
	$colname_rsEmployeeInfo = (get_magic_quotes_gpc()) ? $_SESSION['employee'] : addslashes($_SESSION['employee']);
}
$query_rsEmployeeInfo = sprintf("SELECT employeeID, firstName, displayName FROM employees WHERE employeeID = %s", $colname_rsEmployeeInfo);
$rsEmployeeInfo = $conn->query($query_rsEmployeeInfo);
$row_rsEmployeeInfo = $rsEmployeeInfo->fetch_assoc();
$totalRows_rsEmployeeInfo = $rsEmployeeInfo->num_rows;

//unassigned support requests
$query_rsUnassignedSupportRequests = "SELECT escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, applications.application, subject, assignedTo, status, ticket, customers.customer, DATE_FORMAT(targetDate, '%m/%d/%Y') as targetDate FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN customers ON escalations.customerID=customers.customerID WHERE assignedTo='48' AND status <> 'Closed' AND status <> 'Returned' ORDER BY targetDate ASC";
$rsUnassignedSupportRequests = $conn->query($query_rsUnassignedSupportRequests);
$row_rsUnassignedSupportRequests = $rsUnassignedSupportRequests->fetch_assoc();
$totalRows_rsUnassignedSupportRequests = $rsUnassignedSupportRequests->num_rows;

//my project tasks
$varEngineer_rsMyProjectTasks = "1";
if (isset($_SESSION['employee'])) {
	$varEngineer_rsMyProjectTasks = (get_magic_quotes_gpc()) ? $_SESSION['employee'] : addslashes($_SESSION['employee']);
}
$query_rsMyProjectTasks = sprintf("SELECT projectevents.projectEventID, projectevents.projectEvent, projectevents.projectID, projects.projectName, projectevents.order, projectevents.engineerID, employees.displayName, DATE_FORMAT(projectevents.targetDate, '%%m/%%d/%%Y') as targetDate, projectevents.status FROM projectevents LEFT JOIN projects ON projects.projectID=projectevents.projectID LEFT JOIN employees ON employees.employeeID=projectevents.engineerID WHERE projectevents.engineerID = '%s' AND projectevents.status <> 'Complete' ORDER BY projectevents.projectID ASC, projectevents.order", $varEngineer_rsMyProjectTasks);
$rsMyProjectTasks = $conn->query($query_rsMyProjectTasks);
$row_rsMyProjectTasks = $rsMyProjectTasks->fetch_assoc();
$totalRows_rsMyProjectTasks = $rsMyProjectTasks->num_rows;
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php buildTitle("My Portal"); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--
        <link href="userPortal.css" rel="stylesheet" type="text/css" />
        <link href="../inc/global.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="../inc/js/js.js"></script>
        -->
        <link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />

        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- Font Awesome Icons -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="../bootstrap/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../bootstrap/AdminLTE.min.css" rel="stylesheet" type="text/css" />
        <!-- AdminLTE Skins. Choose a skin from the css/skins 
        folder instead of downloading all of them to reduce the load. -->
        <link href="../bootstrap/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
        <link href="../bootstrap/global_app.css" rel="stylesheet" type="text/css" />
    </head>
    <body class="skin-blue layout-top-nav">
        <div class="wrapper">
            <header class="main-header">
                <nav class="navbar navbar-static-top">
                    <div class="container-fluid">
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                            <div class="navbar-header">
                                <a href="index.php" class="navbar-brand"><img width="100" src="../images/masflight-logo.png"/></a>
                            </div>
                            <ul class="nav navbar-nav">                                
                                <li><a href="../rfas/rfas.php">RFCs</a></li>
                                <li><a href="../statusReports/statusReports.php">Status Reports</a></li>
                                <li><a href="../maintenances/maintenances.php">Maintenance Notifications</a></li>
                                <li><a href="../supportRequests/supportRequests.php">Support Requests</a></li>
                                <li><a href="http://54.144.64.79/masflight_projects/index.php">Projects</a></li>                                
                            </ul>                      
                        </div><!-- /.navbar-collapse -->

                        <!-- Navbar Right Menu -->
                        <div class="navbar-custom-menu">
                            <ul class="nav navbar-nav">
                                <?php
								if (!isset($_SESSION['employee'])) {
									echo "<li>\n<a href=\"index.php\">Login</a>\n</li>\n";
								} else {
									echo "<li><a href='#'>Welcome, {$row_rsEmployeeInfo['firstName']}!</a></li>\n<li><a href=\"$logoutAction\">Logout</a></li>\n";
								}
								?>
                            </ul>
                        </div>
                    </div><!-- /.container-fluid -->
                </nav>
            </header> 
        </div>      


        <div class="content-wrapper">

            <div class="container-fluid">

                <div class="page-header">
                    <h1>My Portal</h1>
                    <p class="lead">Data flow masflight</p>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs pull-right">
                                <li class="active"><a href="#tab_1-1" data-toggle="tab" aria-expanded="true">Requests for my support</a></li>
                                <li class=""><a href="#tab_2-2" data-toggle="tab" aria-expanded="false">Unassigned Support Requests</a></li>
                                <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false">Pending Maintenances</a></li>
                                <li class="pull-left header"><i class="fa fa-th"></i> My Settings</li>
                            </ul>
                            <div class="tab-content">

                                <div class="tab-pane active" id="tab_1-1">
                                    <h4><strong>Requests for my support: </strong></h4><br/>                
                                    <?php if ($totalRows_rsSupportRequests == 0) { ?>
                                        <div align="center"><strong>There are currently no requests for your support</strong></div>
                                    <?php } else { ?>
                                        <table class="showMySettings table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date<br />Requested</th>
                                                    <th>Target<br />Date</th>
                                                    <th>Subject</th>
                                                    <th>Customer</th>
                                                    <th>App</th>
                                                    <th>Ticket</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                while ($row_rsSupportRequests = $rsSupportRequests->fetch_assoc()) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $row_rsSupportRequests['dateEscalated']; ?></td>
                                                        <td><?php echo $row_rsSupportRequests['targetDate']; ?></td>
                                                        <td><a href="../supportRequests/supportRequest.php?supportRequest=<?php echo $row_rsSupportRequests['escalationID']; ?>&amp;function=view"><?php echo stripslashes($row_rsSupportRequests['subject']); ?></a></td>
                                                        <td><?php echo $row_rsSupportRequests['customer']; ?></td>
                                                        <td><?php echo $row_rsSupportRequests['application']; ?></td>
                                                        <td><?php echo $row_rsSupportRequests['application']; ?></td>
                                                        <td><?php echo ($row_rsSupportRequests['ticket'] == "0") ? '-' : $row_rsSupportRequests['ticket']; ?></td>
                                                        <td><?php echo $row_rsSupportRequests['status']; ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                </div><!-- /.tab-pane -->

                                <div class="tab-pane" id="tab_2-2">                
                                    <h4><strong>Unassigned support requests: </strong></h4><br/>
                                    <table class="showMySettings table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date<br />Requested</th>
                                                <th>Target<br />Date</th>
                                                <th>Subject</th>
                                                <th>Customer</th>
                                                <th>App</th>
                                                <th>Ticket</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            while ($row_rsUnassignedSupportRequests = $rsUnassignedSupportRequests->fetch_assoc()) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $row_rsUnassignedSupportRequests['dateEscalated']; ?></td>
                                                    <td><?php echo $row_rsUnassignedSupportRequests['targetDate']; ?></td>
                                                    <td><a href="../supportRequests/supportRequest.php?supportRequest=<?php echo $row_rsUnassignedSupportRequests['escalationID']; ?>&amp;function=view"><?php echo stripslashes($row_rsUnassignedSupportRequests['subject']); ?></a></td>
                                                    <td><?php echo $row_rsUnassignedSupportRequests['customer']; ?></td>
                                                    <td><?php echo $row_rsUnassignedSupportRequests['application']; ?></td>
                                                    <td><?php echo ($row_rsUnassignedSupportRequests['ticket'] == "0") ? '-' : $row_rsUnassignedSupportRequests['ticket']; ?></td>
                                                    <td><?php echo $row_rsUnassignedSupportRequests['status']; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div><!-- /.tab-pane -->

                                <div class="tab-pane" id="tab_3">                
                                    <h4><strong>Pending Maintenances: </strong></h4><br/>
                                    <?php if ($totalRows_rsPendingMaintenances == 0) { ?>
                                        <div align="center"><strong>There are no <em>pending</em> Maintenances</strong></div>
                                    <?php } else { ?>
                                        <table class="showMySettings table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Start<br />Date</th>
                                                    <th>Start<br />Time</th>
                                                    <th>Reason</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                while ($row_rsPendingMaintenances = $rsPendingMaintenances->fetch_assoc()) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $row_rsPendingMaintenances['startDate']; ?></td>
                                                        <td align="right"><?php echo $row_rsPendingMaintenances['startTime']; ?></td>
                                                        <td><a href="../maintenances/maintenance.php?maintenance=<?php echo $row_rsPendingMaintenances['maintenanceNotifsID']; ?>&amp;function=view"><?php echo $row_rsPendingMaintenances['reason']; ?></a></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                </div><!-- /.tab-pane -->

                            </div><!-- /.tab-content -->
                        </div>
                    </div>
                </div>



            </div> <!-- /container -->
        </div> <!-- /content-wrapper -->

        <footer class="footer">
            <div class="container">
                <p class="text-muted" style="text-align: center;">&copy; 2015 Marks Systems Inc, - masFlight<br/>Technical Support. Version 2.0</p>
            </div>
        </footer>

        <!-- jQuery 2.1.4 -->
        <script src="../bootstrap/jQuery/jQuery-2.1.4.min.js"></script>
        <!-- jQuery UI 1.11.2 -->
        <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>
        <!-- Bootstrap 3.3.2 JS -->
        <script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>    

        <!-- DATA TABES SCRIPT -->
        <script src="../bootstrap/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="../bootstrap/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>

        <!-- AdminLTE App -->
        <script src="../bootstrap/app.min.js" type="text/javascript"></script>

        <!-- Global App JS -->
        <script src="../bootstrap/global_app.js" type="text/javascript"></script>

    </body>
</html>

