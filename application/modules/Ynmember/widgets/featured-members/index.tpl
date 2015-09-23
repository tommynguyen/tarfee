<?php 
	$this->headScript()
        ->appendFile($this->baseUrl() . '/application/modules/Ynmember/externals/scripts/jquery-1.7.1.min.js')
        ->appendFile($this->baseUrl() . '/application/modules/Ynmember/externals/scripts/jquery.content_slider.min.js');
	$this->headLink()
        ->appendStylesheet($this->baseUrl() . '/application/modules/Ynmember/externals/styles/content_slider_style.css');
		
	$max_shown_items = $this->limit;

	if (count($this->users) < $max_shown_items) {
		$max_shown_items = count($this->users);
	}

	if ($max_shown_items%2==0) {
		$max_shown_items = $max_shown_items-1;
	}
?>

<script type="text/javascript">
	jQuery.noConflict();
	(function($){
		$(document).ready(function() {
			var image_array = new Array();
			image_array = [
				<?php foreach($this -> users as $user) :?>
					<?php $canAdd = Engine_Api::_()->ynmember()->canAddFriendButton($user);?>
					<?php $photoUrl = Engine_Api::_()->ynmember()->getMemberPhoto($user);?>
					{image: '<?php echo $photoUrl; ?>' <?php if(is_array($canAdd)):?>, link_url: '<?php echo $this->url($canAdd['params'], $canAdd['route'], array());?>', link_class: 'smoothbox'<?php endif;?>},
				<?php endforeach;?>
			];

			$('#advmember_slider').content_slider({		// bind plugin to div id="slider1"
				map : image_array,				// pointer to the image map
				max_shown_items: <?php echo $max_shown_items; ?>,				// number of visible circles
				hv_switch: 0,					// 0 = horizontal slider, 1 = vertical
				active_item: 0,					// layer that will be shown at start, 0=first, 1=second...
				wrapper_text_max_height: 320,	// height of widget, displayed in pixels
				middle_click: 1,				// when main circle is clicked: 1 = slider will go to the previous layer/circle, 2 = to the next
				under_600_max_height: 1200,		// if resolution is below 600 px, set max height of content
				border_radius:	-1,				// -1 = circle, 0 and other = radius
				automatic_height_resize: 1,
				border_on_off: 0,
				allow_shadow: 0,
				auto_play: true,
				auto_play_pause_time: 3000,

				// set image blank for case
				no_image_path: '<?php echo $this->baseUrl(); ?>/application/modules/Ynmember/externals/images/slide_profile_blank.png',
			});
		});
	})(jQuery);
</script>

<div class="content_slider_wrapper" id="advmember_slider">
	<?php $slider_id = 0; ?>
	<?php foreach($this -> users as $user) :?>	
	<div class="circle_slider_text_wrapper" id="sw<?php echo $slider_id; ?>" style="display: none;">
	<!-- content for the first layer, id="sw0" -->
		<div class="content_slider_text_block_wrap">
		<!-- "content_slider_text_block_wrap" is a div class for custom content -->
			<h3><a href='<?php echo $user->getHref();?>'><?php echo $user->getTitle();?></a></h3>
			
			<div class="ynmember-item-info">

				<!-- study -->
				<div class="ynmember-item-info-studyplaces">
				<i class="fa fa-graduation-cap"></i>
				<span><?php echo $this-> translate("Studied at");?></span>
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
				</div>
					
				<!-- workplace -->
				<div class="ynmember-item-info-workplaces">
					<i class="fa fa-briefcase"></i>
					<span><?php echo $this-> translate("Works at");?></span>
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
				</div>
				
				<!-- living places -->
				<div class="ynmember-item-info-live">
				<i class="fa fa-map-marker"></i>
				<span><?php echo $this-> translate("Lives in");?></span>
				<?php
				$livePlacesTbl = Engine_Api::_()->getDbTable('liveplaces', 'ynmember');
				$liveplaces = $livePlacesTbl -> getLiveCurrentPlacesByUserId($user -> getIdentity());
				$lives = array();
				foreach ($liveplaces as $live)
				{
					if($live -> isViewable())
					{
						if($live -> isViewable())
						{
							$lives[] = "<a target='_blank' href='https://www.google.com/maps?q={$live->latitude},{$live->longitude}'>{$live->location}</a>";
						}
					}
				}
				$lives = implode(", ", $lives);
				if($lives) :?>
				 	<?php echo $lives; ?> 
				<?php endif;?>						
				</div>
				
				<!-- groups -->
				<div class="ynmember-item-info-group">
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
				<i class="fa fa-users"></i>
				<span><?php echo $this-> translate("Groups:");?></span>
				<?php if (count($groups)):?>
					<?php foreach ($groups as $group) :?>
						 <a href="<?php echo $group->getHref();?>"><?php echo $group->getTitle();?></a>
						<?php break;?>
					<?php endforeach;?>
					<?php if (count($groups) > 1) : ?>
						<?php echo $this-> translate("and");?> <a class="smoothbox" href="<?php echo $this->url(array('controller' => 'review', 'action' => 'user-group', 'id' => $user -> getIdentity()),'ynmember_extended');?>"><?php echo $this -> translate(array("%s other", "%s others" , (count($groups) - 1 )), (count($groups) - 1))?></a>
					<?php endif;?>
				<?php endif;?>
				</div>
			</div>
		</div>
		<div class="clear"></div>	
	</div>	
	<?php $slider_id++; ?>
	<?php endforeach;?>
</div>