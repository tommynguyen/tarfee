<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
    <div class="ynvideo_addTo_frame" id="ynvideo_addTo_list">
        <?php if ($this->loggedIn) : ?>
            <div class="ynvideo_addTo_watchlater">
                <div class="ynvideo_add_to_menu_item" id="ynvideo_add_to_watch_later">
                    <?php echo $this->translate('Watch Later') ?>
                </div>
            </div>
            <div class="ynvideo_addTo_playlists" id="ymbScrollPlaylist">
            	<div>
                <div class="ynvideo_addTo_text">
                    <?php echo $this->translate('Add to') ?>
                </div>
                <div class="ynvideo_add_to_menu_item ynvideo_add_to_favorite_menu_item">
                    <?php echo $this->translate('Favorite') ?>
                </div>
                <?php foreach ($this->playlists as $playlist) : ?>
                    <div class="ynvideo_add_to_menu_item ynvideo_menu_item_playlist" 
                         playlist="<?php echo $playlist->getIdentity() ?>">
                             <?php echo $playlist->title ?>
                    </div>
               <?php endforeach; ?>
               </div>
            </div>
            <div class="ynvideo_addTo_newplaylist">
                <div class="ynvideo_add_to_menu_item" id="ynvideo_menu_item_add_to_new_playlist">
                    <?php echo $this->translate('Add to new playlist') ?>
                </div>
            </div>
            <div class="ynvideo_addTo_cancel">
                <div class="ynvideo_add_to_menu_item" id="ynvideo_menu_item_cancel">
                    <?php echo $this->translate('Cancel') ?>
                </div>
            </div>
        <?php else : ?>
            <div class="ynvideo_addTo_result_block">
                <?php
                echo $this->htmlLink(
                        array('route' => 'user_login'), 'Sign In') . ' ' . $this->translate('to add this video to a playlist')
                ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($this->loggedIn) : ?>
        <div class="ynvideo_addTo_frame" id="ynvideo_addTo_new_playlist" style="display: none;">
            <?php if ($this->form) : ?>
                <?php echo $this->form->render(); ?>   
            <?php endif; ?>
        </div>

        <div class="ynvideo_addTo_frame" id="ynvideo_addTo_new_playlist_successfully" style="display: none;">
            <div class="ynvideo_addTo_result_block">
                <div class="ynvideo_addTo_result_text ynvideo_addTo_successfully">            
                    <?php echo $this->translate('Added to') ?>
                </div>

                <p class="ynvideo_addTo_playlist_text" id="ynvideo_addTo_playlist_successfully"></p>
            </div>
            <div class="ynvideo_addTo_cancel">
                <div class="ynvideo_add_to_menu_item" id="ynvideo_menu_item_cancel_successfully">
                    <?php echo $this->translate('Close') ?>
                </div>
            </div>
        </div>

        <div class="ynvideo_addTo_frame" id="ynvideo_addTo_dupplicate" style="display:none;">
            <div class="ynvideo_addTo_result_block">
                <div class="ynvideo_addTo_result_text ynvideo_addTo_dupplicate">            
                    <?php echo $this->translate('Duplicates are not allowed for this playlist.') ?>
                </div>

                <p class="ynvideo_addTo_playlist_text" id="ynvideo_addTo_playlist_dupplicate">
                    <a href="javascript:void" onclick="nextSlideAddToList()">
                        <?php echo $this->translate('Back to playlist') ?>
                    </a>
                </p>
            </div>
            <div class="ynvideo_addTo_cancel">
                <div class="ynvideo_add_to_menu_item" id="ynvideo_menu_item_cancel_dupplicate">
                    <?php echo $this->translate('Close') ?>
                </div>
            </div>
        </div> 
    
        <div class="ynvideo_addTo_frame" id="ynvideo_addTo_unsuccessfully" style="display:none;">
            <div class="ynvideo_addTo_result_block">
                <div class="ynvideo_addTo_result_text ynvideo_addTo_unsuccessfully">            
                    <?php echo $this->translate('There is an error occured. Please try again !!!') ?>
                </div>
            </div>
            <div class="ynvideo_addTo_cancel">
                <div class="ynvideo_add_to_menu_item" id="ynvideo_menu_item_cancel_unsuccessfully">
                    <?php echo $this->translate('Close') ?>
                </div>
            </div>
        </div>  
    <?php endif; ?>
	<script type="text/javascript">
		var myScroll;
		jQuery(document).ready(function() {
	        setTimeout(function () {
				myScroll = new iScroll('ymbScrollPlaylist');
			}, 200);
	    });
	</script>