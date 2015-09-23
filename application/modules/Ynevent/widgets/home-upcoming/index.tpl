<?php ?>

<ul id="ynevents-upcoming" class="generic_list_widget">    
     <?php
     foreach ($this->paginator as $event):
          // Convert the dates for the viewer
          $startDateObject = new Zend_Date(strtotime($event->starttime));
          $endDateObject = new Zend_Date(strtotime($event->endtime));
          if ($this->viewer() && $this->viewer()->getIdentity()) {
               $tz = $this->viewer()->timezone;
               $startDateObject->setTimezone($tz);
               $endDateObject->setTimezone($tz);
          }
          $isOngoing = ( $startDateObject->toValue() < time() );
          ?>
          <li<?php if ($isOngoing): ?> class="ongoing"<?php endif ?>>
               <div class="photo">
                    <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.icon'), array('class' => 'thumb')) ?>
               </div>
                <div class="info">
        <div class="title">
          <?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
        </div>
              
               <div class="ynevents-upcoming-date">
                    <?php echo $this->timestamp($event->starttime, array('class' => 'eventtime')) ?>
               </div>
               <?php if ($isOngoing): ?>
                    <div class="ynevents-upcoming-ongoing">
                         <?php echo $this->translate('Ongoing') ?>
                    </div>
               <?php endif; ?>
          </li>
     <?php endforeach; ?>
     <li style="text-align: right">
          <a href="<?php echo $this->url(array(), 'event_general', true); ?>">
               <?php echo $this->translate('View More...') ?>
          </a>

     </li>
</ul>

