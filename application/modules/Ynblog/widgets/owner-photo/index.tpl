<div class="quicklinks">
<!-- Owner Photo -->
<?php echo $this->htmlLink($this->owner->getHref(), 
    $this->itemPhoto($this->owner),
    array('class' => 'ynblogs_owner_photo')) ?>
</div>
<!-- Owner Name -->
<br/>
<div class="quicklinks">
<?php echo $this->htmlLink($this->owner->getHref(), 
    $this->owner->getTitle(), 
    array('class' => 'ynblogs_owner_name')) ?>
</div>
<br/>
   