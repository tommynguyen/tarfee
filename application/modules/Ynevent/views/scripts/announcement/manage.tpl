<?php echo $this->content()->renderWidget('ynevent.browse-menu') ?>
<h2>
    <?php echo $this->event->__toString() ?>
    <?php echo $this->translate('&#187; Announcement');?>
</h2>
<div class="generic_layout_container layout_middle">
	<div >
	    <ul class="ynevent_announcement_browse event_discussions_options">
	    	<li >
		        <a class="buttonlink icon_back" href="<?php echo $this->event->getHref().'/tab/'.$this -> tab?>"><?php echo $this -> translate("Back To Event");?></a>
		        <a style="color: #000;margin-left: 10px" class="buttonlink icon_event_manage_announcement" href="<?php echo $this -> url(array('controller' => 'announcement', 'action' => 'manage', 'event_id' => $this -> event -> getIdentity(), 'tab' => $this -> tab), 'event_extended', true)?>"><?php echo $this -> translate("Manage Announcements");?></a>
		        <a style="margin-left: 10px" class="buttonlink icon_event_create_blog" href="<?php echo $this -> url(array('controller' => 'announcement', 'action' => 'create', 'event_id' => $this -> event -> getIdentity(), 'tab' => $this -> tab), 'event_extended', true)?>"><?php echo $this -> translate("Create Announcement");?></a>
		    </li>
		</ul>
	</div>
	<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	    <ul class="ynevent_announcement_browse">
	      <?php foreach( $this->paginator as $item ): ?>
	        <li id="ynevent_announcement_item_<?php echo $item -> getIdentity()?>">
	          <div class='ynevent_announcement_browse_options'>
	          	<?php if( $item->isOwner($this->viewer) ):?>
	            <?php
	            echo $this->htmlLink(array(
	              'route' => 'event_extended',
				  'controller' => 'announcement',
	              'action' => 'edit',
	              'announcement_id' => $item->getIdentity(),
	              'event_id' => $this -> event ->getIdentity(),
	              'reset' => true,
	            ), $this->translate('Edit Entry'), array(
	              'class' => 'buttonlink icon_event_edit',
	            ));
	            ?>
	           
	            <?php
	            echo $this->htmlLink(array(
	                'route' => 'event_extended',
				  	'controller' => 'announcement',
	                'action' => 'delete',
	                'announcement_id' => $item->getIdentity(),
	                'event_id' => $this -> event ->getIdentity(),
	                'format' => 'smoothbox'
	                ), $this->translate('Delete Entry'), array(
	              'class' => 'buttonlink smoothbox icon_event_delete'
	            ));
	            ?>
	           
	            <?php
	            echo $this->htmlLink(array(
	                'route' => 'event_extended',
				  	'controller' => 'announcement',
	                'action' => 'highlight',
	                'announcement_id' => $item->getIdentity(),
	                'event_id' => $this -> event ->getIdentity(),
	                ), $item->highlight ? $this->translate('Un-highlight') : $this->translate('Highlight'), 
	                array(
	              'class' => $item->highlight?'smoothbox buttonlink ynevent_announcement_highlight icon_ynevent_announcement_unhighlight':'smoothbox buttonlink ynevent_announcement_highlight icon_ynevent_announcement_highlight'
	            ));
	            endif;?>
	          </div>
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
	    </ul>

  	<?php else: ?>
	    <div class="tip">
	      <span>
	        <?php echo $this->translate('You do not have any announcements.');?>
	      </span>
	    </div>
   	<?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array()); ?>
</div>
