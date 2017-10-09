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

if($_GET['type'])
	$_SESSION['GET']['type'] = $_GET['type'];
if($_GET['id'])
	$_SESSION['GET']['id'] = $_GET['id'];
if($_GET['apid'])
{
	$rowAP = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM APs WHERE APID='".clean($_GET['apid'])."'"));
	$_SESSION['GET']['type'] = "AP";
	$_SESSION['GET']['id'] = $rowAP['ID'];
	
	if($_GET['suid'])
	{
		$rowSU = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM SUs WHERE SUID='".clean($_GET['suid'])."' AND AP_ID='".$rowAP['ID']."'"));
		$_SESSION['GET']['type'] = "SU";
		$_SESSION['GET']['id'] = $rowSU['ID'];
	}
}

if($_SESSION['GET']['type'])
	$type = $_SESSION['GET']['type'];
if($_SESSION['GET']['id'])
	$ID = $_SESSION['GET']['id'];
	
if(isset($_SESSION['POST']))
{
	if($_SESSION['POST']['Action'] == "Send Config to Radio")
	{
		$form = $_SESSION['POST'];
		mysqli_query($dblink,"UPDATE SUWizard SET 
			push='1',
			status='Waiting for router'
			WHERE mac='".clean($form['MAC'])."'") or die(mysqli_error($dblink));
		
		unset($_SESSION['POST']);
	}
	if($_SESSION['POST']['Action'] == "Cancel Configuration")
	{
		$form = $_SESSION['POST'];
		mysqli_query($dblink,"UPDATE SUWizard SET 
			push='0',
			status=''
			WHERE mac='".clean($form['MAC'])."'") or die(mysqli_error($dblink));
		
		unset($_SESSION['POST']);
	}
	if($_SESSION['POST']['Action'] == "Cancel" or $_SESSION['POST']['Action'] == "Normal View")
	{
		unset($_SESSION['POST']);
	}
	if($_SESSION['POST']['Action'] == "Delete")
	{
		$form = $_SESSION['POST'];
		
		$refreshOpener = true;
		$message = 'Equipment deleted.<br><br><input onclick="window.close();" value="Close" type="button">';
		if($form['EquipmentType'] == "SU")
		{
			mysqli_query($dblink,"DELETE FROM SUs WHERE ID = '".clean($ID)."'") or die(mysqli_error($dblink));
		}
		else if($form['EquipmentType'] == "AP")
		{
			mysqli_query($dblink,"DELETE FROM APs WHERE ID = '".clean($ID)."'") or die(mysqli_error($dblink));
		}
	}
	else if($_SESSION['POST']['Action'] == "Save")
	{
		$form = $_SESSION['POST'];

		//reformat MAC for storage
		$form['MAC'] = strtoupper(preg_replace("/[-: ]/", "", $form['MAC']));
		
		//reformat date/time for storage
		if($form['InstallDate'])
			$form['InstallDate'] = date("Y-m-d H:i:s", strtotime($form['InstallDate']));
		else
			$form['InstallDate'] = "0000-00-00 00:00:00";

		if(!$error)
		{
			$refreshOpener = true;
			$message = "Equipment modified.";

			if($form['EquipmentType'] == "SU")
			{
				mysqli_query($dblink,"UPDATE SUs SET 
					InventoryNumber='".clean($form['InventoryNumber'])."', 
					SerialNumber='".clean($form['SerialNumber'])."', 
					AP_ID='".clean($form['AP_ID'])."', 
					SUID='".clean($form['SUID'])."', 
					LRAntenna_ID='".clean($form['LRAntenna_ID'])."', 
					MAC='".clean($form['MAC'])."', 
					IP='".clean($form['IP'])."', 
					Status_ID='".clean($form['Status_ID'])."', 
					CustomerName='".clean($form['CustomerName'])."', 
					CustomerAddress='".clean($form['CustomerAddress'])."', 
					CustomerCity='".clean($form['CustomerCity'])."', 
					CustomerState='".clean($form['CustomerState'])."', 
					CustomerZIP='".clean($form['CustomerZIP'])."', 
					CustomerGeocode='".clean($form['CustomerGeocode'])."', 
					CustomerPhone1='".clean($form['CustomerPhone1'])."', 
					CustomerPhone2='".clean($form['CustomerPhone2'])."', 
					CustomerPhone3='".clean($form['CustomerPhone3'])."', 
					NetworkNumber='".clean($form['NetworkNumber'])."', 
					InstallDate='".clean($form['InstallDate'])."', 
					Installer_ID='".clean($form['Installer_ID'])."', 
					Contract_ID='".clean($form['Contract_ID'])."', 
					DataRate_ID='".clean($form['DataRate_ID'])."', 
					Speed_Down='".clean($form['Speed_Down'])."', 
					Speed_Up='".clean($form['Speed_Up'])."', 
					Notes='".clean($form['Notes'])."' 
					WHERE ID='".clean($ID)."'") or die(mysqli_error($dblink));
			}
			else if($form['EquipmentType'] == "AP")
			{
				mysqli_query($dblink,"UPDATE APs SET 
					InventoryNumber='".clean($form['InventoryNumber'])."', 
					SerialNumber='".clean($form['SerialNumber'])."', 
					APID='".clean($form['APID'])."', 
					MAC='".clean($form['MAC'])."', 
					IP='".clean($form['IP'])."', 
					Status_ID='".clean($form['Status_ID'])."', 
					POP='".clean($form['POP'])."',
					Technology='".clean($form['Technology'])."',
					ColorCode='".clean($form['ColorCode'])."',
					Azimuth='".clean($form['Azimuth'])."',
					ElevationAGL='".clean($form['ElevationAGL'])."',
					RadiationAngle='".clean($form['RadiationAngle'])."',
					Altitude='".clean($form['Altitude'])."',
					ChBW='".clean($form['ChBW'])."',
					LocationName='".clean($form['LocationName'])."', 
					LocationAddress='".clean($form['LocationAddress'])."', 
					LocationCity='".clean($form['LocationCity'])."', 
					LocationState='".clean($form['LocationState'])."', 
					LocationZIP='".clean($form['LocationZIP'])."', 
					LocationGeocode='".clean($form['LocationGeocode'])."', 
					LocationPhone1='".clean($form['LocationPhone1'])."', 
					LocationPhone2='".clean($form['LocationPhone2'])."', 
					LocationPhone3='".clean($form['LocationPhone3'])."', 
					InstallDate='".clean($form['InstallDate'])."', 
					Notes='".clean($form['Notes'])."' 
					WHERE ID='".clean($ID)."'") or die(mysqli_error($dblink));
			}
			unset($_SESSION['POST']);
		}
	}
}

/*$pageTitle = "hurricane | Equipment Detail";*/
$noMenu = true;
$noFooter = true;
$noHeader = true;
if($_SESSION['POST']['Action'] == 'Edit')
	$useGMaps = true;
include("include/header.php");
	
if($refreshOpener)
{
	$_SESSION['ScrollTo'] = $type.$ID;
	echo '<script type="text/javascript">opener.location.reload();</script>';
}
if(!isset($_SESSION['POST']))
{
	if($type == "SU")
	{
		$configScript = false;
		$radiusScript = false;
		$queryResults = mysqli_query($dblink,"SELECT sus.*, lra.ConfigGenerator, lra.RadiusGenerator FROM SUs sus LEFT JOIN LRAntennas lra ON lra.ID = sus.LRAntenna_ID WHERE sus.ID='".clean($ID)."'");
		$row = mysqli_fetch_array($queryResults);
		if(!$row)
			$error = 'Equipment not found.';
		else
		{
			if($row['ConfigGenerator'] != '' && $row['IP'] != '')
				$configScript = $row['ConfigGenerator'];
			if($row['RadiusGenerator'] != '' && $row['MAC'] != '')
				$radiusScript = $row['RadiusGenerator'];
				
			$showSend = false;
			$disableEdits = false;
			if( $row['Status_ID'] == 14) {
					$showSend = true; 
					$rowWizardStatus = mysqli_fetch_array(mysqli_query($dblink,"SELECT push, running, status FROM SUWizard WHERE mac = '".$row['MAC']."'"));
					if($rowWizardStatus)
					{
						if($rowWizardStatus['running'] == 1)
						{
							$disableEdits = true;
							$pushbutton = "Running Configuration";
						}
						else if($rowWizardStatus['push'] == 1)
						{
							$pushbutton = "Cancel Configuration";
						}
						else
						{
							$pushbutton = "Send Config to Radio";
						}
					}
					else
					{
						$showSend = false;
					}
			}			
?>

<b>Equipment Type:</b> <?php echo $type; ?><br>
<br>
<table class="table_multi_form">
	<tr>
		<td width="300">
			<table class="table_form">
				<tr><td><b>Inventory Number</b><td class="spacer"><?php echo $row['InventoryNumber']; ?>
				<tr><td><b>Serial Number</b><td><?php echo $row['SerialNumber']; ?>
				<?php $rowAP = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM APs WHERE ID = '".$row['AP_ID']."'")); ?>
				<tr><td><b>AP/SUID</b><td><a href="//tsunami.somedomain/<?php echo $rowAP['APID']; ?>"><?php echo $rowAP['APID']; ?></a> 
					<a href="//tsunami.somedomain/<?php echo $rowAP['APID'].'/'.sprintf("%03s", $row['SUID']); ?>"><?php if($row['SUID']) echo sprintf("%03s", $row['SUID']); ?></a>
				<?php $rowLRA = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM LRAntennas WHERE ID = '".$row['LRAntenna_ID']."'")); ?>
				<tr><td><b>Antenna</b><td><?php echo $rowLRA['LRAntenna']; ?>
				<tr><td><b>MAC</b><td><?php echo reformatMAC($row['MAC']); ?>
				<tr><td><b>IP</b><td><?php echo $row['IP']; ?>
				<?php $rowStatus = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM Statuses WHERE ID = '".$row['Status_ID']."'")); ?>
				<tr><td><b>Status</b><td><span id="suStatus"><?php echo $rowStatus['Status']; ?></span><br><em id="confStatus"><?php if ( $showSend && $rowWizardStatus['push'] == 1 ) echo $rowWizardStatus['status']; ?></em>
				<tr><td><b>Network Number</b><td><?php echo $row['NetworkNumber']; ?>
			</table>
		<td width="300">
			<table class="table_form" width="30%">
				<tr><td><b>Name</b><td class="spacer"><?php echo $row['CustomerName']; ?>
				<tr><td><b>Address</b><td width="80%"><?php echo '<a href="//maps.google.com?q='.$row['CustomerAddress'].' '.$row['CustomerCity'].', '.$row['CustomerState'].' '.$row['CustomerZIP'].'">'.$row['CustomerAddress'].'</a>'; ?>
				<tr><td><b>City</b><td><?php echo $row['CustomerCity']; ?>
				<tr><td><b>State</b><td><?php echo $row['CustomerState']; ?>
				<tr><td><b>ZIP</b><td><?php echo $row['CustomerZIP']; ?>
				<tr><td><b>Geocode</b><td><?php echo $row['CustomerGeocode']; ?>
				<tr><td><b>Phone 1</b><td><?php echo $row['CustomerPhone1']; ?>
				<tr><td><b>Phone 2</b><td><?php echo $row['CustomerPhone2']; ?>
				<tr><td><b>Phone 3</b><td><?php echo $row['CustomerPhone3']; ?>
			</table>
		<td width="300">
			<table class="table_form" width="30%">
				<tr><td><b>Install Date</b><td class="spacer"><?php if($row['InstallDate'] != "0000-00-00 00:00:00") echo date("n/j/Y", strtotime($row['InstallDate'])); ?>
				<?php $rowInstaller = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM Installers WHERE ID = '".$row['Installer_ID']."'")); ?>
				<tr><td><b>Installer</b><td><?php echo $rowInstaller['Installer']; ?>
				<?php $rowContract = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM Contracts WHERE ID = '".$row['Contract_ID']."'")); ?>
				<tr><td><b>Contract</b><td><?php echo $rowContract['Contract']; ?>
				<?php $rowDataRate = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM DataRates WHERE ID = '".$row['DataRate_ID']."'")); ?>
				<tr><td><b>Data Rate</b><td><?php echo $rowDataRate['DataRate']; ?>
				<tr><td><b>Speed Down</b><td><?php echo $row['Speed_Down']; ?>
				<tr><td><b>Speed Up</b><td><?php echo $row['Speed_Up']; ?>
				<tr class="tall"><td><b>Notes<b><td width="180"><?php echo nl2br(reformatNotes($row['Notes'])); ?>
			</table>
</table>
<form name="ModifyEquipment" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<table class="table_wide">
	<tr>
		<td>
			<input type="submit" name="Action" id="editPushButton" value="Edit"<?php if ($disableEdits) echo ' disabled=true'; ?>> <input onclick="window.close();" value="Close" type="button"> 
			<?php if($configScript){ ?><input onclick="loadConfig('<?php echo $configScript; ?>', <?php echo $ID; ?>)" value="Get Config" type="button"/> <?php } ?>
			<?php if($radiusScript){ ?><input onclick="setRadius('<?php echo $radiusScript; ?>', <?php echo $ID; ?>)" value="Add to Radius" type="button"/> <?php } ?>
		<td class="right">
			<input type="submit" name="Action" value="Copy View">
			<span id="confPushContainer" style="<?php if( !$showSend ) { echo 'display:none;'; } ?>">
				<input type="hidden" name="MAC" value="<?php echo $row['MAC']; ?>">
				<input type="submit" name="Action" id="confPushButton" value="<?php echo $pushbutton; ?>"<?php if ($disableEdits) echo ' disabled=true'; ?>>
			</span>
</table>
</form>

<script type="text/javascript">
	function runStatusCheck(){
		setTimeout(function(){
			new Ajax.Request('/api/status.php?mac=<?php echo $row['MAC']; ?>', {
			  onSuccess: function(response) {
			      var status = JSON.parse(response.responseText);
			      $("suStatus").update(status.su);
			      
			      if(status.status == "false")
			      {
				      $("confPushContainer").hide();
				      $("editPushButton").enable();
				      $("confStatus").update("");
			      }
			      else
			      {
			      	  $("confPushContainer").show();
			      	  
				      if(status.push == 1 || status.running == 1)
					    $("confStatus").update(status.status);
					  else
					  	$("confStatus").update("");
					  	
				      if(status.running == 1)
				      {
				      	$("confPushButton").disable();
				      	$("editPushButton").disable();
						$("confPushButton").value = "Running Configuration";
				      }
				      else
				      {
				      	$("confPushButton").enable();
				      	$("editPushButton").enable();
				      	
					  	if(status.push == 1)
							$("confPushButton").value = "Cancel Configuration";
						else
							$("confPushButton").value = "Send Config to Radio";
						console.log("Should be here");
				      }
				  }
			  },
			  onComplete: function() {
			      runStatusCheck();
			  }
			});
		}, 5000)
	}
	runStatusCheck();
</script>

<?php
		}
	}
	else if($type == "AP")
	{
		$queryResults = mysqli_query($dblink,"SELECT * FROM APs WHERE ID='".clean($ID)."'");
		$row = mysqli_fetch_array($queryResults);
		if(!$row)
			$error = 'Equipment not found.';
		else
		{
?>

<b>Equipment Type:</b> <?php echo $type; ?><br>
<br>
<table class="table_multi_form"Save>
	<tr>
		<td>
			<table class="table_form">
				<tr><td><b>Inventory Number</b><td><?php echo $row['InventoryNumber']; ?>
				<tr><td><b>Serial Number</b><td><?php echo $row['SerialNumber']; ?>
				<tr><td><b>APID</b><td><a href="//tsunami.somedomain/<?php echo $row['APID']; ?>"><?php echo $row['APID']; ?></a> 
				<?php $rowTech = mysqli_fetch_assoc(mysqli_query($dblink,"SELECT name, hasColorCode, apHasExternalAntenna FROM Technologies WHERE id='".$row['Technology']."'")); ?>
				<tr><td><b>Technology:</b><td><?php echo $rowTech['name']; ?>
				<?php $rowPop = mysqli_fetch_array(mysqli_query($dblink,"SELECT name FROM POPs WHERE ID='".$row['POP']."'")); ?>
				<tr><td><b>POP:</b><td><?php echo $rowPop['name']; ?>
				<tr style="<?php if(!$rowTech || $rowTech['hasColorCode'] == 0){ echo 'display:none'; } ?>"><td><b>ColorCode:</b><td><?php echo $row['ColorCode']; ?>
				<tr><td><b>Azimuth:</b><td><?php echo $row['Azimuth']; ?>&deg;
				<tr><td><b>Elevation AGL:</b><td><?php echo $row['ElevationAGL']; ?> ft
				<tr><td><b>Radiation Angle:</b><td><?php echo $row['RadiationAngle']; ?>&deg;
				<tr><td><b>Tilt:</b><td><?php echo $row['Altitude']; ?>&deg;
				<tr><td><b>Ch BW:</b><td><?php echo $row['ChBW']; ?> MHz
				<tr><td><b>MAC</b><td><?php echo reformatMAC($row['MAC']); ?>
				<tr><td><b>IP</b><td><a href="//<?php echo $row['IP']; ?>"><?php echo $row['IP']; ?></a>
				<?php $rowStatus = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM Statuses WHERE ID = '".$row['Status_ID']."'")); ?>
				<tr><td><b>Status</b><td><?php echo $rowStatus['Status']; ?>
			</table>
		<td>
			<table class="table_form">
				<tr><td><b>Name</b><td><?php echo $row['LocationName']; ?>
				<tr><td><b>Address</b><td><?php echo '<a href="//maps.google.com?q='.$row['LocationAddress'].' '.$row['LocationCity'].', '.$row['LocationState'].' '.$row['LocationZIP'].'">'.$row['LocationAddress'].'</a>'; ?>
				<tr><td><b>City</b><td><?php echo $row['LocationCity']; ?>
				<tr><td><b>State</b><td><?php echo $row['LocationState']; ?>
				<tr><td><b>ZIP</b><td><?php echo $row['LocationZIP']; ?>
				<tr><td><b>Geocode</b><td><?php echo $row['LocationGeocode']; ?>
				<tr><td><b>Phone 1</b><td><?php echo $row['LocationPhone1']; ?>
				<tr><td><b>Phone 2</b><td><?php echo $row['LocationPhone2']; ?>
				<tr><td><b>Phone 3</b><td><?php echo $row['LocationPhone3']; ?>
			</table>
		<td>
			<table class="table_form">
				<tr><td><b>Install Date</b><td><?php if($row['InstallDate'] != "0000-00-00 00:00:00") echo date("n/j/Y", strtotime($row['InstallDate'])); ?>
				<tr class="tall"><td><b>Notes<b><td><?php echo nl2br(reformatNotes($row['Notes'])); ?>
			</table>
</table>
<form name="ModifyEquipment" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<table class="table_wide">
	<tr>
		<td>
			<input type="submit" name="Action" value="Edit"> <input onclick="window.close();" value="Close" type="button">
		<td class="right">
			<input type="submit" name="Action" value="Copy View">
</table>
</form>
<?php
		}
	}
}
else if($_SESSION['POST']['Action'] == "Copy View")
{
	if($type == "SU")
	{
		$queryResults = mysqli_query($dblink,"SELECT * FROM SUs WHERE ID='".clean($ID)."'");
		$row = mysqli_fetch_array($queryResults);
		if(!$row)
			$error = 'Equipment not found.';
		else
		{
			echo $type." ".$row['InventoryNumber']."<br>";
			if($row['SerialNumber'])
				echo $row['SerialNumber']."<br>";

			$rowAP = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM APs WHERE ID = '".$row['AP_ID']."'"));
			echo $rowAP['APID']."-".sprintf("%03s", $row['SUID'])."<br>";

			$rowLRA = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM LRAntennas WHERE ID = '".$row['LRAntenna_ID']."'"));
			echo $rowLRA['LRAntenna']."<br>";

			echo reformatMAC($row['MAC'])."<br>";
			echo $row['IP']."<br>";

			$rowStatus = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM Statuses WHERE ID = '".$row['Status_ID']."'"));
			echo $rowStatus['Status']."<br>";

			echo $row['CustomerName']."<br>";
			echo $row['CustomerAddress']."<br>";
			echo $row['CustomerCity'].", ".$row['CustomerState']." ".$row['CustomerZIP']."<br>";
			if($row['CustomerPhone1'])
				echo $row['CustomerPhone1']."<br>";
			if($row['CustomerPhone2'])
				echo $row['CustomerPhone2']."<br>";
			if($row['CustomerPhone3'])
				echo $row['CustomerPhone3']."<br>";
			if($row['NetworkNumber'])
				echo $row['NetworkNumber']."<br>";
			if($row['InstallDate'] != "0000-00-00 00:00:00")
				echo date("n/j/Y", strtotime($row['InstallDate']))."<br>";

			$rowInstaller = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM Installers WHERE ID = '".$row['Installer_ID']."'"));
			if($rowInstaller['Installer'])
				echo $rowInstaller['Installer']."<br>";

			$rowContract = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM Contracts WHERE ID = '".$row['Contract_ID']."'"));
			if($rowContract['Contract'])
				echo $rowContract['Contract']."<br>";

			$rowDataRate = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM DataRates WHERE ID = '".$row['DataRate_ID']."'"));
			if($rowDataRate['DataRate'])
				echo $rowDataRate['DataRate']."<br>";

			if($row['Speed_Down'])
				echo $row['Speed_Down']."d/u";
			if($row['Speed_Up'])
				echo $row['Speed_Up']."<br>";

			if($row['Notes'])
				echo nl2br($row['Notes'])."<br>";
			echo "<br>";
?>
<form name="ModifyEquipment" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<table class="table_wide">
	<tr>
		<td>
			<input type="submit" name="Action" value="Edit"> <input onclick="window.close();" value="Close" type="button">
		<td class="right">
			<input type="submit" name="Action" value="Normal View">
</table>
</form>
<?php
		}
	}
	else if($type == "AP")
	{
		$queryResults = mysqli_query($dblink,"SELECT * FROM APs WHERE ID='".clean($ID)."'");
		$row = mysqli_fetch_array($queryResults);
		if(!$row)
			$error = 'Equipment not found.';
		else
		{
			echo $type." ".$row['InventoryNumber']."<br>";
			if($row['SerialNumber'])
				echo $row['SerialNumber']."<br>"; 
			echo $row['APID']."<br>"; 
			echo reformatMAC($row['MAC'])."<br>";
			echo $row['IP']."<br>";

			$rowStatus = mysqli_fetch_array(mysqli_query($dblink,"SELECT * FROM Statuses WHERE ID = '".$row['Status_ID']."'"));
			echo $rowStatus['Status']."<br>";

			echo $row['LocationName'];
			if($row['ColorCode'] !== 0)
				echo ' (' . $row['ColorCode'] . ')';
			echo "<br>";
			echo $row['LocationAddress']."<br>";
			echo $row['LocationCity'].", ".$row['LocationState']." ".$row['LocationZIP']."<br>";
			if($row['LocationPhone1'])
 				echo $row['LocationPhone1']."<br>";
			if($row['LocationPhone2'])
 				echo $row['LocationPhone2']."<br>";
			if($row['LocationPhone3'])
				echo $row['LocationPhone3']."<br>";
			if($row['InstallDate'] != "0000-00-00 00:00:00")
				echo "Installed ".date("n/j/Y", strtotime($row['InstallDate']));
			if($row['Notes'])
				echo nl2br($row['Notes']);
			echo "<br>";
?>
<form name="ModifyEquipment" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<table class="table_wide">
	<tr>
		<td>
			<input type="submit" name="Action" value="Edit"> <input onclick="window.close();" value="Close" type="button">
		<td class="right">
			<input type="submit" name="Action" value="Normal View">
</table>
</form>

<?php
		}
	}
}
else if($_SESSION['POST']['Action'] == "Edit")
{
	if($type == "SU")
	{
		$queryResults = mysqli_query($dblink,"SELECT * FROM SUs WHERE ID='".clean($ID)."'");
		$row = mysqli_fetch_array($queryResults);
		if(!$row)
			$error = 'Equipment not found.';
		else
		{
?>
<b>Equipment Type:</b> SU<br>
<br>
<form id="SUForm" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<input type="hidden" name="EquipmentType" value="SU">
<table>
	<tr>
		<td>
			<table class="table_form">
				<tr><td><b>Inventory Number</b><td><input name="InventoryNumber" type="text" size="16" value="<?php echo $row['InventoryNumber']?>">
				<tr><td><b>Serial Number</b><td><input name="SerialNumber" type="text" size="16" value="<?php echo $row['SerialNumber']?>">
				<tr><td><b>AP/SUID</b>
					<td><select name="AP_ID">
						<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM APs ORDER BY APID ASC") or die(mysqli_error($dblink));
						while($rowField = mysqli_fetch_array($queryResults))
						{
							if($rowField['APID'])
							{
								if($rowField['ID'] == $row['AP_ID'])
									echo '<option value="'.$rowField['ID'].'" selected="selected">'.$rowField['APID'];
								else
									echo '<option value="'.$rowField['ID'].'">'.$rowField['APID'];
							}
						}
						?>
					</select>
					<input type="text" name="SUID" size="4" value="<?php echo $row['SUID']?>">
				<tr><td><b>Antenna</b>
					<td><select name="LRAntenna_ID">
						<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM LRAntennas ORDER BY LRAntenna ASC") or die(mysqli_error($dblink));
						while($rowField = mysqli_fetch_array($queryResults))
						{
							if($rowField['ID'] == $row['LRAntenna_ID'])
								echo '<option value="'.$rowField['ID'].'" selected="selected">'.$rowField['LRAntenna'];
							else
								echo '<option value="'.$rowField['ID'].'">'.$rowField['LRAntenna'];
						}
						?>
					</select>
				<tr><td><b>MAC</b><td><input type="text" name="MAC" size="16" value="<?php echo reformatMAC($row['MAC']); ?>">
				<tr><td><b>IP</b><td><input type="text" name="IP" size="16" value="<?php echo $row['IP']; ?>">
				<tr><td><b>Status</b>
				<?php if( $row['Status_ID'] == 14) { ?>
					<td><input type="hidden" value="<?php echo $row['Status_ID']; ?>" name="Status_ID"/>Configuration</td>
				<?php }else{ ?>
					<td><select name="Status_ID">
						<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Statuses ORDER BY Status ASC") or die(mysqli_error($dblink));
						while($rowField = mysqli_fetch_array($queryResults))
						{
							if($rowField['ID'] == $row['Status_ID'])
								echo '<option value="'.$rowField['ID'].'" selected="selected">'.$rowField['Status'];
							else
								echo '<option value="'.$rowField['ID'].'">'.$rowField['Status'];
						}
						?>
					</select></td>
				<?php } ?>
				<tr><td><b>Network Number</b><td><input type="text" name="NetworkNumber" value="<?php echo $row['NetworkNumber']?>">
			</table>
		<td>
			<table class="table_form">
				<tr><td><b>Name</b><td><input type="text" name="CustomerName" value="<?php echo $row['CustomerName']?>">
				<tr><td><b>Address</b><td><input type="text" name="CustomerAddress" value="<?php echo $row['CustomerAddress']?>">
				<tr><td><b>City</b><td><input type="text" name="CustomerCity" value="<?php echo $row['CustomerCity']?>">
				<tr><td><b>State</b>
					<td><select name="CustomerState">
						<option value=""></option>
						<?php
						echo '<option value="NM">NM';
						echo '<option value="">';
						foreach($state_list as $state)
						{
							if($state == $row['CustomerState'])
								echo '<option value="'.$state.'" selected="selected">'.$state;
							else
								echo '<option value="'.$state.'">'.$state;
						}
						?>
					</select>

				<tr><td><b>ZIP</b><td><input type="text" size="12" name="CustomerZIP" value="<?php echo $row['CustomerZIP']?>">
				<tr><td><b>Geocode</b><td><input id="geocode" type="text" size="15" name="CustomerGeocode" value="<?php echo $row['CustomerGeocode']?>"><input type="button" value="Find" onclick="DoGeocode(this.form);">
				<tr><td><b>Phone 1</b><td><input type="text" name="CustomerPhone1" value="<?php echo $row['CustomerPhone1']?>">
				<tr><td><b>Phone 2</b><td><input type="text" name="CustomerPhone2" value="<?php echo $row['CustomerPhone2']?>">
				<tr><td><b>Phone 3</b><td><input type="text" name="CustomerPhone3" value="<?php echo $row['CustomerPhone3']?>">
			</table>
		<td>
			<table class="table_form">
				<tr><td><b>Install Date</b><td><input type="text" size="10" name="InstallDate" value="<?php echo ($row['InstallDate'] != "0000-00-00 00:00:00" ? date("n/j/Y", strtotime($row['InstallDate'])) : ""); ?>"><input type="button" onclick="FillToday(this.form);" value="Today">
				<tr><td><b>Installer</b>
					<td><select name="Installer_ID">
						<option value="">
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Installers ORDER BY Installer ASC") or die(mysqli_error($dblink));
						while($rowField = mysqli_fetch_array($queryResults))
						{
							if($rowField['ID'] == $row['Installer_ID'])
								echo '<option value="'.$rowField['ID'].'"selected="selected">'.$rowField['Installer'];
							else
								echo '<option value="'.$rowField['ID'].'">'.$rowField['Installer'];
						}
						?>
					</select>
				<tr><td><b>Contract</b>
					<td><select name="Contract_ID">
						<option value="">
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Contracts ORDER BY Contract ASC") or die(mysqli_error($dblink));
						while($rowField = mysqli_fetch_array($queryResults))
						{
							if($rowField['ID'] == $row['Contract_ID'])
								echo '<option value="'.$rowField['ID'].'" selected="selected">'.$rowField['Contract'];
							else
								echo '<option value="'.$rowField['ID'].'">'.$rowField['Contract'];
						}
						?>
					</select>
				<tr><td><b>Data Rate</b>
					<td><select name="DataRate_ID" onchange="setSpeed(this.value);">
						<option value="">
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM DataRates ORDER BY DataRate ASC") or die(mysqli_error($dblink));
						while($rowField = mysqli_fetch_array($queryResults))
						{
							if($rowField['ID'] == $row['DataRate_ID'])
								echo '<option value="'.$rowField['ID'].'" selected="selected">'.$rowField['DataRate'];
							else
								echo '<option value="'.$rowField['ID'].'">'.$rowField['DataRate'];
						}
						?>
					</select>
				<tr><td><b>Speed Down</b><td><input name="Speed_Down" type="text" size="12" name="Speed_Down" id="Speed_Down" value="<?php echo $row['Speed_Down']?>">
				<tr><td><b>Speed Up</b><td><input type="text" size="12" name="Speed_Up" id="Speed_Up" value="<?php echo $row['Speed_Up']?>">
				<tr class="tall"><td><b>Notes</b><td><textarea rows="4" cols="32" name="Notes"><?php echo $row['Notes']?></textarea>
			</table>
</table>
<input name="Action" value="Save" type="submit"> <input onclick="return confirmDelete();" name="Action" value="Delete" type="submit"> <input name="Action" value="Cancel" type="submit">
</form>
<?php
		}
	}
	else if($type == "AP")
	{
		$queryResults = mysqli_query($dblink,"SELECT * FROM APs WHERE ID='".clean($ID)."'");
		$row = mysqli_fetch_array($queryResults);
		if(!$row)
			$error = 'Equipment not found.';
		else
		{
			$technologyResults = false;
			if($row['Technology'] != 0)
				$technologyResults = mysqli_fetch_assoc(mysqli_query($dblink,"SELECT * FROM Technologies WHERE id='" . $row['Technology'] . "'"));
?>
<b>Equipment Type:</b> AP<br>
<br>
<form id="APForm" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<input type="hidden" name="EquipmentType" value="AP">
<table>
	<tr>
		<td>
			<table class="table_form">
				<tr><td><b>Inventory Number</b><td><input name="InventoryNumber" type="text" size="16" value="<?php echo $row['InventoryNumber']?>">
				<tr><td><b>Serial Number</b><td><input name="SerialNumber" type="text" size="16" value="<?php echo $row['SerialNumber']?>">
				<tr><td><b>APID</b><td><input type="text" name="APID" size="16" value="<?php echo $row['APID']?>">
				<tr><td><b>Technology:</b>
					<td><select name="Technology" onchange="changeTech(this)">
							<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT id, name FROM Technologies ORDER BY name");
						while($rowField = mysqli_fetch_assoc($queryResults))
							printf('<option value="%s"%s>%s</option>', $rowField['id'], $row['Technology'] == $rowField['id'] ? ' selected="selected"' : '', $rowField['name']);
						?>
					</select>
				<tr><td><b>POP:</b>
					<td><select name="POP">
							<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT ID, name FROM POPs ORDER BY name");
						while($rowField = mysqli_fetch_array($queryResults))
							printf('<option value="%s"%s>%s</option>', $rowField['ID'], $row['POP'] == $rowField['ID'] ? ' selected="selected"' : '', $rowField['name']);
						?>
					</select>
				<tr id="colorCodeRow" style="<?php if(!$technologyResults || $technologyResults['hasColorCode'] == 0) echo 'display:none;'; ?>"><td><b>ColorCode</b><td><input type="text" name="ColorCode" size="3" value="<?php echo $row['ColorCode']?>">
				<tr><td><b>Azimuth</b><td><input type="text" name="Azimuth" size="14" value="<?php echo $row['Azimuth']?>">&deg;
				<tr><td><b>Elevation AGL</b><td><input type="text" name="ElevationAGL" size="14" value="<?php echo $row['ElevationAGL']?>">ft
				<tr><td><b>Radiation Angle</b><td><input type="text" name="RadiationAngle" size="14" value="<?php echo $row['RadiationAngle']?>">&deg;
				<tr><td><b>Tilt</b><td><input type="text" name="Altitude" size="14" value="<?php echo $row['Altitude']?>">&deg;
				<tr><td><b>Ch BW</b><td><input type="text" name="ChBW" size="14" value="<?php echo $row['ChBW']?>">MHz
				<tr><td><b>MAC</b><td><input type="text" name="MAC" size="16" value="<?php echo reformatMAC($row['MAC']); ?>">
				<tr><td><b>IP</b><td><input type="text" name="IP" size="16" value="<?php echo $row['IP']; ?>">
				<tr><td><b>Status</b>
					<td><select name="Status_ID">
						<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Statuses ORDER BY Status ASC") or die(mysqli_error($dblink));
						while($rowField = mysqli_fetch_array($queryResults))
						{
							if($rowField['ID'] == $row['Status_ID'])
								echo '<option value="'.$rowField['ID'].'" selected="selected">'.$rowField['Status'];
							else
								echo '<option value="'.$rowField['ID'].'">'.$rowField['Status'];
						}
						?>
					</select>
			</table>
		<td>
			<table class="table_form">
				<tr><td><b>Name</b><td><input type="text" name="LocationName" value="<?php echo $row['LocationName']?>">
				<tr><td><b>Address</b><td><input type="text" name="LocationAddress" value="<?php echo $row['LocationAddress']?>">
				<tr><td><b>City</b><td><input type="text" name="LocationCity" value="<?php echo $row['LocationCity']?>">
				<tr><td><b>State</b>
					<td><select name="LocationState">
						<option value=""></option>
						<?php
						echo '<option value="NM">NM';
						echo '<option value="">';
						foreach($state_list as $state)
						{
							if($state == $row['LocationState'])
								echo '<option value="'.$state.'" selected="selected">'.$state;
							else
								echo '<option value="'.$state.'">'.$state;
						}
						?>
					</select>

				<tr><td><b>ZIP</b><td><input type="text" size="12" name="LocationZIP" value="<?php echo $row['LocationZIP']?>">
				<tr><td><b>Geocode<td><input id="geocode" type="text" size="13" name="LocationGeocode" value="<?php echo $row['LocationGeocode']?>"><input type="button" value="Find" onclick="DoGeocode(this.form);">
				<tr><td><b>Phone 1</b><td><input type="text" name="LocationPhone1" value="<?php echo $row['LocationPhone1']?>">
				<tr><td><b>Phone 2</b><td><input type="text" name="LocationPhone2" value="<?php echo $row['LocationPhone2']?>">
				<tr><td><b>Phone 3</b><td><input type="text" name="LocationPhone3" value="<?php echo $row['LocationPhone3']?>">
			</table>
		<td>
			<table class="table_form">
				<tr><td><b>Install Date</b><td><input type="text" size="10" name="InstallDate" value="<?php echo ($row['InstallDate'] != "0000-00-00 00:00:00" ? date("n/j/Y", strtotime($row['InstallDate'])) : ""); ?>"><input type="button" onclick="FillToday(this.form);" value="Today">
				<tr class="tall"><td><b>Notes</b><td><textarea rows="4" cols="32" name="Notes"><?php echo $row['Notes']?></textarea>
			</table>
</table>
<input name="Action" value="Save" type="submit"> <input name="Action" value="Delete" type="submit"> <input name="Action" value="Cancel" type="submit">
</form>
<?php
		}
	}
}


unset($_SESSION['POST']);

include("include/footer.php");
?>
