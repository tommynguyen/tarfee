<h2>
    <?php echo $this->translate('YouNet Feedback Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Status') ?></h3>

<div class="add_link">
<?php echo $this->htmlLink(
    array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'status', 'action' => 'create'),
    $this->translate('Add Status'), 
    array(
        'class' => 'buttonlink add_faq smoothbox',
    )) ?>
</div>
<?php if( count($this->paginator) ): ?>
<table class='admin_table'>
    <thead>
        <tr>
            <th><?php echo $this->translate("Status") ?></th>
            <th style="width: 10%"><?php echo $this->translate("Options") ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($this->paginator as $status): ?>
        <tr>
            <td><?php echo $status->title ?></td>
            <td>
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'status', 'action' => 'edit', 'id' => $status->getIdentity()),
                $this->translate('Edit'),
                array('class' => 'smoothbox')
            )?>
            <?php if ($status->getIdentity() != 1) : ?>
             | 
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynfeedback', 'controller' => 'status', 'action' => 'delete', 'id' => $status->getIdentity()),
                $this->translate('Delete'),
                array('class' => 'smoothbox')
            )?>
            <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>