<?php
include("include/common.php");
session_start();

$onload="loadEvtHandlers();";

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
		unset($_SESSION['POST']);
	}
	else if($_SESSION['POST']['Action'] == "Add")
	{
		$form = $_SESSION['POST'];

		//reformat MAC for storage
		$form['MAC'] = strtoupper(preg_replace("/[-:. ]/", "", $form['MAC']));
		
		//reformat date/time for storage
		if($form['InstallDate'])
			$form['InstallDate'] = date("Y-m-d H:i:s", strtotime($form['InstallDate']));
		else
			$form['InstallDate'] = "";

		if(!$error)
		{
			$message = "Equipment added.";

			if($form['EquipmentType'] == "SU")
			{
				mysqli_query($dblink,"INSERT INTO SUs VALUES (
					'', 
					'".clean($form['InventoryNumber'])."', 
					'".clean($form['SerialNumber'])."', 
					'".clean($form['AP_ID'])."', 
					'".clean($form['SUID'])."', 
					'".clean($form['LRAntenna_ID'])."', 
					'".clean($form['MAC'])."', 
					'".clean($form['IP'])."', 
					'".clean($form['Status_ID'])."', 
					'".clean($form['CustomerName'])."', 
					'".clean($form['CustomerAddress'])."', 
					'".clean($form['CustomerCity'])."', 
					'".clean($form['CustomerState'])."', 
					'".clean($form['CustomerZIP'])."', 
					'".clean($form['CustomerGeocode'])."', 
					'".clean($form['CustomerPhone1'])."', 
					'".clean($form['CustomerPhone2'])."', 
					'".clean($form['CustomerPhone3'])."', 
					'".clean($form['NetworkNumber'])."', 
					'".clean($form['InstallDate'])."', 
					'".clean($form['Installer_ID'])."', 
					'".clean($form['Contract_ID'])."', 
					'".clean($form['DataRate_ID'])."', 
					'".clean($form['DataRate_ID'])."', 
					'".clean($form['DataRate_ID'])."', 
					'".clean($form['Notes'])."',
					NOW())") or die(mysqli_error($dblink));
			}
			else if($form['EquipmentType'] == "AP")
			{
				mysqli_query($dblink,"INSERT INTO APs (InventoryNumber,SerialNumber,APID,MAC,IP,Status_ID,POP,LocationName,LocationAddress,LocationCity,LocationState,LocationZIP,LocationGeocode,LocationPhone1,LocationPhone2,LocationPhone3,InstallDate,Notes) VALUES (
					'".clean($form['InventoryNumber'])."', 
					'".clean($form['SerialNumber'])."', 
					'".clean($form['APID'])."', 
					'".clean($form['MAC'])."', 
					'".clean($form['IP'])."', 
					'".clean($form['Status_ID'])."', 
					'".clean($form['POP'])."',
					'".clean($form['LocationName'])."', 
					'".clean($form['LocationAddress'])."', 
					'".clean($form['LocationCity'])."', 
					'".clean($form['LocationState'])."', 
					'".clean($form['LocationZIP'])."', 
					'".clean($form['LocationGeocode'])."', 
					'".clean($form['LocationPhone1'])."', 
					'".clean($form['LocationPhone2'])."', 
					'".clean($form['LocationPhone3'])."', 
					'".clean($form['InstallDate'])."', 
					'".clean($form['Notes'])."'
					)") or die(mysqli_error($dblink));
							}
		}

		unset($_SESSION['POST']);
	}
}

$useGMaps = true;
$pageTitle = "hurricane | Add Equipment";
include("include/header.php");

?>
<div style="float:right;margin-right:15px;"><a href="./bulkadd.php">Bulk Add SUs</a></div>
<b>Equipment Type:</b>
<select onchange='ShowAddForm(this.value);'>
	<option value='SU' <?php if($_SESSION['POST']['EquipmentType'] == "SU") echo 'selected="selected"'; ?>>SU</option>
	<option value='AP' <?php if($_SESSION['POST']['EquipmentType'] == "AP") echo 'selected="selected"'; ?>>AP</option>
</select><br>
<br>
<div id="AddSU" <?php if($_SESSION['POST']['EquipmentType'] == "AP") echo "style='display:none'"; ?>>
<form id="SUForm" name="AddEquipment" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<input type="hidden" name="EquipmentType" value="SU">
<table class="table_multi_form">
	<tr>
		<td>
			<table class="table_form">
				<tr><td><b>Inventory Number</b><td><input name="InventoryNumber" type="text" size="16" value="<?php echo $_SESSION['POST']['InventoryNumber']?>">
				<tr><td><b>Serial Number</b><td><input id="input_serial" name="SerialNumber" type="text" size="16" value="<?php echo $_SESSION['POST']['SerialNumber']?>">
				<tr><td><b>AP/SUID</b>
					<td><select name="AP_ID">
						<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM APs ORDER BY APID ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['APID'])
							{
								if($row['ID'] == $_SESSION['POST']['AP_ID'])
									echo '<option value="'.$row['ID'].'" selected="selected">'.$row['APID'];
								else
									echo '<option value="'.$row['ID'].'">'.$row['APID'];
							}
						}
						?>
					</select>
					<input type="text" name="SUID" size="4" value="<?php echo $_SESSION['POST']['SUID']?>">
				<tr><td><b>Antenna</b>
					<td><select name="LRAntenna_ID">
						<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM LRAntennas ORDER BY LRAntenna ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['ID'] == $_SESSION['POST']['LRAntenna_ID'])
								echo '<option value="'.$row['ID'].'" selected="selected">'.$row['LRAntenna'];
							else
								echo '<option value="'.$row['ID'].'">'.$row['LRAntenna'];
						}
						?>
					</select>
				<tr><td><b>MAC</b><td><input id="input_mac" type="text" name="MAC" size="16" value="<?php echo $_SESSION['POST']['MAC']?>">
				<tr><td><b>IP</b><td><input type="text" name="IP" size="16" value="<?php echo $_SESSION['POST']['IP']?>">
				<tr><td><b>Status</b>
					<td><select name="Status_ID">
						<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Statuses ORDER BY Status ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['ID'] == $_SESSION['POST']['Status_ID'])
								echo '<option value="'.$row['ID'].'" selected="selected">'.$row['Status'];
							else
								echo '<option value="'.$row['ID'].'">'.$row['Status'];
						}
						?>
					</select>
			</table>
		<td>
			<table class="table_form">
				<tr><td><b>Name</b><td><input type="text" name="CustomerName" value="<?php echo $_SESSION['POST']['CustomerName']?>">
				<tr><td><b>Address</b><td><input type="text" name="CustomerAddress" value="<?php echo $_SESSION['POST']['CustomerAddress']?>">
				<tr><td><b>City</b><td><input type="text" name="CustomerCity" value="<?php echo $_SESSION['POST']['CustomerCity']?>">
				<tr><td><b>State</b>
					<td><select name="CustomerState">
						<option value=""></option>
						<?php
						echo '<option value="NM">NM';
						echo '<option value="">';
						foreach($state_list as $state)
						{
							if($state == $_SESSION['POST']['CustomerState'])
								echo '<option value="'.$state.'" selected="selected">'.$state;
							else
								echo '<option value="'.$state.'">'.$state;
						}
						?>
					</select>

				<tr><td><b>ZIP</b><td><input type="text" size="12" name="CustomerZIP" value="<?php echo $_SESSION['POST']['CustomerZIP']?>">
				<tr><td><b>Geocode</b><td><input id="geocode1" type="text" size="13" name="CustomerGeocode" value="<?php echo $_SESSION['POST']['CustomerGeocode']?>"><input type="button" value="Find" onclick="DoGeocode(this.form);">
				<tr><td><b>Phone 1</b><td><input type="text" name="CustomerPhone1" value="<?php echo $_SESSION['POST']['CustomerCBR1']?>">
				<tr><td><b>Phone 2</b><td><input type="text" name="CustomerPhone2" value="<?php echo $_SESSION['POST']['CustomerCBR2']?>">
				<tr><td><b>Phone 3</b><td><input type="text" name="CustomerPhone3" value="<?php echo $_SESSION['POST']['CustomerCBR3']?>">
				<tr><td><b>Network Number</b><td><input type="text" name="NetworkNumber" value="<?php echo $_SESSION['POST']['NetworkNumber']?>">
			</table>
		<td>
			<table class="table_form">
				<tr><td><b>Install Date</b><td><input type="text" size="10" name="InstallDate" value="<?php echo $_SESSION['POST']['InstallDate']?>">
				<tr><td><b>Installer</b>
					<td><select name="Installer_ID">
						<option value="">
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Installers ORDER BY Installer ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['ID'] == $_SESSION['POST']['Installer_ID'])
								echo '<option value="'.$row['ID'].'"selected="selected">'.$row['Installer'];
							else
								echo '<option value="'.$row['ID'].'">'.$row['Installer'];
						}
						?>
					</select>
				<tr><td><b>Contract</b>
					<td><select name="Contract_ID">
						<option value="">
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Contracts ORDER BY Contract ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['ID'] == $_SESSION['POST']['Contract_ID'])
								echo '<option value="'.$row['ID'].'" selected="selected">'.$row['Contract'];
							else
								echo '<option value="'.$row['ID'].'">'.$row['Contract'];
						}
						?>
					</select>
				<tr><td><b>Data Rate</b>
					<td><select name="DataRate_ID">
						<option value="">
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM DataRates ORDER BY DataRate ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['ID'] == $_SESSION['POST']['DataRate_ID'])
								echo '<option value="'.$row['ID'].'" selected="selected">'.$row['DataRate'];
							else
								echo '<option value="'.$row['ID'].'">'.$row['DataRate'];
						}
						?>
					</select>
				<tr class="tall"><td><b>Notes</b><td><textarea rows="4" cols="32" name="Notes"><?php echo $_SESSION['POST']['Notes']?></textarea>
			</table>
</table>

<input name="Action" value="Clear" type="submit"><input name="Action" value="Add" type="submit">
</form>
</div>

<div id="AddAP" <?php if(!isset($_SESSION['POST']) or $_SESSION['POST']['EquipmentType'] == "SU") echo "style='display:none'"; ?>>
<form id="APForm" name="AddEquipment" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<input type="hidden" name="EquipmentType" value="AP">
<table class="table_multi_form">
	<tr>
		<td>
			<table class="table_form">
				<tr><td><b>Inventory Number</b><td><input name="InventoryNumber" type="text" size="16" value="<?php echo $_SESSION['POST']['InventoryNumber']; ?>">
				<tr><td><b>Serial Number</b><td><input name="SerialNumber" type="text" size="16" value="<?php echo $_SESSION['POST']['SerialNumber']; ?>">
				<tr><td><b>APID</b>
					<td><input name="APID" type="text" size="16" value="<?php echo $_SESSION['POST']['APID']; ?>">
				<tr><td><b>POP:</b>
					<td><select name="POP" onchange="showPOP()">
							<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT ID, name FROM POPs ORDER BY name");
						while($row = mysqli_fetch_array($queryResults))
							printf('<option value="%s"%s>%s</option>', $row['ID'], $_SESSION['POST']['POP'] == $row['ID'] ? ' selected="selected"' : '', $row['name']);
						?>
					</select>
				<tr><td><b>MAC</b><td><input type="text" name="MAC" size="16" value="<?php echo $_SESSION['POST']['MAC']?>">
				<tr><td><b>IP</b><td><input type="text" name="IP" size="16" value="<?php echo $_SESSION['POST']['IP']?>">
				<tr><td><b>Status</b>
					<td><select name="Status_ID">
						<option value=""></option>
						<?php
						$queryResults = mysqli_query($dblink,"SELECT * FROM Statuses ORDER BY Status ASC") or die(mysqli_error($dblink));
						while($row = mysqli_fetch_array($queryResults))
						{
							if($row['ID'] == $_SESSION['POST']['Status_ID'])
								echo '<option value="'.$row['ID'].'" selected="selected">'.$row['Status'];
							else
								echo '<option value="'.$row['ID'].'">'.$row['Status'];
						}
						?>
					</select>
			</table>
		<td>
			<table class="table_form">
				<tr><td><b>Name</b><td><input type="text" name="LocationName" value="<?php echo $_SESSION['POST']['LocationName']?>">
				<tr><td><b>Address</b><td><input type="text" name="LocationAddress" value="<?php echo $_SESSION['POST']['LocationAddress']?>">
				<tr><td><b>City</b><td><input type="text" name="LocationCity" value="<?php echo $_SESSION['POST']['LocationCity']?>">
				<tr><td><b>State</b>
					<td><select name="LocationState">
						<option value=""></option>
						<?php
						echo '<option value="NM">NM';
						echo '<option value="">';
						foreach($state_list as $state)
						{
							if($state == $_SESSION['POST']['LocationState'])
								echo '<option value="'.$state.'" selected="selected">'.$state;
							else
								echo '<option value="'.$state.'">'.$state;
						}
						?>
					</select>

				<tr><td><b>ZIP</b><td><input type="text" size="12" name="LocationZIP" value="<?php echo $_SESSION['POST']['LocationZIP']?>">
				<tr><td><b>Geocode</b><td><input id="geocode2" type="text" size="13" name="LocationGeocode" value="<?php echo $_SESSION['POST']['LocationGeocode']?>"><input type="button" value="Find" onclick="DoGeocode(this.form);">
				<tr><td><b>Phone 1</b><td><input type="text" name="LocationPhone1" value="<?php echo $_SESSION['POST']['LocationCBR1']?>">
				<tr><td><b>Phone 2</b><td><input type="text" name="LocationPhone2" value="<?php echo $_SESSION['POST']['LocationCBR2']?>">
				<tr><td><b>Phone 3</b><td><input type="text" name="LocationPhone3" value="<?php echo $_SESSION['POST']['LocationCBR3']?>">
			</table>
		<td>
			<table class="table_form">
				<tr><td><b>Install Date</b><td><input type="text" size="10" name="InstallDate" value="<?php echo $_SESSION['POST']['InstallDate']?>">
				<tr class="tall"><td><b>Notes</b><td><textarea rows="4" cols="32" name="Notes"><?php echo $_SESSION['POST']['Notes']?></textarea>
			</table>
</table>

<input name="Action" value="Clear" type="submit"><input name="Action" value="Add" type="submit">
</form>
</div>

<?php
unset($_SESSION['POST']);

include("include/footer.php");
?>
