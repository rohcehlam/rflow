<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');

$args = array(
	'process' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);

$query = <<<EOD
 SELECT type_proc, datetime_event, period_proc, processed_rec, total_rec, files
 FROM logmas_%%fecha%%
 WHERE PROCESS='%%process%%'
	 LIMIT 1000;
EOD;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("an rCron"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(7); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'DashBoard'], ['url' => 'rcrons.php', 'text' => 'rCrons'], ['url' => '#', 'text' => 'View a rCron']], 'View a rCron', 'Process:&nbsp;'.$my_get['process']) ?>

					 <section class="content">

						  <div class="box box-primary">
								<div class="box-header with-border">
									 <h3 class="box-title">View a rCron:&nbsp;<?php echo $my_get['process']; ?></h3>
								</div>
								<div class="box-body">
									 <table id="table_rcron" class='table table-striped table-bordered'>
										  <thead>
												<tr>
													 <th>type_proc</th>
													 <th>datetime_event</th>
													 <th>period_proc</th>
													 <th>processed_rec</th>
													 <th>total_rec</th>
													 <th>files</th>
												</tr>
										  </thead>
										  <tbody>
												<?php
												$result = $conn_dbevents->query(str_replace('%%fecha%%', date('Ym'), str_replace('%%process%%', $my_get['process'], $query)));
												while ($row = $result->fetch_assoc()) {
													?>
													<tr>
														 <td><?php echo $row['type_proc']; ?></td>
														 <td><?php echo $row['datetime_event']; ?></td>
														 <td><?php echo $row['period_proc']; ?></td>
														 <td><?php echo $row['processed_rec']; ?></td>
														 <td><?php echo $row['total_rec']; ?></td>
														 <td><?php echo $row['files']; ?></td>
													</tr>
													<?php
												}
												?>
										  </tbody>
									 </table>
									 <script type="text/javascript">
                               $(document).ready(function () {
                                   $('#table_rcron').dataTable({"order": [[4, "desc"], [1, "asc"], [0, "asc"]], "displayLength": 25, });
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

