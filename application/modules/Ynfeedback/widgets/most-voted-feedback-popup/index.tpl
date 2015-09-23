<div class="ynfeedback-most-voted-feedback">
	<div class="ynfeedback-popup-simple-content">
	<?php if(count($this -> ideas)) :?>	
		<ul class="ynfeedback-list-most-items">
		<?php foreach ($this -> ideas as $idea):?>
		    <li class="ynfeedback-list-most-item">
				<div class="ynfeedback-list-most-item-title"><?php echo $this->htmlLink($idea->getHref(), $idea->title); ?></div>
				<div class="ynfeedback-list-most-item-content">
					<span class="ynfeedback-list-most-item-status" style="background-color: <?php echo $idea->getStatusColor(); ?>"><?php echo $idea -> getStatus();?></span>
					<span><i class="fa fa-check-circle"></i> <?php echo $this -> translate(array("%s vote", "%s votes", $idea -> vote_count), $idea -> vote_count);?></span>
					<?php if($idea->getCategory()) :?>
						<span><i class="fa fa-folder-open"></i> <?php echo $this->htmlLink($idea->getCategory()->getHref(), $idea->getCategory()->getTitle());?></span>
					<?php endif;?>
				</div>
			</li>
		<?php endforeach;?>
		</ul>

	<?php else:?>
		<div class="tip">
			<span><?php echo $this -> translate('There are no feedback yet.');?></span>
		</div>
	<?php endif;?>
	</div>

	<div class="ynfeedback-popup-simple-footer flex-footer">
		<div id="post_own_feedback_btn"><?php echo $this -> translate("Post your own feedback");?></div>
		<div><?php echo $this -> htmlLink($this -> url(array(), 'ynfeedback_general', true), $this -> translate('Browse all'));?></div>
	</div>
</div>

<div id="post_own_feedback" style="display: none">
	<?php if($this -> isFinal) :?>
		<h3><?php echo $this -> translate('Awesome!!!'); ?></h3>
		<p class='form-description'><?php echo $this -> translate('Do you still want to send us a message?');?></p>
	<?php else:?>
		<?php echo $this -> translate('POST_OWN_FEEDBACK_DESC');?>
	<?php endif;?>
	<textarea id="post_own_feedback_title" rows="4" cols="50" placeholder="<?php echo $this -> translate('Describe your feedback');?>"></textarea>

	<div class="ynfeedback-popup-simple-footer">
		<div id="post_own_feedback_back"><i class="fa fa-arrow-left"></i> <?php echo $this -> translate('Back');?></div>
		<div id="post_own_feedback_next"><?php echo $this -> translate('Next');?> <i class="fa fa-arrow-right"></i></div>
	</div>
</div>

<script type="text/javascript">

	window.addEvent('domready', function() {
		
		if($('post_own_feedback_btn'))
		{
			$('post_own_feedback_btn').addEvent('click', function(e){
				$('simple_popup_title').innerHTML = '<?php echo $this -> translate("Post your own feedback")?>';
				$('add-new-idea-content').innerHTML = post_own_feedback.innerHTML;
				addEvents();
			});
		}
		
		<?php if($this -> inputTitle):?>
			if($('post_own_feedback_btn'))
				$('post_own_feedback_btn').click();
		<?php endif;?>
		
		function addEvents()
		{		
			var titleValue = "";
			titleValue = getCookiePopUp('ynfeedback-title_popup');
			if(titleValue != "")
			{
				if($('post_own_feedback_title'))
				{
					$('post_own_feedback_title').set('value', titleValue);
				}
			}
			
			$('post_own_feedback_back').addEvent('click', function(e){
				$('simple_popup_title').innerHTML = '<?php echo $this -> translate("Most Voted Feedback")?>';
				titleValue = $('post_own_feedback_title').get('value');
				setCookiePopUp('ynfeedback-title_popup', titleValue, 1);
				
				var params = {};
		        params['format'] = 'html';
		        var request = new Request.HTML({
		            url : en4.core.baseUrl + 'widget/index/name/ynfeedback.most-voted-feedback-popup',
		            data : params,
		            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
		                $('add-new-idea-content').innerHTML = responseHTML;
		                eval(responseJavaScript);
		            }
		        });
	        	request.send();
			});
			
			$('post_own_feedback_next').addEvent('click', function(e){
				
				titleValue = $('post_own_feedback_title').get('value');
				setCookiePopUp('ynfeedback-title_popup', titleValue, 1);
				
				var params = {};
		        params['format'] = 'html';
		        params['text'] = titleValue;
		        var request = new Request.HTML({
		            url : '<?php echo $this -> url(array('action' => 'simple-helpful'), 'ynfeedback_general', true);?>',
		            data : params,
		            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
		                $('add-new-idea-content').innerHTML = responseHTML;
		                eval(responseJavaScript);
		            }
		        });
	        	request.send();
				
			});
		}
	});

</script>