<?php
include("include/common.php");
session_start();

$onload="loadEvtHandlers();";
connectToDB($servername, $dbname, $dbusername, $dbpassword);
$buttonText = "Add";
$error = array();

$functions = array(
	'c58' => 'addCambium58SU',
	'c365' => 'addCambium365SU',
	'e58' => 'addEpmp58SU'
);

// Process the bulk post
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$_SESSION['POST'] = $_POST;
	
	// Error checking
	if(!array_key_exists('type', $_POST) || !$_POST['type'])
		$error['type'] = 'Choose an SU type';
		
	if(!array_key_exists('data', $_POST) || !$_POST['data'])
		$error['data'] = 'Enter import data';
		
	if(!array_key_exists('format', $_POST) || !$_POST['format'])
		$error['format'] = 'Choose data format';	
	if(count($error) == 0)
	{
		$data = explode("\n", trim($_POST['data']));
		if(count($data)%2 != 0)
		{
			$error['format'] = 'Data mismatch, possibly missing a line of data';
		}
		else
		{
			$first = array();
			$second = array();
			
			if($_POST['format'] == 'mas')
			{
				for($i = 0; $i < count($data); $i++)
				{
					$first[] = $data[$i];
					$second[] = $data[$i];
				}
				$mac = $first;
				$serial = $second;
			}
			else
			{
				if($_POST['format'] == 'sbm')
				{
					$even = 'serial';
					$odd = 'mac';
				}
				elseif($_POST['format'] == 'mbs')
				{
					$even = 'mac';
					$odd = 'serial';
				}
					
				for($i = 0; $i < count($data); $i+=2)
				{
					$first[] = $data[$i];
					$second[] = $data[$i+1];
				}
				
				$$even = $first;
				$$odd = $second;
			}
			
			foreach($mac as $key=>$singlemac)
			{
				$cleanmac = str_replace(':', '', trim($singlemac));
				$cleanmac = str_replace('-', '', $cleanmac);
				$cleanmac = strtolower($cleanmac);
				if(preg_match('/([a-f0-9]{12})/', $cleanmac) != 1)
				{
					$error['data'] = 'Found incorrect MAC address: ' . $singlemac;
					break;
				}
				$mac[$key] = $cleanmac;
			}
			
			$checkdup1 = count($mac);
			$newmac = array_unique($mac);
			$checkdup2 = count($newmac);
			if($checkdup2 < $checkdup1)
			{
				$isdup = array();
				$countvals = array_count_values($mac);
				print_r($countvals);
				foreach($newmac as $hasmac)
					if($countvals[$hasmac] > 1)
						$isdup[] = $hasmac;
				$error['data'] = 'Duplicate MAC address exists: ' . implode(', ', $isdup);
			}
			
			if(count($error) == 0)
			{
				$added = 0;
				for($i = 0; $i < count($mac); $i++)
				{
					$sql = 'call ' . $functions[$_POST['type']] . '("' . trim($mac[$i]) . '","' . trim($serial[$i]) . '", @return);';
					mysql_query($sql) or die(mysql_error());
					$added++;
				}
				unset($_SESSION['POST']);
			}
		}
	}
}

$pageTitle = "hurricane | Bulk Add Equipment";
include("include/header.php");
	
?>


<div id="AddEquipment">
<form id="BAForm" name="AddEquipment" method="post" action="https://<?php echo $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>">
<table class="table_multi_form">
	<tr>
		<td>
			<table class="table_form">
				<?php if($added > 0){ ?>
					<tr><td colspan="2" style="color:green;"><?php echo $added; ?> records added</td></tr>
				<?php }?>
				<tr><td><b>Equipment Type:</b></td><td>
					<select name="type" onchange="updateType(this.value)">
						<option value=''> -- Select SU Type-- </option>
						<option value='c58' <?php if($_SESSION['POST']['type'] == "c58") echo 'selected="selected"'; ?>>Cambium 5.8GHz SU</option>
						<option value='c365' <?php if($_SESSION['POST']['type'] == "c365") echo 'selected="selected"'; ?>>Cambium 3.65GHz SU</option>
						<option value='e58' <?php if($_SESSION['POST']['type'] == "e58") echo 'selected="selected"'; ?>>ePMP 5.8GHz SU</option>
					</select>
					<?php
						if(array_key_exists('type', $error))
							echo '<div style="color:red">' . $error['type'] . '</div>';
					?>
				</td></tr>
				<tr><td><b>Import from scanner:</b></td><td><textarea name="data" rows="15" cols="70"><?php echo $_SESSION['POST']['data']; ?></textarea>
					<?php
						if(array_key_exists('data', $error))
							echo '<div style="color:red">' . $error['data'] . '</div>';
					?></td></tr>
				<tr><td><b>Format</b></td><td><input name="format" type="radio" value="sbm"<?php echo $_SESSION['POST']['format'] == 'sbm' ? ' checked="checked"' : ''; ?>> Serial before MAC &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="format" type="radio" value="mbs"<?php echo $_SESSION['POST']['format'] == 'mbs' ? ' checked="checked"' : ''; ?>> MAC before Serial &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="format" type="radio" value="mas"<?php echo $_SESSION['POST']['format'] == 'mas' ? ' checked="checked"' : ''; ?>> MAC as Serial
					<?php
						if(array_key_exists('format', $error))
							echo '<div style="color:red">' . $error['format'] . '</div>';
					?></td></tr>
			</table>
		</td>
</table>

<input name="Action" value="Clear" type="submit"><input name="Action" id="add" value="<?php echo $buttonText; ?>" type="submit">
</form>
</div>

<?php
unset($_SESSION['POST']);
unset($error);

include("include/footer.php");
?>
