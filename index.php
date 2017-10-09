<?php 
include("include/common.php");
session_start();

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$_SESSION['POST'] = $_POST;
	session_write_close();
	header('Location: //'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
	exit();
}

connectToDB($servername, $dbname, $dbusername, $dbpassword);

if(isset($_SESSION['POST']))
{
	if($_SESSION['POST']['Action'] == "Clear")
	{
		unset($_SESSION['View']->Search);
	}
	else if($_SESSION['POST']['Action'] == "Search")
	{
		$_SESSION['View']->Search = $_SESSION['POST'];		
	}
	else if($_SESSION['POST']['Action'] == "Sort")
	{
		$_SESSION['View']->SortBy($_SESSION['POST']['SortBy']);
	}
}

$pageTitle = "hurricane";
include("include/header.php");

if(!isset($_SESSION['View']))
	$_SESSION['View'] = new View();

if(isset($_SESSION['POST']['SearchType']))
	$_SESSION['View']->searchType = $_SESSION['POST']['SearchType'];
	
$view = $_SESSION['View'];

$sortColumn1 = ", IF(ISNULL(SUs.AP_ID) OR SUs.AP_ID = '', 1, 0) AS isnull";
$sortColumn2 = ", IF(ISNULL(APs.APID) OR APs.APID = '', 1, 0) AS isnull";
$orderBy = " ORDER BY isnull, APID ASC, CAST(SUID AS DECIMAL) ASC";

$where1 = "";
$where2 = "";

if($view->Search['SearchName'])
{
	$searchWords = explode(" ", $view->Search['SearchName']);
	
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

if($view->Search['SearchAddress'])
{
	$searchWords = explode(" ", $view->Search['SearchAddress']);
	
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

if($view->Search['SearchMAC'])
{
	$searchMAC = clean(preg_replace("/[-:. ]/", "", $view->Search['SearchMAC']));
	$where1 .= " AND SUs.MAC LIKE '%".$searchMAC."%'";
	$where2 .= " AND APs.MAC LIKE '%".$searchMAC."%'";
}

if($view->Search['SearchEquipmentType'])
{
	if($view->Search['SearchEquipmentType'] == "SU")
	{
		$where2 .= " AND '1'='0'";
	}
	else if($view->Search['SearchEquipmentType'] == "AP")
	{
		$where1 .= " AND '1'='0'";
	}
}

if($view->Search['SearchInventoryNumber'])
{
	$searchInventoryNumber = clean($view->Search['SearchInventoryNumber']);
	$where1 .= " AND SUs.InventoryNumber LIKE '%".$searchInventoryNumber."%'";
	$where2 .= " AND APs.InventoryNumber LIKE '%".$searchInventoryNumber."%'";
}

if($view->Search['SearchSerialNumber'])
{
	$searchSerialNumber = clean($view->Search['SearchSerialNumber']);
	$where1 .= " AND SUs.SerialNumber LIKE '%".$searchSerialNumber."%'";
	$where2 .= " AND APs.SerialNumber LIKE '%".$searchSerialNumber."%'";
}

if($view->Search['SearchPOP'] != "")
{
	$searchPOP = clean($view->Search['SearchPOP']);
	
	if($searchPOP == "0")
	{
		$where1 .= " AND APs.POP ='0'";
		$where2 .= " AND APs.POP ='0'";
	}
	else
	{
		$where1 .= " AND APs.POP ='".$searchPOP."'";
		$where2 .= " AND APs.POP ='".$searchPOP."'";
	}
}

if($view->Search['SearchAPID'] != "")
{
	$searchAPID = clean($view->Search['SearchAPID']);
	
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

if($view->Search['SearchSUID'])
{
	$searchSUID = clean($view->Search['SearchSUID']);
	
	$where1 .= " AND SUs.SUID ='".$searchSUID."'";
	$where2 .= " AND '1'='0'";
}

if($view->Search['SearchAntenna'] != "")
{
	$searchAntenna = clean($view->Search['SearchAntenna']);
	
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

if($view->Search['SearchIP'])
{
	$searchIP = clean($view->Search['SearchIP']);
	
	$where1 .= " AND SUs.IP LIKE '%".$searchIP."%'";
	$where2 .= " AND APs.IP LIKE '%".$searchIP."%'";
}

if($view->Search['SearchStatus'] != "")
{
	$searchStatus = clean($view->Search['SearchStatus']);
	
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

if($view->Search['SearchPhone'])
{
	$searchPhone = '%'.clean(preg_replace("/[- \(\)]/", "%", $view->Search['SearchPhone'])).'%';

	$where1 .= " AND (SUs.CustomerPhone1 LIKE '".$searchPhone."'";
	$where1 .= " OR SUs.CustomerPhone2 LIKE '".$searchPhone."'";
	$where1 .= " OR SUs.CustomerPhone3 LIKE '".$searchPhone."')";
	$where2 .= " AND (APs.LocationPhone1 LIKE '".$searchPhone."'";
	$where2 .= " OR APs.LocationPhone2 LIKE '".$searchPhone."'";
	$where2 .= " OR APs.LocationPhone3 LIKE '".$searchPhone."')";
}

if($view->Search['SearchNetNum'])
{
	$searchNetNum = '%'.clean(preg_replace("/[- \(\)]/", "%", $view->Search['SearchNetNum'])).'%';
	$where1 .= " SUs.NetworkNumber LIKE '".$searchNetNum."'";
}

if($view->Search['SearchNotes'])
{
	$searchWords = explode(" ", $view->Search['SearchNotes']);
	
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

$StartDate = $view->Search['SearchInstallDateStart'];
$EndDate = $view->Search['SearchInstallDateEnd'];

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

if($view->Search['SearchInstaller'] != "")
{
	$searchInstaller = clean($view->Search['SearchInstaller']);

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

if($view->Search['SearchContract'] != "")
{
	$searchContract = clean($view->Search['SearchContract']);
	
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

if($view->Search['SearchDataRate'] != "")
{
	$searchDataRate = clean($view->Search['SearchDataRate']);
	
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

$t = mysqli_query($dblink,"select ID,DataRate from DataRates");
while ($r = mysqli_fetch_array($t)) {
  $dataRateNames[$r['ID']] = $r['DataRate'];
}

$search = "(SELECT SUs.*, 'SU' AS EquipmentType, APs.APID".$sortColumn1.", '0' AS POP FROM SUs, APs WHERE SUs.AP_ID = APs.ID".$where1.") UNION 
(SELECT ID, InventoryNumber, SerialNumber, ID, NULL, '0', MAC, IP, Status_ID,
LocationName, LocationAddress, LocationCity, LocationState, LocationZIP, LocationGeocode, 
LocationPhone1, LocationPhone2, LocationPhone3, NULL, InstallDate, NULL, NULL, NULL, NULL, NULL, Notes, NULL, 'AP' as EquipmentType, APID".$sortColumn2.", POP
FROM APs WHERE APs.ID > 0".$where2.")".$where.$orderBy;

$searchResults = mysqli_query($dblink,$search) or die(mysqli_error($dblink));
//echo $search;
?>

<div id="SimpleSearch" <?php if($_SESSION['View']->searchType == "Advanced") echo "style='display:none;'"; ?>>
<form name="SimpleSearchForm" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<input type="hidden" name="SearchType" value="Simple">
<table class="table_form">
	<tr>
		<td><a onclick='ShowSearch("Advanced");'><img src="image/plus.png"></a>
		<td>Name:<td><input name="SearchName" type="text" value="<?php echo $_SESSION['View']->Search['SearchName']; ?>">
		<td>Address:<td><input name="SearchAddress" type="text" value="<?php echo $_SESSION['View']->Search['SearchAddress']; ?>">
		<td>MAC:<td><input name="SearchMAC" type="text" size="16" value="<?php echo $_SESSION['View']->Search['SearchMAC']; ?>">
		<td>Network Number:<td><input name="SearchNetNum" type="text" size="16" value="<?php echo $_SESSION['View']->Search['SearchNetNum']; ?>">
		<td><input type="submit" name="Action" value="Search"><input type="submit" name="Action" value="Clear">
</table>
</form>
</div>

<div id="AdvancedSearch" <?php if($_SESSION['View']->searchType == "Simple" or !isset($_SESSION['View']->searchType)) echo "style='display:none;'"; ?>>
<form name="AdvancedSearchForm" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<input type="hidden" name="SearchType" value="Advanced">
<table>
	<tr>
		<td>
			<table class="table_form">
				<tr><td><a onclick='ShowSearch("Simple");'><img src="image/minus.png"></a><td>Equipment Type<td>
					<select name="SearchEquipmentType">
						<option value="">Any</option>
						<option value="SU" <?php if($_SESSION['View']->Search['SearchEquipmentType'] == "SU") echo 'selected="selected"'; ?>>SU
						<option value="AP" <?php if($_SESSION['View']->Search['SearchEquipmentType'] == "AP") echo 'selected="selected"'; ?>>AP
					</select>
				<tr><td><td>Inventory Number<td><input type="text" size="16" name="SearchInventoryNumber" value="<?php echo $_SESSION['View']->Search['SearchInventoryNumber']; ?>">
				<tr><td><td>Serial Number<td><input type="text" size="16" name="SearchSerialNumber" value="<?php echo $_SESSION['View']->Search['SearchSerialNumber']; ?>">
				<tr><td><td>AP/SUID<td>
					<select name="SearchAPID">
						<option value="">Any</option>
						<option value="0" <?php if($_SESSION['View']->Search['SearchAPID'] == "0") echo ' selected="selected"'; ?>>None</option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM APs ORDER BY APID ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['APID'])
							{
								if($row['ID'] == $_SESSION['View']->Search['SearchAPID'])
									echo '<option value="'.$row['ID'].'" selected="selected">'.$row['APID'];
								else
									echo '<option value="'.$row['ID'].'">'.$row['APID'];
							}
						}
						?>
					</select><input type="text" size="4" name="SearchSUID" value="<?php echo $_SESSION['View']->Search['SearchSUID']; ?>">
				<tr><td><td>Antenna<td>
					<select name="SearchAntenna">
						<option value="">Any</option>
						<option value="0" <?php if($_SESSION['View']->Search['SearchAntenna'] == "0") echo ' selected="selected"'; ?>>None</option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM LRAntennas ORDER BY LRAntenna ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['LRAntenna'])
							{
								if($row['ID'] == $_SESSION['View']->Search['SearchAntenna'])
									echo '<option value="'.$row['ID'].'" selected="selected">'.$row['LRAntenna'];
								else
									echo '<option value="'.$row['ID'].'">'.$row['LRAntenna'];
							}
						}
						?>
					</select>
				<tr><td><td>POP<td>
					<select name="SearchPOP">
						<option value="">Any</option>
						<option value="0" <?php if($_SESSION['View']->Search['SearchAntenna'] == "0") echo ' selected="selected"'; ?>>None</option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT ID, name FROM POPs ORDER BY name ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['name'])
							{
								if($row['ID'] == $_SESSION['View']->Search['SearchPOP'])
									echo '<option value="'.$row['ID'].'" selected="selected">'.$row['name'];
								else
									echo '<option value="'.$row['ID'].'">'.$row['name'];
							}
						}
						?>
					</select>
				<tr><td><td>MAC<td><input type="text" size="16" name="SearchMAC" value="<?php echo $_SESSION['View']->Search['SearchMAC']; ?>">
				<tr><td><td>IP<td><input type="text" size="16" name="SearchIP" value="<?php echo $_SESSION['View']->Search['SearchIP']; ?>">
				<tr><td><td>Status<td>
					<select name="SearchStatus">
						<option value="">Any</option>
						<option value="0" <?php if($_SESSION['View']->Search['SearchStatus'] == "0") echo ' selected="selected"'; ?>>None</option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Statuses ORDER BY Status ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['Status'])
							{
								if($row['ID'] == $_SESSION['View']->Search['SearchStatus'])
									echo '<option value="'.$row['ID'].'" selected="selected">'.$row['Status'];
								else
									echo '<option value="'.$row['ID'].'">'.$row['Status'];
							}
						}
						?>
					</select>
			</table>
		<td>
			<table class="table_form">
				<tr><td>Name<td><input type="text" name="SearchName" value="<?php echo $_SESSION['View']->Search['SearchName']; ?>">
				<tr><td>Address<td><input type="text" name="SearchAddress" value="<?php echo $_SESSION['View']->Search['SearchAddress']; ?>">
				<tr><td>Network Number<td><input type="text" name="PhoneNetNum" value="<?php echo $_SESSION['View']->Search['SearchNetNum']; ?>">
				<tr class="tall"><td>Notes<td><textarea rows="4" cols="20" name="SearchNotes"><?php echo $_SESSION['View']->Search['SearchNotes']; ?></textarea>
			</table>
		<td>
			<table class="table_form">
				<tr><td>Install Date<td><input type="text" size="8" name="SearchInstallDateStart" value="<?php echo $_SESSION['View']->Search['SearchInstallDateStart']; ?>"> - <input type="text" size="8" name="SearchInstallDateEnd" value="<?php echo $_SESSION['View']->Search['SearchInstallDateEnd']; ?>">
				<tr><td>Installer<td>
					<select name="SearchInstaller">
						<option value="">Any</option>
						<option value="0" <?php if($_SESSION['View']->Search['SearchInstaller'] == "0") echo ' selected="selected"'; ?>>None</option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Installers ORDER BY Installer ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['Installer'])
							{
								if($row['ID'] == $_SESSION['View']->Search['SearchInstaller'])
									echo '<option value="'.$row['ID'].'" selected="selected">'.$row['Installer'];
								else
									echo '<option value="'.$row['ID'].'">'.$row['Installer'];
							}
						}
						?>
					</select>
				<tr><td>Contract<td>
					<select name="SearchContract">
						<option value="">Any</option>
						<option value="0" <?php if($_SESSION['View']->Search['SearchContract'] == "0") echo ' selected="selected"'; ?>>None</option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Contracts ORDER BY Contract ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['Contract'])
							{
								if($row['ID'] == $_SESSION['View']->Search['SearchContract'])
									echo '<option value="'.$row['ID'].'" selected="selected">'.$row['Contract'];
								else
									echo '<option value="'.$row['ID'].'">'.$row['Contract'];
							}
						}
						?>
					</select>
				<tr><td>Data Rate<td>
					<select name="SearchDataRate">
						<option value="">Any</option>
						<option value="0" <?php if($_SESSION['View']->Search['SearchDataRate'] == "0") echo ' selected="selected"'; ?>>None</option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM DataRates ORDER BY DataRate ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['DataRate'])
							{
								if($row['ID'] == $_SESSION['View']->Search['SearchDataRate'])
									echo '<option value="'.$row['ID'].'" selected="selected">'.$row['DataRate'];
								else
									echo '<option value="'.$row['ID'].'">'.$row['DataRate'];
							}
						}
						?>
					</select>
			</table>
</table>
<input type="submit" name="Action" value="Search"><input type="submit" name="Action" value="Clear">
</form>
</div>

<?php
if(isset($searchResults))
{
?>

<form name="mainForm" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<input type="hidden" name="Action" value="">
<input type="hidden" name="SortBy" value="">
<table class="table_db table_wide sortable">
<thead>
	<tr>
		<th class="sorttable_alpha">INV
<!--		<th>SN -->
		<th>APID
<!--		<th>SUID -->
		<th>POP
		<th class="sorttable_alpha">MAC
		<th>IP
		<th>Status
		<th>DataRate
<!--		<th>Spd_Dn --> 
<!--		<th>Spd_Up --> 
		<th>Name
		<th class="sorttable_alpha">Network Number
		<th colspan=4>Address
</thead>
<tbody>

<?php

	$count = 0;

	while($row = mysqli_fetch_array($searchResults))
	{
		$count++;
		
		echo '<tr id="'.$row['EquipmentType'].$row['ID'].'" ondblclick=\'HideTooltip(); EditRow(this.id);\' style="background-color:'.(($StatusRow = mysqli_fetch_array(mysqli_query($dblink,"SELECT RowColor FROM Statuses WHERE ID = '".$row['Status_ID']."'"))) ? $StatusRow['RowColor'] : "").'">';
		echo '<td><a onclick=\'HideTooltip(); EditRow(this.parentNode.parentNode.id); return false;\' href=\'#\'>'.$row['InventoryNumber'].'</a>';
		//echo '<td>'. ltrim($row['SerialNumber'], "0");
		
		echo '<td>';
		{
			if($APRow = mysqli_fetch_array(mysqli_query($dblink,"SELECT APID, POP FROM APs WHERE ID = '".$row['AP_ID']."'")))
			{
				error_log($row['AP_ID'] . ': ' . $row['EquipmentType']);
				echo '<a target="_blank" href="//tsunami.somedomain/'.$APRow['APID'].'">'.$APRow['APID'].'</a>';
			}
		}
				
		//echo '<td>';
		//if($row['EquipmentType'] == "SU")
		//{
			//echo '<a target="_blank" href="//tsunami.somedomain/'.$APRow['APID'].'/'.sprintf("%03s", $row['SUID']).'">'.$row['SUID'].'</a>';
		//}

		echo '<td>';
		{
			if($APRow['POP'] != 0)
				if($POPRow = mysqli_fetch_array(mysqli_query($dblink,"SELECT `name` FROM POPs WHERE ID = '" . $APRow['POP'] . "'")))
					echo $POPRow['name'];
		}

		echo '<td>'.reformatMAC($row['MAC']);
		echo '<td sorttable_customkey="'.makeSortableIP($row['IP']).'"><a target="blank" href="http://'.$row['IP'].'">'.$row['IP'].'</a>';
		echo '<td>';
		if($StatusRow = mysqli_fetch_array(mysqli_query($dblink,"SELECT Status FROM Statuses WHERE ID = '".$row['Status_ID']."'")))
			echo $StatusRow['Status'];
	
		echo '<td>'.$dataRateNames[$row['DataRate_ID']];
//		echo '<td>'.na;
//		echo '<td>'.$row['Speed_Down'];
//		echo '<td>'.$row['Speed_Up'];
	
		echo '<td>'.$row['CustomerName'];

//		$tooltip = "";
//		if($row['CustomerPhone2'] or $row['CustomerPhone3'])
//		{
//			if($row['CustomerPhone1'])
//				$tooltip .= $row['CustomerPhone1']."<br>";
//			if($row['CustomerPhone2'])
//				$tooltip .= $row['CustomerPhone2']."<br>";
//			if($row['CustomerPhone3'])
//				$tooltip .= $row['CustomerPhone3']."<br>";
//		}
//		if($tooltip)
//		{
//			echo '<td onmouseover=\'ShowTooltip("'.$tooltip.'");\' onmouseout=\'HideTooltip();\'>'.$row['CustomerPhone1'];
//		}
//		else
//		{
			echo '<td><a target="_blank" href="http://crumbs.tbtc.net/cgi-bin/macc_getInfoByNet.py?netType=INT&netNum='.$row['NetworkNumber'].'">'.$row['NetworkNumber'].'</a>';
//		}

		echo '<td width="100%"><a href="//maps.google.com?q='.$row['CustomerAddress'].' '.$row['CustomerCity'].', '.$row['CustomerState'].' '.$row['CustomerZIP'].'">'.$row['CustomerAddress'].'</a>';
//		echo '<td>'.$row['CustomerCity'];
//		echo '<td>'.$row['CustomerState'];
//		echo '<td>'.$row['CustomerZIP'];
		echo "\n";
	}
?>
</tbody>
</table>
<?php echo $count." record".($count != 1 ? "s" : "")." found."; ?>
</form>
<?php
}

if($_SESSION['ScrollTo'])
{
	echo '<script type="text/javascript">new Element.scrollTo("'.$_SESSION['ScrollTo'].'");</script>';
	unset($_SESSION['ScrollTo']);
}

unset($_SESSION['POST']);

include("include/footer.php");
?>
