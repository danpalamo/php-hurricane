<?php

include( "../include/common.php" );

if( $_SERVER['REQUEST_METHOD'] == "POST" || $_SERVER['REQUEST_METHOD'] == "GET" )
{
	if( array_key_exists( "technology", $_REQUEST ) )
	{
		session_start();
		connectToDB( $servername, $dbname, $dbusername, $dbpassword );

		$rowTechnology = mysqli_fetch_assoc( mysqli_query($dblink,"SELECT * FROM Technologies WHERE id = '" . clean($_REQUEST['technology']) . "'" ) );
		if(!$rowTechnology || mysql_error($dblink))
		{
			$rowTechnology = new stdClass();
		}

		echo json_encode($rowTechnology);
	}
}
