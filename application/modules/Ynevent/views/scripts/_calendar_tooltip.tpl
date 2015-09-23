
<div class="tooltip">
     <?php
     $item = $this->event;
     ?>
     <ul class="ynevent-tooltip-container">
          <div class="jay">
               <li>
                    <div class="photo">
                         <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'thumb')) ?>
                    </div>
                    <div class="info">
                         <div class="title">
                              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                         </div>
                         <div class="stats">
                              <?php echo $this->timestamp(strtotime($item->starttime)) ?> -
                              <?php echo $this->translate(array('%s attending', '%s attendings', $item->getAttendingCount()), $this->locale()->toNumber($item->getAttendingCount())) ?>

                         </div>
                    </div>
                    <div class="ynevent_jay">

                    </div>

               </li>  
          </div>      

     </ul>

     <div class="ynevent_arrow">
     </div>

</div>
