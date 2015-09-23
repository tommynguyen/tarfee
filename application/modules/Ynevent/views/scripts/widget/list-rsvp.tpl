<?php 
$row = $this->event->membership()->getRow($this -> viewer());
$functionName = 'changeRsvp';
$optionName = "";
if($this -> widget)
{
	$functionName = $this -> widget;
	$optionName = '_'.$functionName;
}
if($row):
	$rsvp = $row -> rsvp;
	?>
	<a id="rsvp_option<?php echo $optionName?>_<?php echo $this->event -> getIdentity();?>_2" <?php if($rsvp == 2):?> class="active" <?php else:?> onclick="<?php echo $functionName?>('<?php echo $this->event -> getIdentity();?>', 2);" <?php endif;?> href="javascript:;"><?php echo $this->translate('attending'); ?></a>
	<a id="rsvp_option<?php echo $optionName?>_<?php echo $this->event -> getIdentity();?>_0" <?php if($rsvp == 0):?> class="active" <?php else:?> onclick="<?php echo $functionName?>('<?php echo $this->event -> getIdentity();?>', 0);" <?php endif;?> href="javascript:;"><?php echo $this->translate('not attending'); ?></a>
	<a id="rsvp_option<?php echo $optionName?>_<?php echo $this->event -> getIdentity();?>_1" <?php if($rsvp == 1):?> class="active" <?php else:?> onclick="<?php echo $functionName?>('<?php echo $this->event -> getIdentity();?>', 1);" <?php endif;?> href="javascript:;"><?php echo $this->translate('maybe'); ?></a>
<?php else:
	$param = array();
	$label = "";
	if ($this->event -> membership() -> isResourceApprovalRequired())
	{
		$label = 'request invite';
		$param = array(
			'controller' => 'member',
			'action' => 'request',
			'event_id' => $this->event -> getIdentity(),
		);
	}  
	else
	{
		$label = 'join';
		$param = array(
			'controller' => 'member',
			'action' => 'join',
			'event_id' => $this ->event -> getIdentity(),
		);
	}
	if($param):
		?>
		<a href="<?php echo $this->url($param, 'event_extended', true);?>" class="smoothbox" title="<?php echo $this -> translate($label); ?>">
			<?php echo  $this -> translate($label);?>
		</a>
	<?php endif;?>
<?php endif;?>
