<?php if ($this -> paginator -> getTotalItemCount() > 0) :?>
	<div class="ynfeedback-browse-top">
		<?php $total = $this -> paginator -> getTotalItemCount();?>
		<?php 
			echo '<span class="ynfeedback-count">'.$total.'</span> ';
		    echo $this->translate(array('ynfeedback_feedback', 'Feedbacks', $total),$total);
	    ?>
	</div>

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
	                    <div><?php echo date("M d Y", strtotime($feedback->creation_date)); ?></div>
	                </div>

	                <div class="ynfeedback-listing-info ynfeedback-description">                        
	                    <div><?php echo $this->viewMore($feedback -> description, 255, 3*1027); ?></div>
	                </div>

	                <div class="ynfeedback-listing-stats">
	                    <span><i class="fa fa-folder-open"></i><?php echo $this->htmlLink($feedback->getCategory()->getHref(), $feedback->getCategory()->getTitle());?></span>
	                    <span><i class="fa fa-heart"></i><?php echo $feedback->like_count; ?></span>
	                    <span><i class="fa fa-comment"></i><?php echo $feedback->comment_count; ?></span>
	                    <span><i class="fa fa-share-square-o"></i><?php echo $feedback->getShareCount(); ?></span>
	                </div>      

	                <?php if ($feedback -> decision):?>
	                    <div class="ynfeedback-listing-decision">
	                        <div class="ynfeedback-listing-decision-status" style="background-color: <?php echo $feedback->getStatusColor(); ?>"><?php echo $feedback->getStatus(); ?></div>

	                        <div class="ynfeedback-listing-decision-author">
	                            <?php $owner = $feedback->getDecisionOwner();?>
	                            <div class="feedback-listing-image"><?php echo $this -> htmlLink ($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'), array() ) ;?></div>
	                            <div class="ynfeedback-listing-author-name"><?php echo $this -> translate("Responded by %s", $this -> htmlLink ($owner->getHref(), $owner->getTitle(), array() ));  ;?></div>
	                        </div>

	                        <div class="ynfeedback-listing-decision-content ynfeedback-description"><?php echo $feedback -> decision;?></div>
	                    </div>
	                <?php endif;?>
	            </div>
			</li>
		<?php endforeach;?>
	</ul>
	<div id='paginator'>
		<?php if( $this->paginator->count() > 1 ): ?>
		     <?php echo $this->paginationControl($this->paginator,null, array("pagination/pagination.tpl", "ynfeedback"));?>
		<?php endif; ?>
	</div>
<?php else: ?>
    <div class="tip">
        <span>
        <?php echo $this->translate('There are no feedbacks.') ?>
        </span>
    </div>
<?php endif;?>
<script type="text/javascript">
	window.addEvent('domready', function(){
		if ($("ynfeedback-quick-form-text"))
		{
			$("ynfeedback-quick-form-text").innerHTML = '<?php echo $this->translate("Vote for existing feedback <span>(%s)</span>", $this -> paginator -> getTotalItemCount());?>';
		}
	});
</script>