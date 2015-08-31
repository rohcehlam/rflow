<?php
require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');
include_once '../classes/server.php';
session_start();
check_permission();

$args = array(
	'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	'id' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);

$server = new tServer($conn_dbevents, $my_get['id']);
if ($my_get['function'] == 'update' || $my_get['function'] == 'view' || $my_get['function'] == 'delete') {
	$server->load();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("a Server"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
		  <link href="../css/jquery-cron.css" rel="stylesheet" type="text/css"/>

	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">
				<?php build_navbar(); ?>
				<?php build_sidebar(8); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'Dashboard'], ['url' => 'servers.php', 'text' => 'Servers'], ['url' => '#', 'text' => ucwords($my_get['function']) . ' a Server']], ucwords($my_get['function']) . ' a Server', $server->get_description()); ?>

					 <section class="content">
						  <form class="form-horizontal" action="serverSend.php" method="post" enctype="multipart/form-data" name="serverForm" id="serverForm">
								<div class="box box-primary">
									 <div class="box-header with-border">
										  <h3 class="box-title"><?php echo ucwords($my_get['function']); ?> a Server</h3>
									 </div>
									 <div class="box-body">

										  <?php
										  $server->form($my_get['function']);
										  ?>

									 </div><!-- /.box-body -->
									 <input type="hidden" name="cron_exp" id="cron_exp" value=""/>
									 <div class='box-footer'>
										  <?php if ($my_get['function'] == 'add') { ?>
											  <div class="col-xs-offset-2 col-xs-6"><button class="btn btn-primary" type='submit'><i class="fa fa-save"></i>&nbsp;Create new Server</button></div>
											  <input type="hidden" name="function" value="add"/>
										  <?php } ?>
										  <?php if ($my_get['function'] == 'update') { ?>
											  <div class="col-xs-offset-2 col-xs-6"><button class="btn btn-success" type='submit'><i class="fa fa-save"></i>&nbsp;Update Server</button></div>
											  <input type="hidden" name="function" value="update"/>
											  <input type="hidden" name="id" value="<?php echo $my_get['id']; ?>"/>
										  <?php } ?>
										  <?php if ($my_get['function'] == 'delete') { ?>
											  <div class="col-xs-offset-2 col-xs-6"><button class="btn btn-danger" type='submit'><i class="fa fa-remove"></i>&nbsp;Delete Server</button></div>
											  <input type="hidden" name="function" value="delete"/>
											  <input type="hidden" name="id" value="<?php echo $my_get['id']; ?>"/>
										  <?php } ?>

									 </div>
								</div> <!-- /.box -->
						  </form>
					 </section>

				</div> <!-- /container -->
				<?php build_footer(); ?>
		  </div> <!-- /content-wrapper -->

	 </body>
</html>