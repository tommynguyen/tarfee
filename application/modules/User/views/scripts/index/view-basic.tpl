<div id="user-profile-sections-content">
    <ul id="sections-content-items">
	    <li class="sections-content-item" id="sections-content-item_basic">
	        <div class="profile-section">
	        	<?php $content = Engine_Api::_()->user()->renderSection('basic', $this->subject, array('view'=>true)); ?>
	        	<?php echo $content; ?>
	        </div>
        </li>
        <?php if (($this->viewer()->level_id == 6) || ($this->viewer()->level_id == 7) || $this->viewer()->isAdmin()) :?>
        <li class="sections-content-item" id="sections-content-item_contact">
	        <div class="profile-section">
	        	<?php $content = Engine_Api::_()->user()->renderSection('contact', $this->subject, array('view'=>true)); ?>
	        	<?php echo $content; ?>
	        </div>
        </li>
        <?php endif;?>
    </ul>
</div>
<a href="javascript:;" class="icon_event_viewall" onclick="parent.Smoothbox.close()"><?php echo $this->translate('Close')?></a>