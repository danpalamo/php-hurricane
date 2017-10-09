<?php
$wwwPath = "/";

$servername='localhost';
$dbusername='hurricaneuser';
$dbpassword='hurricanepass';
$dbname='hurricane';
$dblink = mysqli_connect("$servername", "$dbusername", "$dbpassword", "$dbname") or die("Some error occurred during connection " . mysqli_error($dblink));

$state_list = array("AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DE", "DC", "FL", 
					"GA", "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", 
					"MD", "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", 
					"NJ", "NM", "NY", "NC", "ND", "OH", "OK", "OR", "PA", "RI", 
					"SC", "SD", "TN", "TX", "UT", "VT", "VA", "WA", "WV", "WI", 
					"WY");
					

                
class View
{
	var $searchType;
	var $Search = array();
	
	function View()
	{
		$this->sortDirection = "NONE";
	}
}

function getDirection($deg)
{
	$direction_list = array(
		'N' => 0,
		'NE' => 45,
		'E' => 90,
		'SE' => 135,
		'S' => 180,
		'SW' => 225,
		'W' => 270,
		'NW' => 315
	);

	$return = '';
	if($deg > 360-22.5)
		$deg = $deg - 360;
	foreach($direction_list as $k=>$v)
	{
		if( $v > $deg - 22.5 && $v < $deg + 22.5)
		{
			$return = $k;
			break;
		}
	}
	return $return;
}

function clean($rawString)
{
	$link = mysqli_connect("localhost", "hurricaneuser", "hurricanepass", "hurricane");
	$ret = stripslashes($rawString);
	$ret = mysqli_real_escape_string($link,$ret);
	return $ret;
}

function connectToDB($servername, $dbname, $dbuser, $dbpassword)
{
	$link = mysqli_connect("$servername", "$dbuser", "$dbpassword", "$dbname");
	if(!$link)
	{
		die("Could not connect to MySQL server at $servername");
	}
//	mysqli_select_db("$dbname", $link) or die("could not open database".mysqli_error());
	mysqli_select_db($link, "$dbname") or die("could not open database".mysqli_error());
}

function reformatMAC($MAC)
{
	if(!$MAC)
		return "";
	$exploded = sscanf($MAC, "%2s%2s%2s%2s%2s%2s");
	while(count($exploded) < 6)
		$exploded[] = " ";
	return vsprintf("%s-%s-%s-%s-%s-%s", $exploded);
}

function makeSortableIP($IP)
{
	if($IP == "")
		return "";

	$IParray = explode(".", $IP);
	foreach($IParray as &$octet)
	{
		$octet = sprintf("%03s", $octet);
	}
	$ret = implode("", $IParray);
	return $ret;
}

function reformatNotes($Notes)
{
//	return preg_replace("/(\bw?bugs?#?)\s*#?(\d+)/i", "<a href=\"http://bugzilla.somedomain/show_bug.cgi?id=$2\">Bug $2</a>", $Notes);
}

function logDebug($data)
{
	if( is_array( $data ) || is_object( $data ) )
		error_log( print_r( $data, true ) );
	else
		error_log( $data );
	
	return;
}
