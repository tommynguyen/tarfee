<?php
/**
 * Younetco
 *
 * @category   Application_Extensions
 * @package    Ynevent
 * @copyright  Copyright 2014 Younetco
 * @author     LONGL
 */
?>
<script type="text/javascript">
var gl_setting_clicked = false;
var gl_invitation_proceed_clicked = false;

en4.core.runonce.add(function() 
{
	$('ynevent_widget_cover_settings').addEvent('click', function(e) {
		if (gl_setting_clicked == false)
		{
			gl_setting_clicked = true;
			$$(".ynevent-detail-setting").set("style", "");

			gl_invitation_proceed_clicked = false;
			$$(".ynevent-detail-request").set("style", "display: none;");
		}
		else
		{
			gl_setting_clicked = false;
			$$(".ynevent-detail-setting").set("style", "display: none;");
		}
    });

	invitation_proceed = $('ynevent_widget_cover_invitation_proceed');
	if (invitation_proceed !== null)
	{
		$('ynevent_widget_cover_invitation_proceed').addEvent('click', function(e) {
			if (gl_invitation_proceed_clicked == false)
			{
				gl_invitation_proceed_clicked = true;
				$$(".ynevent-detail-request").set("style", "");

				gl_setting_clicked = false;
				$$(".ynevent-detail-setting").set("style", "display: none;");
			}
			else
			{
				gl_invitation_proceed_clicked = false;
				$$(".ynevent-detail-request").set("style", "display: none;");
			}
			
	    });
	}
	
    
});
function setFollow(option_id)
{
	new Request.JSON({
        url: '<?php echo $this->url(array('module' => 'ynevent', 'controller' => 'widget', 'action' => 'profile-follow', 'subject' => $this->subject()->getGuid()), 'default', true); ?>',
        method: 'post',
        data : {
        	format: 'json',
            'event_id': <?php echo $this->subject()->event_id ?>,
            'option_id' : option_id
        },
        onComplete: function(responseJSON, responseText) {
            if (option_id == '0')
            {
            	$("ynevent_widget_cover_follow").set("html", '<i class="ynicon-followed"></i>');
            	$("ynevent_widget_cover_follow").set("onclick", "setFollow(1)");
            	$("ynevent_widget_cover_follow").set("title", "Follow this event");
            }
            else if (option_id == '1')
            {
            	$("ynevent_widget_cover_follow").set("html", '<i class="ynicon-followed-w"></i>');
            	$("ynevent_widget_cover_follow").set("onclick", "setFollow(0)");
            	$("ynevent_widget_cover_follow").set("title", "Un-follow this event");
            }
            
        }
    }).send();
}

function checkOpenPopup(url)
{
	  if(window.innerWidth <= 480)
	  {
	  	Smoothbox.open(url, {autoResize : true, width: 300});
	  }
	  else
	  {
	  	Smoothbox.open(url);
	  }
}

function ynevent_like(ele)     
{   
    var like ="<img class='ynevent_thumpup' src='application/modules/Ynevent/externals/images/thumb-up-icon.png'>";
    var unlike ="<img class='ynevent_thumpdown' src='application/modules/Ynevent/externals/images/thumb-down-icon.png'>"
    if (ele.className=="ynevent_like") {
        var request_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'like', 'subject' => $this->subject()->getGuid()), 'default', true); ?>';
    } else {
        var request_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'unlike', 'subject' => $this->subject()->getGuid()), 'default', true); ?>';
    }
    new Request.JSON({
        url:request_url ,
        method: 'post',
        data : {
            format: 'json',
            'type':'event',
            'id': <?php echo $this->subject()->event_id ?>
                    
        },
        onComplete: function(responseJSON, responseText) {
            if (responseJSON.error) {
                en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
            } else {
                if (ele.className=="ynevent_like") {
                    ele.setAttribute("class", "ynevent_unlike")|| ele.setAttribute("className", "ynevent_unlike");
                    ele.title= '<?php echo $this->translate("Liked") ?>';
                    ele.innerHTML = '<i class="ynicon-liked-w"></i>';                    
                } else {    
                    ele.setAttribute("class", "ynevent_like")|| ele.setAttribute("className", "ynevent_like"); 
                    ele.title= '<?php echo $this->translate("Like") ?>';                        
                    ele.innerHTML = '<i class="ynicon-liked"></i>';
                }                   
            }
        }
    }).send();
}
</script>

<?php
$coverPhotoUrl = "";
if ($this->event->cover_photo)
{
	$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->event->cover_photo)->current();
	$coverPhotoUrl = $coverFile->map();
}
?>

<div class="ynevent-widget-profile-cover">
	<?php
		$eventPhotoUrl = ($this->event->getPhotoUrl())
			? ($this->event->getPhotoUrl())
			: $this->layout()->staticBaseUrl . 'application/modules/Ynevent/externals/images/nophoto_event_thumb_profile.png';
	?>

    <?php if ($coverPhotoUrl!="") : ?>
    <div class="profile-cover-picture">
        <span class="profile-cover-picture-span" style="background-image: url(<?php echo $coverPhotoUrl; ?>);"></span>
    </div>
    <?php else : ?>
    <div class="profile-cover-picture">
        <span class="profile-cover-picture-span" style="background-image: url('application/modules/Ynevent/externals/images/ynevent_default_cover.jpg');"></span>
    </div>
    <?php endif; ?>
    <div class="profile-cover-avatar">
        <span style="background-image: url(<?php echo $eventPhotoUrl; ?>);"></span>
    </div>
    <div class="ynevent-detail-info">
        <div class="info-top ynclearfix">
            <div class="ynevent-detail-action">
            		<?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
						<?php if ($this->subject()->likes()->isLike($this->viewer())) : ?>
							<div class="">
								<a title="<?php echo $this->translate("Unlike this event")?>"
								id="ynevent_unlike" href="javascript:void(0);"
								onClick="ynevent_like(this);" class="ynevent_unlike"> 
								     <i class="ynicon-liked-w"></i>
								</a>	
							</div>		
					<?php else : ?>
						<div class="">
							<a title="<?php echo $this->translate("Like this event") ?>" id="ynevent_like"
								href="javascript:void(0);" onClick="ynevent_like(this);"
								class="ynevent_like"> 
								     <i class="ynicon-liked"></i>
							</a>
						</div>
			            <?php endif;?>
					<?php endif; ?>
            	<?php if ($this->viewer()->getIdentity()): ?>
                	<div id="ynevent_widget_cover_follow" class="" title="<?php echo ($this->follow) ? $this -> translate("Un-follow this event") : $this -> translate("Follow this event")?>" onclick="<?php echo ($this->follow) ? "setFollow(0);" : "setFollow(1);"; ?>"><?php echo ($this->follow) ? '<i class="ynicon-followed-w"></i>' : '<i class="ynicon-followed"></i>';?></div>
                <?php endif;?>
                <?php if($this->aJoinButton && is_array($this->aJoinButton)):?>
	                <?php if (count($this->aJoinButton) == '2'):?>
	                			<div id="ynevent_widget_cover_invitation_proceed"><i class="ynicon-request"></i></div>
					<?php else:?>
								<?php if (isset($this->aJoinButton['params']['action'])) 
								{
									$action = $this->aJoinButton['params']['action'];
								}
								?>
								<div class="">
				                	<a href="<?php echo $this->url($this->aJoinButton['params'], $this->aJoinButton['route'], array());?>" class="<?php echo $this->aJoinButton['class'];?>" title="<?php echo $this -> translate($this->aJoinButton['label']); ?>">
				                		<?php if ($action === 'join'):?>
				                			<i class="ynicon-joining"></i>
				                		<?php elseif ($action === 'leave'):?>
				                			<i class="ynicon-leaving"></i>
				                		<?php elseif ($action === 'request'):?>
				                			<i class="ynicon-request"></i>
				                		<?php elseif ($action === 'cancel'):?>
				                			<i class="ynicon-pending"></i>
				                		<?php endif;?>
				                	</a>
                				</div>
					<?php endif;?>                
                <?php endif;?>
		
                <?php if ($this->viewer()->getIdentity()): ?>
                	<div id="ynevent_widget_cover_settings"><i class="ynicon-setting" title="<?php echo $this -> translate("Event options")?>"></i></div>
                <?php endif;?>
            </div>            
            <div class="ynevent-detail-main">
                <div><strong title="<?php echo $this->event->getTitle();?>"><?php echo $this -> string() -> truncate($this->event->getTitle(), 30) . " ";?></strong><?php echo $this->translate("by") . " ";?> <strong title="<?php echo $this->user->getTitle()?>"><?php echo $this->htmlLink($this->user->getHref(), $this -> string() -> truncate($this->user->getTitle(), 15), array()); ?></strong></div>
                <div>
                	<?php
	                	$startDateObject = new Zend_Date(strtotime($this->event->starttime));
	                	$endDateObject = new Zend_Date(strtotime($this->event->endtime));
	                	if( $this->viewer() && $this->viewer()->getIdentity() ) {
	                		$tz = $this->viewer()->timezone;
	                		$startDateObject->setTimezone($tz);
	                		$endDateObject->setTimezone($tz);
	                	}
                	?>
                    <span><i class="ynicon-time" title="<?php echo $this -> translate("Time of event")?>"></i><?php echo $this->translate('%1$s %2$s',
			            $this->locale()->toDate($startDateObject),
			            $this->locale()->toTime($startDateObject)
			          	) ?> <a>-</a> <?php echo $this->translate('%1$s %2$s',
			            $this->locale()->toDate($endDateObject),
			            $this->locale()->toTime($endDateObject)
			          	) ?></span>
                    <span><i class="ynicon-person" title="<?php echo $this -> translate("Guests")?>"></i><?php echo $this->translate(array('%s guest', '%s guests', $this->event->member_count),$this->locale()->toNumber($this->event->member_count)); ?> </span>
                    <span><i title="<?php echo $this -> translate("Rating")?>" class="<?php echo (Engine_Api::_()->ynevent()->checkRated($this->event->getIdentity(), $this->viewer()->getIdentity())) ? "ynicon-rating-w" : "ynicon-rating"; ?>"></i><?php echo number_format($this->event->rating, 1); ?></span>
                </div>
                <?php if($this->event->address):?>
                	<div><span title="<?php echo $this->event->address?>"><i class="ynicon-location" title="<?php echo $this -> translate("Location")?>"></i><?php echo $this -> string() -> truncate($this->event->address, 50);?></span></div>
                <?php endif;?>
            </div>
            
            <?php if($this->aJoinButton && is_array($this->aJoinButton)):?>
	                <?php if (count($this->aJoinButton) == '2'):?>
						<div class="ynevent-detail-request" style="display: none;">
							<?php foreach ($this->aJoinButton as $button):?>
								<?php if (isset($button['params']['action'])) 
									{
										$action = $button['params']['action'];
									}
									?>
									<div class="">
					                	<a href="<?php echo $this->url($button['params'], $button['route'], array());?>" class="<?php echo $button['class'];?>" title="<?php echo $this -> translate($button['label']); ?>">
					                		<?php echo $this -> translate($button['label']); ?>
					                	</a>
	                				</div>
							<?php endforeach;?>
			            </div>
	                <?php endif;?>
			<?php endif;?>
            
            
            
            <div class="ynevent-detail-setting" style="display: none;">
            	<?php if($this->aEditButton):?>
                <div class="">
                	<a href="<?php echo $this->url($this->aEditButton['params'], $this->aEditButton['route'], array());?>">
                		<?php echo $this -> translate($this->aEditButton['label']); ?>
                	</a>
                </div>
                <?php endif;?>
                
                <?php if($this->aStyleButton):?>
                <div class="">
                	<a href="<?php echo $this->url($this->aStyleButton['params'], $this->aStyleButton['route'], array());?>" class="<?php echo $this->aStyleButton['class'];?>">
                		<?php echo $this -> translate($this->aStyleButton['label']); ?>
                	</a>
                </div>
                <?php endif;?>
                <?php $url = $this->url(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $this -> event ->getGuid(), 'format' => 'smoothbox'),'default', true);?>
				<div class=""><a class="smoothbox" href="<?php echo $url?>"><?php echo $this -> translate("Report"); ?></a></div>
                <?php if($this->aInviteButton):?>
                <div class="">
                	<a href="<?php echo $this->url($this->aInviteButton['params'], $this->aInviteButton['route'], array());?>" class="<?php echo $this->aInviteButton['class'];?>">
                		<?php echo $this -> translate($this->aInviteButton['label']); ?>
                	</a>
                </div>
                <?php endif;?>
                
                <?php if($this->aTrasferButton):?>
                <div class="">
                	<a href="<?php echo $this->url($this->aTrasferButton['params'], $this->aTrasferButton['route'], array());?>" class="<?php echo $this->aTrasferButton['class'];?>">
                		<?php echo $this -> translate($this->aTrasferButton['label']); ?>
                	</a>
                </div>
                <?php endif;?>
                
                <?php if($this->aMessageButton):?>
                <div class="">
                	<a href="<?php echo $this->url($this->aMessageButton['params'], $this->aMessageButton['route'], array());?>">
                		<?php echo $this -> translate($this->aMessageButton['label']); ?>
                	</a>
                </div>
                <?php endif;?>
                
                <?php if($this->aDeleteButton):?>
                <div class="">
                	<a href="<?php echo $this->url($this->aDeleteButton['params'], $this->aDeleteButton['route'], array());?>" class="<?php echo $this->aDeleteButton['class'];?>">
                		<?php echo $this -> translate($this->aDeleteButton['label']); ?>
                	</a>
                </div>
                <?php endif;?>
                
                <?php if($this->aInviteGroupButton):?>
                <div class="">
                	<a href="<?php echo $this->url($this->aInviteGroupButton['params'], $this->aInviteGroupButton['route'], array());?>" class="<?php echo $this->aInviteGroupButton['class'];?>">
                		<?php echo $this -> translate($this->aInviteGroupButton['label']); ?>
                	</a>
                </div>
                <?php endif;?>
                
                <?php if($this->aPromoteButton):?>
                <div class="">
                	<a href="<?php echo $this->url($this->aPromoteButton['params'], $this->aPromoteButton['route'], array());?>" class="<?php echo $this->aPromoteButton['class'];?>">
                		<?php echo $this -> translate($this->aPromoteButton['label']); ?>
                	</a>
                </div>
                <?php endif;?>
                
                <?php if ($this->event->getOwner()->getIdentity() == $this->viewer()->getIdentity()): ?>
                <div class="">
                	<a href="<?php echo $this -> url(array('controller' => 'announcement', 'action' => 'manage', 'event_id' => $this -> event -> getIdentity()), 'event_extended', true)?>">
                		<?php echo $this -> translate("Manage Announcements");?>
                	</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="info-bottom ynclearfix">
            
            <div class="ynevent-detail-contact">
            	<?php if($this->event->email) :?>
            		<div class=""><span><i class="ynicon-email"></i></span><a href="mailto:<?php echo $this->event->email; ?>"><?php echo $this -> translate("Contact Us"); ?></a></div>
            	<?php endif;?>
                <?php if($this->event->url) :?>
            		<div class=""><span><i class="ynicon-global"></i></span><a href="<?php echo $this->event->url; ?>"><?php echo $this->event->url; ?></a></div>
            	<?php endif;?>
                <?php if($this->event->phone) :?>
            		<div class=""><span><i class="ynicon-mobile"></i></span><?php echo $this->event->phone; ?></div>
            	<?php endif;?>
            </div>
            
            <div class="ynevent-detail-more">
            	<?php if ($this->event->host):?>
            		<div class=""><span><i class="ynicon-sponsor" title="<?php echo $this -> translate("Host")?>"></i></span><?php 
            				if($this->event->host)
				            {
				            	if(strpos($this->event->host,'younetco_event_key_') !== FALSE)
								{
								  	$user_id = substr($this->event->host, 19, strlen($this->event->host));
									$user = Engine_Api::_() -> getItem('user', $user_id);
									
									echo $this->translate('Host by %1$s',
				                  	$this->htmlLink($user->getHref(), $user->getTitle())) ;
								}
								else{
									echo $this->translate('Host by %1$s', '<strong>'.$this->event->host.'</strong>');
								}
							}
            			?>
            		</div>
            	<?php endif;?>
            	<?php if ($this->event->location):?>
            		<div class=""><span><i class="ynicon-nearby" title="<?php echo $this -> translate("Near by")?>"></i></span><strong><?php echo $this->event->location; ?></strong></div>
            	<?php endif;?>
            	<?php if ($this->category):?>
            		<div class=""><span><i class="ynicon-category" title="<?php echo $this -> translate("Category")?>"></i></span><?php echo $this->category->getTitle(); ?></div>
            	<?php endif;?>
            	<div class="ynevent_widget_cover_custom_fields" title="<?php echo $this -> translate("Custom fields")?>">
					<?php if($this->fieldStructure):?>
				         <?php echo $this->fieldValueLoop($this->event, $this->fieldStructure); ?>
				    <?php endif;?>
				</div>
            </div>
        	<!-- Add addthis share-->
        	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-558fa99deeb4735f" async="async"></script>
			<div class="addthis_sharing_toolbox"></div>
        </div>
    </div>
</div>
