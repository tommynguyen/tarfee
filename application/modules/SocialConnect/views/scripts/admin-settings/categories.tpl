<h2><?php echo $this->translate("Footer Management") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
  <div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Footer Categories") ?></h3>
          <?php if(count($this->categories)>0):?>
         <table class='admin_table' style="position: relative; width: 100%">
          <thead>
            <tr>
              <th style="width: 80%"><?php echo $this->translate("Category Name") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody id="category_list">
            <?php foreach ($this->categories as $category): ?>
                    <tr id='category_item_<?php echo $category->getIdentity() ?>'>
                      <td><?php echo $category->category_name?></td>
                      <td>
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'social-connect', 'controller' => 'admin-settings', 'action' => 'edit-category', 'id' =>$category->category_id), $this->translate('edit'), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'social-connect', 'controller' => 'admin-settings', 'action' => 'delete-category', 'id' =>$category->category_id), $this->translate('delete'), array(
                          'class' => 'smoothbox',
                        )) ?>
						|
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'social-connect', 'controller' => 'admin-settings', 'action' => 'pages', 'id' =>$category->category_id), $this->translate('show pages'), array(
                          'class' => '',
                        )) ?>
                      </td>
                    </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no categories.") ?></span>
      </div>
      <?php endif;?>
        <br/>
        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'social-connect', 'controller' => 'admin-settings', 'action' => 'add-category'), 'Add New Category', array(
          'class' => 'smoothbox buttonlink',
          'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>
    </div>
    </form>
    </div>
  </div>
<script type="text/javascript">
en4.core.runonce.add(function(){
    new Sortables('category_list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('controller'=>'settings','action'=>'sort-category'), 'admin_default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'order': this.serialize().toString(),
          }
        }).send();
      }
    });
    
});
</script>