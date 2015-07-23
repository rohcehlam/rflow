<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$args = array(
	'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	'rfa' => FILTER_SANITIZE_SPECIAL_CHARS,
	'modWindow' => FILTER_SANITIZE_SPECIAL_CHARS,
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
		$varRFA_rsRFA = addslashes($my_get['rfa']);
	}
	$query_rsRFA = "SELECT changerequests.changeRequestID, employees1.displayName as submittedBy, employees2.displayName as reviewer, changerequests.reviewedBy"
		. ", DATE_FORMAT(dateSubmitted, '%m/%d/%Y') AS dateSubmitted, TIME_FORMAT(timeSubmitted,'%k:%i') AS timeSubmitted, changerequests.summary, changerequests.description"
		. ", changerequests.status, changerequests.comments, changerequests.requestOrigin, changerequests.requestOriginID, changerequests.flagged"
		. ", DATE_FORMAT(windowStartDate, '%m/%d/%Y') AS windowStartDate, TIME_FORMAT(windowStartTime,'%k:%i') AS windowStartTime"
		. ", DATE_FORMAT(windowEndDate, '%m/%d/%Y') AS windowEndDate, TIME_FORMAT(windowEndTime,'%k:%i') AS windowEndTime, changerequests.applicationID, applications.application"
		. ", changerequests.subapplicationID, subapplications.subapplication, changerequests.layerID, layers.layer, changerequests.risk"
		. " FROM changerequests"
		. " LEFT JOIN employees as employees1 ON changerequests.submittedBy=employees1.employeeID"
		. " LEFT JOIN employees AS employees2 ON changerequests.reviewedBy=employees2.employeeID"
		. " LEFT JOIN applications ON changerequests.applicationID=applications.applicationID"
		. " LEFT JOIN subapplications ON changerequests.subapplicationID=subapplications.subapplicationID"
		. " LEFT JOIN layers ON changerequests.layerID=layers.layerID WHERE changerequests.changeRequestID = $varRFA_rsRFA";
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
		  <?php build_header(); ?>

		  <script src="../js/bootstrap-datepicker.js"></script>
		  <link rel="stylesheet" href="../css/datepicker.css"/>
	 </head>
	 <body class="skin-blue layout-top-nav">

		  <div class="wrapper">
				<header class="main-header">
					 <?php build_navbar($conn, 1); ?>
				</header> 
		  </div>

		  <div class="content-wrapper">

				<div class="container-fluid">

					 <?php
					 buildNewHeader('rfas.php', 'RFCs', "{$my_get['function']} a RFC");
					 ?>

					 <div class='row'>
						  <div class='col-md-2'></div>
						  <div class='col-md-8'>
								<div class="box box-primary">
									 <div class="box-header with-border">
										  <h4><?php echo $my_get['function']; ?> a RFC</h4>
									 </div>
									 <div class="box-body">

										  <form class="form-horizontal" action="rfaSend.php" method="post" enctype="multipart/form-data" name="rfaForm">

												<div class="form-group">
													 <?php if ($my_get['function'] == "add") { ?>
														 <label for='submittedBy' class="control-label col-xs-2">Submitted by:</label>
														 <div class="col-xs-4">
															  <select name="submittedBy" id="submittedBy" class="form-control">
																	<?php
																	while ($row = $rsEmployees->fetch_assoc()) {
																		echo "<option " . (($row['employeeID'] == $_SESSION['employee']) ? 'selected="selected"' : '') . " value='{$row['employeeID']}'>{$row['displayName']}</option>\n";
																	}
																	?>
															  </select>
														 </div>
														 <label for='dateSubmitted' class="control-label col-xs-1">On:</label>
														 <div class="col-xs-2">
															  <div class="input-group">
																	<span class="input-group-addon" onclick='opendsdatepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
																	<input type="text" id="dateSubmitted" name='dateSubmitted' class="form-control" placeholder="<?php echo date('Y-m-d') ?>"/>
															  </div>
														 </div>
														 <div class="col-xs-3">
															  <div class="input-group">
																	<span class="input-group-addon">&nbsp;<strong>at: </strong>&nbsp;</span>
																	<input type="text" name="timeSubmitted" id="timeSubmitted" value="" class='form-control' placeholder='<?php echo date('H:i'); ?>' maxlength="5"/>
																	<span class="input-group-addon">&nbsp;<strong>UTC</strong>&nbsp;</span>
															  </div>
														 </div>
													 <?php } else { ?>
														 <label for='startdate' class="control-label col-xs-2">Submitted by:</label>
														 <div class="col-xs-4">
															  <div class="input-group">
																	<input type="text" name='submittedBy' class="form-control" value="<?php echo $row_rsRFA['submittedBy']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsRFA['submittedBy'];                   ?></p> -->
														 </div>
														 <label for='startdate' class="control-label col-xs-1">On:</label>
														 <div class="col-xs-2">
															  <div class="input-group">
																	<input type="text" name='dateSubmitted' class="form-control" value="<?php echo $row_rsRFA['dateSubmitted']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsRFA['dateSubmitted'];                   ?></p> -->
														 </div>
														 <div class="col-xs-3">
															  <div class="input-group">
																	<span class="input-group-addon">at:</span>
																	<input type="text" name='dateSubmitted' class="form-control" value="<?php echo $row_rsRFA['timeSubmitted']; ?>" readonly />
																	<span class="input-group-addon">UTC</span>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static">at:&nbsp;<?php // echo $row_rsRFA['timeSubmitted'];                   ?>&nbsp;UTC</p> -->
														 </div>
													 <?php } ?>
												</div>
												<div class='form-group'>
													 <label for='summary' class="control-label col-xs-2">Summary:</label>
													 <div class="col-xs-10">
														  <input id='summary' name='summary' value='<?php echo $row_rsRFA['summary']; ?>' class='form-control' placeholder='Summary'/>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='description' class="control-label col-xs-2">Description:</label>
													 <div class="col-xs-10">
														  <textarea name='description' id='description' class='form-control' rows="5" placeholder='Description of the change that will be made'><?php echo $row_rsRFA['description']; ?></textarea>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='application' class="control-label col-xs-2">Application:</label>
													 <div class="col-xs-4">
														  <?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
															  <select name="application" id="application"class='form-control'>
																	<?php
																	while ($row = $rsApplications->fetch_assoc()) {
																		echo "<option value='{$row['applicationID']}'" . ($row['applicationID'] == $row_rsRFA['applicationID'] ? ' selected="selected"' : '') . ">{$row['application']}</option>";
																	}
																	?>
															  </select>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" value="<?php echo $row_rsRFA['application']; ?>" class='form-control' readonly/>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsRFA['application'];     ?></p> -->
														  <?php } ?>
													 </div>
													 <label for='subapplication' class="control-label col-xs-2">Sub Application:</label>
													 <div class="col-xs-4">
														  <?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
															  <select name="subapplication" id="subapplication" class='form-control'>
																	<?php
																	while ($row = $rsSubapplications->fetch_assoc()) {
																		echo "<option value='{$row['subapplicationID']}'" . ($row['subapplicationID'] == $row_rsRFA['subapplicationID'] ? ' selected="selected"' : '') . ">{$row['subapplication']}</option>";
																	}
																	?>
															  </select>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" value="<?php echo $row_rsRFA['subapplication']; ?>" class='form-control' readonly/>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsRFA['subapplication'];      ?></p> -->
														  <?php } ?>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='layer' class="control-label col-xs-2">Layer:</label>
													 <div class="col-xs-4">
														  <?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
															  <select name="layer" id="layer" class='form-control'>
																	<?php
																	while ($row = $rsLayers->fetch_assoc()) {
																		echo "<option value='{$row['layerID']}'" . ($row['layerID'] == $row_rsRFA['layerID'] ? ' selected="selected"' : '') . ">{$row['layer']}</option>";
																	}
																	?>
															  </select>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" value="<?php echo $row_rsRFA['layer']; ?>" class='form-control' readonly/>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsRFA['layer'];    ?></p> -->
														  <?php } ?>
													 </div>
													 <div class="col-xs-2">&nbsp;</div>
													 <div class="col-xs-4">
														  &nbsp;
														  <?php if ($my_get['function'] != "add") { ?>
															  <a class='btn btn-primary' href="rfa.php?function=update&amp;rfa=<?php echo $my_get['rfa']; ?>&amp;modWindow=<?php
															  if ((!isset($my_get['modWindow'])) || ($my_get['modWindow'] == "n")) {
																  echo "y";
															  } elseif ($my_get['modWindow'] == "y") {
																  echo "n";
															  }
															  ?>"><span class="glyphicon glyphicon-check"></span>&nbsp;<?php echo ($my_get['modWindow'] == "y") ? "Cancel Update/Approve RFC" : "Update/Approve RFC"; ?></a>
															  <?php } ?>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='requestOrigin' class="control-label col-xs-2">Request Origin:</label>
													 <div class="col-xs-4">
														  <?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
															  <select name="requestOrigin" id="requestOrigin" class='form-control'>
																	<?php
																	foreach (['Ticket', 'Support Request', 'Emergency Request'] as $data) {
																		echo "<option value='$data'" . ($data == $row_rsRFA['requestOrigin'] ? ' selected="selected"' : '') . ">$data</option>\n";
																	}
																	?>
															  </select>
														  <?php } else { ?>
															  <div class="input-group">
																	<input type="text" value="<?php echo $row_rsRFA['requestOrigin']; ?>" class='form-control' readonly/>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsRFA['requestOrigin'];   ?></p> -->
														  <?php } ?>
													 </div>
													 <label for='requestOriginID' class="control-label col-xs-2">Origin ID:</label>
													 <div class="col-xs-4">
														  <input id='requestOriginID' name='requestOriginID' value='<?php echo $row_rsRFA['requestOriginID']; ?>' class='form-control' placeholder='Origin ID'/>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='windowStartDate' class="control-label col-xs-2">Change Window:</label>
													 <div class="col-xs-6">
														  <?php if (($my_get['function'] == "add") || (($my_get['function'] == "update") && ($my_get['modWindow'] == "y") )) { ?>
															  <div class="input-group">
																	<span class="input-group-addon">Starting</span>
																	<input type="text" id="windowStartDate" name='windowStartDate' class="form-control" placeholder="<?php echo date('Y-m-d') ?>"/>
																	<span class="input-group-addon" onclick='openwsdatepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
															  </div>
														  <?php } else { ?>
															  <div class="input-group">
																	<span class="input-group-addon">Starting</span>
																	<input type="text" name='windowEndDate' class="form-control" value="<?php echo $row_rsRFA['windowStartDate']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsRFA['windowStartDate']                 ?></p> -->
														  <?php } ?>
													 </div>
													 <div class="col-xs-4">
														  <div class="input-group">
																<span class="input-group-addon">&nbsp;<strong>at: </strong>&nbsp;</span>
																<input type="text" name="windowStartTime" id="windowStartTime" value="" class='form-control' placeholder='<?php echo date('H:i'); ?>' maxlength="5"/>
																<span class="input-group-addon">&nbsp;<strong>UTC</strong>&nbsp;</span>
														  </div>
													 </div>
												</div>
												<div class='form-group'>
													 <div class="col-xs-2">&nbsp;</div>
													 <div class="col-xs-6">
														  <?php if (($my_get['function'] == "add") || (($my_get['function'] == "update") && ($my_get['modWindow'] == "y") )) { ?>
															  <div class="input-group">
																	<span class="input-group-addon">Ending</span>
																	<input type="text" id="windowEndDate" name='windowEndDate' class="form-control" placeholder="<?php echo date('Y-m-d') ?>"/>
																	<span class="input-group-addon" onclick='openwedatepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
															  </div>
														  <?php } else { ?>
															  <div class="input-group">
																	<span class="input-group-addon">Ending</span>
																	<input type="text" name='windowEndDate' class="form-control" value="<?php echo $row_rsRFA['windowEndDate']; ?>" readonly />
																	<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
															  <!-- <p class="form-control-static"><?php //echo $row_rsRFA['windowEndDate'];                  ?></p> -->
														  <?php } ?>
													 </div>
													 <div class="col-xs-4">
														  <div class="input-group">
																<span class="input-group-addon">&nbsp;<strong>at: </strong>&nbsp;</span>
																<input type="text" name="windowStartTime" id="windowStartTime" value="" class='form-control' placeholder='<?php echo date('H:i'); ?>' maxlength="5"/>
																<span class="input-group-addon">&nbsp;<strong>UTC</strong>&nbsp;</span>
														  </div>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='risk' class="control-label col-xs-2">Risk:</label>
													 <div class="col-xs-10">
														  <textarea name='risk' id='risk' class='form-control' rows="5" placeholder='Worst case scenario'><?php echo $row_rsRFA['risk']; ?></textarea>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='status' class="control-label col-xs-2">Status:</label>

													 <?php if (($my_get['function'] == "add") || ($my_get['function'] == "update")) { ?>
														 <div class="col-xs-4">
															  <select name="status" id="status" class="form-control">
																	<option value="Pending Approval">Pending Approval</option>
																	<?php
																	if ($_SESSION['group'] == "1") {
																		foreach (['Pre-approved', 'Approved', 'Declined', 'Returned', 'Submitted for CAB Approval', 'Approved by CAB', 'Rejected by CAB'
																		, 'Returned by CAB', 'Completed', 'Resolved'] as $data) {
																			echo "<option value='$data' " . (($row_rsRFA['status'] == $data) ? " selected='selected'" : '') . ">$data</option>";
																		}
																	}
																	?>
															  </select>
														 </div>
														 <div class="col-xs-6">&nbsp;</div>
													 <?php } else { ?>
														 <div class="col-xs-4">
															  <div class="input-group">
																	<input type="text" value="<?php echo $row_rsRFA['status']; ?>" class='form-control'/>
																	<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
															  </div>
														 </div>
														 <?php if ($row_rsRFA['status'] != "Pending Approval") { ?>
															 <label class="control-label col-xs-2">By:</label>
															 <div class="col-xs-4">
																  <div class="input-group">
																		<input type="text" value="<?php echo $row_rsRFA['reviewer']; ?>" class='form-control'/>
																		<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
																  </div>
															 </div>
														 <?php } else { ?>
															 <div class="col-xs-6">&nbsp;</div>
														 <?php } ?>
			<!-- <p class="form-control-static"><?php //echo $row_rsRFA['status'] . (($row_rsRFA['status'] != "Pending Approval") ? "&nbsp;by&nbsp;{$row_rsRFA['reviewer']}" : '');          ?></p> -->
													 <?php } ?>

												</div>
												<div class='form-group'>
													 <label for='comments' class="control-label col-xs-2">Comments:</label>
													 <div class="col-xs-10">
														  <textarea name='comments' id='comments' class='form-control' rows="5" placeholder='Include Requester'><?php echo $row_rsRFA['comments']; ?></textarea>
													 </div>
												</div>

												<div class="form-group">
													 <div class="col-xs-offset-2 col-xs-10">
														  <button type="submit" class="btn btn-primary"><span class='glyphicon glyphicon-save'></span>&nbsp;Send RFC</button>
														  <?php if ($my_get['function'] != "add") { ?>
															  <a class="btn btn-default" href="../maintenances/maintenanceAdd.php?function=add&amp;rfa=<?php echo $my_get['rfa']; ?>&amp;module=maintenance">
																	<span class="glyphicon glyphicon-lock"></span>&nbsp;Generate Maintenance Notification
															  </a>
														  <?php } sentSuccessful("RFC sent successfully!"); ?>
													 </div>
												</div>

												<?php if ($my_get['function'] == "add") { ?>
													<input type="hidden" name="MM_insert" value="rfaAdd" />
												<?php } elseif ($my_get['function'] == "update") { ?>
													<input type="hidden" name="MM_update" value="rfaUpdate" />
													<input type="hidden" name="changeRequestID" value="<?php echo $row_rsRFA['changeRequestID']; ?>" />
													<input type="hidden" name="reviewedBy" value="<?php echo $_SESSION['employee']; ?>" />
												<?php } ?>
										  </form>
										  <script>
                                   $(function () {
                                       $("#dateSubmitted").datepicker();
                                       $("#windowStartDate").datepicker();
                                       $("#windowEndDate").datepicker();
                                   });
                                   function opendsdatepicker() {
                                       $("#dateSubmitted").datepicker("show");
                                   }
                                   function openwsdatepicker() {
                                       $("#windowStartDate").datepicker("show");
                                   }
                                   function openwedatepicker() {
                                       $("#windowEndDate").datepicker("show");
                                   }
										  </script>
									 </div><!-- /.box-body -->
								</div><!-- /.box -->
						  </div>
						  <div class='col-md-2'></div>
					 </div>

				</div> <!-- /container -->
		  </div> <!-- /content-wrapper -->

		  <?php build_footer(); ?>
	 </body>
</html>