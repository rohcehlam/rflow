<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');

session_start();
check_permission();

$cron_query = <<<EOD
SELECT id, `process`, `begin`, `end`, 
 IF(ISNULL(`end`), TIMEDIFF(NOW(), `begin`), TIMEDIFF(`end`, `begin`)) AS diff,
 `min`, `max`, period, processed_rec, total_rec, files, db,
 IF(NOT ISNULL(`end`), TIMEDIFF(NOW(), `end`), 0) AS `Idle`
FROM rCron
EOD;

function get_date($format, $time) {
	$temp = DateTime::createFromFormat('Y-m-d H:i:s', $time);
	return $temp->format($format);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("rCrons"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">

				<?php build_navbar(); ?>
				<?php build_sidebar(7); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'Dashboard'], ['url' => '', 'text' => 'rCrons']], 'rCrons', $filter_text) ?>

					 <section class="content">

						  <div class="box box-primary">

								<div class="box-body">
									 <table id="table_rcrons" class="table table-striped table-bordered">
										  <thead>
												<tr>
													 <th>Process</th>
													 <th>Last Run</th>
													 <th>Start Time</th>
													 <th>End Time</th>
													 <th>Running</th>
													 <th>Run Time</th>
													 <th>Files</th>
													 <th>Records</th>
													 <th>Idle</th>
												</tr>
										  </thead>
										  <tbody>
												<?php
												$result = $conn_dbevents->query(str_replace('%%table_name%%', date('Ym'), $cron_query)) or die("<div class='callout callout-danger lead'><h4>Error!</h4><p>{$conn->error}</p></div>");
												while ($row = $result->fetch_assoc()) {
													?>
													<tr>
														 <td><a href='rcron.php?process=<?php echo $row['id']; ?>'><?php echo $row['process']; ?></a></td>
														 <td><?php echo get_date('m/d/Y', $row['begin']); ?></td>
														 <td class='text-right'><?php echo get_date('H:i:s', $row['begin']); ?></td>
														 <td class='text-right'><?php echo $row['end'] != '' ? get_date('H:i:s', $row['end']) : '-'; ?></td>
														 <td><?php echo $row['end'] == '' ? "<span class='label label-info'>Running</span>" : "<span class='label label-success'>Finished!</span>"; ?></td>
														 <td class='text-right'><?php echo $row['end'] == '' ? "<span class='text-blue'>{$row['diff']}</span>" : $row['diff']; ?></td>
														 <td class='text-right'><?php echo $row['files']; ?></td>
														 <td class='text-right'><?php echo $row['processed_rec']; ?></td>
														 <td class='text-right'><?php echo $row['Idle']; ?></td>
													</tr>
													<?php
												}
												?>

										  </tbody>
									 </table>


								</div>
						  </div>

					 </section>
				</div>
				<?php build_footer(); ?>
				<script type="text/javascript">
               $(document).ready(function () {
                   $('#table_rcrons').dataTable({"order": [[4, "desc"], [1, "asc"], [0, "asc"]], "displayLength": 25, });
               });
				</script>
		  </div>
	 </body>
</html>
