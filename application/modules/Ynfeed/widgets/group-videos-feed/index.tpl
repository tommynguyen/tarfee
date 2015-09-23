<?php if(empty($this->getUpdate) && empty($this->checkUpdate)): ?>
<script type="text/javascript">
	var addMoreOptions = function() 
	{
		var ynfeed_pulldown = this.getParent('.ynfeed_pulldown_btn_wrapper');
	    if (!ynfeed_pulldown.hasClass('ynfeed_pulldown_open') ) 
	    {
	      $$('.ynfeed_pulldown_open').each(function(item, index)
	      {
	        item.removeClass('ynfeed_pulldown_open');
	      }); 
	      ynfeed_pulldown.addClass('ynfeed_pulldown_open');
	    } 
	    else {      
	      ynfeed_pulldown.removeClass('ynfeed_pulldown_open');
		}
	}
	
	var openCommentOptions = function() 
    {
        var yncomment_pulldown = this.getParent('.yncomment_replies_pulldown');
        if (!yncomment_pulldown.hasClass('yncomment_replies_pulldown_open') ) 
        {
          $$('.yncomment_replies_pulldown_open').each(function(item, index)
          {
            item.removeClass('yncomment_replies_pulldown_open');
          }); 
          yncomment_pulldown.addClass('yncomment_replies_pulldown_open');
        } 
        else {      
          yncomment_pulldown.removeClass('yncomment_replies_pulldown_open');
        }
    }
	/**
	 * extends Mootools component
	 */
	Element.implement({
		addLiveEvent : function(event, selector, fn) {
			this.addEvent(event, function(e) {
				var t = $(e.target);
				if(!t.match(selector))
					return ;
				if(typeof fn.apply !== "undefined")
				    fn.apply(t, [e]);
			}.bindWithEvent(this, selector, fn));
		}
	});
  // create some phrases language
  var langs = {
              "with" : '<?php echo $this->string()->escapeJavascript($this->translate("with")) ?>',
              "at" : '<?php echo $this->string()->escapeJavascript($this->translate("at")) ?>',
              "others" : '<?php echo $this->string()->escapeJavascript($this->translate("others")) ?>',
              "and" : '<?php echo $this->string()->escapeJavascript($this->translate("and")) ?>',
              "Undo" : '<?php echo $this->string()->escapeJavascript($this->translate("Undo")) ?>',
              "hide_all_by" : '<?php echo $this->string()->escapeJavascript($this->translate("Hide all by")) ?>',
              "message_hide_from_user" : '<?php echo $this->string()->escapeJavascript($this->translate("Feeds from %s are hidden now and will not appear in your Activity Feed anymore.")) ?>',
              'message_hide_feed': '<?php echo $this->string()->escapeJavascript($this->translate("This feed  is now hidden from your Activity Feed")) ?>',
              'message_report': '<?php echo $this -> string() -> escapeJavascript($this -> translate("To mark it offensive, please %s"))?>',
              'file a report': '<?php echo $this -> string() -> escapeJavascript($this -> translate("file a report"))?>',
              'Write a reply...': '<?php echo $this -> string() -> escapeJavascript($this -> translate("Write a reply..."))?>'
           };
    window.addEvent('domready', function()
    {
    	 $(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox i', openCommentOptions);
    });
	var countAutoload = <?php echo $this->countAutoload?>;
	var end = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;
	var loadMore = true;
	var noFeed = <?php echo ( $this->noFeed ? 'true' : 'false' ) ?>;
	
	<?php if($this -> autoLoadMore):?>
		var spyContainer = window;
		var min = spyContainer.getScrollSize().y - spyContainer.getSize().y -50; /*- 150 tolerance */
		new ScrollSpy({
		    container: spyContainer,
		    min: min,
		    onEnter: function() {
		            if ($('nothing-tip2') && $('nothing-tip2').getStyle('display') == 'block') return;
		            if (!end && countAutoload <= <?php echo $this->max_times ?> && loadMore)
		            {
		                $('feed_viewmore_link').fireEvent('click');
		            } 
		            else 
		            {}
		        }
		});
	<?php endif;?>
//End
</script>
<?php endif;?>
<?php if( (!empty($this->feedOnly) || !$this->endOfFeed ) &&
    (empty($this->getUpdate) && empty($this->checkUpdate)) ): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      var next_id = <?php echo sprintf('%d', $this->nextid) ?>;
      var subject_guid = '<?php echo $this->subjectGuid ?>';
      var endOfFeed = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;
      var activityViewMore = window.activityViewMore = function(next_id, subject_guid) {
        if( en4.core.request.isRequestActive() ) return;
        
        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';         
        $('feed_viewmore').style.display = 'none';
        $('feed_loading').style.display = '';
        
          var request = new Request.HTML({
          url : url,
          data : {
            format : 'html',
            'maxid' : next_id,
            'feedOnly' : true,
            'nolayout' : true,
            'subject' : subject_guid,
            'countAutoload':<?php echo $this->countAutoload?>,
            'actionFilter': '<?php echo $this->actionFilter;?>',
            'filterValue': '<?php echo $this->filterValue;?>'
          },
          evalScripts : true,
          onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            Elements.from(responseHTML).inject($('activity-feed'));
            en4.core.runonce.trigger();
            Smoothbox.bind($('activity-feed'));
            $(document.body).addLiveEvent('click', 'span.ynfeed_pulldown_btn', addMoreOptions);
            $(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox i', openCommentOptions);
          }
        });
       request.send();
      }
      
      if( next_id > 0 && !endOfFeed ) 
      {
        $('feed_viewmore').style.display = '';
        $('feed_loading').style.display = 'none';
        $('feed_viewmore_link').removeEvents('click').addEvent('click', function(event)
        {
          if (event != undefined)
          {
	         event.stop();
	      }
          activityViewMore(next_id, subject_guid);
        });
      } 
      else 
      {
      	loadMore = false;
      	$('feed_viewmore').style.display = 'none';
        $('feed_loading').style.display = 'none';
      }
      
    });
  </script>
<?php endif; ?>

<?php if( !empty($this->feedOnly) && empty($this->checkUpdate)): // Simple feed only for AJAX
  echo $this->ynfeedLoop($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
    'feedOnly' =>$this->feedOnly,
    'getUpdate' => $this->getUpdate,
    'openHide' => $this->openHide,
  ));
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if( !empty($this->getUpdate) ): // if this is for the get live update ?>
   <script type="text/javascript">
     activityUpdateHandler.options.last_id = <?php echo sprintf('%d', $this->firstid) ?>;
   </script>
<?php endif; ?>

<?php if( $this->enableComposer )
  echo $this->partial('_ynfeedcomposer.tpl', 'ynfeed', array(
        'enableComposer' => $this->enableComposer,
        'hasPrivacy' => $this->hasPrivacy,
        'hasTag' => $this->hasTag,
        'composePartials' => $this->composePartials,
        'formToken' => $this->formToken,
        'friendUsers' => $this->friendUsers,
        'showDefault' =>  true 
    ));
?>
<?php if( $this->viewer()->getIdentity() ): ?>
   <script type="text/javascript">
          en4.user.viewer.iconUrl = '<?php echo $this->viewer()->getPhotoUrl('thumb.icon'); ?>';
          en4.user.viewer.title = '<?php echo $this->string()->escapeJavascript($this->viewer()->getTitle()); ?>';
          en4.user.viewer.href = '<?php echo $this->string()->escapeJavascript($this->viewer()->getHref()); ?>';

          if(!en4.user.viewer.iconUrl){
            en4.user.viewer.iconUrl=en4.core.staticBaseUrl + 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';
          }
          en4.ynfeed.fewSecHTML='<?php echo str_replace('timestamp-update','timestamp-fixed',$this->timestamp(time()-2)); ?>';
    </script>
<?php endif; ?>

<?php if( $this->post_failed == 1 ): ?>
  <div class="ynfeed-tip">
    <span>
      <?php $url = $this->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'privacy'), 'default', true) ?>
      <?php echo $this->translate('The post was not added to the feed. Please check your %1$sprivacy settings%2$s.', '<a href="'.$url.'">', '</a>') ?>
    </span>
  </div>
<?php endif; ?>

<div id="feed_loading2" class="ynfeed_loading" style="display: none;">
  <img src='application/modules/Ynfeed/externals/images/loading.gif' style='margin-right: 5px;' />
</div>
<?php // If requesting a single action and it doesn't exist, show error ?>
<?php if( !$this->activity  && $this->actionFilter =='all'): ?>
  <?php if( $this->action_id ): ?>
    <h2><?php echo $this->translate("Activity Item Not Found") ?></h2>
    <p>
      <?php echo $this->translate("The page you have attempted to access could not be found.") ?>
    </p>
  <?php return; else: ?>
    <div class="ynfeed-tip">
      <span>
        <?php echo $this->translate("Nothing has been posted here yet - be the first!") ?>
      </span>
    </div>
  <?php return; endif; ?>
 <?php elseif(!$this->activity): ?>
    <ul class='feed' id="activity-feed"></ul>
<?php endif; ?>

<?php echo $this->ynfeedLoop($this->activity, array(
  'action_id' => $this->action_id,
  'viewAllComments' => $this->viewAllComments,
  'viewAllLikes' => $this->viewAllLikes,
  'getUpdate' => $this->getUpdate,
  'openHide' => $this->openHide,
)) ?>

<div class="feed_viewmore" id="feed_viewmore" style="display: none;">
  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
    'id' => 'feed_viewmore_link',
    'class' => 'buttonlink icon_viewmore'
  )) ?>
</div>

<div class="ynfeed_loading" id="feed_loading" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Ynfeed/externals/images/loading.gif' />
</div>
<div class="ynfeed_feed_tip" id="feed_no_more" style="display: <?php echo (!$this->endOfFeed || !empty($this->action_id)) ? 'none':'block'?>;"> 
  <?php echo $this->translate("There are no more posts to show.") ?>
</div>
