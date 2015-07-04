<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_connProdOps = "54.144.64.79";
$database_connProdOps = "prodops_karen";
$username_connProdOps = "mfroot";
$password_connProdOps = "Mfroo7";
$connProdOps = mysql_connect($hostname_connProdOps, $username_connProdOps, $password_connProdOps) or trigger_error(mysql_error(),E_USER_ERROR); 

