<?php
						if(isset($error))
							echo '<span class="span_error">'.$error.'</span><br><br>';
						if(isset($warning))
							echo '<span class="span_warning">'.$warning.'</span><br><br>';
						if(isset($message))
							echo '<span class="span_message">'.$message.'</span><br><br>';

if(!isset($noMenu))
{
	echo '</table>';
}
?>

	</div>

<?php	
if(!isset($noFooter))
{
?>
	<div class="div_footer"><?php echo date("M d, Y H:i "); ?></div>
<?php
}
?>
	
</body>

</html>
