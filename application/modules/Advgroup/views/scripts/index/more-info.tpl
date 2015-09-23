<div style="width: 700px; height: 390px; webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; padding:5px">
	<div class="left_column">
		<div class="tarfee-popup-close"><i class="fa fa-times fa-lg"></i></div>
			<?php $photoUrl = ($this ->group -> getPhotoUrl('thumb.main')) ? $this ->group->getPhotoUrl('thumb.main') : "application/modules/Advgroup/externals/images/nophoto_group_thumb_profile.png" ?>
		<div class="club-photo" style="background-image: url(<?php echo $photoUrl; ?>)"></div>
		<?php if($this ->group -> website):?>
		<div class="club-website">
			<?php $websiteURl = $this ->group -> website;
			if((strpos($websiteURl,'http://') === false) && (strpos($websiteURl,'https://') === false)) $websiteURl = 'http://'.$websiteURl; ?>
			<a target="_blank" href="<?php echo $websiteURl?>">
				<?php echo $this ->group -> website?>
			</a>
		</div>
		<?php endif;?>
		<div class="club-info-general">
			<div class="club-like-count">
				<i class="fa fa-heart"></i>
				<span class="like-count">
					<?php $rows = $this -> group -> membership() ->getMembers();?>
					<?php echo $this -> translate("Fans")." (".count($rows).")";?>
				</span>
			</div>
			
			<?php 
				$establishDateObj = null;
				if (!is_null($this->group->establish_date) && !empty($this->group->establish_date) && $this->group->establish_date) 
				{
					$establishDateObj = new Zend_Date(strtotime($this->group->establish_date));	
				}
				if( $this->viewer() && $this->viewer()->getIdentity() ) 
				{
					$tz = $this->viewer()->timezone;
					if (!is_null($establishDateObj))
					{
						$establishDateObj->setTimezone($tz);
					}
			    }
			?>
			<?php if(!empty($establishDateObj)) :?>
				<div class="club-establish">
					<?php echo (!is_null($establishDateObj)) ?  date('d M, Y', $establishDateObj -> getTimestamp()) : ''; ?>
				</div>
			<?php endif;?>
			<?php if ($this->group->getCountry()) :?>
			<div class="club-country">
				<?php echo $this->group->getCountry()->getTitle()?>
				<?php if ($this->group->getCity()) :?>
					<?php echo ", ".$this->group->getCity()->getTitle()?>
				<?php endif;?>
			</div>
			<?php endif;?>
			<div class="club-social">
				<?php if($this ->group -> twitter):?>
					<?php $URl = $this ->group -> twitter;
					if((strpos($URl,'http://') === false) && (strpos($URl,'https://') === false)) $URl = 'http://'.$URl; ?>
					<a target="_blank" href="<?php echo $URl?>"><img src="application/modules/SocialConnect/externals/images/twitter.png" /></a>
				<?php endif;?>
				<?php if($this ->group -> facebook):?>
					<?php $URl = $this ->group -> facebook;
					if((strpos($URl,'http://') === false) && (strpos($URl,'https://') === false)) $URl = 'http://'.$URl; ?>
					<a target="_blank"  href="<?php echo $URl?>"><img src="application/modules/SocialConnect/externals/images/facebook.png" /></a>
				<?php endif;?>
				<?php if($this ->group -> google):?>
					<?php $URl = $this ->group -> google;
					if((strpos($URl,'http://') === false) && (strpos($URl,'https://') === false)) $URl = 'http://'.$URl; ?>
					<a target="_blank"  href="<?php echo $URl?>"><img src="application/modules/SocialConnect/externals/images/google.png" /></a>
				<?php endif;?>
			</div>
		</div>
	</div>
	<div class="right_column">
		<div class="club-title">
			<?php echo $this->group->getTitle()?>
		</div>
		<div class="club-description" <?php if($this->group -> isOwner($this->viewer())) echo "style = 'height: 350px'"?>>
			<?php echo $this->group->description?>
		</div>
		<?php if (Engine_Api::_()->ynfbpp()->_allowMessage($this->viewer(), $this->group -> getOwner())) :?>
			<div class="club-message">
            <?php echo $this->htmlLink(array(
                'route' => 'messages_general',
                'action' => 'compose',
                'to' => $this->group -> getOwner() ->getIdentity()
            ), $this -> translate("Send Message"), array(
                'class' => 'actions_generic', 'title' => $this -> translate("Send Message"),'target' => "_parent"
            ));
            ?>
            </div>
         <?php elseif (Engine_Api::_()->ynfbpp()->_allowMail($this->viewer(), $this->group -> getOwner())) :?>
         	<div class="club-message">
            <?php echo $this->htmlLink(array(
                'route' => 'user_general',
                'action' => 'in-mail',
                'to' =>  $this->group -> getOwner() ->getIdentity()
            ), $this -> translate("Send Email"), array(
                'class' => 'smoothbox actions_generic', 'title' => $this -> translate("Send Email")
            ));
            ?>
            </div>
 		<?php endif;?>
	</div>
</div>
<script type="text/javascript">
	$$('.tarfee-popup-close').addEvent('click',function(){parent.Smoothbox.close()});	
</script>