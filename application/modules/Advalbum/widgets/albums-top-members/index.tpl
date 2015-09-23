<div class="advalbum_members">
   <?php 
      foreach ($this->members as $item):  ?>
         <div class='member_thumb'>
						<?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon', $item->getOwner()->getTitle()), array('title'=>$item->getOwner()->getTitle())) ?>
 				 </div>      
 	<?php endforeach; ?>              
</div>
