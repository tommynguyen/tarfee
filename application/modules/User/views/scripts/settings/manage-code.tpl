<style>
	table, th, td {
	    border: 1px solid black;
	    border-collapse: collapse;
	}
	th, td {
	    padding: 15px;
	}
</style>

<form method="post" action="" class="global_form_popup">
<?php if(count($this -> available_codes)) :?>
	<table border="1" style="width:100%">
		<tr>
			<th><?php echo $this -> translate("Code");?></th>
			<th><?php echo $this -> translate("Active/Deactive");?></th>
		</tr>
		<?php foreach($this -> available_codes as $code) :?>
			<tr>
				<td><?php echo $code -> code;?></td>
				<td><input name="<?php echo $code -> code;?>" type="checkbox" <?php echo ($code -> active)? "checked" : "" ?> /></td>
			</tr>
		<?php endforeach;?>
	</table>
	<br/>
<button type="submit"><?php echo $this -> translate("Save");?></button>
<?php else :?>
	<div class="tip">
		<span><?php echo $this -> translate("There are no available codes.");?></span>
	</div>
<?php endif;?>
</form>

