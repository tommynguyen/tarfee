<div style="width: 600px; height: 400px">
	<h3><?php echo $this->translate('Players who %s has eyed on', $this->user)?></h3>
	<?php $players = $this->user->getEyeOns();?>
	<?php if (count($players)) :?>
	<ul class="user-list user-items" style="height: 330px; overflow: auto">
		<?php foreach ($players as $player):?>
		<li class="user-item">
			<div class="user-photo"><?php echo $this->itemPhoto($player, 'thumb.icon')?></div>
			<div class="user-title">
				<?php echo $this->htmlLink($player->getHref(), $this -> string() -> truncate($player->getTitle(), 100), array('title' => $player->getTitle(), 'target' => '_parent', 'class' => ''));?>
		</li>
		<?php endforeach;?>
	</ul>
	<?php else: ?>
	<div class="tip">
		<span><?php echo $this->translate('No players found!')?></span>
	</div>
	<?php endif;?>
	<button style="margin-top: 15px" type="button" onclick="parent.Smoothbox.close()"><?php echo $this->translate('Close')?></button>
</div>