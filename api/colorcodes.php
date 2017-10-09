<?php

include( "../include/common.php" );

if( $_SERVER['REQUEST_METHOD'] == "POST" || $_SERVER['REQUEST_METHOD'] == "GET" )
{
	if( array_key_exists( "pop", $_REQUEST ) && array_key_exists( "technology", $_REQUEST ) )
	{
		$return = array();
		
		session_start();
		connectToDB( $servername, $dbname, $dbusername, $dbpassword );
		
		$APs = mysql_query( "SELECT ColorCode, Azimuth FROM APs WHERE POP = '" . clean($_REQUEST['pop']) . "' AND Technology = '" . clean($_REQUEST['technology']) . "' ORDER BY ColorCode" );
		while( $row = mysql_fetch_assoc( $APs ) )
		{
			$return[$row['ColorCode']] = getDirection($row['Azimuth']);
			
		}
		
		echo json_encode($return);
	}
}