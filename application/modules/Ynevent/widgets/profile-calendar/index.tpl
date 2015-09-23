<?php 
$this->headTranslate(array(
		"Please choose the event(s)", 
		"Please choose event(s) to join", 
		"Please choose event(s) to leave", 
		"You have successfully RSVPed to this event."
	));
?>

<a id="ynevent_profile_calender_anchor"></a>
<div class="ynevent_profile_calender_btn_view">
	<?php
	echo $this->htmlLink(array('route' => 'event_profile', 'id' => $this->event->getIdentity(), 'tab' => $this->identity, 'view' => 'calendar'), '', array(
		'class' => (($this->viewMethod == 'calendar') ? 'tab_icon_calendar active' : 'tab_icon_calendar') . ' ynevent_calendar_view'
	));

	echo $this->htmlLink(array('route' => 'event_profile', 'id' => $this->event->getIdentity(), 'tab' => $this->identity, 'view' => 'list'), '', array(
		'class' => (($this->viewMethod == 'list') ? 'tab_icon_list_view active' : 'tab_icon_list_view') . ' ynevent_calendar_view'
	));
	?>
</div>

<?php //LIST VIEW ?>
<?php if ($this -> viewMethod == 'list'):?>
<div id="ynevent_profile_calendar_select_deselect">
	<a class="buttonlink" href="javascript:void(0);" onclick="selectAll(1);"><?php echo $this->translate("Select All");?></a>
	<a class="buttonlink" href="javascript:void(0);" onclick="selectAll(0);"><?php echo $this->translate("De-select All");?></a>
</div>
<script type="text/javascript">
	var count = 0;
	en4.core.runonce.add(function()
	{
		$$("select[name='ynevent_calendar_event_select_action']").each(function(el){
		    el.removeEvents().addEvent('change', function(){   
		    	var rsvp = el.value;
		        var event_id = el.get("event_id");
		        var event_guid = el.get("event_guid");

				new Request.JSON({
	                url: '<?php echo $this->url(array('module' => 'ynevent', 'controller' => 'widget', 'action' => 'profile-rsvp'), 'default', true); ?>' + '/subject/' + event_guid,
	                method: 'post',
	                data : {
	                     format: 'json',
	                     'event_id': event_id,
	                     'option_id' : rsvp
	                },
	     
	                onComplete: function(responseJSON, responseText)
	                {
						alert(en4.core.language.translate("You have successfully RSVPed to this event."));                     
	                }
	           }).send();
		    	
		    });
		});
		$$('div.ynevent_calendar_event_checkbox input[type=checkbox]').addEvent('click', function()
       {
			if(this.checked == true)
			{
				count ++;
			}
			else
			{
				count --;
			}
			$('ynevent_calendar_event_select').childNodes[1].text = "<?php echo $this->translate('With Selected'); ?>" + " (" + count +")";
			$('ynevent_calendar_event_select').childNodes[1].label = "<?php echo $this->translate('With Selected'); ?>" + " (" + count +")";
		});
	});

	var repeatedEventPage = Number('<?php echo $this->events->getCurrentPageNumber() ?>');
	
	var switchView = function(view){
		
		var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
        en4.core.request.send(new Request.HTML({
            'url' : url,
            'data' : {
                'format' : 'html',
                'subject' : en4.core.subject.guid,
                'page' : repeatedEventPage,
                'viewMethod' : view
            }
        }), {
            'element' : jQuery('#ynevent_profile_calender_anchor').getParent()
        });
	};
	
	var selectAll = function(doCheck){
		checkboxList = $$("input[name='ynevent_cb_repeated_event[]']");
		if (checkboxList.length > 0)
			checkboxList.each(function(el) { el.checked = doCheck; });
		if(doCheck == 1)
		{
			count = checkboxList.length;
		}
		else
		{
			count = 0;
		}
		$('ynevent_calendar_event_select').childNodes[1].text = "<?php echo $this->translate('With Selected'); ?>" + " (" + count +")";
		$('ynevent_calendar_event_select').childNodes[1].label = "<?php echo $this->translate('With Selected'); ?>" + " (" + count +")";
	};
    
    var paginateRepeatedEvent = function(page) 
    {
        var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
        en4.core.request.send(new Request.HTML({
            'url' : url,
            'data' : {
                'format' : 'html',
                'subject' : en4.core.subject.guid,
                'page' : page,
            },
             onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
             {
             	 $('ynevent_profile_calender_anchor').getParent().innerHTML = responseHTML;
		         $$('.calendar_pages').each(function(el){el.removeClass('active')});
    			 $('page_' + page).addClass('active');
		     },
        }), 
        {
        });
       
    };

    var joinEvent = function()
    {
        var checkboxList = $$("input[name='ynevent_cb_repeated_event[]']:checked");
        if (checkboxList.length == 0)
        {
            alert(en4.core.language.translate("Please choose the event(s)"));
            return;
        }

        var joinedIds = new Array();
        var notJoinedIds = new Array();
        checkboxList.each(function(cb){
            if(cb.get("status") == '1')
            	joinedIds.push(cb.get("value"));
            else if(cb.get("status") == '2')
            	notJoinedIds.push(cb.get("value"));
        });
        
        var status = $("ynevent_calendar_event_select").get("value");
        if (status == '1')//join action
        {
            if (notJoinedIds.length == 0)
            {
            	alert(en4.core.language.translate("Please choose event(s) to join")); return;
            }
                
        	strNotJoinedIds = notJoinedIds.join();
			Smoothbox.open("<?php echo $this->url(array('controller'=>'member', 'action' => 'join-repeated-events', 'event_id' => $this->event->getIdentity(), 'tab' => $this->identity),'event_extended');?>" + '/ids/' + strNotJoinedIds);
		}
        else if (status == '2')//leave action
        {
            if (joinedIds.length == 0)
            {
            	alert(en4.core.language.translate("Please choose event(s) to leave")); return;
            }
        	strJoinedId = joinedIds.join();
        	Smoothbox.open("<?php echo $this->url(array('controller'=>'member', 'action' => 'leave-repeated-events', 'event_id' => $this->event->getIdentity(), 'tab' => $this->identity),'event_extended');?>" + '/ids/' + strJoinedId);
        }
	};
    
</script>

<?php $hasInvitationEvent = false; ?>
<?php foreach ($this->events as $event) :
	if ($event->approval)
	{
		$hasInvitationEvent = true;	
	}
	$memberInfo = $event->membership()->getMemberInfo($this->viewer);
	// Convert the dates for the viewer
	$startDateObject = new Zend_Date(strtotime($event->starttime));
	$endDateObject = new Zend_Date(strtotime($event->endtime));
	if( $this->viewer && $this->viewer->getIdentity() ) 
	{
		$tz = $this->viewer->timezone;
		$startDateObject->setTimezone($tz);
		$endDateObject->setTimezone($tz);
	}
?>
<div class="ynevent_calendar_wrap_item">
	<?php if ($this->viewer()->getIdentity()):?>
		<?php $joined = ($memberInfo !== null) && ($memberInfo -> active); ?>
		<?php if ($joined):?>
			<div class="ynevent_calendar_action">
				<select id="ynevent_calendar_event_select_action" name="ynevent_calendar_event_select_action" event_id="<?php echo $event->getIdentity();?>" event_guid="<?php echo $event->getGuid(); ?>">
		                <option value="2" <?php echo ($memberInfo->rsvp == '2') ? 'selected="true"' : ''; ?>><?php echo $this->translate("Attending"); ?></option>
		                <option value="1" <?php echo ($memberInfo->rsvp == '1') ? 'selected="true"' : ''; ?>><?php echo $this->translate("Maybe Attending"); ?></option>
		                <option value="0" <?php echo ($memberInfo->rsvp == '0') ? 'selected="true"' : ''; ?>><?php echo $this->translate("Not Attending"); ?></option>
				</select>
				
				<?php
					if (!$event->isOwner($this->viewer())) 
					{
						echo $this->htmlLink(array('route' => 'event_extended', 'controller'=>'member', 'action' => 'leave', 'event_id' => $event->getIdentity(), 'tab' => $this->identity), $this->translate('Leave Event'), array(
								'class' => 'buttonlink smoothbox icon_event_leave'
						));
					}
				 ?>
			</div>
		<?php else:?>
			<?php 
				$menu = new Ynevent_Plugin_Menus();
				$aJoinButton = $menu->renderEventAction($event);
			?>
			<?php if($aJoinButton && is_array($aJoinButton)):?>
				<div class="ynevent_calendar_action">
	                <?php if (count($aJoinButton) == '2'):?>
	                			<?php foreach ($aJoinButton as $subMenu):?>
	                			<div>
	                				<a href="<?php echo $this->url($subMenu['params'], $subMenu['route'], array());?>" class="buttonlink <?php echo $subMenu['class'];?>" style="background-image: url(<?php echo $subMenu['icon'];?>);">
				                		<?php echo $subMenu['label']; ?>
				                	</a>
				                </div>
	                			<?php endforeach;?>
					<?php else:?>
							<?php $aJoinButton['params']['tab'] = $this->identity; ?>	
		                	<a href="<?php echo $this->url($aJoinButton['params'], $aJoinButton['route'], array());?>" class="buttonlink <?php echo $aJoinButton['class'];?>" style="background-image: url(<?php echo $aJoinButton['icon'];?>);">
		                		<?php echo $aJoinButton['label']; ?>
		                	</a>
					<?php endif;?>   
				</div>	             
            <?php endif;?>
		<?php endif;?>
	<?php endif;?>
	
		<div class="ynevent_calendar_event_checkbox">
			<input type="checkbox" value="<?php echo $event->getIdentity();?>" status="<?php echo ($joined) ? "1" : "2"?>" name="ynevent_cb_repeated_event[]" />
		</div>
		<div class="events_photo ynevent_calendar_event_photo">
                <div class="date">                    
                    <strong><?php echo date("d", strtotime($this->locale()->toDate($startDateObject))); ?></strong>
                    <?php echo date("M", strtotime($this->locale()->toDate($startDateObject))); ?>
                </div>
                <?php echo $this->htmlLink($event->getHref(), '<span class="image-thumb" style="background-image: url('.$event->getPhotoUrl().');"></span>') ?>
         </div>
         <div class="ynevent_calendar_event_info">
         	<div>
         		<strong>
	         	<?php echo $this->translate('%1$s %2$s',
	            $this->locale()->toDate($startDateObject),
	            $this->locale()->toTime($startDateObject)
	          	) ?>
	          	-
	          	<?php echo $this->translate('%1$s %2$s',
	            $this->locale()->toDate($startDateObject),
	            $this->locale()->toTime($startDateObject)
	          	) ?>
	          	</strong>
          	</div>
         	<div>
         		<?php
         			$guestLabel = $this->translate(array('%s guest', '%s guests', $event->member_count),$this->locale()->toNumber($event->member_count));
	         		echo $this->htmlLink(array('route' => 'event_extended', 'controller'=>'member', 'action' => 'listing', 'event_id' => $event->getIdentity()), $guestLabel, array(
	         				'class' => 'buttonlink smoothbox activity_icon_ynevent_join'
	         		));
         		?>
         	</div>
         	<div>
         		<ul class='ynevent_calendar_members'>
         		<?php
         			$select = $event->membership()->getMembersObjectSelect();
         			$select->limit($this->maximumMember);
         			$userTable = Engine_Api::_()->getDbtable('users', 'user');
         			$members = $userTable->fetchAll($select);
         		?>
         		<?php foreach($members as $member):?>
         			<li id="event_member_<?php echo $member->getIdentity() ?>">
         				<?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'ynevent_members_icon')) ?>
         			</li>
         		<?php endforeach;?>
         		</ul>
         	</div>
         </div>
         <div style="clear: both;"></div>
	</div>
<?php endforeach;?>
<div class="layout_ynevent_profile_calendar_footer ynclearfix">
<?php
if ($this-> events -> count() > 1): ?>
    <div class="paginateRepeatedEvent">
        <?php if ($this->events->getCurrentPageNumber() > 1): ?>
            <div id="user_event_members_previous" class="paginator_previous">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                    'onclick' => 'paginateRepeatedEvent(repeatedEventPage - 1)',
                    'class' => 'buttonlink icon_previous',
                    'style' => '',
                ));
                ?>
            </div>
        <?php endif; ?>
        
		<?php if (count($this->pageRange)):?>
			<?php foreach ($this->pageRange as $page):?>
				<?php 
				echo $this->htmlLink('javascript:void(0);', $page, array(
                    'onclick' => 'paginateRepeatedEvent('.$page.')',
                    'class' => 'buttonlink calendar_pages'.(($page == 1)?' active':''),
                    'style' => '',
                    'id' => 'page_'.$page,
                ));
				?>
			<?php endforeach;?>
	    <?php endif;?>      
        
        <?php if ($this->events->getCurrentPageNumber() < $this-> events -> count()): ?>
            <div id="user_event_members_next" class="paginator_next">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                    'onclick' => 'paginateRepeatedEvent(repeatedEventPage + 1)',
                    'class' => 'buttonlink icon_next'
                ));
                ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php if (!($hasInvitationEvent)) : ?>	
	<?php if ( $this->event->getOwner()->getIdentity() != $this->viewer()->getIdentity() ): ?>
		<?php if ($this->viewer()->getIdentity() && $this -> events -> getTotalItemCount()):?>
		      	<div class="ynevent_calendar_event_choose">
		      		<select id="ynevent_calendar_event_select" name="ynevent_calendar_event_select">
		      			<option value="0"><?php echo $this->translate('With Selected').' (0)'; ?></option>
		                <option value="1"><?php echo $this->translate("Join"); ?></option>
		                <option value="2"><?php echo $this->translate("Leave"); ?></option>
					</select>
					<button onclick="joinEvent();"><?php echo $this->translate("Go");?></button>
		      	</div>
		      	<?php $showedMassOption = true;?>
		<?php endif;?>
	<?php endif;?>
<?php endif;?>
</div>
<?php if(!$showedMassOption): ?>
<script>
window.addEvent('domready', function(){
	$("ynevent_profile_calendar_select_deselect").set('html','');
	$("ynevent_profile_calendar_select_deselect").set("style","height: 20px");
	$$(".ynevent_calendar_event_checkbox").set('html','');
});
</script>	
<?php endif;?>

<?php //CALENDAR VIEW ?>
<?php else:?>

<?php
/* date settings */
$month = $this->month;
$year = $this->year;

/* select month control */
$select_month_control = '<select name="month" id="ynevent_month">';
for ($x = 1; $x <= 12; $x++) {
    $m = $this->translate(date('F', mktime(0, 0, 0, $x, 1, $year)));
    $select_month_control.= '<option value="' . $x . '"' . ($x != $month ? '' : ' selected="selected"') . '>' . $m . '</option>';
}
$select_month_control.= '</select>';

/* select year control */
$year_range = 7;
$select_year_control = '<select name="year" id="ynevent_year">';
for ($x = ($year - floor($year_range / 2)); $x <= ($year + floor($year_range / 2)); $x++) {
    $select_year_control.= '<option value="' . $x . '"' . ($x != $year ? '' : ' selected="selected"') . '>' . $x . '</option>';
}
$select_year_control.= '</select>';

/* hidden tab control */
$tab_control = "<input type='hidden' name='tab' value='{$this->identity}'>";

/* "next month" control */
$baseurl = $this->layout()->baseUrl;
$next_month_link = '<a href="' . $this->url(array('tab' => $this->identity, 'id' => $this->event->getIdentity()), 'event_profile') . '?' . 'month=' . ($month != 12 ? $month + 1 : 1) . '&amp;year=' . ($month != 12 ? $year : $year + 1) . '" class="control control-ynevent-next"><img class="ynevent_arrow" src="application/modules/Ynevent/externals/images/next_rtl_calendar.png" /></a>';


/* "previous month" control */
$previous_month_link = '<a href="' . $this->url(array('tab' => $this->identity, 'id' => $this->event->getIdentity()), 'event_profile') . '?' . 'month=' . ($month != 1 ? $month - 1 : 12) . '&amp;year=' . ($month != 1 ? $year : $year - 1) . '" class="control control-ynevent-prev"><img src="application/modules/Ynevent/externals/images/previous-ltr_calendar.png" /></a>';

/* bringing the controls together */
$label = $this->translate("Go");
$controls = '<form class="ynevent_mycalendar_form" method="get">' . $tab_control . $select_month_control . $select_year_control . '<button onclick="getData()" name="submit" value="Go">'.$label.'</button>' . $previous_month_link . '' . $next_month_link . ' </form>';

/* draws a calendar */
$events = $this->events;
$m =$this->translate( date('F',mktime(0,0,0,$month,1,$year)));
echo '<h3 style="padding-right:15px;">' . $m . ' ' . $year . '</h3>';
echo '<div id="ynevent_myCalendar">';
echo '<div class="mycalendar_controls">' . $controls . '</div>';
echo $this->calendar; //draw_calendar($month, $year, $events);
echo '</div>';
echo '<br /><br />';
?>
<?php endif;?>
