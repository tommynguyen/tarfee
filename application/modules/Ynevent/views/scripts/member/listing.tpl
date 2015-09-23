<?php ?>

<a id="ynevent_profile_members_anchor"></a>

<script type="text/javascript">
    var waiting = '<?php echo $this->waiting ?>';
    en4.core.runonce.add(function() {
        $$('.ynevent_members_filter select').each(function(el){
            el.removeEvents().addEvent('change', function(){                    
                var filter = el.value;
                var url = '<?php echo $this->url(array('controller'=>'member', 'action' => 'listing', 'event_id' => $this->event->getIdentity()),'event_extended');?>';
                en4.core.request.send(new Request.HTML({
                    'url' : url,
                    'data' : {
                        'format' : 'html',
                        'subject' : en4.core.subject.guid,
                        'filter':filter
                    }
                }), {
                    'element' : $('ynevent_profile_members_anchor').getParent()
                });
               
            })
        });
    });
    
</script>

<?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
    <script type="text/javascript">
        var showWaitingMembers = function() {
            //var url = '<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action' => 'profile-members', 'subject' => $this->subject()->getGuid(), 'format' => 'html'), 'default', true) ?>';
            var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
            en4.core.request.send(new Request.HTML({
                'url' : url,
                'data' : {
                    'format'  : 'html',
                    'subject' : en4.core.subject.guid,
                    'waiting' : true
                }
            }), {
                'element' : $('ynevent_profile_members_anchor').getParent()
            });
        }
        
        var showRegisteredMembers = function() {
    //var url = '<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action' => 'profile-members', 'subject' => $this->subject()->getGuid(), 'format' => 'html'), 'default', true) ?>';
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format'  : 'html',
        'subject' : en4.core.subject.guid,
      }
    }), {
      'element' : $('ynevent_profile_members_anchor').getParent()
    });
  }                             
                                     
                                     
                                     
    </script>
<?php endif; ?>

<?php if (!$this->waiting): ?>
    <div class="ynevent_members_info">
        <div class="ynevent_members_filter">
            <select id="ynevent_members_filter">
                <option value="-1" ><?php echo $this->translate("All"); ?></option>
                <option value="2" <?php if ($this->filter == "2"): ?>selected ="selected"<?php endif ?>><?php echo $this->translate("Attending"); ?></option>
                <option value="1" <?php if ($this->filter == "1"): ?>selected ="selected"<?php endif ?>><?php echo $this->translate("Maybe Attending"); ?></option>
                <option value="0" <?php if ($this->filter == "0"): ?>selected ="selected"<?php endif ?>><?php echo $this->translate("Not Attending"); ?></option>
            </select>
        </div>
        <div class="ynevent_members_total">
            <?php if ('' == $this->search): ?>
            	<?php if (($this->filter == '0' || $this->filter == '1' || $this->filter == '2') && (count($this->members) <= 0)) : ?>
                	<?php echo $this->translate("This event has 0 guests that match your search criteria."); ?>
                <?php else :?>
                	<?php echo $this->translate(array('This event has %1$s guest.', 'This event has %1$s guests.', count($this->members)), $this->locale()->toNumber(count($this->members))) ?>
                <?php endif;?>
            <?php else: ?>
                <?php echo $this->translate(array('This event has %1$s guest that matched the query "%2$s".', 'This event has %1$s guests that matched the query "%2$s".', count($this->members)), $this->locale()->toNumber(count($this->members)), $this->search) ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
            <div class="ynevent_members_total">
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('See Waiting'), array('onclick' => 'showWaitingMembers(); return false;')) ?>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="ynevent_members_info">
        <div class="ynevent_members_total">
            <?php echo $this->htmlLink('javascript:void(0);', 
           					$this->translate(
           					array(
           					'This event has %1$s member waiting approval or waiting for a invite response.', 
           					'This event has %1$s members waiting approval or waiting for a invite response.', 
           					count($this->members)),
           					$this->locale()->toNumber(count($this->members))), 
           					array('onclick' => 'showRegisteredMembers(); return false;'))  ?> 	
        </div>
    </div>
<?php endif; ?>

<?php if (count($this->members) > 0): ?>
    <ul class='ynevent_members'>
        <?php
        foreach ($this->members as $member):
            if (!empty($member->resource_id)) {
                $memberInfo = $member;
                $member = $this->item('user', $memberInfo->user_id);
            } else {
                $memberInfo = $this->event->membership()->getMemberInfo($member);
            }
            ?>

            <li id="event_member_<?php echo $member->getIdentity() ?>">

                <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'ynevent_members_icon')) ?>
                <div class='ynevent_members_options'>

                    <?php // Remove/Promote/Demote member  ?>
                    <?php if ($this->event->isOwner($this->viewer())): ?>

                        <?php if (!$this->event->isOwner($member) && $memberInfo->active == true): ?>
                            <?php
                            echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Remove Member'), array(
                                'class' => 'buttonlink smoothbox icon_friend_remove'
                            ))
                            ?>
                        <?php endif; ?>
                        <?php if ($memberInfo->active == false && $memberInfo->resource_approved == false): ?>
                            <?php
                            echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Approve Request'), array(
                                'class' => 'buttonlink smoothbox icon_event_accept'
                            ))
                            ?>
                            <?php
                            echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Reject Request'), array(
                                'class' => 'buttonlink smoothbox icon_event_reject'
                            ))
                            ?>
                        <?php endif; ?>
                        <?php if ($memberInfo->active == false && $memberInfo->resource_approved == true): ?>
                            <?php
                            echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'cancel', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Cancel Invite'), array(
                                'class' => 'buttonlink smoothbox icon_event_cancel'
                            ))
                            ?>
                        <?php endif; ?>


                    <?php endif; ?>
                </div>
                <div class='ynevent_members_body'>
                    <div>
                        <span class='ynevent_members_status'>
                            <?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?>

                            <?php // Titles  ?>
                            <?php if ($this->event->getParent()->getGuid() == ($member->getGuid())): ?>
                                <?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner'))) ?>
                            <?php endif; ?>
                        </span>                       
                        <span>
                            <?php echo $member->status; ?>
                        </span>
                    </div>
                    <div class="ynevent_members_rsvp">
                        <?php if ($memberInfo->rsvp == 0): ?>
                            <?php echo $this->translate('Not Attending') ?>
                        <?php elseif ($memberInfo->rsvp == 1): ?>
                            <?php echo $this->translate('Maybe Attending') ?>
                        <?php elseif ($memberInfo->rsvp == 2): ?>
                            <?php echo $this->translate('Attending') ?>
                        <?php else: ?>
                            <?php echo $this->translate('Awaiting Reply') ?>
                        <?php endif; ?>
                    </div>
                </div>

            </li>

        <?php endforeach; ?>

    </ul>


    

<?php endif; ?>