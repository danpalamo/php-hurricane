<?php

include( "../include/common.php" );

session_start();
connectToDB( $servername, $dbname, $dbusername, $dbpassword );

$rowSUWiz = mysql_fetch_assoc( mysql_query( "SELECT mac FROM SUWizard WHERE running = '1'" ) );
if($rowSUWiz)
{
	echo $rowSUWiz['mac'];
}