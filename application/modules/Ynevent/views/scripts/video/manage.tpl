<!-- Header -->
<h2>
    <?php echo $this->event->__toString() . " ";
          echo $this->translate('&#187;') . " ";
          echo $this->translate('Videos') ;
    ?>
</h2>

<!-- Menu Bar -->
<div class="event_discussions_options">
  <?php echo $this->htmlLink(array('route' => 'event_profile', 'id' => $this->event->getIdentity()), $this->translate('Back to Event'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
  <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller'=>'video','action'=>'list','subject' => $this->subject()->getGuid()), $this->translate('Browse Videos'), array(
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
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="ynvideo_videos_manage videos_manage">
        <h3>
            <?php
            $totalVideo = $this->paginator->getTotalItemCount();
            echo $this->translate(array('%1$s video', '%1$s video', $totalVideo), $this->locale()->toNumber($totalVideo));
            ?>
        </h3>
        <?php foreach ($this->paginator as $item): ?>
            <li>
                <div class="ynvideo_thumb_wrapper video_thumb_wrapper">
                    <?php if ($item->duration): ?>
                        <?php echo $this->partial('_video_duration.tpl','ynevent', array('video' => $item)) ?>
                    <?php endif; ?>
                    <?php
                    if ($item->photo_id) {
                        echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
                    } else {
                        echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Ynvideo/externals/images/video.png">';
                    }
                    ?>
                    <?php if(Engine_Api::_()-> ynevent() -> checkYouNetPlugin('ynvideo')) :?>
                    <span class="video_button_add_to_area">
                        <button class="ynvideo_uix_button ynvideo_add_button" video-id="<?php echo $item->getIdentity() ?>">
                            <div class="ynvideo_plus" />
                        </button>
                    </span>
                    <?php endif;?>
                </div>
                <div class='video_options'>
                    <?php
                    $ynvideo_enable = Engine_Api::_() -> ynevent() ->checkYouNetPlugin('ynvideo');
                    if($ynvideo_enable)
                    {
	                   	 echo $this->htmlLink(array(
	                        'route' => 'video_general',
	                        'action' => 'edit',
	                        'video_id' => $item->video_id,
	                        'subject_id' => $this->event->event_id,
	                        'parent_type' => 'event',
	                        ), $this->translate('Edit Video'), array('class' => 'buttonlink icon_video_edit'));
					}
				    else
					{
						 echo $this->htmlLink(array(
	                        'route' => 'default',
				            'module' => 'video',
				            'controller' => 'index',
				            'action' => 'edit',
	                        'video_id' => $item->video_id,
	                        'subject_id' => $this->event->event_id,
	                        'parent_type' => 'event',
	                        ), $this->translate('Edit Video'), array('class' => 'buttonlink icon_video_edit'));
					}
				    ?>
                    
                    <?php
                    if ($item->status != 2) {
                    	if($ynvideo_enable)
                    	{
	                        echo $this->htmlLink(array(
	                            'route' => 'video_general',
	                            'action' => 'delete',
	                            'video_id' => $item->video_id,
	                            'event_id' => $this->event->event_id,
	                            'format' => 'smoothbox',
	                            'case' => 'video',
	                            'parent_type' => 'event',
	                            ), $this->translate('Delete Video'), array('class' => 'buttonlink smoothbox icon_video_delete'));
                        }
                    	else
						{
	                     	echo $this->htmlLink(array(
	                     	        'route' => 'default', 
			                     	'module' => 'video', 
			                     	'controller' => 'index', 
			                     	'action' => 'delete', 
			                     	'video_id' => $item->video_id, 
			                     	'event_id' => $this->event->event_id,
	                            	'case' => 'video',
	                            	'parent_type' => 'event',
			                     	'format' => 'smoothbox'), 
			                     	$this->translate('Delete Video'), array('class' => 'buttonlink smoothbox icon_video_delete'
					         ));
				        }
					}
                    ?>
                    
                    <?php
                    
                 	if($this->viewer->isSelf($this->event->getOwner()))
					{
	                    $table = Engine_Api::_() -> getDbTable('highlights', 'ynevent');
	                    $select = $table -> select() -> where("event_id = ?", $this->event->event_id) -> where('item_id = ?', $item->getIdentity()) -> where("type = 'video'") -> limit(1);
						$row = $table -> fetchRow($select);
						if(!$row){
							$row = $table -> createRow();
							$row -> setFromArray(array(
													'event_id' => $this->event->event_id, 
													'item_id' => $item->getIdentity(), 
													'user_id' => $item->owner_id, 
													'type' => 'video', 
													'creation_date' => date('Y-m-d H:i:s'), 
													'modified_date' => date('Y-m-d H:i:s')));
							$row -> save();
						}
						
						
	                    if($row->highlight){
	                     	echo $this->htmlLink(
				  				array(
					  				'route' => 'event_extended', 
					  				'controller'=>'video',
					  				'action'=>'highlight',
					  				'event_id' => $this->event->event_id,
					  				'video_id' => $item->getIdentity(),
					  				
				  				), 
				  				$this->translate('Un Highlight'), 
				  				array(
				    				'class' => 'smoothbox buttonlink icon_ynevent_unhighlight'
				  				)
					  		);
			  			}
						else {
							echo $this->htmlLink(
				  				array(
					  				'route' => 'event_extended', 
					  				'controller'=>'video',
					  				'action'=>'highlight',
					  				'event_id' => $this->event->event_id,
					  				'video_id' => $item->getIdentity(),
				  				), 
				  				$this->translate('Highlight'), 
				  				array(
				    				'class' => 'smoothbox buttonlink icon_ynevent_highlight'
				  				)
					  		);
						}
					}
		  		?>
                </div>
                <div class="video_info video_info_in_list">
                    <div class="ynvideo_title">
                        <?php echo $this->htmlLink($item->getHref(), htmlspecialchars($item->getTitle())) ?>
                        <?php if($row->highlight) :?>
                       			<strong style="color: red;"><?php echo " - " . $this->translate("highlighted"); ?></strong> 
                        <?php endif;?>
                    </div>
                    <div class="video_stats">
                        <?php echo $this->partial('_video_views_stat.tpl','ynevent', array('video' => $item)) ?>
                        <div class="ynvideo_block">
                            <?php echo $this->partial('_video_rating_big.tpl','ynevent', array('video' => $item)) ?>
                        </div>
                    </div>
                    <div class="video_desc">
                            <?php echo $this->string()->truncate($this->string()->stripTags($item->description), 300) ?>
                    </div>
                    <?php if ($item->status == 0): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.') ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 2): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.') ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 3): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 4): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 5): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 7): ?>
                        <div class="tip">
                            <span>
                        <?php echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
  </ul>
  <br/>
  <div class="ynvideo_pages">
      <?php
      echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => true,
          'query' => $this->formValues,
      ));
      ?>
  </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('You do not have any videos.'); ?>
        </span>
    </div>
<?php endif; ?>   