<div id="ynevent_list_item_<?php echo $this -> identity;?>" class="<?php echo $this -> class_mode;?>"> 	
 	<div id="yn_event_tabs_<?php echo $this -> identity;?>" class="tabs_alt tabs_parent">
          <!--  Tab bar -->
          <ul id="yn_event_tab_list_<?php echo $this -> identity;?>" class = "main_tabs">
          	<?php if(in_array('popular', $this -> tab_enabled)):?>
              <!-- Popular -->
              <li>
                  <a href="javascript:;" id="<?php echo $this -> identity;?>" rel="tab_events_popular" class="selected">
                        <?php echo $this->translate('Popular Events');?>
                  </a>
              </li>
            <?php endif;?>
            <?php if(in_array('attending', $this -> tab_enabled)):?>
              <!-- Attending -->
              <li>
                  <a href="javascript:;" id="<?php echo $this -> identity;?>" rel="tab_events_attending">
                        <?php echo $this->translate('Most Attending');?>
                  </a>
              </li>
            <?php endif;?>
            <?php if(in_array('liked', $this -> tab_enabled)):?>
              <!-- Public like -->
              <li>
                  <a href="javascript:;" id="<?php echo $this -> identity;?>" rel="tab_events_liked">
                        <?php echo $this->translate('Most Liked');?>
                  </a>
              </li>
             <?php endif;?>
             <?php if(in_array('rated', $this -> tab_enabled)):?>
             	 <!-- Public ranking -->
              <li>
                  <a href="javascript:;" id="<?php echo $this -> identity;?>" rel="tab_events_rated">
                        <?php echo $this->translate('Most Rated');?>
                  </a>
              </li>  
             <?php endif;?>                
          </ul>
          <div class="ynevent-action-view-method">
          	 <?php if(in_array('map', $this -> mode_enabled)):?>
	              <div class="ynevent_home_page_list_content_<?php echo $this -> identity;?>" rel="map_view">
	                	<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('Map View')?></div>
	                	<span id="map_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_map_view" onclick="ynevent_view_map(<?php echo $this -> identity;?>);"></span>
	              </div>
              <?php endif;?>
              <?php if(in_array('grid', $this -> mode_enabled)):?>
	        	  <div class="ynevent_home_page_list_content_<?php echo $this -> identity;?>" rel="grid_view">
	                	<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
	                	<span id="grid_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_grid_view" onclick="ynevent_view_grid(<?php echo $this -> identity;?>);"></span>
	              </div>
              <?php endif;?>
              <?php if(in_array('list', $this -> mode_enabled)):?>
	              <div class="ynevent_home_page_list_content_<?php echo $this -> identity;?>" rel="list_view">
	                	<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('List View')?></div>
	                	<span id="list_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_list_view" onclick="ynevent_view_list(<?php echo $this -> identity;?>);"></span>
              </div>
              <?php endif;?>
           </div>
    </div>
    <div id="ynevent_list_item_content_<?php echo $this -> identity;?>" class="ynevent-tabs-content ynclearfix">
    	<?php if(in_array('popular', $this -> tab_enabled)):?>
		    <!-- Popular Events Tab Content-->
		    <div id="tab_events_popular_<?php echo $this -> identity;?>" class="tabcontent">
			<?php
				echo $this->partial('_list_most_item.tpl', 'ynevent', array('events' => $this->events_popular, 'tab' => 'events_popular'));
			?>
		    </div>
	    <?php endif;?>
        <?php if(in_array('attending', $this -> tab_enabled)):?>
		    <!-- Most Attending Tab Content -->
		    <div id="tab_events_attending_<?php echo $this -> identity;?>" class="tabcontent">
		    <?php
				echo $this->partial('_list_most_item.tpl', 'ynevent', array('events' => $this->events_attending, 'tab' => 'events_attending'));
			?>
		    </div>
	    <?php endif;?>
        <?php if(in_array('liked', $this -> tab_enabled)):?>
		    <!-- Most Liked Tab Content -->
		    <div id="tab_events_liked_<?php echo $this -> identity;?>" class="tabcontent">
		    <?php
				echo $this->partial('_list_most_item.tpl', 'ynevent', array('events' => $this->events_liked, 'tab' => 'events_liked'));
			?>
		    </div>
	    <?php endif;?>
        <?php if(in_array('rated', $this -> tab_enabled)):?>
		    <!-- Most Rated Tab Content -->
		    <div id="tab_events_rated_<?php echo $this -> identity;?>" class="tabcontent">
		    <?php
				echo $this->partial('_list_most_item.tpl', 'ynevent', array('events' => $this->events_rated, 'tab' => 'events_rated'));
			?>
		    </div>
    	<?php endif;?>
		<iframe id='list-most-items-iframe_<?php echo $this -> identity;?>' style="max-height: 500px;"> </iframe>
    </div>
    
     <script type="text/javascript">
           var yn_event_tabs =new ddtabcontent("yn_event_tabs_<?php echo $this -> identity;?>", "<?php echo $this -> identity;?>");
           yn_event_tabs.setpersist(false);
           yn_event_tabs.setselectedClassTarget("link");
           yn_event_tabs.init(900000);
           var ynevent_view_map = function(id)
           {
           		var eventIds = null;
	       		$$('#ynevent_list_item_' + id + ' .tabcontent').each(function (el){
					if(el.get('style') == "display: block;")
					{
						var idElement = el.get('id');
						switch(idElement) {
						    case 'tab_events_popular_'+id:
						        eventIds =  '<?php echo $this -> eventIdsPopular ?>';
						        break;
						    case 'tab_events_attending_'+id:
						        eventIds =  '<?php echo $this -> eventIdsAttending ?>';
						        break;
						    case 'tab_events_liked_'+id:
						        eventIds =  '<?php echo $this -> eventIdsLiked ?>';
						        break;
						    case 'tab_events_rated_'+id:
						        eventIds =  '<?php echo $this -> eventIdsRated ?>';
						        break;
						    default:
						        eventIds =  '<?php echo $this -> eventIdsPopular ?>';
						}
					}       			
	       		});
           		
           		document.getElementById('ynevent_list_item_'+id).set('class','ynevent_map-view');
           		var tab = $$('.layout_ynevent_list_most_items #yn_event_tab_list_' + id + ' li .selected')[0].get('rel');
           		var html =  '<?php echo $this->url(array('action'=>'display-map-view'), 'event_general') ?>' + '/ids/' + eventIds;
           		if(document.getElementById('list-most-items-iframe_'+id))
           			document.getElementById('list-most-items-iframe_'+id).dispose();
           		var iframe = new IFrame({
           			id : 'list-most-items-iframe_'+id,
           			src: html,
    			    styles: {
    			        'height': 500,
    			    },
    			});
           		iframe.inject($$('#ynevent_list_item_content_'+id)[0]);
           		document.getElementById('list-most-items-iframe_'+id).style.display = 'block';
           }  
           
           var ynevent_view_grid =  function(id)
           {
           		if(document.getElementById('list-most-items-iframe_'+id))
           			document.getElementById('list-most-items-iframe_'+id).dispose();
           		document.getElementById('ynevent_list_item_'+id).set('class','ynevent_grid-view');
           }  
           
            var ynevent_view_list = function(id)
           {
           		if(document.getElementById('list-most-items-iframe_'+id))
           			document.getElementById('list-most-items-iframe_'+id).dispose();
           		document.getElementById('ynevent_list_item_'+id).set('class','ynevent_list-view');
           }  
    </script>
    
    <script type="text/javascript">
		en4.core.runonce.add(function()
		{
		    function setCookie(cname, cvalue, exdays) {
			    var d = new Date();
			    d.setTime(d.getTime() + (exdays*24*60*60*1000));
			    var expires = "expires="+d.toUTCString();
			    document.cookie = cname + "=" + cvalue + "; " + expires;
			}
		
			function getCookie(cname) {
			    var name = cname + "=";
			    var ca = document.cookie.split(';');
			    for(var i=0; i<ca.length; i++) {
			        var c = ca[i];
			        while (c.charAt(0)==' ') c = c.substring(1);
			        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
			    }
			    return "";
			}
		    
		 	// Get cookie
			var myCookieViewMode = getCookie('ynevent-viewmode-cookie_<?php echo $this -> identity;?>');
			if ( myCookieViewMode == '') 
			{
				myCookieViewMode = '<?php echo $this -> view_mode;?>_view';
			}
			if ( myCookieViewMode == '') 
			{
				myCookieViewMode = 'list_view';
			}
			switch(myCookieViewMode) {
			    case 'map_view':
			        ynevent_view_map(<?php echo $this -> identity;?>);
			        break;
			    case 'grid_view':
			        ynevent_view_grid(<?php echo $this -> identity;?>);
			        break;
			 	case 'list_view':
			        ynevent_view_list(<?php echo $this -> identity;?>);
			        break;
		    }
			
			// Set click viewMode
			$$('.ynevent_home_page_list_content_<?php echo $this -> identity;?>').addEvent('click', function(){
				var viewmode = this.get('rel');
				setCookie('ynevent-viewmode-cookie_<?php echo $this -> identity;?>', viewmode, 1);
			});
		});
		
	</script>
</div>
