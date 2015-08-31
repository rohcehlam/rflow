<?php

require_once('../Connections/conn_dbevents.php');
require_once('../inc/functions.php');
include_once '../classes/server.php';
session_start();

$args = array(
	'function' => FILTER_SANITIZE_SPECIAL_CHARS,
	'id' => FILTER_SANITIZE_SPECIAL_CHARS,
	'nombre' => FILTER_SANITIZE_SPECIAL_CHARS,
	'host' => FILTER_SANITIZE_SPECIAL_CHARS,
);

$my_post = filter_input_array(INPUT_POST, $args);
/*
  echo "<pre>";
  print_r($my_post);
  echo "<pre>\n";
  exit(0);
 */
$server = new tServer($conn_dbevents, $my_post['id'], $my_post['nombre'], $my_post['host']);

if ($my_post['function'] == 'add') {
	if ($server->add()) {
		header("Location: servers.php?added={$server->get_id()}");
	} else {
		header("Location: servers.php?error={$server->error}");
	}
}

if ($my_post['function'] == 'update') {
	$server->update();
	header("Location: servers.php?updated={$server->get_id()}");
}

if ($my_post['function'] == 'delete') {
	$server->delete();
	header("Location: servers.php?deleted=true");
}