<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	 'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'rfa' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);
//var_dump($my_get);

$query_rsApplications = "SELECT applicationID, `application` FROM applications ORDER BY `application` ASC";
$rsApplications = $conn->query($query_rsApplications);
$row_rsApplications = $rsApplications->fetch_assoc();
$totalRows_rsApplications = $rsApplications->num_rows;

$query_rsSubapplications = "SELECT subapplicationID, subapplication FROM subapplications ORDER BY subapplication ASC";
$rsSubapplications = $conn->query($query_rsSubapplications);
$row_rsSubapplications = $rsSubapplications->fetch_assoc();
$totalRows_rsSubapplications = $rsSubapplications->num_rows;

$query_rsEmployees = "SELECT employeeID, displayName FROM employees ORDER BY displayName ASC";
$rsEmployees = $conn->query($query_rsEmployees);
$row_rsEmployees = $rsEmployees->fetch_assoc();
$totalRows_rsEmployees = $rsEmployees->num_rows;

$query_rsLayers = "SELECT layerID, layer FROM layers ORDER BY layer ASC";
$rsLayers = $conn->query($query_rsLayers);
$row_rsLayers = $rsLayers->fetch_assoc();
$totalRows_rsLayers = $rsLayers->num_rows;

if ($my_get['function'] != "add") {
	$varRFA_rsRFA = "-1";
	if (isset($my_get['rfa'])) {
		$varRFA_rsRFA = (get_magic_quotes_gpc()) ? $my_get['rfa'] : addslashes($my_get['rfa']);
	}
	$query_rsRFA = sprintf("SELECT changerequests.changeRequestID, employees1.displayName as submittedBy, employees2.displayName as reviewer, changerequests.reviewedBy, DATE_FORMAT(dateSubmitted, '%%m/%%d/%%Y') AS dateSubmitted, TIME_FORMAT(timeSubmitted,'%%k:%%i') AS timeSubmitted, changerequests.summary, changerequests.description, changerequests.status, changerequests.comments, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged, DATE_FORMAT(windowStartDate, '%%m/%%d/%%Y') AS windowStartDate, TIME_FORMAT(windowStartTime,'%%k:%%i') AS windowStartTime, DATE_FORMAT(windowEndDate, '%%m/%%d/%%Y') AS windowEndDate, TIME_FORMAT(windowEndTime,'%%k:%%i') AS windowEndTime, changerequests.applicationID, applications.application, changerequests.subapplicationID, subapplications.subapplication, changerequests.layerID, layers.layer, changerequests.risk FROM changerequests LEFT JOIN employees as employees1 ON changerequests.submittedBy=employees1.employeeID LEFT JOIN employees AS employees2 ON changerequests.reviewedBy=employees2.employeeID LEFT JOIN applications ON changerequests.applicationID=applications.applicationID LEFT JOIN subapplications ON changerequests.subapplicationID=subapplications.subapplicationID LEFT JOIN layers ON changerequests.layerID=layers.layerID WHERE changerequests.changeRequestID = %s", GetSQLValueString($varRFA_rsRFA, "int"));
	$rsRFA = $conn->query($query_rsRFA);
	$row_rsRFA = $rsRFA->fetch_assoc();
	$totalRows_rsRFA = $rsRFA->num_rows;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
			dolphintabs.init("menunav", 1);
		</script>
		<div class="casing" align="left">
			<?php
			if ($my_get['function'] == "add") {
				buildHeader("rfa", "RFCs", "rfa", "Add an RFC", null);
			} elseif ($my_get['function'] == "update") {
				buildHeader("rfa", "RFCs", "rfa", "Update an RFC", "Add an RFC");
			} else {
				buildHeader("rfa", "RFCs", "rfa", "View an RFC", "Add an RFC");
			}
			?>
			<form action="rfaSend.php" method="post" enctype="multipart/form-data" name="rfaForm">
				<table align="center" class="viewLarge" cellpadding="2" cellspacing="0">
					<tr>
						<td width="132" nowrap="nowrap" class="contrast"><label for="submittedBy">Submitted by:</label></td>
						<td colspan="3">
							<?php if ($my_get['function'] == "add") { ?>
								<select name="submittedBy" id="submittedBy" tabindex="1">
									<?php
									echo "<option value='' " . (('' == $_SESSION['employee']) ? ' selected="selected"' : '') . ">Select Employee</option>\n";
									while ($row = $rsEmployees->fetch_assoc()) {
										echo "<option " . (($row['employeeID'] == $_SESSION['employee']) ? 'selected="selected"' : '') . " value='{$row['employeeID']}'>{$row['displayName']}</option>\n";
									}
									?>
								</select>&nbsp;on&nbsp;
								<script>
									DateInput('dateSubmitted', true, 'YYYY-MM-DD');
								</script>at&nbsp;&nbsp;<input type="text" name="timeSubmitted" id="timeSubmitted" tabindex="2" value="<?php echo date('H:i'); ?>" size="4" maxlength="5" />
								<?php
							} else {
								echo $row_rsRFA['submittedBy'] . "&nbsp;on&nbsp;" . $row_rsRFA['dateSubmitted'] . "&nbsp;at&nbsp;" . $row_rsRFA['timeSubmitted'];
							}
							?>&nbsp;UTC</td>
					</tr>
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
						<td width="190"><?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) {
								?>
								<select name="application" id="application" tabindex="5">
									<option value="">Select Application</option>
									<?php
									while ($row = $rsApplications->fetch_assoc()) {
										echo "<option value='{$row['applicationID']}'" . ($row['applicationID'] == $row_rsRFA['applicationID'] ? ' selected="selected"' : '') . ">{$row['application']}</option>";
									}
									?>
								</select><?php requiredField(); ?>
								<?php
							} else {
								echo $row_rsRFA['application'];
							}
							?></td>

						<td width="120" class="contrast"><label for="subapplication">Subapplication:</label></td>
						<td><?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) {
								?>
								<select name="subapplication" id="subapplication" tabindex="6">
									<option value="">Select Subapplication</option>
									<?php
									while ($row = $rsSubapplications->fetch_assoc()) {
										echo "<option value='{$row['subapplicationID']}'" . ($row['subapplicationID'] == $row_rsRFA['subapplicationID'] ? ' selected="selected"' : '') . ">{$row['subapplication']}</option>";
									}
									?>
								</select><?php requiredField(); ?>
								<?php
							} else {
								echo $row_rsRFA['subapplication'];
							}
							?></td>
					</tr>
					<tr>
						<td class="contrast"><label for="layer">Layer:</label></td>
						<td colspan="3"><?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) {
								?>
								<select name="layer" id="layer" tabindex="7">
									<option value="">Select Layer</option>
									<?php
									while ($row = $rsLayers->fetch_assoc()) {
										echo "<option value='{$row['layerID']}'" . ($row['layerID'] == $row_rsRFA['layerID'] ? ' selected="selected"' : '') . ">{$row['layer']}</option>";
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
						<td colspan="3"><?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
								<select name="requestOrigin" id="requestOrigin" tabindex="8">
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
						<td nowrap="nowrap" colspan="2">Starting <?php if (($my_get['function'] == "add") || (($my_get['function'] == "update") && ($my_get['modWindow'] == "y") )) {
								?>
								<script>
									DateInput('windowStartDate', true, 'YYYY-MM-DD');
								</script>
								<?php
							} else {
								echo $row_rsRFA['windowStartDate'] . "&nbsp;";
							}
							?>at&nbsp;<?php
							if (($my_get['function'] == "add") || (($my_get['function'] == "update") && ($my_get['modWindow'] == "y") )) {
								formField("text", "windowStartTime", $row_rsRFA['windowStartTime'], "5", "5", null, null, "10", "n");
							} else {
								echo $row_rsRFA['windowStartTime'];
							}
							?>&nbsp;UTC<?php requiredField(); ?></td>
					</tr>
					<tr>
						<td class="contrast">&nbsp;</td>
						<td nowrap="nowrap" colspan="2">Ending <?php if (($my_get['function'] == "add") || (($my_get['function'] == "update") && ($my_get['modWindow'] == "y") )) {
								?>
								<script>
									DateInput('windowEndDate', true, 'YYYY-MM-DD');
								</script>
								<?php
							} else {
								echo $row_rsRFA['windowEndDate'] . "&nbsp;";
							}
							?>at&nbsp;<?php
							if (($my_get['function'] == "add") || (($my_get['function'] == "update") && ($my_get['modWindow'] == "y") )) {
								formField("text", "windowEndTime", $row_rsRFA['windowEndTime'], "5", "5", null, null, "11", "n");
							} else {
								echo $row_rsRFA['windowEndTime'];
							}
							?>&nbsp;UTC<?php requiredField(); ?></td>
						<?php if ($my_get['function'] != "add") { ?>
							<td>
								<a class='btn btn-app' href="rfa.php?function=update&amp;rfa=<?php echo $my_get['rfa']; ?>&amp;modWindow=<?php
								if ((!isset($my_get['modWindow'])) || ($my_get['modWindow'] == "n")) {
									echo "y";
								} elseif ($my_get['modWindow'] == "y") {
									echo "n";
								}
								?>"><?php
										if ($my_get['modWindow'] == "y") {
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
						<td valign="top"><?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) {
							?><select name="status" id="status" tabindex="13">
									<option value="Pending Approval">Pending Approval</option>
									<?php if ($_SESSION['group'] == "1") { ?>

										<option value="">Select Status</option>
										<?php
										$select_selected = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
										$helper_select = array(
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
											echo "<option value='$key' " . (($row_rsRFA['status'] == $key) ? "selected='selected'" : '') . ">$value</option>";
										}
										?>

									<?php } ?></select><?php
							} else {
								echo $row_rsRFA['status'];
								if ($row_rsRFA['status'] != "Pending Approval") {
									echo "&nbsp;by&nbsp;" . $row_rsRFA['reviewer'];
								}
							}
							?></td>
						<td class="contrast" valign="top"><label for="comments">Comments (Include Requester):</label></td>
						<td valign="top"><?php //formField("textarea", "comments", $row_rsRFA['comments'], "32", null, "5", "virtual", "14", "n");                           ?>
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
							<?php if ($my_get['function'] != "add") { ?><br /><a style="font-weight: bold;" href="../maintenances/maintenanceAdd.php?function=add&amp;rfa=<?php echo $my_get['rfa']; ?>&amp;module=maintenance">Generate Maintenance Notification</a><br />
							<?php } ?>
							<?php sentSuccessful("RFC sent successfully!"); ?></td></tr>
				</table>
				<?php if ($my_get['function'] == "add") { ?>
					<input type="hidden" name="MM_insert" value="rfaAdd" />
				<?php } elseif ($my_get['function'] == "update") { ?>
					<input type="hidden" name="MM_update" value="rfaUpdate" />
					<input type="hidden" name="changeRequestID" value="<?php echo $row_rsRFA['changeRequestID']; ?>" />
					<input type="hidden" name="reviewedBy" value="<?php echo $_SESSION['employee']; ?>" />
				<?php } ?>
			</form>
			<?php buildFooter("0"); ?>
		</div>
	</body>
</html>