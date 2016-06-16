<?php
ini_set('display_errors', '1');

$db=mysql_connect("localhost","adonis","adonis_123")||die(mysql_error());
mysql_query("SET NAMES 'utf8'");
//mysql_select_db("grab_data_youtube")||die(mysql_error());
mysql_select_db("pocool")||die(mysql_error());



$SQL = "SELECT * 
FROM  `streetname` 
LIMIT 0 , 30";

$result = mysql_query($SQL);

$r = mysql_fetch_array($result);
var_dump($r);

?>