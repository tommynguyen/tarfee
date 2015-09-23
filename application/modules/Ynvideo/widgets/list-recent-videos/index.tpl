<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<div id="ynvideo_recent_videos_container">
<?php $videoCount = $this->paginator->getTotalItemCount(); ?>
<?php if ($videoCount > 0) : ?>
    <script type="text/javascript">
        en4.core.runonce.add(function(){
            addEventForButtonAddTo();
            <?php if (!$this->renderOne): ?>
                var anchor = $('ynvideo_recent_videos_container');
                
                $('ynvideo_videos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
                $('ynvideo_videos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

                $('ynvideo_videos_previous').removeEvents('click').addEvent('click', function(){
                    en4.core.request.send(new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                        data : {
                            format : 'html',
                            subject : en4.core.subject.guid,
                            page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                    	},
                    	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        	Elements.from(responseHTML).replaces(anchor);  
                        	eval(responseJavaScript);                      	
                    	}
                	}));
                });

                $('ynvideo_videos_next').removeEvents('click').addEvent('click', function(){
                    en4.core.request.send(new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                        data : {
                            format : 'html',
                            subject : en4.core.subject.guid,
                            page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                        },
                        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        	Elements.from(responseHTML).replaces(anchor);
                        	eval(responseJavaScript);                      	
                    	}
                    })); 
                });            
            <?php endif; ?>
        });
    </script>

    <ul class="generic_list_widget ynvideo_widget videos_browse ynvideo_frame ynvideo_list" id="ynvideo_recent_videos">
        <?php foreach ($this->paginator as $item) : ?>
            <li class="video-item">
				<?php
		        		echo $this->partial('_players_of_week.tpl', 'ynvideo', array(
		        			'video' => $item
		        		));
		            ?>
			</li>
        <?php endforeach; ?>        
    </ul>
	
	<div>
        <div id="ynvideo_videos_previous" class="paginator_previous">
            <?php
            	echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            		'onclick' => '',
            		'class'   => 'buttonlink icon_previous'
            	));
            ?>
        </div>
        <div id="ynvideo_videos_next" class="paginator_next">
            <?php
            	echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
            		'onclick' => '',
            		'class'   => 'buttonlink link_ynvideo_right icon_next'
            	));
            ?>
        </div>
        <div class="clear"></div>
	</div>	
    
<?php else : ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('You do not have any videos.'); ?>
            <?php if ($this->can_create) : ?>
                <?php echo $this->translate('Get started by %1$sposting%2$s a new video.', '<a href="' . $this->url(array('action' => 'create')) . '">', '</a>'); ?>
            <?php endif; ?>
        </span>
    </div>
<?php endif; ?>
</div>
