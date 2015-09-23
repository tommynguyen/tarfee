<h2><?php echo $this->translate("Referral Program Settings") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Users Generated Codes') ?></h3>

<br />
<div class="admin_search">
    <?php //echo $this->form->render($this);?>
</div>

<div class="tip">
	<span><?php echo $this -> translate("This page list all users who generated codes.");?></span>
</div>

<?php if( count($this->paginator) ): ?>
<table class='admin_table ynsocial_table' style="width: 100%">
  <thead>
    <tr>
    	<th><?php echo $this->translate('User') ?></th>
    	<th><?php echo $this->translate('Total Code') ?></th>
    	<th><?php echo $this->translate('Used Code') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <?php $user = Engine_Api::_() -> getItem('user', $item -> user_id) ?>
      <?php if($user -> getIdentity()) :?>
      	<!-- get code -->
      		<?php
      			$inviteTable = Engine_Api::_() -> getDbTable('invites', 'invite');
		    	$select = $inviteTable->select()
			      ->from($inviteTable->info('name'))
			      ->where('user_id = ?', $user -> getIdentity());
				
				$select = $select -> where('new_user_id = 0');
				$availableCodes = $inviteTable -> fetchAll($select);
				
				$select = $inviteTable->select()
			      ->from($inviteTable->info('name'))
			      ->where('user_id = ?', $user -> getIdentity());
				$select = $select -> where('new_user_id <> 0');
				$usedCodes = $inviteTable -> fetchAll($select);
			?>
  		<!-- end get code -->
	      <tr>
	      	<td>
	      		<?php echo $user;?>
	      	</td>
	      	<td>
	      		<?php echo (count($usedCodes) + count($availableCodes)) ;?>
	      	</td>
	      	<td>
	      		<?php if(count($usedCodes)) :?>
		      		<table class='admin_table ynsocial_table' style="width: 100%">
		      			<tr>
			      			<td>
		      					<?php echo $this -> translate("Code");?>
		      				</td>	
		      				<td>
		      					 <?php echo $this -> translate("User");?>
		      				</td>
	      				</tr>
			      		<?php foreach($usedCodes as $usedCode) :?>
			      			<?php $inviteCode = Engine_Api::_() -> invite() -> getRowCode($usedCode -> code); ?>
			      			<?php if($inviteCode) :?>
			      				<?php $used_user = Engine_Api::_() -> getItem('user', $inviteCode -> new_user_id);?>
			      				<tr>
				      				<td>
				      					<?php echo $usedCode -> code;?>
				      				</td>	
				      				<td>
				      					 <?php echo $used_user;?>
				      				</td>
			      				</tr>
		      				<?php endif;?>
			      		<?php endforeach;?>
		      		</table>
	      		<?php endif;?>
	      	</td>
	      </tr>
      <?php endif;?>
    <?php endforeach; ?>
  </tbody>
</table>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
	echo $this->translate(array('Total %s user', 'Total %s users', $total),$total);
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
      <?php echo $this->translate('There are no users.') ?>
    </span>
  </div>
<?php endif; ?>
