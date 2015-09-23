<ul class="ynfeedback_manage_menu">
	<li class="ynfeedback_my <?php if($this -> action == 'manage') echo "active"?>">
		<a href="<?php echo $this -> url(array('action' => 'manage'), 'ynfeedback_general', true)?>">
			<i class="fa fa-files-o"></i>
			<?php echo $this -> translate("My Posted Feedback")?>
		</a>
	</li>
	<li class="ynfeedback_myclaim <?php if($this -> action == 'manage-follow') echo "active"?>">
		<a href="<?php echo $this -> url(array('action' => 'manage-follow'), 'ynfeedback_general', true)?>">
			<i class="fa fa-share-square-o"></i>
			<?php echo $this -> translate("My Following Feedback")?>
		</a>
	</li>
</ul>