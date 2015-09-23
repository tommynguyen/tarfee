<ul class="generic_list_widget" style="overflow: hidden;">
  <?php if(count($this->bloggers) > 0):?>
	  <?php foreach( $this->bloggers as $item ):?>
	      <?php
	      $owner = $item->getOwner();
	      if ($owner->getIdentity() <= 0) continue;
	      ?>
		  <li>
		    <div class="blogger_item">
		      <a class="blogger_photo" href="talks/<?php echo $item->owner_id;?>" >
		            <?php echo  $this->itemPhoto($item -> getOwner(), 'thumb.icon'); ?>
		      </a>
		      <br/>
		      <a class="blogger_name" title="<?php echo $item->getOwner()->getTitle();?>" href="talks/<?php echo $item->owner_id;?>">
		          <?php echo Engine_Api::_()->ynblog()->subPhrase($item->getOwner()->getTitle(),12); ?>
		      </a>
		    </div>
		  </li>
	  <?php endforeach; ?>
  <?php else: ?>
	<div class="tip">
	  <span>
	         <?php echo $this->translate('There is no blogger.');?>
	  </span>
	</div>  
  <?php endif;?>
</ul>



