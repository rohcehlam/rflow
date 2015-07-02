<?php require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');
?><?php
session_start();

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
    $logoutAction .="&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
    //to fully log out a visitor we need to clear the session varialbles
    unset($_SESSION['MM_Username']);
    unset($_SESSION['MM_UserGroup']);

    $logoutGoTo = "index.php?loggedoff=y";
    if ($logoutGoTo) {
        header("Location: $logoutGoTo");
        exit;
    }
}
?><?php
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
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?"))
        $MM_qsChar = "&";
    if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0)
        $MM_referrer .= "?" . $QUERY_STRING;
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo);
    exit;
}
?><?php
//my projects
$varEmployee_rsMyProjects = "1";
if (isset($_SESSION['employee'])) {
    $varEmployee_rsMyProjects = (get_magic_quotes_gpc()) ? $_SESSION['employee'] : addslashes($_SESSION['employee']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsMyProjects = sprintf("SELECT projectID, projectName, status, applications.application, customers.customer, organizingEngineerID, DATE_FORMAT(targetDate, '%%m/%%d/%%Y') as targetDate, wrm, ticket FROM projects LEFT JOIN applications ON projects.applicationID=applications.applicationID LEFT JOIN customers ON projects.primaryCustomerID=customers.customerID WHERE organizingEngineerID = %s AND status <> 'Completed' ORDER BY targetDate ASC", $varEmployee_rsMyProjects);
$rsMyProjects = mysql_query($query_rsMyProjects, $connProdOps) or die(mysql_error());
$row_rsMyProjects = mysql_fetch_assoc($rsMyProjects);
$totalRows_rsMyProjects = mysql_num_rows($rsMyProjects);

//my support requests
$varEmployee_rsSupportRequests = "1";
if (isset($_SESSION['employee'])) {
    $varEmployee_rsSupportRequests = (get_magic_quotes_gpc()) ? $_SESSION['employee'] : addslashes($_SESSION['employee']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsSupportRequests = sprintf("SELECT escalationID, DATE_FORMAT(dateEscalated, '%%m/%%d/%%Y') as dateEscalated, applications.application, subject, assignedTo, status, ticket, customers.customer, DATE_FORMAT(targetDate, '%%m/%%d/%%Y') as targetDate FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN customers ON escalations.customerID=customers.customerID WHERE assignedTo = %s AND status <> 'Closed' AND status <> 'Returned' ORDER BY targetDate ASC", $varEmployee_rsSupportRequests);
$rsSupportRequests = mysql_query($query_rsSupportRequests, $connProdOps) or die(mysql_error());
$row_rsSupportRequests = mysql_fetch_assoc($rsSupportRequests);
$totalRows_rsSupportRequests = mysql_num_rows($rsSupportRequests);

//pending maintenances
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsPendingMaintenances = "SELECT maintenanceNotifsID, reason, TIME_FORMAT(startTime,'%k:%i') AS startTime, DATE_FORMAT(startDate, '%m/%d/%Y') as startDate FROM maintenancenotifs WHERE status = 'Open' OR status='Extended' ORDER BY startDate DESC, startTime DESC";
$rsPendingMaintenances = mysql_query($query_rsPendingMaintenances, $connProdOps) or die(mysql_error());
$row_rsPendingMaintenances = mysql_fetch_assoc($rsPendingMaintenances);
$totalRows_rsPendingMaintenances = mysql_num_rows($rsPendingMaintenances);

$colname_rsEmployeeInfo = "1";
if (isset($_SESSION['employee'])) {
    $colname_rsEmployeeInfo = (get_magic_quotes_gpc()) ? $_SESSION['employee'] : addslashes($_SESSION['employee']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEmployeeInfo = sprintf("SELECT employeeID, firstName, displayName FROM employees WHERE employeeID = %s", $colname_rsEmployeeInfo);
$rsEmployeeInfo = mysql_query($query_rsEmployeeInfo, $connProdOps) or die(mysql_error());
$row_rsEmployeeInfo = mysql_fetch_assoc($rsEmployeeInfo);
$totalRows_rsEmployeeInfo = mysql_num_rows($rsEmployeeInfo);

//unassigned support requests
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsUnassignedSupportRequests = "SELECT escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') as dateEscalated, applications.application, subject, assignedTo, status, ticket, customers.customer, DATE_FORMAT(targetDate, '%m/%d/%Y') as targetDate FROM escalations LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN customers ON escalations.customerID=customers.customerID WHERE assignedTo='48' AND status <> 'Closed' AND status <> 'Returned' ORDER BY targetDate ASC";
$rsUnassignedSupportRequests = mysql_query($query_rsUnassignedSupportRequests, $connProdOps) or die(mysql_error());
$row_rsUnassignedSupportRequests = mysql_fetch_assoc($rsUnassignedSupportRequests);
$totalRows_rsUnassignedSupportRequests = mysql_num_rows($rsUnassignedSupportRequests);

//my project tasks
$varEngineer_rsMyProjectTasks = "1";
if (isset($_SESSION['employee'])) {
    $varEngineer_rsMyProjectTasks = (get_magic_quotes_gpc()) ? $_SESSION['employee'] : addslashes($_SESSION['employee']);
}
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsMyProjectTasks = sprintf("SELECT projectevents.projectEventID, projectevents.projectEvent, projectevents.projectID, projects.projectName, projectevents.order, projectevents.engineerID, employees.displayName, DATE_FORMAT(projectevents.targetDate, '%%m/%%d/%%Y') as targetDate, projectevents.status FROM projectevents LEFT JOIN projects ON projects.projectID=projectevents.projectID LEFT JOIN employees ON employees.employeeID=projectevents.engineerID WHERE projectevents.engineerID = '%s' AND projectevents.status <> 'Complete' ORDER BY projectevents.projectID ASC, projectevents.order", $varEngineer_rsMyProjectTasks);
$rsMyProjectTasks = mysql_query($query_rsMyProjectTasks, $connProdOps) or die(mysql_error());
$row_rsMyProjectTasks = mysql_fetch_assoc($rsMyProjectTasks);
$totalRows_rsMyProjectTasks = mysql_num_rows($rsMyProjectTasks);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php buildTitle("My Portal"); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="userPortal.css" rel="stylesheet" type="text/css" />
        <link href="../inc/global.css" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
        <script type="text/javascript" src="../inc/js/js.js"></script>
    </head>
    <body>
        <div style="background-color:#2F3F4C; text-align: center;"><img src="../images/masflight-logo.png" /></div><br />
        <div id="myportalalt">
            <div class="module">
                <div class="app" style="background-color: #FF3300;"><a style="color: #FFFFFF; text-decoration: none;" title="View Pending Maintenances" href="../n3xt/maintenances/maintenances.php">Pending Maintenances</a></div>
                <div class="modulecontents">
                    <?php if ($totalRows_rsPendingMaintenances == 0) { ?>
                        <div align="center"><strong>There are no <em>pending</em> Maintenances</strong></div>
<?php } else { ?>
                        <table class="pendingMaintenances" width="80%" cellpadding="2" cellspacing="0">
                            <tr>
                                <th width="15%">Start<br />Date</th>
                                <th width="10%">Start<br />Time</th>
                                <th>Reason</th>
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
                                <td><?php echo $row_rsPendingMaintenances['startDate']; ?></td>
                                <td align="right"><?php echo $row_rsPendingMaintenances['startTime']; ?></td>
                                <td><a href="../maintenances/maintenance.php?maintenance=<?php echo $row_rsPendingMaintenances['maintenanceNotifsID']; ?>&amp;function=view"><?php echo $row_rsPendingMaintenances['reason']; ?></a></td>
                                </tr>
                        <?php } while ($row_rsPendingMaintenances = mysql_fetch_assoc($rsPendingMaintenances)); ?>
                        </table>
<?php } ?>
                </div>
            </div>
        </div>
        <div id="myportalmenu">
            <div class="module">
                <div class="app"><?php if (!isset($_SESSION['employee'])) { ?>Login<?php } else { ?>Logout<?php } ?></div>
                <div class="modulecontents"><?php if (!isset($_SESSION['employee'])) { ?><a href="index.php">Login</a>/<?php } else { ?>Welcome, <?php echo $row_rsEmployeeInfo['firstName']; ?>! (<a href="<?php echo $logoutAction ?>">Logout</a>)<?php } ?></div>
            </div>
            <div class="module">
                <div class="app">Main Menu</div>
                <div class="modulecontents"><ul>
                        <li><a href="../maintenances/maintenances.php">Maintenance Notifications</a></li>
                        <li><a href="../statusReports/statusReports.php">Status Reports</a></li>
                        <li><a href="../projects/projects.php">Projects</a></li>
                        <li><a href="../rfas/rfas.php">RFCs</a></li>
                        <li><a href="../supportRequests/supportRequests.php">Support Requests</a></li>
                    </ul>
                </div>
            </div>
            <!--<div class="module">
                    <div class="app">Salesforce.com</div>
                    <div class="modulecontents"><ul>
                            <li><a href="https://na2.salesforce.com/00O400000018Biw">ProdOps' Report</a></li>
                            <li><a href="https://na2.salesforce.com/00O4000000180Tc">Americas' Report</a></li>
                            <li><a href="https://na2.salesforce.com/00O400000018Bih">Cases per Engineer</a></li>
                            <li><a href="https://na2.salesforce.com/00O400000018Bj6">New Cases</a></li>
                    </ul></div>
            </div>-->
        </div>
        <div id="myportalmain">
            <!-- <div class="header">main header</div> -->
            <div class="module">
                <div class="app" style="background-color: #6633FF;"><a style="color: #FFFFFF; text-decoration: none;" title="View My Projects" href="../n3xt/projects/projects.php?engineer=<?php echo $_SESSION['employee']; ?>">My Projects</a></div>
                <div class="modulecontents">
                    <?php if ($totalRows_rsMyProjects == 0) { ?>
                        <div align="center"><strong>You are currently not organizing any Projects.</strong></div>
<?php } else { ?>
                        <table class="projects" width="90%" cellpadding="2" cellspacing="0">
                            <tr>
                                <th width="6%">Target<br />Date</th>
                                <th width="33%">Project Name</th>
                                <th width="19%">Customer</th>
                                <th width="8%">App</th>
                                <th width="7%">Case</th>
                                <th width="7%">Ticket</th>
                                <th width="10%">Status</th>
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
                                <td><?php echo $row_rsMyProjects['targetDate']; ?></td>
                                <td><a href="../projects/project.php?project=<?php echo $row_rsMyProjects['projectID']; ?>&amp;function=view"><?php echo stripslashes($row_rsMyProjects['projectName']); ?></a></td>
                                <td><?php echo $row_rsMyProjects['customer']; ?></td>
                                <td><?php echo $row_rsMyProjects['application']; ?></td>
                                <td><?php
                                    if ($row_rsMyProjects['wrm'] == "0") {
                                        echo "-";
                                    } else {
                                        echo $row_rsMyProjects['wrm'];
                                    }
                                    ?></td>
                                <td><?php
                                    if ($row_rsMyProjects['ticket'] == "0") {
                                        echo "-";
                                    } else {
                                        echo $row_rsMyProjects['ticket'];
                                    }
                                    ?></td>
                                <td><?php echo $row_rsMyProjects['status']; ?></td>
                                </tr>
    <?php } while ($row_rsMyProjects = mysql_fetch_assoc($rsMyProjects)); ?>
                        </table>
<?php } ?>
                </div>
            </div>

            <div class="module">
                <div class="app" style="background-color: #0000FF;">My Project Tasks</div>
                <div class="modulecontents">
<?php if ($totalRows_rsMyProjectTasks == 0) { ?>
                        <div align="center"><strong>There are currently no Project Tasks assigned to you.</strong></div>
<?php } else { ?>
                        <table class="projectTasks" cellpadding="2" cellspacing="0">
                            <tr>
                                <th width="6%">Target<br />Date</th>
                                <th width="6%">Order</th>
                                <th>Task</th>
                                <th>Project</th>
                                <th width="10%">Status</th>
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
                                <td><?php echo $row_rsMyProjectTasks['targetDate']; ?></td>
                                <td><?php echo $row_rsMyProjectTasks['order']; ?></td>
                                <td><?php echo stripslashes($row_rsMyProjectTasks['projectEvent']); ?></td>
                                <td><a href="../projects/project.php?function=view&amp;project=<?php echo $row_rsMyProjectTasks['projectID']; ?>"><?php echo stripslashes($row_rsMyProjectTasks['projectName']); ?></a></td>
                                <td><?php echo $row_rsMyProjectTasks['status']; ?></td>
                                </tr>
    <?php } while ($row_rsMyProjectTasks = mysql_fetch_assoc($rsMyProjectTasks)); ?>
                        </table>
<?php } ?>
                </div>
            </div>

            <div class="module">
                <div class="app" style="background-color: #009900;"><a style="color: #FFFFFF; text-decoration: none;" title="View Requests for your Support" href="../n3xt/supportRequests/supportRequests.php?employee=<?php echo $_SESSION['employee']; ?>&amp;status=none">Requests for your Support</a></div>
                <div class="modulecontents">
<?php if ($totalRows_rsSupportRequests == 0) { ?>
                        <div align="center"><strong>There are currently no requests for your support</strong></div>
<?php } else { ?>
                        <table class="mySupportRequests" width="90%" cellpadding="2" cellspacing="0">
                            <tr>
                                <th width="6%">Date<br />Requested</th>
                                <th width="6%">Target<br />Date</th>
                                <th width="34%">Subject</th>
                                <th width="16%">Customer</th>
                                <th width="8%">App</th>
                                <th width="7%">Ticket</th>
                                <th width="10%">Status</th>
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
                                <td><?php echo $row_rsSupportRequests['dateEscalated']; ?></td>
                                <td><?php echo $row_rsSupportRequests['targetDate']; ?></td>
                                <td><a href="../supportRequests/supportRequest.php?supportRequest=<?php echo $row_rsSupportRequests['escalationID']; ?>&amp;function=view"><?php echo stripslashes($row_rsSupportRequests['subject']); ?></a></td>
                                <td><?php echo $row_rsSupportRequests['customer']; ?></td>
                                <td><?php echo $row_rsSupportRequests['application']; ?></td>
                                <td><?php
                            if ($row_rsSupportRequests['ticket'] == "0") {
                                echo "-";
                            } else {
                                echo $row_rsSupportRequests['ticket'];
                            }
                                ?></td>
                                <td><?php echo $row_rsSupportRequests['status']; ?></td>
                                </tr>
    <?php } while ($row_rsSupportRequests = mysql_fetch_assoc($rsSupportRequests)); ?>
                        </table>
<?php } ?>
                </div>
            </div>

            <div class="module">
                <div class="app" style="background-color: #FFFF00; color: #000000;">Unassigned Support Requests</div>
                <div class="modulecontents">
                    <table class="unassSupportRequests" cellpadding="2" cellspacing="0">
                        <tr>
                            <th width="6%">Date<br />Requested</th>
                            <th width="6%">Target<br />Date</th>
                            <th width="34%">Subject</th>
                            <th width="16%">Customer</th>
                            <th width="8%">App</th>
                            <th width="7%">Ticket</th>
                            <th width="10%">Status</th>
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
                            <td><?php echo $row_rsUnassignedSupportRequests['dateEscalated']; ?></td>
                            <td><?php echo $row_rsUnassignedSupportRequests['targetDate']; ?></td>
                            <td><a href="../supportRequests/supportRequest.php?supportRequest=<?php echo $row_rsUnassignedSupportRequests['escalationID']; ?>&amp;function=view"><?php echo stripslashes($row_rsUnassignedSupportRequests['subject']); ?></a></td>
                            <td><?php echo $row_rsUnassignedSupportRequests['customer']; ?></td>
                            <td><?php echo $row_rsUnassignedSupportRequests['application']; ?></td>
                            <td><?php
                            if ($row_rsUnassignedSupportRequests['ticket'] == "0") {
                                echo "-";
                            } else {
                                echo $row_rsUnassignedSupportRequests['ticket'];
                            }
                            ?></td>
                            <td><?php echo $row_rsUnassignedSupportRequests['status']; ?></td>
                            </tr>
<?php } while ($row_rsUnassignedSupportRequests = mysql_fetch_assoc($rsUnassignedSupportRequests)); ?>
                    </table>
                </div>
            </div>

        </div>
    </body>
</html><?php
mysql_free_result($rsMyProjects);
mysql_free_result($rsSupportRequests);
mysql_free_result($rsPendingMaintenances);
mysql_free_result($rsEmployeeInfo);
mysql_free_result($rsUnassignedSupportRequests);
?>