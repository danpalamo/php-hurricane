
<?php
include("include/common.php");
session_start();

$script = $_GET['script'];
$suId	= $_GET['id'];

$output = array();

connectToDB($servername, $dbname, $dbusername, $dbpassword);
$queryResults = mysql_query("SELECT sus.IP FROM SUs sus WHERE sus.ID='".$suId."'");
$row = mysql_fetch_array($queryResults);

if($row && array_key_exists('action',$_GET) && $_GET['action'] == 'save')
{
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . $row['IP'] . '.cfg');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	//header('Content-Length: ' . filesize('file.txt'));
	exec('/usr/local/share/configGenerators/' . $script . '.pl ' . $row['IP'] . ' 2>&1', $output);
	echo trim(implode("\n", $output));
}
else
{
	$noMenu = true;
	$noFooter = true;
	$noHeader = true;
	include("include/header.php");

	if(!$row)
	{
		echo 'Equipment not found.';
	}
	else
	{
		exec('/usr/local/share/configGenerators/' . $script . '.pl ' . $row['IP'] . ' 2>&1', $output);
		if(count($output) > 0)
		{
			echo '<form method="get" action="">';
			echo '<input type="hidden" name="action" value="save"/>';
			echo '<input type="hidden" name="script" value="' . $script . '"/>';
			echo '<input type="hidden" name="id" value="' . $suId . '"/>';
			echo '<br/><input type="submit" value="Save"/> <input onclick="window.close();" value="Close" type="button">';
			echo '<pre>' . trim(implode("\n", $output)) . '</pre>';
			echo '<br/><input type="submit" value="Save"/> <input onclick="window.close();" value="Close" type="button">';
			echo '</form>';
		}
		else
		{
			echo "Failed to generate config output";
		}
	}

	include("include/footer.php");
}
