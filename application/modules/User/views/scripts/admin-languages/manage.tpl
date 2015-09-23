  <h2> <?php echo $this->translate("Manage Languages") ?> </h2>
  <div class='clear'>
    <div class='settings'>
    <form class="global_form">
      <div>
       <i class="fa fa-plus"></i><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'languages', 'action' => 'add'), $this->translate('Add New Language'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'padding-left: 10px;'
	  )) ?>
		<br />
          <?php if(count($this->languages)>0):?>

         <table style="padding-top: 5px;" class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Language") ?></th>
              <th><?php echo $this->translate("Actions") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->languages as $type): ?>
                    <tr>
                      <td>
                          <span class="user-languages-collapse-nocontrol"></span>
                          <?php echo $type->title?>
                      </td>
                      <td>
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'languages', 'action' => 'edit', 'id' =>$type->language_id), $this->translate("edit"), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'user', 'controller' => 'languages', 'action' => 'delete', 'id' =>$type->language_id), $this->translate("delete"), array(
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
      <span><?php echo $this->translate("There are currently no languages.") ?></span>
      </div>
      <?php endif;?>
        <br/>
    </div>
    </form>
    </div>
  </div>
     