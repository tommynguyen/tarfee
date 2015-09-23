<ul class="generic_list_widget">
     <?php foreach ($this->showedEvents as $item): ?>
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
                         - <?php echo $this->translate('led by %1$s', $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?>
                    </div>
               </div>
          </li>
     <?php endforeach; ?>
</ul>