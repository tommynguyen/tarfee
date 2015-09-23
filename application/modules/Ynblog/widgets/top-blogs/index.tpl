<ul class="tf_list_talk" style="overflow: hidden;">
  <?php foreach( $this->blogs as $item ):?>
  	  	<?php if ($item->checkPermission($item->getIdentity())) :?>
    <li class="ynblog_new">

      <div class="tf_talk_owner">
            <?php echo $this->htmlLink($item -> getOwner()->getHref(), $this->itemPhoto($item -> getOwner(), 'thumb.icon', $item -> getOwner()->getTitle(), array('style' => 'width: auto')), array('class' => 'members_thumb')) ?>   

            <div class='members_info'>
                <div class='members_name'>
                      <?php echo $this->htmlLink($item -> getOwner()->getHref(), $item -> getOwner() ->getTitle()) ?>
                </div>
                <div class='members_date'>
                  <?php echo $this->timestamp($item->creation_date); ?>
                </div>
            </div>
      </div>


          <div class="tf_talk_info">
              <div class="talk_title">
                    <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
              </div>
          
              <div class="talk_description">
                    <?php echo $this -> viewMore($item -> body); ?>
              </div>
              <div class="talk_statistics">
		          	<span><?php echo $this->translate(array('%s view','%s views', $item -> view_count), $item -> view_count)?></span>
		          	<span><?php echo $this->translate(array('%s comment','%s comments', $item -> comment_count), $item -> comment_count)?></span>
		        	<?php $likeCount = $item ->likes()->getLikeCount(); ?>
		        	<span><?php echo $this->translate(array('%s like','%s likes', $likeCount), $likeCount)?></span>
		        	<?php $disLikeCount = Engine_Api::_()->getDbtable('dislikes', 'yncomment') -> getDislikeCount($item); ?>
		        	<span><?php echo $this->translate(array('%s dislike','%s dislikes', $disLikeCount), $disLikeCount)?></span>
	          </div>
          </div>
    </li>
        <?php endif; ?>
   <?php endforeach; ?>
</ul>