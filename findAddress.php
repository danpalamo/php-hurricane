<?php 
include("include/common.php");
session_start();

$onload = "mapInitializeFind('".$_GET['address']."');";
$onunload = 'GUnload();';
$useGMaps = true;
$pageTitle = "hurricane | Find Address";
$noMenu = true;
$noFooter = true;
$noHeader = true;
include("include/header.php");

?>

<div id="map" style="width: 100%; height: 100%"></div>
<input type="button" onclick="mapDoneFind(findMarker.getPoint()); window.close();" value="Done">

<?php
include("include/footer.php");
?>
