<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('ynblog_filter_form').submit();
  }
</script>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class='ynblogs_entrylist'>
  <?php foreach ($this->paginator as $item): ?>
    <li>
      <div class ="ynblog_entry_owner_photo">
        <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
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
          <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 200) ?>
        </div>
      </span>
    </li>
  <?php endforeach; ?>
  </ul>

<?php elseif( $this->category || $this->tag ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No one has not published any blog entries with that criteria.'); ?>
    </span>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No one has written any blog entries yet.'); ?>
    </span>
  </div>
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator,null, array("pagination/pagination.tpl","ynblog"));?>