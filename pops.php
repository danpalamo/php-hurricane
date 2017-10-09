<?php
include("include/common.php");
session_start();

$onload="loadEvtHandlers();";
connectToDB($servername, $dbname, $dbusername, $dbpassword);
$buttonText = "Add";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	if(array_key_exists('POP', $_POST))
	{
		$queryResults = mysqli_query($dblink,"SELECT * FROM POPs WHERE ID='" . clean( $_POST['POP'] ) . "'");
		$pop = mysqli_fetch_array($queryResults);
	}
	else
	{
		$_SESSION['POST'] = $_POST;
		session_write_close();
		header('Location: //'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
		exit();
	}
}

if(isset($_SESSION['POST']))
{
	if($_SESSION['POST']['Action'] == "Clear")
	{
		unset($_SESSION['POST']);
	}
	else if($_SESSION['POST']['Action'] == "Add")
	{
		$form = $_SESSION['POST'];

		if(!$error)
		{
			$message = "POP added.";

			mysqli_query($dblink,"INSERT INTO POPs (name, description, type, access, location_owner, management_company, management_contact, latitude, longitude, elevation) VALUES (
				'".clean($form['name'])."', 
				'".clean($form['description'])."', 
				'".clean($form['type'])."', 
				'".clean($form['access'])."', 
				'".clean($form['location_owner'])."',
				'".clean($form['management_company'])."', 
				'".clean($form['management_contact'])."', 
				'".clean($form['latitude'])."', 
				'".clean($form['longitude'])."', 
				'".clean($form['elevation'])."'
				)") or die(mysqli_error($dblink));
			$_SESSION['POST']['ID'] = mysqli_insert_id($dblink);
		}
	}
	else if($_SESSION['POST']['Action'] == "Update")
	{
		$form = $_SESSION['POST'];

		if(!$error)
		{
			$message = "POP updated.";

			mysqli_query($dblink,"UPDATE POPs SET 
				name='".clean($form['name'])."', 
				description='".clean($form['description'])."', 
				type='".clean($form['type'])."', 
				access='".clean($form['access'])."', 
				location_owner='".clean($form['location_owner'])."', 
				management_company='".clean($form['management_company'])."', 
				management_contact='".clean($form['management_contact'])."', 
				latitude='".clean($form['latitude'])."', 
				longitude='".clean($form['longitude'])."', 
				elevation='".clean($form['elevation'])."'
				WHERE ID='".clean($form['ID'])."'") or die(mysqli_error($dblink));
			
		}
	}
	$buttonText = "Update";
	$_POST['POP'] = $_SESSION['POST']['ID'];
}
else if(array_key_exists('POP', $_POST) && $_POST['POP'] != '999')
{
	$_SESSION['POST'] = $pop;
	$buttonText = "Update";
}

$useGMaps = true;
$pageTitle = "hurricane | Add Equipment";
include("include/header.php");

?>
<form id="SelectPOP" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<b>POP:</b>
<select name="POP" onchange="showPOP()">
	<option value="999">Add POP</option>
	<option value="">-------</option>
<?php
$queryResults = mysqli_query($dblink,"SELECT ID, name FROM POPs ORDER BY name");
while($row = mysqli_fetch_array($queryResults))
	printf('<option value="%s"%s>%s</option>', $row['ID'], $_POST['POP'] == $row['ID'] ? ' selected="selected"' : '', $row['name']);
?>
</select>
</form>

<br/>
<br/>


<div id="AddPOP">
<form id="APForm" name="AddPOP" method="post" action="//<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<input type="hidden" name="ID" value="<?php echo $_SESSION['POST']['ID']; ?>" />
<table class="table_multi_form">
	<tr>
		<td>
			<table class="table_form">
				<tr><td><b>POP Type:</b></td><td>
					<select name="type" onchange="updateType(this.value)">
						<option value='Tower' <?php if($_SESSION['POST']['type'] == "Tower") echo 'selected="selected"'; ?>>Tower</option>
						<option value='Building' <?php if($_SESSION['POST']['type'] == "Building") echo 'selected="selected"'; ?>>Building</option>
					</select>
				</td></tr>
				<tr><td><b>Identifier</b></td><td><input name="name" type="text" size="16" value="<?php echo $_SESSION['POST']['name']; ?>"></td></tr>
				<tr><td><b>Notes</b></td><td><textarea name="description" rows="4" cols="70"><?php echo $_SESSION['POST']['description']; ?></textarea></td></tr>
				<tr><td><b>Access Info</b></td><td><textarea name="access" rows="4" cols="70"><?php echo $_SESSION['POST']['access']; ?></textarea></td></tr>
				<tr><td><b>Latitude</b></td><td><input name="latitude" type="text" size="16" value="<?php echo $_SESSION['POST']['latitude']; ?>"></td></tr>
				<tr><td><b>Longitude</b></td><td><input name="longitude" type="text" size="16" value="<?php echo $_SESSION['POST']['longitude']; ?>"></td></tr>
				<tr><td><b>Elevation</b></td><td><input name="elevation" type="text" size="16" value="<?php echo $_SESSION['POST']['elevation']; ?>"> (at base)</td></tr>
			</table>
		</td>
		<td>
			<table class="table_form">
				<tr><td><b id="owner"><?php echo isset($_SESSION['POST']) ? $_SESSION['POST']['type'] : 'Tower'; ?> Owner</b></td><td><input type="text" name="location_owner" value="<?php echo $_SESSION['POST']['location_owner']?>"></td></tr>
				<tr><td><b>Management Company</b></td><td><input type="text" name="management_company" value="<?php echo $_SESSION['POST']['management_company']?>"></td></tr>
				<tr><td><b>Management Contact</b></td><td><textarea name="management_contact" rows="4" cols="70"><?php echo $_SESSION['POST']['management_contact']; ?></textarea></td></tr>
			</table>
		</td>
</table>

<input name="Action" value="Clear" type="submit"><input name="Action" id="add" value="<?php echo $buttonText; ?>" type="submit">
</form>
</div>

<?php
unset($_SESSION['POST']);

include("include/footer.php");
?>
