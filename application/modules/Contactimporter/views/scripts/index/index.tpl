<h2>
  <?php echo $this->hello_world_title ?>
</h2>

<div>
  <?php echo $this->translate("Contact Importer") ?>
</div>

<br />
<br />


<div class='layout_right'>
  <?php if( $this->viewer()->getIdentity() ): ?>
  <div class="quicklinks">
    <ul>
      <li>
        <?php echo $this->htmlLink(array('action' => 'create', 'reset' => false), $this->translate('Post a Contact Importer'), array('class' => 'buttonlink icon_blog_new')) ?>
      </li>
    </ul>
  </div>
  <?php endif; ?>
</div>


<div class='layout_middle'>

  <h2>
    <?php echo $this->translate('User Contact Importers') ?>
  </h2>

  <?php if( $this->paginator->count() >= 1): ?>
    <div>
      <?php echo $this->paginationControl($this->paginator); ?>
    </div>
    <br />
  <?php endif; ?>

  <ul class="blogs_browse">
    <?php foreach( $this->paginator as $Contactimporter ): ?>
      <li>
        <div class='blogs_browse_photo'>
          <?php echo $this->htmlLink($Contactimporter->getHref(), $this->itemPhoto($Contactimporter, 'thumb.icon')) ?>
        </div>
        <div class='blogs_browse_info'>
          <p class='blogs_browse_info_title'>
            <?php echo $this->htmlLink($Contactimporter->getHref(), $Contactimporter->getTitle()) ?>
          </p>
          <p class='blogs_browse_info_date'>
            <?php echo $this->translate('Posted by %s about %s', $Contactimporter->getOwner()->toString(), $this->timestamp($Contactimporter->creation_date)) ?>
          </p>
          <p class='blogs_browse_info_blurb'>
            <?php echo $this->viewMore($Contactimporter->getDescription()) ?>
          </p>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
  
</div>