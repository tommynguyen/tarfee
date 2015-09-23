<div class="ynfeedback-browse-top">
<?php if (count($this->paginator)) {
    echo '<span class="ynfeedback-count">'.$this->paginator -> getTotalItemCount().'</span> ';
    echo $this->translate(array('ynfeedback_feedback', 'Feedbacks', $this->paginator -> getTotalItemCount()), $this->paginator -> getTotalItemCount());
}?>
</div>
<?php if( count($this->paginator) ): ?>
<div id="ynfeedback-browse-listings" class="ynfeedback-browse-idea-viewmode-list">
	<ul>
	<?php foreach ($this->paginator as $feedback) :?>
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
                    <span><i class="fa fa-folder-open"></i><?php echo $this->htmlLink($feedback->getCategory()->getHref(), $feedback->getCategory()->getTitle());?></span>
                    <span><i class="fa fa-heart"></i><?php echo $feedback->like_count; ?></span>
                    <span><i class="fa fa-comment"></i><?php echo $feedback->comment_count; ?></span>
                    <span><i class="fa fa-share-square-o"></i><?php echo $feedback->getShareCount(); ?></span>

                    <div class="ynfeedback-listing-following">
                        <?php echo $this->htmlLink(array(
                          'action' => 'un-follow',
                          'idea_id' => $feedback->getIdentity(),
                          'route' => 'ynfeedback_specific',
                          'reset' => true,
                        ), '<i class="fa fa-share-square-o"></i> '.$this->translate('Unfollow'), array(
                          'class' => 'buttonlink smoothbox icon_ynfeedback_unfollow',
                        )) ?>
                    </div>
                </div>      

                <?php if ($feedback -> decision):?>
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

                        <div class="ynfeedback-listing-decision-content ynfeedback-description"><?php echo $this->viewMore($feedback -> decision, 255, 3*1027); ?></div>
                    </div>
                <?php endif;?>
            </div>
        </li>
	<?php endforeach;?>
	</ul>
	<div>
	    <?php echo $this->paginationControl($this->paginator, null, null, array(
	        'pageAsQuery' => true,
	        'query' => $this->formValues,
	    )); ?>
	</div>
</div>	
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no following feedbacks.') ?>
    </span>
  </div>
<?php endif; ?>