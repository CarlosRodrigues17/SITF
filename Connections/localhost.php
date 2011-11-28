<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_localhost = "localhost";
$database_localhost = "teste";
$username_localhost = "root";
$password_localhost = "pass";
$localhost = mysql_pconnect($hostname_localhost, $username_localhost, $password_localhost) or trigger_error(mysql_error(),E_USER_ERROR
);

mysql_select_db($database_localhost, $localhost);  
mysql_query('SET character_set_results=utf8');
mysql_query('SET names=utf8');  
mysql_query('SET character_set_client=utf8');
mysql_query('SET character_set_connection=utf8');   
mysql_query('SET character_set_results=utf8');   
mysql_query('SET collation_connection=utf8_general_ci');

?>