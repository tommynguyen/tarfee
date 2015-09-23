<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('ynblog_filter_form').submit();
  }
</script>
<h2>
 <?php echo $this->owner;?>
 <?php echo $this->translate("'s Entries")?>
</h2>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class='ynblogs_entrylist'>
  <?php foreach ($this->paginator as $item):
        if($item->authorization()->isAllowed(null,'view')):
  ?>
    <li>
      <div class ="ynblog_entry_owner_photo">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
      </div>
      <span>
        <h3>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </h3>
        <div class="ynblog_entrylist_entry_date">
         <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getParent(), $item->getParent()->getTitle()) ?>
          <?php echo $this->timestamp($item->creation_date) ?>
        </div>
        <div class="ynblog_entrylist_entry_body">
          <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
        </div>
        <?php if ($item->comment_count > 0) :?>          
          <?php echo $this->htmlLink($item->getHref(), $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) , array('class' => 'buttonlink icon_comments')) ?>
        <?php endif; ?>
      </span>
    </li>
  <?php endif; endforeach; ?>
  </ul>

<?php elseif( $this->category || $this->tag ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('%1$s has not published a blog entry with that criteria.', $this->owner->getTitle()); ?>
    </span>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('%1$s has not written a blog entry yet.', $this->owner->getTitle()); ?>
    </span>
  </div>
<?php endif; ?>

 <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
<br/>