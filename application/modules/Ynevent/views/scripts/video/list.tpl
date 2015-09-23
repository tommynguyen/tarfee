<!-- Header -->
<h2>
    <?php echo $this->event->__toString() . " ";
          echo $this->translate('&#187;') . " ";
          echo $this->translate('Videos');
    ?>
</h2>

<!-- Menu Bar -->
<div class="event_discussions_options">
  <?php echo $this->htmlLink(array('route' => 'event_profile', 'id' => $this->event->getIdentity()), $this->translate('Back to Event'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
  <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller'=>'video','action'=>'manage','subject' => $this->subject()->getGuid()), $this->translate('My Videos'), array(
    'class' => 'buttonlink icon_event_video'
  )) ?>
 <?php if( $this->canCreate ): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'video_general',
        'action' => 'create',
        'parent_type' =>'event',
        'subject_id' =>  $this->event->event_id,
      ), $this->translate('Create New Video'), array(
        'class' => 'buttonlink icon_event_video_new'
    )) ?>
  <?php endif; ?>
</div>

<!-- Search Bar -->
<div class="advevent_video_search_form">
  <?php echo $this->form->render($this);?>
</div>
<br/>

<!-- Content -->
 <?php if ($this->paginator->getTotalItemCount()> 0) : ?>
      <ul class="videos_browse" id="ynvideo_recent_videos">
              <?php foreach ($this->paginator as $item): ?>
                  <li style="margin-right: 18px;">
                      <?php
                      echo $this->partial('_video_listing.tpl', 'ynevent', array(
                          'video' => $item,
                          'infoCol' => $this->infoCol,
                      ));
                      ?>
                  </li>
              <?php endforeach; ?>
      </ul>
      <br/>
      <div class ="ynvideo_pages">
          <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
          )); ?>
      </div>
      
<?php else : ?>
      <div class="tip">
          <span>
              <?php echo $this->translate('There is no video found.'); ?>
          </span>
      </div>
<?php endif; ?>
