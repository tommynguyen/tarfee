<h2><?php echo $this->translate("YouNet Feedback Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>
<h3>
    <a href="<?php echo $this -> url(array("module"=>"ynfeedback", "controller"=>"feedbacks", "action"=>"index"), 'admin_default');?>">
    	<?php echo $this-> translate("Manage Feedback");?>
    </a>
    &raquo;
    <?php echo $this-> translate("View Statistic");?>
</h3>
<div class="ynfeedback_statistics">
	<div>
		<span class="label"><?php echo $this -> translate("Title");?></span>
		<span class="info"><?php echo $this -> feedback ->getTitle();?></span>
	</div>
	<div>
		<span class="label"><?php echo $this -> translate("Description");?></span>
		<span class="info"><?php echo $this -> feedback -> getDescription();?></span>
	</div>
	<?php $category = $this->feedback->getCategory();?>
	<?php if (!is_null($category)):?>
	<div>
		<span class="label"><?php echo $this -> translate("Category");?></span>
		<span class="info"><?php echo $category -> title;?></span>
	</div>
	<?php endif;?>
	<div>
		<span class="label"><?php echo $this -> translate("Severity");?></span>
		<span class="info"><?php echo $this->feedback-> getSeverity();?></span>
	</div>
	<div>
		<span class="label"><?php echo $this -> translate("Status");?></span>
		<span class="info"><?php echo $this->feedback->getStatus();?></span>
	</div>
	<div>
		<span class="label"><?php echo $this -> translate("Owner");?></span>
		<?php $owner = $this -> feedback->getOwner();?>
		<span class="info"><?php echo $this->htmlLink($owner->getHref(), $owner->getTitle(), array());?></span>
	</div>
	<div>
		<span class="label"><?php echo $this -> translate("Posted Date");?></span>
		<span class="info"><?php echo $this->feedback->getCreationDate();?></span>
	</div>
	<div>
		<span class="label"><?php echo $this -> translate("Vote");?></span>
		<span class="info"><?php echo $this->feedback->vote_count;?></span>
	</div>
	<div>
		<span class="label"><?php echo $this -> translate("Follow");?></span>
		<span class="info"><?php echo $this->feedback->follow_count;?></span>
	</div>
	<div>
		<span class="label"><?php echo $this -> translate("Like");?></span>
		<span class="info"><?php echo $this->feedback->like_count;?></span>
	</div>
	<div>
		<span class="label"><?php echo $this -> translate("Comment");?></span>
		<span class="info"><?php echo $this->feedback->comment_count;?></span>
	</div>
	<div>
		<span class="label"><?php echo $this -> translate("View");?></span>
		<span class="info"><?php echo $this->feedback->view_count;?></span>
	</div>
	<?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($this -> feedback); ?>
	<?php if($this -> fieldValueLoop($this -> feedback, $fieldStructure)):?>
	<div>
		<span class="label"><?php echo $this -> translate('Specifications');?></span>
		<span class="info"><?php echo $this -> fieldValueLoop($this -> feedback, $fieldStructure); ?></span>
	</div>
	<?php endif; ?>
</div>
