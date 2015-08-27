<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');

session_start();
check_permission();

$helper_trigger = array(
	0 => 'Row Count',
	1 => 'Update Time',
	2 => 'Data Length',
);

$args = array(
	'added' => FILTER_SANITIZE_SPECIAL_CHARS,
	'updated' => FILTER_SANITIZE_SPECIAL_CHARS,
	'deleted' => FILTER_SANITIZE_SPECIAL_CHARS,
	'error' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);

$query = <<< EOD
SELECT dbalarms.id AS `id`, ms_server.nombre, `server`, table_name, `field`, current_state, previous_state, last_change, cron_exp
	FROM dbalarms
	LEFT JOIN ms_server ON dbalarms.`server`=ms_server.id
EOD;
$result = $conn_dbevents->query($query) or die($conn_dbevents->error);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("Alarms"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">

				<?php build_navbar(); ?>
				<?php build_sidebar(6); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'Dashboard'], ['url' => '', 'text' => 'Alarms']], 'Alarms', $filter_text) ?>

					 <section class="content">

						  <?php if (isset($my_get['added'])) { ?>
							  <div class='box box-success'>
									<div class='box-header with-border'>
										 <h3 class='box-title'>Success!</h3>
									</div>
									<div class="box-body">
										 <p>New Alarm Successfully Created</p>
									</div>
							  </div>
						  <?php } ?>
						  <?php if (isset($my_get['updated'])) { ?>
							  <div class='box box-info'>
									<div class='box-header with-border'>
										 <h3 class='box-title'>Success!</h3>
									</div>
									<div class="box-body">
										 <p>Alarm Successfully Updated</p>
									</div>
							  </div>
						  <?php } ?>
						  <?php if (isset($my_get['deleted'])) { ?>
							  <div class='box box-warning'>
									<div class='box-header with-border'>
										 <h3 class='box-title'>Success!</h3>
									</div>
									<div class="box-body">
										 <p>Alarm Successfully Deleted</p>
									</div>
							  </div>
						  <?php } ?>
						  <?php if (isset($my_get['error'])) { ?>
							  <div class='box box-danger'>
									<div class='box-header with-border'>
										 <h3 class='box-title'>Error!</h3>
									</div>
									<div class="box-body">
										 <p><?php echo str_replace('\n', '<br/>', $my_get['error']); ?></p>
									</div>
							  </div>
						  <?php } ?>

						  <div class="box box-primary">
								<div class='box-header with-border'>
									 <div class='col-xs-6'>
										  <a class='btn btn-primary' href='alarm.php?function=add'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp;Add an Alarm</a>
									 </div>
								</div>
								<div class="box-body">
									 <table id="table_alarms" class="table table-striped table-bordered">
										  <thead>
												<tr>
													 <th>Server</th>
													 <th>Table Name</th>
													 <th>Field</th>
													 <th>Cron Expression</th>
													 <th>&nbsp;</th>
												</tr>
										  </thead>
										  <tbody>
												<?php
												while ($row = $result->fetch_assoc()) {
													?>
													<tr>
														 <td><?php echo $row['nombre']; ?></td>
														 <td>
															  <a href='alarm.php?function=view&id=<?php echo $row['id']; ?>'><?php echo $row['table_name']; ?></a>
															  <?php
															  if (isset($my_get['added']) && $my_get['added'] == $row['id']) {
																  echo "<span class='label label-success pull-right'><span class='glyphicon glyphicon-star'></span>&nbsp;New</span>\n";
															  }
															  ?>
															  <?php
															  if (isset($my_get['updated']) && $my_get['updated'] == $row['id']) {
																  echo "<span class='label label-info pull-right'><span class='glyphicon glyphicon-star'></span>&nbsp;Updated</span>\n";
															  }
															  ?>
														 </td>
														 <td><?php echo $helper_trigger[$row['field']]; ?></td>
														 <td><?php echo $row['cron_exp']; ?></td>
														 <td>
															  <a href='alarm.php?function=view&id=<?php echo $row['id']; ?>'><i class='fa fa-eye'></i></a>
															  &nbsp;<a href='alarm.php?function=update&id=<?php echo $row['id']; ?>'><i class='fa fa-pencil'></i></a>
															  &nbsp;<a href='alarm.php?function=delete&id=<?php echo $row['id']; ?>'><i class='fa fa-remove'></i></a>
														 </td>
													</tr>
													<?php
												}
												?>
										  </tbody>
									 </table>
									 <script type="text/javascript">
                               $(document).ready(function () {
                                   $('#table_alarms').dataTable({"order": [[0, "asc"], [1, "asc"]], "displayLength": 25, });
                               });
									 </script>
								</div>
						  </div>

					 </section>
				</div>
				<?php build_footer(); ?>
		  </div>
	 </body>
</html>
