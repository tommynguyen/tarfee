<?php echo $this->content()->renderWidget('ynevent.browse-menu') ?>
<h2>
    <?php echo $this->event->__toString() ?>
    <?php echo $this->translate('&#187; Announcement');?>
</h2>
<div class="generic_layout_container layout_middle">
	<!-- Announcement menus -->
	<div>
	    <ul class="ynevent_announcement_browse event_discussions_options">
	    	<li >
		        <a class="buttonlink icon_back" href="<?php echo $this->event->getHref().'/tab/'.$this -> tab?>"><?php echo $this -> translate("Back To Event");?></a>
		        <a style="color: #000;margin-left: 10px" class="buttonlink icon_event_manage_announcement" href="<?php echo $this -> url(array('controller' => 'announcement', 'action' => 'manage', 'event_id' => $this -> event -> getIdentity(), 'tab' => $this -> tab), 'event_extended', true)?>"><?php echo $this -> translate("Manage Announcements");?></a>
		        <a style="margin-left: 10px" class="buttonlink icon_event_create_blog" href="<?php echo $this -> url(array('controller' => 'announcement', 'action' => 'create', 'event_id' => $this -> event -> getIdentity(), 'tab' => $this -> tab), 'event_extended', true)?>"><?php echo $this -> translate("Create Announcement");?></a>
		    </li>
		</ul>
	</div>
	<br />
	 <?php echo $this->form->render($this) ?>
</div>