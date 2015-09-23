<div class="ynvideo_thumb_wrapper video_thumb_wrapper">
    <?php
    if ($this->video->photo_id) {
        echo $this->htmlLink($this->video->getPopupHref(), $this->itemPhoto($this->video, 'thumb.large'), array('class'=>'smoothbox'));
    } else {
        echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynvideo/externals/images/video.png">';
    }
    ?>
    
</div>

<div class="video-title">
	<?php echo $this->htmlLink($this->video->getPopupHref(), $this->video->getTitle(), array('class'=>'smoothbox'))?>
</div>
<div class="video-statistic-rating">
	<div class="video-statistic">
		<?php echo $this->translate(array('%s view','%s views', $this->video->view_count), $this->video->view_count)?>
		<br>
		<?php $commentCount = $this->video->comments()->getCommentCount(); ?>
		<?php echo $this->translate(array('%s comment','%s comments', $commentCount), $commentCount)?>
	</div>

	<?php 
    	echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $this->video));
	?>
</div>

<?php if ($this->video->parent_type == 'user_playercard') :?>
	<?php $player = $this->video->getParent();?>
	<?php if ($player):?>
	
	<div class="player-info">
		<div class="player-photo">
			<?php echo $this->itemPhoto($player, 'thumb.icon')?>
		</div>
		<div class="player_info_detail">
			<div class="player-title">
				<?php echo $player?>
			</div>
			<div class="player-position">
			<?php $position = $player->getPosition()?>
			<?php if ($position) : ?>
				<?php echo $position;?>
			<?php endif;?>
	
			<?php $sport = $player->getSport();?>
				<?php if ($sport):?>	
					<?php echo ' - '.$sport->title ?>
			<?php endif;?>
			</div>
		</div>
	</div>
	<?php endif;?>
<?php endif;?>
<?php $user = $this->video->getOwner() ?>
<?php if ($user) : ?>
	<div class="nickname">
		<?php echo $this->translate('By') ?>
	    <?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
    </div>
<?php endif; ?>

<?php if($this -> viewer() -> getIdentity()):?>
	<span class="tf_btn_action">
		<a href="javascript:;" class="tf_button_action" onclick="unfavorite_video(<?php echo $this->video -> getIdentity()?>)"><?php echo $this->translate('remove')?></a>
	</span>
<?php endif;?>