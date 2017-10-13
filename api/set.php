<?php

include( "../include/common.php" );

if( $_SERVER['REQUEST_METHOD'] == "POST" || $_SERVER['REQUEST_METHOD'] == "GET" )
{
	if( array_key_exists( "mac", $_REQUEST ) )
	{
		session_start();
		connectToDB( $servername, $dbname, $dbusername, $dbpassword );

		$cleanMAC = strtoupper( trim( $_REQUEST['mac'] ) );
		$cleanMAC = str_replace( ':', '', $cleanMAC );
		$cleanMAC = str_replace( '-', '', $cleanMAC );
		$cleanMAC = str_replace( ' ', '', $cleanMAC );

		$rowSUWiz = mysqli_fetch_assoc( mysqli_query($dblink,"SELECT mac FROM SUWizard WHERE running = '1'" ) );
		if(!$rowSUWiz)
		{
			mysqli_query($dblink,"UPDATE SUWizard SET running = '1', status = 'Router ready'  WHERE mac = '" . $cleanMAC . "' AND done = '0'" );
			if( mysqli_error($dblink) || mysqli_affected_rows() == 0 )
			{
				die();
			}

			echo 'OK';
		}
	}
}
