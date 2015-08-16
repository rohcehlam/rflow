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

		  <script src="../js/bootstrap-datepicker.js"></script>
		  <link rel="stylesheet" href="../css/datepicker.css"/>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(1); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'DashBoard'], ['url' => 'employees.php', 'text' => 'Employees'], ['url' => '#', 'text' => ucwords($my_get['function']) . ' a Employee']], ucwords($my_get['function']) . ' a Employee') ?>

					 <section class="content">
						  <form class="form-horizontal" action="employee.php" method="post" enctype="multipart/form-data" name="employeeForm">
								<div class="box box-primary">
									 <div class="box-header with-border">
										  <h3 class="box-title"><?php echo ucwords($my_get['function']); ?> a Employee</h3>
									 </div>
									 <div class="box-body">

										  <?php
										  $employee = new tEmployee($conn, $my_get['employeeID']);
										  if ($my_get['function'] == 'update' || $my_get['function'] == 'view') {
											  $employee->load();
										  }
										  $employee->form($my_get['function']);
										  ?>

									 </div><!-- /.box-body -->
									 <div class='box-footer'>

									 </div>
								</div> <!-- /.box -->
						  </form>
					 </section>

				</div> <!-- /container -->
				<?php build_footer(); ?>
		  </div> <!-- /content-wrapper -->

	 </body>
</html>