<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	 'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'supportRequest' => FILTER_SANITIZE_SPECIAL_CHARS,
	 'category' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);

if ($my_get['function'] != "add") {
//Support Request
	$varEscalation_rsEscalations = "1";
	if (isset($my_get['supportRequest'])) {
		$varEscalation_rsEscalations = addslashes($my_get['supportRequest']);
	}
	$query_rsEscalations = "SELECT escalations.escalationID, DATE_FORMAT(dateEscalated, '%m/%d/%Y') AS dateEscalated, DATE_FORMAT(targetDate, '%m/%d/%Y') AS targetDate"
			  . ", TIME_FORMAT(timeEscalated,'%k:%i') AS timeEscalated, escalations.submittedBy, employees1.displayName AS escalator, escalations.applicationID"
			  . ", applications.application, escalations.categoryID, reporttypes.reportType AS category, escalations.subject, escalations.assignedTo, employees2.displayName AS receiver"
			  . ", escalations.status, escalations.ticket, escalations.priority, escalations.description, escalations.recreateSteps, escalations.whatWasTested"
			  . ", escalations.customerImpact, escalations.logs, DATE_FORMAT(dateClosed, '%m/%d/%Y') AS dateClosed, TIME_FORMAT(timeClosed,'%k:%i') AS timeClosed"
			  . ", escalations.addInfo, escalations.outcome, escalations.deptID, departments.department, escalations.customerID, customers.customer"
			  . " FROM escalations"
			  . " LEFT JOIN applications ON escalations.applicationID=applications.applicationID"
			  . " LEFT JOIN reporttypes ON escalations.categoryID=reporttypes.reportTypeID"
			  . " LEFT JOIN (employees AS employees1, employees AS employees2) ON (escalations.submittedBy=employees1.employeeID AND escalations.assignedTo=employees2.employeeID)"
			  . " LEFT JOIN departments ON escalations.deptID=departments.departmentID"
			  . " LEFT JOIN customers ON escalations.customerID=customers.customerID"
			  . " WHERE escalations.escalationID = $varEscalation_rsEscalations";
	$rsEscalations = $conn->query($query_rsEscalations) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
	$row_rsEscalations = $rsEscalations->fetch_assoc();
	$totalRows_rsEscalations = $rsEscalations->num_rows;
}

//Employees
$query_rsEmployees = "SELECT employees.employeeID, employees.lastName, employees.displayName FROM employees ORDER BY employees.displayName ASC";
$rsEmployees = $conn->query($query_rsEmployees) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsEmployees = $rsEmployees->fetch_assoc();
$totalRows_rsEmployees = $rsEmployees->num_rows;

//Departments
$query_rsDepartments = "SELECT departmentID, department FROM departments ORDER BY department ASC";
$rsDepartments = $conn->query($query_rsDepartments) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsDepartments = $rsDepartments->fetch_assoc();
$totalRows_rsDepartments = $rsDepartments->num_rows;

//Applications
$query_rsApplication = "SELECT applicationID, application FROM applications ORDER BY application ASC";
$rsApplication = $conn->query($query_rsApplication) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsApplication = $rsApplication->fetch_assoc();
$totalRows_rsApplication = $rsApplication->num_rows;

//Categories
if (!isset($_SESSION['MM_Username'])) {
	$query_rsCategories = "SELECT reportTypeID, reportType FROM reporttypes WHERE reportTypeID <> 15 ORDER BY reportType ASC";
	$rsCategories = $conn->query($query_rsCategories) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
	$row_rsCategories = $rsCategories->fetch_assoc();
	$totalRows_rsCategories = $rsCategories->num_rows;
} else {
	$query_rsCategories = "SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC";
	$rsCategories = $conn->query($query_rsCategories) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
	$row_rsCategories = $rsCategories->fetch_assoc();
	$totalRows_rsCategories = $rsCategories->num_rows;
}

//Engineers
$query_rsAssignedTo = "SELECT employeeID, displayName FROM employees WHERE engineer='y' AND active='t' ORDER BY displayName ASC";
$rsAssignedTo = $conn->query($query_rsAssignedTo) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsAssignedTo = $rsAssignedTo->fetch_assoc();
$totalRows_rsAssignedTo = $rsAssignedTo->num_rows;

//Customers
$query_rsCustomers = "SELECT customerID, customer FROM customers ORDER BY customer ASC";
$rsCustomers = $conn->query($query_rsCustomers) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
$row_rsCustomers = $rsCustomers->fetch_assoc();
$totalRows_rsCustomers = $rsCustomers->num_rows;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php buildTitle("a Support Request"); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php build_header(); ?>

		<script src="../js/bootstrap-datepicker.js"></script>
		<link rel="stylesheet" href="../css/datepicker.css"/>
	</head>
	<body class="skin-blue layout-top-nav">

		<div class="wrapper">
			<header class="main-header">
				<?php build_navbar($conn, 2); ?>
			</header> 
		</div>

		<div class="content-wrapper">

			<div class="container-fluid">

				<?php
				buildNewHeader('supportRequests.php', 'Support request', "{$my_get['function']} a Support Request");
				?>

				<div class='row'>
					<div class='col-md-2'></div>
					<div class='col-md-8'>
						<form class="form-horizontal" action="supportRequestSend.php" method="post" enctype="multipart/form-data" name="supportRequestForm" id="supportRequestForm">

							<?php if (($my_get['function'] == "update") || ($my_get['function'] == "view")) { ?>
								<div class="form-group">
									<label class="control-label col-xs-2">Update Support Request:</label>
									<?php if ($my_get['function'] == "update") { ?>
										<div class="col-xs-4">
											<div class="input-group">
												<span class="input-group-addon">Updated on:</span>
												<input type="text" id="dateUpdated" name='dateUpdated' class="form-control" placeholder="<?php echo date('Y-m-d') ?>"/>
												<span class="input-group-addon" onclick='opendsdatepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
											</div>
										</div>
										<div class="col-xs-3">
											<div class="input-group">
												<span class="input-group-addon">at:</span>
												<input type="text" id="timeUpdated" name='timeUpdated' class="form-control" placeholder="<?php echo date('H:i') ?>"/>
												<span class="input-group-addon">UTC</span>
											</div>
										</div>
										<div class="col-xs-3">
											<div class="input-group">
												<span class="input-group-addon">to:</span>
												<select name="status" id="status" class="form-control">
													<?php
													foreach (["Open", "Analysis", "Closed", "In Progress", "On Hold", "Returned"] as $data) {
														echo "<option value='$data'" . ($data == $row_rsEscalations['status'] ? ' selected="selected"' : '') . ">$data</option>\n";
													}
													?>
												</select>
											</div>
										</div>
									<?php } else { ?>
										<div class="col-xs-4">
											<p class="form-control-static"><?php echo $row_rsEscalations['dateClosed']; ?></p>
										</div>
										<div class="col-xs-3">
											<p class="form-control-static">at&nbsp;<?php echo $row_rsEscalations['timeClosed']; ?>&nbsp;UTC</p>
										</div>
										<div class="col-xs-3">
											<p class="form-control-static">
												<?php
												echo $row_rsEscalations['status'];
												if (isset($_SESSION['MM_Username'])) {
													echo " <a class='btn btn-default' href=\"supportRequest.php?supportRequest=" . $my_get['supportRequest'] . "&amp;function=update\" class=\"update\"> Add Comment </a>";
												}
												?>
											</p>
										</div>
									<?php } ?>
								</div>
								<div class="form-group">
									<label for='assignedTo' class="control-label col-xs-2">Engineer:</label>
									<div class="col-xs-4">
										<?php if ($my_get['function'] == "update") { ?>
											<select name="assignedTo" id="assignedTo" class="form-control">
												<?php
												while ($row_rsAssignedTo = $rsAssignedTo->fetch_assoc()) {
													echo "<option value='{$row_rsAssignedTo['employeeID']}'" . (($row_rsAssignedTo['employeeID'] == $row_rsEscalations['assignedTo']) ? ' selected="selected"' : '') . ">{$row_rsAssignedTo['displayName']}</option>\n";
												}
												?>
											</select>
										<?php } else { ?>
											<p class="form-control-static"><?php echo $row_rsEscalations['receiver']; ?></p>
										<?php } ?>
									</div>
									<div class="col-xs-6">&nbsp;</div>
								</div>
								<div class='form-group'>
									<label for='comments' class="control-label col-xs-2">Comments:</label>
									<div class="col-xs-10">
										<textarea name='comments' id='comments' class='form-control' rows="5" placeholder="Comments"><?php echo $row_rsEscalations['outcome']; ?></textarea>
									</div>
								</div>
								<div class='form-group'>
									<div class="col-xs-2">&nbsp;</div>
									<div class="col-xs-10">
										<?php if ($my_get['function'] == "update") { ?>
											<button type="submit" name="update" id="update" class="btn btn-primary"><span class='glyphicon glyphicon-save'></span>&nbsp;Update Support Request" </button>
											<?php
										} elseif (isset($my_get['sent'])) {
											sentSuccessful("Support Request updated successfully!");
										}
										?>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-xs-2">Support Request #:</label>
									<div class="col-xs-4">
										<p class="form-control-static"><?php echo $row_rsEscalations['escalationID']; ?></p>
									</div>
									<label class="control-label col-xs-2">Subject :</label>
									<div class="col-xs-4">
										<p class="form-control-static"><?php echo $row_rsEscalations['subject']; ?></p>
									</div>
								</div>
							<?php } ?>

							<div class="form-group">
								<label for='submittedBy' class="control-label col-xs-2">Requested by:</label>
								<div class="col-xs-4">
									<?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
										<select name="submittedBy" id="submittedBy" class="form-control">
											<?php
											while ($row = $rsEmployees->fetch_assoc()) {
												echo "<option " . (($row['employeeID'] == $row_rsEscalations['submittedBy']) ? 'selected="selected"' : '') . " value='{$row['employeeID']}'>{$row['displayName']}</option>\n";
											}
											?>
										</select>
									<?php } else { ?>
										<p class="form-control-static"><?php echo $row_rsEscalations['escalator']; ?></p>
									<?php } ?>
								</div>
								<?php if ($my_get['function'] != "add") { ?>
									<label for='dateEscalated' class="control-label col-xs-1">On:</label>
									<div class="col-xs-2">
										<p class="form-control-static">on:&nbsp;<?php echo $row_rsEscalations['dateEscalated']; ?></p>
									</div>
									<div class="col-xs-3">
										<p class="form-control-static">at:&nbsp;<?php echo $row_rsEscalations['timeEscalated']; ?>&nbsp;UTC</p>
									</div>
								<?php } else { ?>
									<label for='dateEscalated' class="control-label col-xs-1">On:</label>
									<div class="col-xs-2">
										<div class="input-group">
											<span class="input-group-addon" onclick='openEddatepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
											<input type="text" id="dateEscalated" name='dateEscalated' class="form-control" placeholder="<?php echo date('m/d/Y') ?>"/>
										</div>
									</div>
									<div class="col-xs-3">
										<div class="input-group">
											<span class="input-group-addon">&nbsp;<strong>at: </strong>&nbsp;</span>
											<input type="text" name="timeEscalated" id="timeEscalated" value="" class='form-control' placeholder='<?php echo date('H:i'); ?>' maxlength="5"/>
											<span class="input-group-addon">&nbsp;<strong>UTC</strong>&nbsp;</span>
										</div>
									</div>
								<?php } ?>
							</div>
							<div class="form-group">
								<label for='dept' class="control-label col-xs-2">Assign to Department:</label>
								<div class="col-xs-4">
									<?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
										<select name="dept" id="dept" class="form-control">
											<?php
											while ($row_rsDepartments = $rsDepartments->fetch_assoc()) {
												echo "<option value='{$row_rsDepartments['departmentID']}'" . ($row_rsDepartments['departmentID'] == $row_rsEscalations['deptID'] ? ' selected="selected"' : '') . ">{$row_rsDepartments['department']}</option>\n;";
											}
											?>
										</select>
									<?php } else { ?>
										<p class="form-control-static">at:&nbsp;<?php echo $row_rsEscalations['department']; ?></p>
									<?php } ?>
								</div>
								<label for='ticket' class="control-label col-xs-2">Ticket:</label>
								<div class="col-xs-4">
									<input type="text" name="ticket" id="ticket" class='form-control' placeholder='Ticket'/>
								</div>
							</div>
							<div class="form-group">
								<label for='subject' class="control-label col-xs-2">Subject:</label>
								<div class="col-xs-10">
									<input type="text" name="subject" id="subject" class='form-control' placeholder='Subject'/>
								</div>
							</div>
							<div class="form-group">
								<label for='targetDate' class="control-label col-xs-2">Target Date:</label>
								<div class="col-xs-4">
									<?php if ((isset($my_get['modTarget']) == "y") || ($my_get['function'] == "add")) { ?>
										<div class="input-group">
											<span class="input-group-addon" onclick='opentDdatepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
											<input type="text" id="targetDate" name='targetDate' class="form-control" placeholder="<?php echo date('Y-m-d') ?>"/>
										</div>
									<?php } else { ?>
										<p class="form-control-static"><?php
											echo $row_rsEscalations['targetDate'] . "&nbsp;&nbsp;";
											if (isset($_SESSION['MM_Username'])) {
												?>
												<a class="btn btn-default" title="Update Target Date" href="supportRequest.php?function=update&amp;modTarget=y&amp;<?php echo ($my_get['category'] == "internal" ? 'category=internal&amp;' : '') ?>supportRequest=<?php echo $my_get['supportRequest']; ?>">Update</a>
												<?php
											}
											?> </p>

									<?php } ?>
								</div>
								<label for='priority' class="control-label col-xs-2">Priority:</label>
								<div class="col-xs-4">
									<?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
										<select name="priority" id="priority" class="form-control">
											<?php
											foreach (['Fire', 'Hot', 'Medium', 'Low'] as $data) {
												echo "<option value='$data'" . ($row_rsEscalations['priority'] == $data ? ' selected="selected"' : '') . ">$data</option>\n";
											}
											?>
										</select><?php
									} else {
										echo $row_rsEscalations['priority'];
									}
									?>
								</div>
							</div>
							<div class="form-group">
								<label for='customerImpact' class="control-label col-xs-2">Customer Impact:</label>
								<div class="col-xs-4">
									<input type="text" id="customerImpact" name='customerImpact' class="form-control" placeholder="Customer Impact"/>
								</div>
								<label for='customer' class="control-label col-xs-2">Customer:</label>
								<div class="col-xs-4">
									<?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
										<select name="customer" id="customer" class="form-control">
											<?php
											while ($row_rsCustomers = $rsCustomers->fetch_assoc()) {
												echo "<option value='{$row_rsCustomers['customerID']}'" . ($row_rsCustomers['customerID'] == $row_rsEscalations['customerID'] ? ' selected="selected"' : '') . ">{$row_rsCustomers['customer']}</option>\n";
											}
											?>
										</select>
									<?php } else { ?>
										<p class="form-control-static">at:&nbsp;<?php echo $row_rsEscalations['customer']; ?></p>
									<?php } ?>
								</div>
							</div>
							<div class="form-group">
								<label for='application' class="control-label col-xs-2">Application:</label>
								<div class="col-xs-4">
									<?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
										<select name="application" id="application" class="form-control">
											<?php
											while ($row_rsApplication = $rsApplication->fetch_assoc()) {
												echo "<option value='{$row_rsApplication['applicationID']}'" . ($row_rsApplication['applicationID'] == $row_rsEscalations['applicationID'] ? ' selected="selected"' : '') . ">{$row_rsApplication['application']}</option>\n";
											}
											?>
										</select>
									<?php } else { ?>
										<p class="form-control-static">at:&nbsp;<?php echo $row_rsEscalations['application']; ?></p>
									<?php } ?>
								</div>
								<label for='category' class="control-label col-xs-2">Category:</label>
								<div class="col-xs-4">
									<?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
										<select name="category" id="category" class="form-control">
											<?php
											while ($row_rsCategories = $rsCategories->fetch_assoc()) {
												echo "<option value='{$row_rsCategories['reportTypeID']}'" . ($row_rsCategories['reportTypeID'] == $row_rsEscalations['categoryID'] ? ' selected="selected"' : '') . ">{$row_rsCategories['reportType']}</option>\n";
											}
											?>
										</select>
									<?php } else { ?>
										<p class="form-control-static">at:&nbsp;<?php echo $row_rsEscalations['category']; ?></p>
									<?php } ?>
								</div>
							</div>
							<div class='form-group'>
								<label for='description' class="control-label col-xs-2">Description:</label>
								<div class="col-xs-10">
									<textarea name='description' id='description' class='form-control' rows="5" placeholder="Description"><?php echo $row_rsEscalations['description']; ?></textarea>
								</div>
							</div>
							<?php if (isset($my_get['category']) != "internal") { ?>
								<div class='form-group'>
									<label for='whatWasTested' class="control-label col-xs-2">Tests Performed:</label>
									<div class="col-xs-10">
										<textarea name='whatWasTested' id='whatWasTested' class='form-control' rows="5" placeholder="Tests Performed"><?php echo $row_rsEscalations['whatWasTested']; ?></textarea>
									</div>
								</div>
								<div class='form-group'>
									<label for='recreateSteps' class="control-label col-xs-2">Verification Steps:</label>
									<div class="col-xs-10">
										<textarea name='recreateSteps' id='recreateSteps' class='form-control' rows="5" placeholder="Verification Steps"><?php echo $row_rsEscalations['recreateSteps']; ?></textarea>
									</div>
								</div>
								<div class='form-group'>
									<label for='logs' class="control-label col-xs-2">Logs:</label>
									<div class="col-xs-10">
										<textarea name='logs' id='logs' class='form-control' rows="5" placeholder="Logs"><?php echo $row_rsEscalations['logs']; ?></textarea>
									</div>
								</div>
							<?php } ?>
							<div class='form-group'>
								<label for='addInfo' class="control-label col-xs-2">Additional Info:</label>
								<div class="col-xs-10">
									<textarea name='addInfo' id='addInfo' class='form-control' rows="5" placeholder="Additional Info"><?php echo $row_rsEscalations['addInfo']; ?></textarea>
								</div>
							</div>

							<?php if ($my_get['function'] == "add") { ?>
								<div class="form-group">
									<div class="col-xs-offset-2 col-xs-10">
										<button class="btn btn-primary" type="submit" name="add" id="add"><span class='glyphicon glyphicon-save'></span>&nbsp;Submit Support Request</button>
										<?php sentSuccessful("Support Request submitted successfully!"); ?>
									</div>
								</div>
							<?php } ?>

							<?php if ($my_get['function'] == "add") { ?>
								<input type="hidden" name="MM_insert" value="supportRequestAdd" />
								<input type="hidden" name="status" id="status" value="Open" />
							<?php } elseif ($my_get['function'] == "update") { ?>
								<input type="hidden" name="MM_update" value="supportRequestUpdate" />
								<input type="hidden" name="supportRequestID" id="supportRequestID" value="<?php echo $my_get['supportRequest']; ?>" />
							<?php } ?>
						</form>
						<script>
							$(function () {
								$("#dateUpdated").datepicker({dateFormat: 'yy-mm-dd'});
								$("#dateEscalated").datepicker();
								$("#targetDate").datepicker({dateFormat: 'yy-mm-dd'});
							});
							function opendsdatepicker() {
								$("#dateUpdated").datepicker("show");
							}
							function openEddatepicker() {
								$("#dateEscalated").datepicker("show");
							}
							function opentDdatepicker() {
								$("#targetDate").datepicker("show");
							}
						</script>
					</div>
					<div class='col-md-2'></div>
				</div>

			</div> <!-- /container -->
		</div> <!-- /content-wrapper -->

		<?php build_footer(); ?>
	</body>
</html>