<?php 
	$table = Engine_Api::_() -> getDbtable('categories', 'ynfeedback');
	$tableIdea = Engine_Api::_() -> getDbtable('ideas', 'ynfeedback');
	$node = $table -> getNode($this -> category -> getIdentity());
	//get its ideas
	$ideas = $tableIdea -> getAllChildrenIdeasByCategory($node);
?>

<div class="ynfeedback-form-submit">
	<h3><?php echo $this -> translate('Add a new Feedback'); ?></h3>
	<p class='form-description'><?php echo $this -> translate('We would like to get your feedbacks on how our website should be improved. Tell us about new features we should consider or how we can improve existing features.');?></p>
	<?php echo $this -> translate('Are any of these helpful?');?>
	<?php if (count($ideas) > 0) :?>
		<div class='ynfeedback-idea-helpful'>
		<?php foreach ($ideas as $item) :?>
			<div class='ynfeedback-idea-lists'>
			<?php foreach ($item->toArray() as $idea) :?>				
				<?php $idea = Engine_Api::_() -> getItem('ynfeedback_idea', $idea['idea_id']);?>
				<?php if($idea) :?>
					<div class='ynfeedback-idea-item'>
						<i class="fa fa-files-o"></i>
						<a data-id='<?php echo $idea -> getIdentity();?>' href='javascript:void(0)' class='idea_popup ynfeedback-idea-item-title'><?php echo $idea -> getTitle();?></a>
						<div class='ynfeedback-idea-item-description'>
							<?php echo $idea->description;?>
						</div>					
					</div>
				<?php endif;?>
			<?php endforeach;?>	
			</div>
		<?php endforeach;?>
		</div>
	<?php endif;?>
	<span name='popup_back_button' id='popup_back_button'><i class='fa fa-arrow-left'></i><?php echo $this -> translate('Back');?></span>
	<button name='popup_skip_button' id='popup_skip_button'><?php echo $this -> translate('Skip and send Feedback');?></button>
</div>
