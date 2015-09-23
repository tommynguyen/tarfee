<h2><?php echo $this->translate("Profile Section Settings") ?></h2>

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
        <h3> <?php echo $this->translate("Manage Services") ?> </h3>
     	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'services', 'action' => 'add'), $this->translate('Add New Service'), array(
      	'class' => 'smoothbox',
	  	)) ?>
		<br />	<br />
          <?php if(count($this->services)>0):?>
         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Service") ?></th>
              <th><?php echo $this->translate("Actions") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->services as $service): ?>
                    <tr>
                      <td>
                          <?php echo $service->title?>
                      </td>
                      <td>
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'services', 'action' => 'edit', 'id' =>$service->service_id), $this->translate("edit"), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'services', 'action' => 'delete', 'id' =>$service->service_id), $this->translate("delete"), array(
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
      <span><?php echo $this->translate("There are currently no services.") ?></span>
      </div>
      <?php endif;?>
        <br/>
    </div>
    </form>
    </div>
  </div>
     