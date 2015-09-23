<?php 
	//get idea
	$idea = $this -> idea;
?>
<div class="ynfeedback-form-submit">
	<h3><?php echo $this -> translate('Add a new Feedback'); ?></h3>
	<p class='form-description'><?php echo $this -> translate('We would like to get your feedbacks on how our website should be improved. Tell us about new features we should consider or how we can improve existing features.');?></p>
	<?php echo $this -> translate('Are any of these helpful?');?>

	<?php if($idea) :?>
		<div class='ynfeedback-idea-helpful'>
			<div class='ynfeedback-idea-helpful-item'>
				<a class='ynfeedback-idea-helpful-title' target='_blank' href='<?php echo $idea -> getHref();?>'><?php echo $idea -> getTitle();?></a>
				<div class='ynfeedback-idea-helpful-description'>
					<?php echo $idea->description;?>
				</div>
				
				<div class='ynfeedback-idea-helpful-stats'>
		            <span><i class='fa fa-heart'></i> <?php echo $idea->like_count; ?></span>
		            <span><i class='fa fa-comment'></i> <?php echo $idea->comment_count; ?></span>
		            <span><i class='fa fa-share-square-o'></i> <?php echo $idea->getTotalShare(); ?></span>
		        </div>
	        </div>
		</div>
	<?php endif;?>
	<span name='popup_back_button_detail' data-id='<?php echo $idea -> category_id ?>' id='popup_back_button_detail'><i class='fa fa-arrow-left'></i><?php echo $this -> translate('Back');?></span>
	<button name='popup_skip_button_detail' id='popup_skip_button_detail'><?php echo $this -> translate('This answers my question');?></button>
</div>
