<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
 $this -> headScript()
 	-> appendFile($this->baseUrl().'/application/modules/Ynvideo/externals/scripts/iscroll.js');
?>
    function nextSlideNewPlaylist() {
        jQuery('#ynvideo_addTo_menu_list > div').hide();
        jQuery('#ynvideo_addTo_new_playlist').show();
    }
    
    function nextSlideAddSuccessfully() {
        jQuery('#ynvideo_addTo_menu_list > div').hide();
        jQuery('#ynvideo_addTo_new_playlist_successfully').show();
    }
    
    function nextSlideAddToList() {
        jQuery('#ynvideo_addTo_menu_list > div').hide();
        jQuery('#ynvideo_addTo_list').show();
    }
    
    function nextSlideAddDupplicate(message) {
        jQuery('#ynvideo_addTo_menu_list > div').hide();
        jQuery('#ynvideo_addTo_dupplicate').show();
    }
    
    function nextSlideAddUnsucessfully() {
        jQuery('#ynvideo_addTo_menu_list > div').hide();
        jQuery('#ynvideo_addTo_unsuccessfully').show();
    }
    
    function nextSlideLoading() {
        jQuery('#ynvideo_addTo_menu_list > div').hide();
        jQuery('#ynvideo_addTo_frame_loading').show();
    }
    
    function nextSlideErrorOccured(message) {
        jQuery('#ynvideo_addTo_menu_list > div').hide();
        jQuery('#ynvideo_addTo_unsuccessfully').show();
        if (message) {
            jQuery('#ynvideo_addTo_unsuccessfully').find('.ynvideo_addTo_result_text').text(message);
        }
    }
    
    function afterAddedToPlaylist(data) 
    {
        if (data.result == '1') {
            nextSlideAddSuccessfully();                            
            jQuery('#ynvideo_addTo_playlist_successfully').html(data.message);
        } else if (data.result == '0') {
            nextSlideErrorOccured(data.message);
        } else if (data.result == '-1') { // dupplicate error
            nextSlideAddDupplicate(data.message);                                    
        }
    }
    
    function addEventForButtonAddTo() 
    {
        jQuery('button.ynvideo_add_button').unbind('click').bind('click', function(e) 
        {
            e.stopPropagation();
            // remove the add to frame if it is existed on the document
            jQuery('#ynvideo_addTo_menu_list').remove();
            
            var videoId = jQuery(this).attr('video-id');
            var btnOffset = jQuery(this).parent().offset();
            var bodyElement = jQuery('body')[0];
            var addToContextMenu =  jQuery(bodyElement)
                .append('<div id="ynvideo_addTo_menu_list"><div class="ynvideo_addTo_frame" id="ynvideo_addTo_frame_loading"><div id="ynvideo_addTo_loading"><?php echo $this->translate('LOADING')?></div></div></div>');
            jQuery('#ynvideo_addTo_menu_list').css({
                        'left' : btnOffset.left,
                        'top' : btnOffset.top + jQuery(this).outerHeight()
                    });  
            
            jQuery.get(
                '<?php echo $this->url(array('action' => 'add-to'), 'video_general', true) ?>', 
                {'video_id' : videoId},
                function(data) {                    
                    var addToContextMenu = jQuery(data).insertAfter('#ynvideo_addTo_frame_loading');                
                    
                    jQuery(document).bind('click', function(event) {
                        var target = event.target;
                        // if the user click outside the add to menu box, remove the add to menu box
                        if (jQuery('#ynvideo_addTo_menu_list').has(event.target).length == 0) {
                            jQuery('#ynvideo_addTo_menu_list').remove();
                            jQuery(document).unbind('click');
                        }
                    });
                    
                    jQuery('#ynvideo_menu_item_add_to_new_playlist').click(function() {
                        nextSlideNewPlaylist();    
                    });
                    
	                    jQuery('#ynvideo_menu_item_cancel').click(function() {
	                         jQuery('#ynvideo_addTo_menu_list > div').hide();
	                    });
	                    jQuery('#ynvideo_menu_item_cancel_successfully').click(function() {
	                         jQuery('#ynvideo_addTo_menu_list > div').hide();
	                    });
	                    jQuery('#ynvideo_menu_item_cancel_dupplicate').click(function() {
	                         jQuery('#ynvideo_addTo_menu_list > div').hide();
	                    });
	                    jQuery('#ynvideo_menu_item_cancel_unsuccessfully').click(function() {
	                         jQuery('#ynvideo_addTo_menu_list > div').hide();
	                    });

					
                    jQuery('#ynvideo_quick_create_playlist').submit(function() {                       
                        var params = jQuery(this).serializeArray();
                        var action = jQuery(this).attr('action');
                        nextSlideLoading();
                        jQuery.post(action, params, function(data) {
                            afterAddedToPlaylist(data);
                            return false;    
                        });
                        return false;
                    });

                    jQuery('#ynvideo_add_to_watch_later').click(function() {                    
                        nextSlideLoading();
                        jQuery.post(
                            '<?php echo $this->url(array('action' => 'add-to'), 'video_watch_later') ?>', 
                            {'video_id' : videoId},
                            afterAddedToPlaylist
                        );            
                        
                    });

                    jQuery('#ynvideo_addTo_list .ynvideo_menu_item_playlist').click(function() {
                        nextSlideLoading();
                        var playlistId = jQuery(this).attr('playlist');
                        jQuery.post(
                            '<?php echo $this->url(array('action' => 'add'), 'video_playlist') ?>', 
                            {'playlist_id' : playlistId, 'video_id' : videoId},
                            afterAddedToPlaylist
                        );
                    });
                    
                    jQuery('.ynvideo_add_to_favorite_menu_item').click(function() {
                        nextSlideLoading();
                        jQuery.post(
                            '<?php echo $this->url(array('action' => 'add'), 'video_favorite')?>',
                            {'video_id' : videoId},
                            afterAddedToPlaylist
                        );
                    });
                }
            );
        });
    }
    
    
    jQuery(document).ready(function() {
        addEventForButtonAddTo();
    });