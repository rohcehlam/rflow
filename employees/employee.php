<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');
include_once '../classes/employee.php';
session_start();

$args = array(
	'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	'employeeID' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("an Employee"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>

	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(1); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'DashBoard'], ['url' => 'employees.php', 'text' => 'Employees'], ['url' => '#', 'text' => ucwords($my_get['function']) . ' a Employee']], ucwords($my_get['function']) . ' a Employee') ?>

					 <section class="content">
						  <form class="form-horizontal" action="employeeSend.php" method="post" enctype="multipart/form-data" name="employeeForm">
								<div class="box box-primary">
									 <div class="box-header with-border">
										  <h3 class="box-title"><?php echo ucwords($my_get['function']); ?> a Employee</h3>
									 </div>
									 <div class="box-body">

										  <?php
										  $employee = new tEmployee($conn, $my_get['employeeID']);
										  if ($my_get['function'] == 'update' || $my_get['function'] == 'view' || $my_get['function'] == 'delete') {
											  $employee->load();
										  }
										  $employee->form($my_get['function']);
										  ?>

									 </div><!-- /.box-body -->
									 <div class='box-footer'>
										  <?php if ($my_get['function'] == 'add') { ?>
											  <div class="col-xs-offset-2 col-xs-6"><button class="btn btn-primary" type='submit'><i class="fa fa-save"></i>&nbsp;Create new Employee</button></div>
											  <input type="hidden" name="function" value="add"/>
										  <?php } ?>
										  <?php if ($my_get['function'] == 'update') { ?>
											  <div class="col-xs-offset-2 col-xs-6"><button class="btn btn-success" type='submit'><i class="fa fa-save"></i>&nbsp;Update Employee</button></div>
											  <input type="hidden" name="function" value="update"/>
										  <?php } ?>
										  <?php if ($my_get['function'] == 'delete') { ?>
											  <div class="col-xs-offset-2 col-xs-6"><button class="btn btn-danger" type='submit'><i class="fa fa-remove"></i>&nbsp;Delete Employee</button></div>
											  <input type="hidden" name="function" value="delete"/>
										  <?php } ?>

									 </div>
								</div> <!-- /.box -->
						  </form>
					 </section>

				</div> <!-- /container -->
				<?php build_footer(); ?>
				<script src="../js/bootstrap-datepicker.js"></script>
				<link rel="stylesheet" href="../css/datepicker.css"/>
		  </div> <!-- /content-wrapper -->

	 </body>
</html>