<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('blog_filter_form').submit();
  }
</script>

<div class='layout_right'>
  <?php echo $this->form->render($this) ?>
</div>

<div class='layout_middle'>
	<h3><?php echo $this -> translate("My Talks")?></h3>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="ynblogs_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='ynblogs_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
          </div>
          <div class='ynblogs_browse_options'>
            <?php
            echo $this->htmlLink(array(
              'route' => 'blog_specific',
              'action' => 'edit',
              'blog_id' => $item->getIdentity(),
              'reset' => true,
            ), $this->translate('Edit'), array(
              'class' => 'buttonlink icon_ynblog_edit',
            ));?>
            <?php
            echo $this->htmlLink(array(
                'route' => 'blog_specific',
                'action' => 'delete',
                'blog_id' => $item->getIdentity(),
                'format' => 'smoothbox'
                ), $this->translate('Delete'), array(
              'class' => 'buttonlink smoothbox icon_ynblog_delete'
            ));?>
          </div>
          <div class='ynblogs_browse_info'>
            <div class='ynblogs_browse_info_title'>
              <b><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></b>
              -
              <?php $status = $item->getStatus();?>
                <font class ="ynblogs_browse_info_status_<?php echo $status['type'];?>">
                   <?php echo $this->translate($status['condition']);?>
                </font>
            </div>
            <p class='ynblogs_browse_info_date'>
              <?php echo $this->translate('Posted by');?>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
              <?php echo $this->translate('about');?>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
            </p>
            <p class='ynblogs_browse_info_blurb'>
              <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
            </p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any blog entries that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any blog entries.');?>
        <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Get started by %1$swriting%2$s a new entry.', '<a href="'.$this->url(array('action' => 'create'), 'blog_general').'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>

</div>