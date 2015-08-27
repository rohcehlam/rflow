<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');
include_once '../classes/alarm.php';
session_start();
check_permission();

$args = array(
	'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	'id' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("an Alarm"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
		  <link href="../css/jquery-cron.css" rel="stylesheet" type="text/css"/>

	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(6); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'Dashboard'], ['url' => 'alarms.php', 'text' => 'Alarms'], ['url' => '#', 'text' => ucwords($my_get['function']) . ' an Alarm']], ucwords($my_get['function']) . ' an Alarm') ?>

					 <section class="content">
						  <form class="form-horizontal" action="alarmSend.php" method="post" enctype="multipart/form-data" name="alarmForm">
								<div class="box box-primary">
									 <div class="box-header with-border">
										  <h3 class="box-title"><?php echo ucwords($my_get['function']); ?> an Alarm</h3>
									 </div>
									 <div class="box-body">

										  <?php
										  $alarm = new tAlarma($conn_dbevents, $my_get['id']);
										  if ($my_get['function'] == 'update' || $my_get['function'] == 'view' || $my_get['function'] == 'delete') {
											  $alarm->load();
										  }
										  $alarm->form($my_get['function']);
										  ?>

									 </div><!-- /.box-body -->
									 <div class='box-footer'>
										  <?php if ($my_get['function'] == 'add') { ?>
											  <div class="col-xs-offset-2 col-xs-6"><button class="btn btn-primary" type='submit'><i class="fa fa-save"></i>&nbsp;Create new Alarm</button></div>
											  <input type="hidden" name="function" value="add"/>
										  <?php } ?>
										  <?php if ($my_get['function'] == 'update') { ?>
											  <div class="col-xs-offset-2 col-xs-6"><button class="btn btn-success" type='submit'><i class="fa fa-save"></i>&nbsp;Update Alarm</button></div>
											  <input type="hidden" name="function" value="update"/>
											  <input type="hidden" name="employeeID" value="<?php echo $my_get['employeeID']; ?>"/>
										  <?php } ?>
										  <?php if ($my_get['function'] == 'delete') { ?>
											  <div class="col-xs-offset-2 col-xs-6"><button class="btn btn-danger" type='submit'><i class="fa fa-remove"></i>&nbsp;Delete Alarm</button></div>
											  <input type="hidden" name="function" value="delete"/>
											  <input type="hidden" name="employeeID" value="<?php echo $my_get['employeeID']; ?>"/>
										  <?php } ?>

									 </div>
								</div> <!-- /.box -->
						  </form>
					 </section>

				</div> <!-- /container -->
				<?php build_footer(); ?>
				<script src="../js/jquery-cron.js"></script>
		  </div> <!-- /content-wrapper -->

	 </body>
</html>