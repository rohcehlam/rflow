<?php
require_once('../Connections/connection.php');
require_once('../inc/functions.php');

session_start();
check_permission();

$args = array(
	'added' => FILTER_SANITIZE_SPECIAL_CHARS,
	'updated' => FILTER_SANITIZE_SPECIAL_CHARS,
	'deleted' => FILTER_SANITIZE_SPECIAL_CHARS,
	'error' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_get = filter_input_array(INPUT_GET, $args);

$query = <<< EOD
SELECT employeeID, firstName, lastName, displayName, title, workEmail, g.group AS `group`, d.department AS `department`, engineer, manager, active
	FROM employees AS e
	LEFT JOIN(groups AS g) ON g.groupID=e.groupID
	LEFT JOIN(departments AS d) ON d.departmentID=e.departmentID
EOD;
$result = $conn->query($query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
	 <head>
		  <title><?php buildTitle("Employees"); ?></title>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		  <?php build_header(); ?>
	 </head>
	 <body class="skin-blue sidebar-mini">

		  <div class="wrapper">

				<?php build_navbar(); ?>
				<?php build_sidebar(1); ?>

				<div class="content-wrapper">

					 <?php breadcrumbs([['url' => '../userPortals/myPortal.php', 'text' => 'DashBoard'], ['url' => '', 'text' => 'Employees']], 'Employees', $filter_text) ?>

					 <section class="content">

						  <?php if (isset($my_get['added'])) { ?>
							  <div class='box box-success'>
									<div class='box-header with-border'>
										 <h3 class='box-title'>Success!</h3>
									</div>
									<div class="box-body">
										 <p>New Employee Successfully Created</p>
									</div>
							  </div>
						  <?php } ?>
						  <?php if (isset($my_get['updated'])) { ?>
							  <div class='box box-info'>
									<div class='box-header with-border'>
										 <h3 class='box-title'>Success!</h3>
									</div>
									<div class="box-body">
										 <p>Employee Successfully Updated</p>
									</div>
							  </div>
						  <?php } ?>
						  <?php if (isset($my_get['deleted'])) { ?>
							  <div class='box box-warning'>
									<div class='box-header with-border'>
										 <h3 class='box-title'>Success!</h3>
									</div>
									<div class="box-body">
										 <p>Employee Successfully Deleted</p>
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
										  <a class='btn btn-primary' href='employee.php?function=add'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp;Add a Employee</a>
									 </div>
								</div>
								<div class="box-body">
									 <table id="table_employees" class="table table-striped table-bordered">
										  <thead>
												<tr>
													 <th>Username</th>
													 <th>Given Name</th>
													 <th>Title</th>
													 <th>Work Email</th>
													 <th>Group</th>
													 <th>Department</th>
													 <th>Engineer</th>
													 <th>Manager</th>
													 <th>Active</th>
													 <?php
													 if (isset($_SESSION['MM_Username']) and $_SESSION['MM_UserGroup'] == 1) {
														 echo "<th>&nbsp;</th>\n";
													 }
													 ?>
												</tr>
										  </thead>
										  <tbody>
												<?php
												while ($row = $result->fetch_assoc()) {
													?>
													<tr>
														 <td>
															  <a href='employee.php?function=view&employeeID=<?php echo $row['employeeID']; ?>'><?php echo $row['displayName']; ?></a>
															  <?php
															  if (isset($my_get['added']) && $my_get['added'] == $row['employeeID']) {
																  echo "<span class='label label-success pull-right'><span class='glyphicon glyphicon-star'></span>&nbsp;New</span>\n";
															  }
															  ?>
															  <?php
															  if (isset($my_get['updated']) && $my_get['updated'] == $row['employeeID']) {
																  echo "<span class='label label-info pull-right'><span class='glyphicon glyphicon-star'></span>&nbsp;Updated</span>\n";
															  }
															  ?>
														 </td>
														 <td><?php echo $row['firstName'] . '&nbsp;' . $row['lastName']; ?></td>
														 <td><?php echo $row['title']; ?></td>
														 <td><?php echo $row['workEmail']; ?></td>
														 <td><?php echo $row['group']; ?></td>
														 <td><?php echo $row['department']; ?></td>
														 <td class='text-center'><?php echo $row['engineer'] == 'y' ? '<span class="label label-success">Yes</span>' : '<span class="label label-default">No</span>'; ?></td>
														 <td class='text-center'><?php echo $row['manager'] == 'y' ? '<span class="label label-success">Yes</span>' : '<span class="label label-default">No</span>'; ?></td>
														 <td class='text-center'><?php echo $row['active'] == 't' ? '<span class="label label-success">Yes</span>' : '<span class="label label-default">No</span>'; ?></td>
														 <?php
														 if (isset($_SESSION['MM_Username']) and $_SESSION['MM_UserGroup'] == 1) {
															 echo "<td>"
															 . "<a href='employee.php?function=view&employeeID={$row['employeeID']}'><i class='fa fa-eye'></i></a>"
															 . "&nbsp;<a href='employee.php?function=update&employeeID={$row['employeeID']}'><i class='fa fa-pencil'></i></a>"
															 . "&nbsp;<a href='employee.php?function=pass&employeeID={$row['employeeID']}'><i class='fa fa-key'></i></a>"
															 . "&nbsp;<a href='employee.php?function=delete&employeeID={$row['employeeID']}'><i class='fa fa-remove'></i></a>"
															 . "</td>";
														 }
														 ?>
													</tr>
													<?php
												}
												?>
										  </tbody>
									 </table>
									 <script type="text/javascript">
                               $(document).ready(function () {
                                   $('#table_employees').dataTable({"order": [[0, "asc"]], "displayLength": 25, });
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
