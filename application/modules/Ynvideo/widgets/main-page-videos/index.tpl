<?php
    $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.min.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.wookmark.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.imagesloaded.js');
?>

<ul class = "video-items" id="main-page-videos">
<?php $count = 1;?>
<?php foreach( $this->results as $row): ?>
	<?php if ($count > $this->limit) break;?>
	<li class="video-item">
		<?php
        		echo $this->partial('_video_listing_mainpage.tpl', 'ynvideo', array(
        			'video'     => $row
        		));
            ?>
	</li>
	<?php $count++;?>
<?php endforeach;
Engine_Api::_() -> core() -> clearSubject();
?>
</ul>
<?php if ($this-> count > $this->limit && !$this->reachLimit):?>

  <span style="cursor:pointer" id="video-viewmore-btn" class="tf_button_action" onclick="showMore(<?php echo ($this->limit + $this->from)?>)"><?php echo $this->translate('View More') ?></span>

<div id="video-loading" style="display: none;">
	<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left;margin-right: 5px;' />
</div>
<script type="text/javascript">
function showMore(from){
    var url = '<?php echo $this->url(array('module' => 'core','controller' => 'widget','action' => 'index','name' => 'ynvideo.main-page-videos'), 'default', true) ?>';
    $('video-viewmore-btn').destroy();
    $('video-loading').style.display = 'inline-block';
    var params = {};
    params.format = 'html';
    params.from = from;
    params.strIds = '<?php echo $this -> strIds;?>';
    var request = new Request.HTML({
      	url : url,
      	data : params,
      	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        	$('video-loading').destroy();
            var result = Elements.from(responseHTML);
            var results = result.getElement('#main-page-videos').getChildren();
            $('main-page-videos').adopt(results);
            var viewMore = result.getElement('#video-viewmore-btn');
            if (viewMore[0]) viewMore.inject($('main-page-videos'), 'after');
            var loading = result.getElement('#video-loading');
            if (loading[0]) loading.inject($('main-page-videos'), 'after');
            eval(responseJavaScript);
            setPin();
        }
    });
   request.send();
  }

</script>
<?php endif;?>	

<script type="text/javascript">
   var unfavorite_video = function(videoId)
   {
   	   var obj = document.getElementById('favorite_' + videoId);
   	   obj.innerHTML = '<a href="javascript:;" style="background:#ff6633; color: #fff"><img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" /></a>';
   	   var url = '<?php echo $this -> url(array('action' => 'remove-favorite'), 'video_favorite', true)?>';
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  url,
            'data' : {
                'video_id' : videoId
            },
            'onComplete':function(responseObject)
            {  
                obj.innerHTML = '<a href="javascript:;" title="<?php echo $this->translate("Favourite")?>" onclick="favorite_video('+videoId+')">' + '<i class="fa fa-heart-o"></i>' + '</a>';
            }
        });
        request.send();  
   } 
   var favorite_video = function(videoId)
   {
   	   var obj = document.getElementById('favorite_' + videoId);
   	   obj.innerHTML = '<a href="javascript:;"><img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" /></a>';
   	   var url = '<?php echo $this -> url(array('action' => 'add-favorite'), 'video_favorite', true)?>';
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  url,
            'data' : {
                'video_id' : videoId
            },
            'onComplete':function(responseObject)
            {  
                obj.innerHTML = '<a href="javascript:;" style="background:#ff6633;color: #fff" title="<?php echo $this->translate("Unfavourite")?>" onclick="unfavorite_video('+videoId+')">' + '<i class="fa fa-heart"></i>' + '</a>';
            }
        });
        request.send();  
   }
   
   var tempLike = 0;
   var video_like = function(id, action)
   {
   		if (tempLike == 0) 
   		{
   			tempLike = 1;
   			if ($(action + '_video_' + id)) {
				$(action + '_video_' + id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			var url = en4.core.baseUrl + 'ynvideo/video/' + action;
   			en4.core.request.send(new Request.JSON({
				url : url,
				data : {
					format : 'json',
					id : id
				},
				onComplete : function(e) {
					tempLike = 0;
				}
			}), {
				'element' : $('like_unsure_dislike_' + id)
			});
		}
   }
</script>

<script type="text/javascript">
	var setPin = function()
	{
	    jQuery.noConflict();
	    (function (jQuery){
	        var handler = jQuery('#main-page-videos li');
	
	        handler.wookmark({
	            // Prepare layout options.
	            autoResize: true, // This will auto-update the layout when the browser window is resized.
	            container: jQuery('#main-page-videos'), // Optional, used for some extra CSS styling
	            offset: 10, // Optional, the distance between grid items
	            outerOffset: 0, // Optional, the distance to the containers border
	            itemWidth: 225, // Optional, the width of a grid item
	            flexibleWidth: '50%',
	        });
	    })(jQuery);
	}
	setPin();
</script>

