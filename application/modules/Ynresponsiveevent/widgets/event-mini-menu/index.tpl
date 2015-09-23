<?php $viewer = $this->viewer();?>
<div class="yntheme-event-container clearfix <?php echo $viewer->getIdentity()?'':' not-login'?>">
	<div class="ybo_logo">
		<?php
		$site_name = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
		if($this -> site_name)
		{
			$site_name = $this -> site_name;
		}
		$logo  = $this->logo ? $this->logo: false;
		$route = $this->viewer()->getIdentity()
					 ? array('route'=>'user_general', 'action'=>'home')
					 : array('route'=>'default');
		echo ($logo)
			 ? $this->htmlLink(($this -> logo_link)?$this -> logo_link:$route, $this->htmlImage($logo, array('class' => 'navbar-brand')))
			 : $this->htmlLink(($this -> site_link)?$this -> site_link:$route, $site_name, array('class' => 'navbar-brand'));
		?>
	</div>
    <?php if($this->search_check):?>
		<div id="global_search_form_container">
			<?php $url = $this->url(array('controller' => 'search'), 'default', true) ;
			if(strpos($_SERVER['REQUEST_URI'], 'search?type'))
				$url = $_SERVER['REQUEST_URI'];?>
			<form id="global_search_form" action="<?php echo $url?>" method="get">
			  <input type='text' class='text suggested' name='query' id='global_search_field' size='20' maxlength='100'/>                       
			</form>
		</div>
	<?php endif;?>
    <div class="group-mini-menu">
    	<div>
    		<?php
    		$enabled  = Zend_Registry::isRegistered('YNTOUR_ENABLED')?Zend_Registry::get('YNTOUR_ENABLED'):false;
	        if($enabled):?>
	        	<a href="javascript:en4.yntour.startTour();" class="no-dloader menu_core_mini core_mini_yntour"><?php echo $this -> translate("Tour Guide")?></a>
    		<?php endif;?>
    	</div>
    	<?php
    		// Reverse the navigation order (they're floating right)
    		$count = count($this->navigation);
    		foreach( $this->navigation->getPages() as $item ) $item->setOrder(--$count);
    		$route = array('route'=>'user_logout', 'action'=>'logout');
    	?>
    	 
    	<?php if(!$viewer || !$viewer->getIdentity() ): ?>
    		<?php if($this->fbLoginEnabled || $this->TwLoginEnabled || $this->JrLoginEnabled): ?> 	
    		<!-- When Jarain API was enabled -->   
    		<li class="login_form_container">
    			<div class="social_connect_btn">
    				<!-- Janrain Connect  -->
    				  <?php if($this->JrLoginEnabled): ?>      
    				   <div class ="fb_connect yl_janrain"> <?php echo $this->jrlogin; ?> </div>      
    				   <?php else: ?>
    				  <?php endif; ?>    
    				<!-- Janrain Connect End -->		
    				<!-- Twitter Connect  -->
    				  <?php if($this->TwLoginEnabled): ?>      
    				   <div class ="fb_connect yl_twitter"> <?php echo $this->twlogin; ?> </div>      
    				   <?php else: ?>
    				  <?php endif; ?>    
    				<!-- Twitter Connect End -->		
    				<!-- Facebook Connect -->
    				  <?php if($this->fbLoginEnabled): ?>      
    				   <div class ="fb_connect yl_facebook"> <?php echo $this->fblogin; ?> </div>      
    				   <?php else: ?>
    				  <?php endif; ?>    
    				<!-- Facebook Connect End -->					
    			</div>		
    		</li>		 
    		<?php endif; ?>				
    	<?php endif; ?>	
    	<div class="ynadvmenu_notification" id="ynadvmenu_notification">
    		<?php if($this->viewer->getIdentity()):?>
                <div id="ynadvmenu_MessagesUpdates" class="ynadvmenu_mini_wrapper">
    				<a href="javascript:void(0);" class="ynadvmenu_NotifiIcon" id="ynadvmenu_messages" >
    					<span id="ynadvmenu_MessageIconCount" class="ynadvmenu_NotifiIconWrapper" style="display:none"><span id="ynadvmenu_MessageCount"></span></span>
    				</a>
    				<div class="ynadvmenuMini_dropdownbox" id="ynadvmenu_messageUpdates" style="display: none;">
    					<div class="ynadvmenu_dropdownHeader">
    						<div class="ynadvmenu_dropdownArrow"></div>				  
    					</div>
    					<div class="ynadvmenu_dropdownTitle">
    						<h3><?php echo $this->translate("Messages") ?> </h3>				
    						<a href="<?php echo $this->url(array('action'=>'compose'),'messages_general', true)?>"><?php echo $this->translate("Send a New Message") ?></a>
    					</div>
    					<div class="ynadvmenu_dropdownContent" id="ynadvmenu_messages_content">
    						<!-- Ajax get and out content to here -->
    					</div>				
    					<div class="ynadvmenu_dropdownFooter">
    						<a class="ynadvmenu_seeMore" href="<?php echo $this->url(array('action'=>'inbox'),'messages_general') ?>">
    							<span><?php echo $this->translate("See All Messages") ?> </span>
    						</a>				
    					</div>				
    				</div>
    			</div>
                
    			<div id="ynadvmenu_FriendsRequestUpdates" class="ynadvmenu_mini_wrapper">
    				<a href="javascript:void(0);" class="ynadvmenu_NotifiIcon" id = "ynadvmenu_friends">
    					<span id="ynadvmenu_FriendIconCount" class="ynadvmenu_NotifiIconWrapper" style="display:none"><span id="ynadvmenu_FriendCount"></span></span>
    				</a>
    				<div class="ynadvmenuMini_dropdownbox" id="ynadvmenu_friendUpdates" style="display: none;">
    					<div class="ynadvmenu_dropdownHeader">
    						<div class="ynadvmenu_dropdownArrow"></div>				  
    					</div>
    					<div class="ynadvmenu_dropdownTitle">
    						<h3><?php echo $this->translate("Friend Requests") ?> </h3>				
    						<a href="<?php echo $this->url(array(),'user_general', true)?>"><?php echo $this->translate("Find Friends") ?></a>
    					</div>
    					<div class="ynadvmenu_dropdownContent" id="ynadvmenu_friends_content">
    						<!-- Ajax get and out content to here -->										
    					</div>				
    					<div class="ynadvmenu_dropdownFooter">
						<a class="ynadvmenu_seeMore" href="<?php echo $this->url(array('action' => 'friend-requests'),'ynresponsive_general')?>">
    							<span><?php echo $this->translate("See All Friend Requests") ?> </span>
    						</a>				
    					</div>				
    				</div>
    			</div>
    	
    			<div id="ynadvmenu_NotificationsUpdates" class="ynadvmenu_mini_wrapper">
    				<a href="javascript:void(0);" class="ynadvmenu_NotifiIcon" id = "ynadvmenu_updates">
    					<span id="ynadvmenu_NotifyIconCount" class="ynadvmenu_NotifiIconWrapper"><span id="ynadvmenu_NotifyCount"></span></span>
    				</a>
    				<div class="ynadvmenuMini_dropdownbox" id="ynadvmenu_notificationUpdates" style="display: none;">
    					<div class="ynadvmenu_dropdownHeader">
    						<div class="ynadvmenu_dropdownArrow"></div>				  
    					</div>
    					<div class="ynadvmenu_dropdownTitle">
    						<h3><?php echo $this->translate("Notifications") ?> </h3>									
    					</div>
    					<div class="ynadvmenu_dropdownContent" id="ynadvmenu_updates_content">
    						<!-- Ajax get and out contetn to here -->
    					</div>				
    					<div class="ynadvmenu_dropdownFooter">
						<a class="ynadvmenu_seeMore" href="<?php echo $this->url(array('action' => 'notifications'),'ynresponsive_general')?>">
    							<span><?php echo $this->translate("See All Notifications") ?> </span>
    						</a>				
    					</div>				
    				</div>
    			</div>	
    		<?php endif; ?>
    	</div>	
        <div class="user-profile">
        	<?php
        	if($this->viewer->getIdentity()) : 
            	{
            		$img = $this->itemPhoto($this->viewer(), 'thumb.profile');

            		if($this->viewer()->getTitle() == 'admin')
            		{
            			echo "<div data-toggle='collapse' data-target='.user-profile-submenu' id='user-profile-info' class='user-profile-info collapsed'>" .$img. "<span>".$this->translate('Admin') . "</span><i class='ynicon-setting-w'></i></div>";
            		}
            		else
            		{				
            		  	echo "<div data-toggle='collapse' data-target='.user-profile-submenu' id='user-profile-info' class='user-profile-info collapsed'>" .$img. "<span>".strip_tags($this->string()->truncate($this->viewer()->getTitle(), 20))."</span>
                     <i class='fa fa-caret-down'></i></div>";				 
            		}
            	}
            	?>
            
                <ul id="user-profile-submenu" class="user-profile-submenu collapse"> 
                    <li class="user-profile-submenu-title"><?php echo $this->translate('Account & Settings  ').'<i class="fa fa-caret-right"></i>'; ?></li>
                        <?php if($this->search_check):?>
                    		<li class="global_search_form_second">
                    			<form action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
                    			  <input type='text' class='text suggested' name='query' size='20' maxlength='100' alt='<?php echo $this->translate('Search') ?>' placeholder="<?php echo $this->translate('Search') ?>" />
                    			</form>
                    		</li>
                    	<?php endif;?>
                    <?php foreach( $this->navigation as $item ): ?>
            		<li <?php if ($viewer && !$viewer->getIdentity()): ?> class="Login" <?php endif;?>>
            			<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
            			'class' => ( !empty($item->class) ? $item->class : null ),
            			'alt' => ( !empty($item->alt) ? $item->alt : null ),
            			'target' => ( !empty($item->target) ? $item->target : null ),
            			))) ?>
            		</li>
            		<?php endforeach; ?>                    
            	</ul>
            <?php else : ?>
                <ul class="user-profile-login"> 
            		<?php foreach( $this->navigation as $item ): ?>
            		<li <?php if ($viewer && !$viewer->getIdentity()): ?> class="Login" <?php endif;?>>
            			<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
            			'class' => ( !empty($item->class) ? $item->class : null ),
            			'alt' => ( !empty($item->alt) ? $item->alt : null ),
            			'target' => ( !empty($item->target) ? $item->target : null ),
            			))) ?>
            		</li>
            		<?php endforeach; ?>
            	</ul>
            <?php endif; ?>            	 
        </div>
     </div>
</div>

<script type='text/javascript'>
  var toggleUpdatesPulldown = function(event, element, user_id) {
    if( element.className=='updates_pulldown' ) {
      element.className= 'updates_pulldown_active';
      showNotifications();
    } else {
      element.className='updates_pulldown';
    }
  }
	
  var toggleSortPulldown = function(event, element, user_id) {
    if( element.className=='sort_pulldown' ) {
      element.className= 'sort_pulldown_active';
    } else {
      element.className='sort_pulldown';
    }
  }  
  if (typeof jQuery != 'undefined') 
  { 
     jQuery.noConflict();
  }
  var notificationUpdater;
  <?php if($this->viewer->getIdentity()):?>
  var hide_all_drop_box = function(except)
  {
      //hide all sub-minimenu
      $$('.updates_pulldown_active').set('class','updates_pulldown');
      // reset inbox
      if (except != 1) {
          $('ynadvmenu_messages').removeClass('notifyactive');
          $('ynadvmenu_messageUpdates').hide();
          inbox_status = false;
          inbox_count_down = 1;
      }
      if (except != 2) {
          // reset friend
          $('ynadvmenu_friends').removeClass('notifyactive');
          $('ynadvmenu_friendUpdates').hide();
          friend_status = false;
          friend_count_down = 1;
      }
      if (except != 3) {
            // reset notification
          $('ynadvmenu_updates').removeClass('notifyactive');
          $('ynadvmenu_notificationUpdates').hide();
          notification_status = false;
          notification_count_down = 1;
      }
      
      $('user-profile-info').addClass('collapsed');
      $('user-profile-submenu').removeClass('in').addClass('collapse').hide();


  }
  //refresh box
  var refreshBox = function(box) {
      var img_loading = '<?php echo $this->baseUrl(); ?>/application/modules/Ynresponsive1/externals/images/loading.gif';
      if (box == 1) {
          // refresh message box
          inbox_count_down = 1;
          $('ynadvmenu_messages_content').innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
          inbox();
      } else if (box == 2) {
          // refresh friend box
          friend_count_down = 1;
          $('ynadvmenu_friends_content').innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
          freq();
      } else if (box == 3) {
          // refresh notification box
          notification_count_down = 1;
          $('ynadvmenu_updates_content').innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
          notification();
      }
  }
  var isLoaded = [0, 0, 0]; // friend request, message, notifcation
  var timerNotificationID = 0;
  //time to check for notification updates (in seconds)
  var updateTimes = <?php echo Engine_Api::_()->getApi('settings','core')->getSetting('core.general.notificationupdate',30000) ?>; 
  var getNotificationsTotal = function()
  {
    var notif = new Request.JSON({
           url    :  '<?php echo $this->layout()->staticBaseUrl?>'  +  'application/lite.php?module=ynresponsive1&name=total&viewer_id=' + <?php echo $this->viewer->getIdentity() ?>,
           onSuccess : function(data) {
                if(data != null)
                {
                     if (data.notification > 0) {
                          var notification_count = $('ynadvmenu_NotifyCount');
                          notification_count.set('text', data.notification);
                          notification_count.getParent().setStyle('display', 'block');
                          $('ynadvmenu_NotificationsUpdates').className += " ynadvmenu_hasNotify";
                          isLoaded[2] = 0;
                     }
                     else
                     {
                        var notification_count = $('ynadvmenu_NotifyCount');
                        notification_count.getParent().setStyle('display', 'none'); 
                        $('ynadvmenu_NotificationsUpdates').className = "ynadvmenu_mini_wrapper"; 
                     }
                     if (data.freq > 0) {
                          var friend_req_count = $('ynadvmenu_FriendCount');
                          friend_req_count.set('text', data.freq);
                          friend_req_count.getParent().setStyle('display', 'block');
                          $('ynadvmenu_FriendsRequestUpdates').className += " ynadvmenu_hasNotify";
                          isLoaded[0] = 0;
                     }
                     else
                     {
                         var friend_req_count = $('ynadvmenu_FriendCount');
                         friend_req_count.getParent().setStyle('display', 'none');
                         $('ynadvmenu_FriendsRequestUpdates').className = "ynadvmenu_mini_wrapper"; 
                     }
                     if (data.msg > 0) {
                          var msg_count = $('ynadvmenu_MessageCount');
                          msg_count.set('text', data.msg);
                          msg_count.getParent().setStyle('display', 'block');
                          $('ynadvmenu_MessagesUpdates').className += " ynadvmenu_hasNotify";
                          isLoaded[1] = 0;
                     }
                     else
                     {
                          var msg_count = $('ynadvmenu_MessageCount');
                           msg_count.getParent().setStyle('display', 'none');
                           $('ynadvmenu_MessagesUpdates').className = "ynadvmenu_mini_wrapper";
                     }
                }              
           }
    }).get();
    <?php if($this->viewer()->getIdentity() > 0): ?>
    if(updateTimes > 10000){
        timerNotificationID = setTimeout(getNotificationsTotal, updateTimes);
    }
    <?php endif; ?>
 }

  var inbox = function() {
       new Request.HTML({
           'url'    :    en4.core.baseUrl + 'ynresponsive1/index/message',
           'data' : {
                'format' : 'html',
                'page' : 1
            },
            'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        if(responseHTML)
                        {
                              $('ynadvmenu_messages_content').innerHTML = responseHTML;
                              $('ynadvmenu_MessageCount').getParent().setStyle('display', 'none'); 
                              $('ynadvmenu_MessagesUpdates').removeClass('ynadvmenu_hasNotify'); 
                              $('ynadvmenu_messages_content').getChildren('ul').getChildren('li').each(function(el){
                                  el.addEvent('click', function(){inbox_count_down = 1;});
                              });
                        }
            }
       }).send();
   }
   //inbox();

   var freq = function() {
       new Request.HTML({
           'url'    :    en4.core.baseUrl + 'ynresponsive1/index/friend',
           'data' : {
                'format' : 'html',
                'page' : 1
            },
            'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                if(responseHTML)
                { 
                    $('ynadvmenu_friends_content').innerHTML = responseHTML;
                    $('ynadvmenu_FriendCount').getParent().setStyle('display', 'none');
                    $('ynadvmenu_FriendsRequestUpdates').removeClass('ynadvmenu_hasNotify');
                    $('ynadvmenu_friends_content').getChildren('ul').getChildren('li').each(function(el){
                           el.addEvent('click', function(){friend_count_down = 1;});
                    });
                }
            }
       }).send();
   }
   //freq();

   var notification = function() {
       new Request.HTML({
           'url'    :    en4.core.baseUrl + 'ynresponsive1/index/notification',
           'data' : {
                'format' : 'html',
                'page' : 1
            },
            'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                if(responseHTML)
                { 
                    $('ynadvmenu_updates_content').innerHTML = responseHTML;    
                    $('ynadvmenu_NotifyCount').getParent().setStyle('display', 'none');  
                    $('ynadvmenu_NotificationsUpdates').removeClass('ynadvmenu_hasNotify');      
                    $('ynadvmenu_updates_content').getChildren('ul').getChildren('li').each(function(el){
                       el.addEvent('click', function(){notification_count_down = 1;});
                    });
                }
            }
       }).send();
   }
   //notification();
  // Show Inbox Message
  var inbox_count_down = 1;
  var inbox_status = false; // false -> not shown, true -> shown
  $('ynadvmenu_messages').addEvent('click', function() 
  {
      hide_all_drop_box(1);
      if (inbox_status) inbox_count_down = 1; 
      if (!inbox_status) {
          // show
          $(this).addClass('notifyactive');
          $('ynadvmenu_messageUpdates').setStyle('display', 'block');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
      } else {
          // hide
          $(this).removeClass('notifyactive');
          $('ynadvmenu_messageUpdates').setStyle('display', 'none');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
      }
      inbox_status = inbox_status ? false : true;
      if (isLoaded[1] == 0) 
      {
          refreshBox(1);
          isLoaded[1] = 1;
      }
  });
  // Friend box
  var friend_count_down = 1;
  var friend_status = false;
  $('ynadvmenu_friends').addEvent('click', function(){
  	
      hide_all_drop_box(2);
      if (friend_status) friend_count_down = 1;
      if (!friend_status) {
          $(this).addClass('notifyactive');
          $('ynadvmenu_friendUpdates').setStyle('display', 'block');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
      } else {
          $(this).removeClass('notifyactive');
          $('ynadvmenu_friendUpdates').setStyle('display', 'none');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
      }
      friend_status = friend_status ? false : true; 

      // Set all message as read
      if (isLoaded[0] == 0) {
          refreshBox(2);
          isLoaded[0] = 1;   // get again is check isloaded = 0      
      }
  });
  //Notification box
  var notification_count_down = 1;
  var notification_status = false;
  $('ynadvmenu_updates').addEvent('click', function()
  {
      hide_all_drop_box(3);
      if (notification_status) notification_count_down = 1;
      if (!notification_status) {
          // active
          $(this).addClass('notifyactive');
          $('ynadvmenu_notificationUpdates').setStyle('display', 'block');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
      } else {
          $(this).removeClass('notifyactive');
          $('ynadvmenu_notificationUpdates').setStyle('display', 'none');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
      }
      notification_status = notification_status ? false : true;

      if (isLoaded[2] == 0) {
          refreshBox(3);
          isLoaded[2] = 1;
      }
  });
  do_confrim_friend = false;

  $(document).addEvent('click', function(event) 
  { 
        if (inbox_status && inbox_count_down <= 0) {
            $('ynadvmenu_messages').removeClass('notifyactive');
            $('ynadvmenu_messageUpdates').setStyle('display', 'none');
            var elements = document.getElements('video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'block';
					  });
					  var elements = document.getElements('img.thumb_video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'block';
					  });
            inbox_status = false;            
            inbox_count_down = 1;
        } else if (inbox_status) {
            inbox_count_down = (inbox_count_down <= 0) ? 0 : --inbox_count_down;
        }         
        
        if (friend_status && friend_count_down <= 0) {
            if (do_confrim_friend) {do_confrim_friend = false; return false;}
            $('ynadvmenu_friends').removeClass('notifyactive');
            $('ynadvmenu_friendUpdates').setStyle('display', 'none');
             var elements = document.getElements('video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'block';
					  });
					  var elements = document.getElements('img.thumb_video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'none';
					  });
            friend_status = false;            
            friend_count_down = 1;
        } else if (friend_status) {
            friend_count_down = (friend_count_down <= 0) ? 0 : --friend_count_down;
        } 
        if (notification_status && notification_count_down <= 0) {
            $('ynadvmenu_updates').removeClass('notifyactive');
            $('ynadvmenu_notificationUpdates').setStyle('display', 'none');
            var elements = document.getElements('video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'block';
					  });
					  var elements = document.getElements('img.thumb_video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'none';
					  });
            notification_status = false;            
            notification_count_down = 1;
        } else if (notification_status) {
            notification_count_down = (notification_count_down <= 0) ? 0 : --notification_count_down;
        }
        
        var parent = $(event.target).closest('.user-profile');
        if(parent == null)
        {
        	$('user-profile-info').addClass('collapsed');
	      	$('user-profile-submenu').removeClass('in').addClass('collapse').hide();
        }
   });
<?php endif;?>
var firefox = false;
if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent))
{ //test for Firefox/x.x or Firefox x.x (ignoring remaining digits);
 var ffversion=new Number(RegExp.$1) // capture x.x portion and store as a number
 if (ffversion>=1)
 {
     firefox = true;
 } 
}
window.addEvent('domready', function()
{
	<?php if($this->viewer->getIdentity()):?>
		getNotificationsTotal();
	<?php endif;?>
});

</script>