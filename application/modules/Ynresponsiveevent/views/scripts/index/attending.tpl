<div class="global_form_popup">
	<h3><?php echo $this -> translate("Guests")?></h3>
	<div class="event_members_total">
        <?php echo $this->translate(array('This event has %1$s guest.', 'This event has %1$s guests.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
    </div>
	<ul class='event_members'>
    <?php foreach( $this->members as $member ):
      if( !empty($member->resource_id) ) {
        $memberInfo = $member;
        $member = $this->item('user', $memberInfo->user_id);
      } else {
        $memberInfo = $this->event->membership()->getMemberInfo($member);
      }
      ?>

      <li id="event_member_<?php echo $member->getIdentity() ?>">

        <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'event_members_icon')) ?>
        <div class='event_members_options'>

          <?php // Remove/Promote/Demote member ?>
          <?php if( $this->event->isOwner($this->viewer())): ?>

            <?php if( !$this->event->isOwner($member) && $memberInfo->active == true ): ?>
              <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Remove Member'), array(
                'class' => 'buttonlink smoothbox icon_friend_remove'
              )) ?>
            <?php endif; ?>
            <?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
              <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Approve Request'), array(
                'class' => 'buttonlink smoothbox icon_event_accept'
              )) ?>
              <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Reject Request'), array(
                'class' => 'buttonlink smoothbox icon_event_reject'
              )) ?>
            <?php endif; ?>
            <?php if( $memberInfo->active == false && $memberInfo->resource_approved == true ): ?>
              <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'cancel', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Cancel Invite'), array(
                'class' => 'buttonlink smoothbox icon_event_cancel'
              )) ?>
            <?php endif; ?>


          <?php endif; ?>
        </div>
        <div class='event_members_body'>
          <div>
            <span class='event_members_status'>
              <?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?>

              <?php // Titles ?>
              <?php if( $this->event->getParent()->getGuid() == ($member->getGuid())): ?>
                <?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner') )) ?>
              <?php endif; ?>

            </span>
            <span>
              <?php echo $member->status; ?>
            </span>
          </div>
          <div class="event_members_rsvp">
            <?php if( $memberInfo->rsvp == 0 ): ?>
              <?php echo $this->translate('Not Attending') ?>
            <?php elseif( $memberInfo->rsvp == 1 ): ?>
              <?php echo $this->translate('Maybe Attending') ?>
            <?php elseif( $memberInfo->rsvp == 2 ): ?>
              <?php echo $this->translate('Attending') ?>
            <?php else: ?>
              <?php echo $this->translate('Awaiting Reply') ?>
            <?php endif; ?>
          </div>
        </div>

      </li>

    <?php endforeach;?>

  </ul>
</div>