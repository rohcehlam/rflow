<?php
error_reporting(E_ALL & ~E_NOTICE);

function buildNewHeader($rootPage, $rootPageText, $pageText = '', $destiny = '', $addText = '') {
	?>
	<div class="page-header">
		 <div class='row'>
			  <div class='col-xs-10'>
					<small>
						 <ul style='margin-top: 8px;' class='breadcrumb'>
							  <li><a href='../userPortals/myPortal.php'>Home</a></li>						
							  <?php if ($pageText != '') { ?>
								  <li><a href='<?php echo $rootPage; ?>'><?php echo $rootPageText; ?></a></li>
								  <li class='active'><?php echo $pageText; ?></li>
							  <?php } else { ?>
								  <li class='active'><?php echo $rootPageText; ?></li>
							  <?php } ?>
						 </ul>
					</small>
			  </div>
			  <div class='col-xs-2'>
					<?php if ($addText != '') { ?>
						<a style='margin-top: 12px;' class='btn btn-primary' href='<?php echo $destiny; ?>?function=add'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp;<?php echo $addText; ?></a>
					<?php } ?>
					&nbsp;</div>
			  <?php //buildHeader("statusReport", null, "statusReports", "Status Reports", "Add a Status Report"); ?>
		 </div>
	</div>
	<?php
}

function sudoAuth($linkedPage, $linkText, $icon) {
	global $sudo;
	$sudo = filter_input(INPUT_GET, 'sudo_auth');
	if (isset($sudo) && ($sudo == "admin")) {
		echo "<span class=\"input-group-btn\">\n";
		echo "<a class=\"btn btn-default\" href=\"{$linkedPage}.php?sudo=admin\"><span class=\"glyphicon {$icon}\"></span>&nbsp;{$linkText}</a>\n";
		echo "</span>\n";
		//echo "&nbsp;<img src=\"../images/icons/" . $icon . ".gif\" alt=\"" . $icon . "\" /><a title=\"" . $linkText . "\" href=\"" . $linkedPage . ".php?sudo_auth=admin\">" . $linkText . "</a>";
	} else {
		echo "<span class=\"input-group-btn\"><a class=\"btn btn-default\" href=\"#\" title=\"You're not authorized to use this option\"><span class=\"glyphicon glyphicon-tags\"></span>&nbsp;</a></span>\n";
	}
}

function sudoAuthData($linkedPage, $linkedText, $tdth, $icon, $param) {
	if (isset($_SESSION['MM_Username'])) {
		if ($tdth == "th") {
			echo "<th></th>\n";
		} elseif ($tdth == "td") {
			?>
			<td align="center">
				 <a href="<?php echo "{$linkedPage}?{$param}"; ?>">
					  <span class="glyphicon <?php echo (($icon == 'edit') ? 'glyphicon-edit' : 'glyphicon-remove-sign'); ?>"></span>
				 </a>
			</td>
			<?php
		}
	}
}

function sentSuccessful($message) {
	$my_get = filter_input_array(INPUT_GET, array('sent' => FILTER_SANITIZE_SPECIAL_CHARS,));
	if ((isset($my_get['sent'])) && ($my_get['sent'] == 'y')) {
		?>
		<div class="callout callout-success">
			 <h4>Success!</h4>
			 <p><?php echo $message; ?></p>
		</div>
		<?php
		//echo "<span class=\"label label-success\">&nbsp;::&nbsp;" . $message . "&nbsp;::&nbsp;</span>\n";
	}
}

function buildTitle($remainder, $company = "GEE:") {
	$my_get = filter_input_array(INPUT_GET, ['function' => FILTER_SANITIZE_SPECIAL_CHARS]);
	echo $company . '&nbsp;';
	if (isset($my_get['function'])) {
		echo ucwords($my_get['function']);
	}
	echo '&nbsp;' . $remainder;
}

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
	$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
	switch ($theType) {
		case "text":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
		case "long":
		case "int":
			$theValue = ($theValue != "") ? intval($theValue) : "NULL";
			break;
		case "double":
			$theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
			break;
		case "date":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
		case "defined":
			$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			break;
	}
	return $theValue;
}

function determineState($var, $trueState, $trueAction, $falseAction) {
	if ($var == $trueState) {
		echo $trueAction;
	} else {
		echo $falseAction;
	}
}

function build_header() {
	?>
	<link rel="shortcut icon" href="../images/logos/favicon.ico" type="image/x-icon" />

	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- Font Awesome Icons -->
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- Ionicons -->
	<link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />

	<!-- Theme style -->
	<link href="../css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
	<!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
	<link href="../css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />

	<!-- jQuery 2.1.4 -->
	<script src="../js/jQuery-2.1.4.min.js"></script>

	<!-- DATA TABLES -->
	<link href="../css/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

	<?php
}

function build_navbar($active = 0) {
	?>
	<header class="main-header">

		 <!-- Logo -->
		 <a href="#" class="logo">
			  <img alt="GEE" src="../images/GEE_White_Logo.png" height="48" />
		 </a>

		 <!-- Header Navbar -->
		 <nav class="navbar navbar-static-top" role="navigation">
			  <!-- Sidebar toggle button-->
			  <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Toggle navigation</span>
			  </a>
			  <!-- Navbar Right Menu -->
			  <div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						 <!-- maintenances: style can be found in dropdown.less-->
						 <li class="dropdown messages-menu">
							  <!-- Menu toggle button -->
							  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-wrench"></i>
									<span class="label label-default">0</span>
							  </a>
							  <ul class="dropdown-menu">
									<li class="header">Pending Maintenances</li>
									<li>
										 <!-- Inner Menu: contains the notifications -->
										 <ul class="menu">
											  <li><!-- start notification -->
													<a href="#">
														 <i class="fa fa-wrench text-gray"></i> 0 Pending Maintenances
													</a>
											  </li><!-- end notification -->
										 </ul>
									</li>
									<li class="footer"><a href="#">View all</a></li>
							  </ul>
						 </li><!-- /.maintenances-menu -->

						 <!-- Alarms Menu -->
						 <li class="dropdown notifications-menu">
							  <!-- Menu toggle button -->
							  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-bell-o"></i>
									<span class="label label-default">0</span>
							  </a>
							  <ul class="dropdown-menu">
									<li class="header">Active Alarms</li>
									<li>
										 <!-- Inner Menu: contains the notifications -->
										 <ul class="menu">
											  <li><!-- start notification -->
													<a href="#">
														 <i class="fa fa-bell-o"></i> 0 Active Alarms
													</a>
											  </li><!-- end notification -->
										 </ul>
									</li>
									<li class="footer"><a href="#">View all</a></li>
							  </ul>
						 </li>
						 <!-- Requests for my support Menu -->
						 <li class="dropdown tasks-menu">
							  <!-- Menu Toggle Button -->
							  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-bullhorn"></i>
									<span class="label label-default">0</span>
							  </a>
							  <ul class="dropdown-menu">
									<li class="header">Requests for my Support</li>
									<li>
										 <!-- Inner Menu: contains the notifications -->
										 <ul class="menu">
											  <li><!-- start notification -->
													<a href="#">
														 <i class="fa fa-bullhorn"></i> 0 Requests for my Support
													</a>
											  </li><!-- end notification -->
										 </ul>
									</li>
									<li class="footer">
										 <a href="#">View all tasks</a>
									</li>
							  </ul>
						 </li>
						 <!-- Unassigned Support Requests Menu -->
						 <li class="dropdown tasks-menu">
							  <!-- Menu Toggle Button -->
							  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-inbox"></i>
									<span class="label label-default">0</span>
							  </a>
							  <ul class="dropdown-menu">
									<li class="header">Unassigned Support Requests</li>
									<li>
										 <!-- Inner Menu: contains the notifications -->
										 <ul class="menu">
											  <li><!-- start notification -->
													<a href="#">
														 <i class="fa fa-inbox"></i> 0 Unassigned Support Requests
													</a>
											  </li><!-- end notification -->
										 </ul>
									</li>
									<li class="footer">
										 <a href="#">View all tasks</a>
									</li>
							  </ul>
						 </li>
						 <!-- Low Disk Servers Menu -->
						 <li class="dropdown tasks-menu">
							  <!-- Menu Toggle Button -->
							  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-database"></i>
									<span class="label label-default">0</span>
							  </a>
							  <ul class="dropdown-menu">
									<li class="header">Low Disk Space Servers</li>
									<li>
										 <!-- Inner Menu: contains the notifications -->
										 <ul class="menu">
											  <li><!-- start notification -->
													<a href="#">
														 <i class="fa fa-database"></i> 0 Servers with Low Disk Space
													</a>
											  </li><!-- end notification -->
										 </ul>
									</li>
									<li class="footer">
										 <a href="#">View all tasks</a>
									</li>
							  </ul>
						 </li>
						 <!-- User Account Menu -->
						 <li class="dropdown user user-menu">
							  <!-- Menu Toggle Button -->
							  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<!-- The user image in the navbar-->
									<img src="../images/nano_001.jpg" class="user-image" alt="User Image" />
									<!-- hidden-xs hides the username on small devices so only the image appears. -->
									<span class="hidden-xs">Orlando Jimenez</span>
							  </a>
							  <ul class="dropdown-menu">
									<!-- The user image in the menu -->
									<li class="user-header">
										 <img src="../images/nano_001.jpg" class="img-circle" alt="User Image" />
										 <p>
											  Orlando Jimenez - Senior Programmer
											  <small>There's No place like 127.0.0.1</small>
										 </p>
									</li>
									<!-- Menu Footer-->
									<li class="user-footer">
										 <div class="pull-left">
											  <a href="#" class="btn btn-default btn-flat">Profile</a>
										 </div>
										 <div class="pull-right">
											  <a href="../userPortals/myPortal.php?doLogout=true" class="btn btn-default btn-flat">Sign out</a>
										 </div>
									</li>
							  </ul>
						 </li>
					</ul>
			  </div>
		 </nav>
	</header>
	<?php
}

function build_sidebar($active = 0) {
	?>
	<aside class="main-sidebar">

		 <!-- sidebar: style can be found in sidebar.less -->
		 <section class="sidebar">

			  <!-- Sidebar user panel (optional) -->
			  <div class="user-panel">
					<div class="pull-left image">
						 <img src="../images/nano_001.jpg" class="img-circle" alt="User Image" />
					</div>
					<div class="pull-left info">
						 <p>Orlando Jimenez</p>
						 <!-- Status -->
						 <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
					</div>
			  </div>

			  <!-- Sidebar Menu -->
			  <ul class="sidebar-menu">
					<li class="header">HEADER</li>
					<!-- Optionally, you can add icons to the links -->
					<li <?php echo $active == 0 ? 'class="active"' : ''; ?>><a href="../userPortals/myPortal.php"><i class="fa fa-dashboard"></i> <span>DashBoard</span></a></li>
					<li <?php echo $active == 1 ? 'class="active"' : ''; ?>><a href="../employees/employees.php"><i class="fa fa-users"></i> <span>Employees</span></a></li>
					<li class='treeview<?php echo ($active == 2 || $active == 3 || $active == 4 || $active == 5) ? ' active' : ''; ?>'>
						 <a href='#'><i class='fa fa-stack-overflow'></i><span>Rflow</span> <i class="fa fa-angle-left pull-right"></i></a>
						 <ul class='treeview-menu'>
							  <li<?php echo $active == 2 ? ' class="active"' : ''; ?>><a href='../rfas/rfas.php'><i class='fa fa-cog'></i>RFC</a></li>
							  <li<?php echo $active == 3 ? ' class="active"' : ''; ?>><a href='../statusReports/statusReports.php'><i class='fa fa-flag'></i>Status Reports</a></li>
							  <li<?php echo $active == 4 ? ' class="active"' : ''; ?>><a href='../maintenances/maintenances.php'><i class='fa fa-wrench'></i>Maintenances</a></li>
							  <li<?php echo $active == 5 ? ' class="active"' : ''; ?>><a href='../supportRequests/supportRequests.php'><i class='fa fa-bullhorn'></i>Support Requests</a></li>
						 </ul>
					</li>
					<li class='treeview<?php echo ($active == 6 || $active == 7 || $active == 8 || $active == 9) ? ' active' : ''; ?>'>
						 <a href='#'><i class='fa fa-bell-o'></i><span>Alarms System</span> <i class="fa fa-angle-left pull-right"></i></a>
						 <ul class='treeview-menu'>
							  <li<?php echo $active == 6 ? ' class="active"' : ''; ?>><a href='#'><i class='fa fa-bell-o'></i>Alarms</a></li>
							  <li<?php echo $active == 7 ? ' class="active"' : ''; ?>><a href='../rcron/rcrons.php'><i class='fa fa-bolt'></i>rCron</a></li>
							  <li<?php echo $active == 8 ? ' class="active"' : ''; ?>><a href='#'><i class='fa fa-server'></i>Servers</a></li>
							  <li<?php echo $active == 9 ? ' class="active"' : ''; ?>><a href='#'><i class='fa fa-bomb'></i>CMQ</a></li>
						 </ul>
					</li>
					<li class='treeview'>
						 <a href='#'><i class='fa fa-user-plus'></i><span>Much More</span> <i class="fa fa-angle-left pull-right"></i></a>
						 <ul class='treeview-menu'>
							  <li><a href='#'><i class='fa fa-suitcase'></i>Projects</a></li>
							  <li><a href='#'><i class='fa fa-ticket'></i>Tickets</a></li>
							  <li><a href='#'><i class='fa fa-tasks'></i>Process</a></li>
						 </ul>
					</li>

			  </ul><!-- /.sidebar-menu -->
		 </section>
		 <!-- /.sidebar -->
	</aside>
	<?php
}

function build_footer() {
	?>
	<footer class="main-footer">
		 <!-- To the right -->
		 <div class="pull-right hidden-xs">
			  Rflow 2.1
		 </div>
		 <!-- Default to the left -->
		 <strong>Copyright &copy; 2015 <a href="#">GEE</a>.</strong> All rights reserved.
	</footer>
	<!-- Bootstrap 3.3.2 JS -->
	<script src="../js/bootstrap.min.js" type="text/javascript"></script>
	<!-- AdminLTE App -->
	<script src="../js/app.min.js" type="text/javascript"></script>
	<!-- jQuery UI 1.11.2 -->
	<script src="http://code.jquery.com/ui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>

	<!-- DATA TABES SCRIPT -->
	<script src="../js/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="../js/dataTables.bootstrap.js" type="text/javascript"></script>
	<?php
}

function breadcrumbs($breadcrumbs = array(), $header = '', $description = '') {
	?>
	<section class="content-header">
		 <h1>
			  <?php echo $header; ?>
			  <small><?php echo $description; ?></small>
		 </h1>
		 <ol class="breadcrumb">
			  <?php
			  $i = 0;
			  foreach ($breadcrumbs as $data) {
				  if ($i == sizeof($breadcrumbs) - 1) {
					  echo "<li class='active'>{$data['text']}</li>\n";
				  } else {
					  echo "<li><a href={$data['url']}>{$data['text']}</a></li>\n";
				  }
				  $i++;
			  }
			  ?>
		 </ol>		 
	</section>
	<?php
}

function check_permission() {
	$MM_restrictGoTo = "../userPortals/index.php";
	if (!isset($_SESSION['MM_Username'])) {
		$MM_qsChar = "?";
		$MM_referrer = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS);
		if (strpos($MM_restrictGoTo, "?")) {
			$MM_qsChar = "&";
		}
		if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) {
			$MM_referrer .= "?" . $QUERY_STRING;
		}
		$MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
		header("Location: " . $MM_restrictGoTo);
		exit;
	}
}
