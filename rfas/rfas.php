<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');

$args = array(
	'application' => FILTER_SANITIZE_SPECIAL_CHARS,
	'engineer' => FILTER_SANITIZE_SPECIAL_CHARS,
	'status' => FILTER_SANITIZE_SPECIAL_CHARS,
	'summary' => FILTER_SANITIZE_SPECIAL_CHARS,
);
$my_get = filter_input_array(INPUT_GET, $args);

$currentPage = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS);
session_start();

$where = " WHERE changerequests.status='Pending Approval' OR changerequests.status='Submitted for CAB Approval' OR changerequests.status='Returned'";
$filter_text = '&nbsp;';
if (isset($my_get['application'])) {
	$where = " WHERE changerequests.applicationID={$my_get['application']}";
	$result = $conn->query("SELECT application FROM applications WHERE applicationID={$my_get['application']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Application: </em>{$row['application']}\n";
}
if (isset($my_get['engineer'])) {
	$where = " WHERE changerequests.submittedBy={$my_get['engineer']}";
	$result = $conn->query("SELECT displayName FROM employees WHERE employeeID={$my_get['engineer']}");
	$row = $result->fetch_assoc();
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Engineer: </em>{$row['displayName']}\n";
}
if (isset($my_get['status'])) {
	$where = " WHERE changerequests.status='{$my_get['status']}'";
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Status: </em>{$my_get['status']}\n";
}
if (isset($my_get['summary'])) {
	$where = " WHERE changerequests.summary like('%{$my_get['summary']}%')";
	$filter_text = "&nbsp;<span class='glyphicon glyphicon-filter'></span>&nbsp;Filter: <em>Summary like: </em>{$my_get['summary']}\n";
}

$query_rsChangeRequests = str_replace('%%where%%', $where, "SELECT changerequests.changeRequestID, changerequests.submittedBy, employees.displayName, DATE_FORMAT(dateSubmitted, '%m/%d/%Y') as dateSubmitted"
	. ", changerequests.summary, changerequests.applicationID, applications.application, changerequests.status, changerequests.requestOrigin, changerequests.requestOriginID"
	. ", changerequests.flagged, DATE_FORMAT(windowStartDate, '%m/%d/%Y') as windowStartDate"
	. " FROM changerequests"
	. " LEFT JOIN employees ON changerequests.submittedBy=employees.employeeID"
	. " LEFT JOIN applications ON changerequests.applicationID=applications.applicationID"
	. " %%where%%"
	. " ORDER BY windowStartDate DESC, windowStartTime DESC");

$rsChangeRequests = $conn->query($query_rsChangeRequests) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
/*
  $totalRows_rsChangeRequests = filter_input(INPUT_GET, 'totalRows_rsChangeRequests', FILTER_SANITIZE_SPECIAL_CHARS);
  if (!$totalRows_rsChangeRequests) {
  $all_rsChangeRequests = $conn->query($query_rsChangeRequests) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
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
  $rsApps = $conn->query($query_rsApps) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
  $row_rsApps = $rsApps->fetch_assoc();
  $totalRows_rsApps = $rsApps->num_rows;

  //select employees for employee filter list
  $query_rsEngineers = "SELECT employeeID, displayName FROM employees WHERE employees.engineer ='y' ORDER BY displayName ASC";
  $rsEngineers = $conn->query($query_rsEngineers);
  $row_rsEngineers = $rsEngineers->fetch_assoc();
  $totalRows_rsEngineers = $rsEngineers->num_rows;
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php buildTitle("Change Requests"); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php build_header(); ?>
	</head>
	<body class="skin-blue layout-top-nav">

		<div class="wrapper">
			<header class="main-header">
				<?php build_navbar($conn, 1) ?>
			</header> 
		</div>

		<div class="content-wrapper">

			<div class="container-fluid">

				<?php
				buildNewHeader('rfas.php', 'RFCs', '', 'rfa.php', 'Add an RFC');
				?>

				<div class="row">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title">RFCs<?php echo $filter_text; ?></h3>

							<div class="box-tools pull-right">
								<div id="div_flt_nofilter">
									<form class="form-inline" role="form">
										<div class="input-group">
											<div class="input-group-btn">
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter&nbsp;<span class="caret"></span></button>
												<ul class="dropdown-menu">
													<li class="active"><a href="#">No Filter</a></li>
													<li class="divider"></li>
													<li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
													<li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
													<li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
													<li><a href="#" onclick="display_filter('div_flt_summary')">Summary</a></li>
												</ul>
											</div>
											<label class="form-control">No Filter</label>
											<div class="input-group-btn">
												<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
											</div>
										</div>
									</form>
								</div> <!-- /#div_flt_nofilter -->
								<div id="div_flt_application" style="display: none;">
									<form class="form-inline" role="form">
										<div class="input-group">
											<div class="input-group-btn">
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
												<ul class="dropdown-menu">
													<li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
													<li class="divider"></li>
													<li class="active"><a href="#">Application</a></li>
													<li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
													<li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
													<li><a href="#" onclick="display_filter('div_flt_summary')">Summary</a></li>
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
								<div id="div_flt_engineer" style="display: none;">
									<form class="form-inline" role="form">
										<div class="input-group">
											<div class="input-group-btn">
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
												<ul class="dropdown-menu">
													<li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
													<li class="divider"></li>
													<li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
													<li class="active"><a href="#">Engineer</a></li>
													<li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
													<li><a href="#" onclick="display_filter('div_flt_summary')">Summary</a></li>												</ul>
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
								<div id="div_flt_status" style="display: none;">
									<form class="form-inline" role="form">
										<div class="input-group">
											<div class="input-group-btn">
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
												<ul class="dropdown-menu">
													<li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
													<li class="divider"></li>
													<li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
													<li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
													<li class="active"><a href="#">Status</a></li>
													<li><a href="#" onclick="display_filter('div_flt_summary')">Summary</a></li>												</ul>
											</div>
											<label class="input-group-addon">Status:&nbsp;</label>
											<select id="input_div_flt_status" name="status" class="form-control">
												<?php
												foreach (['Pending Approval', 'Pre-approved', 'Approved', 'Declined', 'Returned', 'Submitted for CAB Approval', 'Approved by CAB', 'Rejected by CAB'
												, 'Returned by CAB', 'Completed', 'Resolved'] as $data) {
													echo "<option value='$data'" . ($data == $my_get['status'] ? " selected='selected'" : '') . ">$data</option>";
												}
												?>
											</select>
											<div class="input-group-btn">
												<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
											</div>
										</div>
									</form>
								</div> <!-- /#div_flt_status -->
								<div id="div_flt_summary" style="display: none;">
									<form class="form-inline" role="form">
										<div class="input-group">
											<div class="input-group-btn">
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Choose Filter:&nbsp;<span class="caret"></span></button>
												<ul class="dropdown-menu">
													<li><a href="#" onclick="display_filter('div_flt_nofilter')">No Filter</a></li>
													<li class="divider"></li>
													<li><a href="#" onclick="display_filter('div_flt_application')">Application</a></li>
													<li><a href="#" onclick="display_filter('div_flt_engineer')">Engineer</a></li>
													<li><a href="#" onclick="display_filter('div_flt_status')">Status</a></li>
													<li class="active"><a href="#">Summary</a></li>												</ul>
											</div>
											<label class="input-group-addon">Summary:&nbsp;</label>
											<input id="input_div_flt_summary" name="summary" value="<?php echo $my_get['summary']; ?>" size="16" class="form-control"/>
											<div class="input-group-btn">
												<button type="submit" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-filter"></span>&nbsp;Apply</button>
											</div>
										</div>
									</form>
								</div> <!-- /#div_flt_summary -->
							</div> <!-- /.box-tools -->

						</div>
						<div class="box-body">
							<table id='rfas_table' class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Date Submitted</th>
										<?php
										if (!isset($my_get['engineer'])) {
											echo "<th>Submitted By</th>\n";
										}
										?>
										<th>Summary</th>
										<?php
										if (!isset($my_get['application'])) {
											echo "<th>Application</th>\n";
										}
										?>
										<?php
										if (!isset($my_get['status'])) {
											echo "<th>Status</th>\n";
										}
										?>
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
											<?php
											if (!isset($my_get['engineer'])) {
												echo "<td>{$row_rsChangeRequests['displayName']}</td>\n";
											}
											?>
											<td><?php echo "<a href=\"rfa.php?function=view&amp;rfa=" . $row_rsChangeRequests['changeRequestID'] . "\">" . $row_rsChangeRequests['summary'] . "</a>"; ?></td>
											<?php
											if (!isset($my_get['application'])) {
												echo "<td>{$row_rsChangeRequests['application']}</td>\n";
											}
											?>
											<?php
											if (!isset($my_get['status'])) {
												echo "<td>{$row_rsChangeRequests['status']}</td>\n";
											}
											?>
											<td><?php echo $row_rsChangeRequests['windowStartDate']; ?></td>
											<?php sudoAuthData("rfa.php", "Update RFA", "td", "edit", "function=update&amp;rfa=" . $row_rsChangeRequests['changeRequestID']); ?>
										</tr>
									<?php } ?>
								</tbody>
							</table>
							<script type="text/javascript">
								$(document).ready(function() {
									$('#rfas_table').dataTable({"order": [[0, 'desc']], "pageLength": 25});
								});
								function display_filter(filter) {
									$("#div_flt_nofilter").hide();
									$("#div_flt_application").hide();
									$("#div_flt_engineer").hide();
									$("#div_flt_status").hide();
									$("#div_flt_summary").hide();
									$("#" + filter).show();
									$("#input_" + filter).focus();
								}
<?php echo isset($my_get['application']) ? "display_filter('div_flt_application');\n" : ''; ?>
<?php echo isset($my_get['engineer']) ? "display_filter('div_flt_engineer');\n" : ''; ?>
<?php echo isset($my_get['status']) ? "display_filter('div_flt_status');\n" : ''; ?>
<?php echo isset($my_get['summary']) ? "display_filter('div_flt_summary');\n" : ''; ?>
							</script>
						</div><!-- /.box-body -->
					</div><!-- /.box -->

				</div> <!-- /row -->

			</div> <!-- /container -->
		</div> <!-- /content-wrapper -->

		<?php build_footer(); ?>
	</body>
</html>


