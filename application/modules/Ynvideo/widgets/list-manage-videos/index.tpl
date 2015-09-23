<?php if (($this->current_count >= $this->quota) && !empty($this->quota)): ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('You have already created the maximum number of videos allowed. If you would like to post a new video, please delete an old one first.'); ?>
        </span>
    </div>
    <br/>
<?php else : ?>
    <h2><?php echo $this->translate('My Videos') ?></h2>
<?php endif; ?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="ynvideo_frame ynvideo_videos_manage videos_manage">
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
                        <?php echo $this->partial('_video_duration.tpl', array('video' => $item)) ?>
                    <?php endif; ?>
                    <?php
                    if ($item->photo_id) {
                        echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
                    } else {
                        echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Ynvideo/externals/images/video.png">';
                    }
                    ?>
                    <span class="video_button_add_to_area">
                        <button class="ynvideo_uix_button ynvideo_add_button" video-id="<?php echo $item->getIdentity() ?>">
                            <div class="ynvideo_plus" />
                        </button>
                    </span>
                </div>
                <div class='video_options'>
                    <?php
                    echo $this->htmlLink(array(
                        'route' => 'video_general',
                        'action' => 'edit',
                        'video_id' => $item->video_id
                        ), $this->translate('Edit Video'), array('class' => 'buttonlink icon_ynvideo_edit'))
                    ?>
                    <?php
                    if ($item->status != 2) {
                        echo $this->htmlLink(array(
                            'route' => 'video_general',
                            'action' => 'delete',
                            'video_id' => $item->video_id,
                            'format' => 'smoothbox'
                            ), $this->translate('Delete Video'), array('class' => 'buttonlink smoothbox icon_ynvideo_delete'));
                    }
                    ?>
                </div>
                <div class="video_info video_info_in_list">
                    <div class="ynvideo_title">
                        <?php echo $this->htmlLink($item->getHref(), htmlspecialchars($item->getTitle())) ?>
                    </div>
                    <div class="video_stats">
                        <?php echo $this->partial('_video_views_stat.tpl', array('video' => $item)) ?>
                        <div class="ynvideo_block">
                            <?php echo $this->partial('_video_rating_big.tpl', array('video' => $item)) ?>
                        </div>
                    </div>
                    <div class="video_desc">
                            <?php echo $this->string()->truncate($this->string()->stripTags($item->description), 300) ?>
                    </div>
                    <?php
                    $session = new Zend_Session_Namespace('mobile');
                   if ($item->status == 0): ?>
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
                        		<?php  
                        		if($session -> mobile)
									echo $this->translate('Video conversion failed.');
		   						else
                					echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); 
                        		?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 4): ?>
                        <div class="tip">
                            <span>
                        	<?php  
                        	if($session -> mobile)
								echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG.');
	   						else
           		 				echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 5): ?>
                        <div class="tip">
                            <span>
                        <?php 
                        if($session -> mobile)
							echo $this->translate('Video conversion failed. Audio files are not supported.');
   						else
                        	echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php elseif ($item->status == 7): ?>
                        <div class="tip">
                            <span>
                        <?php 
                        if($session -> mobile)
							echo $this->translate('Video conversion failed. You may be over the site upload limit.');
   						else
                        	echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="' . $this->url(array('action' => 'create', 'type' => 3)) . '">', '</a>'); ?>
                            </span>
                        </div>
                            <?php endif; ?>
                </div>
            </li>
                <?php endforeach; ?>
                <?php if ($this->paginator->getCurrentItemCount() < $totalVideo) : ?>
            <li class="ynvideo_pages">
            <?php
            echo $this->paginationControl($this->paginator, null, null, array(
                'query' => $this->params,
            ));
            ?>
            </li>
            <?php endif; ?>
    </ul>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('You do not have any videos.'); ?>
            <?php if ($this->can_create): ?>
                <?php echo $this->translate('Get started by %1$sposting%2$s a new video.', '<a href="' . $this->url(array('action' => 'create')) . '">', '</a>'); ?>
            <?php endif; ?>
        </span>
    </div>
<?php endif; ?>    