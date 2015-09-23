<div class="ynfeedback-browse-top">
<?php if (count($this->paginator)) {
    echo '<span class="ynfeedback-count">'.$this->total.'</span> ';
    echo $this->translate(array('ynfeedback_feedback', 'Feedbacks', $this->total),$this->total);
}?>
</div>

<?php if( count($this->paginator) ): ?>
<div id="ynfeedback-browse-listings">
    <ul class="ynfeedback-listing">
    <?php foreach ($this->paginator as $feedback) :?>
        <li class="ynfeedback-listing-item ynfeedback-clearfix">           

            <div class="ynfeedback-listing-option">
                <?php
                    Engine_Api::_()->core()->clearSubject();
                    Engine_Api::_()->core()->setSubject($feedback);
                    
                    $menu = new Ynfeedback_Plugin_Menus();
                    $aEditButton = $menu -> onMenuInitialize_YnfeedbackEditFeedback();
                    $aManageScreenshotsButton = $menu -> onMenuInitialize_YnfeedbackManageScreenshots();
                    $aManageFilesButton = $menu -> onMenuInitialize_YnfeedbackManageFiles();
                    $aDeleteButton = $menu -> onMenuInitialize_YnfeedbackDeleteFeedback();
                    $options = array ($aEditButton, $aManageScreenshotsButton, $aManageFilesButton, $aDeleteButton);
                ?>

                <?php foreach ($options as $option) :?>
                    <?php if ($option) : ?>
                    <div class="option-item">
                        <a href="<?php echo $this->url($option['params'], $option['route'], true)?>" class="<?php echo $option['class']?>">
                            <i class="<?php echo $option['icon-font']?>"></i>
                            <?php echo $this -> translate($option['label'])?>
                        </a>
                    </div>
                    <?php endif; ?>
                <?php endforeach;?>    
                <?php Engine_Api::_()->core()->clearSubject();?>
            </div> 

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
        <?php echo $this->translate('There are no feedbacks.') ?>
        </span>
    </div>
<?php endif; ?>