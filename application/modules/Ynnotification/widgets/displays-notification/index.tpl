<script type="text/javascript">

/**
 * advanced notification
 * @package ynnotification
 * @author luan
 * @version 4.02
 * @since May 2013
 *
 */

var ynnof = {
	opts : {
		refresh : <?php echo Engine_Api::_()->getApi('settings','core')->getSetting('ynnotification.time.refresh',30000) ?>,
		//refresh : 3000,
		time_deplay : <?php echo Engine_Api::_()->getApi('settings','core')->getSetting('ynnotification.time.deplay',10000) ?>,
		photo_notification : <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.photo.notification', 0) ?>,
		background_css : <?php
						$values = Engine_Api::_()->getApi('settings', 'core')->getSetting('avdnotification.customcssobj', 0);
						$values = Zend_JSON::decode($values);		
						echo "'#".$values['mes_background']."'";		
						?>,
		text_css : <?php
				$values = Engine_Api::_()->getApi('settings', 'core')->getSetting('avdnotification.customcssobj', 0);
				$values = Zend_JSON::decode($values);		
				echo "'#".$values['text_color']."'";
				?>,
		urlbase : null,
		sound : null,
		sound_wav : null,
		soundSetting :<?php echo  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.sound.setting', 0);?>,
		soundUserSetting : null,
	},
	cookieName : {
		lastRun : 'last_run_at',
		lastUpdated : 'last_changed_at'		
	},
	
	hideAll : function() 
		{			
			 var eles = $$('.ynnotification_item:not(.delayed)');
			 var c = eles.length;
			    if(c > 0)
			    {	
					var t = ynnof;		    	
				    			
			        eles.each(function(i)
					{  			
						/*var r1 = new Request.HTML({
								url    :    en4.core.baseUrl + 'ynnotification/index/hide/id/'+i.get('notification_id'),							   
								data : {
									 format : 'html',			           	          
								},	
								onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
									   
									//console.log(responseHTML)  ; 	
								}	    	
						});
						r1.send();
						*/
			        	var remove = function()
						{								
			        		i.dispose();
						};	
						remove.delay(t.opts.time_deplay, i);
						
			        });     
			    }    
		},
	
	
	displayFeed : function(data)
	{
		var t = ynnof;
		var notification_message = $$('.ynnotification_message')[0];
							
		for(var i=0;i<data.length;i++)
		{			
			var divItem = document.createElement("div");
			divItem.id ="ynnotification_item_"+data[i].notification_id;
			divItem.setAttribute("notification_id",data[i].notification_id);
			
			divItem.setAttribute("onclick","notifyRead("+data[i].notification_id+");");
			notification_message.appendChild(divItem);
			
			$("ynnotification_item_"+data[i].notification_id).addClass("ynnotification_item");
		 
		  	var divEmt = document.createElement("div");
			divEmt.id ="div_ynnotification_item_"+data[i].notification_id;
			divEmt.style.background = t.opts.background_css;	
			divEmt.style.overflow = 'hidden';	
			divItem.appendChild(divEmt);
			$("div_ynnotification_item_"+data[i].notification_id).addClass('ynnotification ynnotification_top ynnotification_bottom ynnotification_selected');
		   
		  	var spanX = document.createElement("span");	
		   	spanX.id ="div_ynnotification_x"+data[i].notification_id;
		   	spanX.setAttribute("href","javascript:;");							   
		   	spanX.setAttribute("onclick","close_message("+data[i].notification_id+");");
		   	divEmt.appendChild(spanX);	
		   
		   	$("div_ynnotification_x"+data[i].notification_id).addClass('ynnotification_x');
		   	
			if(t.opts.photo_notification)
			{									
				var divIcon = document.createElement("div");			
				//divIcon.addClass('ynnotification_icon');
				divIcon.id ="div_ynnotification_icon"+data[i].notification_id;	
				divEmt.appendChild(divIcon);
				$("div_ynnotification_icon"+data[i].notification_id).addClass('ynnotification_icon');
				
				var iIcon = document.createElement("i");	
			   
			   	iIcon.id ="i_beeper_icon"+data[i].notification_id;	
			   	divIcon.appendChild(iIcon);
			   	$("i_beeper_icon"+data[i].notification_id).addClass('beeper_icon');
			   
				var aHref = document.createElement("a");	
		   		aHref.setAttribute("href",data[i].user_getHref);
		   		aHref.setAttribute("title",data[i].user_getTitle);	
		   		aHref.innerHTML = 	data[i].user_getPhotoUrl;
		   		iIcon.appendChild(aHref);
			}					   
		   	var aLinkTag = document.createElement("div");	
		   
		   	aLinkTag.id ="dev_ynnotification_content"+data[i].notification_id;	
		   	aLinkTag.style.color = t.opts.text_css;	
		   	divEmt.appendChild(aLinkTag);
		   	$("dev_ynnotification_content"+data[i].notification_id).addClass('ynnotification_content');		
		   					  
		    aLinkTag.innerHTML = 	data[i].content;		

		   	if(t.opts.sound && t.opts.soundSetting && t.opts.soundUserSetting)
		   	{							
				var divSoundTag =  document.createElement("div");
				divSoundTag.style.display = 'none';	
				divSoundTag.innerHTML = '<audio autoplay="autoplay"><source src="' + t.opts.sound + '" type="audio/mpeg" /><source src="' + t.opts.sound_wav  + '" type="audio/wav" /><embed hidden="true" autostart="true" loop="false" src="' + t.opts.sound +'" /></audio>';								
				divItem.appendChild(divSoundTag);									
		   }								   
       }  
       
       //t.hideAll.delay(1000);
       t.hideAll();
	},
	
	/**
	 * request to server to check if there is new notification
	 * + changed cookie if there is new notification
	 * + update run at
	 * + request to server if runat > cookie runat value.
	 * @return void
	 */
	checkNew : function() {	
		var t = ynnof;
		var now = (new Date()).getTime();		
		var lastRun =  Cookie.read(t.cookieName.lastRun);
		if(now - lastRun >= t.opts.refresh) {	
			Cookie.write(t.cookieName.lastRun, now);			
			t._liveCheck(now);
		}
	},
	/**
	 * request to server to check if there is new update.
	 */
	_liveCheck : function(now) 
	{
		var t = ynnof;		
		var r1 = new Request.JSON({
				url    :    en4.core.baseUrl + 'ynnotification/index/get-feeds',
				onSuccess : function(data) {					
	                if(data.length>0)
	                {	 
	                	// update cookie value.   
	                	Cookie.write(t.cookieName.lastUpdated, now);
	                	console.log("request to server to check if there is new update.");
	                }    
				}	    	
		});
		r1.send();
		
	},
	/**
	 * There are new notifications, need to request to server than display to browsers.
	 * @see ynnof.onReady
	 * @param void
	 * @return void
	 */
	checkUpdated : function() {
		var t = ynnof;
		/**
		 * @var int current time
		 */
		var now = (new Date()).getTime();
		/**
		 * @var int
		 * get last updated from cookie.
		 */
		//var lastUpdated = $.cookie(t.cookieName.lastUpdated);
		var lastUpdated = Cookie.read(t.cookieName.lastUpdated);
		//console.log("lastUpdated : "+lastUpdated);
		//console.log("t.lastUpdated : "+t.lastUpdated);
		if(t.lastUpdated < lastUpdated) {
			t.lastUpdated = lastUpdated;
			// request to server to get notification detail.			
			t._liveUpdate(now);			
		}
	},
	/**
	 * @param int now current time
	 * @return void
	 */
	_liveUpdate : function(now) {		    
		var t = ynnof;	
		
		var r2 = new Request.JSON({
				url    :    en4.core.baseUrl + 'ynnotification/index/display-feed',
				onSuccess : function(data) {
					console.log(data);
	                if(data.length>0)
	                {	 
	                	console.log('request to server to check if there is new notification.');
			            t.displayFeed(data);  
	                }    
				}	    	
		});
		r2.send();	
	},
	/**
	 * @called when document is ready
	 * @return void
	 */
	onReady : function() {
		var t = ynnof;	
		
		var urlbase = en4.core.baseUrl;		
		if(urlbase.indexOf("index.php")>0)
		{			
			urlbase = urlbase.substring(0,urlbase.length-10);			
		}	
		t.opts.urlbase = urlbase;
		
		var sound = <?php echo  "'public/temporary/".Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.sound.alert', null)."'";?>;
		t.opts.sound = urlbase + sound;
		
		var sound_wav = <?php echo  "'public/temporary/".Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.sound.wav.alert', null)."'";?>;
		t.opts.sound_wav = urlbase + sound_wav;	
		
		var soundUserSetting = <?php echo  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.user'.$this->viewer->getIdentity().'sound.setting', 2);?>;
		
		if(soundUserSetting == 2)
			soundUserSetting = true;
		t.opts.soundUserSetting = soundUserSetting;		
		
		var now = (new Date()).getTime();
		t.lastUpdated = now;
		
		//console.log(t);
		setInterval(t.checkNew, t.opts.refresh);
		setInterval(t.checkUpdated, 2000);
		
		//t.checkNew.delay(t.opts.refresh);
		//t.checkUpdated.delay(2000);
		
	}
}

document.onload = ynnof.onReady(); 

function close_message(not_id) {
	var div = $('ynnotification_item_'+not_id);	
	div.remove();	
};
function notifyRead(actionid)
{
	
      en4.core.request.send(new Request.JSON({       
        url    :    en4.core.baseUrl + 'ynnotification/index/notify-read',
        data : {
          format     : 'json',
          'actionid' :  actionid
        },
        'onComplete' : function(response)
        {
        	console.log(response);
              var content = $('dev_ynnotification_content'+actionid).innerHTML;
              var arrHref = content.split("href");
              if(arrHref[2])
              {
                var strlink = arrHref[2];
                var arrstr = strlink.split('"');
                window.location = arrstr[1];
              }
              else if(arrHref[1])
              {
                  strlink = arrHref[1];
                  arrstr = strlink.split('"');
                  window.location = arrstr[1]; 
              }    
        }
      })); 
}

	
</script>
		    
<div class="ynnotification_message" id="ynnotification_message">
</div>



