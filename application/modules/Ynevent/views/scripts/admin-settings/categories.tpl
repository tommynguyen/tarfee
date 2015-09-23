<?php ?>

<h2><?php echo $this->translate("Events Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
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
                <h3> <?php echo $this->translate("Event Categories") ?> </h3>
                <p class="description">
                    <?php echo $this->translate("YNEVENT_VIEWS_SCRIPTS_ADMINSETTINGS_CATEGORIES_DESCRIPTION") ?>
                </p>
                <div>
                	<?php echo $this->htmlLink(array('route' => 'ynevent_admin_default', 'action' => 'categories', 'parent_id' => 0), $this->translate('All Categories'), array()) ?>
					<?php  if(isset($this->category)): ?>
                    <?php foreach ($this->category->getBreadCrumNode() as $node): ?>
                        &raquo;
                        <?php echo $this->htmlLink(array('route' => 'ynevent_admin_default', 'action' => 'categories', 'parent_id' => $node->category_id), $this->translate($node->shortTitle()), array()) ?>
                    <?php endforeach; ?>
                   <?php endif; ?>
                </div>
                <br />
                <?php if (count($this->categories) > 0): ?>

                    <table class='admin_table'>
                        <thead>
                            <tr>
                                <th><?php echo $this->translate("Category Name") ?></th>
                                <?php //              <th># of Times Used</th>  ?>
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
                                                array('route' => 'ynevent_admin_default', 'action' => 'edit-category', 'id' => $category->category_id), $this->translate('edit'), array('class' => 'smoothbox',
                                        ))
                                        ?>
                                        |
                                        <?php
                                        echo $this->htmlLink(
                                                array('route' => 'ynevent_admin_default', 'action' => 'delete-category', 'id' => $category->category_id), $this->translate('delete'), array('class' => 'smoothbox',
                                        ))
                                        ?>
                                        
                                        |
                                       
                                       <?php     echo $this->htmlLink(array('route' => 'ynevent_admin_default', 'action' => 'add-category', 'parent_id' => $category->category_id), $this->translate('add sub-category'), array(
                                                'class' => 'smoothbox',
                                            ));
                                            ?>
                                            |
                                       
                                        <?php echo $this->htmlLink(array('route' => 'ynevent_admin_default', 'action' => 'categories', 'parent_id' => $category->category_id), $this->translate('view sub-category'), array(
                                        )) ?>
                                       
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <br/>
                    <div class="tip">
                        <span><?php echo $this->translate("There are currently no categories.") ?></span>
                    </div>
                <?php endif; ?>
                <br/>

                <?php
               
                if(!isset($this->category))
                {
                    $parentId = 0;
                }
                else{
                    $parentId = $this->category->category_id;
                }
                
                echo $this->htmlLink(array('route' => 'ynevent_admin_default', 'action' => 'add-category', 'parent_id' => $parentId), $this->translate('Add New Category'), array(
                    'class' => 'smoothbox buttonlink',
                    'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);'))
                ?>

            </div>
        </form>
    </div>
</div>
