<?php
?>
<div class="ynvideo_thumb_wrapper video_thumb_wrapper">
    <?php
    if ($this->video->photo_id) {
        echo $this->htmlLink($this->video->getPopupHref(), $this->itemPhoto($this->video, 'thumb.large'), array('class'=>'smoothbox'));
    } else {
        echo $this->htmlLink($this->video->getPopupHref(),'<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynvideo/externals/images/video.png">', array('class'=>'smoothbox'));
    }
    ?>
</div>

<div class="player-info-author">
	<?php $player = $this->video->getParent();?>
	<?php if ($player && $player->getType() == 'user_playercard'):?>
	<div class="player-info">
	    <div class="player-photo" title="<?php echo $player -> getTitle();?>">
	        <?php echo $this->itemPhoto($player, 'thumb.icon')?>
	    </div>
	    <div class="player_info_detail">
	        <div class="player-title" title="<?php echo $player -> getTitle();?>">
	            <?php echo $player;?>
	        </div>
	        <?php $position = $player->getPosition()?>
	        <?php if ($position) : ?>
			<div class="player-position" title="<?php echo $this -> translate($position);?>">
	        	<?php 
		    		preg_match_all('/[A-Z]/', $position, $matches);
					echo implode($matches[0]);?>
			</div>
	        <?php endif;?>
	        <?php if($player -> getSport()):?>
				<span title="<?php echo $this -> translate($player -> getSport());?>"><?php echo $this -> itemPhoto($player -> getSport(), 'thumb.icon');?></span>
			<?php endif;?>
			<?php if($this -> viewer() -> getIdentity() && !$player -> isOwner($this -> viewer())):?>
		    	<span title="<?php echo $this -> translate("Keep Eye on this player card")?>" id="user_eyeon_<?php echo $player -> getIdentity()?>">
		    		<?php if($player->isEyeOn()): ?>              
		        	<a class="actions_generic eye-on eye_on" href="javascript:void(0);" onclick="removeEyeOn('<?php echo $player->getIdentity() ?>')">
		        		<i class="fa fa-eye-slash"></i>
		    		</a>
		    		<?php else: ?>
		        	<a class="actions_generic eye_on" href="javascript:void(0);" onclick="addEyeOn('<?php echo $player->getIdentity() ?>')">
		    			<i class="fa fa-eye"></i>
		        	</a>
		    		<?php endif; ?>
				</span>
			<?php endif; ?>
	    </div>
	</div>
	<?php endif;?>
	<div class="video-statistic-rating">
		<div class="video-rating">
			<?php 
	        	echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $this->video));
	    	?>
		</div>
		<div class="video-statistic">
			<span><?php echo $this->translate(array('%s view','%s views', $this->video->view_count), $this->video->view_count)?></span>
			<?php $commentCount = $this->video->comments()->getCommentCount(); ?>
			<span><?php echo $this->translate(array('%s comment','%s comments', $commentCount), $commentCount)?></span>
		</div>
	</div>
	<div class="video_author">
	    <?php $user = $this->video->getOwner() ?>
	    <?php $user = ($user) ? $user : $this->translate('Unknown')?>
		
		<?php echo $this->translate('by') ?>
		<?php echo $user ?>
	</div>
</div>