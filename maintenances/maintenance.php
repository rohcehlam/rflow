<?php
require_once('../Connections/connProdOps.php');
require_once('../inc/functions.php');
session_start();

if ($_GET['function'] == "view") {
    //maintenance notification
    $varMaintenance_rsMaintenanceNotif = "1";
    if (isset($_GET['maintenance'])) {
        $varMaintenance_rsMaintenanceNotif = (get_magic_quotes_gpc()) ? $_GET['maintenance'] : addslashes($_GET['maintenance']);
    }
    mysql_select_db($database_connProdOps, $connProdOps);
    $query_rsMaintenanceNotif = sprintf("SELECT maintenancenotifs.maintenanceNotifsID, maintenancenotifs.reason, maintenancenotifs.customerImpact, maintenancenotifs.nocImpact, maintenancenotifs.prodChanges, TIME_FORMAT(startTime, '%%H:%%i') as startTime, maintenancenotifs.employeeID, maintenancenotifs.estimatedHours, maintenancenotifs.estimatedMinutes, DATE_FORMAT(startDate, '%%m/%%d/%%Y') as startDate, employees.displayName, maintenancenotifs.status FROM maintenancenotifs, employees WHERE maintenancenotifs.maintenanceNotifsID = %s AND maintenancenotifs.employeeID=employees.employeeID", $varMaintenance_rsMaintenanceNotif);
    $rsMaintenanceNotif = mysql_query($query_rsMaintenanceNotif, $connProdOps) or die(mysql_error());
    $row_rsMaintenanceNotif = mysql_fetch_assoc($rsMaintenanceNotif);
    $totalRows_rsMaintenanceNotif = mysql_num_rows($rsMaintenanceNotif);

    //associated status reports
    $varMaintenance_rsAssociatedStatusReports = "1";
    if (isset($_GET['maintenance'])) {
        $varMaintenance_rsAssociatedStatusReports = (get_magic_quotes_gpc()) ? $_GET['maintenance'] : addslashes($_GET['maintenance']);
    }
    mysql_select_db($database_connProdOps, $connProdOps);
    $query_rsAssociatedStatusReports = sprintf("SELECT statusreports.statusReportID, DATE_FORMAT(endDate, '%%m/%%d/%%Y') as endDate, statusreports.subject, statusreports.employeeID, employees.displayName, statusreports.maintenanceNotifID FROM statusreports LEFT JOIN employees ON statusreports.employeeID=employees.employeeID WHERE statusreports.maintenanceNotifID = %s ORDER BY statusreports.endDate, statusreports.endTime ASC", $varMaintenance_rsAssociatedStatusReports);
    $rsAssociatedStatusReports = mysql_query($query_rsAssociatedStatusReports, $connProdOps) or die(mysql_error());
    $row_rsAssociatedStatusReports = mysql_fetch_assoc($rsAssociatedStatusReports);
    $totalRows_rsAssociatedStatusReports = mysql_num_rows($rsAssociatedStatusReports);
}

if (($_GET['function'] == "view") || ($_GET['function'] == "update")) {
    //find out if this maintenance notification is already tied to a project event/project
    $varModuleID_rsAnyPEforMN = "1";
    if (isset($_GET['maintenance'])) {
        $varModuleID_rsAnyPEforMN = (get_magic_quotes_gpc()) ? $_GET['maintenance'] : addslashes($_GET['maintenance']);
    }
    mysql_select_db($database_connProdOps, $connProdOps);
    $query_rsAnyPEforMN = sprintf("SELECT projecttasksxmodules.projectTasksXmoduleID, projecttasksxmodules.projectID, projecttasksxmodules.projectTaskID, projecttasksxmodules.module, projecttasksxmodules.moduleID FROM projecttasksxmodules WHERE projecttasksxmodules.moduleID = %s AND projecttasksxmodules.module='maintenance'", $varModuleID_rsAnyPEforMN);
    $rsAnyPEforMN = mysql_query($query_rsAnyPEforMN, $connProdOps) or die(mysql_error());
    $row_rsAnyPEforMN = mysql_fetch_assoc($rsAnyPEforMN);
    $totalRows_rsAnyPEforMN = mysql_num_rows($rsAnyPEforMN);
}

//Select engineers
mysql_select_db($database_connProdOps, $connProdOps);
$query_rsEngineers = "SELECT employeeID, engineer, lastName, displayName FROM employees WHERE employees.engineer='y' AND employees.active='t' ORDER BY displayName ASC";
$rsEngineers = mysql_query($query_rsEngineers, $connProdOps) or die(mysql_error());
$row_rsEngineers = mysql_fetch_assoc($rsEngineers);
$totalRows_rsEngineers = mysql_num_rows($rsEngineers);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php buildTitle("Maintenance Notification"); ?></title>
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
                <?php buildHeader("maintenance", "Maintenance Notifications", "maintenance", "Add a Maintenance Notification", "Update a Maintenance Notification"); ?>
            <form action="maintenanceSend.php" method="post" enctype="multipart/form-data" name="maintenanceUpdate" id="maintenanceUpdate" onsubmit="MM_validateForm('startMonth', '', 'RinRange1:12', 'startDay', '', 'RinRange1:31', 'startYear', '', 'RinRange2006:2020', 'startHour', '', 'RinRange0:23', 'startMinute', '', 'RinRange0:59', 'estHours', '', 'RinRange0:99', 'estMins', '', 'RinRange0:999', 'custImpact', '', 'R', 'nocImpact', '', 'R', 'engineer', '', 'R', 'prodChanges', '', 'R');
                return document.MM_returnValue">
                <table class="<?php
                if ($_GET['function'] == "update") {
                    echo "update";
                } else {
                    echo "viewLarge";
                }
                ?>" align="center" cellpadding="2" cellspacing="0">
                    <tr bgcolor="#7EABCD"><td colspan="5"></td></tr>
                    <tr>
                        <td width="108" class="contrast"><?php makeLabel("startDate", "Start Date:"); ?></td>
                        <td colspan="2" nowrap="nowrap"><?php if ($_GET['function'] != "view") {
                    ?><script>DateInput('startDate', true, 'YYYY-MM-DD')</script><?php
                            } else {

                                echo $row_rsMaintenanceNotif['startDate'];
                            }
                            ?></td>
                        <td width="138" class="contrast"><?php makeLabel("status", "Status:"); ?></td>
                        <td valign="top"><select<?php
                                if (($row_rsMaintenanceNotif['status'] == "Closed") || ($row_rsMaintenanceNotif['status'] == "Canceled")) {
                                    echo " name=\"userStatus\" id=\"userStatus\" disabled=\"disabled\"";
                                } else {
                                    echo " name=\"status\" id=\"status\"";
                                }
                                ?>>
                                <option value="Open"<?php if (!(strcmp("Open", $row_rsMaintenanceNotif['status']))) {
                                    echo " selected=\"selected\"";
                                } ?>>Open</option>
                                <option value="Closed"<?php if (!(strcmp("Closed", $row_rsMaintenanceNotif['status']))) {
                                echo " selected=\"selected\"";
                            } ?>>Closed</option>
                                <option value="Canceled"<?php if (!(strcmp("Canceled", $row_rsMaintenanceNotif['status']))) {
                                echo " selected=\"selected\"";
                            } ?>>Canceled</option>
                                <option value="Extended"<?php if (!(strcmp("Extended", $row_rsMaintenanceNotif['status']))) {
                                echo " selected=\"selected\"";
                            } ?>>Extended</option>
                            </select><?php
                            if ($_GET['function'] != "add") {
                                if (($row_rsMaintenanceNotif['status'] == "Closed") || ($row_rsMaintenanceNotif['status'] == "Canceled")) {
                                    echo "<input type=\"hidden\" value=\"" . $row_rsMaintenanceNotif['status'] . "\" name=\"status\" id=\"status\" />";
                                }
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td class="contrast"><?php makeLabel("startTime", "Start Time:"); ?></td>
                        <td colspan="2" valign="top"><?php if ($_GET['function'] != "view") {
                                ?><input type="text" name="startHour" id="startHour" value="<?php
                                       $now = getdate();
                                       $hour = $now['hours'];
                                       $DST = true;
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
                                       ?>" size="2" maxlength="2" />:<input type="text" name="startMinute" id="startMinute" value="<?php
                                $time = getdate();
                                $min = $time['minutes'];
                                if ($min < 10) {
                                    echo "0" . $min;
                                } else {
                                    echo $min;
                                }
                                ?>" size="2" maxlength="2" />&nbsp;<strong>UTC</strong>&nbsp;<abbr class="required" title="Required">*</abbr>
                            <?php
                            } else {
                                echo $row_rsMaintenanceNotif['startTime'] . "&nbsp;UTC";
                            }
                            ?></td>
                        <td width="138" class="contrast"><?php makeLabel("estDuration", "Estimated Duration:"); ?></td>
                        <td width="263"><?php if ($_GET['function'] != "view") {
                                ?><input type="text" name="estHours" id="estHours" size="2" maxlength="2" tabindex="1" /><label for="estHours">&nbsp;hour(s)</label>&nbsp;<label for="estMins"><input type="text" name="estMins" id="estMins" size="2" maxlength="2" tabindex="2" />&nbsp;minute(s)</label>&nbsp;<abbr class="required" title="Required">*</abbr><?php
                            } else {
                                if ($row_rsMaintenanceNotif['estimatedHours'] > "1") {
                                    echo $row_rsMaintenanceNotif['estimatedHours'] . " hours";
                                } elseif ($row_rsMaintenanceNotif['estimatedHours'] == "1") {
                                    echo $row_rsMaintenanceNotif['estimatedHours'] . " hour";
                                } else {
                                    echo "";
                                }

                                //start estimated minutes
                                if ($row_rsMaintenanceNotif['estimatedMinutes'] > "1") {
                                    echo " " . $row_rsMaintenanceNotif['estimatedMinutes'] . " minutes";
                                } elseif ($row_rsMaintenanceNotif['estimatedMinutes'] == "1") {
                                    echo " " . $row_rsMaintenanceNotif['estimatedMinutes'] . " minute";
                                } else {
                                    echo "";
                                }
                            }
                            ?></td>
                    </tr>
                    <tr class="spacer"><td colspan="5"></td></tr>
                    <tr>
                        <td class="contrast"><?php makeLabel("reason", "Reason:"); ?></td>
                        <td colspan="4"><?php formField("text", "reason", $row_rsMaintenanceNotif['reason'], "100", "255", null, null, "3", "y"); ?></td>
                    </tr>
                    <tr class="spacer"><td colspan="5"></td></tr>
                    <tr>
                        <td class="contrast"><?php makeLabel("customerImpact", "Customer Impact:"); ?></td>
                        <td colspan="4"><?php formField("text", "customerImpact", $row_rsMaintenanceNotif['customerImpact'], "100", "255", null, null, "4", "y"); ?></td>
                    </tr>
                    <tr>
                        <td class="contrast"><?php makeLabel("nocImpact", "NOC Impact:"); ?></td>
                        <td colspan="4"><?php formField("text", "nocImpact", $row_rsMaintenanceNotif['nocImpact'], "100", "255", null, null, "5", "y"); ?></td>
                    </tr>
                    <tr>
                        <td class="contrast"><?php makeLabel("engineer", "Engineer:"); ?></td>
                        <td colspan="4"><?php if ($_GET['function'] != "view") {
                                ?><select name="engineer" id="engineer" tabindex="6">
                                    <option value=""<?php if (!(strcmp("", $_SESSION['employee']))) {
                                    echo " selected=\"selected\"";
                                } ?>>Select Engineer</option>
                        <?php do { ?>
                                        <option value="<?php echo $row_rsEngineers['employeeID'] ?>"<?php if (!(strcmp($row_rsEngineers['employeeID'], $_SESSION['employee']))) {
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
                                </select><?php requiredField(); ?><?php
    sudoAuth("../common/employee.php?function=add", "Add an Engineer", "add");
} else {
    echo $row_rsMaintenanceNotif['displayName'];
}
?></td>
                    </tr>
                    <tr class="spacer"><td colspan="5"></td></tr>
                    <tr>
                        <td valign="top" class="contrast"><?php makeLabel("prodChanges", "Production Changes:"); ?></td>
                        <td valign="top" colspan="4"><?php formField("textarea", "prodChanges", $row_rsMaintenanceNotif['prodChanges'], "82", null, "10", "virtual", "7", "n"); ?></td>
                    </tr>
                    <?php if (!isset($_GET['corp'])) { ?>
                        <tr><td colspan="5" class="recipients"><br />Email Recipients</td></tr>
                        <tr>
                            <td class="contrast"><strong>To:</strong></td>
                            <td width="118" nowrap="nowrap"><label title="DL_SYB-AMPRODOPSTL@exchange.sap.corp, DL_SYB-PM_prodops@exchange.sap.corp, DL_SYB-PMopsmgmt@exchange.sap.corp, DL_SYB-Prodops@exchange.sap.corp"><input type="checkbox" name="prodOps" id="prodOps" value="y" checked="checked" />&nbsp;US ProdOps</label></td>
                            <td width="146" nowrap="nowrap"><label title="OperatorServices.SGNOC@exchange.sap.corp, OperatorServices.NOC@sap.com, DL_SYB-OS_SUI_TEAM@exchange.sap.corp"><input type="checkbox" name="noc" id="noc" value="y" checked="checked" />&nbsp;NOC &amp; SUI</label></td>
                            <td valign="middle" bgcolor="#7EABCD" align="right"><?php makeLabel("cc", "CC:"); ?></td>
                            <td valign="middle"><input type="text" name="cc" id="cc" size="36" /></td>
                        </tr>
                        <tr>
                            <td class="contrast"></td>
                            <td valign="middle"><label title="DL_SYB-syseng@exchange.sap.corp"><input type="checkbox" name="syseng" id="syseng" value="y" />&nbsp;SysEng</label></td>
                            <td valign="middle"><label title="DL_SYB-neteng@exchange.sap.corp"><input type="checkbox" name="neteng" id="neteng" value="y" />&nbsp;NetEng</label></td>
                            <td colspan="2" bgcolor="#7EABCD">&nbsp;</td>
                        </tr>
                        <tr class="button">
                            <td colspan="5"><br />
                                <input type="submit" name="submit" id="submit" value="Send Maintenance Notification" /><?php if ($_GET['function'] != "add") { ?>
                                    <a style="font-weight: bold;" href="../statusReports/statusReport.php?function=add&amp;maintenance=<?php
                        echo $row_rsMaintenanceNotif['maintenanceNotifsID'];
                        if ($row_rsAnyPEforMN > 0) {
                            echo "&amp;project=" . $row_rsAnyPEforMN['projectID'] . "&amp;module=statusReport&amp;projectEvent=" . $row_rsAnyPEforMN['projectTaskID'] . "&amp;function=add";
                        }
                        ?>">Generate Status Report</a><?php }
                    ?><br /><?php sentSuccessful("Maintenance Notification updated successfully!"); ?></td>
                        </tr>
<?php } ?>
                </table>
<?php if ($_GET['function'] != "add") { ?>
                    <input type="hidden" name="maintenance" id="maintenance" value="<?php echo $_GET['maintenance']; ?>" />
                    <input type="hidden" name="MM_update" id="MM_update" value="maintenanceUpdate" />
                <?php } else { ?>
                    <input type="hidden" name="MM_insert" value="maintenanceNotif1" />
                    <input type="hidden" name="status" value="Open" />
                    <?php
                    if (isset($_GET['module'])) {
                        echo "<input type=\"hidden\" name=\"module\" value=\"" . $_GET['module'] . "\" />";

                        if (isset($_GET['project'])) {
                            echo "<input type=\"hidden\" name=\"project\" value=\"" . $_GET['project'] . "\" />";
                        }
                        if (isset($_GET['projectEvent'])) {
                            echo "<input type=\"hidden\" name=\"projectEvent\" value=\"" . $_GET['projectEvent'] . "\" />";
                        }
                        if (isset($_GET['rfa'])) {
                            echo "<input type=\"hidden\" name=\"rfa\" value=\"" . $_GET['rfa'] . "\" />";
                        }
                    }
                }
                ?>
            </form>
            <?php if (($_GET['function'] == "view") && ($row_rsAssociatedStatusReports > 0)) { ?>
                <br />
                <table class="xreference" align="center" cellspacing="0">
                    <caption>Status Reports written regarding this Maintenance</caption>
                    <tr>
                        <th width="13%">Date</th>
                        <th>Subject</th>
                        <th width="15%">Engineer</th>
                    </tr><?php
                        $num = 0;
                        do {
                            $num++;
                            echo "<tr";
                            if ($num % 2) {
                                echo " class=\"odd\"";
                            }
                            echo ">";
                        ?>
                        <td><?php echo $row_rsAssociatedStatusReports['endDate']; ?></td>
                        <td><a href="../statusReports/statusReport.php?statusReport=<?php echo $row_rsAssociatedStatusReports['statusReportID']; ?>&amp;function=view"><?php echo $row_rsAssociatedStatusReports['subject']; ?></a></td>
                        <td><?php echo $row_rsAssociatedStatusReports['displayName']; ?></td>
                        </tr>
                    <?php } while ($row_rsAssociatedStatusReports = mysql_fetch_assoc($rsAssociatedStatusReports)); ?>
                </table>
<?php } ?>
<?php buildFooter("0"); ?>
        </div>
    </body>
</html><?php
mysql_free_result($rsEngineers);
mysql_free_result($rsMaintenanceNotif);
?>
 