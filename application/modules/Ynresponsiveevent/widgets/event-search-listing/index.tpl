<div class="yntheme-event-container">
<?php if(!$this -> view_more):?>
    <script type="text/javascript">
      var eventPageCount = <?php echo sprintf('%d', $this->paginator->count()); ?>;
      var eventPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()); ?>;
      var url = '<?php echo $this->url(array_merge(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), $this -> formValues), 'default', true) ?>';  
      var loadMoreEvents = function() 
      {
        eventPage ++;
        new Request.HTML({
          'url' : url,
          'data' : {
            'format' : 'html',
            'page' : eventPage,
            'view_more' : true 
          },
          'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) 
          {
            $('ynresponsive_events_loading_main').setStyle('display', 'none');
            if( '' != responseHTML.trim() && eventPageCount > eventPage ) {
              $('ynresponsive_events_viewmore').setStyle('display', '');
            }
            $('event-listing-main').innerHTML += responseHTML;
          }
        }).send();
      };
      
    function setCookie(cname,cvalue,exdays)
    {
        var d = new Date();
        d.setTime(d.getTime()+(exdays*24*60*60*1000));
        var expires = "expires="+d.toGMTString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }
    
    function getCookie(cname)
    {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) 
          {
          var c = ca[i].trim();
          if (c.indexOf(name)==0) return c.substring(name.length,c.length);
        }
        return "";
    }
    </script>
    <div class="event-listing-choose-view">
        <span class="icon-list-mode" data-view="list"></span>
        <span class="icon-grid-mode" data-view="grid"></span>
    </div>
    <div class="event-listing-main-title"><?php echo $this -> translate("Events"); ?></div>
    <div class="event-listing-main-status">
    	<?php echo $this -> translate(array("%s event found", "%s events found", $this->paginator -> getTotalItemCount()),$this->paginator -> getTotalItemCount())?>
    	<?php if($this -> tagName):?>
			<?php echo " ".$this -> translate("by tag"). " ";?>"<strong><?php echo $this -> tagName?></strong>"
		<?php endif;?>
    </div>
	<div class="event-listing-main clearfix <?php if (!$this->viewer()->getIdentity()) echo 'no-action'; ?>" id="event-listing-main">
	<?php endif;?>
	<?php  
	if($this->paginator -> getTotalItemCount() > 0 ): ?>
    	
        <?php foreach($this -> paginator as $event):?>
            <div class="event-listing-item col-xs-6">
                <a href="<?php echo $event -> getHref();?>" title="<?php echo $event -> getTitle();?>" class="event-listing-title"><?php echo $event -> getTitle();?></a>
                <div class="event-listing-description">
                    <span class="event-listing-date">
                        <i class="ynicon-time-w"></i> 
                        <?php 
                        $start_time = strtotime($event -> starttime);
    					$oldTz = date_default_timezone_get();
    					if($this->viewer() && $this->viewer()->getIdentity())
    					{
    						date_default_timezone_set($this -> viewer() -> timezone);
    					}
    					else 
    					{
    						date_default_timezone_set( $this->locale() -> getTimezone());
    					}
                        echo date("M j, Y", $start_time); 
                        $select = $event->membership()->getMembersObjectSelect();
                        $select -> where('rsvp = 2');
                        $members = Zend_Paginator::factory($select);
                        $members -> setItemCountPerPage(1);
                        $total = $members -> getTotalItemCount();
                        foreach($members as $member){}?>
                    </span>
                    <?php if($event -> location):?>
                    <span class="event-listing-host"><i class="ynicon-location-w"></i> <?php echo $event -> location;?></span>
                    <?php endif;?>
                </div>  
                <a href="<?php echo $event -> getHref();?>" class="event-listing-image">
                	<?php $imgUrl = $event -> getPhotoUrl();
                	if(!$imgUrl)
                		$imgUrl = 'application/modules/Ynresponsiveevent/externals/images/nophoto_event_thumb_main.png';?>
                    <span class="event-listing-image-span" style="background-image: url('<?php echo $imgUrl;?>'); "></span>
                </a>
                <div class="event-listing-content"><?php echo $this -> string() -> truncate(strip_tags($event -> description),150);?></div>

    			<div class="event-listing-attending"><?php if($total == 1)
    			{
    				echo $this -> translate("%s is attending", $member);
    			}
    			else if($total > 1)
    			{
    				$link = $this->htmlLink(array('route' => 'ynresponsive_event', 'action' => 'attending', 'id' => $event->event_id), $this->translate('%s other', $total - 1), array('class' => 'smoothbox'));
    				echo $this -> translate("%1s and %2s are attending", $member, $link);
    			}?>
                </div>
                
                <div class="event-listing-action">
        			<?php if ($this->viewer()->getIdentity()):
        				if($this -> event_active == 'ynevent'): 
        					$followTable = Engine_Api::_()->getDbTable('follow','ynevent');
                  			$row = $followTable->getFollowEvent($event->getIdentity(),$this->viewer()->getIdentity());?>
                        	<a href="javascript:;" id="ynresponsive_follow_<?php echo $event->getIdentity();?>" class="" title="<?php echo $this -> translate("Follow this event")?>" onclick="<?php echo ($row->follow) ? "setFollow(0,".$event->getIdentity().")" : "setFollow(1, ".$event->getIdentity().")"; ?>"><?php echo ($row->follow) ? $this -> translate('Unfollow') : $this -> translate('Follow');?></a>
                    	<?php endif;?>
                        
                        <?php
                        $row = $event -> membership() -> getRow($this->viewer());
        				// Not yet associated at all
        				if (null === $row)
        				{
        					if ($event -> membership() -> isResourceApprovalRequired())
        					{
        						echo $this->htmlLink(array(
        							'route' => 'event_extended', 
        							'controller' => 'member',
        							'action' => 'request',
        							'event_id' => $event -> getIdentity()), 
        							$this->translate('Send request'),
        							array('class' => 'smoothbox'));
        					}
        					else 
        					{
        						echo $this->htmlLink(array(
        							'route' => 'event_extended', 
        							'controller' => 'member',
        							'action' => 'join',
        							'event_id' => $event -> getIdentity()), 
        							$this->translate('Join'),
        							array('class' => 'smoothbox'));
        				      }
        				}
        			    // Full member
        			    // @todo consider owner
        			    else if( $row->active ) 
        			    {
        			      if( !$event->isOwner($this->viewer()) ) 
        			      {
        			      	echo $this->htmlLink(array(
        						'route' => 'event_extended', 
        						'controller' => 'member',
        						'action' => 'leave',
        						'event_id' => $event -> getIdentity()), 
        						$this->translate('Leave'),
        						array('class' => 'smoothbox'));
        			      } 
        			    } 
        			    else if( !$row->resource_approved && $row->user_approved ) 
        			    {
        			    	echo $this->htmlLink(array(
        						'route' => 'event_extended', 
        						'controller' => 'member',
        						'action' => 'cancel',
        						'event_id' => $event -> getIdentity()), 
        						$this->translate('Cancel request'),
        						array('class' => 'smoothbox'));
        			    } 
        			    else if( !$row->user_approved && $row->resource_approved ) 
        			    {
                            echo '<div>';
							echo "<a class = 'confirm_invite' id = 'confirm_invite' href = 'javascript:;'>".$this -> translate("Confirm invite")."</a>
							<span id = 'confirm_dropdown'>";
        			    	echo $this->htmlLink(array(
        						'route' => 'event_extended', 
        						'controller' => 'member',
        						'action' => 'accept',
        						'event_id' => $event -> getIdentity()), 
        						$this->translate('Accept invite'),
        						array('class' => 'smoothbox', 'id' => 'accept_request'));
        					echo $this->htmlLink(array(
        						'route' => 'event_extended', 
        						'controller' => 'member',
        						'action' => 'reject',
        						'event_id' => $event -> getIdentity()), 
        						$this->translate('Ignore invite'),
        						array('class' => 'smoothbox', 'id' => 'ignore_request'));
        					echo "</span>";
                            echo '</div>';
        			    }
                        ?>                    
                    <?php endif;?>
                </div>
            </div>
    	<?php endforeach;?>
        </div>
	<?php else: 
	if(!$this -> view_more):?>
	  <div class="tip">
		    <span>
		    <?php if ($this->is_search): ?>
		    	<?php echo $this->translate('There were no events found matching your search criteria.') ?>
		    <?php else: ?>
			      <?php echo $this->translate('Nobody has created an event yet.') ?>
			      <?php if( $this->canCreate ): ?>
			        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action'=>'create'), 'event_general').'">', '</a>'); ?>
			      <?php endif; ?>
			    <?php endif; ?>
		    </span>
	  </div>
  	<?php endif; 
   endif;?>
</div>

<?php if(!$this -> view_more):?>
	<div class="yntheme-event-container-viewmore">
		  <div class="ynresponsive_event_options">
          
	      <?php if( $this->paginator->getTotalItemCount() > 10 ): ?>
	        <div class="ynresponsive_events_viewmore" id="ynresponsive_events_viewmore">
	          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
	            'id' => 'ynresponsive_events_viewmore_link',
	            'class' => 'buttonlink icon_viewmore',
	            'onClick' => 'loadMoreClick()'
	          )) ?>
	        </div>
	      <?php endif; ?>
          
	      <div class="ynresponsive_events_viewmore" id="ynresponsive_events_loading_main" style="display: none;">
	        <img src='application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
	        <?php echo $this->translate("Loading ...") ?>
	      </div>
          
	    </div>
	</div>
 <?php endif; ?>
</div>

<script type="text/javascript">
	function setFollow(option_id, event_id)
	{
		new Request.JSON({
	        url: '<?php echo $this->url(array('action' => 'event-follow'), 'ynresponsive_event', true); ?>',
	        method: 'post',
	        data : {
	        	format: 'json',
	            'event_id': event_id,
	            'option_id' : option_id
	        },
	        onComplete: function(responseJSON, responseText) {
	            if (option_id == '0')
	            {
	            	$("ynresponsive_follow_" + event_id).set("html", 'Follow');
	            	$("ynresponsive_follow_" + event_id).set("onclick", "setFollow(1,"+ event_id +")");
	            }
	            else if (option_id == '1')
	            {
	            	$("ynresponsive_follow_" + event_id).set("html", 'Unfollow');
	            	$("ynresponsive_follow_" + event_id).set("onclick", "setFollow(0),"+ event_id +")");
	            }
	            
	        }
	    }).send();
	}
	var loadMoreClick = function()
	{
	    $('ynresponsive_events_viewmore').setStyle('display', 'none');
	    $('ynresponsive_events_loading_main').setStyle('display', '');
	    loadMoreEvents();
    }
    
    /** set view cookie **/
    var cview_mode = getCookie("ynevent-theme-listing-view");
    if (cview_mode.length == 0) {
        cview_mode = '<?php echo $this -> view_mode; ?>';
    }
    
    if (cview_mode == 'list') {        
        jQuery('.icon-list-mode').addClass('active');
    } else {
        jQuery('.icon-grid-mode').addClass('active');
    }
    jQuery('#event-listing-main').addClass( cview_mode );
    /** end set view cookie **/
    
    jQuery('.event-listing-choose-view span').click(function()
    {
        jQuery('.event-listing-choose-view span').removeClass('active');
        jQuery(this).addClass('active');        
        
        var view_mode = jQuery(this).data('view');
        setCookie("ynevent-theme-listing-view", view_mode);
        
        jQuery('#event-listing-main').removeClass('list').removeClass('grid').addClass(view_mode);
        if(view_mode == 'grid')
        {
            if($('confirm_invite'))
        	   $('confirm_invite').style.display = 'inline-block';
        	jQuery('#confirm_dropdown').hide();
        }
        else
        {
        	jQuery('#confirm_invite').hide();
        	jQuery('#confirm_dropdown').show();
        }
    });
    
    jQuery('.confirm_invite').click(function()
    {
    	if($('confirm_dropdown').style.display == 'none')
    	{
    		jQuery('#confirm_dropdown').show();
    		jQuery('#confirm_invite').addClass('active');
    	}
    	else
    	{
    		jQuery('#confirm_dropdown').hide();
    		jQuery('#confirm_invite').removeClass('active');
    	}
    });
    window.addEvent('domready', function()
    {
    	<?php if($this -> view_mode == 'grid'):?>
    		jQuery('#confirm_invite').show();
        	jQuery('#confirm_dropdown').hide();
    	<?php else:?>
    		jQuery('#confirm_invite').hide();
        	jQuery('#confirm_dropdown').show();
    	<?php endif;?>
    });
</script>