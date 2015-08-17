<?php

require_once('../Connections/connection.php');
require_once('../inc/functions.php');
include_once '../classes/employee.php';
session_start();

$args = array(
	'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	'employeeID' => FILTER_SANITIZE_SPECIAL_CHARS,
	'firstName' => FILTER_SANITIZE_SPECIAL_CHARS,
	'lastName' => FILTER_SANITIZE_SPECIAL_CHARS,
	'displayName' => FILTER_SANITIZE_SPECIAL_CHARS,
	'title' => FILTER_SANITIZE_SPECIAL_CHARS,
	'cellPhone' => FILTER_SANITIZE_SPECIAL_CHARS,
	'homePhone' => FILTER_SANITIZE_SPECIAL_CHARS,
	'workEmail' => FILTER_SANITIZE_SPECIAL_CHARS,
	'groupID' => FILTER_SANITIZE_SPECIAL_CHARS,
	'departmentID' => FILTER_SANITIZE_SPECIAL_CHARS,
	'engineer' => FILTER_SANITIZE_SPECIAL_CHARS,
	'manager' => FILTER_SANITIZE_SPECIAL_CHARS,
	'hireDate' => FILTER_SANITIZE_SPECIAL_CHARS,
	'active' => FILTER_SANITIZE_SPECIAL_CHARS,
	'user_photo' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_post = filter_input_array(INPUT_POST, $args);
/*
echo "<pre>";
print_r($my_post);
echo "<pre>\n";
exit(0);
*/
$employee = new tEmployee($conn, $my_post['employeeID'], $my_post['firstName'], $my_post['lastName'], $my_post['displayName'], $my_post['title'], $my_post['cellPhone']
	, $my_post['homePhone'], $my_post['workEmail'], $my_post['groupID'], $my_post['departmentID'], $my_post['engineer'], $my_post['manager'], $my_post['hireDate'], $my_post['active']);

if ($my_post['function'] == 'add') {
	if ($employee->add()) {
		header("Location: employees.php?added={$employee->get_employeeID()}");
	} else {
		header("Location: employees.php?error={$employee->error}");
	}
}

if ($my_post['function'] == 'update'){
	$employee->update();
	header("Location: employees.php?updated={$employee->get_employeeID()}");
}