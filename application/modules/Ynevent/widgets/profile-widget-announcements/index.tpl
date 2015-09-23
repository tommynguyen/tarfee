<ul class="ynevent_announcement_browse">
	<li style="text-align: left;">
        <a class="buttonlink icon_event_manage_announcement" href="<?php echo $this -> url(array('controller' => 'announcement', 'action' => 'manage', 'event_id' => $this -> event -> getIdentity(), 'tab' => $this->identity), 'event_extended', true)?>"><?php echo $this -> translate("Manage Announcements");?></a>
        <a class="buttonlink icon_event_create_blog" href="<?php echo $this -> url(array('controller' => 'announcement', 'action' => 'create', 'event_id' => $this -> event -> getIdentity(),'tab' => $this->identity), 'event_extended', true)?>"><?php echo $this -> translate("Create New Announcement");?></a>
    </li>
  <?php if(count($this->announcements)>0):?> 		 
  <?php foreach( $this->announcements as $item ): ?>
    <li id="ynevent_announcement_item_<?php echo $item -> getIdentity()?>">
      <div class='ynevent_announcement_browse_info'>
        <div class='ynevent_announcement_browse_info_title'>
          <b><?php echo $item->getTitle() ?></b>
        </div>
        <p>
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle());?>
        </p>
        <p class='ynevent_announcement_browse_info_blurb'>
          <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
        </p>
      </div>
    </li>
  <?php endforeach; ?>
  <?php else:?>
   	<div class="tip">
        <span>
            <?php echo $this->translate("There are no announcements yet.") ?>
        </span>
    </div>
  <?php endif;?>
</ul>
