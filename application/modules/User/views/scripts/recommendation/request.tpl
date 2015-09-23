<form method="post" class="global_form_popup">
<?php if (count($this->recommendations)) : ?>
<h3><?php echo $this->translate(array(
	'%s person waiting your recommendation',
	'%s people waiting your recommendation',
	count($this->recommendations)
),count($this->recommendations))?></h3>
<ul class="recommendation-list">
<?php foreach ($this->recommendations as $item):?>
	<li class="recommendation-item" id="recommendation-<?php echo $item->getIdentity()?>">
    	<div class="receiver-info">
    		<?php $receiver = Engine_Api::_()->user()->getUser($item->receiver_id);?>
    		<span class="photo"><?php echo $this->htmlLink($receiver->getHref(), $this->itemPhoto($receiver, 'thumb.icon'), array())?></span>
    		<span class="title"><?php echo $receiver?></span>
    	</div>
    	<div class="recommendation-options">
    		<div class="checkbox-wrapper">
    			<input type="checkbox" name="ignore_checkbox[]" id="ignore-checkbox-<?php echo $item->getIdentity()?>" value="<?php echo $item->getIdentity()?>"/>
    			<label for="ignore-checkbox-<?php echo $item->getIdentity()?>"><?php echo $this->translate('Ignore request')?></label>
    		</div>
    		<div class="button-wrapper">
    			<?php echo $this->htmlLink(array('route'=>'user_recommendation', 'action'=>'give', 'receiver_id'=>$item->receiver_id), $this->translate('Write recommendation', array()))?>
    		</div>
    	</div>
    </li>
<?php endforeach;?>
</ul>
<button type="submit"><?php echo $this->translate('Save change')?></button>
<button type="button" onclick="parent.Smoothbox.close()"><?php echo $this->translate('Cancel')?></button>
<?php else: ?>
<div class="tip">
	<span><?php echo $this->translate('No people waiting your recommendation.')?></span>
</div>
<?php endif; ?>
</form>