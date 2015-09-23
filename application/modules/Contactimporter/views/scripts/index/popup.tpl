<div class="global_form_popup">
	<h3><?php 
	if($this->checkFromLogin)
		echo ucfirst ($this->provider)." ".$this->translate("Authorization");
	else
		echo ucfirst ($this->provider)." ".$this->translate("Import Your Contacts");?></h3>
	<?php
	if($this->checkFromLogin):
		$des = "";
		if($this->type == 'social')
		{
			$des = $this->translate("%s requires the Authorization to access and get contacts from your account.",$this->provider);
		}
		else
		{
			$des = $this->translate("Login to access and get %s contacts from your account.",$this->provider);
		}
		?>
		<p><?php echo $des;?></p>
		<div class="popup_buttons">
	  		<button onclick="parent.location.href = '<?php echo $this->url?>';parent.Smoothbox.close();"><?php echo $this->translate("Yes")?></button>
	    	<button onclick="parent.Smoothbox.close();"><?php echo $this->translate("No")?></button>
		</div>
	<?php else: ?>
		<?php echo $this->form->render($this);?>
	<?php endif;?>
</div>
