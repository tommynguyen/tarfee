<script src="<?php $this->baseURL()?>application/modules/Ynmember/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynmember/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynmember/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynmember/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->baseURL()?>application/modules/Ynmember/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">

<div class="ynmember-birthday-input">
	<i class="fa fa-calendar"></i>
	<input type="text" class="date_picker input_small" value="<?php echo sprintf("%s/%s/%s", $this->pickedMonth, $this->pickedDay, $this->pickedYear);?>" />
</div>

<script type="text/javascript">
    window.addEvent('load', function() {
        new Picker.Date($$('.date_picker'), { 
            positionOffset: {x: 5, y: 0}, 
            pickerClass: 'datepicker_dashboard', 
            useFadeInOut: !Browser.ie,
            onSelect: function(date){
            	window.location= "<?php echo $this->url(array('controller' =>'member', 'action' => 'birthday'), 'ynmember_extended', true);?>?date=" + date.getDate() + "&month=" + (date.getMonth()+1) + "&year="+date.getFullYear();
            }
        });
    });
</script>

<?php if( count($this->paginator) > 0 ): ?>
<h3>
	<?php echo $this->translate("Members have birthday on"); ?>
	<?php echo sprintf(" %s/%s/%s", $this->pickedMonth, $this->pickedDay, $this->pickedYear);?>
</h3>

<ul class="ynmember-member-items ynmember-clearfix">
	<?php foreach($this->paginator as $user) :?>
	<li>
		<div class="ynmember-member-item">
			<div class="ynmember-member-item-option">
				<span class="ynmember-member-item-more-btn"><i class="fa fa-cog"></i></span>
				<div class="ynmember-member-item-option-hover">
					<?php echo $this->action('render', 'menu', 'ynmember', array('id' => $user->getIdentity()));?>
					<?php Engine_Api::_()->core()->clearSubject();?>
				</div>
			</div>
			<div class="ynmember-member-item-avatar">

				<?php $background_image = $this->baseUrl()."/application/modules/User/externals/images/nophoto_user_thumb_profile.png"; ?>
				<?php if ($user->getPhotoUrl('thumb.profile')) 
					$background_image = $user->getPhotoUrl('thumb.profile'); ?>
				<?php echo $this->htmlLink($user->getHref(), '<span alt="'.$user->getTitle().'" class="ynmember-profile-image" style="background-image:url('.$background_image.');"></span>', array('title'=>$user->getTitle())) ?>
				
				<!-- add friend button -->
				<?php $canAdd = Engine_Api::_()->ynmember()->canAddFriendButton($user);?>
				<?php if(is_array($canAdd)):?>
					<a href="<?php echo $this->url($canAdd['params'], $canAdd['route'], array());?>" class="smoothbox ynmember-btn-addfriend">
						<i class="fa fa-plus"></i>
		            </a>
				<?php endif;?>
			</div>
			<div class="ynmember-member-item-info">
				<div class="ynmember-member-item-title">
					<?php
				         $onlineTable = Engine_Api::_() -> getDbtable('online', 'user');
				         $step = 900;
				         $select = $onlineTable -> select() -> where('user_id=?', (int)$user -> getIdentity()) -> where('active > ?', date('Y-m-d H:i:s', time() - $step));
				         $online = $onlineTable -> fetchRow($select);
				         if(is_object($online)): ?>
				     		<span class="ynmember-item-status online"></span>
				        <?php else:?>
				            <span class="ynmember-item-status off"></span>
				        <?php endif;?>
					<span><a href='<?php echo $user->getHref();?>'><?php echo $user->getTitle();?></a></span>					
				</div>
				<div class="ynmember-member-item-more">
					<!-- studyplace -->
					<span><i class="fa fa-graduation-cap"></i> <?php echo $this-> translate("Studied at");?> 
					<?php
						$studyPlacesTbl = Engine_Api::_()->getDbTable('studyplaces', 'ynmember');
						$studyplaces = $studyPlacesTbl -> getCurrentStudyPlacesByUserId($user -> getIdentity());
						if ($studyplaces) :?>
						<?php
							$str_studyplace = "";
							if($studyplaces -> isViewable())
							{
								$str_studyplace = "<a target='_blank' href='https://www.google.com/maps?q={$studyplaces->latitude},{$studyplaces->longitude}'>{$studyplaces->name}</a>";
							}
						?>
							 <?php echo $str_studyplace;?> 
						<?php endif;?>	
					</span>	
					<!-- workplace -->
					<span><i class="fa fa-briefcase"></i> <?php echo $this-> translate("Works at");?> 
					<?php
						$workPlacesTbl = Engine_Api::_()->getDbTable('workplaces', 'ynmember');
						$workplaces = $workPlacesTbl -> getCurrentWorkPlacesByUserId($user -> getIdentity());
						if ($workplaces) :?>
						<?php
							$str_workplace = "";
							if($workplaces -> isViewable())
							{
								$str_workplace = "<a target='_blank' href='https://www.google.com/maps?q={$workplaces->latitude},{$workplaces->longitude}'>{$workplaces->company}</a>";
							}
						?>
							 <?php echo $str_workplace;?> 
						<?php endif;?>	
					</span>	
					<!-- living places -->		
					<span><i class="fa fa-map-marker"></i> <?php echo $this-> translate("Lives in");?> 
					<?php
						$livePlacesTbl = Engine_Api::_()->getDbTable('liveplaces', 'ynmember');
						$liveplaces = $livePlacesTbl -> getLiveCurrentPlacesByUserId($user -> getIdentity());
						$lives = array();
						foreach ($liveplaces as $live)
						{
							if($live -> isViewable())
							{
								$lives[] = "<a target='_blank' href='https://www.google.com/maps?q={$live->latitude},{$live->longitude}'>{$live->location}</a>";
							}
						}
						$lives = implode(", ", $lives);
						if($lives) :?>
						 	<?php echo $lives; ?> 
						<?php endif;?>		
					</span>	
					<!-- groups -->
					<?php
						if (Engine_Api::_()->hasModuleBootstrap('group') || Engine_Api::_()->hasModuleBootstrap('advgroup'))
						{
							$groupTbl = Engine_Api::_()->getItemTable('group');
							$membership = (Engine_Api::_()->hasModuleBootstrap('advgroup'))
								? Engine_Api::_()->getDbtable('membership', 'advgroup')
								: Engine_Api::_()->getDbtable('membership', 'group');
						
							$select = $membership->getMembershipsOfSelect($user);
							$groups = $groupTbl->fetchAll($select);
						}
						?>
					<span><i class="fa fa-users"></i> <?php echo $this-> translate("Groups:");?> 
					<?php if (count($groups)):?>
							<?php foreach ($groups as $group) :?>
								 <a href="<?php echo $group->getHref();?>"><?php echo $group->getTitle();?></a>
								<?php break;?>
							<?php endforeach;?>
							<?php if (count($groups) > 1) : ?>
								<?php echo $this-> translate("and");?> <a class="smoothbox" href="<?php echo $this->url(array('controller' => 'review', 'action' => 'user-group', 'id' => $user -> getIdentity()),'ynmember_extended');?>"><?php echo $this -> translate(array("%s other", "%s others" , (count($groups) - 1 )), (count($groups) - 1))?></a>
							<?php endif;?>
						<?php endif;?>
					</span>	
				</div>

				<div class="ynmember-review-item-rating">
					<?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $user -> rating));?>
            	</div>
			</div>
		</div>
	</li>
	<?php endforeach;?>
</ul>

<div id='paginator'>
	<?php if( $this->paginator->count() > 1 ): ?>
	     <?php echo $this->paginationControl($this->paginator, null, null, array(
	            'pageAsQuery' => true,
	            'query' => $this->formValues,
	          )); ?>
	<?php endif; ?>
</div>
<script type="text/javascript">
	$$('.ynmember-member-item-option .ynmember-member-item-more-btn').addEvent('click', function(){
		this.getParent('.ynmember-member-item-option').toggleClass('ynmember-member-item-option-show')
	});
</script>
<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no members have birthday today.') ?>
		</span>
    </div>
<?php endif; ?>
