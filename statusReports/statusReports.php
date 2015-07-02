<?php
require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');
session_start();

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsStatusReports = 45;
$pageNum_rsStatusReports = 0;
if (isset($_GET['pageNum_rsStatusReports'])) {
    $pageNum_rsStatusReports = $_GET['pageNum_rsStatusReports'];
}
$startRow_rsStatusReports = $pageNum_rsStatusReports * $maxRows_rsStatusReports;

//set variable for getting which wrm to filter on
$varApp_rsStatusReports = "1";
if (isset($_GET['app'])) {
    $varApp_rsStatusReports = (get_magic_quotes_gpc()) ? $_GET['app'] : addslashes($_GET['app']);
}
//set variable for getting which customer to filter on
$varCarrier_rsStatusReports = "1";
if (isset($_GET['customer'])) {
    $varCarrier_rsStatusReports = (get_magic_quotes_gpc()) ? $_GET['customer'] : addslashes($_GET['customer']);
}
//set variable for getting which employee to filter on
$varEmployee_rsStatusReports = "1";
if (isset($_GET['employee'])) {
    $varEmployee_rsStatusReports = (get_magic_quotes_gpc()) ? $_GET['employee'] : addslashes($_GET['employee']);
}
//set variable for getting which report type to filter on
$varReportType_rsStatusReports = "1";
if (isset($_GET['reporttype'])) {
    $varReportType_rsStatusReports = (get_magic_quotes_gpc()) ? $_GET['reporttype'] : addslashes($_GET['reporttype']);
}
//set variable for filtering based on keyword(s) found in the subject field
$varSubject_rsStatusReports = "1";
if (isset($_GET['subject'])) {
    $varSubject_rsStatusReports = (get_magic_quotes_gpc()) ? $_GET['subject'] : addslashes($_GET['subject']);
}
//set variable for getting which Ticket to filter on
$varTicket_rsStatusReports = "1";
if (isset($_GET['ticket'])) {
    $varTicket_rsStatusReports = (get_magic_quotes_gpc()) ? $_GET['ticket'] : addslashes($_GET['ticket']);
}
//set variable for getting which wrm to filter on
$varWRM_rsStatusReports = "1";
if (isset($_GET['wrm'])) {
    $varWRM_rsStatusReports = (get_magic_quotes_gpc()) ? $_GET['wrm'] : addslashes($_GET['wrm']);
}
$varSortBy_rsStatusReports = "1";
if (isset($_GET['sortBy'])) {
    $varSortBy_rsStatusReports = (get_magic_quotes_gpc()) ? $_GET['sortBy'] : addslashes($_GET['sortBy']);
}
$varSortOrder_rsStatusReports = "1";
if (isset($_GET['sortOrder'])) {
    $varSortOrder_rsStatusReports = (get_magic_quotes_gpc()) ? $_GET['sortOrder'] : addslashes($_GET['sortOrder']);
}

//define various queries for each filter type
mysql_select_db($database_connProdOps, $connProdOps);
if (isset($_GET['app'])) {
    if (!isset($_GET['sortBy'])) {
        $query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.applicationID=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varApp_rsStatusReports);
    } else {
        $query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.applicationID=%s ORDER BY %ss.%s %s", $varApp_rsStatusReports, $varSortBy_rsStatusReports, $varSortBy_rsStatusReports, $varSortOrder_rsStatusReports);
    }
} elseif (isset($_GET['customer'])) {
    $query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.customerID=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varCarrier_rsStatusReports);
} elseif (isset($_GET['employee'])) {
    $query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.employeeID=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varEmployee_rsStatusReports);
} elseif (isset($_GET['reporttype'])) {
    $query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.reportTypeID=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varReportType_rsStatusReports);
} elseif (isset($_GET['subject'])) {
    $query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.subject REGEXP '%s' ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varSubject_rsStatusReports);
} elseif (isset($_GET['ticket'])) {
    $query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.magicTicket=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varTicket_rsStatusReports);
} elseif (isset($_GET['wrm'])) {
    $query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID AND statusreports.wrm=%s ORDER BY statusreports.endDate DESC, statusreports.endTime DESC", $varWRM_rsStatusReports);
} else {
    $query_rsStatusReports = sprintf("SELECT statusreports.statusReportID, statusreports.applicationID, statusreports.customerID, statusreports.employeeID, applications.application, customers.customer, employees.displayName, statusreports.magicTicket, statusreports.subject, statusreports.wrm, statusreports.reportTypeID, reporttypes.reportType, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate FROM applications, customers, statusreports, employees, reporttypes WHERE statusreports.applicationID=applications.applicationID AND statusreports.customerID=customers.customerID AND statusreports.reportTypeID=reporttypes.reportTypeID AND statusreports.employeeID=employees.employeeID ORDER BY statusreports.endDate DESC, statusreports.endTime DESC");
}
$query_limit_rsStatusReports = sprintf("%s LIMIT %d, %d", $query_rsStatusReports, $startRow_rsStatusReports, $maxRows_rsStatusReports);
$rsStatusReports = mysql_query($query_limit_rsStatusReports, $connProdOps) or die(mysql_error());
$row_rsStatusReports = mysql_fetch_assoc($rsStatusReports);

if (isset($_GET['totalRows_rsStatusReports'])) {
    $totalRows_rsStatusReports = $_GET['totalRows_rsStatusReports'];
} else {
    $all_rsStatusReports = mysql_query($query_rsStatusReports);
    $totalRows_rsStatusReports = mysql_num_rows($all_rsStatusReports);
}
$totalPages_rsStatusReports = ceil($totalRows_rsStatusReports / $maxRows_rsStatusReports) - 1;

$queryString_rsStatusReports = "";
if (!empty($_SERVER['QUERY_STRING'])) {
    $params = explode("&", $_SERVER['QUERY_STRING']);
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
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsApps = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApps = mysql_query($query_rsApps, $connProdOps) or die(mysql_error());
$row_rsApps = mysql_fetch_assoc($rsApps);
$totalRows_rsApps = mysql_num_rows($rsApps);

//select customers for customer filter list
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsCarrier = "SELECT customerID, customer FROM customers ORDER BY customer ASC";
$rsCarrier = mysql_query($query_rsCarrier, $connProdOps) or die(mysql_error());
$row_rsCarrier = mysql_fetch_assoc($rsCarrier);
$totalRows_rsCarrier = mysql_num_rows($rsCarrier);

//select employees for employee filter list
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEngineers = "SELECT employeeID, displayName FROM employees WHERE employees.engineer ='y' ORDER BY displayName ASC";
$rsEngineers = mysql_query($query_rsEngineers, $connProdOps) or die(mysql_error());
$row_rsEngineers = mysql_fetch_assoc($rsEngineers);
$totalRows_rsEngineers = mysql_num_rows($rsEngineers);

//select report types for report type filter list
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsReportTypes = "SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC";
$rsReportTypes = mysql_query($query_rsReportTypes, $connProdOps) or die(mysql_error());
$row_rsReportTypes = mysql_fetch_assoc($rsReportTypes);
$totalRows_rsReportTypes = mysql_num_rows($rsReportTypes);
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
            dolphintabs.init("menunav", 3)
        </script>
        <!-- <iframe src="statusReportActions.php" scrolling="no" style="float:right; width:19.5%;" frameborder="0" height="100%"></iframe> -->
        <div class="casing" align="left">
            <?php buildHeader("statusReport", null, "statusReports", "Status Reports", "Add a Status Report"); ?>
            <!-- TABS -->
            <div id="tabs">
                <span class="<?php 
                if ((isset($_GET['app'])) || (isset($_GET['customer'])) || (isset($_GET['employee'])) || (isset($_GET['reporttype'])) || (isset($_GET['subject'])) || (isset($_GET['ticket'])) || (isset($_GET['wrm']))) {
                    echo "tabbak";
                } else {
                    echo "tabfor";
                } ?>" id="tab_none"><a href="statusReports.php?filter=none">No Filter</a>
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
                    <div id="tabscontent_none"<?php if ((isset($_GET['app'])) || (isset($_GET['customer'])) || (isset($_GET['employee'])) || (isset($_GET['reporttype'])) || (isset($_GET['subject'])) || (isset($_GET['ticket'])) || (isset($_GET['wrm']))) {
    echo " style=\"display: none;\"";
} ?>>Select a tab to filter the available Status Reports</div>
                    <a name="tabapp" id="tabapp"></a>
                    <div id="tabscontent_app"<?php if (!isset($_GET['app'])) {
                                    echo " style=\"display: none;\"";
                                } ?>>
                        <form action="statusReports.php" method="get" name="filterApp" id="filterApp">
                            Display Status Reports for
                            <select name="app" id="app">
                                <option value="">Select Application</option>
<?php do { ?>
                                    <option value="<?php echo $row_rsApps['applicationID'] ?>"<?php if (isset($_GET['app']) && ($row_rsApps['applicationID'] == $_GET['app'])) {
        echo " selected=\"selected\"";
    } ?>><?php echo $row_rsApps['application'] ?></option>
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
                    <a name="tabcustomer" id="tabcustomer"></a>
                    <div id="tabscontent_customer"<?php if (!isset($_GET['customer'])) {
                                    echo " style=\"display: none;\"";
                                } ?>>
                        <form action="statusReports.php" method="get" name="filterCustomer" id="filterCustomer">
                            Display Status Reports for <select name="customer" id="customer">
                                <option value="">Select Customer</option>
                                <?php do { ?>
                                    <option value="<?php echo $row_rsCarrier['customerID'] ?>"<?php if (isset($_GET['customer']) && ($row_rsCarrier['customerID'] == $_GET['customer'])) {
        echo " selected=\"selected\"";
                                } ?>><?php echo $row_rsCarrier['customer'] ?></option>
                                <?php
                                } while ($row_rsCarrier = mysql_fetch_assoc($rsCarrier));
                                $rows = mysql_num_rows($rsCarrier);
                                if ($rows > 0) {
                                    mysql_data_seek($rsCarrier, 0);
                                    $row_rsCarrier = mysql_fetch_assoc($rsCarrier);
                                }
                                ?>
                            </select>
                            <input type="submit" name="Submit" value="Submit" />
                        </form>
                    </div>
                    <a name="tabengineer" id="tabengineer"></a>
                    <div id="tabscontent_engineer"<?php if (!isset($_GET['employee'])) {
                                    echo " style=\"display: none;\"";
                                } ?>>
                        <form action="statusReports.php" method="get" name="filterEngineer" id="filterEngineer">
                            Display Status Reports by <select name="employee" id="employee">
                                <option value="">Select Engineer</option>
                                <?php do { ?>
                                    <option value="<?php echo $row_rsEngineers['employeeID'] ?>"<?php if (isset($_GET['employee']) && ($row_rsEngineers['employeeID'] == $_GET['employee'])) {
                                    echo " selected=\"selected\"";
                                } ?>><?php echo $row_rsEngineers['displayName'] ?></option>
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
                    <a name="tabreporttype" id="tabreporttype"></a>
                    <div id="tabscontent_reporttype"<?php if (!isset($_GET['reporttype'])) {
                                    echo " style=\"display: none;\"";
                                } ?>>
                        <form action="statusReports.php" method="get" name="filterReportType" id="filterReportType">
                            Display 
                            <select name="reporttype" id="reporttype">
                                <option value="">Select Report Type</option>
                                <?php do { ?>
                                    <option value="<?php echo $row_rsReportTypes['reportTypeID'] ?>"<?php if (isset($_GET['reporttype']) && ($row_rsReportTypes['reportTypeID'] == $_GET['reporttype'])) {
                                        echo " selected=\"selected\"";
                                    } ?>><?php echo $row_rsReportTypes['reportType'] ?></option>
                                <?php
                                } while ($row_rsReportTypes = mysql_fetch_assoc($rsReportTypes));
                                $rows = mysql_num_rows($rsReportTypes);
                                if ($rows > 0) {
                                    mysql_data_seek($rsReportTypes, 0);
                                    $row_rsReportTypes = mysql_fetch_assoc($rsReportTypes);
                                }
                                ?>
                            </select> Status Reports
                            <input type="submit" name="Submit" value="Submit" />
                        </form>
                    </div>
                    <a name="tabsubject" id="tabsubject"></a>
                    <div id="tabscontent_subject"<?php if (!isset($_GET['subject'])) {
    echo " style=\"display: none;\"";
} ?>>
                        <form action="statusReports.php" method="get" name="filterSubject" id="filterSubject">
                            Display Status Reports with the Subject containing <input type="text" name="subject" id="subject"<?php if (isset($_GET['subject'])) {
                echo " value=\"" . $_GET['subject'] . "\"";
            } ?> size="20" />
                            <input type="submit" name="Submit" value="Submit" />
                        </form>
                    </div>
                    <a name="tabticket" id="tabticket"></a>
                    <div id="tabscontent_ticket"<?php if (!isset($_GET['ticket'])) {
                echo " style=\"display: none;\"";
            } ?>>
                        <form action="statusReports.php" method="get" name="filterTicket" id="filterTicket">
                            Display Status Reports for Ticket # <input type="text" name="ticket" id="ticket"<?php if (isset($_GET['ticket'])) {
                echo " value=\"" . $_GET['ticket'] . "\"";
            } ?> size="10" />
                            <input type="submit" name="Submit" value="Submit" />
                        </form>
                    </div>
                    <a name="tabwrm" id="tabwrm"></a>
                    <div id="tabscontent_wrm"<?php if (!isset($_GET['wrm'])) {
                        echo " style=\"display: none;\"";
                    } ?>>
                        <form action="statusReports.php" method="get" name="filterWRM" id="filterWRM">
                            Display Status Reports for Case # <input type="text" name="wrm" id="wrm"<?php if (isset($_GET['wrm'])) {
                        echo " value=\"" . $_GET['wrm'] . "\"";
                    } ?> size="10" />
                            <input type="submit" name="Submit" value="Submit" />
                        </form>
                    </div>
                </div>
            </div><br />

                    <?php
                    //Start filter headers
                    if (isset($_GET['app'])) {
                        echo "<h3>Status Reports for <em>" . $row_rsStatusReports['application'] . "</em></h3>\n";
                    } elseif (isset($_GET['customer'])) {
                        echo "<h3>Status Reports for <em>" . $row_rsStatusReports['customer'] . "</em></h3>\n";
                    } elseif (isset($_GET['employee'])) {
                        echo "<h3>Status Reports by <em>" . $row_rsStatusReports['displayName'] . "</em></h3>\n";
                    } elseif (isset($_GET['reporttype'])) {
                        echo "<h3><em>" . $row_rsStatusReports['reportType'] . "</em> Status Reports</h3>\n";
                    } elseif (isset($_GET['subject'])) {
                        echo "<h3>Status Reports with the Subject containing <em>" . $_GET['subject'] . "</em></h3>\n";
                    } elseif (isset($_GET['ticket'])) {
                        echo "<h3>Status Reports with <em>Ticket #" . $row_rsStatusReports['magicTicket'] . "</em></h3>\n";
                    } elseif (isset($_GET['wrm'])) {
                        echo "<h3>Status Reports for <em>Case #" . $row_rsStatusReports['wrm'] . "</em></h3>\n";
                    }
                    ?>
            <table class="data" align="center" cellpadding="2" cellspacing="0">
                <tr>
                    <th>Date</th>
                    <th>ID</th>
                    <th>Subject</th>
                <?php
                if (!isset($_GET['app'])) {
                    echo "          <th>App</th>\n";
                }
                if (!isset($_GET['customer'])) {
                    echo "          <th>Customer</th>\n";
                }
                if (!isset($_GET['employee'])) {
                    echo "          <th>Engineer</th>\n";
                }
                if (!isset($_GET['reporttype'])) {
                    echo "          <th>Report Type</th>\n";
                }
                if (!isset($_GET['ticket'])) {
                    echo "          <th>Ticket</th>\n";
                }
                if (!isset($_GET['wrm'])) {
                    echo "          <th>Case</th>\n";
                }
                //	sudoAuthData(null, null, "th", null, null); 
                ?>
                </tr>
                <?php
                $num = 0;
                do {
                    $num++;
                    echo "<tr";
                    if ($num % 2) {
                        echo " class=\"odd\"";
                    }
                    echo ">\n";
                    ?>
                    <td><?php echo $row_rsStatusReports['endDate']; ?></td>
                    <td><a title="View this Status Report" href="statusReport.php?function=view&amp;statusReport=<?php echo $row_rsStatusReports['statusReportID']; ?><?php if (isset($_GET['corp'])) {
                        echo "&amp;corp=y";
                    } ?>"><?php echo $row_rsStatusReports['statusReportID']; ?></a></td>
                    <td><a title="View this Status Report" href="statusReport.php?function=view&amp;statusReport=<?php echo $row_rsStatusReports['statusReportID']; ?><?php if (isset($_GET['corp'])) {
                        echo "&amp;corp=y";
                    } ?>"><?php echo stripslashes($row_rsStatusReports['subject']); ?></a></td>
                            <?php
//App column
                            if (!isset($_GET['app'])) {
                                echo "          <td>" . $row_rsStatusReports['application'] . "</td>\n";
                            }
//Carrier column
                            if (!isset($_GET['customer'])) {
                                echo "          <td>" . $row_rsStatusReports['customer'] . "</td>\n";
                            }
//Employee column
                            if (!isset($_GET['employee'])) {
                                echo "          <td>" . $row_rsStatusReports['displayName'] . "</td>\n";
                            }
//Report Type column
                            if (!isset($_GET['reporttype'])) {
                                echo "          <td>" . $row_rsStatusReports['reportType'] . "</td>\n";
                            }
//Ticket column
                            if (!isset($_GET['ticket'])) {
                                echo "          <td>";
                                if ($row_rsStatusReports['magicTicket'] == "0") {
                                    echo "-";
                                } else {
                                    echo $row_rsStatusReports['magicTicket'];
                                }
                                echo "</td>\n";
                            }
//WRM column
                            if (!isset($_GET['wrm'])) {
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
<?php } while ($row_rsStatusReports = mysql_fetch_assoc($rsStatusReports)); ?>
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
</html><?php
mysql_free_result($rsCarrier);
mysql_free_result($rsStatusReports);
mysql_free_result($rsEngineers);
mysql_free_result($rsApps);
mysql_free_result($rsReportTypes);
?>