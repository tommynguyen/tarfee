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
	<h3><?php echo $this -> translate("My Favorite Talks")?></h3>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="ynblogs_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li id="tftalk_favorite_<?php echo $item -> getIdentity()?>">
          <div class='ynblogs_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
          </div>
          <div class='ynblogs_browse_options'>
           	<a href="javascript:;" onclick="unfavourite_blog(<?php echo $item -> getIdentity()?>)"><i class="fa fa-heart"></i> <?php echo $this->translate('Unfavourite')?></a>
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
<script type="text/javascript">
   function unfavourite_blog(blogId)
   {
   	   var url = '<?php echo $this -> url(array('action' => 'un-favorite-ajax'), 'blog_general', true)?>';
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  url,
            'data' : {
                'blog_id' : blogId
            },
            'onComplete':function(responseObject)
            {  
                $('tftalk_favorite_' + blogId).destroy();
            }
        });
        request.send();  
   } 
</script>