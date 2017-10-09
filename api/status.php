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
		
		$rowSUWiz = mysql_fetch_assoc( mysql_query( "SELECT push, running, status FROM SUWizard WHERE mac = '" . $cleanMAC . "'" ) );
		$rowSU    = mysql_fetch_assoc( mysql_query( "SELECT s.Status FROM SUs LEFT JOIN Statuses s ON s.ID = SUs.Status_ID WHERE SUs.MAC = '" . $cleanMAC . "'" ) );
		if( mysql_error() || !$rowSUWiz )
		{
			print json_encode( array( 'status'=>"false", 'su'=>$rowSU['Status'] ) );
		}
		else
		{
			$rowSUWiz['su'] = $rowSU['Status'];
			print json_encode($rowSUWiz);
		}
	}
}
