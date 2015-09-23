<form method="post" class="global_form_popup">
<?php if (count($this->recommendations)) : ?>
<h3><?php echo $this->translate(array(
	'%s recommendation waiting for approve',
	'%s recommendations waiting for approve',
	count($this->recommendations)
),count($this->recommendations))?></h3>
<ul class="recommendation-list">
<?php foreach ($this->recommendations as $item):?>
	<li class="recommendation-item" id="recommendation-<?php echo $item->getIdentity()?>">
    	<div class="giver-info">
    		<?php $giver = Engine_Api::_()->user()->getUser($item->giver_id);?>
    		<span class="photo"><?php echo $this->htmlLink($giver->getHref(), $this->itemPhoto($giver, 'thumb.icon'), array())?></span>
    		<span class="title"><?php echo $giver?></span>
    	</div>
    	<div class="recommendation-content">
    		<div class="content">
    			<?php echo $this->viewMore($item->content, 255);?>
    		</div>
    		<div class="time">
    			<?php echo date('M, d, Y', $item->getGivenDate()->getTimestamp());?>
    		</div>
    	</div>
    	<div class="recommendation-options">
    		<div class="checkbox-wrapper">
    			<input type="checkbox" name="approve_checkbox[]" id="approve-checkbox-<?php echo $item->getIdentity()?>" value="<?php echo $item->getIdentity()?>"/>
    			<label for="approve-checkbox-<?php echo $item->getIdentity()?>"><?php echo $this->translate('Approved')?></label>
    		</div>
    		<div class="checkbox-wrapper">
    			<input type="checkbox" name="delete_checkbox[]" id="delete-checkbox-<?php echo $item->getIdentity()?>" value="<?php echo $item->getIdentity()?>"/>
    			<label for="delete-checkbox-<?php echo $item->getIdentity()?>"><?php echo $this->translate('Deleted')?></label>
    		</div>
    	</div>
    </li>
<?php endforeach;?>
</ul>
<button type="submit"><?php echo $this->translate('Save change')?></button>
<button type="button" onclick="parent.Smoothbox.close()"><?php echo $this->translate('Cancel')?></button>
<?php else: ?>
<div class="tip">
	<span><?php echo $this->translate('No recommendations waiting for approve.')?></span>
</div>
<?php endif; ?>
</form>