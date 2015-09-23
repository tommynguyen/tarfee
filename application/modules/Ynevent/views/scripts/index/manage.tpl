<?php $repeateType = array('','1'=> $this->translate('daily'),'7'=> $this->translate('weekly'),'30'=> $this->translate('monthly'), '99'=> $this->translate('specify'))?>

<script type="text/javascript">
    window.addEvent('domready', function()
    {
	    $$('.ynevents_options select').each(
	    function(el)
	    {       
		    el.removeEvents().addEvent('change', function()
		    {
		    
		    var event_id = el.get('id');
		    var remain_time = el.value;
		                        
		    (new Request.JSON({
		   
		    'method' : 'get',
		    'url' : '<?php echo $this->url(array('module' => 'ynevent', 'controller' => 'index', 'action' => 'remind'), 'default', true) ?>',
		        'data' : {
		        'format' : 'json',
		        'remain_time' : remain_time,
		        'event_id': event_id
	    		}                  
					})).send();
				});
			});
	});
  en4.core.runonce.add(function()
  {
   if($('text'))
    {
      new OverText($('text'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }
  });
</script>

<div class='generic_layout_container layout_middle'>
    <div class='generic_layout_container layout_ynevent_my_manage'>
    <h3><?php echo $this->translate("My Events")?></h3>
	<div class="ynevent-action-view-method ynevent-my-event-manage-btn-top ynclearfix">
	  <div class="ynevent_home_page_list_content" rel="map_view">
        	<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
        	<a href="<?php echo $this -> url(array('action' => 'calendar'), 'event_general', true);?>"  class="ynevent_home_page_list_content_icon tab_icon_calendar"></a>
      </div>
      <div class="ynevent_home_page_list_content" rel="map_view">
        	<div class="ynevent_home_page_list_content_tooltip acitve"><?php echo $this->translate('List View')?></div>
        	<a href="javascript:;" class="ynevent_home_page_list_content_icon tab_icon_list_view active"></a>
      </div>
   </div>
	<?php if (count($this->paginator) > 0): ?>
    <ul class='ynevents_browse events_browse'>
        <?php foreach ($this->paginator as $event): if(!$event->getIdentity()){continue;}?>
        <li>
            <div class="ynevents_photo events_photo">
                <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
            </div>
            <div class="ynevents_options events_options">
                <?php if ($this->viewer() && $event->isOwner($this->viewer())): ?>
                <?php
                echo $this->htmlLink(array('route' => 'event_specific', 'action' => 'edit', 'event_id' => $event->getIdentity()), $this->translate('Edit Event'), array(
                'class' => 'buttonlink icon_event_edit'
                ))
                ?>
                <?php
                echo $this->htmlLink(array('route' => 'default', 'module' => 'event', 'controller' => 'event', 'action' => 'delete', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Event'), array(
                'class' => 'buttonlink smoothbox icon_event_delete'
                ));
                ?>
                <?php endif; ?>
                <?php if ($this->viewer() && !$event->membership()->isMember($this->viewer(), null)): ?>
                <?php
                echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'join', 'event_id' => $event->getIdentity()), $this->translate('Join Event'), array(
                'class' => 'buttonlink smoothbox icon_event_join'
                ))
                ?>
                <?php elseif ($this->viewer() && $event->membership()->isMember($this->viewer()) && !$event->isOwner($this->viewer())): ?>
                <?php
                echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'leave', 'event_id' => $event->getIdentity()), $this->translate('Leave Event'), array(
                'class' => 'buttonlink smoothbox icon_event_leave'
                ))
                ?>
                <?php endif; ?>
                <?php
                $date = date('y-m-d H:m:s');
                $start = $event->starttime;
                if (strtotime($start) > strtotime($date)):?>
                	<div class="ynevent_remind">
                	<?php echo $this->partial('_remind_dropdown.tpl', array('event' => $event));?>
                	</div>
                <?php endif;
                ?>
								

            </div>
            <div class="ynevents_info events_info">
                <div class="ynevents_title events_title">
                    <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
                </div>
                <div class="ynevents_members events_members">
                    <?php echo $this->locale()->toDateTime($event->starttime) ?>
                </div>
                <div class="ynevents_members events_members">
                    <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?>
                    <?php echo $this->translate('led by') ?>
                    <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
                </div>
                <?php if ($event->repeat_group > 0) : ?>
		          <div class="ynevents_members ynevents_title_repeat_type">
		          		<?php
		          		$nextEvent = $event->getNextEvent();
		          		if (is_object($nextEvent)) : ?>
		          			<img class="ynevent_arrow" src="application/modules/Ynevent/externals/images/types/event.png" style="margin-top: 3px;"/>
		          			<a href="<?php echo $nextEvent->getHref();?>" title="<?php echo $this->translate('go to next event'); ?>" target="blank" style="margin-right: 10px;"><?php echo $this->translate('Next event'); ?></a>
						<?php endif;?>   
						<span title="<?php echo $this->translate('repeat type');?>"><?php echo $repeateType[$event->repeat_type]; ?></span>       	
		          </div>
		          <?php endif;?>
                <div class="ynevents_desc events_desc">
                    <?php
            			if(trim($event->brief_description) != "")
							echo $event->brief_description;
						else 
            				echo $event->getDescription() ?>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($this->paginator->count() > 1): ?>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
    'query' => array('view' => $this->view, 'text' => $this->text)
    ));
    ?>
    <?php endif; ?>
	<?php else: ?>
<div class="tip">
    <span>
    	<?php if ($this->view == '2'): ?>
    		<?php echo $this->translate('You have not led any events yet.'); ?>
    	<?php else: ?>
        	<?php echo $this->translate('You have not joined any events yet.'); ?>
        <?php endif;?>
        
        <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'event_general') . '">', '</a>') ?>
        <?php endif; ?>
    </span>
</div>
<?php endif; ?>
</div>


</div>


