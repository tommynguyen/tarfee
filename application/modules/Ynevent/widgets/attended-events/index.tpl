<ul id="profile_events_<?php echo $this->identity?>" class="ynevents_profile_tab">
    <?php foreach( $this->paginator as $event ): ?>
    <li>
        <div class="ynevents_info">
            <div class="ynevents_title">
                <?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
            </div>
        </div>
        <div class="ynevents_time_place_rating">
            <div class="ynevents_time_place">
                <span>
                    <?php 
					$startDateObj = null;
					if (!is_null($event->starttime) && !empty($event->starttime)) 
					{
						$startDateObj = new Zend_Date(strtotime($event->starttime));	
					}
					if( $this->viewer() && $this->viewer()->getIdentity() ) {
						$tz = $this->viewer()->timezone;
						if (!is_null($startDateObj))
						{
							$startDateObj->setTimezone($tz);
						}
				    }
					if(!empty($startDateObj)) :?>
						<?php echo (!is_null($startDateObj)) ?  date('d M, Y H:i', $startDateObj -> getTimestamp()) : ''; ?>
					<?php endif; ?>
                </span>
            </div>
        </div>
        <?php 
        if($this -> viewer() -> getIdentity()):
            ?>
            <div class="ynevents_button" id = "ynevent_rsvp_attend_<?php echo $event -> getIdentity()?>">
               <?php echo $this -> action('list-rsvp', 'widget', 'ynevent', array( 'id' => $event -> getIdentity(), 'widget' => 'changeRsvpAttend'));?>
            </div>
        <?php endif;?>
    </li>
    <?php endforeach; ?>
</ul>
<?php if($this->paginator->getTotalItemCount() > $this->itemCountPerPage):?>
  <?php echo $this->htmlLink($this->url(array('action' => 'manage'), 'event_general'), $this -> translate("View all"), array('class' => 'icon_event_viewall')) ?>
<?php endif;?>

<?php if($this -> viewer() -> getIdentity()):?>
<script type="text/javascript">
 var tempChange = 0;
   var changeRsvpAttend = function(id, option)
   {
        if (tempChange == 0) 
        {
            tempChange = 1;
            if ($('rsvp_option_changeRsvpAttend_' + id + '_' + option)) {
                $('rsvp_option_changeRsvpAttend_' + id + '_' + option).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
            }
            var url = en4.core.baseUrl + 'ynevent/widget/rsvp';
            en4.core.request.send(new Request.JSON({
                url : url,
                data : {
                    format : 'json',
                    id : id,
                    widget: 'changeRsvpAttend',
                    option_id: option
                },
                onComplete : function(e) {
                    tempChange = 0;
                }
            }), {
                'element' : $('ynevent_rsvp_attend_' + id)
            });
        }
   }
</script>
<?php endif;?>