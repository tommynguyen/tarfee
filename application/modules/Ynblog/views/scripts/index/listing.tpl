<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('ynblog_filter_form').submit();
  }
</script>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class='ynblogs_entrylist'>
  <?php foreach ($this->paginator as $item):
         if($item->authorization()->isAllowed(null,'view')):
  ?>
    <li>
      <div class ="ynblog_entry_owner_photo" style="display: none">
        <?php //echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
      </div>

        <h3>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </h3>

        <?php $category = Engine_Api::_ ()->getItemTable ( 'blog_category' )->find ( $item->category_id )->current (); ?>

        <?php if($category): ?>
        <div class="ynblog_category"><?php echo $category -> category_name ?></div>
        <?php endif; ?>

        <div class="ynblog_entrylist_entry_body">
           <?php echo $this -> viewMore($item -> body, 800); ?>
        </div>

        <div class="ynblog_entrylist_entry_date">
         <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getParent(), $item->getParent()->getTitle()) ?>

          <span>&nbsp;-&nbsp;<?php echo $this->timestamp($item->creation_date) ?></span>
        </div>

        <div class="ynblog_statistics">
        	<?php $likeCount = $item ->likes()->getLikeCount(); ?>
        	<span><?php echo $this->translate(array('%s like','%s likes', $likeCount), $likeCount)?></span>
        	<?php $disLikeCount = Engine_Api::_()->getDbtable('dislikes', 'yncomment') -> getDislikeCount($item); ?>
        	<span><?php echo $this->translate(array('%s dislike','%s dislikes', $disLikeCount), $disLikeCount)?></span>
	        <span><?php echo $this->translate(array('%s comment','%s comments', $item -> comment_count), $item -> comment_count)?></span>
	        <!--
	        <?php $disLikeCount = Engine_Api::_()->getDbtable('dislikes', 'yncomment') -> getDislikeCount($item); ?>
        	<span><?php echo $this->translate(array('%s share','%s shares', $disLikeCount), $disLikeCount)?></span>
        	-->
        </div>

    </li>
  <?php endif; endforeach; ?>
  </ul>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No one has not published any talks with that criteria.'); ?>
    </span>
  </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator,null, array("pagination/pagination.tpl","ynblog"));?>