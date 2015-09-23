<script type="text/javascript">
  function removeSubmit()
  {
   	$('buttons-wrapper').hide();
  }
</script>
<div class="headline">
  <h2>
    <?php if ($this->viewer->isSelf($this->user)):?>
      <?php echo $this->translate('Edit My Profile');?>
    <?php else:?>
      <?php echo $this->translate('%1$s\'s Profile', $this->htmlLink($this->user->getHref(), $this->user->getTitle()));?>
    <?php endif;?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class="ynmember-container">
<div class="ynmember-profile-cover-main ynmember-clearfix">
	<div class="ynmember-profile-cover">
		<?php
		$coverPhotoUrl = "";
		if ($this->subject->cover_id)
		{
			$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->subject->cover_id)->current();
			if($coverFile)
				$coverPhotoUrl = $coverFile->map();
		}
		?>				
		<div class="ynmember-profile-cover-picture" style="background-image: url('<?php echo $coverPhotoUrl; ?>');"></div>		
	</div>
	<div class="ynmember-profile-info">
		<div class="ynmember-profile-avatar">
			<span>
			<?php $background_image = $this->baseUrl()."/application/modules/User/externals/images/nophoto_user_thumb_profile.png"; ?>
			<?php if ( $this->subject()->getPhotoUrl('thumb.profile')) 
				$background_image = $this->subject()->getPhotoUrl('thumb.profile'); ?>
			<?php echo $this->htmlLink($this->subject()->getHref(), '<span alt="'.$this->subject()->getTitle().'" class="ynmember-profile-image" style="background-image:url('.$background_image.');"></span>', array('title'=>$this->subject()->getTitle())) ?>
			</span>
		</div>

		<div class="ynmember-profile-information">
			<!-- title -->
			<div class="ynmember-profile-information-title"><?php echo $this -> subject -> getTitle(); ?></div>
			
			<!-- member type -->
			<?php if( !empty($this->memberType) ): ?>
		    <div class="ynmember-profile-information-type"><?php echo $this->translate($this->memberType) ?></div>
	   		<?php endif; ?>		
		   
		   	<div class="ynmember-profile-information-stats clearfix">
		  		<!-- view -->
		  		<div>
		  			<?php echo $this->translate(array('<span>%s</span> view', '<span>%s</span> views', $this->subject->view_count), $this->locale()->toNumber($this->subject->view_count)) ?>
		  		</div>
	        	
	        	<!-- friend -->
	        	<div>
	        	<?php $direction = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction');
			    if ( $direction == 0 ): ?>
			      	<?php echo $this->translate(array('<span>%s</span> follower', '<span>%s</span> followers', $this->friendCount), $this->locale()->toNumber($this->friendCount)) ?>      
			    <?php else: ?>  
			    	<?php echo $this->translate(array('<span>%s</span> friend', '<span>%s</span> friends', $this->friendCount), $this->locale()->toNumber($this->friendCount)) ?>
			    <?php endif; ?>		  			
		  		</div>
			</div>

			<!-- updated -->
			<div class="ynmember-profile-information-lastupdate">
			<?php echo $this->translate('Last Update:')?>
			<?php 
  				if($this->subject->modified_date != "0000-00-00 00:00:00"){
		        	echo $this->timestamp($this->subject->modified_date);
		      	}
		      	else{
		          	echo $this->timestamp($this->subject->creation_date);
		      	}
		    ?>
			</div>

		    <!-- join -->
		    <div class="ynmember-profile-information-joined">
		    	<?php echo $this->translate('Joined:')?>
				<?php echo $this->timestamp($this->subject->creation_date) ?>
		    </div>
		</div>
		
	</div>	
</div>

<?php 
	echo $this->htmlLink(array(
		'route' => 'ynmember_extended',
		'controller' => 'edit',
		'action' => 'edit-cover-photo',
		'id' =>  $this->user->getIdentity(),
	), $this->translate('<i class="fa fa-pencil-square-o"></i> Edit Cover Photo'), array(
	'class' => 'smoothbox btn-ynmember-edit'
	)) ;
?>
</div> <!-- end ynmember-container -->