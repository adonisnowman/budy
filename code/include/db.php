<?php 
	
	$db = mysql_connect("localhost", "root", "")||die(mysql_error());
		mysql_query("SET NAMES 'utf8'");
		mysql_select_db("budy")||die(mysql_error());
?>