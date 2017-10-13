<?php

include( "../include/common.php" );

if( $_SERVER['REQUEST_METHOD'] == "POST" || $_SERVER['REQUEST_METHOD'] == "GET" )
{
	if( array_key_exists( "mac", $_REQUEST ) && array_key_exists( "action", $_REQUEST ) )
	{
		session_start();
		connectToDB( $servername, $dbname, $dbusername, $dbpassword );

		$cleanMAC = strtoupper( trim( $_REQUEST['mac'] ) );
		$cleanMAC = str_replace( ':', '', $cleanMAC );
		$cleanMAC = str_replace( '-', '', $cleanMAC );
		$cleanMAC = str_replace( ' ', '', $cleanMAC );

		$rowSU = mysqli_fetch_assoc( mysqli_query($dblink,"SELECT `S`.`ID`, `S`.`STATUS_ID`, `L`.`ConfigIP`, `L`.`ConfigPort` FROM `SUs` AS `S` LEFT JOIN `LRAntennas` AS `L` ON `L`.`ID` = `S`.`LRAntenna_ID` WHERE `MAC` LIKE '" . $cleanMAC . "'" ) );

		#need this to allow the MT to remove devices that hurricane doesn't know about
		if($_REQUEST['action'] == 'remove' && !$rowSU)
		{
			echo 'OK';
			die();
		}

		if( array_key_exists( 'debug', $_REQUEST ) )
			logDebug( $rowSU );

		if( $rowSU['STATUS_ID'] == 14 )
		{
			if( $_REQUEST['action'] == 'remove' )
			{
				mysqli_query($dblink,"UPDATE `SUs` SET `STATUS_ID` = '3' WHERE `ID` = '" . $rowSU['ID'] . "'" );
				if( mysqli_error($dblink) )
					die();

				mysqli_query($dblink,"DELETE FROM `SUWizard` WHERE `mac` = '" . $cleanMAC . "'");
				if( mysqli_error($dblink) )
					die();

				echo 'OK';
			}
			else if( $_REQUEST['action'] == 'add' )
			{
				$rowWiz = mysqli_fetch_assoc( mysqli_query($dblink,"SELECT `port` FROM `SUWizard` WHERE `mac` = '" . $cleanMAC . "'") );
				if( mysqli_error($dblink) )
					die();

				echo $rowWiz['port'] . ',' . $rowSU['ConfigIP'] . ',' . $rowSU['ConfigPort'];
			}
		}

		elseif( $rowSU['STATUS_ID'] == 3 && $rowSU['ConfigPort'] != 0 )
		{
			if( $_REQUEST['action'] == 'add' )
			{
				mysqli_query($dblink, "UPDATE `SUs` SET `STATUS_ID` = '14' WHERE `ID` = '" . $rowSU['ID'] . "'" );
				if( mysqli_error($dblink) )
					die();

				mysqli_query($dblink,"INSERT INTO `SUWizard` SET `mac` = '" . $cleanMAC . "' ON DUPLICATE KEY UPDATE push='0', running='0', status=''");
				if( mysqli_error($dblink) )
					die();

				$remotePort = mysqli_insert_id();
				echo $remotePort . ',' . $rowSU['ConfigIP'] . ',' . $rowSU['ConfigPort'];
			}
			else if( $_REQUEST['action'] == 'remove' )
			{
				echo 'OK';
			}
		}
	}
}
