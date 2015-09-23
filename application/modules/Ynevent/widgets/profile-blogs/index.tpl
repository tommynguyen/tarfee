<ul class="generic_list_widget">
	<li style="margin-bottom: 20px;text-align: left;">
		<?php if ($this->event ->isOwner($this->viewer())): ?>
			<a class="smoothbox buttonlink icon_event_import_blog" href="<?php echo $this -> url(array('action' => 'import-blogs', 'event_id' => $this -> event -> getIdentity(), 'tab' => $this->identity), 'ynevent_blog', true)?>"><?php echo $this -> translate("Import Blog Entry");?></a>
		<?php endif;?>
        <a class="buttonlink icon_event_create_blog" href="<?php echo $this -> url(array('action' => 'create', 'parent_type' => 'event', 'subject_id' => $this -> event -> getIdentity(), 'tab' => $this->identity,), 'blog_general', true)?>"><?php echo $this -> translate("Create New Entry");?></a>
    </li>
  <?php if($this->paginator -> getTotalItemCount()):?> 		 
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class="photo">
        <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.profile'), array('class' => 'thumb')) ?>
      </div>
       <div style="text-align: right;">
       		<?php if($item->isOwner($this->viewer())):?>
        		<a title="<?php echo $this->translate('Edit blog')?>" href="<?php echo $this -> url(array('action' => 'edit', 'blog_id' => $item->getIdentity(), 'parent_type' => 'event', 'subject_id' => $this -> event -> getIdentity(), 'tab' => $this->identity), 'blog_specific', true)?>"><img src="application/modules/Ynevent/externals/images/edit.png" /></a>
        		<a title="<?php echo $this->translate('Delete blog')?>"class="smoothbox" href="<?php echo $this -> url(array('action' => 'delete','event_id' => $this -> event -> getIdentity(), 'blog_id' => $item->getIdentity(), 'tab' => $this->identity,), 'ynevent_blog', true)?>"><img src="application/modules/Ynevent/externals/images/item/delete.png" /></a>
        	<?php endif;?>
        	<?php if ($this->event ->isOwner($this->viewer())): ?>
       			<a title="<?php echo $this->translate('Remove blog from event')?>" class="smoothbox" href="<?php echo $this -> url(array('action' => 'remove', 'event_id' => $this -> event -> getIdentity(), 'blog_id' => $item->getIdentity(), 'tab' => $this->identity,), 'ynevent_blog', true)?>"><img src="application/modules/Ynevent/externals/images/delete.png" /></a>
       		<?php endif;?>	
      </div>
      <div class="info">
        <div class="title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </div>
        <div class="stats">
          <?php echo $this->translate('by %1$s',
              $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?>, <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
        </div>
        <div class="description">
        	<?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
        </div>
      </div>
      
    </li>
  <?php endforeach; ?>
   <div>
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>
  <?php else:?>
   	<div class="tip">
        <span>
            <?php echo $this->translate("There are no blog yet.") ?>
        </span>
    </div>
  <?php endif;?>
</ul>
