<?php if(!Engine_Api::_()->getDbtable('likes', 'core')->isLike($this->video, $this->viewer())):?>
	<a id="like_video_<?php echo $this->video->getIdentity() ?>" title="<?php echo $this->translate('Like') ?>" href="javascript:void(0);" onclick="video_like('<?php echo $this->video->getIdentity() ?>', 'like')">
		<i class="fa fa-thumbs-up"></i> 
	</a>
<?php else:?>
	<a id="unlike_video_<?php echo $this->video->getIdentity() ?>" title="<?php echo $this->translate('Like') ?>" style=" background: #ff6633;color:#fff" href="javascript:void(0);" onclick="video_like('<?php echo $this->video->getIdentity() ?>', 'unlike')">
		<i class="fa fa-thumbs-up"></i> 
	</a>
<?php endif;?>
<!--
<?php if(Engine_Api::_()->getDbtable('unsures', 'yncomment')->getUnsure($this->video, $this->viewer())):?>
	<a id="undounsure_video_<?php echo $this->video->getIdentity() ?>" title="<?php echo $this->translate('Unsure') ?>" style="background: #2A6496" href="javascript:void(0);" onclick="video_like('<?php echo $this->video->getIdentity() ?>', 'undounsure')">
		<i class="fa fa-meh-o"></i> 
	</a>
	<?php else :?>
	<a id="unsure_video_<?php echo $this->video->getIdentity() ?>" title="<?php echo $this->translate('Unsure') ?>" href="javascript:void(0);" onclick="video_like('<?php echo $this->video->getIdentity() ?>', 'unsure')">
		<i class="fa fa-meh-o"></i> 
	</a>
	<?php endif;?>
-->
<?php if(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislike($this->video, $this->viewer())):?>
<a id="undislike_video_<?php echo $this->video->getIdentity() ?>" title="<?php echo $this->translate('Dislike') ?>" style="background: #ff6633; color: #fff" href="javascript:void(0);" onclick="video_like('<?php echo $this->video->getIdentity() ?>', 'undislike')">
	<i class="fa fa-thumbs-down"></i> 
	</a>
<?php else :?>
<a id="dislike_video_<?php echo $this->video->getIdentity() ?>" title="<?php echo $this->translate('Dislike') ?>" href="javascript:void(0);" onclick="video_like('<?php echo $this->video->getIdentity() ?>', 'dislike')">
	<i class="fa fa-thumbs-down"></i> 
	</a>
<?php endif;?>
