<?php if (count($this->ideas)) : ?>
    <?php $viewer = Engine_Api::_()->user()->getViewer();?>
    <ul class='ynfeedback-listing'>
    <?php foreach ($this->ideas as $feedback) :?>
        <li class='ynfeedback-listing-item ynfeedback-clearfix'>
            <div class='ynfeedback-listing-content'>
                <div class='ynfeedback-listing-info'>
                    <h4><a href='<?php echo $feedback->getHref();?>'><?php echo $feedback->title; ?></a></h4>
                    <div> <div class="ynfeedback-listing-status" style="background-color: <?php echo $feedback->getStatusColor(); ?>"><?php echo $feedback->getStatus(); ?></div><?php echo $this->viewMore($feedback -> description, 255, 3*1027); ?></div>
                </div>

                <div class='ynfeedback-listing-statistics'>
                    <div class='ynfeedback-listing-stats'>
                    	<span id="ynfeedback-like-button-new-<?php echo $feedback -> getIdentity();?>">
							<?php if ($this->viewer()->getIdentity()):?>			
								<?php if( $feedback->likes()->isLike($this->viewer()) ): ?>				
							   		<a title="<?php echo $this -> translate('Liked');?>" href="javascript:void(0);" onclick="unlike('<?php echo $feedback->getType()?>', '<?php echo $feedback->getIdentity() ?>', '<?php echo $feedback->like_count; ?>')"><i class="fa fa-heart"></i></a><?php echo $feedback->like_count; ?>
								<?php else: ?>
							       	<a title="<?php echo $this -> translate('Like');?>" href="javascript:void(0);" onclick="like('<?php echo $feedback->getType()?>', '<?php echo $feedback->getIdentity() ?>', '<?php echo $feedback->like_count; ?>')"><i class="fa fa-heart-o"></i></a><?php echo $feedback->like_count; ?>
								<?php endif; ?>
							<?php else :?>
								<i title="<?php echo $this -> translate('Like');?>" class="fa fa-heart"></i><?php echo $feedback->like_count; ?>
							<?php endif;?>	
						</span>
						<span><a title="<?php echo $this -> translate('Comment');?>" href="<?php echo $feedback -> getHref();?>"><i class="fa fa-comment"></i></a><?php echo $feedback->comment_count; ?></span>
			        	<span>
			        		<?php if ($this->viewer()->getIdentity()):?>
					        	<a title="<?php echo $this -> translate('Share');?>" href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $this -> url(
					        	array(
				        			'module' => 'activity',
							        'controller' => 'index',
							        'action' => 'share',
							        'type' => $feedback->getType(),
							        'id' => $feedback->getIdentity(),
							        'format' => 'smoothbox',
						        ), 'default', true);?>')">
									<i class="fa fa-share-square-o"></i></a><?php echo $feedback->getShareCount(); ?>
							<?php else :?>
								<i title="<?php echo $this -> translate('Share');?>" class="fa fa-share-square-o"></i><?php echo $feedback->getShareCount(); ?>
							<?php endif;?>
						</span>
                    </div>

                    <div class="ynfeedback-listing-categories"><i class="fa fa-folder-open"></i><?php echo $this->htmlLink($feedback->getCategory()->getHref(), $feedback->getCategory()->getTitle());?></div>
                </div>

                <?php $widgetId = ($this->identity) ? ($this->identity) : 0;?>
                <div class='ynfeedback-listing-votes' id='ynfeedback-item-vote-action-<?php echo $feedback->getIdentity();?>-<?php echo $widgetId;?>'>
                    <?php echo $this->partial ('_vote_action.tpl', 'ynfeedback', array('feedback' =>  $feedback, 'widget_id' => $widgetId));?>
                </div>
            </div>
        </li>
        <?php endforeach;?>
    </ul>
<script type="text/javascript">
<?php if ($this->viewer()->getIdentity()):?>
		
	    //check open popup
	    function checkOpenPopup(url) {
	        if(window.innerWidth <= 480) {
	            Smoothbox.open(url, {autoResize : true, width: 300});
	        }
	        else {
	            Smoothbox.open(url);
	        }
	    }
		
		function like(itemType, itemId, likeCount)
		{
			likeCount = parseInt(likeCount) + 1;
			
			html = '<a><i class="fa fa-heart"></i>'+likeCount+'</a>';
        	$('ynfeedback-like-button-new-'+itemId).set('html', html);
        	
			new Request.JSON({
		        url: en4.core.baseUrl + 'core/comment/like',
		        method: 'post',
		        data : {
		        	format: 'json',
		        	type : itemType,
		            id : itemId,
		            comment_id : 0
		        },
		        onSuccess: function(responseJSON, responseText) {
		        	if (responseJSON.status == true)
		        	{
		            	html = '<a title="<?php echo $this -> translate('Liked');?>" href="javascript:void(0);" onclick="unlike(\''+itemType+'\', \''+itemId+'\', \''+likeCount+'\')"><i class="fa fa-heart"></i></a>'+likeCount;
		            	$('ynfeedback-like-button-new-'+itemId).set('html', html);
		        	}            
		        },
		        onComplete: function(responseJSON, responseText) {
		        }
		    }).send();
		}

		function unlike(itemType, itemId, likeCount)
		{
			likeCount = parseInt(likeCount) - 1;
			
			html = '<a><i class="fa fa-heart"></i>'+likeCount+'</a>';
        	$('ynfeedback-like-button-new-'+itemId).set('html', html);
			
			new Request.JSON({
		        url: en4.core.baseUrl + 'core/comment/unlike',
		        method: 'post',
		        data : {
		        	format: 'json',
		        	type : itemType,
		            id : itemId,
		            comment_id : 0
		        },
		        onSuccess: function(responseJSON, responseText) {
		        	if (responseJSON.status == true)
		        	{
		        		html = '<a title="<?php echo $this -> translate('Like');?>" href="javascript:void(0);" onclick="like(\''+itemType+'\', \''+itemId+'\', \''+likeCount+'\')"><i class="fa fa-heart-o"></i></a>'+likeCount;
		            	$('ynfeedback-like-button-new-'+itemId).set('html', html);
		        	}   
		        }
		    }).send();
		}
<?php endif;?>		
</script>       
    
<?php else: ?>
    <div class='tips'>
        <span><?php echo $this->translate('No ideas found.')?></span>
    </div>
<?php endif; ?>