<?php

include( "../include/common.php" );

if( $_SERVER['REQUEST_METHOD'] == "POST" || $_SERVER['REQUEST_METHOD'] == "GET" )
{
	if( array_key_exists( "technology", $_REQUEST ) )
	{
		session_start();
		connectToDB( $servername, $dbname, $dbusername, $dbpassword );
		
		$rowTechnology = mysql_fetch_assoc( mysql_query( "SELECT * FROM Technologies WHERE id = '" . clean($_REQUEST['technology']) . "'" ) );
		if(!$rowTechnology || mysql_error())
		{
			$rowTechnology = new stdClass();
		}
		
		echo json_encode($rowTechnology);
	}
}