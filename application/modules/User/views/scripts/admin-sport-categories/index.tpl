<h2><?php echo $this->translate("Player Card Settings") ?></h2>
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
                <h3> <?php echo $this->translate("Sport Categories") ?> </h3>
                <div>
                	<?php if(is_object($this->category)): $sportcategory_id = $this->category->getIdentity();?>
			         <?php
			         foreach($this->category->getBreadCrumNode() as $node): ?>
			        		<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'sport-categories', 'action' => 'index', 'parent_id' =>$node->sportcategory_id), $this->translate($node->getTitle()), array()) ?>
			        		&raquo;
			         <?php endforeach; ?>
			         <strong><?php
			            echo $this->category;
			         ?></strong>
					<?php else: $sportcategory_id = 0;?>
						<strong><?php echo $this->category; ?></strong>
					<?php endif;?>
                </div>
                <br />
                <?php if (count($this->categories) > 0): ?>

                    <table class='admin_table'>
                        <thead>
                            <tr>
                                <th><?php echo $this->translate("Category Name") ?></th>
                                <th><?php echo $this->translate("Sub-Category") ?></th>
                                <th><?php echo $this->translate("Options") ?></th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php foreach ($this->categories as $category): ?>
                                <tr>
                                    <td><?php echo $category->title ?></td>
                                    <td><?php echo $category->countChildren() ?></td>
                                    <td>
                                        <?php
                                        echo $this->htmlLink(
                                                array('route' => 'admin_default', 'module' => 'user', 'controller' => 'sport-categories', 'action' => 'edit-category', 'id' => $category->sportcategory_id), $this->translate('edit'), array('class' => 'smoothbox',
                                        ))
                                        ?>
                                        |
                                        <?php
                                        echo $this->htmlLink(
                                            array('route' => 'admin_default', 'module' => 'user', 'controller' => 'sport-categories', 'action' => 'delete-category', 'id' => $category->sportcategory_id), $this->translate('delete'), array('class' => 'smoothbox',
                                        ))
                                        ?>
                                        <?php
                                       if(count($this->category->getBreadCrumNode()) < 2):?>
                                        |
                                       <?php  echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'sport-categories', 'action' => 'add-category', 'parent_id' => $category->sportcategory_id), $this->translate('add sub-category'), array(
                                                'class' => 'smoothbox',
                                            ));
                                            ?>
                                            |
                                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'sport-categories', 'action' => 'index', 'parent_id' => $category->sportcategory_id), $this->translate('view sub-category'), array(
                                        )) ?>
                                       <?php endif;?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <br/>
                    <div class="tip">
                        <span><?php echo $this->translate("There are currently no sport categories.") ?></span>
                    </div>
                <?php endif; ?>
                <br/>

                <?php
               
                if(!isset($this->category))
                {
                    $parentId = 0;
                }
                else{
                    $parentId = $this->category->sportcategory_id;
                }
                
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'sport-categories', 'action' => 'add-category', 'parent_id' => $parentId), $this->translate('Add New Category'), array(
                    'class' => 'smoothbox buttonlink',
                    'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);'))
                ?>

            </div>
        </form>
    </div>
</div>
