
<?php if($this -> isOwner) :?>
	<?php if($this -> canCreate) :?>
		<a href="<?php echo $this -> url(array('action' => 'create'), 'group_general' ,true);?>">
			<button><?php echo $this -> translate('Create organization');?>&nbsp;<i class="fa fa-plus fa-lg"></i></button>
		</a>
	<?php endif;?>
<?php endif;?>

<?php if(isset($this -> group) && $this -> group -> getIdentity()) :?>
	<a href="<?php echo $this -> group -> getHref();?>">
		<button><?php echo $this -> translate('Organization page');?>&nbsp;<i class="fa fa-eye fa-lg"></i></button>
	</a>
<?php endif;?>
