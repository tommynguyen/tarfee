<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render()?>
</div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
        <form class="global_form">
            <div>
                <p class="form-description"><?php echo $this->translate("REASONS_DESCRIPTION") ?></p>
                <?php if(count($this->reasons)>0):?>
                <table class='admin_table' id="manage_reason">
                    <thead>
                        <tr>
                            <th><?php echo $this->translate("ID") ?></th>
                            <th><?php echo $this->translate("Description") ?></th>
                            <th><?php echo $this->translate("Options") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($this->reasons as $reason): ?>
                        <tr>
                            <td><?php echo $reason->reason_id?></td>
                            <td><?php echo $reason->description?></td>
                            <td>
                              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'slprofileverify', 'controller' => 'reason', 'action' => 'edit-reason', 'id' =>$reason->reason_id), $this->translate("Edit"), array(
                                'class' => 'smoothbox',
                              )) ?>
                              |
                              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'slprofileverify', 'controller' => 'reason', 'action' => 'delete-reason', 'id' =>$reason->reason_id), $this->translate("Delete"), array(
                                'class' => 'smoothbox',
                              )) ?>
                            </td>         
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else:?>
                <div class="tip">
                    <span><?php echo $this->translate("There are currently no reasons!") ?></span>
                </div>
                <?php endif;?>
                <div>
                    <?php 
                        echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'slprofileverify', 'controller' => 'reason', 'action' => 'add-reason'), 
                            $this->translate("Add New Reason"), 
                            array('class' => 'smoothbox buttonlink','style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);'
                        ));
                    ?>
                </div>
            </div>
        </form>
    </div>
</div>
