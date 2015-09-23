<?php 
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynfeedback/externals/scripts/awesomplete.js'); 
?>
<style>
div.awesomplete{
	width: 100%;
}
</style>
<?php if ($this->viewSuggestForm):?>
	<script type="text/javascript">
	window.addEvent('domready', function(){
	    $("ynfeedback_keyword1").addEvent('keyup', function(e){
	    	if(e.code === 13){
	    	    $("photo_loading_image").style.display = 'inline-block';
                $("ynfeedback-list-items").innerHTML = "";
            	new Request.HTML({
            		method: 'get',
            		url: '<?php echo $this->url(array('action' => 'suggest-feedback'), 'ynfeedback_general', true);?>',
            		data: {
            			format: 'html',
                		keyword1: $("ynfeedback_keyword1").value
            		},
            		onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript){
            		    $("photo_loading_image").style.display = 'none';
            			$("ynfeedback-list-items").innerHTML = responseHTML;
            			$("keyword").value = $("ynfeedback_keyword1").value;
            		}
            	}).send();
	        }
	    });

	    var input = document.getElementById("ynfeedback_keyword1");
	    var flist = [];
	    <?php if ( count( $this -> feedbackTitles ) ): ?>
	    flist = [<?php echo $this -> feedbackTitles; ?>];
	    <?php endif; ?>
	    new Awesomplete(input, {
	    	minChars: 1,
	    	list: flist
	    });
	});
	
	var goToPage = function(page)
	{
	    $("photo_loading_image").style.display = 'inline-block';
	    $("ynfeedback-list-items").innerHTML = "";
		new Request.HTML({
			method: 'get',
			url: '<?php echo $this->url(array('action' => 'suggest-feedback'), 'ynfeedback_general', true);?>',
			data: {
				format: 'html',
	    		keyword1: $("ynfeedback_keyword1").value,
	    		page: page
			},
			onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript){
			    $("photo_loading_image").style.display = 'none';
				$("ynfeedback-list-items").innerHTML = responseHTML;
				$("keyword").value = $("ynfeedback_keyword1").value;
			}
		}).send();		
	};
	var goToCreatePage = function(){
		url = '<?php echo $this-> url(array('action' => 'create'), 'ynfeedback_general');?>';
		title = $("ynfeedback_keyword1").value;
		if (title != '')
		{
			url = '<?php echo $this-> url(array('action' => 'create'), 'ynfeedback_general');?>'  + '?title=' + title;
		}
		window.location.href = url;
	};
	</script>
	
	<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1);?>
	<div class="ynfeedback-listing-search">
		<h3><?php echo $this -> translate("How can we improve the <a href='#'>%s</a>", $siteTitle);?></h3>
		<div class="ynfeedback-listing-search-content">
			<input id="ynfeedback_keyword1" type="text" style="width: 100%" name="ynfeedback_keyword1" placeholder="<?php echo $this -> translate("How can we improve the feedback system");?>" value="<?php echo (isset($_GET['keyword']) && $_GET['keyword']) ? "{$_GET['keyword']}" : ''?>"/>
			<i><?php echo $this -> translate("(*) Please press enter to filter feedback.");?></i>
			<div class="ynfeedback-listing-search-result">
				<button onclick="javascript:goToCreatePage();"><i class="fa fa-pencil"></i> <?php echo $this -> translate("Post a new feedback");?></button>
			</div>	
		</div>
	</div>
<?php endif;?>

<div style="text-align: center;">
    <img style="display: none" id='photo_loading_image' src='application/modules/Ynfeedback/externals/images/loading.gif' />
</div>
<div id="ynfeedback-list-items">

<?php 
	$action_name = Zend_Controller_Front::getInstance()->getRequest() -> getActionName();
?>
<?php if($action_name != "index") :?>
<div class="ynfeedback-browse-top">
	<?php $total = $this -> paginator -> getTotalItemCount();?>
	<?php 
		echo '<span class="ynfeedback-count">'.$total.'</span> ';
	    echo $this->translate(array('ynfeedback_feedback', 'Feedbacks', $total),$total);
    ?>
</div>
<?php endif;?>

<?php if ($this -> paginator -> getTotalItemCount() > 0) :?>
	<ul>
		<?php foreach ($this -> paginator as $feedback):?>
			<li class="ynfeedback-listing-item ynfeedback-clearfix">
				<?php $widgetId = ($this->identity) ? ($this->identity) : 0;?>
				<div class="ynfeedback-listing-votes" id="ynfeedback-item-vote-action-<?php echo $feedback->getIdentity();?>-<?php echo $widgetId;?>">
					<?php echo $this->partial ('_vote_action.tpl', 'ynfeedback', array('feedback' =>  $feedback, 'widget_id' => $widgetId));?>
				</div>				

				<div class="ynfeedback-listing-content">
					<h4><a href="<?php echo $feedback->getHref();?>"><?php echo $feedback->title; ?></a></h4>					

					<div class="ynfeedback-listing-author">
						<?php $owner = $feedback->getOwner();?>
						<div class="ynfeedback-listing-author-name"><?php echo $this -> translate("Posted by %s", $this -> htmlLink ($owner->getHref(), $owner->getTitle(), array() ));?> </div>
						<div><span>-</span> <?php echo date("M d Y", strtotime($feedback->creation_date)); ?></div>
					</div>

					<div class="ynfeedback-listing-info ynfeedback-description"><?php echo $this->viewMore($feedback -> description, 255, 3*1027); ?></div>

					<div class="ynfeedback-listing-stats">
						<?php if($feedback->getCategory()) :?>
						<span><i class="fa fa-folder-open"></i><?php echo $this->htmlLink($feedback->getCategory()->getHref(), $feedback->getCategory()->getTitle());?></span>
						<?php endif;?>
						<span id="ynfeedback-like-button-<?php echo $feedback -> getIdentity();?>">
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
					        	<a title="<?php echo $this -> translate('Share');?>" class="smoothbox" href="<?php echo $this -> url(array(
				        			'module' => 'activity',
							        'controller' => 'index',
							        'action' => 'share',
							        'type' => $feedback->getType(),
							        'id' => $feedback->getIdentity(),
							        'format' => 'smoothbox',
						        ), 'default', true);?>">
									<i class="fa fa-share-square-o"></i></a><?php echo $feedback->getShareCount(); ?>
							<?php else :?>
								<i title="<?php echo $this -> translate('Share');?>" class="fa fa-share-square-o"></i><?php echo $feedback->getShareCount(); ?>
							<?php endif;?>
						</span>
					</div>		
					
					<?php if ($feedback -> status_id != "1" || $feedback -> decision):?>
					<div class="ynfeedback-listing-decision">
						<div class="ynfeedback-listing-decision-status" style="background-color: <?php echo $feedback->getStatusColor(); ?>"><?php echo $feedback->getStatus(); ?></div>
						<div class="ynfeedback-listing-decision-author">
							<?php $owner = $feedback->getDecisionOwner();?>
							<?php if($owner -> getIdentity()) :?>
								<?php if ($feedback -> decision):?>
									<div class="ynfeedback-listing-author-name">
										<?php echo $this -> translate("Responded by ");?>
										<div class="feedback-listing-image"><?php echo $this -> htmlLink ($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'), array() ) ;?></div>
										<?php echo $owner;?>
									</div>
								<?php else:?>
									<?php if ($feedback -> status_id != "1"):?>
										<div class="ynfeedback-listing-author-name">
											<?php echo $this -> translate("by ");?>
											<div class="feedback-listing-image"><?php echo $this -> htmlLink ($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'), array() ) ;?></div>
											<?php echo $owner;?>
										</div>
									<?php endif;?>
								<?php endif;?>
							<?php endif;?>
						</div>
						<?php if ($feedback -> decision):?>
							<div class="ynfeedback-listing-decision-content ynfeedback-description"><?php echo $this->viewMore($feedback -> decision, 255, 3*1027); ?></div>
						<?php endif;?>
					</div>
					<?php endif;?> 
				</div>				
			</li>
		<?php endforeach;?>
	</ul>
	<div id='paginator'>
		<?php if( $this->paginator->count() > 1 ): ?>
		     <?php echo $this->paginationControl($this->paginator, null, null, array(
		            'pageAsQuery' => true,
		            'query' => $this->formValues,
		          )); ?>
		<?php endif; ?>
	</div>
<script type="text/javascript">
<?php if ($this->viewer()->getIdentity()):?>
	
		function like(itemType, itemId, likeCount)
		{
			likeCount = parseInt(likeCount) + 1;
			
			html = '<a><i class="fa fa-heart"></i>'+likeCount+'</a>';
        	$('ynfeedback-like-button-'+itemId).set('html', html);
        	
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
		            	$('ynfeedback-like-button-'+itemId).set('html', html);
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
        	$('ynfeedback-like-button-'+itemId).set('html', html);
			
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
		            	$('ynfeedback-like-button-'+itemId).set('html', html);
		        	}   
		        }
		    }).send();
		}
<?php endif;?>		
</script>
<?php else: ?>
    <div class="tip">
        <span>
        <?php echo $this->translate('There are no feedbacks.') ?>
        </span>
    </div>
<?php endif;?>
</div>