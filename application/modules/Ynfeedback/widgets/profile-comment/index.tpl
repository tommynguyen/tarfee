<script type="text/javascript">
function setFollow(option_id)
{
	new Request.JSON({
        url: '<?php echo $this->url(array('action' => 'profile-follow', 'idea_id' => $this->subject()->idea_id), 'ynfeedback_specific', true); ?>',
        method: 'post',
        data : {
        	format: 'json',
            'option_id' : option_id
        },
        onComplete: function(responseJSON, responseText) {
            if (option_id == '0')
            {
            	$("ynfeedback_widget_cover_follow").set("html", '<?php echo $this -> translate("Follow This");?>');
            	$("ynfeedback_widget_cover_follow").set("onclick", "setFollow(1)");
            	$("ynfeedback_widget_cover_follow").set("title", "<?php echo $this -> translate("Follow This");?>");
            }
            else if (option_id == '1')
            {
            	$("ynfeedback_widget_cover_follow").set("html", '<?php echo $this -> translate("Unfollow This");?>');
            	$("ynfeedback_widget_cover_follow").set("onclick", "setFollow(0)");
            	$("ynfeedback_widget_cover_follow").set("title", "<?php echo $this -> translate("Unfollow This");?>");
            }
            
        }
    }).send();
}
</script>

<script type="text/javascript">
  var CommentLikesTooltips;
  en4.core.runonce.add(function() {
    // Scroll to comment
    if( window.location.hash != '' ) {
      var hel = $(window.location.hash);
      if( hel ) {
        window.scrollTo(hel);
      }
    }
    // Add hover event to get likes
    $$('.comments_comment_likes').addEvent('mouseover', function(event) {
      var el = $(event.target);
      if( !el.retrieve('tip-loaded', false) ) {
        el.store('tip-loaded', true);
        el.store('tip:title', '<?php echo $this->translate('Loading...') ?>');
        el.store('tip:text', '');
        var id = el.get('id').match(/\d+/)[0];
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'get-likes'), 'default', true) ?>';
        var req = new Request.JSON({
          url : url,
          data : {
            format : 'json',
            type : 'core_comment',
            id : id
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
        'x' : 48,
        'y' : 16
      }
    });
    // Enable links
    $$('.comments_body').enableLinks();
  });
</script>

<?php $this->headTranslate(array(
  'Are you sure you want to delete this?',
)); ?>

<?php if( !$this->page ): ?>
<div class='comments' id="comments">
<?php endif; ?>
  <div class='comments_options'>
    <span><?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span>

    <?php if( isset($this->form) ): ?>
      - <a href='javascript:void(0);' onclick="$('comment-form').style.display = '';$('comment-form').body.focus();"><?php echo $this->translate('Post Comment') ?></a>
    <?php endif; ?>
	
    <?php if( $this->viewer()->getIdentity() && $this->canComment ): ?>
      <?php if( $this->subject()->likes()->isLike($this->viewer()) ): ?>
        - <a href="javascript:void(0);" onclick="ynfeedback.unlike('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>')"><?php echo $this->translate('Unlike This') ?></a>
      <?php else: ?>
        - <a href="javascript:void(0);" onclick="ynfeedback.like('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>')"><?php echo $this->translate('Like This') ?></a>
      <?php endif; ?>
    <?php endif; ?>
    <?php if( $this->viewer()->getIdentity()): ?>
        <!-- Follow Feedback -->
        - <a href="javascript:;" id="ynfeedback_widget_cover_follow" class="" title="<?php echo ($this->follow) ? $this -> translate("Follow This") : $this -> translate("Unfollow This")?>" onclick="<?php echo ($this->follow) ? "setFollow(0);" : "setFollow(1);"; ?>"> <?php echo ($this->follow) ? $this -> translate("UnFollow This") : $this -> translate("Follow This")?></a>
    <?php endif; ?>    
	<?php $menu = new Ynfeedback_Plugin_Menus(); ?>
	<?php $aShareButton = $menu -> onMenuInitialize_YnfeedbackProfileShare();?>	
	<!-- share -->
    <?php if($aShareButton):?>
       - <a class="<?php echo (!empty($aShareButton['class'])) ? $aShareButton['class'] : "";?>" href="<?php echo $this -> url($aShareButton['params'], $aShareButton['route'], array()); ?>" > 
            <?php echo $this -> translate($aShareButton['label']) ?>
        </a>
    <?php endif;?>
  </div>
  <ul>

    <?php if( $this->likes->getTotalItemCount() > 0 ): // LIKES ------------- ?>
      <li>
        <?php if( $this->viewAllLikes || $this->likes->getTotalItemCount() <= 3 ): ?>
          <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
          <div> </div>
          <div class="comments_likes">
            <?php echo $this->translate(array('%s likes this', '%s like this', $this->likes->getTotalItemCount()), $this->fluentList($this->subject()->likes()->getAllLikesUsers())) ?>
          </div>
        <?php else: ?>
          <div> </div>
          <div class="comments_likes">
            <?php echo $this->htmlLink('javascript:void(0);',
                          $this->translate(array('%s person likes this', '%s people like this', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount())),
                          array('onclick' => 'ynfeedback.showLikes("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'");')
                      ); ?>
          </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if( $this->comments->getTotalItemCount() > 0 ): // COMMENTS ------- ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() > 1 ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'), array(
              'onclick' => 'ynfeedback.loadComments("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'", "'.($this->page - 1).'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>

      <?php if( !$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array(
              'onclick' => 'ynfeedback.loadComments("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'", "'.($this->comments->getCurrentPageNumber()).'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>

      <?php // Iterate over the comments backwards (or forwards!)
      $comments = $this->comments->getIterator();
      if( $this->page ):
        $i = 0;
        $l = count($comments) - 1;
        $d = 1;
        $e = $l + 1;
      else:
        $i = count($comments) - 1;
        $l = count($comments);
        $d = -1;
        $e = -1;
      endif;
      for( ; $i != $e; $i += $d ):
        $comment = $comments[$i];
        $poster = $this->item($comment->poster_type, $comment->poster_id);
        $canDelete = ( ( $this->canDelete || $poster->isSelf($this->viewer()) ) && ($this->viewer()->getIdentity()) );
        ?>
        <li id="comment-<?php echo $comment->comment_id ?>">
          <div class="comments_author_photo">
            <?php echo $this->htmlLink($poster->getHref(),
              $this->itemPhoto($poster, 'thumb.icon')
            ) ?>
          </div>
          <div class="comments_info">
            <span class='comments_author'>
            	<?php if ($comment->poster_id):?>
              		<?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?>
              	<?php else:?>
              		<strong><?php echo $comment->poster_name;?></strong>
              	<?php endif;?>
            </span>
            <span class="comments_body">
              <?php echo $this->viewMore($comment->body) ?>
            </span>
            <div class="comments_date">
              <?php echo $this->timestamp($comment->creation_date); ?>
              <?php if( $canDelete ): ?>
                -
                <a href="javascript:void(0);" onclick="ynfeedback.deleteComment('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $comment->comment_id ?>')">
                  <?php echo $this->translate('delete') ?>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </li>
      <?php endfor; ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
              'onclick' => 'ynfeedback.loadComments("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'", "'.($this->page + 1).'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>

    <?php endif; ?>
		
  </ul>
  <script type="text/javascript">
    en4.core.runonce.add(function(){
      $($('comment-form').body).autogrow();
      ynfeedback.attachCreateComment($('comment-form'));
    });
  </script>
  <?php if( isset($this->form) ) 
  	echo $this
  	->form
  	->setAttribs(array('id' => 'comment-form', 'style' => 'display:none;'))
  	->setAction($this->url(array('controller' => 'comment', 'action' => 'create'),'ynfeedback_extended'))
  	->render(); 
  ?>
<?php if( !$this->page ): ?>
</div>
<?php endif; ?>