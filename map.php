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

if(isset($_GET['show']))
{
	$_SESSION['mapShow'] = $_GET['show'];
}

$onload = "mapLoad('".base64_encode(serialize($_SESSION['View']->Search))."', '".$_SESSION['mapShow']."');";
$onunload = 'GUnload();';
$useGMaps = true;
$pageTitle = "hurricane | Map";
include("include/header.php");

if(!isset($_SESSION['View']))
	$_SESSION['View'] = new View();

if(isset($_SESSION['POST']['SearchType']))
	$_SESSION['View']->searchType = $_SESSION['POST']['SearchType'];

connectToDB($servername, $dbname, $dbusername, $dbpassword);
?>

<div id="SimpleSearch" <?php if($_SESSION['View']->searchType == "Advanced") echo "style='display:none;'"; ?>>
<form name="SimpleSearchForm" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<input type="hidden" name="SearchType" value="Simple">
<table class="table_form">
	<tr>
		<td><a onclick='ShowSearch("Advanced");'><img src="image/plus.png"></a>
		<td>Name<td><input name="SearchName" type="text" value="<?php echo $_SESSION['View']->Search['SearchName']; ?>">
		<td>Address<td><input name="SearchAddress" type="text" value="<?php echo $_SESSION['View']->Search['SearchAddress']; ?>">
		<td>MAC<td><input name="SearchMAC" type="text" size="16" value="<?php echo $_SESSION['View']->Search['SearchMAC']; ?>">
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
				<tr><td>Phone<td><input type="text" name="SearchPhone" value="<?php echo $_SESSION['View']->Search['SearchPhone']; ?>">
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

<div id="map" style="width: 100%; height: 600px"></div>

<form method="link" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
View: 
<select name="show" onchange='this.form.submit();'>
	<option value="">Location Only
	<option value="datarate" <?php if($_SESSION['mapShow'] == "datarate") echo 'selected="selected"'; ?>>Data Rate
	<option value="signal" <?php if($_SESSION['mapShow'] == "signal") echo 'selected="selected"'; ?>>SU Signal Strength
	<option value="lrantennas" <?php if($_SESSION['mapShow'] == "lrantennas") echo 'selected="selected"'; ?>>LR Antennas
</select>
<input type="button" onclick='window.location = kmlURL + "&download=true"' value="Download KML">
<?php if($_SESSION['mapShow'] == "datarate") {?>
 <span style="background:#C9C9C9;border:1px solid black;">&nbsp;&nbsp;&nbsp;<span style="color:green;line-height:16px;">Vector 1</span>&nbsp;&nbsp;&nbsp;<span style="color:yellow;line-height:16px;">Vector 2</span>&nbsp;&nbsp;&nbsp;<span style="color:orange;line-height:16px;">Vector 3</span>&nbsp;&nbsp;&nbsp;<span style="color:red;line-height:16px;">Vector 4</span>&nbsp;&nbsp;&nbsp;</span>
<?php } ?>
</form>
						<?php
include("include/footer.php");
?>
