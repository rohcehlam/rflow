<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$label_colors = array(
	"Open" => 'primary',
	"Analysis" => 'info',
	"Closed" => 'success',
	"In Progress" => 'default',
	"On Hold" => 'warning',
	"Returned" => 'danger',
	"Closed - Sucessful" => 'success',
	"Closed - Failed" => 'danger',
	"Completed" => 'success',
	"Completed - Under review" => 'warning',
);

function priority_icon($priority) {
	switch ($priority) {
		case 'Fire':
			?>
			<span class='label bg-red' title="Fire"><label class='glyphicon glyphicon-fire'></label></span>
			<?php
			break;
		case 'Hot':
			?>
			<span class='label bg-gray' title="High"><label class='glyphicon glyphicon-chevron-up text-red'></label></span>
			<?php
			break;
		case 'Medium':
			?>
			<span class='label bg-gray' title="Medium"><label class='glyphicon glyphicon-minus text-yellow'></label></span>
			<?php
			break;
		case 'Low':
			?>
			<span class='label bg-gray' title="Low"><label class='glyphicon glyphicon-chevron-down text-blue'></label></span>
			<?php
			break;
	}
}

$args = array(
	'application' => FILTER_SANITIZE_SPECIAL_CHARS,
	'category' => FILTER_SANITIZE_SPECIAL_CHARS,
	'department' => FILTER_SANITIZE_SPECIAL_CHARS,
	'engineer' => FILTER_SANITIZE_SPECIAL_CHARS,
	'status' => FILTER_SANITIZE_SPECIAL_CHARS,
	'id' => FILTER_SANITIZE_SPECIAL_CHARS,
	'subject' => FILTER_SANITIZE_SPECIAL_CHARS,
	'ticket' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);
$my_server = filter_input_array(INPUT_SERVER, array(
	'QUERY_STRING' => FILTER_SANITIZE_SPECIAL_CHARS,
	'HTTP_HOST' => FILTER_SANITIZE_SPECIAL_CHARS,
	'PHP_SELF' => FILTER_SANITIZE_SPECIAL_CHARS,
	), true);

$currentPage = $my_server["PHP_SELF"];

$where = "WHERE (escalations.status<>'Returned' AND escalations.status<>'Closed')";
$filter_text = '&nbsp;';
if (isset($my_get['application'])) {
	$where = " WHERE escalations.applicationID={$my_get['application']}";
	$result = $conn->query("SELECT application FROM applications WHERE applicationID={$my_get['application']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Application: </em>{$row['application']}\n";
}
if (isset($my_get['category'])) {
	$where = " WHERE escalations.categoryID={$my_get['category']}";
	$result = $conn->query("SELECT reportType FROM reporttypes WHERE reportTypeID={$my_get['category']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Category: </em>{$row['reportType']}\n";
}
if (isset($my_get['department'])) {
	$where = " WHERE escalations.deptID={$my_get['department']}";
	$result = $conn->query("SELECT department FROM departments WHERE departmentID={$my_get['department']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Department: </em>{$row['department']}\n";
}
if (isset($my_get['engineer'])) {
	$where = " WHERE escalations.assignedTo={$my_get['engineer']}";
	$result = $conn->query("SELECT displayName FROM employees WHERE employeeID={$my_get['engineer']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Engineer: </em>{$row['displayName']}\n";
}
if (isset($my_get['status'])) {
	$where = " WHERE escalations.status ='{$my_get['status']}'";
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Status: </em>{$my_get['status']}\n";
}
if (isset($my_get['id'])) {
	$where = " WHERE escalations.escalationID={$my_get['id']}";
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>ID#</em>{$my_get['id']}\n";
}
if (isset($my_get['subject'])) {
	$where = " WHERE escalations.subject like('%{$my_get['subject']}%')";
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Subject like: </em>{$my_get['subject']}\n";
}
if (isset($my_get['ticket'])) {
	$where = " WHERE escalations.Ticket={$my_get['ticket']}";
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Ticket=</em>{$my_get['ticket']}\n";
}

$query_rsEscalations = str_replace('%%where%%', $where, "SELECT DATEDIFF(CURDATE(),dateEscalated) AS rowcolor, escalations.escalationID, DATE_FORMAT(dateEscalated, '%Y-%m-%d') as dateEscalated"
	. ", DATE_FORMAT(dateClosed, '%Y-%m-%d') as dateUpdated, applications.application, reporttypes.reportType AS category, escalations.subject, escalations.priority"
	. ", employees.displayName AS receiver, escalations.status, escalations.ticket, escalations.priority"
	. " FROM escalations"
	. " LEFT JOIN applications ON escalations.applicationID=applications.applicationID LEFT JOIN reporttypes"
	. " ON escalations.categoryID=reporttypes.reportTypeID"
	. " LEFT JOIN employees"
	. " ON escalations.assignedTo=employees.employeeID"
	. " %%where%%"
	. " ORDER BY escalations.dateEscalated ASC, escalations.escalationID ASC");

$rsEscalations = $conn->query($query_rsEscalations) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");

function escalation_level($colorme, $id) {
	if ($colorme == "Closed") {
		return "<small class=\"label bg-green\">Closed</small>";
	} else {
		if (($colorme >= "20")) {
			return "<span class=\"label bg-red\">$id</span>";
		} elseif (($colorme >= 12) && ($colorme < 20)) {
			return "<span class=\"label bg-orange\">$id</span>";
		} elseif (($colorme >= 2) && ($colorme < 12)) {
			return "<span class=\"label bg-yellow text-black\">$id</span>";
		} elseif (($colorme >= 0) && ($colorme < 2)) {
			return "<span class=\"label bg-gray\">$id</span>";
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("Support Requests"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(5); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'DashBoard'], ['url' => 'supportRequests.php', 'text' => 'Support Requests']], 'Support Requests', $filter_text) ?>

					 <section class="content">

						  <div class='box box-primary'>
								<div class='box-header with-border'>
									 <a class='btn btn-primary' href='supportRequest.php?function=add'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp;Add Support Request</a>
									 <div class="pull-right box-tools">
										  <div id="div_flt_nofilter">
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li class="active"><a href="#">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_category')">Category</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_department')">Department</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_id')">ID</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																</ul>
														  </div>
														  <label class="form-control">No Filter</label>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_nofilter -->
										  <div id="div_flt_application" style='display: none;'>
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li class="active"><a href="#">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_category')">Category</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_department')">Department</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_id')">ID</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Application:&nbsp;</label>
														  <select id="input_div_flt_application" name="application" class="form-control">
																<?php
																$result = $conn->query("SELECT applicationID, application FROM applications ORDER BY application ASC");
																while ($row = $result->fetch_assoc()) {
																	echo "<option value='{$row['applicationID']}'" . ($my_get['application'] == $row['applicationID'] ? " selected='selected'" : '') . ">{$row['application']}</option>\n";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_application -->
										  <div id="div_flt_category" style='display: none;'>
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li class="active"><a href="#">Category</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_department')">Department</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_id')">ID</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Category:&nbsp;</label>
														  <select id="input_div_flt_category" name="category" class="form-control">
																<?php
																$result = $conn->query("SELECT reportTypeID, reportType FROM reporttypes ORDER BY reportType ASC");
																while ($row = $result->fetch_assoc()) {
																	echo "<option value='{$row['reportTypeID']}'" . ($my_get['reporttype'] == $row['reportTypeID'] ? " selected='selected'" : '') . ">{$row['reportType']}</option>\n";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_category -->
										  <div id="div_flt_department" style='display: none;'>
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_category')">Category</a></li>
																	 <li class="active"><a href="#">Department</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_id')">ID</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Department:&nbsp;</label>
														  <select id="input_div_flt_department" name="department" class="form-control">
																<?php
																$result = $conn->query("SELECT departmentID, department FROM departments ORDER BY department ASC");
																while ($row = $result->fetch_assoc()) {
																	echo "<option value='{$row['departmentID']}'" . ($my_get['department'] == $row['departmentID'] ? " selected='selected'" : '') . ">{$row['department']}</option>\n";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_department -->
										  <div id="div_flt_engineer" style='display: none;'>
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_category')">Category</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_department')">Department</a></li>
																	 <li class="active"><a href="#">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_id')">ID</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Engineer:&nbsp;</label>
														  <select id="input_div_flt_engineer" name="engineer" class="form-control">
																<?php
																$result = $conn->query("SELECT employeeID, displayName FROM employees ORDER BY displayName ASC");
																while ($row = $result->fetch_assoc()) {
																	echo "<option value='{$row['employeeID']}'" . ($my_get['engineer'] == $row['employeeID'] ? " selected='selected'" : '') . ">{$row['displayName']}</option>\n";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_engineer -->
										  <div id="div_flt_status" style='display: none;'>
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_category')">Category</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_department')">Department</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li class="active"><a href="#">Status</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_id')">ID</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Status:&nbsp;</label>
														  <select id="input_div_flt_status" name="status" class="form-control">
																<?php
																foreach (["Open", "Analysis", "Closed", "Closed - Sucessful", "Closed - Failed", "Completed", "Completed - Under review", "In Progress", "On Hold", "Returned"] as $data) {
																	$result = $conn->query("SELECT employeeID, displayName FROM employees ORDER BY displayName ASC");
																	//while ($row = $result->fetch_assoc()) {
																	//echo "<option value='{$row['employeeID']}'" . ($my_get['engineer'] == $row['employeeID'] ? " selected='selected'" : '') . ">{$row['displayName']}</option>\n";
																	echo "<option value='$data'" . ($my_get['status'] == $data ? " selected='selected'" : '') . ">$data</option>\n";
																}
																?>
														  </select>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_status -->
										  <div id="div_flt_subject" style='display: none;'>
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_category')">Category</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_department')">Department</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_id')">ID</a></li>
																	 <li class="active"><a href="#">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Subject:&nbsp;</label>
														  <input id="input_div_flt_subject" name="subject" value="<?php echo $my_get['subject']; ?>" size="16" class="form-control"/>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_subject -->
										  <div id="div_flt_id" style='display: none;'>
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_category')">Category</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_department')">Department</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
																	 <li class="divider"></li>
																	 <li class="active"><a href="#">ID</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">Ticket</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">ID:&nbsp;</label>
														  <input id="input_div_flt_id" name="id" value="<?php echo $my_get['id']; ?>" size="16" class="form-control"/>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_id -->
										  <div id="div_flt_ticket" style='display: none;'>
												<form class="form-inline" role="form">
													 <div class="input-group">
														  <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
																<ul class="dropdown-menu">
																	 <li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_category')">Category</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_department')">Department</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
																	 <li class="divider"></li>
																	 <li><a href="#" onclick="display_filter('div_flt_ticket')">ID</a></li>
																	 <li><a href="#" onclick="display_filter('div_flt_subject')">Subject</a></li>
																	 <li class="active"><a href="#">Ticket</a></li>
																</ul>
														  </div>
														  <label class="input-group-addon">Ticket:&nbsp;</label>
														  <input id="input_div_flt_ticket" name="ticket" value="<?php echo $my_get['ticket']; ?>" size="16" class="form-control"/>
														  <div class="input-group-btn">
																<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
														  </div>
													 </div>
												</form>
										  </div> <!-- /#div_flt_ticket -->
									 </div> <!-- /.box-tools -->
								</div>
								<div class='box-body'>

									 <table id='supportRequests_table' class="table table-striped table-bordered">
										  <thead>
												<tr>
													 <th>Date Requested</th>
													 <th>Last Updated</th>
													 <th>ID</th>
													 <th>Subject</th>
													 <?php if (!isset($my_get['application'])) { ?>
														 <th>Application</th>
													 <?php } ?>
													 <?php if (!isset($my_get['ticket'])) { ?>
														 <th>Ticket</th>
													 <?php } ?>
													 <?php if (!isset($my_get['engineer'])) { ?>
														 <th>Engineer</th>
													 <?php } ?>
													 <th>Status</th>
													 <?php sudoAuthData(null, null, "th", "edit", null); ?>
												</tr>
										  </thead>
										  <tbody>
												<?php while ($row_rsEscalations = $rsEscalations->fetch_assoc()) { ?>
													<tr>
														 <td><?php echo $row_rsEscalations['dateEscalated']; ?></td>
														 <td><?php echo $row_rsEscalations['dateUpdated']; ?></td>
														 <td class='text-center'>
															  <a href="supportRequest.php?supportRequest=<?php echo $row_rsEscalations['escalationID']; ?>&amp;function=view"><?php echo escalation_level($row_rsEscalations['rowcolor'], $row_rsEscalations['escalationID']) ?>
															  </a>
														 </td>
														 <td>
															  <a href="supportRequest.php?supportRequest=<?php echo $row_rsEscalations['escalationID']; ?>&function=view">
																	<?php priority_icon($row_rsEscalations['priority']) ?>
																	<?php echo $row_rsEscalations['subject']; ?>
															  </a>
														 </td>
														 <?php
														 //echo "<td><a href=\"supportRequest.php?supportRequest=" . $row_rsEscalations['escalationID'] . "&amp;function=view\">{$row_rsEscalations['subject']}</a></td>\n";
														 if (!isset($my_get['application'])) {
															 echo "<td>" . $row_rsEscalations['application'] . "</td>\n";
														 }
														 if (!isset($my_get['ticket'])) {
															 echo "<td>" . ($row_rsEscalations['ticket'] == "0" ? '-' : $row_rsEscalations['ticket']) . "</td>\n";
														 }
														 if (!isset($my_get['engineer'])) {
															 echo "<td nowrap=\"nowrap\">" . $row_rsEscalations['receiver'] . "</td>\n";
														 }
														 ?>
														 <td><?php echo $row_rsEscalations['status']; ?></td>
														 <?php sudoAuthData("supportRequest.php", "Update Support Request", "td", "edit", "function=update&amp;supportRequest=" . $row_rsEscalations['escalationID']); ?>
													</tr>
												<?php } ?>
										  </tbody>
									 </table>
									 <script type="text/javascript">
                               $(document).ready(function () {
                                   $('#supportRequests_table').dataTable({"order": [[2, 'asc']], "pageLength": 25});
                               });
                               function display_filter(filter) {
                                   $("#div_flt_nofilter").hide();
                                   $("#div_flt_application").hide();
                                   $("#div_flt_category").hide();
                                   $("#div_flt_department").hide();
                                   $("#div_flt_engineer").hide();
                                   $("#div_flt_status").hide();
                                   $("#div_flt_subject").hide();
                                   $("#div_flt_ticket").hide();
                                   $("#div_flt_id").hide();
                                   $("#" + filter).show();
                                   $("#input_" + filter).focus();
                               }
<?php echo isset($my_get['application']) ? "display_filter('div_flt_application');\n" : ''; ?>
<?php echo isset($my_get['category']) ? "display_filter('div_flt_category');\n" : ''; ?>
<?php echo isset($my_get['department']) ? "display_filter('div_flt_department');\n" : ''; ?>
<?php echo isset($my_get['engineer']) ? "display_filter('div_flt_engineer');\n" : ''; ?>
<?php echo isset($my_get['status']) ? "display_filter('div_flt_status');\n" : ''; ?>
<?php echo isset($my_get['subject']) ? "display_filter('div_flt_subject');\n" : ''; ?>
<?php echo isset($my_get['ticket']) ? "display_filter('div_flt_ticket');\n" : ''; ?>
<?php echo isset($my_get['id']) ? "display_filter('div_flt_id');\n" : ''; ?>
									 </script>
								</div> <!-- /.box-body -->
						  </div> <!-- /.box -->

					 </section>

				</div> <!-- /container -->
				<?php build_footer(); ?>
		  </div> <!-- /content-wrapper -->


	 </body>
</html>
