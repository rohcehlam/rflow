<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');
include_once('../classes/html_tools.php');
session_start();
check_permission();
$args = array(
	'process' => FILTER_SANITIZE_SPECIAL_CHARS,
	'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	'min' => FILTER_SANITIZE_SPECIAL_CHARS,
	'max' => FILTER_SANITIZE_SPECIAL_CHARS,
	'top' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);
//echo "SELECT process, `min`, `max`, top FROM rCron WHERE id={$my_get['process']}";
$result = $conn_dbevents->query("SELECT process, min, max, top FROM rCron WHERE id={$my_get['process']}") or die($conn->error);
if ($result->num_rows < 1) {
	header('Location: rcrons.php');
}
while ($row = $result->fetch_assoc()) {
	$process = $row['process'];
	$min = $row['min'];
	$max = $row['max'];
	$top = $row['top'];
}

if ($my_get['function'] == 'save') {
	$conn_dbevents->query("UPDATE rCron SET min={$my_get['min']}, max={$my_get['max']}, top={$my_get['top']} WHERE id={$my_get['process']}") or die($conn_dbevents->error);
	header("Location: rcrons.php?updated={$my_get['process']}");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("an rCron"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
		  <link rel="stylesheet" href="../js/daterangepicker/daterangepicker-bs3.css"/>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(7); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'Dashboard'], ['url' => 'rcrons.php', 'text' => 'rCrons'], ['url' => '#', 'text' => 'Update a rCron']], ucwords($my_get['function']) . ' a rCron', 'Process:&nbsp;' . $process) ?>

					 <section class="content">

						  <form class="form-horizontal">
								<div class="box box-default">
									 <div class="box-header with-border">
										  <h3 class="box-title"><?php echo ucwords($my_get['function']); ?> a rCron</h3>
										  <div class="pull-right box-tools">
												<button class="btn btn-default btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" style="margin-right: 5px;"><i class="fa fa-minus"></i></button>
										  </div>

									 </div>
									 <div class="box-body">
										  <?php
										  $writer = new thtml_writer($my_get['function']);
										  $writer->draw_input('min', 'Minimum Value', 'min', $min, 'Minimum Value');
										  $writer->draw_input('max', 'Maximum Value', 'max', $max, 'Maximum Value');
										  $writer->draw_input('top', 'Ceiling Value', 'top', $top, 'Ceiling Value');
										  ?>
									 </div>
									 <?php if ($my_get['function'] == 'update') { ?>
										 <div class="box-footer">
											  <div class="col-xs-offset-2">
													<button type="submit" class="btn btn-primary"><span class="fa fa-save"></span>&nbsp;Save Changes</button>
													<input type="hidden" name="function" value="save"/>
													<input type="hidden" name="process" value="<?php echo $my_get['process']; ?>"/>
											  </div>
										 </div>
									 <?php } ?>
								</div>
						  </form>
					 </section>
				</div>
				<?php build_footer(); ?>
		  </div>
	 </body>
</html>

