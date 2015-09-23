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
                <?php echo $this -> viewMore($item->body); ?>
          </div>
      </div>
      <div class="talk_statistics">
          <span><?php echo $this->translate(array('%s view', '%s views', $item -> view_count), $item -> view_count) ?></span>
          <span><?php echo $this->translate(array('%s comment', '%s comments', $item -> comment_count), $item -> comment_count) ?></span>
          <?php 
          $totalLike = Engine_Api::_() -> getDbtable('likes', 'yncomment') -> likes($item) -> getLikeCount();
          $totalDislike = Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> getDislikeCount($item);?>
          <span><?php echo $this->translate(array('%s like', '%s likes', $totalLike), $totalLike) ?></span>
          <span><?php echo $this->translate(array('%s dislike', '%s dislikes', $totalDislike), $totalDislike) ?></span>
      </div>
    </li>
    <?php endif; ?>
  <?php endforeach; ?>
  <!-- 
    <?php if(count($this->blogs) == $this->limit): ?>
       <li>
          <div class="more" style="float:right;margin-left:15px;margin-bottom: 10px;">
              <a href="<?php echo $this->url(array(),'default'); ?>talks/listing/sort/recent" >
                <?php echo $this->translate('View all');?>
              </a>
          </div>
       </li> 
    <?php endif; ?>
    -->
</ul>
