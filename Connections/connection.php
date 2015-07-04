<?php

# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_connProdOps = "54.144.64.79";
$database_connProdOps = "prodops_karen";
$username_connProdOps = "mfroot";
$password_connProdOps = "Mfroo7";

$conn = new mysqli($hostname_connProdOps, $username_connProdOps, $password_connProdOps, $database_connProdOps);
if ($conn->connect_errno) {
	echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
}
