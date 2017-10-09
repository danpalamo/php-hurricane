<?php
error_reporting(0);

include("include/common.php");
session_start();

function Color($id)
{
	switch($id)
	{
		case 0:
			return "White";
		case 1:
			return "Black";
		case 2:
			return "Red";
		case 3:
			return "Orange";
		case 4:
			return "Yellow";
		case 5:
			return "Green";
		case 6:
			return "Blue";
		case 7:
			return "Purple";
	}
	
	return "";
}

//$headers = $_SERVER;
//$body = array("Headers:");
//foreach($headers as $header => $value)
//{
//	$body[] = $header.": ".$value;
//}
////$body[] = $_SERVER['']
//$body = join("\n", $body);
//mail("someuser@somedomain", "kml.php Headers", $body);

connectToDB($servername, $dbname, $dbusername, $dbpassword);

$search = unserialize(stripslashes(base64_decode($_GET['search'])));

$where1 = "";
$where2 = "";

if($search['SearchName'])
{
	$searchWords = explode(" ", $search['SearchName']);
	
	$where1 .= " AND (SUs.CustomerName LIKE '%".clean($searchWords[0])."%'";
	$where2 .= " AND (APs.LocationName LIKE '%".clean($searchWords[0])."%'";
	for($i = 1; $i < count($searchWords); $i++)
	{
		$where1 .= " AND SUs.CustomerName LIKE '%".clean($searchWords[$i])."%'";
		$where2 .= " AND APs.LocationName LIKE '%".clean($searchWords[$i])."%'";
	}
	$where1 .= ")";
	$where2 .= ")";
}

if($search['SearchAddress'])
{
	$searchWords = explode(" ", $search['SearchAddress']);
	
	$where1 .= " AND (SUs.CustomerAddress LIKE '%".clean($searchWords[0])."%'";
	$where2 .= " AND (APs.LocationAddress LIKE '%".clean($searchWords[0])."%'";
	for($i = 1; $i < count($searchWords); $i++)
	{
		$where1 .= " AND SUs.CustomerAddress LIKE '%".clean($searchWords[$i])."%'";
		$where2 .= " AND APs.LocationAddress LIKE '%".clean($searchWords[$i])."%'";
	}
	$where1 .= ")";
	$where2 .= ")";
}

if($search['SearchEquipmentType'])
{
	if($search['SearchEquipmentType'] == "SU")
	{
		$where2 .= " AND '1'='0'";
	}
	else if($search['SearchEquipmentType'] == "AP")
	{
		$where1 .= " AND '1'='0'";
	}
}

if($search['SearchInventoryNumber'])
{
	$searchInventoryNumber = clean($search['SearchInventoryNumber']);
	$where1 .= " AND SUs.InventoryNumber LIKE '%".$searchInventoryNumber."%'";
	$where2 .= " AND APs.InventoryNumber LIKE '%".$searchInventoryNumber."%'";
}

if($search['SearchSerialNumber'])
{
	$searchSerialNumber = clean($search['SearchSerialNumber']);
	$where1 .= " AND SUs.SerialNumber LIKE '%".$searchSerialNumber."%'";
	$where2 .= " AND APs.SerialNumber LIKE '%".$searchSerialNumber."%'";
}

if($search['SearchAPID'] != "")
{
	$searchAPID = clean($search['SearchAPID']);
	
	if($searchAPID == "0")
	{
		$where1 .= " AND SUs.AP_ID = '0'";
		$where2 .= " AND APs.ID =''";
	}
	else
	{
		$where1 .= " AND SUs.AP_ID ='".$searchAPID."'";
		$where2 .= " AND APs.ID ='".$searchAPID."'";
	}
}

if($search['SearchSUID'])
{
	$searchSUID = clean($search['SearchSUID']);
	
	$where1 .= " AND SUs.SUID ='".$searchSUID."'";
	$where2 .= " AND '1'='0'";
}

if($search['SearchAntenna'] != "")
{
	$searchAntenna = clean($search['SearchAntenna']);
	
	if($searchAntenna == "0")
	{
		$where1 .= " AND SUs.LRAntenna_ID = '0'";
		$where2 .= " AND '1'='0'";
	}
	else
	{
		$where1 .= " AND SUs.LRAntenna_ID ='".$searchAntenna."'";
		$where2 .= " AND '1'='0'";		
	}
}

if($search['SearchIP'])
{
	$searchIP = clean($search['SearchIP']);
	
	$where1 .= " AND SUs.IP LIKE '%".$searchIP."%'";
	$where2 .= " AND APs.IP LIKE '%".$searchIP."%'";
}

if($search['SearchStatus'] != "")
{
	$searchStatus = clean($search['SearchStatus']);
	
	if($searchStatus == "0")
	{
		$where1 .= " AND SUs.Status_ID = '0'";
		$where2 .= " AND APs.Status_ID = '0'";
	}
	else
	{
		$where1 .= " AND SUs.Status_ID = '".$searchStatus."'";
		$where2 .= " AND APs.Status_ID = '".$searchStatus."'";
		
	}
}

if($search['SearchPhone'])
{
	$searchPhone = '%'.clean(preg_replace("/[- \(\)]/", "%", $search['SearchPhone'])).'%';

	$where1 .= " AND (SUs.CustomerPhone1 LIKE '".$searchPhone."'";
	$where1 .= " OR SUs.CustomerPhone2 LIKE '".$searchPhone."'";
	$where1 .= " OR SUs.CustomerPhone3 LIKE '".$searchPhone."')";
	$where2 .= " AND (APs.LocationPhone1 LIKE '".$searchPhone."'";
	$where2 .= " OR APs.LocationPhone2 LIKE '".$searchPhone."'";
	$where2 .= " OR APs.LocationPhone3 LIKE '".$searchPhone."')";
}

if($search['SearchNotes'])
{
	$searchWords = explode(" ", $search['SearchNotes']);
	
	$where1 .= " AND (SUs.Notes LIKE '%".clean($searchWords[0])."%'";
	$where2 .= " AND (APs.Notes LIKE '%".clean($searchWords[0])."%'";
	for($i = 1; $i < count($searchWords); $i++)
	{
		$where1 .= " AND SUs.Notes LIKE '%".clean($searchWords[$i])."%'";
		$where2 .= " AND APs.Notes LIKE '%".clean($searchWords[$i])."%'";
	}
	$where1 .= ")";
	$where2 .= ")";
}

$StartDate = $search['SearchInstallDateStart'];
$EndDate = $search['SearchInstallDateEnd'];

if($StartDate)
{
	if(!$EndDate or $StartDate == $EndDate)
	{
		//there's a start but no end, or the dates are equal - query the one day
		$where1 .= " AND TO_DAYS(SUs.InstallDate) = TO_DAYS('".date("Y-m-d H:i:s", strtotime($StartDate))."')";
		$where2 .= " AND TO_DAYS(APs.InstallDate) = TO_DAYS('".date("Y-m-d H:i:s", strtotime($StartDate))."')";
	}
	else
	{
		//query the date range
		$where1 .= " AND TO_DAYS(SUs.InstallDate) BETWEEN TO_DAYS('".date("Y-m-d H:i:s", strtotime($StartDate))."') AND TO_DAYS('".date("Y-m-d H:i:s", strtotime($EndDate))."')";
		$where2 .= " AND TO_DAYS(APs.InstallDate) BETWEEN TO_DAYS('".date("Y-m-d H:i:s", strtotime($StartDate))."') AND TO_DAYS('".date("Y-m-d H:i:s", strtotime($EndDate))."')";
	}
}
else
{
	if($EndDate)
	{
		//there's an end but no start - query the one day
		$where1 .= " AND TO_DAYS(SUs.InstallDate) = TO_DAYS('".date("Y-m-d H:i:s", strtotime($EndDate))."')";
		$where2 .= " AND TO_DAYS(APs.InstallDate) = TO_DAYS('".date("Y-m-d H:i:s", strtotime($EndDate))."')";
	}
}

if($search['SearchInstaller'] != "")
{
	$searchInstaller = clean($search['SearchInstaller']);

	if($searchInstaller == "0")
	{
		$where1 .= " AND SUs.Installer_ID = '0'";
		$where2 .= " AND '1'='0'";
	}
	else
	{
		$where1 .= " AND SUs.Installer_ID ='".$searchInstaller."'";
		$where2 .= " AND '1'='0'";
	}
}

if($search['SearchContract'] != "")
{
	$searchContract = clean($search['SearchContract']);
	
	if($searchContract == "0")
	{
		$where1 .= " AND SUs.Contract_ID = '0'";
		$where2 .= " AND '1'='0'";
	}
	else
	{
		$where1 .= " AND SUs.Contract_ID ='".$searchContract."'";
		$where2 .= " AND '1'='0'";
	}
}

if($search['SearchDataRate'] != "")
{
	$searchDataRate = clean($search['SearchDataRate']);
	
	if($searchDataRate == "0")
	{
		$where1 .= " AND SUs.DataRate_ID = '0'";
		$where2 .= " AND '1'='0'";
	}
	else
	{
		$where1 .= " AND SUs.DataRate_ID ='".$searchDataRate."'";
		$where2 .= " AND '1'='0'";
	}
}

$sortColumn1 = ", IF(ISNULL(SUs.AP_ID) OR SUs.AP_ID = '', 1, 0) AS isnull";
$sortColumn2 = ", IF(ISNULL(APs.APID) OR APs.APID = '', 1, 0) AS isnull";
$orderBy .= " ORDER BY isnull, Status_ID, APID ASC, CAST(SUID AS DECIMAL) ASC";

$search = "(SELECT SUs.*, 'SU' AS EquipmentType, APs.APID".$sortColumn1." FROM SUs, APs WHERE SUs.AP_ID = APs.ID AND SUs.CustomerGeocode != ''".$where1.") UNION 
(SELECT ID, InventoryNumber, SerialNumber, ID, NULL, '0', MAC, IP, Status_ID,
LocationName, LocationAddress, LocationCity, LocationState, LocationZIP, LocationGeocode, 
LocationPhone1, LocationPhone2, LocationPhone3, InstallDate, NULL, NULL, NULL, NULL, NULL, Notes, created, 'AP' as EquipmentType, APID".$sortColumn2."
FROM APs WHERE APs.ID > 0 AND APs.LocationGeocode != ''".$where2.")".$where.$orderBy;

$searchResults = mysql_query($search) or die(mysql_error());

if($_GET['download'])
	$GE = "GE";

$kml = array('<?xml version="1.0" encoding="UTF-8"?>');
$kml[] = '<kml xmlns="http://earth.google.com/kml/2.1">';
$kml[] = '	<Document>';
$kml[] = '		<Style id="markerWhiteStyle">';
$kml[] = '			<IconStyle>';
$kml[] = '				<Icon>';
$kml[] = '					<href>http://hurricane.somedomain/image/markerWhite'.$GE.'.png</href>';
$kml[] = '				</Icon>';
$kml[] = '			</IconStyle>';
$kml[] = '		</Style>';
$kml[] = '		<Style id="markerBlackStyle">';
$kml[] = '			<IconStyle>';
$kml[] = '				<Icon>';
$kml[] = '					<href>http://hurricane.somedomain/image/markerBlack'.$GE.'.png</href>';
$kml[] = '				</Icon>';
$kml[] = '			</IconStyle>';
$kml[] = '		</Style>';
$kml[] = '		<Style id="markerRedStyle">';
$kml[] = '			<IconStyle>';
$kml[] = '				<Icon>';
$kml[] = '					<href>http://hurricane.somedomain/image/markerRed'.$GE.'.png</href>';
$kml[] = '				</Icon>';
$kml[] = '			</IconStyle>';
$kml[] = '		</Style>';
$kml[] = '		<Style id="markerOrangeStyle">';
$kml[] = '			<IconStyle>';
$kml[] = '				<Icon>';
$kml[] = '					<href>http://hurricane.somedomain/image/markerOrange'.$GE.'.png</href>';
$kml[] = '				</Icon>';
$kml[] = '			</IconStyle>';
$kml[] = '		</Style>';
$kml[] = '		<Style id="markerYellowStyle">';
$kml[] = '			<IconStyle>';
$kml[] = '				<Icon>';
$kml[] = '					<href>http://hurricane.somedomain/image/markerYellow'.$GE.'.png</href>';
$kml[] = '				</Icon>';
$kml[] = '			</IconStyle>';
$kml[] = '		</Style>';
$kml[] = '		<Style id="markerGreenStyle">';
$kml[] = '			<IconStyle>';
$kml[] = '				<Icon>';
$kml[] = '					<href>http://hurricane.somedomain/image/markerGreen'.$GE.'.png</href>';
$kml[] = '				</Icon>';
$kml[] = '			</IconStyle>';
$kml[] = '		</Style>';
$kml[] = '		<Style id="markerBlueStyle">';
$kml[] = '			<IconStyle>';
$kml[] = '				<Icon>';
$kml[] = '					<href>http://hurricane.somedomain/image/markerBlue'.$GE.'.png</href>';
$kml[] = '				</Icon>';
$kml[] = '			</IconStyle>';
$kml[] = '		</Style>';
$kml[] = '		<Style id="markerPurpleStyle">';
$kml[] = '			<IconStyle>';
$kml[] = '				<Icon>';
$kml[] = '					<href>http://hurricane.somedomain/image/markerPurple'.$GE.'.png</href>';
$kml[] = '				</Icon>';
$kml[] = '			</IconStyle>';
$kml[] = '		</Style>';

// add antenna pattern overly jc20100405
if($search['SearchAPID'] != "") {
  $qPat = mysql_query("select PatternKMLFileName from APs where ID=" . $searchAPID);
  $rPat = mysql_fetch_array($qPat);
  if($rPat['PatternKMLFileName']) {
    $tKML = file_get_contents("patterns/" . $rPat['PatternKMLFileName']);
  }
  if ($tKML) {
    $kml[] = $tKML;
  }
}

while($row = mysql_fetch_array($searchResults)) 
{
	$kml[] = '		<Placemark id="placemark'.$row['EquipmentType'].$row['ID'].'">';

	if($row['EquipmentType'] == "SU")
	{
		if($_GET['show'] == "signal")
		{
			$signal = file_get_contents('http://tsunami.somedomain/'.$row['APID'].'/'.sprintf("%03s", $row['SUID']).'/getSignal.php');
			if(!$signal)
				$signalNotify = "<br><br>Error: SUID does not appear to be valid.";
			else
			{
				if($signal == -120 or $signal == 0)
					$color = "Black";
				else if($signal < -80)
					$color = "Red";
				else if($signal < -70)
					$color = "Orange";
				else if($signal < -60)
					$color = "Yellow";
				else
					$color = "Green";
				if($signal == -120)
					$signalNotify = '<br><br>Radio Offline';
				else
					$signalNotify = '<br><br>Current Signal Strength: '.$signal.'dBm';
			}
		}
		else if($_GET['show'] == "lrantennas")
		{
			if($row['LRAntenna_ID'] == 0)
				$color = "White";
			else
				$color = "Red";
		}
		else if($_GET['show'] == "datarate")
		{
			if($row['DataRate_ID'] == 8 || $row['DataRate_ID'] == 12)
				$color = "Green";
			else if($row['DataRate_ID'] == 9 || $row['DataRate_ID'] == 13)
				$color = "Yellow";
			else if($row['DataRate_ID'] == 10 || $row['DataRate_ID'] == 11)
				$color = "Orange";
			else if($row['DataRate_ID'] == 14)
				$color = "Red";
			else
				$color = "White";
		}
		else
		{
				$color = "White";
		}

		$kml[] = '			<name>'.htmlentities($row['CustomerName']).'</name>';

		if($APRow = mysql_fetch_array(mysql_query("SELECT APID FROM APs WHERE ID = '".$row['AP_ID']."'")));
			$APLink = '<a href="http://tsunami.somedomain/'.$APRow['APID'].'">'.$APRow['APID'].'</a>';
		$SULink = '<a href="http://tsunami.somedomain/'.$APRow['APID'].'/'.sprintf("%03s", $row['SUID']).'">'.$row['SUID'].'</a>';

		$LRANotify = "";
		if($LRARow = mysql_fetch_array(mysql_query("SELECT LRAntenna FROM LRAntennas WHERE ID = '".$row['LRAntenna_ID']."'")))
			if($LRARow['LRAntenna'])
				$LRANotify = '<br><br>Has long-range antenna: '.$LRARow['LRAntenna'];
		
		$kml[] = '			<description>'.htmlentities($APLink." ".$SULink."<br>".$row['CustomerAddress'].$LRANotify.$signalNotify).'</description>';
		$kml[] = '			<styleUrl>#marker'.$color.'Style</styleUrl>';
	}
	else if($row['EquipmentType'] == "AP")
	{
		$color = "Blue";
		$APLink = '<a href="http://tsunami.somedomain/'.$row['APID'].'">'.$row['APID'].'</a>';
		
		$kml[] = '			<name>'.htmlentities($row['APID']).'</name>';
		$kml[] = '			<description>'.htmlentities($APLink.'<br>'.$row['CustomerAddress']).'</description>';
		$kml[] = '			<styleUrl>#marker'.$color.'Style</styleUrl>';
	}
	$kml[] = '			<Point>';
	$latlong = explode(",", preg_replace("/[\(\)]/", "", $row['CustomerGeocode']));
	$kml[] = '				<coordinates>'.trim($latlong[1]).",".trim($latlong[0]).'</coordinates>';
	$kml[] = '			</Point>';
	$kml[] = '		</Placemark>';
} 

$kml[] = ' </Document>';
$kml[] = '</kml>';
$kmlOutput = join("\n", $kml);

header("Content-Disposition: attachment; filename=hurricane.kml");
header('Content-type: application/vnd.google-earth.kml+xml');
header("Cache-Control: no-cache, must-revalidate");
echo $kmlOutput;
?>
