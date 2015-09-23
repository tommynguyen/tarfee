<?php if(isset($this -> errorMessage)) :?>
	<div class="global_form_popup tip">
		<span><?php echo $this -> errorMessage;?></span>
	</div>
<?php else :?>
	<?php echo $this -> form -> render() ;?>
<?php endif;?>

