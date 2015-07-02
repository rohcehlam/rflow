<?php
require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');
session_start();

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsApplications = "SELECT applicationID, `application` FROM applications ORDER BY `application` ASC";
$rsApplications = mysql_query($query_rsApplications, $connProdOps) or die(mysql_error());
$row_rsApplications = mysql_fetch_assoc($rsApplications);
$totalRows_rsApplications = mysql_num_rows($rsApplications);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsSubapplications = "SELECT subapplicationID, subapplication FROM subapplications ORDER BY subapplication ASC";
$rsSubapplications = mysql_query($query_rsSubapplications, $connProdOps) or die(mysql_error());
$row_rsSubapplications = mysql_fetch_assoc($rsSubapplications);
$totalRows_rsSubapplications = mysql_num_rows($rsSubapplications);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEmployees = "SELECT employeeID, displayName FROM employees ORDER BY displayName ASC";
$rsEmployees = mysql_query($query_rsEmployees, $connProdOps) or die(mysql_error());
$row_rsEmployees = mysql_fetch_assoc($rsEmployees);
$totalRows_rsEmployees = mysql_num_rows($rsEmployees);

mysql_select_db($database_connProdOps, $connProdOps);
$query_rsLayers = "SELECT layerID, layer FROM layers ORDER BY layer ASC";
$rsLayers = mysql_query($query_rsLayers, $connProdOps) or die(mysql_error());
$row_rsLayers = mysql_fetch_assoc($rsLayers);
$totalRows_rsLayers = mysql_num_rows($rsLayers);
?><?php
if ($_GET['function'] != "add") {
    $varRFA_rsRFA = "-1";
    if (isset($_GET['rfa'])) {
        $varRFA_rsRFA = (get_magic_quotes_gpc()) ? $_GET['rfa'] : addslashes($_GET['rfa']);
    }
    mysql_select_db($database_connProdOps, $connProdOps);
    //$query_rsRFA = sprintf("SELECT changerequests.changeRequestID, employees1.displayName as submittedBy, employees2.displayName as reviewer, changerequests.reviewedBy, DATE_FORMAT(dateSubmitted, '%%m/%%d/%%Y') AS dateSubmitted, TIME_FORMAT(timeSubmitted,'%%k:%%i') AS timeSubmitted, changerequests.summary, changerequests.description, changerequests.status, changerequests.comments, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%%m/%%d/%%Y') AS windowStartDate, TIME_FORMAT(windowStartTime,'%%k:%%i') AS windowStartTime, DATE_FORMAT(windowEndDate, '%%m/%%d/%%Y') AS windowEndDate, TIME_FORMAT(windowEndTime,'%%k:%%i') AS windowEndTime, changerequests.applicationID, applications.application, changerequests.subapplicationID, subapplications.subapplication, changerequests.layerID, layers.layer, changerequests.risk FROM changerequests LEFT JOIN (employees AS employees1, employees AS employees2) ON (changerequests.submittedBy=employees1.employeeID AND changerequests.reviewedBy=employees2.employeeID) LEFT JOIN applications ON changerequests.applicationID=applications.applicationID LEFT JOIN subapplications ON changerequests.subapplicationID=subapplications.subapplicationID LEFT JOIN layers ON changerequests.layerID=layers.layerID WHERE changerequests.changeRequestID = %s", GetSQLValueString($varRFA_rsRFA, "int"));
    
    $query_rsRFA = sprintf("SELECT changerequests.changeRequestID, employees1.displayName as submittedBy, employees2.displayName as reviewer, changerequests.reviewedBy, DATE_FORMAT(dateSubmitted, '%%m/%%d/%%Y') AS dateSubmitted, TIME_FORMAT(timeSubmitted,'%%k:%%i') AS timeSubmitted, changerequests.summary, changerequests.description, changerequests.status, changerequests.comments, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%%m/%%d/%%Y') AS windowStartDate, TIME_FORMAT(windowStartTime,'%%k:%%i') AS windowStartTime, DATE_FORMAT(windowEndDate, '%%m/%%d/%%Y') AS windowEndDate, TIME_FORMAT(windowEndTime,'%%k:%%i') AS windowEndTime, changerequests.applicationID, applications.application, changerequests.subapplicationID, subapplications.subapplication, changerequests.layerID, layers.layer, changerequests.risk FROM changerequests LEFT JOIN employees as employees1 ON changerequests.submittedBy=employees1.employeeID LEFT JOIN employees AS employees2 ON changerequests.reviewedBy=employees2.employeeID LEFT JOIN applications ON changerequests.applicationID=applications.applicationID LEFT JOIN subapplications ON changerequests.subapplicationID=subapplications.subapplicationID LEFT JOIN layers ON changerequests.layerID=layers.layerID WHERE changerequests.changeRequestID = %s", GetSQLValueString($varRFA_rsRFA, "int"));
    $rsRFA = mysql_query($query_rsRFA, $connProdOps) or die(mysql_error());
    $row_rsRFA = mysql_fetch_assoc($rsRFA);

    $totalRows_rsRFA = mysql_num_rows($rsRFA);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php buildTitle("an RFC"); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="../inc/global.css" rel="stylesheet" type="text/css" />
        <link href="../inc/menu.css" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />
        <script type="text/javascript" src="../inc/js/menu.js"></script>
        <script type="text/javascript" src="../inc/js/js.js"></script>
        <script type="text/javascript" src="../inc/js/calendarDateInput2.js"></script>        
    </head>
    <body>
        <?php buildMenu(); ?>
        <script type="text/javascript">
            dolphintabs.init("menunav", 1)
        </script>
        <div class="casing" align="left">
            <?php
            if ($_GET['function'] == "add") {
                buildHeader("rfa", "RFCs", "rfa", "Add an RFC", null);
            } elseif ($_GET['function'] == "update") {
                buildHeader("rfa", "RFCs", "rfa", "Update an RFC", "Add an RFC");
            } else {
                buildHeader("rfa", "RFCs", "rfa", "View an RFC", "Add an RFC");
            }
            ?>
            <form action="rfaSend.php" method="post" enctype="multipart/form-data" name="rfaForm">
                <table align="center" class="viewLarge" cellpadding="2" cellspacing="0">
                    <tr>
                        <td width="132" nowrap="nowrap" class="contrast"><label for="submittedBy">Submitted by:</label></td>
                        <td colspan="3"><?php if ($_GET['function'] == "add") {
                        ?><select name="submittedBy" id="submittedBy" tabindex="1">
                                    <option value=""<?php
                                    if (!(strcmp("", $_SESSION['employee']))) {
                                        echo " selected=\"selected\"";
                                    }
                                    ?>>Select Employee</option>
                                            <?php
                                            do {
                                                ?><option value="<?php echo $row_rsEmployees['employeeID'] ?>"<?php
                                        if (!(strcmp($row_rsEmployees['employeeID'], $_SESSION['employee']))) {
                                            echo " selected=\"selected\"";
                                        }
                                        ?>><?php echo $row_rsEmployees['displayName'] ?></option>
                                                <?php
                                            } while ($row_rsEmployees = mysql_fetch_assoc($rsEmployees));
                                            $rows = mysql_num_rows($rsEmployees);
                                            if ($rows > 0) {
                                                mysql_data_seek($rsEmployees, 0);
                                                $row_rsEmployees = mysql_fetch_assoc($rsEmployees);
                                            }
                                            ?>
                                </select>&nbsp;on&nbsp;<script>DateInput('dateSubmitted', true, 'YYYY-MM-DD')</script>at&nbsp;&nbsp;<input type="text" name="timeSubmitted" id="timeSubmitted" tabindex="2" value="<?php
                                $now = getdate();
                                $hour = $now['hours'];
                                $min = $now['minutes'];
                                $DST = true;
//hours
                                if ($DST == true) {
                                    $hourAdj = $hour;
                                } else {
                                    $hourAdj = $hour;
                                }
                                if ($hourAdj < 10) {
                                    echo "0" . $hourAdj;
                                } else {
                                    echo $hourAdj;
                                }
                                echo ":";
//minutes
                                if ($min < 10) {
                                    echo "0" . $min;
                                } else {
                                    echo $min;
                                }
                                ?>" size="4" maxlength="5" /><?php
                                } else {
                                    echo $row_rsRFA['submittedBy'] . "&nbsp;on&nbsp;" . $row_rsRFA['dateSubmitted'] . "&nbsp;at&nbsp;" . $row_rsRFA['timeSubmitted'];
                                }
                                ?>&nbsp;UTC</td>
                        <tr>
                            <td class="contrast"><label for="summary">Summary:</label></td>
                            <td colspan="3"><?php formField("text", "summary", $row_rsRFA['summary'], "85", "255", null, null, "3", "y"); ?></td>
                        </tr>
                        <tr>
                            <td valign="top" class="contrast"><label for="description">Description:</label></td>
                            <td colspan="3"><?php formField("textarea", "description", $row_rsRFA['description'], "75", null, "4", "virtual", "5", "n"); ?></td>
                        </tr>
                        <tr>
                            <td class="contrast"><label for="application">Application:</label></td>
                            <td width="190"><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
                                                                                                                                           ?><select name="application" id="application" tabindex="5">
                                        <option value="">Select Application</option>
                                        <?php
                                        do {
                                            ?><option value="<?php echo $row_rsApplications['applicationID'] ?>"<?php
                                            if (!(strcmp($row_rsApplications['applicationID'], $row_rsRFA['applicationID']))) {
                                                echo " selected=\"selected\"";
                                            }
                                            ?>><?php echo $row_rsApplications['application'] ?></option>
                                                    <?php
                                                } while ($row_rsApplications = mysql_fetch_assoc($rsApplications));
                                                $rows = mysql_num_rows($rsApplications);
                                                if ($rows > 0) {
                                                    mysql_data_seek($rsApplications, 0);
                                                    $row_rsApplications = mysql_fetch_assoc($rsApplications);
                                                }
                                                ?>
                                    </select><?php requiredField(); ?>
                                    <?php
                                } else {
                                    echo $row_rsRFA['application'];
                                }
                                ?></td>
                            <td width="120" class="contrast"><label for="subapplication">Subapplication:</label></td>
                            <td><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
                                    ?><select name="subapplication" id="subapplication" tabindex="6">
                                        <option value="">Select Subapplication</option>
                                        <?php
                                        do {
                                            ?><option value="<?php echo $row_rsSubapplications['subapplicationID'] ?>"<?php
                                            if (!(strcmp($row_rsSubapplications['subapplicationID'], $row_rsRFA['subapplicationID']))) {
                                                echo " selected=\"selected\"";
                                            }
                                            ?>><?php echo $row_rsSubapplications['subapplication'] ?></option>
                                                    <?php
                                                } while ($row_rsSubapplications = mysql_fetch_assoc($rsSubapplications));
                                                $rows = mysql_num_rows($rsSubapplications);
                                                if ($rows > 0) {
                                                    mysql_data_seek($rsSubapplications, 0);
                                                    $row_rsSubapplications = mysql_fetch_assoc($rsSubapplications);
                                                }
                                                ?>
                                    </select><?php requiredField(); ?>
                                    <?php
                                } else {
                                    echo $row_rsRFA['subapplication'];
                                }
                                ?></td>
                            <tr>
                                <td class="contrast"><label for="layer">Layer:</label></td>
                                <td colspan="3"><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
                                    ?><select name="layer" id="layer" tabindex="7">
                                            <option value="">Select Layer</option>
                                            <?php
                                            do {
                                                ?><option value="<?php echo $row_rsLayers['layerID'] ?>"<?php
                                                if (!(strcmp($row_rsLayers['layerID'], $row_rsRFA['layerID']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>><?php echo $row_rsLayers['layer'] ?></option>
                                                        <?php
                                                    } while ($row_rsLayers = mysql_fetch_assoc($rsLayers));
                                                    $rows = mysql_num_rows($rsLayers);
                                                    if ($rows > 0) {
                                                        mysql_data_seek($rsLayers, 0);
                                                        $row_rsLayers = mysql_fetch_assoc($rsLayers);
                                                    }
                                                    ?>
                                        </select><?php requiredField(); ?>
                                        <?php
                                    } else {
                                        echo $row_rsRFA['layer'];
                                    }
                                    ?></td>
                            </tr>
                            <tr>
                                <td nowrap="nowrap" class="contrast"><label for="requestOrigin">Request Origin</label><label for="requestOriginID"> &amp; ID:</label></td>
                                <td colspan="3"><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
                                        ?><select name="requestOrigin" id="requestOrigin" tabindex="8">
                                            <option value="">Select Origin</option>
                                            <option value="Ticket"<?php
                                            if (!(strcmp("Ticket", $row_rsRFA['requestOrigin']))) {
                                                echo " selected=\"selected\"";
                                            }
                                            ?>>Ticket</option>                                            
                                            <option value="Support Request"<?php
                                            if (!(strcmp("Support Request", $row_rsRFA['requestOrigin']))) {
                                                echo " selected=\"selected\"";
                                            }
                                            ?>>Support Request</option>
                                            <option value="Emergency Request"<?php
                                            if (!(strcmp("Emergency Request", $row_rsRFA['requestOrigin']))) {
                                                echo " selected=\"selected\"";
                                            }
                                            ?>>Emergency Request</option>
                                        </select><?php
                                    } else {
                                        echo $row_rsRFA['requestOrigin'];
                                    }
                                    ?>&nbsp;#&nbsp;<?php formField("text", "requestOriginID", $row_rsRFA['requestOriginID'], "10", "45", null, null, "9", "y"); ?></td>
                            </tr>
                            <tr>
                                <td class="contrast"><label>Change Window:</label></td>
                                <td nowrap="nowrap" colspan="2">Starting <?php if (($_GET['function'] == "add") || (($_GET['function'] == "update") && ($_GET['modWindow'] == "y") )) {
                                        ?><script>DateInput('windowStartDate', true, 'YYYY-MM-DD')</script><?php
                                    } else {
                                        echo $row_rsRFA['windowStartDate'] . "&nbsp;";
                                    }
                                    ?>at&nbsp;<?php
                                    if (($_GET['function'] == "add") || (($_GET['function'] == "update") && ($_GET['modWindow'] == "y") )) {
                                        formField("text", "windowStartTime", $row_rsRFA['windowStartTime'], "5", "5", null, null, "10", "n");
                                    } else {
                                        echo $row_rsRFA['windowStartTime'];
                                    }
                                    ?>&nbsp;UTC<?php requiredField(); ?></td>
                            </tr>
                            <tr>
                                <td class="contrast">&nbsp;</td>
                                <td nowrap="nowrap" colspan="2">Ending <?php if (($_GET['function'] == "add") || (($_GET['function'] == "update") && ($_GET['modWindow'] == "y") )) {
                                        ?><script>DateInput('windowEndDate', true, 'YYYY-MM-DD')</script><?php
                                    } else {
                                        echo $row_rsRFA['windowEndDate'] . "&nbsp;";
                                    }
                                    ?>at&nbsp;<?php
                                    if (($_GET['function'] == "add") || (($_GET['function'] == "update") && ($_GET['modWindow'] == "y") )) {
                                        formField("text", "windowEndTime", $row_rsRFA['windowEndTime'], "5", "5", null, null, "11", "n");
                                    } else {
                                        echo $row_rsRFA['windowEndTime'];
                                    }
                                    ?>&nbsp;UTC<?php requiredField(); ?></td>
                                <?php if($_GET['function'] != "add"){?>
                                <td>
                                    <a class='btn btn-app' href="rfa.php?function=update&amp;rfa=<?php echo $_GET['rfa']; ?>&amp;modWindow=<?php
                                    if ((!isset($_GET['modWindow'])) || ($_GET['modWindow'] == "n")) {
                                        echo "y";
                                    } elseif ($_GET['modWindow'] == "y") {
                                        echo "n";
                                    }
                                    ?>"><?php
                                    if ($_GET['modWindow'] == "y") {
                                        echo "Cancel Update/Approve RFC";
                                    } else {
                                        echo "Update/Approve RFC";
                                    }
                                    ?></a>
                                </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td class="contrast" valign="top"><label for="risk">Risk:</label></td>
                                <td colspan="3"><?php formField("textarea", "risk", $row_rsRFA['risk'], "75", null, "5", "virtual", "12", "n"); ?></td>
                            </tr>
                            <tr>
                                <td class="contrast" valign="top"><label for="status">Status:</label></td>
                                <td valign="top"><?php if (($_GET['function'] == "add") || ($_GET['function'] == "update")) {
                                               ?><select name="status" id="status" tabindex="13">
                                            <option value="Pending Approval">Pending Approval</option>
                                            <?php if ($_SESSION['group'] == "1") { ?>
                                                <option value="Approved"<?php
                                                if (!(strcmp("Approved", $row_rsRFA['status']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>>Approved</option>
                                                <option value="Declined"<?php
                                                if (!(strcmp("Declined", $row_rsRFA['status']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>>Declined</option>
                                                <option value="Pre-approved"<?php
                                                if (!(strcmp("Pre-approved", $row_rsRFA['status']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>>Pre-approved</option>
                                                <option value="Returned"<?php
                                                if (!(strcmp("Returned", $row_rsRFA['status']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>>Returned</option>
                                                <option value="Submitted for CAB Approval"<?php
                                                if (!(strcmp("Submitted for CAB Approval", $row_rsRFA['status']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>>Submitted for CAB Approval</option>
                                                <option value="Approved by CAB"<?php
                                                if (!(strcmp("Approved by CAB", $row_rsRFA['status']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>>Approved by CAB</option>
                                                <option value="Rejected by CAB"<?php
                                                if (!(strcmp("Rejected by CAB", $row_rsRFA['status']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>>Rejected by CAB</option>
                                                <option value="Returned by CAB"<?php
                                                if (!(strcmp("Returned by CAB", $row_rsRFA['status']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>>Returned by CAB</option>
                                                <option value="Completed"<?php
                                                if (!(strcmp("Completed", $row_rsRFA['status']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>>Completed</option>
                                                <option value="Resolved"<?php
                                                if (!(strcmp("Resolved", $row_rsRFA['status']))) {
                                                    echo " selected=\"selected\"";
                                                }
                                                ?>>Resolved</option>
                                            <?php } ?></select><?php
                                    } else {
                                        echo $row_rsRFA['status'];
                                        if ($row_rsRFA['status'] != "Pending Approval") {
                                            echo "&nbsp;by&nbsp;" . $row_rsRFA['reviewer'];
                                        }
                                    }
                                    ?></td>
                                <td class="contrast" valign="top"><label for="comments">Comments (Include Requester):</label></td>
                                <td valign="top"><?php //formField("textarea", "comments", $row_rsRFA['comments'], "32", null, "5", "virtual", "14", "n");   ?>
                                    <textarea name="comments" id="comments" cols="32" rows="5" tabindex="14" wrap="virtual">
                                        <?php
                                        if (isset($row_rsRFA['comments'])) {
                                            echo $row_rsRFA['comments'];
                                        }
                                        ?>
                                    </textarea>
                                </td>
                            </tr>
                            <tr class="button"><td colspan="4"><input type="submit" name="submit" id="submit" value="Send RFC" />
                                    <?php if ($_GET['function'] != "add") { ?><br /><a style="font-weight: bold;" href="../maintenances/maintenanceAdd.php?function=add&amp;rfa=<?php echo $_GET['rfa']; ?>&amp;module=maintenance">Generate Maintenance Notification</a><br />
                                    <?php } ?>
                                    <?php sentSuccessful("RFC sent successfully!"); ?></td></tr>
                            </table>
                            <?php if ($_GET['function'] == "add") { ?>
                                <input type="hidden" name="MM_insert" value="rfaAdd" />
                            <?php } elseif ($_GET['function'] == "update") { ?>
                                <input type="hidden" name="MM_update" value="rfaUpdate" />
                                <input type="hidden" name="changeRequestID" value="<?php echo $row_rsRFA['changeRequestID']; ?>" />
                                <input type="hidden" name="reviewedBy" value="<?php echo $_SESSION['employee']; ?>" />
                            <?php } ?>
                            </form>
                            <?php buildFooter("0"); ?>
                            </div></div></body>
                            </html><?php
                            mysql_free_result($rsApplications);
                            mysql_free_result($rsSubapplications);
                            mysql_free_result($rsEmployees);
                            mysql_free_result($rsLayers);
                            mysql_free_result($rsRFA);

                            