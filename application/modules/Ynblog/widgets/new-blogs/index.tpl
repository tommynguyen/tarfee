<ul class="generic_list_widget" style="overflow: hidden;">
  <?php if(count($this->blogs) > 0):?>
  <?php foreach( $this->blogs as $item ):?>
  	<?php if ($item->checkPermission($item->getIdentity())) :?>
    <li class="ynblog_new">
          <div class="photo">
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'thumb')) ?>
          </div>
          <div class="info">
              <div class="title">
                    <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
              </div>
              <div class="stats">
                    <?php
                      $owner = $item->getOwner();
                      echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
                    ?>
                    -
                    <?php echo $this->timestamp($item->creation_date); ?>
              </div>
              <div class="description">
                    <?php echo $item->getDescription(); ?>
              </div>
          </div>
    </li>
    <?php endif; ?>
        <?php endforeach; ?>
    <?php if(count($this->blogs) == $this->limit): ?>
        <li>
          <div class="more" style="float:right;margin-left:15px;margin-bottom: 10px;">
              <a href="<?php echo $this->url(array(),'default'); ?>talks/listing/sort/recent" >
                <?php echo $this->translate('View all');?>
              </a>
          </div>
        </li>
    <?php endif; ?>

    <?php else:?>
        <div class="tip">
            <span>
                   <?php echo $this->translate('No one has written any blog entries yet.');?>
            </span>
        </div>
  <?php endif;?>
</ul>
