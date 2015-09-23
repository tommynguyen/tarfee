
<ul class="generic_list_widget">
     <?php foreach ($this->paginator as $item): ?>
          <li>
               <div class="photo">
                    <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'thumb')) ?>
               </div>
               <div class="info">
                    <div class="title">
                         <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                    </div>
                    <div class="stats">
                         <?php echo $this->timestamp(strtotime($item->starttime)) ?>
                         - <?php echo $this->translate('led by %1$s',
              $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?>
                    </div>
                    <div>
                         <?php if ($item->rating > 0): ?>
                              <?php for ($x = 1; $x <= $item->rating; $x++): ?>
                                   <span class="rating_star_generic rating_star"></span>
                              <?php endfor; ?>
                              <?php if ((round($item->rating) - $item->rating) > 0): ?>
                                   <span class="rating_star_generic rating_star_half"></span>
                              <?php endif; ?>
                         <?php endif; ?>
                    </div>
                    - <?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?>
		            - <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
		            - <?php echo $this->translate(array('%s like', '%s likes', $item->likes()->getLikeCount()), $this->locale()->toNumber($item->likes()->getLikeCount())) ?>
                    - <?php echo $this->translate(array('%s rate', '%s rates', $item->rating_count), $this->locale()->toNumber($item->rating_count)) ?>
               </div>
          </li>
	<?php endforeach; ?>
</ul>