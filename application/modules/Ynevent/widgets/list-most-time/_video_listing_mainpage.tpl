<div class="ynvideo_thumb_wrapper video_thumb_wrapper">
    <?php if ($this->video->parent_type == 'user_playercard') :?>
        <span class="icon-player">
            <img src="application\themes\ynresponsive-event\images\icon-player.png" />
        </span>
    <?php endif; ?>

    <?php
    if ($this->video->photo_id) {
        echo $this->htmlLink($this->video->getPopupHref(), $this->itemPhoto($this->video, 'thumb.large'), array('class'=>'smoothbox'));
    } else {
        echo $this->htmlLink($this->video->getPopupHref(),'<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynvideo/externals/images/video.png">', array('class'=>'smoothbox'));;
    }
    ?>
</div>

<div class="button-action-video">
    <?php if($this -> viewer() -> getIdentity()):?>
    <div id="favorite_<?php echo $this->video -> getIdentity()?>">
        <?php if($this->video -> hasFavorite()):?>
            <a href="javascript:;" title="<?php echo $this->translate('Unfavorite')?>" style="background:#ff6633;color: #fff" onclick="unfavorite_video(<?php echo $this->video -> getIdentity()?>)">
                <i class="fa fa-heart"></i>
            </a>
        <?php else:?>   
            <a href="javascript:;" title="<?php echo $this->translate('Favorite')?>" onclick="favorite_video(<?php echo $this->video -> getIdentity()?>)">
                <i class="fa fa-heart-o"></i>
            </a>
        <?php endif;?>  
    </div>

    <div id="like_unsure_dislike_<?php echo $this -> video -> getIdentity()?>">
        <?php echo $this -> action('list-likes', 'video', 'ynvideo', array( 'id' => $this -> video -> getIdentity()));?>
    </div>
    <?php endif;?>
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
        <?php $position = $player->getPosition()?>
        <?php if ($position) : ?>
		<div class="player-position">
    		<?php 
    		preg_match_all('/[A-Z]/', $position, $matches);
			echo implode($matches[0]);?>
 		</div>
        <?php endif;?>
    </div>
</div>
<?php endif;?>
<?php endif;?>
<div class="video-title">
    <?php echo $this->htmlLink($this->video->getPopupHref(), $this->video->getTitle(), array('class'=>'smoothbox'))?>
</div>
<div class="video-statistic-rating">

    <div class="video-statistic">
        <span><?php echo $this->translate(array('%s view','%s views', $this->video->view_count), $this->video->view_count)?></span>
        <?php $commentCount = $this->video->comments()->getCommentCount(); ?>
        <span><?php echo $this->translate(array('%s comment','%s comments', $commentCount), $commentCount)?></span>
    </div>
    <?php 
        echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $this->video));
    ?>
</div>

<div class="video_author">
    <?php $user = $this->video->getOwner() ?>
    <?php if ($user) : ?>
        <?php echo $this->translate('By') ?>
        <?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
    <?php endif; ?>
    <?php 
        $session = new Zend_Session_Namespace('mobile');
         if(!$session -> mobile)
         {
    ?>
    <?php } ?>
</div>
<?php 
Engine_Api::_() -> core() -> clearSubject();
?>