<ul class="contactimporter_top_inviters">
  <?php foreach( $this->inviters as $inviter ): ?>
  	<?php $user = Engine_Api::_()->user()->getUser($inviter->inviter_id);?>
    <li>
      <div class="inviter_photo">
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'thumb')) ?>
      </div>
      <div class="inviter_info">
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle())?>
          <div>
          	<?php echo $this->translate(array("%s invitation", "%s invitations", $inviter->amount), $this->locale()->toNumber($inviter->amount))?>
          </div>
     </div>
     <div style="clear:both"></div>
    </li>
  <?php endforeach; ?>
</ul>