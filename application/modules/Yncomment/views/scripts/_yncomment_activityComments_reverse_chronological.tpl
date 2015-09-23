<?php
if (empty($this->actions)) {
  echo $this->translate("The action you are looking for does not exist.");
  return;
} else {
  $actions = $this->actions;
}
 $this->addHelperPath(APPLICATION_PATH . '/application/modules/Yncomment/View/Helper', 'Yncomment_View_Helper');
 $photoEnabled = false;
 $smiliesEnabled = false;
 $activityTaggingContent = false;
 $row = Engine_Api::_()->yncomment()->getEnabledModule(array('resource_type' => 'ynfeed', 'checkModuleExist' => true));
 if($row){
    $ynactivityParams = $row->params;
    $decodedParams = Zend_Json_Decoder::decode($ynactivityParams);
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')
     || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advalbum'))
    {
      $photoEnabled = in_array("addPhoto", $decodedParams['showComposerOptions']);
    }
    $smiliesEnabled = in_array("addSmilies", $decodedParams['showComposerOptions']);
    $linkEnabled = in_array("addLink", $decodedParams['showComposerOptions']);
    $showAsLike = $decodedParams['showAsLike'];
    $showDislikeUsers = $decodedParams['showDislikeUsers'];
    $showLikeWithoutIcon = $decodedParams['showLikeWithoutIcon'];
    $showLikeWithoutIconInReplies = $decodedParams['showLikeWithoutIconInReplies'];
    $activityTaggingContent = implode($decodedParams['taggingContent'], ",");
    $this->commentShowBottomPost =  $decodedParams['ynfeed_comment_show_bottom_post']; 
    $this->ynfeed_comment_like_box =  $decodedParams['ynfeed_comment_like_box']; 
 }?>

<?php
    $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yncomment/externals/scripts/composer_feed_comment.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yncomment/externals/scripts/composer_feed_comment_tag.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yncomment/externals/scripts/comment_photo.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Activity/externals/scripts/core.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');

    $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Yncomment/externals/styles/style_yncomment.css');
?>
<script type="text/javascript">
    var CommentLikesTooltips;
    var photoEnabled = '<?php echo $photoEnabled ?>';
    var linkEnabled = '<?php echo $linkEnabled ?>';
    var smiliesEnabled = '<?php echo $smiliesEnabled ?>';
    var requestOptionsURLYnComment = en4.core.baseUrl + 'yncomment/album/compose-upload/type/comment';
    var fancyUploadOptionsURLYnComment = en4.core.baseUrl + 'yncomment/album/compose-upload/format/json/type/comment';
    var allowQuickComment= '<?php echo (!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? 0: 1 ;?>';
    var allowQuickReply = '<?php echo (!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? 0 : 1; ?>';
    en4.core.runonce.add(function() {
    // Add hover event to get likes
    $$('.comments_comment_likes').addEvent('mouseover', function(event) {
      var el = $(event.target);
      if( !el.retrieve('tip-loaded', false) ) {
        el.store('tip-loaded', true);
        el.store('tip:title', '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>');
        el.store('tip:text', '');
        var id = el.get('id').match(/\d+/)[0];
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'get-likes'), 'default', true) ?>';
        var req = new Request.JSON({
          url : url,
          data : {
            format : 'json',
            action_id : el.getParent('li').getParent('li').getParent('li').get('id').match(/\d+/)[0],
            comment_id : id
          },
          onComplete : function(responseJSON) {
            el.store('tip:title', responseJSON.body);
            el.store('tip:text', '');
            CommentLikesTooltips.elementEnter(event, el); // Force it to update the text
          }
        });
        req.send();
      }
    });
    // Add tooltips
    CommentLikesTooltips = new Tips($$('.comments_comment_likes'), {
      fixed : true,
      className : 'comments_comment_likes_tips',
      offset : {
        'x' : 20,
        'y' : 10
      }
    });
    // Enable links in comments
    $$('.comments_body').enableLinks();     
  });   
  if ( typeof show_all_comments == 'undefined')
  {
       var show_all_comments = {};
  }
  if ( typeof comment_like_box_show == 'undefined')
  {
       var comment_like_box_show = {};
  }
</script>
<?php
  $optionFeedTable =  Engine_Api::_() -> getDbTable('optionFeeds', 'ynfeed');
  $feedApiCore = Engine_Api::_ ()->ynfeed ();
  foreach( $actions as $action ): // (goes to the end of the file)
    try { // prevents a bad feed item from destroying the entire page
      // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
      if( !$action->getTypeInfo()->enabled ) continue;
      if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
      if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
      
      ob_start();
      list($friendIds, $friends) = $feedApiCore -> getWithFriends($action -> getIdentity());
    ?>
     <?php $item = (isset($action->getTypeInfo()->is_object_thumb) && !empty($action->getTypeInfo()->is_object_thumb)) ? $action->getObject() : $action->getSubject(); ?>

    <?php if($this->onViewPage): $actionBaseId="view-".$action->action_id; else:$actionBaseId=$action->action_id;endif;?>
    <?php $this->commentForm->setActionIdentity($actionBaseId);
    $this->commentForm->action_id->setValue($action->action_id);?>
    <script type="text/javascript">
     show_all_comments[<?php echo $action->action_id?>] = '<?php echo $this -> viewAllComments?>';
     comment_like_box_show[<?php echo $action->action_id?>] = '<?php echo $this->ynfeed_comment_like_box_show?>';
      (function(){
        en4.core.runonce.add(function(){
          $('<?php echo $this->commentForm->body->getAttrib('id') ?>').autogrow();  
           if(allowQuickComment == '1' && <?php echo $this->submitComment ? '1': '0' ?>){ 
              if(document.getElementById("feed-comment-form-open-li_<?php echo $actionBaseId ?>")){
                document.getElementById("feed-comment-form-open-li_<?php echo $actionBaseId ?>").style.display = "block";}  
            }
            <?php if( Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfeed.comment.like.box', 0)):?>$('comment-likes-ac_activityCommentstivityboox-item-<?php echo $action->action_id;?>').toggle();  <?php endif; ?>
            
        });
      })();
    </script>
    <?php // Icon, time since, action links ?>
      <?php
        $icon_type = 'activity_icon_'.$action->type;
        list($attachment) = $action->getAttachments();
        if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
          $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
        endif;
        $canComment = ( $action->getTypeInfo()->commentable &&
            $this->viewer()->getIdentity() &&
            Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
            !empty($this->commentForm) &&
            (!$optionFeedTable -> getOptionFeed($action->getSubject(), $action->action_id, 'comment') || $this->viewer()->isSelf($action->getSubject())) &&
            (!$optionFeedTable -> getOptionFeed($action->getSubject(), $action->action_id, 'lock') || $this->viewer()->isSelf($action->getSubject())));
      ?>
      <div class='feed_item_date feed_item_icon <?php echo $icon_type ?> yncomment_feed_item_date'>
        <ul>
         <!-- // Comment date -->
          <li>
                <?php echo $this->timestamp($action->getTimeValue()) ?>
          </li>
          <!--// End comment -->
          
          <?php if( $canComment ): ?>
             <!-- // Like/dislike -->
            <?php if($showAsLike):?>
                <?php if( $action->likes()->isLike($this->viewer()) ): ?>
                  <li class="feed_item_option_unlike">
                    <span>-</span>
                    <?php if(!$showLikeWithoutIcon):?> 
                        <?php echo $this->htmlLink('javascript:void(0);', '<i class="fa fa-thumbs-down ynfontawesome"></i> '.$this->translate('Unlike'), array('onclick'=>'javascript:en4.ynfeed.unlike(this, '.$action->action_id.');', 'title' => $this->translate('Unlike'), 'action-title' => '<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />')) ?>
                    <?php else:?>
                        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('onclick'=>'javascript:en4.ynfeed.unlike(this, '.$action->action_id.');', 'title' => $this->translate('Unlike'), 'action-title' => '<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />')) ?>
                    <?php endif;?>
                  </li>
                <?php else: ?>
                  <li class="feed_item_option_like">
                    <span>-</span>
                    <?php if(!$showLikeWithoutIcon):?> 
                        <?php echo $this->htmlLink('javascript:void(0);', '<i class="fa fa-thumbs-up ynfontawesome"></i> '.$this->translate('Like'), array('onclick'=>'javascript:en4.ynfeed.like(this, '.$action->action_id.');', 'title' => $this->translate('Like'), 'action-title' => '<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />')) ?>
                    <?php else:?>
                        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick'=>'javascript:en4.ynfeed.like(this, '.$action->action_id.');', 'title' => $this->translate('Like'), 'action-title' => '<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />')) ?>
                    <?php endif;?>
                  </li>
                <?php endif; ?>
             <?php else :?>
                  <?php if (!$action->likes()->isLike($this->viewer())): ?>
                    <li class="feed_item_option_like"> 
                        <span>-</span>
                        <?php if(!$showLikeWithoutIcon):?>     
                            <?php echo $this->htmlLink('javascript:void(0);', '<i class="fa fa-thumbs-up ynfontawesome"></i> '.$this->translate('Like'), array('onclick' => 'javascript:en4.ynfeed.like(this,' . $action->action_id . ');', 'title' => $this->translate('Like'), 'action-title' => '<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />', 'class' => 'yncomment_like'));?>
                        <?php else:?>
                            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick' => 'javascript:en4.ynfeed.like(this,' . $action->action_id . ');', 'title' => $this->translate('Like'), 'action-title' => '<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'));?>
                        <?php endif;?>
                    </li>
                  <?php else :?>
                     <li class="yncomment_wrap"> 
                         <span>-</span>  
                        <?php if(!$showLikeWithoutIcon):?>     
                            <?php //SHOW ICON WITH LIKE?>
                            <i class="fa fa-thumbs-up ynfontawesome"></i>
                        <?php endif;?>
                        <?php
                            //DISABLE LINK
                            echo $this->translate('Like');
                        ?>
                    </li>
                  <?php endif;?>
                  
                  <?php if (!$action->dislikes()->isDislike($this->viewer())):?>
                    <li class="feed_item_option_unlike"> 
                        <span>-</span> 
                         <?php if(!$showLikeWithoutIcon):?>       
                            <?php echo $this->htmlLink('javascript:void(0);', '<i class="fa fa-thumbs-down ynfontawesome"></i> '.$this->translate('Dislike'), array('onclick' => 'javascript:en4.ynfeed.unlike(this,' . $action->action_id . ');', 'title' => $this->translate('Dislike'), 'action-title' => '<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />', 'class' => 'yncomment_unlike')); ?>
                         <?php else:?>
                            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Dislike'), array('onclick' => 'javascript:en4.ynfeed.unlike(this,' . $action->action_id . ');', 'title' => $this->translate('Dislike'), 'action-title' => '<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />')); ?>
                         <?php endif;?>
                   </li>
                  <?php else:?>
                    <li class="feed_item_option_unlike yncomment_wrap"> 
                        <span>-</span>
                        <?php if(!$showLikeWithoutIcon):?>     
                            <?php //SHOW ICON WITH DISLIKE?>
                            <i class="fa fa-thumbs-down ynfontawesome"></i>
                        <?php endif;?>
                        <?php //DISABLE LINK
                            echo $this->translate('Dislike'); ?>
                    </li>
                  <?php endif;?>
              <?php endif;?>
              <!--// End like/dislike -->
              
             <!-- // Comments -->
            <?php if( Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ): // Comments - likes ?>
              <li class="feed_item_option_comment">
                    <span>-</span>
                    <?php  $title = $this->translate('Comment');
                       if(!$showLikeWithoutIcon)
                       {
                           $title = '<i class="fa fa-comment ynfontawesome"></i> '.$title;
                       }?> 
                    <?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action'=>'viewcomment','action_id'=>$action->getIdentity(),'format'=>'smoothbox'), 
                    $title, array(
                      'class'=>'smoothbox',
                    )); ?>
              </li>
            <?php else: ?>
              <li class="feed_item_option_comment"> 
                  <span>-</span>  
                  <?php 
                   $title = $this->translate('Comment');
                   if(!$showLikeWithoutIcon)
                   {
                       $title = '<i class="fa fa-comment ynfontawesome"></i> '.$title;
                   }
                   ?>                 
                  <?php echo $this->htmlLink('javascript:void(0);', $title, array( 'onclick' =>'showCommentBox("' . $this->commentForm->getAttrib('id') . '", "' . $this->commentForm->body->getAttrib('id') . '"); 
                    document.getElementById("' . $this->commentForm->submit->getAttrib('id') . '").style.display = "' . ((!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "block" : "none") . '";
                    if(document.getElementById("feed-comment-form-open-li_' . $actionBaseId . '"))
                    {
                        document.getElementById("feed-comment-form-open-li_' . $actionBaseId . '").style.display = "none";
                    } 
                    $("'.$this->commentForm->getAttrib('id').'").addClass("yncommnet_click_textarea");
                    $$(".swiff-uploader-box").each(function(e){e.title = en4.core.language.translate("Attach a Photo");});
                    comment_like_box_show['.$actionBaseId.'] = 1;
                    document.getElementById("' . $this->commentForm->body->getAttrib('id') . '").focus();document.getElementById("' . "comment-likes-activityboox-item-$actionBaseId" . '").style.display = "block";', 'title' => $this->translate('Leave a comment'))) ?>
              </li>
            <?php endif; ?>
            <!-- // End comments -->
          <?php endif; ?>
          <!-- // Remove tag -->
          <?php if($this->viewer()->getIdentity() && in_array($this->viewer()->getIdentity(), $friendIds)):?>
                <span>-</span>
                <?php  $title = $this->translate('Remove Tag');
                       if(!$showLikeWithoutIcon)
                       {
                           $title = '<i class="fa fa-times ynfontawesome"></i> '.$title;
                       }?> 
                <li class="feed_item_option_remove_tag">             
                    <a href="javascript:void(0);" title="" onclick="removeTag('<?php echo $action->action_id ?>')">
                       <?php echo $title;?></a>
                </li>
           <?php endif ?>
           <!-- // End remove tag -->
           
           <!-- Remove Link Preview-->
           <?php if($this->viewer()->isSelf($action->getSubject()) && is_object($attachment) && $action->attachment_count > 0 && $attachment->item && $attachment->item->getType() == 'core_link'):?>
                <span>-</span>
                <?php  $title = $this->translate('Remove preview');
                       if(!$showLikeWithoutIcon)
                       {
                           $title = '<i class="fa fa-times ynfontawesome"></i> '.$title;
                       }?> 
                <li title="<?php echo $this->translate('Remove preview') ?>" class="feed_item_option_remove_preview">             
                    <a href="javascript:void(0);" title="" onclick="removePreview('<?php echo $action->action_id ?>')">
                       <?php echo $title ?></a>
                  </li>
           <?php endif ?>
          
          <?php // Share ?>
          <?php  $title = $this->translate('Share');
               if(!$showLikeWithoutIcon)
               {
                   $title = '<i class="fa fa-share-alt ynfontawesome"></i> '.$title;
               }?> 
          <?php if( $action->getTypeInfo()->shareable && $this->viewer()->getIdentity() && (!$optionFeedTable -> getOptionFeed($action->getSubject(), $action->action_id, 'lock') || $this->viewer()->isSelf($action->getSubject()))): ?>
            <?php if( $action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()) ): ?>
              <li class="feed_item_option_share">
                <span>-</span>
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity(), 'format' => 'smoothbox'), $title, array('class' => 'smoothbox', 'title' => 'Share')) ?>
              </li>
            <?php elseif( $action->getTypeInfo()->shareable == 2 ): ?>
              <li class="feed_item_option_share">
                <span>-</span>
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'format' => 'smoothbox'), $title, array('class' => 'smoothbox', 'title' => 'Share')) ?>
              </li>
            <?php elseif( $action->getTypeInfo()->shareable == 3 ): ?>
              <li class="feed_item_option_share">
                    <span>-</span>
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity(), 'format' => 'smoothbox'), $title, array('class' => 'smoothbox', 'title' => 'Share')) ?>
              </li>
            <?php elseif( $action->getTypeInfo()->shareable == 4 ): ?>
              <li class="feed_item_option_share">
                    <span>-</span>
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity(), 'format' => 'smoothbox'), $title, array('class' => 'smoothbox', 'title' => 'Share')) ?>
              </li>
            <?php endif; ?>
            <?php
             $type = "";
             $id = 0;
             if(count($action->getAttachments()) > 0)
             {
                $atts = $action->getAttachments();
                $type = $atts[0] -> item -> getType();
                $id = $atts[0] -> item -> getIdentity();
             }
             $shared = $feedApiCore -> getShareds($action -> getIdentity(), $type, $id); 
             if(count($shared)):?>
                 <li class="feed_item_shared">
                    <a title="<?php echo $this -> translate("Total shared")?>" class="smoothbox" href="<?php echo $this->url(array('controller' => 'index', 'action' => 'view-shared', 'id' => $action->getIdentity()), 'ynfeed_extended', true);?>">
                        <div class="yfshare_number feed_item_date"><?php echo count($shared)?></div>
                        <div class="activity_icon_share"></div>
                    </a>
                 </li>
             <?php endif;?>
          <?php endif; ?>
      
      <!-- // show comment statistics -->
      <?php if ($canComment && $this->ynfeed_comment_like_box): ?> 
          <?php $likeCount = $action->likes()->getLikeCount(); ?>
          <?php $commentCount = $action->comments()->getCommentCount() ?> 
          <?php $dislikeCount = $action->dislikes()->getDislikePaginator()->getTotalItemCount(); ?>
          <?php if ($likeCount || $commentCount || $dislikeCount): ?>
            <li class="like_comment_counts" onclick="$('comment-likes-activityboox-item-<?php echo $actionBaseId ?>').toggle()">
              <?php if ($likeCount): ?>
                <span>-</span>
                <span class="yncomment_like"><?php echo $this->translate(array('%s like', '%s likes', $this->locale()->toNumber($likeCount)),$this->locale()->toNumber($likeCount)); ?></span>
              <?php endif; ?>
               <?php if ($dislikeCount && !$showAsLike): ?>
                <span>-</span>
                <span class="yncomment_unlike"><?php echo $this->translate(array('%s dislike', '%s dislikes', $this->locale()->toNumber($dislikeCount)),$this->locale()->toNumber($dislikeCount)); ?></span>
              <?php endif; ?>
              <?php if ($commentCount): ?>
                <span>-</span>
                <span class="comment_icon"><?php echo $this->translate(array('yn_total_comment_l', '%s comments & replies', $this->locale()->toNumber($commentCount)),$this->locale()->toNumber($commentCount)); ?></span>
              <?php endif; ?>
            </li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
    </div>
    
    <!-- // Show like/dislike statistics -->
    <?php if ($action->getTypeInfo()->shareable || $action->getTypeInfo()->commentable) : // Comments - likes -share    ?>
    <div class='comments' id='comment-likes-activityboox-item-<?php echo $actionBaseId ?>' <?php if (!$this->viewAllLikes && !$this->viewAllComments && $this->ynfeed_comment_like_box && !$this->ynfeed_comment_like_box_show && $this->viewer()->getIdentity()): ?>style="display: none;"  <?php endif; ?>>
        <?php $this->dislikes = $action->dislikes()->getDislikePaginator(); ?>
        <ul class="ynfeed_advcomment <?php if($action->getTypeInfo()->commentable && ($action->comments()->getCommentCount() > 0 || $action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0) || ($this->dislikes->getTotalItemCount() > 0 && !$showAsLike))) echo "yncomment_feed_hasComment";?>"> 
          <?php if ($action->getTypeInfo()->commentable): // Comments - likes -share ?>
                    <?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0) || ($this->dislikes->getTotalItemCount() > 0 && !$showAsLike)): ?>
                    <?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0)): ?>
                        <span class="comments_likes">
                            <?php if ($action->likes()->getLike($this->viewer()) && $action->likes()->getLikeCount() == 1) :?>
                                <?php echo '<i class="fa fa-thumbs-up ynfontawesome"></i> '.$this->translate(array('%s like this.', '%s likes this.', $action->likes()->getLikeCount()), $this->feedNCFluentList($action->likes()->getAllLikesUsers(), false, $action)) ?>
                            <?php else:?>
                                <?php echo '<i class="fa fa-thumbs-up ynfontawesome"></i> '.$this->translate(array('%s likes this.', '%s like this.', $action->likes()->getLikeCount()), $this->feedNCFluentList($action->likes()->getAllLikesUsers(), false, $action)) ?>
                            <?php endif;?>
                        </span>
                    <?php endif; ?>  
            
                    <?php if ($this->dislikes->getTotalItemCount() > 0 && !$showAsLike):?>
                      <?php if($showDislikeUsers) :?>
                       <?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0)):?>

                         <?php endif;?>
                          <span class="comments_likes">
                              <?php if ($action->dislikes()->getDislike($this->viewer()) && $this->dislikes->getTotalItemCount() == 1):?>
                              <?php echo '<i class="fa fa-thumbs-down ynfontawesome"></i> '.$this->translate(array('%s dislike this.', '%s dislikes this.', $this->dislikes->getTotalItemCount()), $this->feedFluentDisLikeList($action->dislikes()->getAllDislikesUsers(), false, $action)) ?>
                           <?php else:?>
                              <?php echo '<i class="fa fa-thumbs-down ynfontawesome"></i> '.$this->translate(array('%s dislikes this.', '%s dislike this.', $this->dislikes->getTotalItemCount()), $this->feedFluentDisLikeList($action->dislikes()->getAllDislikesUsers(), false, $action)) ?>
                           <?php endif;?>
                          </span>
                        <?php else:?>
                          <span class="comments_likes">
                          <i class="fa fa-thumbs-down ynfontawesome"></i>
                            <?php echo $this->translate(array('%s person dislikes this.', '%s people dislike this.', $this->dislikes->getTotalItemCount()), $this->locale()->toNumber($this->dislikes->getTotalItemCount()));?>
                          </span>
                        <?php endif;?>
                    <?php endif; ?>
                    </li>
                    <?php endif; ?>
                    <?php if ($canComment):?>
                        <div class="ynfeed_comment_post <?php if (!$this->commentShowBottomPost): ?>yncomment_press_enter<?php endif; ?>">
                            <?php echo $this->commentForm->render(); ?>
                        </div>
                    <?php endif;?>  
                    <?php if ($canComment): ?>
                          <?php $commentFormId = $this->commentForm->getAttrib('id');?>
                          <?php $commentFormBodyId = $this->commentForm->body->getAttrib('id');?>
                            <li id='feed-comment-form-open-li_<?php echo $actionBaseId ?>' onclick='<?php echo '
                                document.getElementById("' . $this->commentForm->submit->getAttrib('id') . '").style.display = "' . ((!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "block" : "none") . '";
                                document.getElementById("feed-comment-form-open-li_' . $actionBaseId . '").style.display = "none";
                                document.getElementById("' . $this->commentForm->body->getAttrib('id') . '").focus();
                                $$(".swiff-uploader-box").each(function(e){e.title = en4.core.language.translate("Attach a Photo");});
                                 $("'.$this->commentForm->getAttrib('id').'").addClass("yncommnet_click_textarea");' ?> showCommentBox("<?php echo $commentFormId?>", "<?php echo $commentFormId?>");' <?php if (!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): ?> style="display:none;"<?php endif; ?> >                  <div></div>
                              <div class="yncomment_comment_box yncomment_txt_light"><?php echo $this->translate('Write a comment...') ?></div></li>
                        <?php endif; ?> 
            <?php if ($action->comments()->getCommentCount() > 0):?>
              <?php foreach ($action->getComments($this->viewAllComments) as $comment): ?>
                  <?php
                  $this->replyForm->setActionIdentity($comment->comment_id);
                  $this->replyForm->comment_id->setValue($comment->comment_id);
                  $this->replyForm->action_id->setValue($action->action_id);
                  ?>
                 <script type="text/javascript">
                  (function() {
                    en4.core.runonce.add(function()  { 
                    <?php if ($this->onViewPage): ?>
                        (function() {
                    <?php endif; ?>
                        if (!$('<?php echo $this->replyForm->body->getAttrib('id') ?>'))
                          return;
                        $('<?php echo $this->replyForm->body->getAttrib('id') ?>').autogrow();
                        if (allowQuickReply == '1' && <?php echo $this->submitReply ? '1' : '0' ?>) {
                          if (document.getElementById("feed-reply-form-open-li_<?php echo $comment->comment_id ?>")) {
                            document.getElementById("feed-reply-form-open-li_<?php echo $comment->comment_id ?>").style.display = "block";
                          }
                          document.getElementById("<?php echo $this->replyForm->body->getAttrib('id') ?>").focus();
                        }
                        <?php if ($this->onViewPage): ?>
                        }).delay(1000);
                        <?php endif; ?>
                    });
                  })();
                </script>
                <?php
                $canEdit = $this->viewer()->getIdentity() &&
                                ("user" == $comment->poster_type && $this->viewer()->getIdentity() == $comment->poster_id);
                $canDelete = $this->viewer()->getIdentity() &&
                                (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                                ("user" == $comment->poster_type && $this->viewer()->getIdentity() == $comment->poster_id) ||
                                ("user" !== $comment->poster_type && Engine_Api::_()->getItemByGuid($comment->poster_type . "_" . $comment->poster_id)->isOwner($this->viewer())) ||
                                $this->activity_moderate );
                $isOpenHide = ($this -> openHide == $comment->getIdentity());
                $isHide = (Engine_Api::_() -> getDbtable('hide', 'yncomment') -> checkHideItem($comment) && !$isOpenHide);
                $openHide = (Engine_Api::_() -> getDbtable('hide', 'yncomment') -> checkHideItem($comment) && $isOpenHide);
                ?>
                <li id="comment-<?php echo $comment->comment_id ?>" class="yncomment_nestcomment <?php if($openHide) echo 'yncomment_hidden_open'?>">
                    <?php if($isHide):?>
                        <!-- Comment has been hidden -->
                        <a class="yncomment_hidden"  href="javascript:;" onclick="en4.ynfeed.openHideComment(this, <?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);">
                            <span title="<?php echo $this -> translate('1 hidden')?>">
                                <i class="fa fa-circle"></i><i class="fa fa-circle"></i><i class="fa fa-circle"></i>
                            </span>
                        </a>
                    <?php else: ?>
                    <?php if ($this->viewer()->getIdentity()): ?>
                    <span class="yncomment_replies_info_op">     
                    <span class="yncomment_replies_pulldown">
                      <div class="yncomment_dropdown_menu_wrapper">
                        <div class="yncomment_dropdown_menu">
                          <ul>  
                             <?php
                             // Edit
                              if ($canEdit): ?>
                            <li>
                            <?php 
                             $attachMentArray  = array();
                             if (!empty($comment->attachment_type) 
                             && Engine_Api::_() -> hasItemType($comment->attachment_type)
                             && null !== ($attachment = $this->item($comment->attachment_type, $comment->attachment_id))): ?>
                              <?php if($comment->attachment_type == 'album_photo' || $comment->attachment_type == 'advalbum_photo'):?>
                                <?php $status = true; ?>
                                <?php $photo_id = $attachment->photo_id; ?>
                                <?php $album_id = $attachment->album_id; ?>
                                <?php $src = $attachment->getPhotoUrl(); ?>
                                <?php $attachMentArray = array("status" => $status, "photo_id"=> $photo_id , "album_id" => $attachment->album_id, "src" => $src);?>
                                <?php elseif($comment->attachment_type == 'core_link') :?>
                                <?php $status = true; ?>
                                <?php $uri = $attachment->uri;?>
                                <?php $attachMentArray = array('status' => $status, 'url' => $uri);?>
                              <?php endif;?>
                            <?php endif;?>
                             <?php  $commentBody = $comment -> body;
                                if (isset($comment->params)) {
                                    $commentBody = $this -> stringTagToElement($commentBody, (array) Zend_Json::decode($comment->params));
                                }?>
                            <script type="text/javascript">  
                                en4.core.runonce.add(function() {
                                  commentAttachment.editComment['<?php echo $comment->comment_id ?>'] = { 'body': '<?php echo $this->string()->escapeJavascript($commentBody);?>', 'attachment_type':'<?php echo $comment->attachment_type ?>', 'attachment_body':<?php echo Zend_Json_Encoder::encode($attachMentArray);?>}
                                });
                            </script>
                             <a href='javascript:void(0);' title="<?php echo $this->translate('Edit') ?>" onclick="en4.yncomment.yncomments.showCommentEditForm('<?php echo $comment->comment_id?>', '<?php echo (!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? 0 : 1; ?>');"><?php echo $this->translate('Edit'); ?>
                             </a>
                            </li>  
                            <?php endif ?>
                            
                            <?php
                            // Delete
                              if ($canDelete):?>
                                <li>
                                  <a href="javascript:void(0);" title="<?php echo $this->translate('Delete') ?>" onclick="deletecomment('<?php echo
                                    $action->action_id ?>', '<?php echo $comment->comment_id ?>', '<?php
                                    echo
                                    $this->escape($this->url(array('route' => 'default',
                                    'module' => 'ynfeed', 'controller' => 'index', 'action' => 'delete')))
                                  ?>')"><?php echo $this->translate('Delete') ?></a>
                                 </li>
                               <?php endif; ?>
                               <?php if(!$canEdit && !$canDelete):?>
                                <!-- Hide -->
                                 <?php if(Engine_Api::_() -> getDbtable('hide', 'yncomment') -> checkHideItem($comment)):?>
                                    <li>
                                        <a href="javascript:void(0);" title="<?php echo $this->translate("Unhide"); ?>" onclick="en4.ynfeed.unHideComment(this, <?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)"><?php echo $this->translate('Unhide'); ?></a>
                                    </li>
                                 <?php else:?>
                                     <li>
                                        <a href="javascript:void(0);" title="<?php echo $this->translate("Hide"); ?>" onclick="hideComment('<?php echo $comment->comment_id ?>','<?php echo $comment->getType() ?>')"><?php echo $this->translate('Hide'); ?></a>
                                    </li>
                                 <?php endif;?>
                                 <!-- Report -->
                                <li>
                                    <?php echo $this->htmlLink($this->url(array('action' => 'create', 'module' => 'core', 'controller' => 'report', 'subject' => $comment->getGuid()), 'default', true), $this->translate("Report"), array('title' => $this->translate("Report"), 'class' => "smoothbox")) ?>
                                </li>
                                <?php endif; ?>
                          </ul>
                        </div>
                      </div>
                      <span class="yncomment_comment_dropbox"><i class="fa fa-caret-down"></i></span>
                    </span>
                  </span>
                    <?php endif; ?>       
                  <div class="comments_author_photo">
                    <?php
                        echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->itemPhoto($this->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle()), array('class' => '', 'rel' => $this->item($comment->poster_type, $comment->poster_id)->getType() . ' ' . $this->item($comment->poster_type, $comment->poster_id)->getIdentity())
                    )
                    ?>
                  </div>
                  <div class="comments_info">
                    <span class='comments_author'>
                      <?php
                            echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle(), array('class' => '', 'rel' => $this->item($comment->poster_type, $comment->poster_id)->getType() . ' ' . $this->item($comment->poster_type, $comment->poster_id)->getIdentity())
                      );
                      ?>
                    </span>
                    <span class="comments_body" id="comments_body_<?php echo $comment->comment_id ?>">
                      <?php $content = $comment->body ?>
                            <?php
                            if (isset($comment->params)) {
                              $actionParams = (array) Zend_Json::decode($comment->params);
                              if (isset($actionParams['tags'])) 
                              {
                                  // Convert tags
                                  $content = $this -> stringTagToObject($content, $actionParams);
                                  // Convert emoticons
                                  echo $smiliesEnabled ? $this->smileyToEmoticons($content): $content ;
                              } 
                              else 
                              {
                                  // Convert emoticons
                                  echo $smiliesEnabled ? $this->smileyToEmoticons($this->viewMore($content, null, 3 * 1027)) : $this->viewMore($content, null, 3 * 1027) ;
                              }
                            }
                            ?>
                      </span>
                      <div id="comment_edit_<?php echo $comment->comment_id ?>" class="mtop5 comment_edit <?php if (!$this->commentShowBottomPost): ?>yncomment_press_enter<?php endif; ?>" style="display: none;"><?php include APPLICATION_PATH . '/application/modules/Yncomment/views/scripts/_editComment.tpl' ?>
                      </div>
                      <?php if($this->viewer()->getIdentity()):?>
                            <div id="close_edit_box-<?php echo $comment->comment_id;?>" class="yncomment_txt_light f_small comment_close" style="display:none;">
                                <a class="yncomment_cancel_edit" href='javascript:void(0);' onclick="closeEdit('<?php echo $comment->getIdentity() ?>');"><i class="fa fa-times ynfontawesome"></i> <?php echo $this->translate('Cancel edit');?></a>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($comment->attachment_type) && Engine_Api::_() -> hasItemType($comment->attachment_type) && null !== ($attachment = $this->item($comment->attachment_type, $comment->attachment_id))): ?>
                            <div class="yncomment_comments_attachment" id="yncomment_comments_attachment_<?php echo $comment->comment_id ?>">
                              <div class="yncomment_comments_attachment_photo">
                                <?php if (null !== $attachment->getPhotoUrl()): ?>
                                       <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo'))) ?>
                                <?php endif; ?>
                              </div>
                              <?php if(!in_array($comment->attachment_type, array('advalbum_photo', 'album_photo'))):?>
                              <div class="yncomment_comments_attachment_info">
                                <div class="yncomment_comments_attachment_title">
                                    <?php echo $this->htmlLink($attachment->getHref(array('comment' => $comment->comment_id)), $attachment->getTitle()) ?>
                                </div>
                                <div class="yncomment_comments_attachment_des">
                                    <?php echo $attachment->getDescription() ?>
                                </div>
                              </div>
                              <?php endif;?>
                            </div>
                        <?php endif; ?> 
    
                    <ul class="comments_date">
                      <?php  if ($canComment):?>
                          <li class="feed_item_option_comment">   
                              <?php  $title = '';
                               if($showLikeWithoutIconInReplies != 1)
                               {
                                   $title = '<i class="fa fa-reply-all ynfontawesome"></i> ';
                               }
                               if($showLikeWithoutIconInReplies != 2)
                               {
                                   $title = $title . $this->translate('Reply');
                               }?>       
                            <?php
                                echo $this->htmlLink('javascript:void(0);', $title, array('onclick' => ' showReplyBox("' . $this->replyForm->getAttrib('id') . '", "' . $this->replyForm->body->getAttrib('id') . '"); 
                                  document.getElementById("' . $this->replyForm->submit->getAttrib('id') . '").style.display = "' . ((!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "block" : "none") . '";
                                  if(document.getElementById("feed-reply-form-open-li_' . $comment->comment_id . '")){
                                  document.getElementById("feed-reply-form-open-li_' . $comment->comment_id . '").style.display = "none";} 
                                    $("'.$this->replyForm->getAttrib('id').'").addClass("yncommnet_click_textarea"); 
                                    $$(".swiff-uploader-box").each(function(e){e.title = en4.core.language.translate("Attach a Photo");});
                                  document.getElementById("' . $this->replyForm->body->getAttrib('id') . '").focus();document.getElementById("' . "comment-likes-activityboox-item-$actionBaseId" . '").style.display = "block"; ', 'title' =>
                                    $this->translate('Leave a reply')))
                                  ?>
                            </li>
                        <?php if($showAsLike):?>
                            <?php  $isLiked = $comment->likes()->isLike($this->viewer());?>
                            <li class="comments_like"> 
                             <span>&nbsp;-&nbsp;</span>
                              <?php if (!$isLiked): ?>
                                <a title="<?php echo $this->translate('Like') ?>" href="javascript:void(0)" onclick="en4.ynfeed.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                  <?php if($showLikeWithoutIconInReplies != 1):?>
                                        <i class="fa fa-thumbs-up ynfontawesome"></i>
                                    <?php endif ?>
                                    <?php if($showLikeWithoutIconInReplies != 2):?>
                                        <?php echo $this->translate('Like') ?>
                                    <?php endif ?>
                                </a>
                              <?php else: ?>
                                <a title="<?php echo $this->translate('Unlike') ?>" href="javascript:void(0)" onclick="en4.ynfeed.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                  <?php if($showLikeWithoutIconInReplies != 1):?>
                                        <i class="fa fa-thumbs-down ynfontawesome"></i>
                                    <?php endif ?>
                                    <?php if($showLikeWithoutIconInReplies != 2):?>
                                        <?php echo $this->translate('Unlike') ?>
                                    <?php endif ?>
                                </a>
                              <?php endif ?>
                            </li>
                        <?php else:?>
                            <?php  $isLiked = $comment->likes()->isLike($this->viewer());?>
                                    <?php if (!$isLiked): ?>
                                        <li class="comments_like"> 
                                        <span>&nbsp;-&nbsp;</span>
                                        <?php if($showLikeWithoutIconInReplies == 0):?>     
                                        <a title="<?php echo $this->translate('Like') ?>" class='nstcomment_like' href="javascript:void(0)" onclick="en4.ynfeed.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                           <i class="fa fa-thumbs-up ynfontawesome"></i>
                                           <?php echo $this->translate('Like') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                        <a title="<?php echo $this->translate('Like') ?>" href="javascript:void(0)" onclick="en4.ynfeed.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('Like') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                         <a title="<?php echo $this->translate('Like') ?>" class='nstcomment_like' href="javascript:void(0)" onclick="en4.ynfeed.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>    
                                            <i class="fa fa-thumbs-up ynfontawesome"></i>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                             <a title="<?php echo $this->translate('Vote up') ?>" href="javascript:void(0)" onclick="en4.ynfeed.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                               <i class="fa fa-arrow-up ynfontawesome"></i> <?php echo $this -> translate ("Vote up") ?>
                                             </a>
                                            <?php if ($comment->likes()->getLikeCount() > 0): ?>
                                                <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => 3), 'default', true);?>
                                                 <a href="javascript:void(0);" class="count-vote-up-down" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $comment->likes()->getLikeCount();?></a>
                                            <?php endif ?>
                                         <?php endif;?>
                                        </li>
                                    <?php else: ?>
                                        <li class="comments_like nstcomment_wrap"> 
                                    <span>&nbsp;-&nbsp;</span> 
                                    <?php if($showLikeWithoutIconInReplies == 0):?> 
                                      <i class="fa fa-thumbs-up ynfontawesome"></i>
                                      <?php echo $this->translate('Like') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                       <?php echo $this->translate('Like') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                      <i class="fa fa-thumbs-up ynfontawesome"></i>
                                    <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                       <i class="fa fa-arrow-up ynfontawesome"></i> <?php echo $this -> translate ("Vote up") ?>
                                        <?php if ($comment->likes()->getLikeCount() > 0): ?>
                                            <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => 3), 'default', true);?>
                                             <a href="javascript:void(0);" class="count-vote-up-down" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $comment->likes()->getLikeCount();?></a>
                                        <?php endif ?>
                                    <?php endif;?>
                                        </li>
                                    <?php endif;?>
                                <?php $isDisLiked = Engine_Api::_()->getDbtable('dislikes', 'yncomment')->isDislike($comment, $this->viewer())?>
                                    <?php if (!$isDisLiked): ?>
                                       <li class="comments_unlike"> 
                                        <span>&nbsp;-&nbsp;</span>
                                         <?php if($showLikeWithoutIconInReplies == 0):?>     
                                        <a title="<?php echo $this->translate('Dislike') ?>" class='nstcomment_unlike' href="javascript:void(0)" onclick="en4.ynfeed.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                           <i class="fa fa-thumbs-down ynfontawesome"></i>
                                           <?php echo $this->translate('Dislike') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                        <a title="<?php echo $this->translate('Dislike') ?>" href="javascript:void(0)" onclick="en4.ynfeed.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('Dislike') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                         <a title="<?php echo $this->translate('Dislike') ?>" class='nstcomment_unlike' href="javascript:void(0)" onclick="en4.ynfeed.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>    
                                            <i class="fa fa-thumbs-down ynfontawesome"></i>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                         <a title="<?php echo $this->translate('Vote down') ?>" href="javascript:void(0)" onclick="en4.ynfeed.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                                <i class="fa fa-arrow-down ynfontawesome"></i> <?php echo $this -> translate ("Vote down") ?>
                                            </a>
                                            <?php if(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment ) && !$showAsLike):?>
                                                <?php if($showDislikeUsers) :?>
                                                    <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => 3), 'default', true);?>
                                                    <a href="javascript:void(0);" class="count-vote-up-down" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )?></a>
                                                <?php else:?>
                                                  <?php echo Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )?>
                                                <?php endif;?>   
                                            <?php endif;?>
                                         <?php endif;?>
                                       </li>
                                    <?php else: ?>
                                        <li class="comments_unlike nstcomment_wrap"> 
                                    <span>&nbsp;-&nbsp;</span>   
                                    <?php if($showLikeWithoutIconInReplies == 0):?> 
                                      <i class="fa fa-thumbs-down ynfontawesome"></i>
                                      <?php echo $this->translate('Dislike') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                       <?php echo $this->translate('Dislike') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                      <i class="fa fa-thumbs-down ynfontawesome"></i>
                                    <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                       <i class="fa fa-arrow-down ynfontawesome"></i> <?php echo $this -> translate ("Vote down") ?>
                                     <?php if(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment ) && !$showAsLike):?>
                                      <?php if($showDislikeUsers) :?>
                                        <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => 3), 'default', true);?>
                                           <a href="javascript:void(0);" class="count-vote-up-down" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )?></a>
                                        <?php else:?>
                                            <b class="count-disable-vote-down"><?php echo Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )?></b>
                                        <?php endif;?>
                                        <?php endif;?>
                                    <?php endif;?>
                                        </li>
                                    <?php endif;?>
                        <?php endif;?>
                      <?php endif ?>
                      
                      <?php if($showLikeWithoutIconInReplies != 3):?>
                            <?php if ($comment->likes()->getLikeCount() > 0): ?>
                              <li class="comments_likes_total"> 
                                <?php if($canComment):?><span>&nbsp;-&nbsp;</span><?php endif;?>
                                <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount()));?></a>
                              </li>
                            <?php endif ?>
                            
                            <?php if (Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment ) > 0 && !$showAsLike): ?>
                              <li class="comments_likes_total"> 
                                <?php if($canComment):?><span>&nbsp;-&nbsp;</span><?php endif;?>
                              <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                              <?php if($showDislikeUsers) :?>
                              <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s dislikes this', '%s dislike this', Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )))?></a>
                              <?php else:?>
                                <?php echo $this->translate(array('%s dislikes this', '%s dislike this', Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment )))?>
                              <?php endif;?>
                              </li>
                              <?php endif ?>
                            <?php endif ?>
                            <?php if($attachMentArray && $comment -> attachment_type == 'core_link'):?>
                                <li>
                                   <span>&nbsp;-&nbsp;</span>
                                    <a title="<?php echo $this->translate('Remove preview') ?>" href="javascript:void(0)" onclick="en4.ynfeed.removePreview(this, <?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                      <?php if($showLikeWithoutIconInReplies != 1):?>
                                        <i class="fa fa-times ynfontawesome"></i>
                                    <?php endif ?>
                                    <?php if($showLikeWithoutIconInReplies != 2):?>
                                        <?php echo $this->translate('Remove preview') ?>
                                    <?php endif ?>
                                    </a>
                                </li>  
                            <?php endif;?>
                            <li class="comments_timestamp">
                                <?php if ((Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $comment ) > 0 && !$showAsLike) || ($comment->likes()->getLikeCount() > 0) || ($this->viewer()->getIdentity() && $canComment)): ?>
                                   <span>&nbsp;-&nbsp;</span>
                                <?php endif ?>
                              <?php echo $this->timestamp($comment->creation_date); ?>
                            </li>
                        </ul>
                  </div>
                    
                  <?php $replies = $action->getReplies($comment->comment_id);
                  if(count($replies)):?>
                  <div class="yncomment_show_hidden_reply">
                    <a <?php if($this->hideReply):?> style="display:none;" <?php else:?> style="display:block;" <?php endif;?>  id="replies_show_<?php echo $comment->comment_id ?>" class="fright buttonlink activity_icon_reply_yncomment_reply comments_viewall mtop5" href="javascript:void(0);" onclick="en4.yncomment.yncomments.loadCommentReplies('<?php echo $comment->comment_id;?>');"><?php echo $this->translate(array("View %s Reply", "View %s Replies", count($action->getReplies($comment->comment_id))), count($action->getReplies($comment->comment_id)));?></a>
                  
                    <a  <?php if($this->hideReply):?> style="display:inline-block;" <?php else:?> style="display:none;" <?php endif;?> id="replies_hide_<?php echo $comment->comment_id ?>" class="fright buttonlink activity_icon_reply_yncomment_reply comments_viewall mtop5 mbot5" href="javascript:void(0);" onclick="en4.yncomment.yncomments.hideCommentReplies('<?php echo $comment->comment_id;?>');"><?php echo $this->translate(array("Hide %s Reply", "Hide %s Replies", count($action->getReplies($comment->comment_id))), count($action->getReplies($comment->comment_id)));?></a>
                  </div>
                  <?php endif;?>
                    
                  <div class="comments">
                  <?php if(count($replies)):?>
                    <ul class="yncomment_reply">
                      <?php foreach ($replies as $reply): ?>
                          <?php 
                        $canEdit = $this->viewer()->getIdentity() &&
                                        ("user" == $reply->poster_type && $this->viewer()->getIdentity() == $reply->poster_id);
                        $canDelete = $this->viewer()->getIdentity() &&
                                        (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                                        ("user" == $reply->poster_type && $this->viewer()->getIdentity() == $reply->poster_id) ||
                                        ("user" !== $reply->poster_type && Engine_Api::_()->getItemByGuid($reply->poster_type . "_" . $reply->poster_id)->isOwner($this->viewer())) ||
                                        $this->activity_moderate );
                        $isOpenHide = ($this -> openHide == $reply->getIdentity());
                        $isHide = (Engine_Api::_() -> getDbtable('hide', 'yncomment') -> checkHideItem($reply) && !$isOpenHide);
                        $openHide = (Engine_Api::_() -> getDbtable('hide', 'yncomment') -> checkHideItem($reply) && $isOpenHide);
                        ?>
                        <li id="reply-<?php echo $reply->comment_id ?>" class="reply<?php echo $comment->comment_id;?> <?php if($openHide) echo 'yncomment_hidden_open';?>" <?php if($this->hideReply):?> style="display:inline-block;" <?php else:?> style="display:none;" <?php endif;?>>
                              <?php if($isHide):?>
                                <!-- Comment has been hidden -->
                                <a class="yncomment_hidden"  href="javascript:;" onclick="en4.ynfeed.openHideComment(this, <?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);">
                                    <span title="<?php echo $this -> translate('1 hidden')?>">
                                        <i class="fa fa-circle"></i><i class="fa fa-circle"></i><i class="fa fa-circle"></i>
                                    </span>
                                </a>
                            <?php else: ?>
                            <?php if ($this->viewer()->getIdentity()):?>
                            <span class="yncomment_replies_info_op">
                            <span class="yncomment_replies_pulldown">
                              <div class="yncomment_dropdown_menu_wrapper">
                                <div class="yncomment_dropdown_menu">
                                  <ul>  
                                       <?php
                                     // Edit
                                      if ($canEdit): ?>
                                    <li>
                                    <?php 
                                       $attachMentArray  = array();
                                       if (!empty($reply->attachment_type) 
                                       && Engine_Api::_() -> hasItemType($reply->attachment_type) 
                                       && null !== ($attachment = $this->item($reply->attachment_type, $reply->attachment_id))): ?>
                                        <?php if($reply->attachment_type == 'album_photo' || $reply->attachment_type == 'advalbum_photo'):?>
                                          <?php $status = true; ?>
                                          <?php $photo_id = $attachment->photo_id; ?>
                                          <?php $album_id = $attachment->album_id; ?>
                                          <?php $src = $attachment->getPhotoUrl(); ?>
                                          <?php $attachMentArray = array('status' => $status, 'photo_id' => $photo_id , 'album_id' => $attachment->album_id, 'src' => $src);?>
                                          <?php elseif($reply->attachment_type == 'core_link') :?>
                                            <?php $status = true; ?>
                                            <?php $uri = $attachment->uri;?>
                                            <?php $attachMentArray = array('status' => $status, 'url' => $uri);?>
                                          <?php endif;?>
                                      <?php endif;?>
                                      <?php  $replyBody = $reply -> body;
                                        if (isset($comment->params)) {
                                            $replyBody = $this -> stringTagToElement($replyBody, (array) Zend_Json::decode($reply->params));
                                        }?>
                                      <script type="text/javascript">  
                                        en4.core.runonce.add(function() {
                                          replyAttachment.editReply['<?php echo $reply->comment_id ?>'] = { 'body': '<?php echo $this->string()->escapeJavascript($replyBody);?>', 'attachment_type':'<?php echo $reply->attachment_type ?>', 'attachment_body':<?php echo Zend_Json_Encoder::encode($attachMentArray);?>}
                                        });
                                      </script>
                                     <a href='javascript:void(0);' title="<?php echo $this->translate('Edit') ?>" onclick="en4.yncomment.yncomments.showReplyEditForm('<?php echo $reply->comment_id?>', '<?php echo (!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? 0 : 1; ?>');"><?php echo $this->translate('Edit'); ?>
                                     </a>   
                                    </li>                                            
                                    <?php endif;?>                                       
                                    <?php
                                     // Delete
                                    if ($canDelete): ?>
                                    <li>
                                      <a href="javascript:void(0);" title="<?php echo $this->translate('Delete') ?>" onclick="deletereply('<?php echo $action->action_id ?>', '<?php echo $reply->comment_id ?>', '<?php echo $this->escape($this->url(array('route' => 'default', 'module' => 'ynfeed', 'controller' => 'index', 'action' => 'delete'))) ?>')"><?php echo $this->translate('Delete') ?></a>
                                      </li>
                                       <?php endif; ?>
                                       <?php if(!$canEdit && !$canDelete):?>
                                        <!-- Hide -->
                                         <?php if(Engine_Api::_() -> getDbtable('hide', 'yncomment') -> checkHideItem($reply)):?>
                                            <li>
                                                <a href="javascript:void(0);" title="<?php echo $this->translate("Unhide"); ?>" onclick="en4.ynfeed.unHideComment(this, <?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>)"><?php echo $this->translate('Unhide'); ?></a>
                                            </li>
                                         <?php else:?>
                                             <li>
                                                <a href="javascript:void(0);" title="<?php echo $this->translate("Hide"); ?>" onclick="hideComment('<?php echo $reply->comment_id ?>','<?php echo $reply->getType() ?>')"><?php echo $this->translate('Hide'); ?></a>
                                            </li>
                                         <?php endif;?>
                                         <!-- Report -->
                                        <li>
                                            <?php echo $this->htmlLink($this->url(array('action' => 'create', 'module' => 'core', 'controller' => 'report', 'subject' => $reply->getGuid()), 'default', true), $this->translate("Report"), array('title' => $this->translate("Report"), 'class' => "smoothbox")) ?>
                                        </li>
                                     <?php endif; ?>
                                  </ul>
                                </div>
                              </div>
                              <span class="yncomment_comment_dropbox"><i class="fa fa-caret-down"></i></span>
                            </span>
                          </span>
                          <?php endif;?>
                          <div class="comments_author_photo">
                            <?php
                            echo $this->htmlLink($this->item($reply->poster_type, $reply->poster_id)->getHref(), $this->itemPhoto($this->item($reply->poster_type, $reply->poster_id), 'thumb.icon', $action->getSubject()->getTitle()), array('class' => '', 'rel' => $this->item($reply->poster_type, $reply->poster_id)->getType() . ' ' . $this->item($reply->poster_type, $reply->poster_id)->getIdentity())
                            )
                            ?>
                          </div>
                          <div class="comments_info">
                            <span class='comments_author'>
                              <?php
                                echo $this->htmlLink($this->item($reply->poster_type, $reply->poster_id)->getHref(), $this->item($reply->poster_type, $reply->poster_id)->getTitle(), array('class' => '', 'rel' => $this->item($reply->poster_type, $reply->poster_id)->getType() . ' ' . $this->item($reply->poster_type, $reply->poster_id)->getIdentity())
                              );
                              ?>
                            </span>
                            <span class="comments_body" id="reply_body_<?php echo $reply->comment_id ?>">
                                    <?php $content = $reply->body ?>
                                     <?php
                                    if (isset($reply->params)) {
                                     $actionParams = (array) Zend_Json::decode($reply->params);
                                      if (isset($actionParams['tags'])) 
                                      {
                                          // Convert tags
                                          $content = $this -> stringTagToObject($content, $actionParams);
                                          // Convert emoticons
                                          echo $smiliesEnabled ? $this->smileyToEmoticons($content): $content ;
                                      } 
                                      else 
                                      {
                                          // Convert emoticons
                                          echo $smiliesEnabled ? $this->smileyToEmoticons($this->viewMore($content, null, 3 * 1027)) : $this->viewMore($content, null, 3 * 1027) ;
                                      }
                                    }
                                    ?>
                                  </span>
                            <div id="reply_edit_<?php echo $reply->comment_id ?>" style="display: none;" class="reply_edit <?php if (!$this->commentShowBottomPost): ?>yncomment_press_enter<?php endif; ?>"><?php include APPLICATION_PATH . '/application/modules/Yncomment/views/scripts/_editReply.tpl' ?>
                            </div>
                            <?php if($this->viewer()->getIdentity()):?>
                            <div id="close_edit_box-<?php echo $reply->comment_id;?>" class="yncomment_txt_light f_small comment_close" style="display:none;">
                                <a class="yncomment_cancel_edit" href='javascript:void(0);' onclick="closeReplyEdit('<?php echo $reply->getIdentity() ?>');"><i class="fa fa-times ynfontawesome"></i> <?php echo $this->translate('Cancel edit');?></a>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($reply->attachment_type) && Engine_Api::_() -> hasItemType($reply->attachment_type) && null !== ($attachment = $this->item($reply->attachment_type, $reply->attachment_id))): ?>
                              <div class="yncomment_comments_attachment" id="yncomment_comments_attachment_<?php echo $reply->comment_id ?>">
                                <div class="yncomment_comments_attachment_photo">
                                  <?php if (null !== $attachment->getPhotoUrl()): ?>
                                         <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo'))) ?>
                                    <?php endif; ?>
                                </div>
                                <?php if(!in_array($reply->attachment_type, array('advalbum_photo', 'album_photo'))):?>
                                <div class="yncomment_comments_attachment_info">
                                  <div class="yncomment_comments_attachment_title">
                                        <?php echo $this->htmlLink($attachment->getHref(array('comment' => $reply->comment_id)), $attachment->getTitle()) ?>
                                  </div>
                                  <div class="yncomment_comments_attachment_des">
                                        <?php echo $attachment->getDescription() ?>
                                  </div>
                                </div>
                                <?php endif;?>
                              </div>
                            <?php endif; ?>
                            <ul class="comments_date">
                              <?php if ($canComment):?>
                                      <?php if($showAsLike):?>
                                            <?php $isLiked = $reply->likes()->isLike($this->viewer());?>
                                            <li class="comments_like"> 
                                              
                                              <?php if (!$isLiked): ?>
                                                <a href="javascript:void(0)" onclick="en4.ynfeed.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                                 <?php if($showLikeWithoutIconInReplies != 1):?>
                                                        <i class="fa fa-thumbs-up ynfontawesome"></i>
                                                    <?php endif ?>
                                                    <?php if($showLikeWithoutIconInReplies != 2):?>
                                                        <?php echo $this->translate('Like') ?>
                                                    <?php endif ?>
                                                </a>
                                              <?php else: ?>
                                                <a href="javascript:void(0)" onclick="en4.ynfeed.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                                  <?php if($showLikeWithoutIconInReplies != 1):?>
                                                        <i class="fa fa-thumbs-down ynfontawesome"></i>
                                                    <?php endif ?>
                                                    <?php if($showLikeWithoutIconInReplies != 2):?>
                                                        <?php echo $this->translate('Unlike') ?>
                                                    <?php endif ?>
                                                </a>
                                              <?php endif ?>
                                            </li>
                                      <?php else:?>
                                      
                                      <?php $isLiked = $reply->likes()->isLike($this->viewer());?> 
                                         <?php if(!$isLiked) :?>
                                             <li class="comments_like"> 
                                        
                                        <?php if($showLikeWithoutIconInReplies == 0):?>     
                                        <a class='nstcomment_like' href="javascript:void(0)" onclick="en4.ynfeed.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                          <i class="fa fa-thumbs-up ynfontawesome"></i>
                                           <?php echo $this->translate('like') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                        <a href="javascript:void(0)" onclick="en4.ynfeed.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('like') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                         <a class='nstcomment_like' href="javascript:void(0)" onclick="en4.ynfeed.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>  
                                         <i class="fa fa-thumbs-up ynfontawesome"></i>  
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                         <a href="javascript:void(0)" onclick="en4.ynfeed.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                           <i class="fa fa-arrow-up ynfontawesome"></i> <?php echo $this->translate('Vote up') ?>
                                         </a>
                                             <?php if ($reply->likes()->getLikeCount() > 0): ?>
                                                <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => 3), 'default', true);?>
                                                 <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $reply->likes()->getLikeCount();?></a>
                                            <?php endif ?>
                                         <?php endif;?>
                                        </li>
                                       <?php else:?>
                                    <li class="comments_like nstcomment_wrap"> 
                                    
                                    <?php if($showLikeWithoutIconInReplies == 0):?> 
                                      <i class="fa fa-thumbs-up ynfontawesome"></i>
                                      <?php echo $this->translate('like') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                       <?php echo $this->translate('like') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                      <i class="fa fa-thumbs-up ynfontawesome"></i>
                                    <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                       <i class="fa fa-arrow-up ynfontawesome"></i> <?php echo $this->translate('Vote up') ?>
                                        <?php if ($reply->likes()->getLikeCount() > 0): ?>
                                            <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => 3), 'default', true);?>
                                             <a href="javascript:void(0);" class="count-vote-up-down" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $reply->likes()->getLikeCount();?></a>
                                       <?php endif ?>
                                    <?php endif;?>
                                        </li>
                                    <?php endif;?>
                                       <?php $isDisLiked = Engine_Api::_()->getDbtable('dislikes', 'yncomment')->isDislike($reply, $this->viewer())?>
                                       <?php if(!$isDisLiked) :?>
                                       <li class="comments_unlike"> 
                                        - 
                                        <?php if($showLikeWithoutIconInReplies == 0):?>     
                                        <a class='nstcomment_unlike' href="javascript:void(0)" onclick="en4.ynfeed.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                          <i class="fa fa-thumbs-down ynfontawesome"></i>
                                           <?php echo $this->translate('dislike') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                        <a href="javascript:void(0)" onclick="en4.ynfeed.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('dislike') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                         <a class='nstcomment_unlike' href="javascript:void(0)" onclick="en4.ynfeed.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>   
                                         <i class="fa fa-thumbs-down ynfontawesome"></i> 
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                         <a href="javascript:void(0)" onclick="en4.ynfeed.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                          <i class="fa fa-arrow-down ynfontawesome"></i> <?php echo $this->translate('Vote down') ?>
                                         </a>
                                         <?php if(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply ) && !$showAsLike):?>
                                            <?php if($showDislikeUsers) :?>
                                          <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => 3), 'default', true);?>
                                                <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply )?></a>
                                                <?php else:?>
                                                    <?php echo Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply )?>
                                                 <?php endif;?>
                                            <?php endif;?>
                                         <?php endif;?>
                                        </li>
                                      <?php else:?>
                                        <li class="comments_unlike nstcomment_wrap">
                                        <span>&nbsp;-&nbsp;</span> 
                                    <?php if($showLikeWithoutIconInReplies == 0):?> 
                                      <i class="fa fa-thumbs-down ynfontawesome"></i>
                                      <?php echo $this->translate('dislike') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                       <?php echo $this->translate('dislike') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                      <i class="fa fa-thumbs-down ynfontawesome"></i>
                                    <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                        <i class="fa fa-arrow-down ynfontawesome"></i> <?php echo $this->translate('Vote down') ?>
                                         <?php if(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply ) && !$showAsLike):?>
                                       <?php if($showDislikeUsers) :?>
                                          <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public', 'showLikeWithoutIconInReplies' => 3), 'default', true);?>
                                                <a href="javascript:void(0);" class="count-vote-up-down" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply )?></a>
                                                <?php else:?>
                                                  <b class="count-disable-vote-down"><?php echo Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply )?></b>
                                                <?php endif;?>
                                        <?php endif;?>
                                    <?php endif;?>
                                        </li>
                                       <?php endif;?>
                                      
                                      <?php endif;?>
                                    <?php endif ?>
                                   <?php if($showLikeWithoutIconInReplies != 3):?>
                                        <?php if ($reply->likes()->getLikeCount() > 0): ?>
                                          <li class="comments_likes_total"> 
                                               <?php if($canComment):?><span>&nbsp;-&nbsp;</span><?php endif;?>
                                            <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                            <a href="javascript:void(0);" id="replies_reply_likes_<?php echo $reply->comment_id ?>" onclick="Smoothbox.open('<?php echo $url;?>')">
                                              <?php echo $this->translate(array('%s likes this', '%s like this', $reply->likes()->getLikeCount()), $this->locale()->toNumber($reply->likes()->getLikeCount())) ?>
                                            </a>

                                          </li>
                                        <?php endif ?>

                                        <?php if (Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply ) > 0 && !$showAsLike): ?>
                                            <li class="comments_likes_total"> 
                                                <?php if($canComment):?><span>&nbsp;-&nbsp;</span><?php endif;?>
                                              <?php if($showDislikeUsers) :?>
                                                <?php $url = $this->url(array('module' => 'yncomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                                <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s dislikes this', '%s dislike this', Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply )))?></a>
                                                <?php else:?>
                                                    <?php echo $this->translate(array('%s dislikes this', '%s dislike this', Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply )))?>
                                                <?php endif;?>
                                            </li>
                                        <?php endif ?>
                                    <?php endif ?>
                                    <?php if($attachMentArray && $reply -> attachment_type == 'core_link'):?>
                                        <li>
                                           <span>&nbsp;-&nbsp;</span>
                                            <a title="<?php echo $this->translate('Remove preview') ?>" href="javascript:void(0)" onclick="en4.ynfeed.removePreview(this, <?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img width = "12" src="application/modules/Yncomment/externals/images/loading.gif" />'>
                                              <?php if($showLikeWithoutIconInReplies != 1):?>
                                                <i class="fa fa-times ynfontawesome"></i>
                                                <?php endif ?>
                                                <?php if($showLikeWithoutIconInReplies != 2):?>
                                                    <?php echo $this->translate('Remove preview') ?>
                                                <?php endif ?>
                                            </a>
                                        </li>  
                                    <?php endif;?>
                                     <li class="comments_timestamp"> 
                                         <?php if ((Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount( $reply ) > 0 && !$showAsLike) || ($reply->likes()->getLikeCount() > 0) || ($this->viewer()->getIdentity() && $canComment)): ?>
                                            <span>&nbsp;-&nbsp;</span>
                                        <?php endif ?>
                                      <?php echo $this->timestamp($reply->creation_date); ?>
                                 </li>
                            </ul>
                          </div>
                          <!-- show all replies (go to object detail)-->
                          <?php if(count($action->getReplies($reply->comment_id))):
                              $count_reply = 0;
                              $action->getAllReplies($reply->comment_id, $count_reply);?>
                            <a  id="replies_show_<?php echo $reply->comment_id ?>" class="fright buttonlink activity_icon_reply_yncomment_reply comments_viewall mtop5" target = "_blank" href="<?php echo $reply -> getHref()?>"><?php echo $this->translate(array("View %s Reply", "View %s Replies", $count_reply), $count_reply);?></a>
                          <?php endif;?>
                           <?php endif; ?>
                        </li>
                      <?php endforeach;?>
                    </ul>
                    <?php endif;?>
                  </div>
                     <?php endif; ?>                
                </li> 
                
                <?php if ($canComment): ?>
                 <?php $replyFormId = $this->replyForm->getAttrib('id');?>
                 <?php $replyFormBodyId = $this->replyForm->body->getAttrib('id');?>
                        <li id='feed-reply-form-open-li_<?php echo $comment->comment_id ?>' onclick='showReplyBox("<?php echo $replyFormId?>", "<?php echo $replyFormBodyId?>");' <?php echo '
                                document.getElementById("' . $this->replyForm->submit->getAttrib('id') . '").style.display = "' . ((!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "block" : "none") . '";
                                document.getElementById("feed-reply-form-open-li_' . $comment->comment_id . '").style.display = "none";
                                  document.getElementById("' . $this->replyForm->body->getAttrib('id') . '").focus(); 
                                  $$(".swiff-uploader-box").each(function(e){e.title = en4.core.language.translate("Attach a Photo");});
                                  $("'.$this->replyForm->getAttrib('id').'").addClass("yncommnet_click_textarea");' ?>' <?php if (!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): ?> style="display:none;"<?php endif; ?> style="display:none;">                               <div></div>
                          <div class="yncomment_comment_box yncomment_txt_light"><?php echo $this->translate('Write a reply...') ?></div>
                        </li>
                <?php endif;?>
                <?php if ($canComment):?>
                    <div class="ynfeed_comment_post <?php if (!$this->commentShowBottomPost): ?>yncomment_press_enter<?php endif; ?>">
                        <?php echo $this->replyForm->render(); ?>
                    </div>
                <?php endif;?>
                <?php endforeach; ?>
                <?php if ($action->comments()->getCommentCount() > 5 && !$this->viewAllComments): ?>
                <span class="comments_likes">
                  <div></div>
                  <div class="comments_viewall" id="comments_viewall">
                      <?php
                        echo $this->htmlLink('javascript:void(0);', '<i class="fa fa-comment ynfontawesome"></i> '.$this->translate(array('View all %s comment & replies', 'View all %s comments & replies', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())), array('onclick' => 'en4.ynfeed.viewComments(' . $action->action_id . ');'))
                      ?>
                  </div>
                   <div style="display:none;" id="show_view_all_loading">
                     <img  width = "12" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />
                   </div>
                </span>
              <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>  
        </ul>
        </div>
      <?php endif; ?>
      
      </div> <!-- End of Comment Likes -->
<?php
      ob_end_flush();
    } catch (Exception $e) {
      ob_end_clean();
      if( APPLICATION_ENV === 'development' ) {
        echo $e->__toString();
      }
    };
  endforeach;
?>

<?php if($smiliesEnabled):?>
<style type="text/css">
    .ynfeed_advcomment + form .compose-body #compose-photo-form-fancy-file,
    .ynfeed_advcomment .compose-body #compose-photo-form-fancy-file{
        right: 23px;
    }
</style>
<?php endif;?>