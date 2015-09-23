<div>
	<?php echo $this -> content;?>
	<button onclick = 'window.open("<?php echo $this -> link_invite?>"); return false;'><?php echo $this -> translate("visit and register")?></button>
</div>
<div>
	<p><?php echo $this -> message?></p>
</div>