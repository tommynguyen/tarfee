<h2><?php echo $this->translate("Player Card Settings") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

  <div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
        <h3> <?php echo $this->translate("Manage Relation") ?> </h3>
       <i class="fa fa-plus"></i><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'manage-relation', 'action' => 'add'), $this->translate('Add New Relation'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'padding-left: 10px;'
	  )) ?>
		<br />	<br />
          <?php if(count($this->severities)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Relation") ?></th>
              <th><?php echo $this->translate("Actions") ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($this->severities as $type): ?>
                    <tr>
                      <td>
                          <span class="relation-category-collapse-nocontrol"></span>
                          <?php echo $type->title?>
                      </td>
                      <td>
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'manage-relation', 'action' => 'edit', 'id' =>$type->relation_id), $this->translate("edit"), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'manage-relation', 'action' => 'delete', 'id' =>$type->relation_id), $this->translate("delete"), array(
                          'class' => 'smoothbox',
                        )) ?>

                      </td>
                    </tr>                  
                   <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no reations.") ?></span>
      </div>
      <?php endif;?>
        <br/>
    </div>
    </form>
    </div>
  </div>
     