<div style="width: 600px; height: 400px">
	<h3><?php echo $this->translate('Members who fans of %s', $this-> club -> gettitle())?></h3>
	<?php $users =  $this -> club -> membership() ->getMembers();?>
	<?php if (count($users)) :?>
	<ul class="user-list user-items" style="height: 330px; overflow: auto">
		<?php foreach ($users as $user):?>
		<li class="user-item">
			<div class="user-photo"><?php echo $this->itemPhoto($user, 'thumb.icon')?></div>
			<div class="user-title">
				<?php echo $this->htmlLink($user->getHref(), $this -> string() -> truncate($user->getTitle(), 100), array('title' => $user->getTitle(), 'target' => '_parent', 'class' => '', 'rel'=> 'user'.' '.$user->getIdentity()));?>
			</div>
		</li>
		<?php endforeach;?>
	</ul>
	<?php else: ?>
	<div class="tip">
		<span><?php echo $this->translate('No members found!')?></span>
	</div>
	<?php endif;?>
	<button style="margin-top: 15px" type="button" onclick="parent.Smoothbox.close()"><?php echo $this->translate('Close')?></button>
</div>