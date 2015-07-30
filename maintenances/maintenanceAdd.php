<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
session_start();

$query_rsEngineers = "SELECT employeeID, engineer, lastName, displayName FROM employees WHERE employees.engineer='y' ORDER BY displayName ASC";
$rsEngineers = $conn->query($query_rsEngineers) or die("<div class='alert alert-danger' role='alert'>{$conn->error}</div>");
//$row_rsEngineers = $rsEngineers->fetch_assoc();
//$totalRows_rsEngineers = $rsEngineers->num_rows;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("Maintenance Notification"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		  <?php build_header(); ?>

		  <script src="../js/bootstrap-datepicker.js"></script>
		  <link rel="stylesheet" href="../css/datepicker.css"/>
		  <!--
		  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"/>
		  -->
		  <script src="../js/validator.js"></script>

	 </head>
	 <body class="skin-blue layout-top-nav">

		  <div class="wrapper">
				<header class="main-header">
					 <?php build_navbar($conn, 3); ?>
				</header> 
		  </div>

		  <div class="content-wrapper">

				<div class="container-fluid">

					 <?php
					 buildNewHeader('maintenances.php', 'Maintenance Notifications', "Add a Maintenance Notification");
					 ?>

					 <div class='row'>
						  <div class='col-md-2'></div>
						  <div class='col-md-8'>

								<div class='box box-primary'>
									 <div class='box-header with-border'>
										  <h3 class="box-title">Add a Maintenance Notification</h3>
									 </div>
									 <div class='box-body'>

										  <form class="form-horizontal" action="maintenanceSend.php" method="POST" enctype="multipart/form-data" name="maintenanceNotif1" id="maintenanceNotif1">

												<div class="form-group">
													 <label for='startdate' class="control-label col-xs-2">Start Date:</label>
													 <div class="col-xs-4">
														  <div class="input-group">
																<span class="input-group-addon" onclick='opendatepicker();'><span class="glyphicon glyphicon-calendar"></span></span>
																<input type="date" id="startDate" name='startDate' class="form-control" value="<?php echo date('Y-m-d') ?>" required/>
														  </div>
													 </div>
													 <div class="col-xs-6">&nbsp;</div>
												</div>
												<div class="form-group">
													 <label for='startTime' class="control-label col-xs-2">Start Time:</label>
													 <div class='col-xs-2'>
														  <div class="input-group">
																<input type="text" name="startHour" id="startHour" maxlength="2" class='form-control' value='<?php echo date('H'); ?>' required/>
																<span class="input-group-addon">&nbsp;<strong>:</strong>&nbsp;</span>
														  </div>
													 </div>
													 <div class='col-xs-2'>
														  <div class="input-group">
																<input type="text" name="startMinute" id="startMinute" maxlength="2" class='form-control' value='<?php echo date('i'); ?>' required/>
																<span class="input-group-addon">UTC</span>
														  </div>
													 </div>
													 <label for='estHours' class="control-label col-xs-2">Estimated Duration:</label>
													 <div class='col-xs-2'>
														  <div class="input-group">
																<span class="input-group-addon">Hour(s)</span>
																<input type="text" name="estHours" id="estHours" maxlength="2"  class='form-control' value='00' required/>
														  </div>
													 </div>
													 <div class='col-xs-2'>
														  <div class="input-group">
																<span class="input-group-addon">Minute(s)</span>
																<input type="text" name="estMins" id="estMins" maxlength="2" tabindex="2" class='form-control' value='30' required/>
														  </div>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='reason' class="control-label col-xs-2">Reason:</label>
													 <div class="col-xs-10">
														  <input id='reason' name='reason' value='' class='form-control' placeholder='Reason' required/>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='customerImpact' class="control-label col-xs-2">Customer Impact:</label>
													 <div class="col-xs-10">
														  <input id='customerImpact' name='customerImpact' value='' class='form-control' placeholder='Customer Impact' required/>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='nocImpact' class="control-label col-xs-2">NOC Impact:</label>
													 <div class="col-xs-10">
														  <input id='nocImpact' name='nocImpact' value='' class='form-control' placeholder='NOC Impact' required/>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='engineer' class='control-label col-xs-2'>Engineer</label>
													 <div class="col-xs-4">
														  <select name="engineer" id="engineer" class='form-control'>
																<?php
																while ($row_rsEngineers = $rsEngineers->fetch_assoc()) {
																	echo "<option value='{$row_rsEngineers['employeeID']}'" . (($row_rsEngineers['employeeID'] == $_SESSION['employee']) ? "selected ='selected'" : '') . ">{$row_rsEngineers['displayName']}</option>\n";
																}
																?>
														  </select>
													 </div>
												</div>
												<div class='form-group'>
													 <label for='prodChanges' class="control-label col-xs-2">Production Changes:</label>
													 <div class="col-xs-10">
														  <textarea name='prodChanges' id='prodChanges' class='form-control' rows="5" ><?php echo $row_rsMaintenanceNotif['prodChanges']; ?></textarea>
													 </div>
												</div>

												<div class='form-group'>
													 <label for='cc' class='control-label col-xs-2'>Email Recipients</label>
													 <div class="col-xs-5">
														  <div class="btn-group btn-group-justified" data-toggle="buttons">
																<label class="btn btn-default">
																	 <input type="checkbox" name="prodOps" id="prodOps"/>Tech Support
																</label>
																<label class="btn btn-default">
																	 <input type="checkbox" name="noc" id="noc"/>Product Dev
																</label>
																<label class="btn btn-default">
																	 <input type="checkbox" name="syseng" id="syseng"/>Sales
																</label>
																<label class="btn btn-default">
																	 <input type="checkbox" name="neteng" id="neteng"/>Projects
																</label>
														  </div>
													 </div>
													 <div class="col-xs-5">
														  <div class="input-group">
																<span class="input-group-addon">CC:</span>
																<input type="text" class="form-control" value="" name='cc' id='cc' placeholder="Carbon Copy"/>
														  </div>
													 </div>
												</div>
									 </div>
									 <div class="box-footer">

										  <div class="form-group">
												<div class="col-xs-offset-2 col-xs-10">
													 <button type="submit" class="btn btn-primary"><span class='glyphicon glyphicon-save'></span>&nbsp;Submit Maintenance Notification</button>
													 <?php sentSuccessful("Maintenance Notification submitted successfully!"); ?>
												</div>
										  </div>

										  <input type="hidden" name="MM_insert" value="maintenanceNotif1"/>
										  </form>
										  <script>
                                   $(function () {
                                       $("#startDate").datepicker();
                                   });
                                   function opendatepicker() {
                                       $("#startDate").datepicker("show");
                                   }
										  </script>
									 </div> <!-- /.box-body -->
								</div><!-- /.box -->
						  </div>
						  <div class='col-md-2'></div>
					 </div>

				</div> <!-- /.container-fluid -->
		  </div> <!-- /.content-wraper -->
		  <?php build_footer(); ?>
	 </body>
</html>