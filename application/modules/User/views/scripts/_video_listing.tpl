<li style="height:auto" class="user-library-video-content">
  	<div class="video_thumb_wrapper">
	    <?php if ($this -> video->duration):?>
	        <span class="video_length" style="display:none">
	          <?php
	            if( $this -> video->duration>360 ) $duration = gmdate("H:i:s", $this -> video->duration); else $duration = gmdate("i:s", $this -> video->duration);
	            if ($duration[0] =='0') $duration = substr($duration,1); echo $duration;
	          ?>
	        </span>
	    <?php endif;?>
	    <div class="avatar">
	        <?php
	          if( $this -> video->photo_id ) {
	            echo $this->htmlLink($this -> video->getPopupHref(), $this->itemPhoto($this -> video, 'thumb.large'), array('class' => 'smoothbox'));
	          } else {
	            echo '<img alt="" src="' . $this->layout()->staticBaseUrl . 'application/modules/Video/externals/images/video.png">';
	          }
	        ?>
	    </div>
  	</div>
	<?php 
	  	$isMobile = false;
	    if(Engine_Api::_() -> hasModuleBootstrap('ynresponsive1')) 
	    {
	  		$isMobile = Engine_Api::_()->getApi('mobile','ynresponsive1')->isMobile();
	  	} 
	?>

	<div class="tf_video_info <?php if(isset($this -> main) && $this -> main) :?>video_main<?php endif;?>">
		<div class="tf_video_title">
			<a class="<?php if(!$isMobile) echo 'smoothbox' ?> video_title" href="<?php echo $this -> video->getPopupHref();?>">
		  	<?php echo $this -> video->getTitle();?>
		  	</a> 
		</div>

		<div class="tf_video_count">
			<span><?php echo $this->translate(array('%s view', '%s views', $this -> video -> view_count), $this -> video -> view_count); ?>&nbsp;&nbsp;</span>
			<span><?php echo $this->translate(array('%s comment', '%s comments', $this -> video -> comment_count), $this -> video -> comment_count); ?></span>
		</div>

	   <div class="tf_video_rating">
	   		<?php if($this -> video->getRating() > 0):?>
	        	<?php for($x=1; $x<=$this -> video->getRating(); $x++): ?><span class="rating_star_generic"><i class="fa fa-star"></i></span><?php endfor; ?><?php if((round($this -> video->rating)-$this -> video->getRating())>0):?><span class="rating_star_generic"><i class="fa fa-star-half-o"></i></span><?php endif; ?>
	 		<?php else :?>
				<?php for($x=1; $x<=5; $x++): ?><span class="rating_star_generic"><i class="fa fa-star-o"></i></span><?php endfor; ?>
	 		<?php endif;?>
		</div>
	</div>
	<?php if($this -> viewer() -> isSelf($this -> subject())) :?>
   		<ul class="tf_video_action">
   			<?php if($this -> viewer() -> getIdentity()):?>
				<li id="favorite_<?php echo $this->video -> getIdentity()?>">
					<?php if($this->video -> hasFavorite()):?>
						<a href="javascript:;" onclick="unfavorite_video_lib(<?php echo $this->video -> getIdentity()?>)"><i class="fa fa-heart"></i></a>
					<?php else:?>	
						<a href="javascript:;" onclick="favorite_video(<?php echo $this->video -> getIdentity()?>)"><i class="fa fa-heart-o"></i></a>
					<?php endif;?>	
			    </li>
			<?php endif;?>
   			<li>
				<?php
					echo $this->htmlLink(array(
						'route' => 'default',
						'module' => 'video',
						'controller' => 'index',
						'action' => 'edit',
						'video_id' => $this -> video->video_id,
						'parent_type' =>'user_library',
						'subject_id' =>  $this->library->getIdentity(),
						'tab' => (isset($this -> tab_id))? $this -> tab_id : "",
				    ), '<i class="fa fa-pencil-square-o"></i>', array('class' => 'buttonlink'));
				?>
		    </li>
		    <li>
				<?php
					echo $this->htmlLink(array(
				 	        'route' => 'default', 
				         	'module' => 'video', 
				         	'controller' => 'index', 
				         	'action' => 'delete', 
				         	'video_id' => $this -> video->video_id, 
				         	'subject_id' =>  $this->library->getIdentity(),
				        	'parent_type' => 'user_library',
				        	'case' => 'video',
				        	'tab' => (isset($this -> tab_id))? $this -> tab_id : "",
				         	'format' => 'smoothbox'), 
				         	'<i class="fa fa-trash-o"></i>', array('class' => 'buttonlink smoothbox'
				     ));
				?>
			</li>
			<?php $subLibraries = $this -> viewer() -> getMainLibrary() -> getSubLibrary();?>
			<?php if(count($subLibraries) > 1) :?>
			<li>
					<?php echo $this->htmlLink(array(
							'route' => 'user_library',
							'action' => 'move-to-sub',
							'id' =>  $this -> video -> video_id,
							'libid' =>  $this->library->getIdentity(),
						), '<i class="fa fa-arrows"></i>', array(
						'class' => 'smoothbox buttonlink'
						)) ;
					?>
			</li>
			<?php endif;?>
			
			<?php if(isset($this -> main) && $this -> main) :?>
				<?php $playerTable = Engine_Api::_() -> getItemTable('user_playercard'); ?>
				<?php if($playerTable -> getTotal($this -> viewer() -> getIdentity())) :?>
				<li>
					<?php echo $this->htmlLink(array(
							'route' => 'user_library',
							'action' => 'move-to-player',
							'id' =>  $this -> video -> video_id,
							'libid' =>  $this->library->getIdentity(),
						), '<i class="fa fa-exchange"></i>', array(
						'class' => 'smoothbox buttonlink'
						)) ;
					?>
				</li>
				<?php endif;?>
			<?php endif;?>
		</ul>
	<?php endif;?>
	<?php if(isset($this -> main) && $this -> main) :?>
		<div class="nickname">
		<?php echo $this->translate('By') ?>
	    <?php echo $this->htmlLink($this -> video -> getOwner()->getHref(), htmlspecialchars ($this->string()->truncate($this -> video -> getOwner()->getTitle(), 25)), array('title' => $this -> video -> getOwner()->getTitle())) ?>
    </div>
	<?php endif;?>
</li>
