<h2><?php echo $this->translate("Referral Program Settings") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Referral Codes') ?></h3>

<br />
<div class="admin_search">
    <?php //echo $this->form->render($this);?>
</div>

<?php if( count($this->paginator) ): ?>
<table class='admin_table ynsocial_table' style="width: 100%">
  <thead>
    <tr>
    	<th><?php echo $this->translate('Code') ?></th>
    	<th><?php echo $this->translate('Created By') ?></th>
      	<th><?php echo $this->translate('Creation Date') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
      	<td><?php echo $item -> code;?></td>
      	<td>
      		<?php $user = Engine_Api::_() -> getItem('user', $item -> user_id) ?>
      		<?php echo $user;?>
      	</td>
      	<td><?php echo $this->locale()->toDateTime($item->timestamp) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
	echo $this->translate(array('Total %s code', 'Total %s codes', $total),$total);
    echo '</p>';
}?>
<br/>
<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no codes.') ?>
    </span>
  </div>
<?php endif; ?>
