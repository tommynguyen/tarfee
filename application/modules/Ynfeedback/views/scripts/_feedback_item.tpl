<li class="ynfeedback-widget-item">
	<div class="ynfeedback-widget-title">
		<?php echo $this->htmlLink($this->idea->getHref(), $this->idea->title); ?>
	</div>	

	<div class="ynfeedback-widget-bottom">
		<span class="ynfeedback-widget-status" style="background-color: <?php echo $this -> idea->getStatusColor(); ?>"><?php echo $this -> idea -> getStatus(); ?></span>

		<?php if ($this->filter == 'vote'):?>
		<span>
			<i class="fa fa-thumbs-o-up"></i> <?php echo $this -> idea -> vote_count; ?>
		</span>
		<?php endif; ?>
		
		<?php if ($this->filter == 'follow'):?>
	    <span>
	        <i class="fa fa-share-square-o"></i><?php echo $this -> idea -> follow_count; ?>
	    </span>
	    <?php endif;?>
	    
	    <?php if ($this->filter == 'like'):?>
	    <span>
	        <i class="fa fa-heart"></i> <?php echo $this -> idea -> like_count; ?>
	    </span>
	    <?php endif;?>
	    
		<?php if ($this->filter == 'discuss'):?>
		<span>
			<i class="fa fa-comment"></i> <?php echo $this -> idea -> comment_count; ?>
		</span>
		<?php endif;?>
	</div>

	<div class="ynfeedback-widget-content">
		<?php echo $this -> idea -> description; ?>
	</div>

	<div class="ynfeedback-widget-category"><i class="fa fa-folder-open"></i> <?php echo $this->htmlLink($this->idea->getCategory()->getHref(), $this->idea->getCategory()->getTitle());?></div>
</li>