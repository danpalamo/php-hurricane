<?php

//ALTER TABLE `Statuses` ADD `RowColor` VARCHAR( 7 ) NOT NULL ;

include("include/common.php");
session_start();

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$_SESSION['POST'] = $_POST;
	session_write_close();
	header('Location: //'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
	exit();
}

$fields = array("Long-Range Antennas", "Equipment Statuses", "Installers", "Contracts", "Data Rates");
$tables = array("LRAntennas", "Statuses", "Installers", "Contracts", "DataRates");
$orders = array("LRAntenna", "Status", "Installer", "Contract", "DataRate");

connectToDB($servername, $dbname, $dbusername, $dbpassword);

if(isset($_SESSION['POST']))
{
	if($_SESSION['POST']['Action'] == "Add")
	{
		$message = "Row added to table \"".$_SESSION['POST']['TableName']."\"";

		$query = "INSERT INTO ".$_SESSION['POST']['TableName']." VALUES ('', ";
		unset($_SESSION['POST']['Action']);
		unset($_SESSION['POST']['TableName']);

		$i = 0;
		foreach($_SESSION['POST'] as $key => $value)
		{
			if($i != 0)
				$query .= ", ";
			$query .= "'".clean($value)."'";
			$i++;
		}
		$query .= ")";
		mysqli_query($dblink,$query) or die(mysqli_error($dblink));
	}
	else if($_SESSION['POST']['Action'] == "Delete")
	{
		$message = "Row deleted from table \"".$_SESSION['POST']['TableName']."\"";
		mysqli_query($dblink,"DELETE FROM ".$_SESSION['POST']['TableName']." WHERE ID = '".clean($_SESSION['POST']['ID'])."'") or die(mysqli_error($dblink));
		
		//TODO do something about SU/AP records that might have had this value set previously
	}
	else if($_SESSION['POST']['Action'] == "Update")
	{
		$message = "Row updated in table \"".$_SESSION['POST']['TableName']."\"";

		$query = "UPDATE ".$_SESSION['POST']['TableName']." SET ";
		unset($_SESSION['POST']['Action']);
		unset($_SESSION['POST']['TableName']);
		$id = $_SESSION['POST']['ID'];
		unset($_SESSION['POST']['ID']);
		
		$i = 0;
		foreach($_SESSION['POST'] as $key => $value)
		{
			if($i != 0)
				$query .= ", ";
			$query .= $key." = '".clean($value)."'";
			$i++;
		}

		$query .= " WHERE ID = '".clean($id)."'";
		
		mysqli_query($dblink,$query) or die(mysqli_error($dblink));
	}
}

$pageTitle = "hurricane | Manage Fields";
include("include/header.php");

for($i = 0; $i < count($fields); $i++)
{
	if($i > 0)
		echo '<hr>';
?>
<div>
<h2><?php echo $fields[$i]; ?></h2>
<table class="table_form">
<?php
		$queryResults = mysqli_query($dblink,"SELECT * FROM ".$tables[$i]." ORDER BY ".$orders[$i]." ASC") or die(mysqli_error($dblink));
//		mysqli_free_result($queryResults);
		$row = mysqli_fetch_array($queryResults, MYSQLI_ASSOC);
//		$row = $queryResults->fetch_array(MYSQLI_ASSOC);
		echo '<tr>';
		foreach($row as $key => $value)
		{
			if($key != "ID")
				echo '<th>'.$key;
		}
		echo '<th>';
		
		do
		{
			echo '<tr>';
			echo '<form name="EditField'.$row['ID'].'" method="post" action="//'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'">';
			echo '<input type="hidden" name="TableName" value="'.$tables[$i].'">';
			echo '<input type="hidden" name="ID" value="'.$row['ID'].'">';
			foreach($row as $key => $value)
			{
				if($key != "ID")
					echo '<td><input type="text" name="'.$key.'" value="'.$row[$key].'">';
			}
			echo '<td><input name="Action" value="Update" type="submit"><input name="Action" value="Delete" type="submit">';
			echo '</tr></form>'."\n";
//		} while($row = mysqli_fetch_array($queryResults, MYSQL_ASSOC));
		} while($row = $queryResults->fetch_array(MYSQLI_ASSOC));
		
		echo '<form name="AddField'.$i.'" method="post" action="//'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'">';
		echo '<input type="hidden" name="TableName" value="'.$tables[$i].'">';

		$queryResults = mysqli_query($dblink,"SELECT * FROM ".$tables[$i]) or die(mysqli_error($dblink));
//		$row = mysqli_fetch_array($queryResults, MYSQL_ASSOC);
		$row = $queryResults->fetch_array(MYSQLI_ASSOC);
		
		echo '<tr>';
		foreach($row as $key => $value)
		{
			if($key != "ID")
				echo '<td><input type="text" name="'.$key.'">';
		}
		echo '<td><input name="Action" value="Add" type="submit"></form></div>'."\n";

		echo '</table><br>';
}

unset($_SESSION['POST']);

include("include/footer.php");
?>
