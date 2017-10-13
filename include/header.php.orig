<html>

<head>
	<title><?php echo $pageTitle ?></title>
	<script type="text/javascript" src="js/prototype.js"></script>
	<script type="text/javascript" src="js/sorttable.js"></script>
<?php if($useGMaps) { ?>
	<script src="https://maps.google.com/maps?file=api&amp;v=2.x&amp;key=YOURKEYHERE" type="text/javascript"></script>
	<script src="js/egeoxml.js" type="text/javascript"></script>
<?php } ?>
	<script type="text/javascript" src="js/scripts.js"></script>
	<style type="text/css" media="all">@import "css/style.css";</style>
</head>

<body<?php if($onload) echo ' onload="'.$onload.'"'; if($onunload) echo ' onunload="'.$onunload.'"'; ?>>

<div id="tooltip" style='display:none;'>test</div>

<div class="div_main">
<?php	
if(!isset($noHeader))
{
?>
	<div class="div_header">
		<table class="table_wide">
			<tr>
				<td><img src="image/hurricane.png">
				<td class="right">
		</table>
	</div>
<?php
}
?>	
	<div class="div_content">
<?php
if(!isset($noMenu))
{
?>

		<table class="table_wide">
			<tr>
				<td class="td_menu" width="100">
					<a href="index.php">Search</a><br>				
					<a href="map.php">Map</a><br>
					<br>
					<a href="add.php">Add Equipment</a><br>
					<a href="manage.php">Manage Fields</a><br>
					<a href="pops.php">Manage POPs</a><br>
					<br>
					<hr>
					<br>
					<a href="//github.com/danpalamo/php-hurricane/">hurricane</a><br>
					<a href="//github.com/danpalamo/php-tsunami/">tsunami</a><br>
					<br>
					<hr>
					<br>
					<a href="//google.com">google.com</a><br>
				<td class="td_main">

<?php
}
?>
