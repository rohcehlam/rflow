<?php

require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');
include_once '../classes/alarm.php';
session_start();

$args = array(
	'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	'id' => FILTER_SANITIZE_SPECIAL_CHARS,
	'server' => FILTER_SANITIZE_SPECIAL_CHARS,
	'table_name' => FILTER_SANITIZE_SPECIAL_CHARS,
	'field' => FILTER_SANITIZE_SPECIAL_CHARS,
	'cron_exp' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_post = filter_input_array(INPUT_POST, $args);
/*
  echo "<pre>";
  print_r($my_post);
  echo "<pre>\n";
  exit(0);
 */
$alarm = new tAlarma($conn_dbevents, $my_post['id'], $my_post['server'], $my_post['table_name'], $my_post['field'], $my_post['cron_exp']);

if ($my_post['function'] == 'add') {
	if ($alarm->add()) {
		header("Location: alarms.php?added={$alarm->get_id()}");
	} else {
		header("Location: alarms.php?error={$alarm->error}");
	}
}

if ($my_post['function'] == 'update') {
	if ($alarm->update()) {
		header("Location: alarms.php?updated={$alarm->get_id()}");
	} else {
		header("Location: alarms.php?error={$alarm->error}");
	}
}

if ($my_post['function'] == 'delete') {
	$alarm->delete();
	header("Location: alarms.php?deleted=true");
}