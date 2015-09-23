<h2><?php echo $this->translate("Group Buy Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
      	
        <h3><?php echo $this->translate("Deal Categories") ?></h3>
        
        <div>
         <?php foreach($this->category->getBreadCrumNode() as $node): ?>
        		<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'groupbuy', 'controller' => 'category', 'action' => 'index', 'parent_id' =>$category->category_id), $this->translate($node->shortTitle()), array()) ?>
        		&raquo;
         <?php endforeach; ?>
         <strong><?php
         if(count($this->category->getBreadCrumNode()) > 0):
            echo $this->category;
          else:
            echo  $this->translate("All Categories");
          endif; ?></strong>
        </div>
        <br />
          <?php if(count($this->categories)>0):?>
         <table class='admin_table'>
          <thead>

            <tr>
              <th><?php echo $this->translate("Category Name") ?></th>
              <th><?php echo $this->translate("Number of Times Used") ?></th>
              <th><?php echo $this->translate("Sub-Category") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->categories as $category): ?>
              <tr>
                <td><?php echo $category->title?></td>
                <td><?php echo $category->getUsedCount()?></td>
                <td><?php echo $category->countChildren() ?></td>
                <td>
                  
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'groupbuy', 'controller' => 'category', 'action' => 'edit-category', 'id' =>$category->category_id), $this->translate('edit'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'groupbuy', 'controller' => 'category', 'action' => 'delete-category', 'id' =>$category->category_id), $this->translate('delete'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'groupbuy', 'controller' => 'category', 'action' => 'add-category', 'parent_id' =>$category->category_id), $this->translate('add sub-category'), array(
                    'class' => 'smoothbox',
                  )) ?>
                  |
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'groupbuy', 'controller' => 'category', 'action' => 'index', 'parent_id' =>$category->category_id), $this->translate('view sub-category'), array(
                    
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
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'groupbuy', 'controller' => 'category', 'action' => 'add-category','parent_id'=>$this->category->getIdentity()), $this->translate('Add Category'), array(
          'class' => 'smoothbox buttonlink',
          'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);')) ?>
    </div>
    </form>
    </div>
  </div>
     
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
  height: 65px;
}
</style>   