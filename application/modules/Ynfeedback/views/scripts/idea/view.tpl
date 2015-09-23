<?php $this -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>

<!-- for show full screenshots -->
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynfeedback/externals/scripts/XtLightbox/XtLightbox.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynfeedback/externals/scripts/XtLightbox/Adaptor.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynfeedback/externals/scripts/XtLightbox/Adaptor/Image.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynfeedback/externals/scripts/XtLightbox/Renderer.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynfeedback/externals/scripts/XtLightbox/Renderer/Lightbox.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl()?>/application/modules/Ynfeedback/externals/scripts/XtLightbox/Renderer/Lightbox/style.css" />
<?php $idea = $this -> idea;?>

<?php $menu = new Ynfeedback_Plugin_Menus(); ?>
<?php $aEditButton = $menu -> onMenuInitialize_YnfeedbackEditFeedback();?>  
<?php $aDeleteButton = $menu -> onMenuInitialize_YnfeedbackDeleteFeedback();?>
<?php $aScreenButton = $menu -> onMenuInitialize_YnfeedbackManageScreenshots();?>
<?php $aFileButton = $menu -> onMenuInitialize_YnfeedbackManageFiles();?>
    
<?php if($aEditButton || $aScreenButton || $aFileButton || $aDeleteButton) :?>
<div class="ynfeedback-detail-option-action">
    <div class="ynfeedback-detail-option-action-btn">
        <i class="fa fa-cog"></i>
    </div>
    <div class="ynfeedback-detail-option-action-popup">
    <?php if($aEditButton) :?>
        <div <?php echo ($this->active == 'edit') ? 'class="active"' : '';?> >
            <a class="<?php echo (!empty($aEditButton['class'])) ? $aEditButton['class'] : "";?>" href="<?php echo $this -> url($aEditButton['params'], $aEditButton['route'], array()); ?>" > 
                <i class="fa fa-pencil"></i> <?php echo $this -> translate($aEditButton['label']) ?>
            </a>
        </div>
    <?php endif;?>

    <?php if($aScreenButton) :?>
        <div <?php echo ($this->active == 'manage-screenshots') ? 'class="active"' : '';?>>
            <a class="<?php echo (!empty($aScreenButton['class'])) ? $aScreenButton['class'] : "";?>" href="<?php echo $this -> url($aScreenButton['params'], $aDeleteButton['route'], array()); ?>" > 
                <i class="fa fa-picture-o"></i> <?php echo $this -> translate($aScreenButton['label']) ?>
            </a>
        </div>
    <?php endif;?>

    <?php if($aFileButton) :?>
        <div <?php echo ($this->active == 'manage-files') ? 'class="active"' : '';?> >
            <a class="<?php echo (!empty($aFileButton['class'])) ? $aFileButton['class'] : "";?>" href="<?php echo $this -> url($aFileButton['params'], $aFileButton['route'], array()); ?>" > 
                <i class="fa fa-file-o"></i> <?php echo $this -> translate($aFileButton['label']) ?>
            </a>
        </div>
    <?php endif;?>

    <?php if($aDeleteButton) :?>
        <div>
            <a class="<?php echo (!empty($aDeleteButton['class'])) ? $aDeleteButton['class'] : "";?>" href="<?php echo $this -> url($aDeleteButton['params'], $aDeleteButton['route'], array()); ?>" > 
                <i class="fa fa-times"></i> <?php echo $this -> translate($aDeleteButton['label']) ?>
            </a>
        </div>
    <?php endif;?>
    </div>
</div>
<?php endif; ?>

<!-- BreadCrumbs -->
<div class="ynfeedback-detail-breadcrumb">
    <i class="fa fa-folder-open"></i>
    <?php foreach($idea -> getCategory() -> getBreadCrumNode() as $category): ?>
        <a href="<?php echo $this->url(array('action' => 'listing'), 'ynfeedback_general') . "?category_id=" . $category->getIdentity();?>"><?php echo $this-> translate($category -> title);?></a>
        <i class="fa fa-angle-right"></i>
     <?php endforeach; ?>
     <?php
     if(count($idea -> getCategory() ->getBreadCrumNode()) > 0):?>
        <a class="ynfeedback-detail-breadcrumb-current" href="<?php echo $this->url(array('action' => 'listing'), 'ynfeedback_general') . "?category_id=" . $idea -> getCategory()->getIdentity();?>"><?php echo $this-> translate($idea -> getCategory() -> title);?></a>
     <?php endif; ?>
</div>

<?php $widgetId = ($this->identity) ? ($this->identity) : 0;?>
<div id="ynfeedback-item-vote-action-<?php echo $idea->getIdentity();?>-<?php echo $widgetId;?>">
    <?php echo $this -> partial ('_vote_action.tpl', 'ynfeedback', array('feedback' => $idea, 'widget_id' => $widgetId));?>
</div>

<div class="ynfeedback-detail-main-content">
    <div class="ynfeedback-detail-top">
    <?php echo $this -> translate('by');?>
    <?php if($idea -> user_id != 0) :?>
        <?php echo $this -> htmlLink($idea -> getOwner() -> getHref(), $idea -> getOwner() -> getTitle()); ?>
    <?php else:?>
        <?php echo $idea -> guest_name;?>
    <?php endif;?>
    &nbsp;&ndash;&nbsp;
    <?php
        $creationDateObj = null;
        $creationDateObj = new Zend_Date(strtotime($idea->creation_date));  
        if( $this->viewer && $this->viewer->getIdentity() ) {
            $tz = $this->viewer->timezone;
            if (!is_null($creationDateObj))
            {
                $creationDateObj->setTimezone($tz);
            }
        }
    ?>
    <?php echo (!is_null($creationDateObj)) ? date('M d Y', $creationDateObj -> getTimestamp())  : ''; ?>
    </div>

    <a class="ynfeedback-detail-title" href="<?php echo $idea -> getHref();?>"><?php echo $idea -> title ?></a>
    
    <div class="ynfeedback-detail-description ynfeedback-description"><?php echo $idea -> description; ?></div>
	
	<?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($idea); ?>
	<?php if($this -> fieldValueLoop($idea, $fieldStructure)):?>
	<h4><?php echo $this -> translate('Feedback Specifications'); ?> </h4>
	<div class="feedback_title">
	       <?php echo $this -> fieldValueLoop($idea, $fieldStructure); ?>
	</div>
	<?php endif; ?>
	
    <!-- co-authous -->
    <?php $tableAuthors = Engine_Api::_() -> getDbTable('authors', 'ynfeedback');
    	  $authors = $tableAuthors -> getAuthorsByIdeaId($idea -> getIdentity());
    	  $indexAuthor = 1;
    ?>
    <?php if(count($authors)):?>
        <div class="ynfeedback-detail-author">
        	<span class="ynfeedback-detail-label">
                <?php $countAuthors =  count($authors);
                    echo $this -> translate('Co-authors');?> :</span>
        	<?php foreach($authors as $author) :?>
        		<?php if(is_numeric($author -> user_id)):?>
    	    		<?php $userAuthor = Engine_Api::_() -> getItem('user', $author -> user_id);?>
    	    		<?php if($userAuthor -> getIdentity()):?>
    	    			<a href ="<?php echo $userAuthor -> getHref();?>"><?php echo $userAuthor -> getTitle();?></a>
    	    		<?php endif;?>
        		<?php else:?>
        			<?php echo $author -> name;?>
        		<?php endif;?>
        		<?php
    				if($indexAuthor < $countAuthors) 
    				{
    					echo ","; 
    				}
    			?>
        		<?php $indexAuthor++;?>
        	<?php endforeach;?>
        </div>
    <?php endif;?>

    <!-- View full screenshot -->
    <?php if (count($this->screenshots) > 0 ): ?>
    <?php $count = 0;?>
    <div class="idea-screenshots ynfeedback-detail-screenshots">
        <h4><span><?php echo $this->translate('Screenshots')?></span></h4>
        <span class="ynfeedback-detail-toggle-btn"><i class="fa fa-chevron-down"></i></span>
        <div class="dea-screenshots ynfeedback-detail-screenshots">
            <ul class="screenshots-list ynfeedback-detail-screenshots-list">
            <?php foreach ($this->screenshots as $screenshot) : ?>
                <li class="<?php if ($count >= 5) echo 'view-more'?>">
                    <a rel="lightbox" href="<?php echo $screenshot->getPhotoUrl()?>" title="<?php echo $screenshot->title?>">
                        <span class="ynfeedback-screenshot-photo" style="background-image: url(<?php echo $screenshot -> getPhotoUrl();?>);"></span>
                    </a>
                </li>
            <?php $count++;?>
            <?php endforeach;?>
            </ul>
            <?php if (count($this->screenshots) > 5) : ?>
                <div class="ynfeedback-detail-screenshots-showmore">
                    <a href="javascript:void(0)" id="view-more-screenshots" onclick="viewMoreScreenshots()"><i class="fa fa-arrow-down"></i> <?php echo $this->translate('View more')?></a>
                    <a href="javascript:void(0)" id="view-less-screenshots" onclick="viewLessScreenshots()"><i class="fa fa-arrow-up"></i> <?php echo $this->translate('View less')?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Download file -->
    <?php if (count($this->files) > 0 ): ?>
    <div class="idea-files ynfeedback-detail-files">
        <h4><span><?php echo $this->translate('Files')?></span></h4>
        <span class="ynfeedback-detail-toggle-btn"><i class="fa fa-chevron-down"></i></span>
        <ul class="files-list">
        <?php foreach ($this->files as $file) : ?>
            <li>
                <i class="fa fa-paperclip"></i>
                <span class="ynfeedback-files-title"><?php echo $file->title?></span>
                <span class="ynfeedback-files-download"><a href="<?php echo $file->getDownloadLink()?>" download="<?php echo $file->title?>"><i class="fa fa-download"></i> <?php echo $this->translate('Download')?></a></span>
            </li>
        <?php endforeach;?>
        </ul>
    </div>
    <?php endif; ?>  

    <?php if ($idea -> decision):?>
        <div class="ynfeedback-detail-decision">            
            <div class="ynfeedback-detail-decision-status" style="background-color: <?php echo $idea->getStatusColor(); ?>"><?php echo $idea->getStatus(); ?></div>

            <div class="ynfeedback-detail-decision-author">
                <?php $owner = $idea->getDecisionOwner();?>
                <?php if($owner -> getIdentity()) :?>
                    <?php if ($idea -> decision):?>
                        <div class="ynfeedback-detail-decision-author-name">
                            <?php echo $this -> translate("Responded by ");?>
                            <div class="feedback-detail-decision-image"><?php echo $this -> htmlLink ($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'), array() ) ;?></div>
                            <?php echo $owner;?>
                        </div>
                    <?php else:?>
                        <?php if ($idea -> status_id != "1"):?>
                            <div class="ynfeedback-detail-decision-author-name">
                                <?php echo $this -> translate("by ");?>
                                <div class="feedback-detail-decision-image"><?php echo $this -> htmlLink ($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'), array() ) ;?></div>
                                <?php echo $owner;?>
                            </div>
                        <?php endif;?>
                    <?php endif;?>
                <?php endif;?>
            </div>

            <div class="ynfeedback-detail-decision-content ynfeedback-description"><?php echo $idea -> decision; ?></div>
        </div>
    <?php endif;?>

</div>

<script type="text/javascript">
    $$('.ynfeedback-detail-option-action-btn').addEvent('click', function(){
        this.toggleClass('ynfeedback-open-action-detail');
        this.getNext().toggle();
    });

    $$('.ynfeedback-detail-toggle-btn').addEvent('click', function(){
        this.getElement('.fa').toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        this.getNext().toggle();
    });

    //script for view more/view less screenshots
    function viewMoreScreenshots() {
        $$('.screenshots-list li.view-more').setStyle('display','inline-block');
        $('view-more-screenshots').hide();
        $('view-less-screenshots').show();
    }
    
    function viewLessScreenshots() {
        $$('.screenshots-list li.view-more').setStyle('display','none');
        $('view-more-screenshots').show();
        $('view-less-screenshots').hide();
    }
    
    window.addEvent('domready', function() {
        new XtLightbox('.screenshots-list a', {
            loop: true,
            adaptorOptions: {
                Image: {
                    lightboxCompat: true
                }
            }
        });
    });
</script> 