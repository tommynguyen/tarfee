<div id="ynevent_list_time_past_<?php echo $this -> identity;?>" class="<?php echo $this -> class_mode;?>"> 	
 	<div id="yn_event_tabs_time_time_<?php echo $this -> identity;?>" class="tabs_alt tabs_parent">
          <!--  Tab bar -->
          <ul id="yn_event_tab_list_<?php echo $this -> identity;?>" class = "main_tabs">
          		<?php if(in_array('past', $this -> tab_enabled)):?>
                  <!-- Past Event -->
                  <li >
                      <a href="javascript:;" id="<?php echo $this -> identity;?>" rel="tab_events_pastevent" class="selected">
                            <?php echo $this->translate('Past Events');?>
                      </a>
                  </li>
                <?php endif;?>
            	<?php if(in_array('week', $this -> tab_enabled)):?>
                  <!-- Previous week -->
                  <li>
                      <a href="javascript:;" id="<?php echo $this -> identity;?>" rel="tab_events_preweek">
                            <?php echo $this->translate('Previous week');?>
                      </a>
                  </li>
                <?php endif;?>
            	<?php if(in_array('month', $this -> tab_enabled)):?>
                  <!-- Previous month -->
                  <li>
                      <a href="javascript:;" id="<?php echo $this -> identity;?>" rel="tab_events_premonth">
                            <?php echo $this->translate('Previous month');?>
                      </a>
                  </li>
                  <?php endif;?> 
          </ul>
          <div class="ynevent-action-view-method">
          	<?php if(in_array('map', $this -> mode_enabled)):?>
              <div class="ynevent_home_page_list_content_<?php echo $this -> identity;?>" rel="map_view">
                	<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('Map View')?></div>
                	<span id="map_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_map_view" onclick="ynevent_view_map_p_time(<?php echo $this -> identity;?>);"></span>
              </div>
              <?php endif;?>
              <?php if(in_array('grid', $this -> mode_enabled)):?>
    		  <div class="ynevent_home_page_list_content_<?php echo $this -> identity;?>" rel="grid_view">
                	<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
                	<span id="grid_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_grid_view" onclick="ynevent_view_grid_p_time(<?php echo $this -> identity;?>);"></span>
              </div>
              <?php endif;?>
              <?php if(in_array('list', $this -> mode_enabled)):?>
              <div class="ynevent_home_page_list_content_<?php echo $this -> identity;?>" rel="list_view">
                	<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('List View')?></div>
                	<span id="list_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_list_view" onclick="ynevent_view_list_p_time(<?php echo $this -> identity;?>)"></span>
              </div>
              <?php endif;?>
        </div>
    </div>
    <div id="ynevent_list_time_past_content_<?php echo $this -> identity;?>" class="ynevent-tabs-content ynclearfix">
    	<?php if(in_array('past', $this -> tab_enabled)):?>
	        <!-- Past event Tab Content-->
	        <div id="tab_events_pastevent_<?php echo $this -> identity;?>" class="tabcontent">
	    	<?php
	    		echo $this->partial('_list_most_item.tpl', 'ynevent', array('events' => $this->events_pastevent, 'tab' => 'events_pastevent'));
	    	?>
	        </div>
        <?php endif;?>
        <?php if(in_array('week', $this -> tab_enabled)):?>
	        <!-- Previous week Tab Content -->
	        <div id="tab_events_preweek_<?php echo $this -> identity;?>" class="tabcontent">
	        <?php
	    		echo $this->partial('_list_most_item.tpl', 'ynevent', array('events' => $this->events_preweek, 'tab' => 'events_preweek'));
	    	?>
	        </div>
        <?php endif;?>
        <?php if(in_array('month', $this -> tab_enabled)):?>
	        <!-- This month Tab Content -->
	        <div id="tab_events_premonth_<?php echo $this -> identity;?>" class="tabcontent">
	        <?php
	    		echo $this->partial('_list_most_item.tpl', 'ynevent', array('events' => $this->events_premonth, 'tab' => 'events_premonth'));
	    	?>
	        </div>
         <?php endif;?>
    	<iframe id='list-most-time-iframe-past_<?php echo $this -> identity;?>'style="max-height: 500px; display: none;" > </iframe>
     </div>
    
</div>    
 <script type="text/javascript">
       var yn_event_tabs_time =new ddtabcontent("yn_event_tabs_time_time_<?php echo $this -> identity;?>", "<?php echo $this -> identity;?>");
       yn_event_tabs_time.setpersist(false);
       yn_event_tabs_time.setselectedClassTarget("link");
       yn_event_tabs_time.init(900000);
       
       var ynevent_view_map_p_time = function(id)
       {
       		var eventIds = null;
       		$$('#ynevent_list_time_past_' + id + ' .tabcontent').each(function (el){
				if(el.get('style') == "display: block;")
				{
					var idElement = el.get('id');
					switch(idElement) {
					    case 'tab_events_pastevent_'+id:
					        eventIds =  '<?php echo $this -> eventIdsPast ?>';
					        break;
					    case 'tab_events_preweek_'+id:
					        eventIds =  '<?php echo $this -> eventIdsPreweek ?>';
					        break;
					    case 'tab_events_premonth_'+id:
					        eventIds =  '<?php echo $this -> eventIdsPremonth ?>';
					        break;
					    default:
					        eventIds =  '<?php echo $this -> eventIdsPast ?>';
					}
				}       			
       		});
       		
       		document.getElementById('ynevent_list_time_past_'+id).set('class','ynevent_map-view');
       		var tab = $$('.layout_ynevent_list_most_time_past #yn_event_tab_list_' + id + ' li .selected')[0].get('rel');
       		var html =  '<?php echo $this->url(array('action'=>'display-map-view'), 'event_general') ?>' + '/ids/' + eventIds;
       		if(document.getElementById('list-most-time-iframe-past_'+id))
       			document.getElementById('list-most-time-iframe-past_'+id).dispose();
       		
       		var iframe = new IFrame({
       			id : 'list-most-time-iframe-past_'+id,
       			src: html,
			    styles: {
			       
			        'height': 500,
			    },
			});
       		iframe.inject($$('#ynevent_list_time_past_content_'+id)[0]);
       		document.getElementById('list-most-time-iframe-past_'+id).style.display = 'block';
       }
        var ynevent_view_grid_p_time = function(id)
       {
       		if(document.getElementById('list-most-time-iframe-past_'+id))
       			document.getElementById('list-most-time-iframe-past_'+id).dispose();
       		document.getElementById('ynevent_list_time_past_'+id).set('class','ynevent_grid-view');
       }  
        var ynevent_view_list_p_time = function(id)
       {
       		if(document.getElementById('list-most-time-iframe-past_'+id))
       			document.getElementById('list-most-time-iframe-past_'+id).dispose();
       		document.getElementById('ynevent_list_time_past_'+id).set('class','ynevent_list-view');
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
			        ynevent_view_map_p_time(<?php echo $this -> identity;?>);
			        break;
			    case 'grid_view':
			        ynevent_view_grid_p_time(<?php echo $this -> identity;?>);
			        break;
			 	case 'list_view':
			        ynevent_view_list_p_time(<?php echo $this -> identity;?>);
			        break;
		    }
			
			// Set click viewMode
			$$('.ynevent_home_page_list_content_<?php echo $this -> identity;?>').addEvent('click', function(){
				var viewmode = this.get('rel');
				setCookie('ynevent-viewmode-cookie_<?php echo $this -> identity;?>', viewmode, 1);
			});
		});
		
</script>