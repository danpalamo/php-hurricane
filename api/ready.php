<?php

include( "../include/common.php" );

session_start();
connectToDB( $servername, $dbname, $dbusername, $dbpassword );

$rowSUWiz = mysqli_fetch_assoc( mysqli_query($dblink,"SELECT mac FROM SUWizard WHERE running = '1'" ) );
if(!$rowSUWiz)
{
	$rowSUWiz = mysqli_fetch_assoc( mysqli_query($dblink,"SELECT mac FROM SUWizard WHERE push = '1' AND done = '0' LIMIT 1" ) );
	if($rowSUWiz)
	{
		echo preg_replace('~..(?!$)~', '\0:', $rowSUWiz['mac']);
	}
}
