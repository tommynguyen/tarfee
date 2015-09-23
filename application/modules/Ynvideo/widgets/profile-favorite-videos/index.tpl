<?php
    $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.min.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.wookmark.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.imagesloaded.js');
?>

<script type="text/javascript">    
    en4.core.runonce.add(function(){
        <?php if (!$this->renderOne): ?>
            var anchor = $('ynvideo_favorite_videos').getParent();
            $('ynvideo_fav_videos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('ynvideo_fav_videos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('ynvideo_fav_videos_previous').removeEvents('click').addEvent('click', function(){
                en4.core.request.send(new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data : {
                        format : 'html',
                        subject : en4.core.subject.guid,
                        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                    },
                    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                    	anchor.innerHTML = responseHTML;
                    	setPin();
                    }
                }))
            });

            $('ynvideo_fav_videos_next').removeEvents('click').addEvent('click', function(){
                en4.core.request.send(new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data : {
                        format : 'html',
                        subject : en4.core.subject.guid,
                        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                    },
                    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                    	anchor.innerHTML = responseHTML;
                    	setPin();
                    }
                }))
            });
        <?php endif; ?>
    });
</script>

<ul id="ynvideo_favorite_videos">
    <?php foreach ($this->paginator as $item): ?>
        <li id="favorite_video_<?php echo $item -> getIdentity()?>">
            <?php
                echo $this->partial('_video_listing_favorite.tpl', 'ynvideo', array('video' => $item));
            ?>
        </li>
<?php endforeach; ?>
</ul>

<script type="text/javascript">
    function setPin(){
        jQuery.noConflict();
        (function (jQuery){
            var handler = jQuery('#ynvideo_favorite_videos li');

            handler.wookmark({
                // Prepare layout options.
                autoResize: true, // This will auto-update the layout when the browser window is resized.
                container: jQuery('#ynvideo_favorite_videos'), // Optional, used for some extra CSS styling
                offset: 10, // Optional, the distance between grid items
                outerOffset: 0, // Optional, the distance to the containers border
                itemWidth: 200, // Optional, the width of a grid item
                flexibleWidth: '100%',
            });
        })(jQuery);
    };

    $$('.tab_layout_ynvideo_profile_favorite_videos').addEvent('click',function(){
        setPin();
    })
    window.addEvent('domready', function()
    {
    	setPin();
    });

</script>

<script type="text/javascript">
   var unfavorite_video = function(videoId)
   {
   	   var url = '<?php echo $this -> url(array('action' => 'remove-favorite'), 'video_favorite', true)?>';
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  url,
            'data' : {
                'video_id' : videoId
            },
            'onSuccess':function(responseJSON, responseText)
            {  
            	$('favorite_video_' + videoId).destroy();
            	setPin();
            }
        });
        request.send();  
   } 
</script>
<div class="ynvideofa-paginator" style="margin-top: 10px">
    <div id="ynvideo_fav_videos_previous" class="paginator_previous">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => '',
            'class' => 'buttonlink icon_previous'
        ));
        ?>
    </div>
    <div id="ynvideo_fav_videos_next" class="paginator_next">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
            'onclick' => '',
            'class' => 'buttonlink_right icon_next',
            'style' => 'display: initial'
        ));
        ?>
    </div>
</div>


