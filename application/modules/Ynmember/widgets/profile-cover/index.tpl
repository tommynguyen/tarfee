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
			<?php $background_image = Engine_Api::_()->ynmember()->getMemberPhoto($this->subject());?>
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
	        	<?php $direction = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 0);
			    if ( $direction == 0 ): ?>
			      	<?php echo $this->translate(array('<span>%s</span> follower', '<span>%s</span> followers', $this->friendCount), $this->locale()->toNumber($this->friendCount)) ?>      
			    <?php else: ?>  
			    	<?php echo $this->translate(array('<span>%s</span> friend', '<span>%s</span> friends', $this->friendCount), $this->locale()->toNumber($this->friendCount)) ?>
			    <?php endif; ?>		  			
		  		</div>
			</div>

			<!-- updated -->
			<div class="ynmember-profile-information-lastupdate">
			<i class="fa fa-pencil-square-o" title="<?php echo $this->translate("Last Update"); ?>"></i>
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
		    	<i class="fa fa-sign-in" title="<?php echo $this->translate("Joined"); ?>"></i>
				<?php echo $this->timestamp($this->subject->creation_date) ?>
		    </div>
		</div>
		
	</div>	
</div>