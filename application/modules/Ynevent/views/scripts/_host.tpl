<?php
$user_id = $this -> element -> getValue();
$title = '';
$href = '';
$value = $user_id;
$host = 'block';
$toValue = 'block';

if(isset($user_id) && $user_id!='')
{
	if(strpos($user_id,'younetco_event_key_') !== FALSE)
	{
		$user_id = substr($user_id, 19, strlen($user_id));
		
		$user = Engine_Api::_() -> getItem('user', $user_id);
		$title = $user->getTitle();
		$href = $user->getHref();
		$host = 'none';
		$toValue = 'block';
	}
	else{
		$host = 'block';
		$toValue = 'none';
	}
}
else{
	$owner = Engine_Api::_() -> user() -> getViewer();
	$title = $owner->getTitle();
	$href = $owner->getHref();
	$value = 'younetco_event_key_'.$owner->getIdentity();
	$host = 'none';
	$toValue = 'block';
}
?>

<div id="host-wrapper" class="form-wrapper" style="display: <?php echo $host?>"><div id="host-label" class="form-label"><label for="host" class="optional"><?php echo $this -> translate("Host") ?></label></div>
	<div id="host-element" class="form-element">
		<input type="text" name="host" id="host" value="<?php echo $value; ?>" autocomplete="off">
	</div>
</div>

<div id="toValues-wrapper" class="form-wrapper" style="height: auto; display: <?php echo $toValue?>">
	<div id="toValues-label" class="form-label">
		<label for="toValues" class="optional"><?php echo $this -> translate("Host") ?></label>
	</div>
	<div id="toValues-element" class="form-element">
		<input type="hidden" name="toValues" value="<?php echo $href ?>" style="margin-top:-5px;" id="toValues">
		<span id="" class="tag"><?php echo $title ?><a href="javascript:void(0);" onclick="this.parentNode.destroy();removeFromToValue('<?php echo $href ?>', toValues);">x</a></span>
	</div>
</div>